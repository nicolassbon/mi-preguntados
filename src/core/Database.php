<?php

namespace App\core;

use mysqli;
use mysqli_stmt;

class Database
{

    private Mysqli $conn;

    public function __construct($servername, $username, $dbname, $password)
    {
        $this->conn = new Mysqli($servername, $username, $password, $dbname);
        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }
    }

    public function query(string $sql, array $params = [], string $types = ""): array
    {
        if ($types === "" && !empty($params)) {
            foreach ($params as $param) {
                $types .= match (true) {
                    is_int($param) => 'i',
                    is_float($param) => 'd',
                    default => 's'
                };
            }
        }

        $stmt = $this->conn->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function execute(string $sql, array $params = [], string $types = ""): bool
    {
        if ($types === "" && !empty($params)) {
            foreach ($params as $param) {
                $types .= match (true) {
                    is_int($param) => 'i',
                    is_float($param) => 'd',
                    default => 's'
                };
            }
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function prepare($sql): bool|mysqli_stmt
    {
        return $this->conn->prepare($sql);
    }

    public function getLastInsertId()
    {
        return $this->conn->insert_id;
    }

    public function __destruct()
    {
        $this->conn->close();
    }

    public function escapeLike($string): string
    {
        return $this->conn->real_escape_string($string);
    }
}
