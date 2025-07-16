<?php

namespace App\core;

use Exception;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Resources\Preference;

class MercadoPagoService
{
    private string $accessToken;
    private string $baseUrl;

    public function __construct(string $accessToken, string $baseUrl) {
        $this->accessToken = $accessToken;
        $this->baseUrl = $baseUrl;
    }

    public function authenticate(): void
    {
        // Set the token the SDK's config
        MercadoPagoConfig::setAccessToken($this->accessToken);
    }

    // Function that will return a request object to be sent to Mercado Pago API
    public function createPreferenceRequest($items, $payer, $externalReference): array
    {
        $paymentMethods = [
            "excluded_payment_methods" => [],
            "installments" => 12,
            "default_installments" => 1
        ];

        $backUrls = [
            'success' => $this->baseUrl . "/trampitas/comprar?compra=success",
            'failure' => $this->baseUrl . "/trampitas/comprar?compra=invalid",
            'pending' => $this->baseUrl . "/trampitas/comprar?compra=pending"
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
            "notification_url" => $this->baseUrl . "/trampitas/recibirNotificacion"
        ];
    }

    public function createPaymentPreference(int $cantidad, float $precio, string $externalReference, array $user): ?Preference
    {
        $product1 = array(
            "id" => "1234567890",
            "title" => "Compra de $cantidad Trampita/s",
            "currency_id" => "USD",
            "quantity" => 1,
            "unit_price" => $precio,
        );

        // Mount the array of products that will integrate the purchase amount
        $items = array($product1);

        // Retrieve information about the user
        $payer = array(
            "name" => $user["nombre_completo"],
            "email" => filter_var($user["email"], FILTER_VALIDATE_EMAIL) ?: "comprador@test.com"
        );

        // Create the request object to be sent to the API when the preference is created
        $request = $this->createPreferenceRequest($items, $payer, $externalReference);

        // Instantiate a new Preference Client
        $client = new PreferenceClient();

        try {
            // Send the request that will create the new preference for user's checkout flow
            return $client->create($request);
        } catch (MPApiException|Exception $error) {
            error_log("Error al crear preferencia de Mercado Pago: " . $error->getMessage());
            return null;
        }
    }
}
