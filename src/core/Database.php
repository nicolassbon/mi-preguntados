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

    /**
     * Ejecuta una consulta preparada (SELECT) y devuelve todos los resultados.
     * Es la forma SEGURA de hacer consultas.
     *
     * @param string $sql La consulta SQL con marcadores de posición (?).
     * @param array $params Un array con los valores a vincular.
     * @param string $types Una cadena con los tipos de datos (ej: 'isd' para integer, string, double).
     * @return array Los resultados de la consulta.
     */
    public function query(string $sql, array $params = [], string $types = ""): array
    {
        // Si no se pasan tipos, los adivina (solo funciona para 'i', 'd', 's')
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
        // El operador '...' (splat) desempaqueta el array de parámetros, es clave aquí.
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        // Si no hay resultados (ej. en un INSERT), devuelve un array vacío
        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /*
    public function query($sql): array
    {
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function execute($sql): void
    {
        $this->conn->query($sql);
    }
    */


    public function execute(string $sql, array $params = [], string $types = ""): void
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
        $stmt->close();
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
