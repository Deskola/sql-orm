<?php

namespace App\Migrations;

use App\Connections\DbConnection;
use App\Exceptions\MigrationException;
use Exception;

class TableMigration
{
    private array $columns = [];

    public function __construct(private DbConnection $dbConnection, private string $tableName)
    {
    }

    public function addColumn(string $columnName, string $dataType, mixed $length = null, mixed $default = null, bool $nullable = true, bool $autoIncrement = false, bool $isPrimary = false, bool $isUnique = false): self
    {
        // build column features
        $column_builder = "$columnName,";

        // check if length is provided
        $column_builder .= !empty($length) ? "$dataType($length)," : "$dataType,";

        // check if field os nullable
        $column_builder .= $nullable ? "NULL," : "NOT NULL,";

        // check if default value is provided
        if (!empty($default)) {
            $column_builder .= "DEFAULT $default,";
        }

        // check if field is primary
        if ($isPrimary && $autoIncrement) {
            $column_builder .= "PRIMARY AUTO_INCREMENT,";
        } else if ($isPrimary && !$autoIncrement) {
            $column_builder .= "PRIMARY,";
        }

        array_push($this->columns, str_replace(',', ' ', $column_builder));

        return $this;
    }

    public function createTable()
    {
        
        try {
            $sql = "create table {$this->tableName}";
            $sql .= "(";

            foreach ($this->columns as $key => $column) {
                $sql .= ' ' . str_replace(['\'', '"'], '', $column) . ',';
            }
            
            $sql = rtrim($sql, ',') . ");";

            return $this->dbConnection->connect()->exec($sql);
            
        } catch (Exception $e) {
            throw $e;
            //throw new  MigrationException($e->getMessage(), 112);
        }
    }
}