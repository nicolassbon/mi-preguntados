<?php

class InicioController
{

    private $view;

    public function __construct($view){
        $this->view = $view;
    }

    public function show(){
        $this->view->render("inicio", [
            'title' => 'Inicio Preguntopolis',
            'css' => '<link rel="stylesheet" href="/public/css/styles.css">'
        ]);
    }
}