<?php

class RuletaModel
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function getUsuario($id_usuario)
    {
        $sql = "SELECT nombre_usuario FROM usuarios WHERE id_usuario = $id_usuario ";
        $resultado = $this->db->query($sql);
        return $resultado[0]['nombre_usuario'];
    }

}