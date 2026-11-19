# Valores del ambiente prod
environment = "prod"
location    = "eastus2"

app_image = "mcr.microsoft.com/k8se/quickstart:latest"
app_port  = 80

app_cpu          = 0.5
app_memory       = "1Gi"
app_min_replicas = 1 # siempre una réplica activa: sin arranque en frío
app_max_replicas = 4
app_debug        = false

# Logs más largos en prod. La protección contra purga queda en false para poder
# destruir el ambiente al terminar el curso; en un sistema real iría en true.
log_retention_days         = 60
key_vault_purge_protection = false

tags = {
  Equipo = "SIGEC"
  Curso  = "DevOps Engineer DMC"
}
