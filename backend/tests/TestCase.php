<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Crea la aplicación y se niega a seguir si la base de datos no es de pruebas.
     *
     * Las pruebas usan RefreshDatabase, que borra todas las tablas. Si el contenedor
     * hereda las variables de desarrollo (por ejemplo, el env_file de Docker Compose),
     * apuntarían a la base real y la vaciarían. Esta guarda lo impide.
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        $connection = $app['config']->get('database.default');
        $database = (string) $app['config']->get("database.connections.{$connection}.database");

        if ($database !== ':memory:' && ! str_ends_with($database, '_test')) {
            throw new RuntimeException(
                "Las pruebas solo pueden usar una base de datos de pruebas (':memory:' o un nombre que termine en _test). "
                ."Base configurada: '{$database}'. Revisa la sección de pruebas del README del backend."
            );
        }

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Las vistas usan @vite; en pruebas no hace falta compilar los assets.
        $this->withoutVite();
    }
}
