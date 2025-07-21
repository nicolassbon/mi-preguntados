<?php

namespace App\model;

use App\core\Database;
use JsonException;

class UbicacionModel
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @throws JsonException
     */
    public function obtenerPaisYCiudadPorCoordenadas(float $lat, float $lng): ?array
    {
        $url = "https://nominatim.openstreetmap.org/reverse?"
            . "format=json&lat=$lat&lon=$lng&zoom=10&addressdetails=1";

        $opts = [
            "http" => [
                "header" => "User-Agent: Preguntopolis/1.0\r\n"
            ]
        ];
        $ctx = stream_context_create($opts);
        $json = @file_get_contents($url, false, $ctx);
        if (!$json) {
            return null;
        }

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $pais = $data['address']['country'] ?? null;
        // usar 'city' o 'town' o fallback a 'state'
        $ciudad = $data['address']['city']
            ?? $data['address']['town']
            ?? $data['address']['state']
            ?? null;

        if (!$pais || !$ciudad) {
            return null;
        }
        return ['pais' => $pais, 'ciudad' => $ciudad];
    }

    public function obtenerOCrearPais(string $nombre)
    {
        $sql = "SELECT id_pais FROM paises WHERE nombre_pais = ?";
        $result = $this->db->query($sql, [$nombre], "s");

        if (!empty($result)) {
            return $result[0]['id_pais'];
        }

        // Insertar pais si no existe
        $sql = "INSERT INTO paises (nombre_pais) VALUES (?)";
        $this->db->query($sql, [$nombre], "s");

        return $this->db->getLastInsertId();
    }

    public function obtenerOCrearCiudad(string $nombreCiudad, int $idPais)
    {
        $sql = "SELECT id_ciudad FROM ciudades WHERE nombre_ciudad = ? AND id_pais = ?";
        $result = $this->db->query($sql, [$nombreCiudad, $idPais], "si");

        if (!empty($result)) {
            return $result[0]['id_ciudad'];
        }

        // Insertar ciudad si no existe
        $sql = "INSERT INTO ciudades (nombre_ciudad, id_pais) VALUES (?, ?)";
        $this->db->query($sql, [$nombreCiudad, $idPais], "si");

        return $this->db->getLastInsertId();
    }

}
