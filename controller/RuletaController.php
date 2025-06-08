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

    public function proceso(){

        $id_usuario = $_SESSION['usuario_id'] ?? null;
        $_SESSION['puntaje'] = 0;

        if($id_usuario == null){
            header('Location: /inicio/show');
            exit;
        }

        //crear partida
        $this->model->crearPartida($id_usuario);

        header('Location: /ruleta/show');

    }

    public function partida(){

       $id_usuario = $_SESSION['usuario_id'] ?? null;

       if($id_usuario == null){
           header('Location: /inicio/show');
           exit;
       }

       $numero_random = $this->model->getGenerarRandom();

       $nombre_categoria = $this->model->getNombreCategoria($numero_random);

       $id_pregunta = $this->model->getIdPreguntaAleatoria($numero_random);

       $pregunta_texto = $this->model->getPreguntaAleatoriaPorId($id_pregunta);

       $respuestas = $this->model->getRespuestasPorIdPreguntaAleatoria($id_pregunta);


       $_SESSION['id_pregunta'] = $id_pregunta;
       $_SESSION['pregunta'] = $pregunta_texto;

       $_SESSION['nombre_categoria'] = $nombre_categoria;

       $_SESSION['opciones'] = $respuestas;


        header('Location: /partida/show');

    }


}