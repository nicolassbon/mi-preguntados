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

        $idUsuario = $_SESSION['usuario_id'] ?? null;

        if($idUsuario == null){
            header('Location: /inicio/show');
            exit;
        }

        $numero = $_SESSION['numCategoria'];

        $partida = $this->model->getPreguntaAleatoriaConSusOpciones($numero);

        $idPartida = $_SESSION['partida_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuesta'])) {
            $idRespuesta = intval($_POST['respuesta']);
            $pregunta = $this->model->getPreguntaPorRespuesta($idRespuesta);


            if ($pregunta) {
                $esCorrecta = false;


                foreach ($pregunta['opciones'] as &$opcion) {
                    $opcion['esSeleccionada'] = ($opcion['id_respuesta'] == $idRespuesta);

                    if ($opcion['esSeleccionada'] && $opcion['es_correcta']) {
                        $esCorrecta = true;
                    }
                }

               $user = $_SESSION['usuario_id'];


                if($esCorrecta){
                    $this->model->incrementoPuntaje($user);
                }else{
                    unset($_SESSION['partida_id']);
                }


                $this->model->creoPartidaPregunta($idPartida, intval($partida['id_pregunta']), $idRespuesta, $esCorrecta);

                $puntaje = $this->model->obtenerPuntajeUsuario($_SESSION['usuario_id']);

                $pregunta['esRespuestaProcesada'] = true;
                $pregunta['esCorrecta'] = $esCorrecta;
                $pregunta['puntaje'] = $puntaje;
                $pregunta['css'] = '<link rel="stylesheet" href="/public/css/styles.css">';

            }



            $this->view->render("partida", $pregunta);

        } else {
            $pregunta = $this->model->getPreguntaAleatoriaConSusOpciones($numero);
            $pregunta['esRespuestaProcesada'] = false;
            $pregunta['esCorrecta'] = null;
            $pregunta['css'] = '<link rel="stylesheet" href="/public/css/styles.css">';

            $this->view->render("partida", $pregunta);
        }
    }


}