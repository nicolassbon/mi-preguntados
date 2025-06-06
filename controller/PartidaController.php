<?php

class PartidaController
{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function registrarRespuesta($idPartida, $idPregunta, $idRespuesta, $acerto){
         $acertoValor = $acerto ? 1 : 0;

         $sql = "
         INSERT INTO partida_pregunta (id_partida , id_pregunta , id_respuesta_elegida , `acerto?`)
         VALUES ($idPartida, $idPregunta, $idRespuesta, $acertoValor);";

         $this->database->execute($sql);

    }

    public function actualizarPuntajeUsuario($idUsuario, $acerto){
        $puntos = $acerto ? 1 : 0;

        $sql = "UPDATE usuarios
        SET puntaje_acumulado =  IFNULL(puntaje_acumulado, 0) + $puntos,
        preguntas_entregadas = preguntas_entregadas + 1,
        preguntas_acertadas = preguntas_acertadas + 1
        WHERE id_usuario = $idUsuario;";

        $this->database->execute($sql);

    }

    public function obtenerPuntajeUsuario($idUsuario) {
        $sql = "SELECT puntaje_acumulado FROM usuarios WHERE id_usuario = $idUsuario";
        $result = $this->database->query($sql);
        return count($result) > 0 ? $result[0]['puntaje_acumulado'] : 0;
    }
}