<?php

class LoginController
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
    $this->view->render("login", [
      'title' => 'Iniciar sesión',
      'extra_css' => '<link rel="stylesheet" href="http://localhost/Preguntados/public/css/styles.css" >
                      <link rel="stylesheet" href="http://localhost/Preguntados/public/css/login.css">'
    ]);
  }

  public function loguearse()
  {
    if (!isset($_POST["email"]) || !isset($_POST["password"])) {
      $this->view->render("login");
      return;
    }

    $email = $_POST["email"];
    $password = $_POST["password"];

    $usuario = $this->model->buscarUsuarioPorEmail($email);

    if ($usuario && password_verify($password, $usuario["contrasena_hash"])) {
      $_SESSION["usuario_id"] = $usuario["id_usuario"];
      $_SESSION["nombre_usuario"] = $usuario["nombre_usuario"];
      header("Location: index.php?controller=perfil&method=show");
    } else {
      echo "<p style='color:red;text-align:center;'>Correo o contraseña incorrectos</p>";
      $this->view->render("login");
    }

  }

}