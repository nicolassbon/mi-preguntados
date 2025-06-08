<?php

class PartidaController
{

    private $model;
    private $view;

    public function __construct($model, $view){
        $this->model = $model;
        $this->view = $view;
    }



    public function show()
    {

        $id_usuario = $_SESSION['usuario_id'] ?? null;

        if($id_usuario == null){
            header('Location: /inicio/show');
            exit;
        }

        $this->view->render("partida", [
            'title' => 'Ruleta',
            'css' => '<link rel="stylesheet" href="/public/css/styles.css">',
            'usuario_id' => $id_usuario,
            'pregunta' => $_SESSION['pregunta'],
            'categoria' => $_SESSION['nombre_categoria'],
            'respuestas' => $_SESSION['opciones'],
            'id_partida' => $_SESSION['id_partida']
        ]);

    }

    public function responder(){

        $id_usuario = $_SESSION['usuario_id'] ?? null;

        if($id_usuario == null){
            header('Location: /inicio/show');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_respuesta'])) {

            $id_pregunta = $_SESSION['id_pregunta'];
            $respuestas = $this->model->getRespuestasPorPregunta($id_pregunta);

            $respuestaCorrecta = false;
            $texto = '';
            $color = '';


            foreach ($respuestas as &$respuesta) {
                $respuesta['id'] = $respuesta['id_respuesta'];
                $respuesta['texto_respuesta'] = $respuesta['respuesta'];


                if ($respuesta['esCorrecta']) {
                    if ($respuesta['id_respuesta'] == $_POST['id_respuesta']) {
                        $respuestaCorrecta = true;
                        $texto = "¡CORRECTA!";
                        $color = 'text-success';

                        $this->model->incrementoPuntaje($_SESSION['id_partida']);
                        $this->model->incremetoPreguntaRespondidaCorrectamente($_SESSION['id_partida']);


                    }
                    $respuesta['clase'] = 'bg-success';
                } elseif ($respuesta['id_respuesta'] == $_POST['id_respuesta']) {
                    $respuesta['clase'] = 'bg-danger';
                        $texto = "¡INCORRECTA!";
                        $color = 'text-danger';

                    $this->model->actualizarFechaPartidaFinalizada($_SESSION['id_partida']);
                } else {

                    $respuesta['clase'] = 'bg-light';
                }
                $respuesta['disabled'] = true;

            }

            $this->model->incrementoPreguntaContestada($_SESSION['id_partida']);
            $_SESSION['cantidad'] = intval($this->model->getCantidadDePreguntas($_SESSION['id_partida']));


            $this->view->render("partida", [
                'title' => 'Ruleta',
                'css' => '<link rel="stylesheet" href="/public/css/styles.css">',
                'usuario_id' => $id_usuario,
                'pregunta' => $_SESSION['pregunta'],
                'respuestas' => $respuestas,
                'categoria' => $_SESSION['nombre_categoria'],
                'correcto' => $respuestaCorrecta,
                'respondido' => true,
                'texto' => $texto,
                'color' => $color,
                'puntaje' => $_SESSION['puntaje']
            ]);

        } else {
            echo 'error';
        }




    }





}