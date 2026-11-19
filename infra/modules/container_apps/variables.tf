variable "environment_name" {
  description = "Nombre del Container Apps Environment"
  type        = string
}

variable "app_name" {
  description = "Nombre de la Container App"
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

variable "log_analytics_workspace_id" {
  description = "ID del Log Analytics Workspace"
  type        = string
}

variable "identity_id" {
  description = "ID de la Managed Identity que usa la app"
  type        = string
}

variable "image" {
  description = "Imagen del contenedor (registro/usuario/repo:tag)"
  type        = string
}

variable "container_name" {
  description = "Nombre del contenedor dentro de la app"
  type        = string
  default     = "sigec"
}

variable "target_port" {
  description = "Puerto que expone el contenedor (debe coincidir con el Dockerfile)"
  type        = number
}

variable "cpu" {
  description = "vCPU del contenedor"
  type        = number
  default     = 0.25
}

variable "memory" {
  description = "Memoria del contenedor (debe ir en pareja con cpu: 0.25 => 0.5Gi)"
  type        = string
  default     = "0.5Gi"
}

variable "min_replicas" {
  description = "Réplicas mínimas (0 permite escalar a cero y ahorrar costos)"
  type        = number
  default     = 0
}

variable "max_replicas" {
  description = "Réplicas máximas"
  type        = number
  default     = 2
}

variable "concurrent_requests" {
  description = "Peticiones concurrentes por réplica que disparan una réplica nueva"
  type        = number
  default     = 50
}

variable "env_vars" {
  description = "Variables de entorno no sensibles"
  type        = map(string)
  default     = {}
}

variable "key_vault_secret_ids" {
  description = "Secretos de la app: nombre => ID sin versión en el Key Vault"
  type        = map(string)
  default     = {}
}

variable "secret_env_vars" {
  description = "Variables de entorno sensibles: NOMBRE_VARIABLE => nombre del secreto"
  type        = map(string)
  default     = {}
}

variable "tags" {
  description = "Etiquetas comunes"
  type        = map(string)
  default     = {}
}
