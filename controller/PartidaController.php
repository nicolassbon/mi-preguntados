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

        $numero = $_SESSION['numCategoria'];
        var_dump($numero);
        $this->model->getPreguntaAleatoriaConSusOpciones($numero);


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

                $this->model->incrementoPuntaje($_SESSION['usuario_id']);
                $puntaje = $this->model->obtenerPuntajeUsuario($_SESSION['usuario_id']);

                $pregunta['esRespuestaProcesada'] = true;
                $pregunta['esCorrecta'] = $esCorrecta;
                $pregunta['puntaje'] = $puntaje;
            }

            $this->view->render("partida", $pregunta);

        } else {
            $pregunta = $this->model->getPreguntaAleatoriaConSusOpciones($numero);
            $pregunta['esRespuestaProcesada'] = false;
            $pregunta['esCorrecta'] = null;

            $this->view->render("partida", $pregunta);
        }
    }


}