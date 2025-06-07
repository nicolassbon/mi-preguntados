<?php

class EmailController
{
  private $model;
  private $view;

  public function __construct($model, $view)
  {
    $this->model = $model;
    $this->view = $view;
  }

  public function show()
  {

    $id_usuario = $_SESSION['usuario_id'] ?? null;


    if($id_usuario != null){
        $this->view->render("validarCorreo", [
            'title' => 'Validar Correo',
            'usuario_id' => $id_usuario,
            'css' => '<link rel="stylesheet" href="/public/css/styles.css">'
        ]);
    }else{
        header("Location: /inicio/show");
    }


  }

  public function validar()
  {

    $id_usuario = $_SESSION['usuario_id'] ?? null;

    $this->model->validarCorreo($id_usuario);

    $this->redirectTo("/login/show");
  }

  private function redirectTo($str)
  {
    header('Location: ' . $str);
    exit();
  }

}