<?php

class UbicacionModel
{
    private $db; // instancia de Database

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Hace reverse‐geocoding sobre lat/lng usando Nominatim
     * y devuelve ['pais' => string, 'ciudad' => string] o null.
     */
    public function obtenerPaisYCiudadPorCoordenadas(float $lat, float $lng)
    {
        $url = "https://nominatim.openstreetmap.org/reverse?"
            . "format=json&lat={$lat}&lon={$lng}&zoom=10&addressdetails=1";

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

        $data = json_decode($json, true);
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

    public function obtenerOCrearPais($nombre)
    {
        $stmt = $this->db->prepare("SELECT id_pais FROM paises WHERE nombre_pais = ?");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $result = $stmt->get_result();
        $pais = $result->fetch_assoc();
        $stmt->close();

        if ($pais) {
            return $pais['id_pais'];
        }

        // Insertar pais si no existe
        $stmt = $this->db->prepare("INSERT INTO paises (nombre_pais) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->close();

        return $this->db->getLastInsertId();
    }

    public function obtenerOCrearCiudad($nombreCiudad, $idPais)
    {
        $stmt = $this->db->prepare("SELECT id_ciudad FROM ciudades WHERE nombre_ciudad = ? AND id_pais = ?");
        $stmt->bind_param("si", $nombreCiudad, $idPais);
        $stmt->execute();
        $result = $stmt->get_result();
        $ciudad = $result->fetch_assoc();
        $stmt->close();

        if ($ciudad) {
            return $ciudad['id_ciudad'];
        }

        // Insertar ciudad si no existe
        $stmt = $this->db->prepare("INSERT INTO ciudades (nombre_ciudad, id_pais) VALUES (?, ?)");
        $stmt->bind_param("si", $nombreCiudad, $idPais);
        $stmt->execute();
        $stmt->close();

        return $this->db->getLastInsertId();
    }

}