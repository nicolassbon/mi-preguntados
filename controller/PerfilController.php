<?php

class PerfilController
{
  private $model;
  private $view;

  public function __construct($model, $view){
    $this->model = $model;
    $this->view = $view;
  }

  public function show()
  {
    $this->view->render("perfil", [
      'title' => 'Perfil Usuario',
      'extra_css' => '<link rel="stylesheet" href="http://localhost/Preguntados/public/css/perfil.css">'
    ]);
  }

}