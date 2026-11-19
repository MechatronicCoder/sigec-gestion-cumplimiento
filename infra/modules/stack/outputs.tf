output "resource_group_name" {
  value = azurerm_resource_group.this.name
}

output "app_url" {
  description = "URL pública de SIGEC en este ambiente"
  value       = module.container_apps.app_url
}

output "container_app_name" {
  value = module.container_apps.app_name
}

output "key_vault_name" {
  value = module.key_vault.name
}

output "log_analytics_name" {
  value = module.log_analytics.name
}

output "log_analytics_workspace_id" {
  value = module.log_analytics.workspace_id
}
