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
            WHERE u.id_usuario = $id_usuario
            LIMIT 1
        ";
        $resultado = $this->db->query($sql);

        return $resultado[0]['nombre_rol'] ?? null;
    }

    public function asignarRolJugador($id_usuario): void
    {
        $result = $this->db->query("SELECT id_rol FROM roles WHERE nombre_rol = 'jugador'");
        $id_rol = $result[0]['id_rol'] ?? null;

        if ($id_rol) {
            $this->db->execute("UPDATE Usuarios SET id_rol = $id_rol WHERE id_usuario = $id_usuario");
        }
    }
}
