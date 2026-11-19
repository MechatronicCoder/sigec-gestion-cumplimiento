# SIGEC · Backend (Laravel)

API y vistas del Sistema de Gestión de Cumplimiento.

- Framework: Laravel 13 (PHP 8.4)
- Paquetes principales: Sanctum (autenticación), Spatie Permission (roles), DomPDF y Laravel Excel (reportes)
- Base de datos: MySQL 8

Las instrucciones para levantar el proyecto están en el [README principal](../README.md).

## Pruebas

Las pruebas usan `RefreshDatabase`, que borra y recrea todas las tablas. Por eso solo corren sobre una base de datos de pruebas: `tests/TestCase.php` se detiene con un error si la base configurada no es `:memory:` ni termina en `_test`. Así se evita vaciar la base de desarrollo, que el contenedor recibe por el `env_file` de Docker Compose.

Con Docker Compose, la opción más simple es SQLite en memoria:

```bash
docker compose exec -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: backend php artisan test
```

Para probar contra MySQL, crea una vez la base `sigec_test` y apunta las pruebas a ella:

```bash
docker compose exec mysql mysql -uroot -p -e "CREATE DATABASE IF NOT EXISTS sigec_test; GRANT ALL ON sigec_test.* TO 'sigec_user'@'%';"
docker compose exec -e DB_DATABASE=sigec_test backend php artisan test
```

Fuera de Docker, `php artisan test` usa SQLite en memoria según `phpunit.xml`. En GitHub Actions las pruebas corren contra un servicio MySQL con la base `sigec_test`.
