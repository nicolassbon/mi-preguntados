<?php

class EmailController
{
    private $model;
    private $view;

    public function __construct($model, $view){
        $this->model = $model;
        $this->view = $view;
    }

    public function show()
    {
        $id_usuario = $_SESSION['id_usuario'] ?? null;

        $this->view->render("validarCorreo", [
            'title' => 'Validar Correo',
            'id_usuario' => $id_usuario
        ]);
    }

    public function validar(){

        $id_usuario = $_SESSION['id_usuario'] ?? null;


        $this->model->validarCorreo($id_usuario);

        header('Location: ../login/show');
    }

}