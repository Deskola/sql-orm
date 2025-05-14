<?php

namespace DeskolaOrm\Connections;

use DeskolaOrm\Exceptions\ConnectionsException;
use PDO;
use PDOException;
use Exception;

class Database
{
    private string $host;
    private string $database;
    private string $username;
    private string $password;
    private string $port;
    private ?string $url;
    private ?string $charset;
    private ?string $unix_socket;
    private ?string $collation;
    private ?string $prefix;
    private ?bool $prefix_indexes;
    private ?bool $strict;
    private ?string $engine;
    private ?array $options;

    // db instance
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    public function __construct(string $host, string $database, string $username, string $password, int $port = 3306, ?string $url = '', ?string $charset = 'utf8mb4', ?string $unix_socket = '', ?string $collation = 'utf8mb4_unicode_ci', ?string $prefix = '', ?bool $prefix_indexes = true, ?bool $strict = true, ?string $engine = null, ?array $options = [])
    {
        // ensure all required variables are passed
        $this->initVariables($host, $database, $username, $password, $port);

        // init variables
        $this->host = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
        $this->url = $url;
        $this->charset = $charset;
        $this->unix_socket = $unix_socket;
        $this->collation = $collation;
        $this->prefix = $prefix;
        $this->prefix_indexes = $prefix_indexes;
        $this->strict = $strict;
        $this->engine = $engine;
        $this->options = $options;

        // connect
        $this->connect();
    }

    private function initVariables(string $host, string $database, string $username, string $password, int $port)
    {
        $lazy_message = 'is required, but is currently missing';

        if (empty($host))
            throw new Exception('host ' . $lazy_message, 1);

        if (empty($database))
            throw new Exception('database ' . $lazy_message, 1);

        if (empty($username))
            throw new Exception('username ' . $lazy_message, 1);

        if (empty($port))
            throw new Exception('port ' . $lazy_message, 1);

    }

    public static function getInstance(string $host, string $database, string $username, string $password, int $port = 3306)
    {
        if (self::$instance === null) {
            self::$instance = new self($host, $database, $username, $password, $port);
        }

        return self::$instance;
    }

    private function connect()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->database};port={$this->port};charset={$this->charset};collation={$this->collation};unix_socket={$this->unix_socket};prefix={$this->prefix};prefix_indexes={$this->prefix_indexes};strict={$this->strict};engine={$this->engine}";
        //$dsn = "mysql:host={$this->host};dbname={$this->database};port={$this->port};charset={$this->charset}";

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    //PDO::MYSQL_ATTR_SSL_CA => null,
                PDO::ATTR_EMULATE_PREPARES => true,
                PDO::MYSQL_ATTR_LOCAL_INFILE => true,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException | Exception $e) {
            throw new ConnectionsException($e->getMessage(), code: 4060);
        }
    }

    public function getConnection()
    {
        if ($this->connection === null) {
            throw new Exception('Database connection is not established', 4060);
        }

        return $this->connection;
    }

    public function hasTable(string $table)
    {
        $sql = "select count(*) from information_schema.tables where table_name = :table AND table_schema = :database";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':database' => $this->database,
            ':table' => $table
        ]);

        return $stmt->fetchColumn() > 0;
    }

    // Check if columns exist in a table
    public function hasColumns(string $table, array $columns): bool
    {
        $placeholders = str_repeat('?,', count($columns) - 1) . '?';
        $query = "SELECT column_name 
                  FROM information_schema.columns 
                  WHERE table_schema = ? 
                    AND table_name = ? 
                    AND column_name IN ($placeholders)";

        $stmt = $this->connection->prepare($query);
        $stmt->execute(array_merge([$this->database, $table], $columns));

        $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Check if all columns exist
        return count($existingColumns) === count($columns);
    }

    public function close()
    {
        $this->connection = null;
    }



}