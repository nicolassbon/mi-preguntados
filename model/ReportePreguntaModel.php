<?php

class ReportePreguntaModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getPreguntasReportadasConDetalles(string $terminoBusqueda = '', $id_categoria = 'todasLasCategorias')
    {
        $where = "pr.estado = 'pendiente'";

        if (trim($terminoBusqueda) !== '') {
            $term = $this->db->escapeLike($terminoBusqueda);
            $where .= " AND p.pregunta LIKE '%$term%'";
        }

        if ($id_categoria !== 'todasLasCategorias') {
            $id_categoria = (int)$id_categoria;
            $where .= " AND p.id_categoria = $id_categoria";
        }

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
                pr.motivo
            FROM preguntas_reportadas pr
            JOIN preguntas p ON pr.id_pregunta = p.id_pregunta
            JOIN categoria c ON p.id_categoria = c.id_categoria
            JOIN usuarios u ON pr.id_reportador = u.id_usuario
            WHERE $where
            ORDER BY pr.fecha_reporte DESC
        ";

        return $this->db->query($sql);
    }

    public function actualizarEstadoReporte(int $id_reporte, string $nuevo_estado)
    {
        $sql = "UPDATE preguntas_reportadas SET estado = ? WHERE id_reporte = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nuevo_estado, $id_reporte);
        $stmt->execute();
    }

    public function aprobarReporte(int $id_pregunta, int $id_reporte = null)
    {
        $this->actualizarEstadoPregunta($id_pregunta, 'deshabilitada');

        if ($id_reporte) {
            $this->actualizarEstadoReporte($id_reporte, 'resuelto');
        } else {
            $sql = "UPDATE preguntas_reportadas SET estado = 'resuelto' WHERE id_pregunta = ? AND estado = 'pendiente'";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id_pregunta);
            $stmt->execute();
        }
    }

    public function descartarReporte(int $id_pregunta, int $id_reporte = null)
    {
        $this->actualizarEstadoPregunta($id_pregunta, 'activa');

        if ($id_reporte) {
            $this->actualizarEstadoReporte($id_reporte, 'descartado');
        } else {
            $sql = "UPDATE preguntas_reportadas SET estado = 'descartado' WHERE id_pregunta = ? AND estado = 'pendiente'";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id_pregunta);
            $stmt->execute();
        }
    }

    private function actualizarEstadoPregunta(int $id_pregunta, string $nuevo_estado)
    {
        $sql = "UPDATE preguntas SET estado = ? WHERE id_pregunta = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nuevo_estado, $id_pregunta);
        $stmt->execute();
    }


}