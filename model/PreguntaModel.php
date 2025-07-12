<?php

class PreguntaModel
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getPreguntaPorId(int $id_pregunta)
    {
        $sql = "
            SELECT p.id_pregunta, p.pregunta, p.estado, c.nombre
            FROM preguntas p
            JOIN categoria c ON p.id_categoria = c.id_categoria
            WHERE p.id_pregunta = ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_pregunta);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $result[0] ?? null;
    }

    public function agregarPregunta(string $pregunta, int $id_categoria)
    {
        $sql = "INSERT INTO preguntas (pregunta, id_categoria, entregadas, correctas, estado)
                VALUES (?, ?, 0, 0, 'sugerida')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $pregunta, $id_categoria);
        $stmt->execute();
        return $this->db->insert_id;
    }

    public function buscarPreguntaCreada(string $pregunta)
    {
        $sql = "SELECT id_pregunta FROM preguntas WHERE pregunta = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $pregunta);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['id_pregunta'] ?? null;
    }

    public function agregarRespuestas($id_pregunta, $opcion, $opcion2, $opcion3, $opcion4, $opcionCorrecta): void
    {

        $insertRespuesta = "INSERT INTO respuestas (respuesta, esCorrecta, id_pregunta, activa) VALUES (?, ?, ?, ?)";
        $esCorrecta = 0;
        $activo = 0;

        if ($opcionCorrecta === 1) {
            $esCorrecta = 1;
        }

        $sql = $insertRespuesta;
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("siii", $opcion, $esCorrecta, $id_pregunta, $activo);
        $stmt->execute();

        $esCorrecta = 0;

        if ($opcionCorrecta === 2) {
            $esCorrecta = 1;
        }

        $sql = $insertRespuesta;
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("siii", $opcion2, $esCorrecta, $id_pregunta, $activo);
        $stmt->execute();

        $esCorrecta = 0;

        if ($opcionCorrecta === 3) {
            $esCorrecta = 1;
        }

        $sql = $insertRespuesta;
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("siii", $opcion3, $esCorrecta, $id_pregunta, $activo);
        $stmt->execute();

        $esCorrecta = 0;

        if ($opcionCorrecta === 4) {
            $esCorrecta = 1;
        }

        $sql = $insertRespuesta;
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("siii", $opcion4, $esCorrecta, $id_pregunta, $activo);
        $stmt->execute();
    }

    public function getRespuestasPorPregunta(int $id_pregunta)
    {
        $sql = "SELECT id_respuesta, respuesta, esCorrecta FROM respuestas WHERE id_pregunta = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_pregunta);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function incrementarEntregadasPregunta(int $id_pregunta): void
    {
        $stmt = $this->db->prepare("UPDATE preguntas SET entregadas = entregadas + 1 WHERE id_pregunta = ?");
        $stmt->bind_param("i", $id_pregunta);
        $stmt->execute();
    }

    public function incrementarCorrectasPregunta(int $id_pregunta): void
    {
        $stmt = $this->db->prepare("UPDATE preguntas SET correctas = correctas + 1 WHERE id_pregunta = ?");
        $stmt->bind_param("i", $id_pregunta);
        $stmt->execute();
    }

    public function activarPregunta(int $id_pregunta): void
    {
        $stmt = $this->db->prepare("UPDATE preguntas SET estado = 'activa' WHERE id_pregunta = ?");
        $stmt->bind_param("i", $id_pregunta);
        $stmt->execute();
    }

    public function desactivarPregunta(int $id_pregunta): void
    {
        $stmt = $this->db->prepare("UPDATE preguntas SET estado = 'deshabilitada' WHERE id_pregunta = ?");
        $stmt->bind_param("i", $id_pregunta);
        $stmt->execute();
    }

    public function actualizarPregunta(int $id_pregunta, string $textoPregunta): void
    {
        $stmt = $this->db->prepare("UPDATE preguntas SET pregunta = ? WHERE id_pregunta = ?");
        $stmt->bind_param("si", $textoPregunta, $id_pregunta);
        $stmt->execute();
    }

    public function actualizarRespuesta(int $id_respuesta, string $textoRespuesta): void
    {
        $stmt = $this->db->prepare("UPDATE respuestas SET respuesta = ? WHERE id_respuesta = ?");
        $stmt->bind_param("si", $textoRespuesta, $id_respuesta);
        $stmt->execute();
    }

    public function getPreguntas(string $terminoBusqueda = ''): array
    {
        $where = "1=1";
        if (trim($terminoBusqueda) !== '') {
            $term = $this->db->escapeLike($terminoBusqueda);
            $where .= " AND p.pregunta LIKE '%$term%'";
        }

        $sql = "
            SELECT p.id_pregunta, p.pregunta, c.nombre, p.estado
            FROM preguntas p
            JOIN categoria c ON p.id_categoria = c.id_categoria
            LEFT JOIN sugerencias_preguntas sp ON sp.id_pregunta = p.id_pregunta
            WHERE (sp.estado IS NULL OR sp.estado != 'rechazada') AND $where
            ORDER BY p.id_pregunta
        ";

        return $this->db->query($sql);
    }

    public function getPreguntasPorCategoria(int $id_categoria, string $terminoBusqueda = ''): array
    {
        $where = "p.id_categoria = $id_categoria";
        if (trim($terminoBusqueda) !== '') {
            $term = $this->db->escapeLike($terminoBusqueda);
            $where .= " AND p.pregunta LIKE '%$term%'";
        }

        $sql = "
            SELECT p.id_pregunta, p.pregunta, c.nombre, p.estado
            FROM preguntas p
            JOIN categoria c ON p.id_categoria = c.id_categoria
            LEFT JOIN sugerencias_preguntas sp ON sp.id_pregunta = p.id_pregunta
            WHERE (sp.estado IS NULL OR sp.estado != 'rechazada')
            AND $where
        ";

        return $this->db->query($sql);
    }

    public function insertarReportePregunta($id_pregunta, $id_reportador, $motivo): void
    {
        $sql = "INSERT INTO `preguntas_reportadas` (`id_pregunta`, `id_reportador`, `motivo`) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $id_pregunta, $id_reportador, $motivo);
        $stmt->execute();
    }

    public function actualizarEstadoPregunta($id_pregunta, $nuevo_estado): void
    {
        $sql = "UPDATE `preguntas` SET `estado` = ? WHERE `id_pregunta` = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nuevo_estado, $id_pregunta);
        $stmt->execute();
    }

}
