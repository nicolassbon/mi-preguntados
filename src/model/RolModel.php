<?php

namespace App\model;

use App\core\Database;

class RolModel
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getRolDelUsuario(int $id_usuario): ?string
    {
        $sql = "
            SELECT r.nombre_rol
            FROM Usuarios u
            JOIN Roles r ON u.id_rol = r.id_rol
            WHERE u.id_usuario = ?
            LIMIT 1
        ";
        $resultado = $this->db->query($sql, [$id_usuario], "i");

        return $resultado[0]['nombre_rol'] ?? null;
    }

    public function asignarRolJugador(int $id_usuario): void
    {
        $sql = "SELECT id_rol FROM roles WHERE nombre_rol = 'jugador'";
        $result = $this->db->query($sql);
        $id_rol = $result[0]['id_rol'] ?? null;

        if ($id_rol) {
            $execute_sql = "UPDATE roles SET id_rol = ? WHERE id_rol = ?";
            $this->db->execute($execute_sql, [$id_rol, $id_usuario], "ii");
        }
    }
}
