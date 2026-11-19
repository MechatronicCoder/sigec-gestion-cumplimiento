# Valores del ambiente test (qa)
environment = "test"
location    = "eastus2"

app_image = "mcr.microsoft.com/k8se/quickstart:latest"
app_port  = 80

app_min_replicas = 0
app_max_replicas = 2
app_debug        = false

tags = {
  Equipo = "SIGEC"
  Curso  = "DevOps Engineer DMC"
}
