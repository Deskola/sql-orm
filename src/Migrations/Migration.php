<?php

namespace DeskolaOrm\Migrations;

use DeskolaOrm\Connections\Database;
use Exception;

enum Constraints
{
    case PrimaryKey;
    case PrimaryKeyAuto;
    case ForeignKey;
    case Unique;
    case Check;
    case Default;
    case Null;
}

class Migration
{
    private array $columns = [];
    private array $special_constraints = [];

    public function __construct(private Database $connection, private string $table)
    {

    }

    /**
     * Add new column
     * @param string $column  
     * @param string $type e.g varchar,int,text,longtext,date  
     * @param int $length   
     * @param array $constraints  
     * @param array $fkReference  
     * @param string $defaultValue  
     * @param string $checkIf  
     * @param string $comment 
     */
    public function addColumn(string $column, string $type, ?int $length = null, array $constraints = [], array $fkReference = [], mixed $defaultValue = null, mixed $checkIf = null, ?string $comment = null): self
    {
        // build column features
        $column_builder = "$column,";

        // check if length is provided
        $column_builder .= !empty($length) ? ($type == 'string' ? "varchar($length)" : "$type($length),") : "$type,";

        // constraint
        if (!empty($constraints)) {
            foreach ($constraints as $constraint) {
                if ($constraint instanceof Constraints) {
                    match ($constraint) {
                        Constraints::PrimaryKey => $this->special_constraints['primary key'][] = $column,
                        Constraints::PrimaryKeyAuto => $this->special_constraints['primary key auto'] = $column,
                        Constraints::ForeignKey => $this->special_constraints['foreign key'][$column] = $fkReference,
                        Constraints::Null => $column_builder .= 'null,',
                        Constraints::Unique => $column_builder .= 'unique,',
                        Constraints::Check => $this->special_constraints['constraint check'][$column] = $checkIf,
                        default => $column_builder .= !empty($defaultValue) ? "default $defaultValue," : "default null,"
                    };
                }
            }

            // null is not defined in constraints
            if (!in_array(Constraints::Null, $constraints)) {
                $column_builder .= " not null,";
            }

        } else {
            $column_builder .= " not null,";
        }

        array_push($this->columns, str_replace(',', ' ', $column_builder));

        return $this;
    }

    public function create(): void
    {
        try {

            // check if table exist
            if ($this->connection->hasTable($this->table))
                return;

            // create table
            $sql = "create table {$this->table}";
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
            $this->connection->getConnection()->exec($sql);

        } catch (Exception $e) {
            throw $e;
            //throw new  MigrationException($e->getMessage(), 112);
        } finally {
            $this->connection->close();
        }
    }
}