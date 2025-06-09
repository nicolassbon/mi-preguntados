<?php

class LobbyController
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

        $id_usuario = $_SESSION['usuario_id'] ?? null;


        if($id_usuario == null){
            header("Location: /inicio/show");
            exit;
        }

        $user = $this->model->getUsuario($id_usuario);

        $this->view->render("lobby", [
            'title' => 'Lobby Preguntopolis',
            'css' => '<link rel="stylesheet" href="/public/css/styles.css">',
            'usuario_id' => $id_usuario,
            'user' => $user
        ]);


    }

}