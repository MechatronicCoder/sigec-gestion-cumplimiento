output "id" {
  description = "ID de la identity (se asigna a la Container App)"
  value       = azurerm_user_assigned_identity.this.id
}

output "principal_id" {
  description = "Principal ID (recibe el rol de lectura en el Key Vault)"
  value       = azurerm_user_assigned_identity.this.principal_id
}

output "client_id" {
  value = azurerm_user_assigned_identity.this.client_id
}
