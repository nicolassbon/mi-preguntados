<?php

class SugerenciaPreguntaModel
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function agregarSugerencia($id_usuario, $id_pregunta, $id_categoria): void
    {
        $sql = "INSERT INTO sugerencias_preguntas(id_usuario, id_pregunta, id_categoria, fecha_envio, estado, fecha_resolucion)
            VALUES (?, ?, ?, NOW(), ?, ?)";
        $stmt = $this->db->prepare($sql);

        $estado = 'pendiente';
        $fecha_resolucion = null;
        $stmt->bind_param("iiiss", $id_usuario, $id_pregunta, $id_categoria, $estado, $fecha_resolucion);
        $stmt->execute();
    }

    public function actualizarEstadoSugerencia($id_pregunta, $estado): void
    {
        $sql = "UPDATE sugerencias_preguntas SET estado = ?, fecha_resolucion = NOW() WHERE id_pregunta = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $estado, $id_pregunta);
        $stmt->execute();
    }

    public function obtenerSugerenciasPendientes(): array
    {
        $sql = "SELECT * FROM sugerencias_preguntas WHERE estado = 'pendiente'";
        return $this->db->query($sql);
    }

    public function obtenerSugerenciasPorUsuario($id_usuario): array
    {
        $sql = "SELECT * FROM sugerencias_preguntas WHERE id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPreguntasSugeridas($terminoBusqueda = '', $id_categoria = 'todasLasCategorias'): array
    {
        $where = "p.estado = 'sugerida'";

        if ($terminoBusqueda !== '') {
            $term = $this->db->escapeLike($terminoBusqueda);
            $where .= " AND p.pregunta LIKE '%$term%'";
        }

        if ($id_categoria !== 'todasLasCategorias') {
            $where .= " AND p.id_categoria = " . (int)$id_categoria;
        }

        $sql = "
            SELECT DISTINCT p.id_pregunta, p.pregunta, c.nombre, u.nombre_usuario, u.email, p.estado
            FROM preguntas p
            JOIN categoria c ON p.id_categoria = c.id_categoria
            JOIN sugerencias_preguntas s ON s.id_pregunta = p.id_pregunta
            JOIN usuarios u ON s.id_usuario = u.id_usuario
            WHERE $where
        ";
        return $this->db->query($sql);
    }

    public function getAutorDePreguntaSugerida($id_pregunta)
    {
        $sql = "
            SELECT u.nombre_usuario, u.email
            FROM sugerencias_preguntas sp
            JOIN usuarios u ON sp.id_usuario = u.id_usuario
            WHERE sp.id_pregunta = $id_pregunta
            LIMIT 1
        ";
        $resultado = $this->db->query($sql);
        return $resultado[0] ?? null;
    }

    public function activarPreguntaSugerida($id): void
    {

        $estado = 'activa';

        $sql = "UPDATE preguntas SET estado = '$estado' WHERE id_pregunta = $id ";
        $this->db->execute($sql);

        $sql2 = "UPDATE respuestas SET activa = '1' WHERE id_pregunta = $id ";
        $this->db->execute($sql2);

    }

    public function desactivarPreguntaSugerida($id): void
    {
        $estado = 'deshabilitada';

        $sql = "UPDATE preguntas SET estado = '$estado' WHERE id_pregunta = $id ";
        $this->db->execute($sql);

        $sql2 = "UPDATE respuestas SET activa = '0' WHERE id_pregunta = $id ";
        $this->db->execute($sql2);

    }

    public function fechaResolucionSugerencia($id): void
    {
        $sql = "UPDATE sugerencias_preguntas SET fecha_resolucion = NOW() WHERE id_pregunta = $id";
        $this->db->execute($sql);
    }


    public function actualizarEstadoPregunta($id, $estado): void
    {
        $sql = "UPDATE sugerencias_preguntas SET estado = '$estado' WHERE id_pregunta = $id";
        $this->db->execute($sql);
    }

}
