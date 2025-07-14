<?php

class UsuarioModel
{
    private Database $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function buscarUsuarioPorEmail($email): bool|array|null
    {
        $sql = "
            SELECT id_usuario, nombre_usuario, contrasena_hash, es_validado, cantidad_trampitas
            FROM usuarios
            WHERE email = ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function registrarUsuario($nombreCompleto, $anioNac, $sexoId, $idPais, $id_ciudad, $email, $contrasenaHash, $nombreUsuario, $fotoPerfil, $latitud, $longitud): array
    {

        $tokenVerificacion = md5(uniqid(mt_rand(), true));

        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nombre_completo, anio_nacimiento, id_sexo, id_pais, id_ciudad, email, contrasena_hash, nombre_usuario, foto_perfil_url, token_verificacion, latitud, longitud)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("siiiisssssdd", $nombreCompleto, $anioNac, $sexoId, $idPais, $id_ciudad, $email, $contrasenaHash, $nombreUsuario, $fotoPerfil, $tokenVerificacion, $latitud, $longitud);
        $stmt->execute();

        $idUsuario = $this->db->getLastInsertId();

        return [
            "idUsuario" => $idUsuario,
            "email" => $email,
            "nombreUsuario" => $nombreUsuario,
            "token" => $tokenVerificacion
        ];
    }

    public function verificarEmailUsuario($idVerificador, $idUsuario): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE token_verificacion = ? AND id_usuario = ?");
        $stmt->bind_param("si", $idVerificador, $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $update = $this->db->prepare("UPDATE usuarios SET es_validado = 1 WHERE id_usuario = ?");
            $update->bind_param("i", $idUsuario);
            $update->execute();

            return true;
        }
        return false;
    }

    public function existeEmail($email): bool
    {
        $stmt = $this->db->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function existeUsuario($usuario): bool
    {
        $stmt = $this->db->prepare("SELECT id_usuario FROM usuarios WHERE nombre_usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function incrementarEntregadasUsuario($id_usuario): void
    {
        $sql = "UPDATE usuarios SET preguntas_entregadas = preguntas_entregadas + 1 WHERE id_usuario = $id_usuario ";
        $this->db->execute($sql);

    }

    public function incrementarCorrectasUsuario($id_usuario): void
    {
        $stmt = $this->db->prepare("UPDATE usuarios SET preguntas_acertadas = preguntas_acertadas + 1 WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
    }

    public function sumarPuntajeUsuario($id_usuario, $puntos): void
    {
        $sql = "UPDATE usuarios SET puntaje_acumulado = puntaje_acumulado + $puntos WHERE id_usuario = $id_usuario ";
        $this->db->execute($sql);
    }

    public function getDatosPerfil($id_usuario): array
    {
        $sql = "
            SELECT u.nombre_usuario, u.foto_perfil_url,u.latitud,u.longitud, u.cantidad_trampitas,
                   p.nombre_pais, c.nombre_ciudad, r.nombre_rol
            FROM usuarios u
            JOIN paises p ON u.id_pais = p.id_pais
            JOIN ciudades c ON u.id_ciudad = c.id_ciudad
            JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.id_usuario = $id_usuario
        ";

        return $this->db->query($sql);
    }

    public function getCantidadPartidasJugadas($id_usuario)
    {
        $sql = "SELECT COUNT(*) AS total FROM partidas WHERE id_usuario = $id_usuario";
        $resultado = $this->db->query($sql);
        return $resultado[0]['total'] ?? 0;
    }

    public function getTotalPreguntasRespondidas($id_usuario)
    {
        $sql = "SELECT preguntas_entregadas FROM usuarios WHERE id_usuario = $id_usuario";
        $resultado = $this->db->query($sql);
        return $resultado[0]['preguntas_entregadas'] ?? 0;
    }

    public function getPorcentajeAcierto($id_usuario): float|int
    {
        $sql = "SELECT preguntas_acertadas, preguntas_entregadas FROM usuarios WHERE id_usuario = $id_usuario";
        $resultado = $this->db->query($sql);


        $acertadas = $resultado[0]['preguntas_acertadas'] ?? 0;
        $entregadas = $resultado[0]['preguntas_entregadas'] ?? 0;
        if ($entregadas === "0") {
            return 0;
        }

        return round(($acertadas / $entregadas) * 100, 2);
    }

    public function getMayorPuntajePartida($id_usuario)
    {
        $sql = "SELECT MAX(puntaje_final) AS max_puntaje FROM partidas WHERE id_usuario = $id_usuario";
        $resultado = $this->db->query($sql);
        return $resultado[0]['max_puntaje'] ?? 0;
    }

    public function getCategoriasDestacadas($id_usuario): array
    {
        $sql = "
            SELECT c.nombre, c.color
            FROM categoria c
            JOIN preguntas p ON p.id_categoria = c.id_categoria
            JOIN partida_pregunta pp ON pp.id_pregunta = p.id_pregunta
            JOIN partidas par ON par.id_partida = pp.id_partida
            WHERE par.id_usuario = $id_usuario
              AND pp.acerto = 1
            GROUP BY c.id_categoria
            ORDER BY COUNT(*) DESC
            LIMIT 3
        ";
        return $this->db->query($sql);
    }

    public function getPosicionRanking($id_usuario)
    {
        $puntaje = $this->db->query("SELECT puntaje_acumulado FROM usuarios WHERE id_usuario = $id_usuario");

        if (!$puntaje || $puntaje[0]['puntaje_acumulado'] === "0") {
            return null;
        }

        $sql = "
            SELECT COUNT(*) + 1 AS posicion
            FROM usuarios u
            WHERE u.id_rol = 1
              AND u.puntaje_acumulado > (
                  SELECT puntaje_acumulado
                  FROM usuarios
                  WHERE id_usuario = $id_usuario
              )
        ";

        $resultado = $this->db->query($sql);
        return $resultado[0]['posicion'] ?? null;
    }

    public function getTrampitas($id_usuario)
    {
        $sql = "
            SELECT cantidad_trampitas
            FROM usuarios
            WHERE id_usuario = $id_usuario
        ";
        $res = $this->db->query($sql);
        return $res[0]['cantidad_trampitas'] ?? 0;
    }

    public function usarTrampita($id_usuario): void
    {
        $sql = "
            UPDATE usuarios
            SET cantidad_trampitas = GREATEST(cantidad_trampitas - 1, 0)
            WHERE id_usuario = $id_usuario
        ";

        $this->db->execute($sql);
    }

    public function sumarTrampitas($id_usuario, $cantidad): void
    {
        $sql = "
            UPDATE usuarios
            SET cantidad_trampitas = cantidad_trampitas + $cantidad
            WHERE id_usuario = $id_usuario
        ";
        $this->db->execute($sql);
    }

    public function registrarCompra($id_usuario, $cantidad, $monto): void
    {
        $sql = "
            INSERT INTO compras_trampitas (id_usuario, cantidad_comprada, monto_pagado, fecha_compra)
            VALUES ($id_usuario, $cantidad, $monto, NOW())
        ";
        $this->db->execute($sql);
    }
}
