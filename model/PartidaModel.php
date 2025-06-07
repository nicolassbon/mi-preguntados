<?php

class PartidaModel
{

    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function getPreguntaAleatoriaConSusOpciones($id_categoria){
        $sql = "SELECT id_pregunta FROM preguntas WHERE id_categoria = $id_categoria ORDER BY RAND() LIMIT 1";
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
            SELECT p.id_pregunta AS id, p.pregunta AS textoPregunta, c.nombre AS categoria,
                   r.id_respuesta, r.respuesta AS texto, r.esCorrecta
            FROM preguntas p
            JOIN categoria c ON p.id_categoria = c.id_categoria
            JOIN respuestas r ON p.id_pregunta = r.id_pregunta
            WHERE p.id_pregunta = $idPregunta
        ";

        $result = $this->database->query($sql);
        if (count($result) === 0) return null;

        $pregunta = [
            "categoria" => $result[0]['categoria'],
            "textoPregunta" => $result[0]['textoPregunta'],
            "id_pregunta" => $result[0]['id'],
            "opciones" => []
        ];

        foreach ($result as $fila) {
            $pregunta['opciones'][] = [
                "id_respuesta" => $fila['id_respuesta'],
                "texto" => $fila['texto'],
                "es_correcta" => boolval($fila['esCorrecta'])
            ];
        }

        return $pregunta;
    }

    public function obtenerPuntajeUsuario($idUsuario)
    {
        $sql = "SELECT puntaje_acumulado FROM usuarios WHERE id_usuario = $idUsuario";
        $resultado = $this->database->query($sql);
        return $resultado[0]['puntaje_acumulado'];

    }

    public function incrementoPuntaje($idUsuario)
    {
        $sql = "UPDATE usuarios SET puntaje_acumulado = puntaje_acumulado + 5 WHERE id_usuario = $idUsuario";
        $this->database->execute($sql);

    }

    public function creoPartidaPregunta($idPartida, $idPregunta, $idRespuesta, $acerto)
    {



        $sql = "INSERT INTO partida_pregunta(id_partida, id_pregunta, id_respuesta_elegida, acerto)
VALUES (?, ?, ?, ?)";

        $consulta = $this->database->prepare($sql);

        $acertoInt = $acerto ? 1 : 0;

        // Asignar parámetros (todos enteros, por eso 'iiii')
        $consulta->bind_param("iiii", $idPartida, $idPregunta, intval($idRespuesta), $acertoInt);

        // Ejecutar
        $consulta->execute();


    }



}