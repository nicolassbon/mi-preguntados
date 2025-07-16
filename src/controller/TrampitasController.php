<?php

namespace App\controller;

use App\core\MustachePresenter;
use App\model\UsuarioModel;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\Resources\Preference;
use MercadoPago\Client\Payment\PaymentClient;


class TrampitasController
{
    private MustachePresenter $view;
    private UsuarioModel $usuarioModel;
    private const ACCESS_TOKEN = 'APP_USR-1161986002564820-112507-a3751c8d816ca6e05db73ad7ff938d68-2115199025';
    private const BASE_URL = "https://f23dd6ee1e00.ngrok-free.app";

    public function __construct($view, $usuarioModel)
    {
        $this->view = $view;
        $this->usuarioModel = $usuarioModel;
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

        $monto = $cantidad * 1.00;
        $externalReference = "trampitas_{$id_usuario}_{$cantidad}_{$monto}_" . time();

        $this->authenticate();

        $preference = $this->createPaymentPreference($cantidad, $monto, $externalReference);

        header("Location: " . $preference->init_point);
        exit;
    }

    public function recibirNotificacion(): void
    {
        $this->authenticate();

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

                        $parts = explode('_', $reference);
                        $id_usuario = $parts[1];
                        $cantidad = $parts[2];
                        $monto = $parts[3];

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

    public function authenticate(): void
    {
        MercadoPagoConfig::setAccessToken(self::ACCESS_TOKEN);
    }

    public function createPreferenceRequest($items, $payer, $externalReference): array
    {
        $paymentMethods = [
            "excluded_payment_methods" => [],
            "installments" => 12,
            "default_installments" => 1
        ];

        $backUrls = [
            'success' => self::BASE_URL . "/trampitas/comprar?compra=success",
            'failure' => self::BASE_URL . "/trampitas/comprar?compra=invalid",
            'pending' => self::BASE_URL . "/trampitas/comprar?compra=pending"
        ];

        return [
            "items" => $items,
            "payer" => $payer,
            "payment_methods" => $paymentMethods,
            "back_urls" => $backUrls,
            "statement_descriptor" => "NAME_DISPLAYED_IN_USER_BILLING",
            "expires" => false,
            "external_reference" => $externalReference,
            "auto_return" => "all",
            "notification_url" => self::BASE_URL . "/trampitas/recibirNotificacion"
        ];
    }

    public function createPaymentPreference($cantidad, $precio, $externalReference): ?Preference
    {
        $product1 = array(
            "id" => "1234567890",
            "title" => "Compra $cantidad de Trampitas",
            "currency_id" => "USD",
            "quantity" => 1,
            "unit_price" => $precio,
        );

        $items = array($product1);

        $user = $this->usuarioModel->getUsuarioPorId($_SESSION['usuario_id'] ?? null);

        $payer = array(
            "name" => $user["nombre_completo"],
            "email" => filter_var($user["email"], FILTER_VALIDATE_EMAIL) ?: "comprador@test.com"
        );

        $request = $this->createPreferenceRequest($items, $payer, $externalReference);

        $client = new PreferenceClient();

        try {
            // Send the request that will create the new preference for user's checkout flow
            return $client->create($request);
        } catch (MPApiException $error) {
            echo "<pre>";
            echo "Mercado Pago Error: " . $error->getMessage() . "\n";
            print_r($error->getApiResponse()->getContent());
            echo "</pre>";
            exit;
        } catch (Exception $e) {
            echo "<pre>";
            echo "Error: " . $e->getMessage() . "\n";
            echo "</pre>";
            exit;
        }
    }
}
