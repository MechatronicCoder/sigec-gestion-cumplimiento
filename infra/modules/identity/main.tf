# Managed Identity (user assigned) de la Container App.
# Con ella la app lee los secretos del Key Vault sin guardar contraseñas (S07 y S08).
resource "azurerm_user_assigned_identity" "this" {
  name                = var.name
  location            = var.location
  resource_group_name = var.resource_group_name
  tags                = var.tags
}
