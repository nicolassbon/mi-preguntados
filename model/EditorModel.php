<?php

class EditorModel
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getCategorias(): array
    {
        $sql = "
              SELECT id_categoria, nombre
              FROM categoria";
        return $this->db->query($sql);
    }

    public function getPreguntasPorCategoria($id_categoria): array
    {
        $sql = "
              SELECT p.id_pregunta ,p.pregunta, c.nombre, p.activa
              FROM preguntas p join categoria c on p.id_categoria = c.id_categoria
              WHERE p.id_categoria = $id_categoria
              ";
        return $this->db->query($sql);
    }

    public function getPreguntasPorCategoriaIncluirInactivas($id_categoria): array
    {
        $sql = "
              SELECT p.id_pregunta ,p.pregunta, c.nombre, p.activa
              FROM preguntas p join categoria c on p.id_categoria = c.id_categoria
              WHERE p.id_categoria = $id_categoria
              ";
        return $this->db->query($sql);
    }
    public function getPreguntas(): array
    {
        $sql = "
              SELECT p.id_pregunta ,p.pregunta,c.nombre, p.activa
              FROM preguntas p join categoria c on p.id_categoria = c.id_categoria
              order by id_pregunta";
        return $this->db->query($sql);
    }

    public function desactivarPregunta($id_pregunta)
    {
        $sql = "
                UPDATE preguntas
                SET activa = 0
                WHERE id_pregunta = $id_pregunta";
        $this->db->execute($sql);
    }

    public function activarPregunta($id_pregunta)
    {
        $sql = "
                UPDATE preguntas
                SET activa = 1
                WHERE id_pregunta = $id_pregunta";
        $this->db->execute($sql);
    }

  /*  public function getPreguntaConRespuestas(int $id_pregunta)
    {
        $sql = "
                SELECT *
                FROM preguntas p join respuestas r on r.id_pregunta = p.id_pregunta
                WHERE p.id_pregunta = $id_pregunta";
        return $this->db->query($sql);
    }*/

    public function getPreguntaPorId($id_pregunta)
    {
        $sql = "SELECT p.id_pregunta, p.pregunta, p.activa 
                FROM preguntas p
                WHERE id_pregunta = $id_pregunta";
        return $this->db->query($sql);
    }

    public function getRespuestasPorPregunta($id_pregunta): array
    {
        $sql = "SELECT * 
                FROM respuestas 
                WHERE id_pregunta = $id_pregunta";
        return $this->db->query($sql);
    }

    public function actualizarPregunta($id_pregunta, $textoPregunta)
    {
        $sql = "UPDATE preguntas
                SET pregunta = '$textoPregunta'
                WHERE id_pregunta = $id_pregunta";
        $this->db->execute($sql);
    }
    public function actualizarRespuesta($id_respuesta, $textoRespuesta)
    {
        $sql = "UPDATE respuestas
                SET respuesta = '$textoRespuesta'
                WHERE id_respuesta = $id_respuesta";
        $this->db->execute($sql);
    }

}