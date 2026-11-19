data "azurerm_client_config" "current" {}

# Key Vault con permisos por roles (RBAC), como en S07 y S08.
resource "azurerm_key_vault" "this" {
  name                       = var.name
  location                   = var.location
  resource_group_name        = var.resource_group_name
  tenant_id                  = data.azurerm_client_config.current.tenant_id
  sku_name                   = "standard"
  enable_rbac_authorization  = true
  soft_delete_retention_days = 7
  purge_protection_enabled   = var.purge_protection_enabled
  tags                       = var.tags
}

# Quien ejecuta Terraform administra los secretos (los crea y los borra).
resource "azurerm_role_assignment" "terraform_admin" {
  scope                = azurerm_key_vault.this.id
  role_definition_name = "Key Vault Administrator"
  principal_id         = data.azurerm_client_config.current.object_id
}

# La identity de la aplicación solo puede LEER secretos.
resource "azurerm_role_assignment" "app_reader" {
  scope                = azurerm_key_vault.this.id
  role_definition_name = "Key Vault Secrets User"
  principal_id         = var.reader_principal_id
}

# Los permisos RBAC tardan en propagarse. Sin esta espera, crear los secretos
# o la Container App puede fallar con 403 la primera vez.
resource "time_sleep" "rbac" {
  create_duration = var.rbac_wait

  depends_on = [
    azurerm_role_assignment.terraform_admin,
    azurerm_role_assignment.app_reader,
  ]
}

# Secretos de la aplicación. Los valores nunca se escriben en el repositorio:
# llegan generados por Terraform (random_*) desde la configuración del ambiente.
resource "azurerm_key_vault_secret" "this" {
  for_each = nonsensitive(toset(keys(var.secrets)))

  name         = each.key
  value        = var.secrets[each.key]
  key_vault_id = azurerm_key_vault.this.id

  depends_on = [time_sleep.rbac]
}
