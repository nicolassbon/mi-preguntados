<?php

class UbicacionModel
{

  private $database;

  public function __construct($database)
  {
    $this->database = $database;
  }

  public function obtenerOCrearPais($nombre)
  {
    $stmt = $this->database->prepare("SELECT id_pais FROM paises WHERE nombre_pais = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $result = $stmt->get_result();
    $pais = $result->fetch_assoc();
    $stmt->close();

    if ($pais) {
      return $pais['id_pais'];
    }

    // Insertar pais si no existe
    $stmt = $this->database->prepare("INSERT INTO paises (nombre_pais) VALUES (?)");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $stmt->close();

    return $this->database->getLastInsertId();
  }

  public function obtenerOCrearCiudad($nombreCiudad, $idPais)
  {
    $stmt = $this->database->prepare("SELECT id_ciudad FROM ciudades WHERE nombre_ciudad = ? AND id_pais = ?");
    $stmt->bind_param("si", $nombreCiudad, $idPais);
    $stmt->execute();
    $result = $stmt->get_result();
    $ciudad = $result->fetch_assoc();
    $stmt->close();

    if ($ciudad) {
      return $ciudad['id_ciudad'];
    }

    // Insertar ciudad si no existe
    $stmt = $this->database->prepare("INSERT INTO ciudades (nombre_ciudad, id_pais) VALUES (?, ?)");
    $stmt->bind_param("si", $nombreCiudad, $idPais);
    $stmt->execute();
    $stmt->close();

    return $this->database->getLastInsertId();
  }
}
