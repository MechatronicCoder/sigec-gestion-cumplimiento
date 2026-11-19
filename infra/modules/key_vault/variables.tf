variable "name" {
  description = "Nombre del Key Vault (único en todo Azure, 3 a 24 caracteres)"
  type        = string
}

variable "location" {
  description = "Región de Azure"
  type        = string
}

variable "resource_group_name" {
  description = "Grupo de recursos donde se crea"
  type        = string
}

variable "reader_principal_id" {
  description = "Principal ID de la Managed Identity que leerá los secretos"
  type        = string
}

variable "secrets" {
  description = "Secretos a guardar: nombre (minúsculas y guiones) => valor"
  type        = map(string)
  sensitive   = true
  default     = {}
}

variable "purge_protection_enabled" {
  description = "Protección contra purga. En prod conviene activarla; en dev y test no, para poder destruir y recrear"
  type        = bool
  default     = false
}

variable "rbac_wait" {
  description = "Espera para que se propaguen los roles RBAC"
  type        = string
  default     = "90s"
}

variable "tags" {
  description = "Etiquetas comunes"
  type        = map(string)
  default     = {}
}
