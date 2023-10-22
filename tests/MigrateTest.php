<?php

require_once "Database.php";

use Astylodon\Migrations\Migrate;
use PHPUnit\Framework\TestCase;

class MigrateTest extends TestCase
{
    public function testFind()
    {
        $database = new Database();
        $migrate = new Migrate($database, "sqlite");

        $migrate->findMigrations(__DIR__ . "/migrations");

        // Check if all migrations have been found
        $reflection = new \ReflectionClass(Migrate::class);
        $property = $reflection->getProperty("migrations");

        $this->assertCount(2, $property->getValue($migrate));
    }

    public function testMigrate()
    {
        $database = new Database();
        $migrate = new Migrate($database, "sqlite");

        $migrate->findMigrations(__DIR__ . "/migrations");
        $migrate->migrate();

        // Check the migrations table
        $count = $database->getScalar("SELECT COUNT(migration) FROM migrations");
        $this->assertEquals(2, $count);

        $migration = $database->getScalar("SELECT migration FROM migrations LIMIT 1");
        $this->assertEquals("0001_initial_create", $migration);

        $migration = $database->getScalar("SELECT migration FROM migrations LIMIT 1 OFFSET 1");
        $this->assertEquals("0002_add_users", $migration);

        // Check if migrations have been applied
        $name = $database->getScalar("SELECT name FROM sqlite_master WHERE name = 'posts'");
        $this->assertNotEmpty($name);

        $name = $database->getScalar("SELECT name FROM sqlite_master WHERE name = 'users'");
        $this->assertNotEmpty($name);
    }
}