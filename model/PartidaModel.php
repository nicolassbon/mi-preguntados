<?php

class PartidaModel
{

    private $database;

    public function __construct($database){
        $this->database = $database;
    }


    public function getColorCategoria($nombre_categoria){

        $sql = "SELECT color FROM categoria WHERE nombre = '$nombre_categoria' ";
        $resultado = $this->database->query($sql);
        return $resultado[0]['color'];

    }

    public function getFotoCategoria($nombre_categoria){
        $sql = "SELECT foto_categoria FROM categoria WHERE nombre = '$nombre_categoria' ";
        $resultado = $this->database->query($sql);
        return $resultado[0]['foto_categoria'];
    }



    public function getRespuestasPorPregunta($id_pregunta)
    {
        $id_preg = intval($id_pregunta);

        $sql = "SELECT id_respuesta, respuesta, esCorrecta FROM respuestas WHERE id_pregunta = $id_preg ";
        return $this->database->query($sql);

    }

    public function getUsuario($id_usuario)
    {
        $sql = "SELECT nombre_usuario FROM usuarios WHERE id_usuario = $id_usuario ";
        $resultado = $this->database->query($sql);
        return $resultado[0]['nombre_usuario'];
    }

    public function incrementoPuntaje($id_partida){

        $_SESSION['puntaje'] = $_SESSION['puntaje'] + 5;
       $puntaje = $_SESSION['puntaje'];
        $sql = "UPDATE partidas SET puntaje_final = $puntaje  WHERE id_partida = $id_partida ";
        $this->database->execute($sql);
    }

    public function acumularPuntajeUsuario($id_usuario){

        $sql = "UPDATE usuarios SET puntaje_acumulado = puntaje_acumulado + 5 WHERE id_usuario = $id_usuario ";
        $this->database->execute($sql);
    }


    public function actualizarFechaPartidaFinalizada($id_partida){

        $sql = "UPDATE partidas SET fecha_fin = NOW() WHERE id_partida = $id_partida ";
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


    public function crearRegistroPreguntaRespondida($id_partida, $id_pregunta, $id_respuesta, $acerto){

        $id_preg = intval($id_pregunta);
        $id_par = intval($id_partida);
        $id_resp = intval($id_respuesta);
        $acerto = intval($acerto);


            $sql = "INSERT INTO partida_pregunta (id_partida, id_pregunta, id_respuesta_elegida, acerto) 
    VALUES ($id_par, $id_preg, $id_resp, $acerto)";

        $this->database->execute($sql);
    }


}