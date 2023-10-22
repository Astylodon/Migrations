<?php

namespace Astylodon\Migrations\Database;

/**
 * The base database interface to be implemented by a database instance
 */
interface DatabaseInterface
{
    /**
     * Executes a store query
     * 
     * @param string $query The query to execute
     * @param mixed $params The parameters to bind in the query
     */
    public function exec(string $query, mixed ...$params);

    /**
     * Executes a query and return a single row
     * 
     * @param string $query The query to execute
     * @param mixed $params The parameters to bind in the query
     */
    public function get(string $query, mixed ...$params);

    /**
     * Executes a query and returns all rows
     * 
     * @param string $query The query to execute
     * @param mixed $params The parameters to bind in the query
     */
    public function getAll(string $query, mixed ...$params);
}