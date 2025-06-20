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

    public function getPreguntasPorCategoria(int $id_categoria): array
    {
        $sql = "
              SELECT p.id_pregunta ,p.pregunta, c.nombre, p.activa
              FROM preguntas p join categoria c on p.id_categoria = c.id_categoria
              WHERE p.id_categoria = $id_categoria
              ";
        return $this->db->query($sql);
    }

    public function getPreguntasPorCategoriaIncluirInactivas(int $id_categoria): array
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


}