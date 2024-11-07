<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Connections\DbConnection;
use App\Migrations\TableMigration;

$host = '127.0.0.1';
$db_name = 'land';
$user = 'root';
$password = '';

try {
    $conn = new DbConnection($host, $db_name, $user, $password);

    $migration = new TableMigration($conn, 'continent');
    $res = $migration
        ->addColumn('continent_id', 'int')
        ->addColumn('continent_name', 'varchar', 20)
        ->addColumn('population', 'bigint')
        ->createTable();

    print_r($res);
} catch (\Throwable $th) {
    print_r($th->getMessage());
}


