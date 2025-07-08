<?php

class RuletaController
{

    private $view;
    private $model;

    public function __construct($model, $view)
    {
        $this->view = $view;
        $this->model = $model;
    }

    public function show()
    {
        $this->view->render("ruleta", [
            'title' => 'Ruleta'
        ]);
    }
}