<?php

namespace App\Connections;

use App\Exceptions\ConnectionsException;
use PDO;
use PDOException;

class DbConnection
{
    public function __construct(private string $dbHost, private string $dbName, private string $dbUser, private string $dbPwd, private int $dbPort = 3306, private string $dbEncode = 'UTF8')
    {

    }

    public function connect(): PDO
    {
        $dns = "mysql:host={$this->dbHost};dbname={$this->dbName};port={$this->dbPort};charset={$this->dbEncode}";

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_LOCAL_INFILE => true,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            return new PDO($dns, $this->dbUser, $this->dbPwd, $options);

        } catch (PDOException $e) {
            throw new ConnectionsException($e->getMessage(), 111);
        }
    }

    public function close(): void
    {

    }
}