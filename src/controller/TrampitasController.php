<?php

namespace App\controller;

use App\core\MercadoPagoService;
use App\core\MustachePresenter;
use App\model\UsuarioModel;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use JsonException;
use MercadoPago\Client\Payment\PaymentClient;

class TrampitasController
{
    private MustachePresenter $view;
    private UsuarioModel $usuarioModel;
    private MercadoPagoService $mercadoPagoService;

    public function __construct($view, $usuarioModel, $mercadoPagoService)
    {
        $this->view = $view;
        $this->usuarioModel = $usuarioModel;
        $this->mercadoPagoService = $mercadoPagoService;
    }

    public function comprar(): void
    {
        $trampitas = $this->usuarioModel->getTrampitas($_SESSION['usuario_id']);
        $estadoCompra = $_GET['compra'] ?? null;
        $mensaje = null;

        if ($estadoCompra === "success") {
            $mensaje = 'Se completo correctamente la compra!';
        }

        if ($estadoCompra === "invalid") {
            $mensaje = 'Hubo un error al completar la compra!';
        }

        if ($estadoCompra === "pending") {
            $mensaje = 'La compra quedo pendiente!';
        }

        $this->view->render("comprarTrampitas", [
            'title' => "Comprar Trampitas",
            'trampitas' => $trampitas,
            'mensaje' => $mensaje
        ]);
    }

    /**
     * @throws Exception
     */
    #[NoReturn] public function procesarCompra(): void
    {
        $id_usuario = $_SESSION['usuario_id'] ?? null;
        $cantidad = (int)($_POST['cantidad'] ?? 0);

        if (!$id_usuario || $cantidad <= 0) {
            error_log("Error: Usuario o cantidad inválidos");
            header("Location: /trampitas/comprar?compra=invalid");
            exit;
        }

        $user = $this->usuarioModel->getUsuarioPorId($_SESSION['usuario_id'] ?? null);

        $monto = $cantidad * 1.00;
        $externalReference = "trampitas_{$id_usuario}_{$cantidad}_{$monto}_" . time();

        $this->mercadoPagoService->authenticate();

        $preference = $this->mercadoPagoService->createPaymentPreference($cantidad, $monto, $externalReference, $user);

        if (!$preference) {
            header("Location: /trampitas/comprar?compra=invalid");
            exit;
        }

        header("Location: " . $preference->init_point);
        exit;
    }

    public function recibirNotificacion(): void
    {
        $this->mercadoPagoService->authenticate();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty(file_get_contents('php://input'))) {
            error_log("Intento inválido de acceder manualmente al webhook");
            header("Location: /");
            exit;
        }

        $input = file_get_contents('php://input');

        try {
            $notification = json_decode($input, true, 512, JSON_THROW_ON_ERROR);

            if (isset($notification['type']) && $notification['type'] === 'payment') {
                $paymentId = $notification['data']['id'];

                $client = new PaymentClient();
                try {
                    // Agregar comentarios para ver el error_log si llega a este metodo y asi
                    // Ultima vez de ejecucion hizo el catch de una excepcion
                    $payment = $client->get($paymentId);

                    if ($payment->status === 'approved') {
                        $reference = $payment->external_reference;

                        if ($this->usuarioModel->compraYaProcesada($reference)) {
                            error_log("Compra ya fue procesada, se ignora.");
                            http_response_code(200);
                            return;
                        }

                        [, $id_usuario, $cantidad, $monto] = explode('_', $reference);

                        $this->usuarioModel->sumarTrampitas($id_usuario, $cantidad);
                        $this->usuarioModel->registrarCompra($id_usuario, $cantidad, $monto, $reference);
                    } else {
                        error_log("El estado del pago no es 'approved', no se hace nada.");
                    }
                } catch (Exception $e) {
                    error_log("Error al consultar el pago: " . $e->getMessage());
                }
            }
        } catch (JsonException $e) {
            error_log("Error al decodificar JSON: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Error en Webhook de Mercado Pago: " . $e->getMessage());
        }

        http_response_code(200);
    }
}
