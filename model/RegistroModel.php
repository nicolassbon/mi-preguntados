<?php

class RegistroModel
{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function getUsuariosRegistrados(){
        $this->database->query("SELECT * FROM usuarios");
    }

    public function registrarUsuario($nombreCompleto, $anioNac, $sexoId, $idPais, $id_ciudad, $email, $contrasenaHash, $nombreUsuario, $fotoPerfil){

        $stmt = $this->database->prepare(
            "INSERT INTO usuarios (nombre_completo, anio_nacimiento, id_sexo, id_pais, id_ciudad, email, contrasena_hash, nombre_usuario, foto_perfil)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("siiiissss", $nombreCompleto, $anioNac, $sexoId, $idPais, $id_ciudad, $email, $contrasenaHash, $nombreUsuario, $fotoPerfil);
        $stmt->execute();
    }
}