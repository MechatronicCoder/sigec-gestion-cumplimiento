output "environment_id" {
  value = azurerm_container_app_environment.this.id
}

output "app_name" {
  value = azurerm_container_app.this.name
}

output "app_url" {
  description = "URL pública de la aplicación"
  value       = "https://${azurerm_container_app.this.ingress[0].fqdn}"
}
