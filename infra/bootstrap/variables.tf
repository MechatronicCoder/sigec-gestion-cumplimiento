variable "workload" {
  description = "Nombre corto del sistema, se usa en todos los nombres"
  type        = string
  default     = "sigec"
}

variable "location" {
  description = "Región de Azure"
  type        = string
  default     = "eastus2"
}

variable "environments" {
  description = "Ambientes que guardan su estado en este Storage Account"
  type        = list(string)
  default     = ["dev", "test", "prod"]
}

variable "project_tag" {
  description = "Valor de la etiqueta Proyecto"
  type        = string
  default     = "SIGEC"
}

variable "tags" {
  description = "Etiquetas adicionales"
  type        = map(string)
  default     = {}
}
