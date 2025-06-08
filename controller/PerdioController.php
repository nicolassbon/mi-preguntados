<?php

class PerdioController
{


    private $view;

    public function __construct($view){
        $this->view = $view;
    }

    public function show(){

        $this->view->render("perdio", [
            'title' => 'Partida Perdida',
            'css' => '<link rel="stylesheet" href="/public/css/styles.css">',
            'puntaje' => $_SESSION['puntaje'],
            'cantidad' => $_SESSION['cantidad'] ?? 0
        ]);

    }


}