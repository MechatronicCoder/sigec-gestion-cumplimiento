output "id" {
  description = "ID del workspace (lo usa el Container Apps Environment)"
  value       = azurerm_log_analytics_workspace.this.id
}

output "workspace_id" {
  description = "Customer ID del workspace (lo usará Grafana)"
  value       = azurerm_log_analytics_workspace.this.workspace_id
}

output "name" {
  value = azurerm_log_analytics_workspace.this.name
}
