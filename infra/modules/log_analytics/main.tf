# Log Analytics Workspace: recibe los logs y métricas del Container Apps Environment.
# También es la fuente de datos de Grafana (monitoreo, Sprint 3).
resource "azurerm_log_analytics_workspace" "this" {
  name                = var.name
  location            = var.location
  resource_group_name = var.resource_group_name
  sku                 = "PerGB2018"
  retention_in_days   = var.retention_in_days
  tags                = var.tags
}
