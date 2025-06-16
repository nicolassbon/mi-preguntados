<?php

class LobbyModel
{


    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function getUsuario($id_usuario)
    {
        $sql = "SELECT nombre_usuario FROM usuarios WHERE id_usuario = $id_usuario ";
        $resultado = $this->database->query($sql);
        return $resultado[0]['nombre_usuario'];
    }




}