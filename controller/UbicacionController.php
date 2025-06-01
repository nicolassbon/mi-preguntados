<?php

class UbicacionController
{
  private $model;

  public function __construct($model)
  {
    $this->model = $model;
  }

  // Transforma la latitud y longitud seleccionada en el mapa en Pais y Ciudad
  public function getUbicacion()
  {
    if (!isset($_GET['lat']) || !isset($_GET['lng'])) {
      http_response_code(400);
      echo json_encode(['error' => 'Coordenadas no válidas']);
      return;
    }

    $lat = $_GET['lat'];
    $lng = $_GET['lng'];

    // Usar la API de Nominatim
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=10&addressdetails=1";

    $opts = [
      "http" => [
        "header" => "User-Agent: Preguntopolis/1.0\r\n"
      ]
    ];
    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
      http_response_code(500);
      echo json_encode(['error' => 'No se pudo obtener ubicación']);
      return;
    }

    $data = json_decode($response, true);

    $pais = $data['address']['country'] ?? 'Desconocido';
    $provincia = $data['address']['state'] ?? 'Desconocido';

    header('Content-Type: application/json');
    echo json_encode([
      'pais' => $pais,
      'provincia' => $provincia
    ]);
  }
}
