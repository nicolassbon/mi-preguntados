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

        $user = $this->model->getUsuario($id_usuario);

        $_SESSION['nombre_usuario'] = $user;

        $this->view->render("lobby", [
            'title' => 'Lobby Preguntopolis',
            'usuario_id' => $id_usuario,
            'user' => $user
        ]);
    }

}