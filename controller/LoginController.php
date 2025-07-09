<?php

class LoginController
{
    private $view;
    private $usuarioModel;
    private $rolModel;

    public function __construct($view, $usuarioModel, $rolModel)
    {
        $this->usuarioModel = $usuarioModel;
        $this->view = $view;
        $this->rolModel = $rolModel;
    }

    public function show()
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirigirPorRol($_SESSION['rol_usuario'] ?? '');
            return;
        }

        $error = $_SESSION['login_error'] ?? null;

        if (isset($_GET['error']) && $_GET['error'] === 'trampa') {
            $error = "Has sido desconectado por intento de trampa.";
        }

        unset($_SESSION['login_error']);

        $this->view->render("login", [
            'title' => 'Iniciar sesión',
            'error' => $error
        ]);
    }

    public function procesar()
    {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $usuario = $this->usuarioModel->buscarUsuarioPorEmail($email);

        if (!$usuario || !password_verify($password, $usuario["contrasena_hash"])) {
            $_SESSION['login_error'] = 'Correo o contraseña incorrectos';
            $this->redirectTo("/login/show");
            return;
        }

        if (!$usuario["es_validado"]) {
            $_SESSION['login_error'] = 'Tu cuenta aún no fue validada. Por favor revisá tu correo.';
            $this->redirectTo("/login/show");
            return;
        }

        $_SESSION["usuario_id"] = $usuario["id_usuario"];
        $_SESSION["nombre_usuario"] = $usuario["nombre_usuario"];

        $rolUsuario = $this->rolModel->getRolDelUsuario($usuario['id_usuario']);
        $_SESSION['rol_usuario'] = $rolUsuario;
        $_SESSION['esEditor'] = $rolUsuario === 'editor' ?? false;
        $_SESSION['esAdmin'] = $rolUsuario === 'admin' ?? false;
        $_SESSION['esJugador'] = $rolUsuario === 'jugador' ?? false;

        $this->redirectTo("/lobby");
    }

    private function redirigirPorRol(string $rol): void
    {
        switch ($rol) {
            case 'admin':
                $this->redirectTo("/admin");
                break;
            case 'editor':
                $this->redirectTo("/editor");
                break;
            case 'jugador':
            default:
                $this->redirectTo("/lobby");
                break;
        }
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_unset();
            session_destroy();
            $this->redirectTo("/login");
        }
    }

    private function redirectTo($str)
    {
        header('Location: ' . $str);
        exit();
    }

}