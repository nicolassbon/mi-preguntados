<?php

class MensajeCreadaCorrectamenteController
{

    private $view;

    public function __construct($view){
        $this->view = $view;
    }

    public function show()
    {
        $this->view->render("crearPreguntaSuccess", [
            'title' => 'Creada Con Exito'
        ]);
    }

}