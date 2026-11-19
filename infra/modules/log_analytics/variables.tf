variable "name" {
  description = "Nombre del Log Analytics Workspace"
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

variable "retention_in_days" {
  description = "Días que se conservan los logs (mínimo 30)"
  type        = number
  default     = 30
}

variable "tags" {
  description = "Etiquetas comunes"
  type        = map(string)
  default     = {}
}
