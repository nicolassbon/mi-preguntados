<?php

class PreguntasModel
{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function getPreguntaAleatoriaConSusOpciones(){
        // Paso 1: Obtenemos una pregunta aleatoria
        $sqlId = "SELECT id_pregunta FROM preguntas ORDER BY RAND() LIMIT 1";
        $resultId = $this->database->query($sqlId);

        if (count($resultId) === 0) {
            return null;
        }

        $idPregunta = $resultId[0]['id_pregunta'];

        // Paso 2: Consultamos la pregunta y sus opciones
        $sql = "
     SELECT p.pregunta AS textoPregunta, c.nombre AS categoria, d.dificultad AS dificultad, r.respuesta AS texto, r.esCorrecta AS esCorrecta
     FROM preguntas p
     JOIN categoria c ON p.id_categoria = c.id_categoria
     JOIN dificultad d ON p.id_dificultad = d.id_dificultad
     JOIN respuestas r ON p.id_pregunta = r.id_pregunta
     WHERE p.id_pregunta = $idPregunta
    ";

        $result = $this->database->query($sql);

        if (count($result) === 0) {
            return null;
        }

        // Armamos la estructura para la vista
        $pregunta = [
            "categoria" => $result[0]['categoria'],
            "dificultad" => $result[0]['dificultad'],
            "textoPregunta" => $result[0]['textoPregunta'],
            "opciones" => []
        ];

        foreach ($result as $fila) {
            $pregunta['opciones'][] = ["texto" => $fila['texto']];
        }

        return $pregunta;
    }
}