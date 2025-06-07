<?php

class RuletaController
{

   private $view;
    private $model;

   public function __construct($model, $view){
        $this->view = $view;$this->model = $model;
    }

    public function show(){

       $id_usuario = $_SESSION['usuario_id'] ?? null;

       if($id_usuario != null){
           $this->view->render("ruleta", [
                'title' => 'Ruleta',
                'css' => '<link rel="stylesheet" href="/public/css/styles.css">',
                'usuario_id' => $id_usuario
            ]);

        }else{
            header('Location: /inicio/show');
        }

    }

    public function partida(){

        $numCategoria = $this->model->getIdCategoria();

        $_SESSION['numCategoria'] = $numCategoria;

        header('Location: /partida/show');

    }


}