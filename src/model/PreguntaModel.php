<?php

namespace App\model;

use App\core\Database;

class PreguntaModel
{

    private Database $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getPreguntaPorId(int $id_pregunta)
    {
        $sql = "
            SELECT p.id_pregunta, p.pregunta, p.estado, c.nombre, p.entregadas, p.correctas
            FROM preguntas p
            JOIN categoria c ON p.id_categoria = c.id_categoria
            WHERE p.id_pregunta = ?
        ";
        $result = $this->db->query($sql, [$id_pregunta], "i");
        return $result[0] ?? null;
    }

    public function agregarPregunta(string $pregunta, int $id_categoria)
    {
        $sql = "INSERT INTO preguntas (pregunta, id_categoria, entregadas, correctas, estado)
                VALUES (?, ?, 0, 0, 'sugerida')";
        $this->db->execute($sql, [$pregunta, $id_categoria], "si");
        return $this->db->getLastInsertId();
    }

    public function buscarPreguntaCreada(string $pregunta)
    {
        $sql = "SELECT id_pregunta FROM preguntas WHERE pregunta = ?";
        $result = $this->db->query($sql, [$pregunta], "s");
        return $result[0]['id_pregunta'] ?? null;
    }

    public function agregarRespuestas(int $id_pregunta, string $opcion1, string $opcion2, string $opcion3, string $opcion4, int $opcionCorrecta): void
    {
        $respuestas = [$opcion1, $opcion2, $opcion3, $opcion4];
        $sql = "INSERT INTO respuestas (respuesta, esCorrecta, id_pregunta, activa) VALUES (?, ?, ?, ?)";

        foreach ($respuestas as $index => $texto) {
            $esCorrecta = ($opcionCorrecta === $index + 1) ? 1 : 0;
            $this->db->execute($sql, [$texto, $esCorrecta, $id_pregunta, 0], "siii");
        }
    }

    public function getRespuestasPorPregunta(int $id_pregunta): array
    {
        $sql = "SELECT id_respuesta, respuesta, esCorrecta FROM respuestas WHERE id_pregunta = ?";
        return $this->db->query($sql, [$id_pregunta], "i");
    }

    public function incrementarEntregadasPregunta(int $id_pregunta): void
    {
        $sql = "UPDATE preguntas SET entregadas = entregadas + 1 WHERE id_pregunta = ?";
        $this->db->execute($sql, [$id_pregunta], "i");
    }

    public function incrementarCorrectasPregunta(int $id_pregunta): void
    {
        $sql = "UPDATE preguntas SET correctas = correctas + 1 WHERE id_pregunta = ?";
        $this->db->execute($sql, [$id_pregunta], "i");
    }

    public function activarPregunta(int $id_pregunta): void
    {
        $sql = "UPDATE preguntas SET estado = 'activa' WHERE id_pregunta = ?";
        $this->db->execute($sql, [$id_pregunta], "i");
    }

    public function desactivarPregunta(int $id_pregunta): void
    {
        $sql = "UPDATE preguntas SET estado = 'deshabilitada' WHERE id_pregunta = ?";
        $this->db->execute($sql, [$id_pregunta], "i");
    }

    public function actualizarPregunta(int $id_pregunta, string $textoPregunta): void
    {
        $sql = "UPDATE preguntas SET pregunta = ? WHERE id_pregunta = ?";
        $this->db->execute($sql, [$textoPregunta, $id_pregunta], "si");
    }

    public function actualizarRespuesta(int $id_respuesta, string $textoRespuesta): void
    {
        $sql = "UPDATE respuestas SET respuesta = ? WHERE id_respuesta = ?";
        $this->db->execute($sql, [$textoRespuesta, $id_respuesta], "si");
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

    public function insertarReportePregunta(int $id_pregunta, int $id_reportador, string $motivo): void
    {
        $sql = "INSERT INTO `preguntas_reportadas` (`id_pregunta`, `id_reportador`, `motivo`) VALUES (?, ?, ?)";
        $this->db->execute($sql, [$id_pregunta, $id_reportador, $motivo], "iis");
    }

    public function actualizarEstadoPregunta(int $id_pregunta, string $nuevo_estado): void
    {
        $sql = "UPDATE `preguntas` SET `estado` = ? WHERE `id_pregunta` = ?";
        $this->db->execute($sql, [$nuevo_estado, $id_pregunta], "si");
    }

}
