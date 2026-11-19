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
    time = {
      source  = "hashicorp/time"
      version = "~> 0.12"
    }
  }

  # Estado remoto en el Storage Account creado por infra/bootstrap.
  # Los datos llegan con: terraform init -backend-config=backend.hcl
  backend "azurerm" {}
}

# La suscripción llega por la variable de entorno ARM_SUBSCRIPTION_ID (no se guarda en el repo).
provider "azurerm" {
  resource_provider_registrations = "none"
  resource_providers_to_register = [
    "Microsoft.App",
    "Microsoft.KeyVault",
    "Microsoft.ManagedIdentity",
    "Microsoft.OperationalInsights",
  ]

  features {
    key_vault {
      # Al destruir el ambiente se purga el Key Vault (evita VaultAlreadyExists, S08)
      purge_soft_delete_on_destroy    = true
      recover_soft_deleted_key_vaults = true
    }
    resource_group {
      # Container Apps crea recursos auxiliares dentro del grupo; esto permite destruirlo
      prevent_deletion_if_contains_resources = false
    }
  }
}

module "sigec" {
  source = "../../modules/stack"

  workload                   = var.workload
  environment                = var.environment
  location                   = var.location
  instance                   = var.instance
  tags                       = var.tags
  log_retention_days         = var.log_retention_days
  key_vault_purge_protection = var.key_vault_purge_protection
  app_image                  = var.app_image
  app_port                   = var.app_port
  app_cpu                    = var.app_cpu
  app_memory                 = var.app_memory
  app_min_replicas           = var.app_min_replicas
  app_max_replicas           = var.app_max_replicas
  app_debug                  = var.app_debug
}
