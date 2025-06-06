<?php

class PreguntasModel
{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function getPreguntaAleatoriaConSusOpciones(){
        $sql = "SELECT id_pregunta FROM preguntas ORDER BY RAND() LIMIT 1";
        $result = $this->database->query($sql);
        if (count($result) === 0) return null;
        return $this->getPreguntaPorId($result[0]['id_pregunta']);
    }

    public function getPreguntaPorRespuesta($idRespuesta) {
        $sql = "SELECT id_pregunta FROM respuestas WHERE id_respuesta = $idRespuesta";
        $result = $this->database->query($sql);
        if (count($result) === 0) return null;
        return $this->getPreguntaPorId($result[0]['id_pregunta']);
    }

    private function getPreguntaPorId($idPregunta) {
        $sql = "
            SELECT p.pregunta AS textoPregunta, c.nombre AS categoria, d.dificultad AS dificultad,
                   r.id_respuesta, r.respuesta AS texto, r.esCorrecta
            FROM preguntas p
            JOIN categoria c ON p.id_categoria = c.id_categoria
            JOIN dificultad d ON p.id_dificultad = d.id_dificultad
            JOIN respuestas r ON p.id_pregunta = r.id_pregunta
            WHERE p.id_pregunta = $idPregunta
        ";

        $result = $this->database->query($sql);

        if (count($result) === 0) return null;

        $pregunta = [
            "categoria" => $result[0]['categoria'],
            "dificultad" => $result[0]['dificultad'],
            "textoPregunta" => $result[0]['textoPregunta'],
            "opciones" => []
        ];

        foreach ($result as $fila) {
            $pregunta['opciones'][] = [
                "id_respuesta" => $fila['id_respuesta'],
                "texto" => $fila['texto'],
                "esCorrecta" => $fila['esCorrecta']
            ];
        }

        return $pregunta;
    }

    /*public function getPreguntaIdPorRespuesta($idRespuesta) {
        $sql = "SELECT id_pregunta FROM respuestas WHERE id_respuesta = $idRespuesta";
        $result = $this->database->query($sql);
        return count($result) > 0 ? $result[0]['id_pregunta'] : null;
    }
    */
}