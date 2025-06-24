<?php

class CrearPreguntaModel
{

    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function agregarPregunta($pregunta, $id_categoria){

        $sql = "INSERT INTO preguntas (pregunta, id_categoria, entregadas, correctas, estado) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->database->prepare($sql);

        $entregadas = 0;
        $correctas = 0;
        $estado = 'sugerida';

        $stmt->bind_param("siiis", $pregunta, $id_categoria, $entregadas, $correctas, $estado);
        $stmt->execute();

    }

    public function buscarPreguntaCreada($pregunta){
        $sql = "SELECT id_pregunta FROM preguntas WHERE pregunta = '$pregunta' ";
        $resultado = $this->database->query($sql);
        return $resultado[0]['id_pregunta'];
    }

    public function agregarRespuestas($id_pregunta, $opcion, $opcion2, $opcion3, $opcion4, $opcionCorrecta){

        $esCorrecta = 0;
        $activo = 0;

       if($opcionCorrecta == 1){
           $esCorrecta = 1;
       }

        $sql = "INSERT INTO respuestas (respuesta, esCorrecta, id_pregunta, activa) VALUES (?, ?, ?, ?)";
        $stmt = $this->database->prepare($sql);

        $stmt->bind_param("siii", $opcion, $esCorrecta, $id_pregunta, $activo);
        $stmt->execute();

        $esCorrecta = 0;

        if($opcionCorrecta == 2){
            $esCorrecta = 1;
        }

        $sql = "INSERT INTO respuestas (respuesta, esCorrecta, id_pregunta, activa) VALUES (?, ?, ?, ?)";
        $stmt = $this->database->prepare($sql);
        $stmt->bind_param("siii", $opcion2, $esCorrecta, $id_pregunta, $activo);
        $stmt->execute();

        $esCorrecta = 0;

        if($opcionCorrecta == 3){
            $esCorrecta = 1;
        }

        $sql = "INSERT INTO respuestas (respuesta, esCorrecta, id_pregunta, activa) VALUES (?, ?, ?, ?)";
        $stmt = $this->database->prepare($sql);
        $stmt->bind_param("siii", $opcion3, $esCorrecta, $id_pregunta, $activo);
        $stmt->execute();

        $esCorrecta = 0;

        if($opcionCorrecta == 4){
            $esCorrecta = 1;
        }

        $sql = "INSERT INTO respuestas (respuesta, esCorrecta, id_pregunta, activa) VALUES (?, ?, ?, ?)";
        $stmt = $this->database->prepare($sql);
        $stmt->bind_param("siii", $opcion4, $esCorrecta, $id_pregunta, $activo);
        $stmt->execute();


    }


    public function agregarPreguntaASugeridas($id_usuario, $pregunta, $id_categoria){

        $sql = "INSERT INTO sugerencias_preguntas (id_usuario, pregunta_sugerida, id_categoria, fecha_envio, estado, fecha_resolucion) VALUES (?, ?, ?, NOW(), ?, ?)";
        $stmt = $this->database->prepare($sql);

        $estado = 'pendiente';
        $fecha_resolucion = null;
        $stmt->bind_param("isiss", $id_usuario, $pregunta, $id_categoria, $estado, $fecha_resolucion);
        $stmt->execute();

    }



}