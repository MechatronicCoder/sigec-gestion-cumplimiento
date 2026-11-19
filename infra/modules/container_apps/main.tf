# Container Apps Environment: red y plataforma donde corren las Container Apps.
# Envía logs y métricas al Log Analytics Workspace.
resource "azurerm_container_app_environment" "this" {
  name                               = var.environment_name
  location                           = var.location
  resource_group_name                = var.resource_group_name
  infrastructure_resource_group_name = "ME_${var.environment_name}"
  log_analytics_workspace_id         = var.log_analytics_workspace_id
  tags                               = var.tags

  workload_profile {
    name                  = "Consumption"
    workload_profile_type = "Consumption"
  }

  lifecycle {
    ignore_changes = [
      infrastructure_resource_group_name,
      workload_profile,
    ]
  }
}

# Container App de SIGEC.
resource "azurerm_container_app" "this" {
  name                         = var.app_name
  container_app_environment_id = azurerm_container_app_environment.this.id
  resource_group_name          = var.resource_group_name
  revision_mode                = "Single"
  workload_profile_name        = "Consumption"
  tags                         = var.tags

  identity {
    type         = "UserAssigned"
    identity_ids = [var.identity_id]
  }

  # Secretos como REFERENCIAS al Key Vault: la app no guarda el valor,
  # lo lee con su Managed Identity (rol Key Vault Secrets User).
  dynamic "secret" {
    for_each = var.key_vault_secret_ids
    content {
      name                = secret.key
      key_vault_secret_id = secret.value
      identity            = var.identity_id
    }
  }

  template {
    min_replicas = var.min_replicas
    max_replicas = var.max_replicas

    # Escalado horizontal según peticiones HTTP concurrentes
    http_scale_rule {
      name                = "http-escalado"
      concurrent_requests = tostring(var.concurrent_requests)
    }

    container {
      name   = var.container_name
      image  = var.image
      cpu    = var.cpu
      memory = var.memory

      # Configuración no sensible del ambiente (APP_ENV, APP_DEBUG, etc.)
      dynamic "env" {
        for_each = var.env_vars
        content {
          name  = env.key
          value = env.value
        }
      }

      # Configuración sensible: variable de entorno => secreto del Key Vault
      dynamic "env" {
        for_each = var.secret_env_vars
        content {
          name        = env.key
          secret_name = env.value
        }
      }
    }
  }

  ingress {
    external_enabled           = true
    target_port                = var.target_port
    allow_insecure_connections = false

    traffic_weight {
      latest_revision = true
      percentage      = 100
    }
  }

  lifecycle {
    # Después del primer despliegue, la imagen la actualiza el pipeline de CD
    # (az containerapp update). Así terraform apply no revierte la versión desplegada.
    ignore_changes = [template[0].container[0].image]
  }
}
