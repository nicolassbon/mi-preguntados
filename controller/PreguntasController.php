<?php

class PreguntasController
{
    private $model;
    private $view;

    public function __construct($model, $view){
        $this->model = $model;
        $this->view = $view;
    }

    public function show(){
        $pregunta = $this->model->getPreguntaAleatoriaConSusOpciones();

        if (!$pregunta) {
            $pregunta = [
                "categoria" => "Sin preguntas",
                "dificultad" => "",
                "textoPregunta" => "No hay preguntas disponibles",
                "opciones" => []
            ];
        }

        $this->view->render("preguntas", $pregunta);
    }
}