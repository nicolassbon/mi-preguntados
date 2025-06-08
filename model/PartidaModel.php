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

    public function incrementoPuntaje($id_partida){
        $_SESSION['puntaje'] = $_SESSION['puntaje'] + 5;
       $puntaje = $_SESSION['puntaje'];
        $sql = "UPDATE partidas SET puntaje_final = $puntaje  WHERE id_partida = $id_partida";
        $this->database->execute($sql);
    }

    public function actualizarFechaPartidaFinalizada($id_partida){

        $sql = "UPDATE partidas SET fecha_fin = NOW() WHERE id_partida = $id_partida";
        $this->database->execute($sql);

    }

    public function incrementoPreguntaContestada($id_partida){

        $sql = "UPDATE partidas SET entregadas = entregadas + 1 WHERE id_partida = $id_partida";
        $this->database->execute($sql);

    }

    public function incremetoPreguntaRespondidaCorrectamente($id_partida){

    $sql = "UPDATE partidas SET correctas = correctas + 1 WHERE id_partida = $id_partida";
    $this->database->execute($sql);
    }

    public function getCantidadDePreguntas($id_partida){
        $sql = "SELECT correctas FROM partidas WHERE id_partida = $id_partida";
       $resultado = $this->database->query($sql);
       return $resultado[0]['correctas'];
    }


    public function getTiempo(){
        $inicio = $_SESSION['inicio_pregunta'] ?? null;
        if (!$inicio) return 0;

        $ahora = time();
        $tiempo_total = 10;
        $tiempo_pasado = $ahora - $inicio;
        $tiempo_restante = max(0, $tiempo_total - $tiempo_pasado);
        return $tiempo_restante;
    }


}