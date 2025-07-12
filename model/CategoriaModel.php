<?php

class CategoriaModel
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function getCategorias() {
        $sql = "SELECT id_categoria, nombre, foto_categoria FROM categoria ORDER BY id_categoria";
        return $this->db->query($sql);
    }

    public function getCategoriaAleatoria()
    {
        $sql = "SELECT * FROM categoria ORDER BY RAND() LIMIT 1";
        $resultado = $this->db->query($sql);
        return $resultado[0] ?? null;
    }

    public function getCategoriasUsadasEnPartida($id_partida): array {
        $sql = "
            SELECT DISTINCT p.id_categoria
            FROM partida_pregunta pp
            JOIN preguntas p ON pp.id_pregunta = p.id_pregunta
            WHERE pp.id_partida = $id_partida
        ";
        $result = $this->db->query($sql);

        return $result ?? [];
    }

    public function elegirCategoriaParaPartida(int $id_partida): array
    {
        $todasLasCategorias = $this->db->query("SELECT * FROM categoria");

        $categoriasUsadas = $this->getCategoriasUsadasEnPartida($id_partida);
        $idsUsadas = array_column($categoriasUsadas, 'id_categoria');

        $categoriasNoUsadas = array_filter($todasLasCategorias, static function ($categoria) use ($idsUsadas) {
            return !in_array($categoria['id_categoria'], $idsUsadas, true);
        });

        $candidatas = !empty($categoriasNoUsadas) ? $categoriasNoUsadas : $todasLasCategorias;

        return $candidatas[array_rand($candidatas)];
    }

}
