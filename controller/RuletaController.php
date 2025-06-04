<?php

class RuletaController
{

    private $view;
    private $model;

    public function __construct($model, $view){
        $this->view = $view;
        $this->model = $model;
    }

    public function show(){
        $this->view->render("ruleta", [
            'title' => 'Ruleta',
            'css' => '<link rel="stylesheet" href="/public/css/styles.css">'
        ]);
    }

    public function partida(){

        $categoria = rand(1, 7);

        $this->model->getCategoriaName($categoria);

        $pregunta = $this->model->getPreguntaAleatoriaPorCategoria($categoria);

        echo $pregunta;

    }


}