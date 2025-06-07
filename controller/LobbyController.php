<?php

class LobbyController
{


    private $view;


    public function __construct($view)
    {

        $this->view = $view;

    }


    public function show()
    {

        $id_usuario = $_SESSION['usuario_id'] ?? null;

        if($id_usuario != null){
            $this->view->render("lobby", [
                'title' => 'Lobby Preguntopolis',
                'css' => '<link rel="stylesheet" href="/public/css/styles.css">',
                'usuario_id' => $id_usuario
            ]);

        }else{
            header("Location: /inicio/show");
        }


    }

}