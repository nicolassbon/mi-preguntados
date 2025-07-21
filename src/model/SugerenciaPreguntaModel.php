<?php

namespace App\model;

use App\core\Database;

class SugerenciaPreguntaModel
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function agregarSugerencia($id_usuario, $id_pregunta, $id_categoria): void
    {
        $sql = "INSERT INTO sugerencias_preguntas(id_usuario, id_pregunta, id_categoria, fecha_envio, estado, fecha_resolucion)
                VALUES (?, ?, ?, NOW(), ?, ?)";
        $this->db->execute($sql, [$id_usuario, $id_pregunta, $id_categoria, 'pendiente', null], 'iiiss');
    }

    public function getPreguntasSugeridas(string $terminoBusqueda = '', string|int $id_categoria = 'todasLasCategorias', string $estado = 'pendiente'): array
    {
        $where = "1=1";

        if ($terminoBusqueda !== '') {
            $term = $this->db->escapeLike($terminoBusqueda);
            $where .= " AND p.pregunta LIKE '%$term%'";
        }

        if ($id_categoria !== 'todasLasCategorias') {
            $where .= " AND p.id_categoria = " . (int)$id_categoria;
        }

        if ($estado !== 'todos') {
            $where .= " AND s.estado = '$estado'";
        }

        $sql = "
            SELECT DISTINCT p.id_pregunta, p.pregunta, c.nombre, u.nombre_usuario, u.email, s.estado
            FROM preguntas p
            JOIN categoria c ON p.id_categoria = c.id_categoria
            JOIN sugerencias_preguntas s ON s.id_pregunta = p.id_pregunta
            JOIN usuarios u ON s.id_usuario = u.id_usuario
            WHERE $where
            ORDER BY s.fecha_envio DESC
        ";
        return $this->db->query($sql);
    }

    public function getAutorDePreguntaSugerida($id_pregunta)
    {
        $sql = "
            SELECT u.nombre_usuario, u.email
            FROM sugerencias_preguntas sp
            JOIN usuarios u ON sp.id_usuario = u.id_usuario
            WHERE sp.id_pregunta = ?
            LIMIT 1
        ";
        $resultado = $this->db->query($sql, [$id_pregunta], 'i');
        return $resultado[0] ?? null;
    }

    public function activarPreguntaSugerida($id): void
    {
        $this->db->execute("UPDATE preguntas SET estado = ? WHERE id_pregunta = ?", ['activa', $id], 'si');
        $this->db->execute("UPDATE respuestas SET activa = '1' WHERE id_pregunta = ?", [$id], 'i');
    }

    public function desactivarPreguntaSugerida($id): void
    {
        $this->db->execute("UPDATE preguntas SET estado = ? WHERE id_pregunta = ?", ['deshabilitada', $id], 'si');
        $this->db->execute("UPDATE respuestas SET activa = '0' WHERE id_pregunta = ?", [$id], 'i');
    }

    public function fechaResolucionSugerencia($id): void
    {
        $this->db->execute("UPDATE sugerencias_preguntas SET fecha_resolucion = NOW() WHERE id_pregunta = ?", [$id], 'i');
    }

    public function actualizarEstadoPregunta($id, $estado): void
    {
        $this->db->execute("UPDATE sugerencias_preguntas SET estado = ? WHERE id_pregunta = ?", [$estado, $id], 'si');
    }
}
