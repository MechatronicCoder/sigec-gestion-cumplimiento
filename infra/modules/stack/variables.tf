variable "workload" {
  description = "Nombre corto del sistema, se usa en todos los nombres"
  type        = string
  default     = "sigec"
}

variable "environment" {
  description = "Ambiente: dev, test o prod"
  type        = string

  validation {
    condition     = contains(["dev", "test", "prod"], var.environment)
    error_message = "El ambiente debe ser dev, test o prod."
  }
}

variable "location" {
  description = "Región de Azure"
  type        = string
}

variable "instance" {
  description = "Número de instancia de la nomenclatura"
  type        = string
  default     = "001"
}

variable "project_tag" {
  description = "Valor de la etiqueta Proyecto"
  type        = string
  default     = "SIGEC"
}

variable "tags" {
  description = "Etiquetas adicionales del ambiente"
  type        = map(string)
  default     = {}
}

variable "log_retention_days" {
  description = "Días de retención de logs"
  type        = number
  default     = 30
}

variable "key_vault_purge_protection" {
  description = "Protección contra purga del Key Vault (recomendado solo en prod)"
  type        = bool
  default     = false
}

variable "app_image" {
  description = "Imagen inicial de la app. Luego la actualiza el pipeline de CD"
  type        = string
}

variable "app_port" {
  description = "Puerto que expone el contenedor"
  type        = number
}

variable "app_cpu" {
  description = "vCPU del contenedor"
  type        = number
  default     = 0.25
}

variable "app_memory" {
  description = "Memoria del contenedor"
  type        = string
  default     = "0.5Gi"
}

variable "app_min_replicas" {
  description = "Réplicas mínimas"
  type        = number
  default     = 0
}

variable "app_max_replicas" {
  description = "Réplicas máximas"
  type        = number
  default     = 2
}

variable "app_debug" {
  description = "APP_DEBUG de Laravel (solo true en dev)"
  type        = bool
  default     = false
}
