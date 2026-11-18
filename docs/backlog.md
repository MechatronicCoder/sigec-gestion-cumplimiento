# Backlog de SIGEC (Jira, proyecto SGC)

Este archivo es la referencia del backlog. El trabajo diario se gestiona en Jira, donde está la versión actualizada.

- **Responsables:** **W** = William (DevOps / Cloud Engineer, Scrum Master) · **J** = Jesús (Developer, Product Owner)
- **Story points:** escala Fibonacci.
- **Reparto:** W 50 pts · J 47 pts.
- **Definition of Done:** rama `tipo/SGC-n-descripcion` → PR con plantilla y revisión del otro integrante → CI en verde → squash merge a `main` → tarjeta en Done.

## Épicas

| Épica | Objetivo |
|---|---|
| Gestión del proyecto y backlog | Jira, repositorio, flujo de trabajo y ceremonias |
| Aplicación SIGEC lista para la nube | Configuración por variables, corrección de bugs, pruebas y evaluación de modernización |
| Contenedores e imágenes | Imágenes de producción y publicación en Docker Hub |
| Infraestructura como código | Terraform modular para dev, test y prod en Azure |
| CI/CD | GitHub Actions: build, pruebas, despliegue por ambientes y seguridad |
| Monitoreo y seguimiento | Grafana con métricas de infraestructura, logs y negocio |
| Documentación y presentación | Informe (6 apartados del curso), diapositivas y demo |

## Sprint 1 · Avance Sesión 2 (18 al 19 nov)
**Objetivo:** presentar la elección del software, el backlog en Jira y el ambiente dev creado con Terraform.

| Tipo | Resumen | Épica | Pts | Resp. |
|---|---|---|---|---|
| Tarea | Crear repositorio con código base limpio y README | Gestión | 1 | W |
| Tarea | Configurar Jira: épicas, sprints e integración GitHub for Jira | Gestión | 2 | W |
| Historia | Como equipo DevOps quiero la infraestructura definida en Terraform con módulos y ambientes dev/test/prod para recrearla sin pasos manuales | IaC | 5 | W |
| ↳ Subtarea | Bootstrap del backend remoto (Storage Account para el tfstate) | | | W |
| ↳ Subtarea | Módulos: resource group, Log Analytics, Container Apps Environment, Key Vault | | | W |
| ↳ Subtarea | Carpetas `envs/dev`, `envs/test`, `envs/prod` con su `.tfvars` y nombres con la nomenclatura de Azure | | | W |
| Tarea | Aplicar el ambiente dev y guardar las capturas de evidencia (`plan` y `apply`) | IaC | 3 | W |
| Tarea | Definir equipo, roles, problema de negocio y objetivos | Documentación | 2 | J |
| Tarea | Justificar la elección del software (SIGEC a medida frente a open source) | Documentación | 1 | J |
| Tarea | Diagrama de arquitectura en Azure | Documentación | 2 | J |
| Tarea | Informe de avance (apartados 1 a 3 del formato del curso) | Documentación | 3 | J |
| ↳ Subtarea | Apartado 1: equipo y roles | | | J |
| ↳ Subtarea | Apartado 2: problema de negocio y objetivos | | | J |
| ↳ Subtarea | Apartado 3: solución técnica DevOps (herramientas, nube, CI/CD, monitoreo) | | | J |
| Tarea | Diapositivas del avance de la Sesión 2 | Documentación | 2 | J |
| Bug | Exportar la auditoría a PDF falla: la vista `auditoria/export-pdf` está fuera de `backend/resources` | App | 1 | J |

## Sprint 2 · App lista para la nube y CI (20 nov al 1 dic)
**Objetivo:** la app se configura solo con variables de entorno, tiene imagen de producción en Docker Hub y CI en verde.

| Tipo | Resumen | Épica | Pts | Resp. |
|---|---|---|---|---|
| Historia | Como administrador quiero que SIGEC se configure solo con variables de entorno para desplegarlo en cualquier ambiente sin cambiar el código | App | 3 | J |
| ↳ Subtarea | URL de la API del frontend sin `localhost:8080` fijo | | | J |
| ↳ Subtarea | `APP_ENV` y `APP_DEBUG` por ambiente (debug apagado fuera de dev) | | | J |
| ↳ Subtarea | Sacar las contraseñas de MySQL del `docker-compose.yml` | | | J |
| Tarea | Usuarios iniciales del seeder sin contraseñas fijas en el código | App | 2 | J |
| Tarea | Health check y logs a stdout para la nube | App | 2 | J |
| Bug | Las pruebas borran la base de desarrollo: `env_file` pisa `phpunit.xml` | App | 2 | J |
| Tarea | Pruebas de la lógica de negocio (`ChecklistCalculator` y permisos por rol) | App | 3 | J |
| Tarea | Evaluar la factibilidad de modernizar partes del código PHP | App | 2 | J |
| Tarea | Dockerfile de producción multi-etapa (php-fpm y nginx con el código, frontend compilado) | Contenedores | 5 | W |
| Tarea | Publicar imágenes en Docker Hub etiquetadas con versión y SHA | Contenedores | 2 | W |
| Tarea | Módulo Terraform de Azure Database for MySQL y secretos en Key Vault | IaC | 5 | W |
| Tarea | CI en GitHub Actions: Pint, PHPUnit con MySQL, lint del frontend y build | CI/CD | 3 | W |
| Tarea | Proteger `main`: ruleset, plantilla de PR y CODEOWNERS | CI/CD | 1 | W |

## Sprint 3 · CD multiambiente y monitoreo (2 al 14 dic)
**Objetivo:** despliegue automático a dev, test y prod, y Grafana mostrando la salud del sistema. Se presenta en la Sesión 3 (15 dic).

| Tipo | Resumen | Épica | Pts | Resp. |
|---|---|---|---|---|
| Tarea | CD: promover la misma imagen dev → test → prod con aprobación manual | CI/CD | 5 | W |
| Tarea | Pipeline de Terraform: `plan` en el PR y `apply` en el merge | CI/CD | 3 | W |
| Tarea | Log Analytics y Grafana desplegados con Terraform | Monitoreo | 5 | W |
| Historia | Como administrador quiero ver en Grafana el cumplimiento por dimensión y las evaluaciones para seguir el avance de la organización | Monitoreo | 3 | J |
| Tarea | Dashboard de Grafana versionado en JSON (CPU, memoria, réplicas y logs) | Monitoreo | 3 | J |
| Tarea | Informe: apartados 3 y 4 (solución técnica y ejecución) | Documentación | 3 | J |
| Tarea | Diapositivas del avance de la Sesión 3 | Documentación | 2 | J |

## Sprint 4 · Aportes extra y cierre (16 dic al 25 ene)
**Objetivo:** aportes extra, informe final y demo ensayada para el 4 de febrero.

| Tipo | Resumen | Épica | Pts | Resp. |
|---|---|---|---|---|
| Tarea | Checkov (seguridad del Terraform) y Trivy (imagen) en el pipeline | CI/CD | 2 | W |
| Tarea | Key Vault y MySQL por red privada (Private Endpoint) | IaC | 5 | W |
| Tarea | Presentación final y video de respaldo de la demo | Documentación | 3 | W |
| Tarea | Implementar las mejoras de modernización acordadas | App | 5 | J |
| Tarea | Pruebas de API de punta a punta | App | 3 | J |
| Tarea | Informe final: apartados 5 y 6 y revisión completa | Documentación | 3 | J |
