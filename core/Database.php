<?php

class Database
{

    private $conn;

    public function __construct($servername, $username, $dbname, $password)
    {
        $this->conn = new Mysqli($servername, $username, $password, $dbname) or die("Error de conexion " . mysqli_connect_error());
    }

    public function query($sql): array
    {
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function execute($sql): void
    {
        $this->conn->query($sql);
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
