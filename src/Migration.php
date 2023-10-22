<?php

namespace Astylodon\Migrations;

use Astylodon\Migrations\Database\DatabaseInterface;

/**
 * The base class for a migration
 */
interface Migration
{
    /**
     * The method that is called when this migration is applied, this is where all queries should be executed
     * 
     * @param DatabaseInterface $database The database instance
     */
    public function up(DatabaseInterface $database);
}