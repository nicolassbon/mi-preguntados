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
      'css' => '<link rel="stylesheet" href="/public/css/styles.css" >'
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
      $this->redirectTo("/perfil/show");
    } else {
      echo "<p style='color:red;text-align:center;'>Correo o contraseña incorrectos</p>";
      $this->view->render("login");
    }

  }

  public function logout()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      session_unset();
      session_destroy();
      $this->redirectTo("/login/show");
    }
  }

  private function redirectTo($str)
  {
    header('Location: ' . $str);
    exit();
  }

}