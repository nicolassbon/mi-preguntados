<?php

class PreguntasController
{
    private $model;
    private $view;

    public function __construct($model, $view){
        $this->model = $model;
        $this->view = $view;
    }

    public function show()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuesta'])) {
            $idRespuesta = intval($_POST['respuesta']);
            $pregunta = $this->model->getPreguntaPorRespuesta($idRespuesta);

            if ($pregunta) {
                foreach ($pregunta['opciones'] as &$opcion) {
                    $opcion['esSeleccionada'] = ($opcion['id_respuesta'] == $idRespuesta);
                }
            }

            $pregunta['esRespuestaProcesada'] = true;

            $this->view->render("preguntas", $pregunta);

        } else {
            $pregunta = $this->model->getPreguntaAleatoriaConSusOpciones();

            $pregunta['esRespuestaProcesada'] = false;

            $this->view->render("preguntas", $pregunta);
        }
    }
}