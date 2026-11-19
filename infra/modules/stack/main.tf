# STACK DE SIGEC
# Compone los módulos de un ambiente. dev, test y prod usan este mismo código
# y solo cambian sus variables (envs/<ambiente>/<ambiente>.tfvars).

locals {
  # Nomenclatura de Azure (S02-3): <tipo>-<workload>-<entorno>-<región>-<instancia>
  base = "${var.workload}-${var.environment}-${var.location}-${var.instance}"

  names = {
    resource_group = "rg-${local.base}"
    log_analytics  = "log-${local.base}"
    identity       = "id-${local.base}"
    cae            = "cae-${local.base}"
    app            = "ca-${var.workload}-web-${var.environment}-${var.instance}"
    # Key Vault: nombre global (máximo 24 caracteres), por eso lleva un sufijo aleatorio
    key_vault = "kv-${var.workload}-${var.environment}-${random_string.sufijo.result}"
  }

  tags = merge(var.tags, {
    Proyecto  = var.project_tag
    Ambiente  = var.environment
    ManagedBy = "Terraform"
  })
}

resource "random_string" "sufijo" {
  length  = 4
  lower   = true
  upper   = false
  numeric = true
  special = false
}

# APP_KEY de Laravel: 32 bytes aleatorios en base64. La genera Terraform y
# solo vive en el Key Vault (nunca en el repositorio ni en la imagen).
resource "random_bytes" "app_key" {
  length = 32
}

resource "azurerm_resource_group" "this" {
  name     = local.names.resource_group
  location = var.location
  tags     = local.tags
}

module "log_analytics" {
  source = "../log_analytics"

  name                = local.names.log_analytics
  location            = azurerm_resource_group.this.location
  resource_group_name = azurerm_resource_group.this.name
  retention_in_days   = var.log_retention_days
  tags                = local.tags
}

module "identity" {
  source = "../identity"

  name                = local.names.identity
  location            = azurerm_resource_group.this.location
  resource_group_name = azurerm_resource_group.this.name
  tags                = local.tags
}

module "key_vault" {
  source = "../key_vault"

  name                     = local.names.key_vault
  location                 = azurerm_resource_group.this.location
  resource_group_name      = azurerm_resource_group.this.name
  reader_principal_id      = module.identity.principal_id
  purge_protection_enabled = var.key_vault_purge_protection
  tags                     = local.tags

  secrets = {
    "app-key" = "base64:${random_bytes.app_key.base64}"
  }
}

module "container_apps" {
  source = "../container_apps"

  environment_name           = local.names.cae
  app_name                   = local.names.app
  location                   = azurerm_resource_group.this.location
  resource_group_name        = azurerm_resource_group.this.name
  log_analytics_workspace_id = module.log_analytics.id
  identity_id                = module.identity.id

  image        = var.app_image
  target_port  = var.app_port
  cpu          = var.app_cpu
  memory       = var.app_memory
  min_replicas = var.app_min_replicas
  max_replicas = var.app_max_replicas
  tags         = local.tags

  env_vars = {
    APP_NAME    = "SIGEC"
    APP_ENV     = var.environment
    APP_DEBUG   = tostring(var.app_debug)
    LOG_CHANNEL = "stderr" # los logs salen por stdout/stderr hacia Log Analytics
  }

  key_vault_secret_ids = module.key_vault.secret_ids

  secret_env_vars = {
    APP_KEY = "app-key"
  }

  # La app necesita los roles RBAC propagados y los secretos creados
  depends_on = [module.key_vault]
}
