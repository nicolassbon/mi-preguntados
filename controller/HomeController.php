<?php

class HomeController
{

    private $view;

    public function __construct($view)
    {

        $this->view = $view;
    }

    public function show()
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
                $this->view->render("lobbyJugador", [
                    'title' => 'Lobby Jugador'
                ]);
                break;
        }
    }

    public function error() {
        $message = $_SESSION['error_message'] ?? null;

        $this->view->render("error", [
            'title' => 'Error',
            'message' => $message
        ]);

        unset($_SESSION['error_message']);
    }

    private
    function redirectTo($str)
    {
        header('Location: ' . $str);
        exit();
    }

}