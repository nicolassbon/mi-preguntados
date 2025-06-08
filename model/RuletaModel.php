<?php

class RuletaModel
{
   private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function getGenerarRandom(): int
    {
        return rand(1,8);
    }

    public function getNombreCategoria($id_categoria){

       $sql = "SELECT nombre FROM categoria WHERE id_categoria = $id_categoria ";
       $resultado = $this->database->query($sql);

       return $resultado[0]['nombre'];
    }

    public function getIdPreguntaAleatoria($id_categoria){

        $sql = "SELECT id_pregunta FROM preguntas WHERE id_categoria = $id_categoria ORDER BY RAND() LIMIT 1";
        $resultado = $this->database->query($sql);
        return $resultado[0]['id_pregunta'];

    }

    public function getPreguntaAleatoriaPorId($id_pregunta){

        $sql = "SELECT pregunta FROM preguntas WHERE id_pregunta = $id_pregunta ";
        $resultado = $this->database->query($sql);
        return $resultado[0]['pregunta'];

    }

    public function getRespuestasPorIdPreguntaAleatoria($id_pregunta){

        $sql = "SELECT id_respuesta, respuesta FROM respuestas WHERE id_pregunta = $id_pregunta ";
        $respuestas_obtenidas = $this->database->query($sql);

        $respuestas = [];

        foreach ($respuestas_obtenidas as $respuesta) {
            $respuestas[] = [
                'id' => $respuesta['id_respuesta'],
                'texto_respuesta' => $respuesta['respuesta']
            ];
        }

        return $respuestas;

    }

    public function crearPartida($id_usuario)
    {

        $id_usuario = intval($id_usuario);
        $sql = "INSERT INTO partidas (id_usuario) VALUES ($id_usuario)";
        return $this->database->execute($sql);

    }


}