<?php

use JetBrains\PhpStorm\NoReturn;

class TrampitasController
{
    private MustachePresenter $view;
    private UsuarioModel $usuarioModel;

    public function __construct($view, $usuarioModel)
    {
        $this->view = $view;
        $this->usuarioModel = $usuarioModel;
    }

    public function comprar(): void
    {
        $trampitas = $this->usuarioModel->getTrampitas($_SESSION['usuario_id']);
        $estadoCompra = $_GET['compra'] ?? null;
        $mensaje = '';

        if ($estadoCompra === "ok") {
            $mensaje = 'Se completo correctamente la compra!';
        }

        if ($estadoCompra === "invalid") {
            $mensaje = 'Hubo un error al completar la compra!';
        }


        $this->view->render("comprarTrampitas", [
            'title' => "Comprar Trampitas",
            'trampitas' => $trampitas,
            'exito' => $estadoCompra === "ok",
            'error' => $estadoCompra === 'invalid',
            'mensaje' => $mensaje
        ]);
    }

    #[NoReturn] public function procesarCompra(): void
    {
        $id_usuario = $_SESSION['usuario_id'] ?? null;
        $cantidad = (int)($_POST['cantidad'] ?? 0);

        if (!$id_usuario || $cantidad <= 0) {
            header("Location: /trampitas/comprar?compra=invalid");
            exit;
        }

        $monto = $cantidad * 1.00;

        $this->usuarioModel->sumarTrampitas($id_usuario, $cantidad);
        $this->usuarioModel->registrarCompra($id_usuario, $cantidad, $monto);

        header("Location: /trampitas/comprar?compra=ok");
        exit;
    }

}
