<?php

class PerdioController
{


    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function show()
    {
        // Limpiar datos de la partida,categoria y pregunta actual en sesión
        unset($_SESSION["nombre_categoria"], $_SESSION["id_pregunta"], $_SESSION["pregunta"], $_SESSION["inicio_pregunta"], $_SESSION['id_partida']);

        $this->view->render("perdio", [
            'title' => 'Partida Perdida',
            'puntaje' => $_SESSION['puntaje'],
            'cantidad' => $_SESSION['cantidad'] ?? 0
        ]);
    }


}