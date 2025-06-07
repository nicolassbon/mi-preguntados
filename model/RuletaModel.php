<?php

class RuletaModel
{
   private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function getIdCategoria(){

        $numCategoria = rand(1, 8);

       $sql = "SELECT id_categoria FROM categoria WHERE id_categoria = $numCategoria ";
        $resultado = $this->database->query($sql);

        return $resultado[0]['id_categoria'];

    }

}