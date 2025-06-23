<?php

class MensajeCreadaCorrectamenteController
{

    private $view;

    public function __construct($view){
        $this->view = $view;
    }

    public function show()
    {
        $this->view->render("mensajeCreadaCorrectamente", [
            'title' => 'Creada Con Exito'
        ]);
    }

}