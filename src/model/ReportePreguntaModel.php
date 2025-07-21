<?php

namespace App\model;

use App\core\Database;

class ReportePreguntaModel
{
    private Database $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getPreguntasReportadasConDetalles(string|int $id_categoria = 'todasLasCategorias', string $terminoBusqueda = '', string $estado = 'pendiente'): array
    {
        $where = [];
        $params = [];
        $types = '';

        if (trim($terminoBusqueda) !== '') {
            $where[] = "p.pregunta LIKE ?";
            $params[] = '%' . $this->db->escapeLike($terminoBusqueda) . '%';
            $types .= 's';
        }

        if ($id_categoria !== 'todasLasCategorias') {
            $where[] = "p.id_categoria = ?";
            $params[] = (int)$id_categoria;
            $types .= 'i';
        }

        if ($estado !== 'todos') {
            $where[] = "pr.estado = ?";
            $params[] = $estado;
            $types .= 's';
        }

        $whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT
                pr.id_reporte,
                pr.id_pregunta,
                p.pregunta,
                c.nombre AS nombre_categoria,
                pr.id_reportador,
                u.nombre_usuario AS reportador_usuario,
                u.email AS reportador_email,
                pr.fecha_reporte,
                pr.motivo,
                pr.estado
            FROM preguntas_reportadas pr
            JOIN preguntas p ON pr.id_pregunta = p.id_pregunta
            JOIN categoria c ON p.id_categoria = c.id_categoria
            JOIN usuarios u ON pr.id_reportador = u.id_usuario
            $whereSql
            ORDER BY pr.fecha_reporte DESC
        ";

        return $this->db->query($sql, $params, $types);
    }

    public function actualizarEstadoReporte(int $id_reporte, string $nuevo_estado): void
    {
        $sql = "UPDATE preguntas_reportadas SET estado = ? WHERE id_reporte = ?";
        $this->db->execute($sql, [$nuevo_estado, $id_reporte], "si");
    }

    public function aprobarReporte(int $id_pregunta, int $id_reporte = null): void
    {
        $this->actualizarEstadoPregunta($id_pregunta, 'deshabilitada');
        $this->actualizarEstadoRespuestas($id_pregunta, 0);

        if ($id_reporte) {
            $this->actualizarEstadoReporte($id_reporte, 'aprobado');
        } else {
            $sql = "UPDATE preguntas_reportadas SET estado = 'aprobado'
                    WHERE id_pregunta = ? AND estado = 'pendiente'";
            $this->db->execute($sql, [$id_pregunta], "i");
        }
    }

    private function actualizarEstadoRespuestas(int $id_pregunta, int $activa): void
    {
        $sql = "UPDATE respuestas SET activa = ? WHERE id_pregunta = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $activa, $id_pregunta);
        $stmt->execute();
        $this->db->execute($sql, [$activa, $id_pregunta], "ii");
    }


    public function descartarReporte(int $id_pregunta, int $id_reporte = null): void
    {
        $this->actualizarEstadoPregunta($id_pregunta, 'activa');

        if ($id_reporte) {
            $this->actualizarEstadoReporte($id_reporte, 'descartado');
        } else {
            $sql = "UPDATE preguntas_reportadas SET estado = 'descartado'
                    WHERE id_pregunta = ? AND estado = 'pendiente'";
            $this->db->execute($sql, [$id_pregunta], "i");
        }
    }

    private function actualizarEstadoPregunta(int $id_pregunta, string $nuevo_estado): void
    {
        $sql = "UPDATE preguntas SET estado = ? WHERE id_pregunta = ?";
        $this->db->execute($sql, [$nuevo_estado, $id_pregunta], "si");
    }

}
