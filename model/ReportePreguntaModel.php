<?php

class ReportePreguntaModel
{
    private Database $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getPreguntasReportadasConDetalles(string|int $id_categoria = 'todasLasCategorias', string $terminoBusqueda = '', string $estado = 'pendiente'): array
    {
        $where = '1=1';
        if (trim($terminoBusqueda) !== '') {
            $term = $this->db->escapeLike($terminoBusqueda);
            $where .= " AND p.pregunta LIKE '%$term%'";
        }

        if ($id_categoria !== 'todasLasCategorias') {
            $id_categoria = (int)$id_categoria;
            $where .= " AND p.id_categoria = $id_categoria";
        }

        if ($estado !== 'todos') {
            $where .= " AND pr.estado = '$estado'";
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
                pr.motivo,
                pr.estado
            FROM preguntas_reportadas pr
            JOIN preguntas p ON pr.id_pregunta = p.id_pregunta
            JOIN categoria c ON p.id_categoria = c.id_categoria
            JOIN usuarios u ON pr.id_reportador = u.id_usuario
            WHERE $where
            ORDER BY pr.fecha_reporte DESC
        ";

        return $this->db->query($sql);
    }

    public function actualizarEstadoReporte(int $id_reporte, string $nuevo_estado): void
    {
        $sql = "UPDATE preguntas_reportadas SET estado = ? WHERE id_reporte = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nuevo_estado, $id_reporte);
        $stmt->execute();
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
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id_pregunta);
            $stmt->execute();
        }
    }

   private function actualizarEstadoRespuestas (int $id_pregunta, int $activa): void {
        $sql = "UPDATE respuestas SET activa = ? WHERE id_pregunta = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $activa, $id_pregunta);
        $stmt->execute();
   }


    public function descartarReporte(int $id_pregunta, int $id_reporte = null): void
    {
        $this->actualizarEstadoPregunta($id_pregunta, 'activa');

        if ($id_reporte) {
            $this->actualizarEstadoReporte($id_reporte, 'descartado');
        } else {
            $sql = "UPDATE preguntas_reportadas SET estado = 'descartado'
                    WHERE id_pregunta = ? AND estado = 'pendiente'";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id_pregunta);
            $stmt->execute();
        }
    }

    private function actualizarEstadoPregunta(int $id_pregunta, string $nuevo_estado): void
    {
        $sql = "UPDATE preguntas SET estado = ? WHERE id_pregunta = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nuevo_estado, $id_pregunta);
        $stmt->execute();
    }

}
