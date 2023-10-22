# Migrations

A simple database migrations library for PHP.

## Usage

```
composer require astylodon/migrations
```

### Database

This library is independent of a database implementation, we instead provide a `DatabaseInterface` interface that should be implemented by the database implementation.

### Example

Create a file that you will invoke when your migrations run.

```php
<?php

use Astylodon\Migrations\Migrate;

$migrate = new Migrate($database, "mysql");

$migrate->findMigrations(__DIR__ . "/migrations");
$migrate->migrate();
```

### Migrations

Migrations should be placed inside a folder you pass to `findMigrations`, migrations are applied by alphabetical order. Applied migrations will be stored in a table named *migrations*.

```php
<?php

// 0001_initial_create.php

use Astylodon\Migrations\Database\DatabaseInterface;
use Astylodon\Migrations\Migration;

return new class implements Migration
{
    public function up(DatabaseInterface $database)
    {
        // Your database migrations here
    }
};
```