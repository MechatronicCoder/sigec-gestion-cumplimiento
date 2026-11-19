# BOOTSTRAP DEL ESTADO REMOTO
# Crea el Storage Account donde Terraform guarda el estado (tfstate) de cada ambiente.
# Se aplica UNA sola vez por proyecto, antes que los ambientes. Su propio estado es local
# porque no puede guardarse en un almacenamiento que todavía no existe.

terraform {
  required_version = ">= 1.5.0"

  required_providers {
    azurerm = {
      source  = "hashicorp/azurerm"
      version = "=4.4.0" # misma versión de los talleres del curso
    }
    random = {
      source  = "hashicorp/random"
      version = "~> 3.6"
    }
    local = {
      source  = "hashicorp/local"
      version = "~> 2.5"
    }
  }
}

# La suscripción llega por la variable de entorno ARM_SUBSCRIPTION_ID (no se guarda en el repo).
provider "azurerm" {
  resource_provider_registrations = "none"
  features {}
}

locals {
  # Nomenclatura de Azure: <tipo>-<workload>-<entorno>-<región>-<instancia>
  rg_name = "rg-${var.workload}-tfstate-${var.location}-001"
  # Storage Account: nombre global, solo minúsculas y números, máximo 24
  st_name = "st${var.workload}tfstate${random_string.sufijo.result}"

  tags = merge(var.tags, {
    Proyecto  = var.project_tag
    Ambiente  = "compartido"
    Proposito = "terraform-state"
    ManagedBy = "Terraform"
  })
}

resource "random_string" "sufijo" {
  length  = 6
  lower   = true
  upper   = false
  numeric = true
  special = false
}

resource "azurerm_resource_group" "tfstate" {
  name     = local.rg_name
  location = var.location
  tags     = local.tags
}

resource "azurerm_storage_account" "tfstate" {
  name                            = local.st_name
  resource_group_name             = azurerm_resource_group.tfstate.name
  location                        = azurerm_resource_group.tfstate.location
  account_tier                    = "Standard"
  account_replication_type        = "LRS"
  min_tls_version                 = "TLS1_2"
  allow_nested_items_to_be_public = false
  tags                            = local.tags

  blob_properties {
    # Si un apply corrompe el estado, se puede recuperar una versión anterior
    versioning_enabled = true
  }
}

resource "azurerm_storage_container" "tfstate" {
  name                  = "tfstate"
  storage_account_name  = azurerm_storage_account.tfstate.name
  container_access_type = "private"
}

# Genera el archivo backend.hcl de cada ambiente para no copiar nombres a mano.
resource "local_file" "backend" {
  for_each = toset(var.environments)

  filename = "${path.module}/../envs/${each.key}/backend.hcl"
  content  = <<-EOT
    # Generado por infra/bootstrap. Uso: terraform init -backend-config=backend.hcl
    resource_group_name  = "${azurerm_resource_group.tfstate.name}"
    storage_account_name = "${azurerm_storage_account.tfstate.name}"
    container_name       = "${azurerm_storage_container.tfstate.name}"
    key                  = "${var.workload}-${each.key}.tfstate"
  EOT
}
