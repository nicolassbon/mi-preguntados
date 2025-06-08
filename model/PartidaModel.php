<?php

class PartidaModel
{

    private $database;

    public function __construct($database){
        $this->database = $database;
    }




    public function getRespuestasPorPregunta($id_pregunta)
    {
        $id_preg = intval($id_pregunta);

        $sql = "SELECT id_respuesta, respuesta, esCorrecta FROM respuestas WHERE id_pregunta = $id_preg ";
        return $this->database->query($sql);

    }

    public function incrementoPuntaje(){
        $_SESSION['puntaje'] = $_SESSION['puntaje'] + 5;
    }

}