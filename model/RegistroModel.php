<?php

class RegistroModel
{
  private $database;

  public function __construct($database)
  {
    $this->database = $database;
  }

  public function getUsuariosRegistrados()
  {
    $this->database->query("SELECT * FROM usuarios");
  }

  public function registrarUsuario($nombreCompleto, $anioNac, $sexoId, $idPais, $id_ciudad, $email, $contrasenaHash, $nombreUsuario, $fotoPerfil, $latitud, $longitud)
  {

    $tokenVerificacion = md5(uniqid(rand(), true));

      $stmt = $this->database->prepare(
          "INSERT INTO usuarios (nombre_completo, anio_nacimiento, id_sexo, id_pais, id_ciudad, email, contrasena_hash, nombre_usuario, foto_perfil_url, token_verificacion, latitud, longitud)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
      );

    $stmt->bind_param("siiiisssssdd", $nombreCompleto, $anioNac, $sexoId, $idPais, $id_ciudad, $email, $contrasenaHash, $nombreUsuario, $fotoPerfil, $tokenVerificacion, $latitud, $longitud);
    $stmt->execute();

    $idUsuario = $this->database->getLastInsertId();

    return [
      "idUsuario" => $idUsuario,
      "email" => $email,
      "nombreUsuario" => $nombreUsuario,
      "token" => $tokenVerificacion
    ];
  }

  public function verificarEmailUsuario($idVerificador, $idUsuario)
  {
    $stmt = $this->database->prepare("SELECT * FROM usuarios WHERE token_verificacion = ? AND id_usuario = ?");
    $stmt->bind_param("si", $idVerificador, $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
      $update = $this->database->prepare("UPDATE usuarios SET es_validado = 1 WHERE id_usuario = ?");
      $update->bind_param("i", $idUsuario);
      $update->execute();

      return true;
    }
    return false;
  }
}