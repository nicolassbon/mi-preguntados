<?php

class RuletaModel
{


    private $database;

    public function __construct($database){
        $this->database = $database;
    }


    public function getCategoriaName($categoria){

        $sql = "SELECT nombre FROM categoria WHERE id_categoria = $categoria ";

       $this->database->query($sql);



    }


    public function getPreguntaAleatoriaPorCategoria($categoria){


        $sql = "SELECT pregunta FROM preguntas WHERE id_categoria = $categoria ORDER BY RAND() LIMIT 1";
        $resultado = $this->database->query($sql);
        $pregunta = $resultado->fetch_assoc();
        return $pregunta['pregunta'];

    }


}