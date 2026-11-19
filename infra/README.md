# Infraestructura como código (Terraform + Azure)

Toda la infraestructura de SIGEC se crea con Terraform. Nada se configura a mano en el portal.

## Estructura

```text
infra/
├── bootstrap/            Storage Account del estado remoto (se aplica una sola vez)
├── modules/
│   ├── log_analytics/    Logs y métricas (fuente de Grafana)
│   ├── identity/         Managed Identity de la app
│   ├── key_vault/        Key Vault con RBAC y secretos
│   ├── container_apps/   Container Apps Environment + Container App
│   └── stack/            Compone los módulos y arma los nombres de un ambiente
└── envs/
    ├── dev/              dev.tfvars
    ├── test/             test.tfvars
    └── prod/             prod.tfvars
```

- **Nomenclatura de Azure:** `<tipo>-<workload>-<entorno>-<región>-<instancia>`, por ejemplo `rg-sigec-dev-eastus2-001`. Ningún nombre está escrito a mano en los recursos: los arma `modules/stack` a partir de las variables.
- **Secretos:** el `APP_KEY` de Laravel lo genera Terraform y solo vive en el Key Vault. La Container App lo lee con su Managed Identity.
- **Estado remoto:** cada ambiente guarda su `tfstate` en el Storage Account creado por `bootstrap/`.
- **Sin datos sensibles en el repo:** la suscripción llega por la variable de entorno `ARM_SUBSCRIPTION_ID`. Los `.tfvars` de cada ambiente solo tienen valores no sensibles.

## Requisitos

- Terraform 1.5 o superior y Azure CLI
- `az login` hecho en la suscripción del proyecto

## Uso (PowerShell)

```powershell
# 1. Suscripción (una vez por terminal)
$env:ARM_SUBSCRIPTION_ID = (az account show --query id -o tsv)

# 2. Estado remoto (solo la primera vez en todo el proyecto)
cd infra/bootstrap
terraform init
terraform apply
#    Genera envs/<ambiente>/backend.hcl con los datos del Storage Account

# 3. Un ambiente (ejemplo: dev)
cd ../envs/dev
terraform init -backend-config=backend.hcl
terraform plan  -var-file=dev.tfvars -out=dev.tfplan
terraform apply dev.tfplan

# 4. Al terminar de usarlo (ahorra créditos)
terraform destroy -var-file=dev.tfvars
```

> El estado de `bootstrap/` es local (`bootstrap/terraform.tfstate`, fuera de Git). No lo borres mientras exista el Storage Account del estado remoto.
