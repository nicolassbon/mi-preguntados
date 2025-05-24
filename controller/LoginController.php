<?php

class LoginController
{
    private $model;
    private $view;

    public function __construct($model, $view){
        $this->model = $model;
        $this->view = $view;
    }

    public function show(){
        $this->view->render("login");
    }

    public function loguearse(){
        if (!isset($_POST["email"]) || !isset($_POST["password"])) {
            $this->view->render("login");
            return;
        }

        $email = $_POST["email"];
        $password = $_POST["password"];

        $usuario = $this->model->buscarUsuarioPorEmail($email);

        if ($usuario && password_verify($password, $usuario["contrasena_hash"])) {
            session_start();
            $_SESSION["usuario_id"] = $usuario["id_usuario"];
            $_SESSION["nombre_usuario"] = $usuario["nombre_usuario"];
            header("Location: index.php?controller=home&method=show");
        } else {
            echo "<p style='color:red;text-align:center;'>Correo o contraseña incorrectos</p>";
            $this->view->render("login");
        }
    }
}