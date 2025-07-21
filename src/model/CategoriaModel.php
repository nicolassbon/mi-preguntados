<?php

namespace App\model;

use App\core\Database;

class CategoriaModel
{
    private Database $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function getCategorias(): array
    {
        $sql = "SELECT id_categoria, nombre, foto_categoria FROM categoria ORDER BY id_categoria";
        return $this->db->query($sql);
    }

    public function getCategoriasUsadasEnPartida(int $id_partida): array
    {
        $sql = "
            SELECT DISTINCT p.id_categoria
            FROM partida_pregunta pp
            JOIN preguntas p ON pp.id_pregunta = p.id_pregunta
            WHERE pp.id_partida = ?
        ";
        return $this->db->query($sql,[$id_partida],"i");
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
