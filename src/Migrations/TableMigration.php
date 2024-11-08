<?php

namespace App\Migrations;

use App\Connections\DbConnection;
use App\Exceptions\MigrationException;
use Exception;

enum Constraints
{
    case PrimaryKey;
    case PrimaryKeyAuto;
    case ForeignKey;
    case NotNull;
    case Unique;
    case Check;
    case Default;
    case Null;
}

class TableMigration
{
    private array $columns = [];
    private array $special_constraints = [];

    public function __construct(private DbConnection $dbConnection, private string $tableName)
    {
    }

    public function addColumn(string $column, string $type, mixed $length = null, array $constraints = [], array $fkReference = [], mixed $defaultValue = null, mixed $checkIf = null, mixed $comment = null): self
    {
        // build column features
        $column_builder = "$column,";

        // check if length is provided
        $column_builder .= !empty($length) ? "$type($length)," : "$type,";
        
        // constraint
        if (!empty($constraints)) {
            foreach ($constraints as $constraint) {
                if ($constraint instanceof Constraints) {
                    match ($constraint) {
                        Constraints::PrimaryKey => $this->special_constraints['primary key'][] = $column,
                        Constraints::PrimaryKeyAuto => $this->special_constraints['primary key auto'] = $column,
                        Constraints::ForeignKey => $this->special_constraints['foreign key'][$column] = $fkReference,
                        Constraints::NotNull => $column_builder .= 'not null,',
                        Constraints::Null => $column_builder .= 'null,',
                        Constraints::Unique => $column_builder .= 'unique,',
                        Constraints::Default => $column_builder .= !empty($defaultValue) ? "DEFAULT $defaultValue," : "DEFAULT NULL,",
                        Constraints::Check => $this->special_constraints['constraint check'][$column] = $checkIf,
                        default => ''
                    };
                }
            }
        }

        array_push($this->columns, str_replace(',', ' ', $column_builder));

        return $this;
    }

    public function create()
    {
        try {
            $sql = "create table {$this->tableName}";
            $sql .= '(';

            foreach ($this->columns as $key => $column) {
                $sql .= ' ' . str_replace(['\'', '"'], '', $column) . ',';
            }

            foreach ($this->special_constraints as $constraint => $columns) {

                // primary key
                if ($constraint === 'primary key') {
                    $sql .= $constraint . '(' . join(',', $columns) . '),';
                }

                // foreign key
                if ($constraint === 'foreign key') {
                    foreach ($columns as $key => $value) {
                        $foreign_table = array_key_first($value);
                        $foreign_column = $value[$foreign_table];

                        $sql .= $constraint . '(' . $key . ') references ' . $foreign_table . '(' . $foreign_column . '),';
                    }
                }

                // constraint check
                if ($constraint === 'constraint check') {
                    foreach ($columns as $column => $condition) {
                        $sql .= $constraint . '(' . $column . ' ' . $condition . '),';
                    }
                }

            }

            $sql = rtrim($sql, ',') . ');';
            //dd($sql);
            return $this->dbConnection->connect()->exec($sql);

        } catch (Exception $e) {
            throw $e;
            //throw new  MigrationException($e->getMessage(), 112);
        }
    }
}