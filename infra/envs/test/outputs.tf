output "resource_group_name" {
  value = module.sigec.resource_group_name
}

output "app_url" {
  description = "URL pública de SIGEC"
  value       = module.sigec.app_url
}

output "container_app_name" {
  value = module.sigec.container_app_name
}

output "key_vault_name" {
  value = module.sigec.key_vault_name
}

output "log_analytics_name" {
  value = module.sigec.log_analytics_name
}
