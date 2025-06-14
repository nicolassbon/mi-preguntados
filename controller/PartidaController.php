<?php

class PartidaController
{

    private $model;
    private $view;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function crearPartida()
    {
        $id_usuario = $_SESSION['usuario_id'] ?? null;
        $_SESSION['puntaje'] = 0;

        if ($id_usuario == null) {
            header('Location: /inicio/show');
            exit;
        }

        //crear partida
        $id_partida = $this->model->crearPartida($id_usuario);

        $_SESSION['id_partida'] = $id_partida;

        header('Location: /ruleta/show');
        exit();
    }

    public function jugar()
    {

        $id_usuario = $_SESSION['usuario_id'] ?? null;

        if ($id_usuario == null) {
            header('Location: /inicio/show');
            exit;
        }

        $categoria = $this->model->getCategoriaAleatoria();

        $nombre_categoria = $categoria["nombre"];

        $pregunta = $this->model->obtenerPregunta($id_usuario, $categoria["id_categoria"]);

        $id_pregunta = $pregunta["id_pregunta"];

        $pregunta_texto = $pregunta["pregunta"];

        $respuestas = $this->model->getRespuestasPorIdPreguntaAleatoria($id_pregunta);

        $_SESSION['id_pregunta'] = $id_pregunta;
        $_SESSION['pregunta'] = $pregunta_texto;
        $_SESSION['nombre_categoria'] = $nombre_categoria;
        $_SESSION['opciones'] = $respuestas;
        $_SESSION['inicio_pregunta'] = time();

        $fondo = $this->model->getColorCategoria($nombre_categoria);
        $foto = $this->model->getFotoCategoria($nombre_categoria);
        $user = $this->model->getUsuario($id_usuario);

        $tiempo_restante = $this->model->getTiempo();

        // Se le entrego la pregunta, actualizar datos bdd
        $this->model->incrementoDeEntregadas($id_usuario);
        $this->model->marcarPreguntaComoVista($id_usuario, $id_pregunta);

        $this->view->render("partida", [
            'title' => 'Ruleta',
            'css' => '<link rel="stylesheet" href="/public/css/styles.css">',
            'usuario_id' => $id_usuario,
            'pregunta' => $pregunta_texto,
            'categoria' => $nombre_categoria,
            'respuestas' => $respuestas,
            'id_partida' => $_SESSION['id_partida'],
            'tiempo_restante' => $tiempo_restante,
            'fondo' => $fondo,
            'foto' => $foto,
            'user' => $user
        ]);

    }

    public function responder()
    {

        $id_usuario = $_SESSION['usuario_id'] ?? null;

        if ($id_usuario == null) {
            header('Location: /inicio/show');
            exit;
        }

        $texto = '';
        $color = '';
        $ocultar = 'display:none';

        $inicio = $_SESSION['inicio_pregunta'] ?? null;


        if ($inicio === null || (time() - $inicio) > 10) {
            $this->model->actualizarFechaPartidaFinalizada($_SESSION['id_partida']);
            unset($_SESSION['inicio_pregunta']);
            header("Location: /perdio/show");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_respuesta'])) {

            $id_pregunta = $_SESSION['id_pregunta'];
            $id_partida = $_SESSION['id_partida'];
            $respuestas = $this->model->getRespuestasPorPregunta($id_pregunta);

            $respuestaCorrecta = false;

            foreach ($respuestas as &$respuesta) {
                $respuesta['id'] = $respuesta['id_respuesta'];
                $respuesta['texto_respuesta'] = $respuesta['respuesta'];


                if ($respuesta['esCorrecta']) {
                    if ($respuesta['id_respuesta'] == $_POST['id_respuesta']) {
                        $respuestaCorrecta = true;
                        $texto = "¡CORRECTA!";
                        $color = 'text-success';

                        $this->model->incrementoPuntaje($id_partida);
                        $this->model->incremetoPreguntaRespondidaCorrectamente($id_partida);
                        $this->model->crearRegistroPreguntaRespondida($id_partida, $id_pregunta, $respuesta['id_respuesta'], 1);
                        $this->model->acumularPuntajeUsuario($id_usuario);
                        $this->model->sumarCorrectaAUsuario($id_usuario);
                    }
                    $respuesta['clase'] = 'bg-success';
                } elseif ($respuesta['id_respuesta'] == $_POST['id_respuesta']) {
                    $respuesta['clase'] = 'bg-danger';
                    $texto = "¡INCORRECTA!";
                    $color = 'text-danger';

                    $this->model->actualizarFechaPartidaFinalizada($_SESSION['id_partida']);
                    $this->model->crearRegistroPreguntaRespondida($id_partida, $id_pregunta, $respuesta['id_respuesta'], 0);
                } else {

                    $respuesta['clase'] = 'bg-light';
                }
                $respuesta['disabled'] = true;

            }

            $_SESSION['cantidad'] = intval($this->model->getCantidadDePreguntas($_SESSION['id_partida']));

            $fondo = $this->model->getColorCategoria($_SESSION['nombre_categoria']);
            $foto = $this->model->getFotoCategoria($_SESSION['nombre_categoria']);
            $user = $this->model->getUsuario($id_usuario);


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
                'puntaje' => $_SESSION['puntaje'],
                'ocultar' => $ocultar,
                'foto' => $foto,
                'fondo' => $fondo,
                'user' => $user
            ]);
        } else {
            echo 'error';
        }
    }
}