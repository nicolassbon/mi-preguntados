<?php

use JetBrains\PhpStorm\NoReturn;

class HomeController
{

    private $view;
    private $usuarioModel;

    public function __construct($view, $usuarioModel)
    {
        $this->view = $view;
        $this->usuarioModel = $usuarioModel;
    }

    public function show(): void
    {
        $rol = $_SESSION['rol_usuario'] ?? null;

        switch ($rol) {
            case 'admin':
                $this->redirectTo("/admin");
                break;
            case 'editor':
                $this->redirectTo("/editor");
                break;
            case 'jugador':
            default:
                $trampitas = $this->usuarioModel->getTrampitas($_SESSION['usuario_id']);
                $this->view->render("lobbyJugador", [
                    'title' => 'Lobby Jugador',
                    'trampitas' => $trampitas,
                ]);
                break;
        }
    }

    public function error(): void
    {
        $message = $_SESSION['error_message'] ?? null;

        $this->view->render("error", [
            'title' => 'Error',
            'message' => $message
        ]);

        unset($_SESSION['error_message']);
    }

    #[NoReturn] private
    function redirectTo($str): void
    {
        header('Location: ' . $str);
        exit();
    }

}
