# Valores del ambiente dev (no contiene secretos: la suscripción va en ARM_SUBSCRIPTION_ID)
environment = "dev"
location    = "eastus2"

# Imagen de prueba de Azure hasta que exista la imagen de producción de SIGEC (SGC-24 y SGC-25)
app_image = "mcr.microsoft.com/k8se/quickstart:latest"
app_port  = 80

app_min_replicas = 0 # escala a cero cuando no hay tráfico (ahorra costos)
app_max_replicas = 2
app_debug        = true

tags = {
  Equipo = "SIGEC"
  Curso  = "DevOps Engineer DMC"
}
