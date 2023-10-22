<?php

namespace Astylodon\Migrations;

use Astylodon\Migrations\Database\DatabaseInterface;

/**
 * Applies database migrations on a database
 */
class Migrate
{
    private DatabaseInterface $database;
    private string $driver;
    private array $migrations;

    /**
     * Constructs a new migrate instance
     * 
     * @param DatabaseInterface $datanase The database instance
     * @param string $driver The driver name
     */
    public function __construct(DatabaseInterface $database, string $driver)
    {
        $this->database = $database;
        $this->driver = $driver;
        $this->migrations = [];
    }

    /**
     * Finds and adds all migrations from a folder
     * 
     * @param string $path The path of the folder
     */
    public function findMigrations(string $path)
    {
        $path = rtrim($path, "/");
        $files = scandir($path);

        foreach ($files as $file)
        {
            // Filter on PHP files
            if (strpos($file, ".php") !== false)
            {
                array_push($this->migrations, $path . "/" . $file);
            }
        }
    }

    /**
     * Migrates the database by applying all migrations
     */
    public function migrate()
    {
        // Get the applied migrations
        $applied = $this->getMigrations();

        // Remove all migrations that have already been applied
        $migrations = $this->diffMigrations($this->migrations, $applied);

        // Apply all migrations
        foreach ($migrations as $file)
        {
            $name = pathinfo($file, PATHINFO_FILENAME);

            echo $name;

            $migration = require $file;
            $this->applyMigration($name, $migration);

            echo " \033[32mOK\033[0m" . PHP_EOL;
        }
    }

    /**
     * Apply a migration
     * 
     * @param string $name The name of the migration
     * @param Migration $migration The migration instance
     */
    private function applyMigration(string $name, Migration $migration)
    {
        // Apply the migration
        $migration->up($this->database);

        // Add the migration to applied migrations
        $this->database->exec("INSERT INTO `migrations` VALUES (?)", $name);
    }

    /**
     * Gets all applied migrations from the database
     */
    private function getMigrations()
    {
        // Ensure the migrations table exists
        $this->ensureMigrations();

        $migrations = $this->database->getAll("SELECT `migration` FROM `migrations`");

        return array_map(function ($row) { return $row->migration; }, $migrations);
    }

    /**
     * Returns all migrations that are not already applied
     * 
     * @param array $total The migrations that should be filtered
     * @param array $applied The applied migrations
     */
    private function diffMigrations(array $total, array $applied)
    {
        $migrations = [];

        foreach ($total as $migration)
        {
            $name = pathinfo($migration, PATHINFO_FILENAME);

            // Only add the migration if it has not already been applied
            if (!in_array($name, $applied))
            {
                array_push($migrations, $migration);
            }
        }

        return $migrations;
    }

    /**
     * Ensures the migrations table exists, and creates it if needed
     */
    private function ensureMigrations()
    {
        if ($this->driver == "sqlite")
        {
            $this->database->exec("CREATE TABLE IF NOT EXISTS `migrations` (`migration` TEXT PRIMARY KEY)");
        }
        else
        {
            $this->database->exec("CREATE TABLE IF NOT EXISTS `migrations` (`migration` VARCHAR(50) PRIMARY KEY)");
        }
    }
}