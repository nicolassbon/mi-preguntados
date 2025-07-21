<?php

namespace App\model;

use App\core\Database;

class UsuarioModel
{
    private Database $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function getUsuarioPorId(int $idUsuario): ?array
    {
        $sql = "SELECT id_usuario, nombre_completo, email FROM usuarios WHERE id_usuario = ?";
        $result = $this->db->query($sql, [$idUsuario], "i");
        return $result[0] ?? null;
    }

    public function buscarUsuarioPorEmail(string $email): ?array
    {
        $sql = "
            SELECT id_usuario, nombre_usuario, contrasena_hash, es_validado, cantidad_trampitas
            FROM usuarios
            WHERE email = ?
        ";

        $result = $this->db->query($sql, [$email], "s");
        return $result[0] ?? null;
    }

    public function registrarUsuario(string $nombreCompleto, string $anioNac, int $sexoId, int $idPais, int $id_ciudad, string $email, string $contrasenaHash, string $nombreUsuario, ?string $fotoPerfil, float $latitud, float $longitud): array
    {

        $tokenVerificacion = md5(uniqid(mt_rand(), true));

        $sql = "INSERT INTO usuarios (nombre_completo, anio_nacimiento, id_sexo, id_pais, id_ciudad, email, contrasena_hash, nombre_usuario, foto_perfil_url, token_verificacion, latitud, longitud)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [$nombreCompleto, $anioNac, $sexoId, $idPais, $id_ciudad, $email, $contrasenaHash, $nombreUsuario, $fotoPerfil, $tokenVerificacion, $latitud, $longitud], "ssiiisssssdd");

        $idUsuario = $this->db->getLastInsertId();

        return [
            "idUsuario" => $idUsuario,
            "email" => $email,
            "nombreUsuario" => $nombreUsuario,
            "token" => $tokenVerificacion
        ];
    }

    public function verificarEmailUsuario(string $idVerificador, int $idUsuario): bool
    {
        $sql = "SELECT * FROM usuarios WHERE token_verificacion = ? AND id_usuario = ?";
        $result = $this->db->query($sql, [$idVerificador, $idUsuario], "si");

        if (!empty($result)) {
            $this->db->execute("UPDATE usuarios SET es_validado = 1 WHERE id_usuario = ?", [$idUsuario], "i");
            return true;
        }
        return false;
    }

    public function existeEmail(string $email): bool
    {
        $sql = "SELECT id_usuario FROM usuarios WHERE email = ?";
        $result = $this->db->query($sql, [$email], "s");
        return count($result) > 0;
    }

    public function existeUsuario(string $usuario): bool
    {
        $sql = "SELECT id_usuario FROM usuarios WHERE nombre_usuario = ?";
        $result = $this->db->query($sql, [$usuario], "s");
        return count($result) > 0;
    }

    public function incrementarEntregadasUsuario(int $id_usuario): void
    {
        $sql = "UPDATE usuarios SET preguntas_entregadas = preguntas_entregadas + 1 WHERE id_usuario = ?";
        $this->db->execute($sql, [$id_usuario], "i");
    }

    public function incrementarCorrectasUsuario(int $id_usuario): void
    {
        $sql = "UPDATE usuarios SET preguntas_acertadas = preguntas_acertadas + 1 WHERE id_usuario = ?";
        $this->db->execute($sql, [$id_usuario], "i");
    }

    public function sumarPuntajeUsuario(int $id_usuario, int $puntos): void
    {
        $sql = "UPDATE usuarios SET puntaje_acumulado = puntaje_acumulado + ? WHERE id_usuario = ?";
        $this->db->execute($sql, [$puntos, $id_usuario], "ii");
    }

    public function getDatosPerfil(int $id_usuario): array
    {
        $sql = "
            SELECT u.nombre_usuario, u.foto_perfil_url,u.latitud,u.longitud, u.cantidad_trampitas,
                   p.nombre_pais, c.nombre_ciudad, r.nombre_rol
            FROM usuarios u
            JOIN paises p ON u.id_pais = p.id_pais
            JOIN ciudades c ON u.id_ciudad = c.id_ciudad
            JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.id_usuario = ?
        ";

        return $this->db->query($sql, [$id_usuario], "i");
    }

    public function getCantidadPartidasJugadas(int $id_usuario)
    {
        $sql = "SELECT COUNT(*) AS total FROM partidas WHERE id_usuario = ?";
        $resultado = $this->db->query($sql, [$id_usuario], "i");
        return $resultado[0]['total'] ?? 0;
    }

    public function getTotalPreguntasRespondidas(int $id_usuario)
    {
        $sql = "SELECT preguntas_entregadas FROM usuarios WHERE id_usuario = ?";
        $resultado = $this->db->query($sql, [$id_usuario], "i");
        return $resultado[0]['preguntas_entregadas'] ?? 0;
    }

    public function getPorcentajeAcierto(int $id_usuario): float|int
    {
        $sql = "SELECT preguntas_acertadas, preguntas_entregadas FROM usuarios WHERE id_usuario = ?";
        $resultado = $this->db->query($sql, [$id_usuario], "i");


        $acertadas = $resultado[0]['preguntas_acertadas'] ?? 0;
        $entregadas = $resultado[0]['preguntas_entregadas'] ?? 0;
        if ((int)$entregadas === 0) {
            return 0;
        }

        return round(($acertadas / $entregadas) * 100, 2);
    }

    public function getMayorPuntajePartida(int $id_usuario)
    {
        $sql = "SELECT MAX(puntaje_final) AS max_puntaje FROM partidas WHERE id_usuario = ?";
        $resultado = $this->db->query($sql, [$id_usuario], "i");
        return $resultado[0]['max_puntaje'] ?? 0;
    }

    public function getCategoriasDestacadas(int $id_usuario): array
    {
        $sql = "
            SELECT c.nombre, c.color
            FROM categoria c
            JOIN preguntas p ON p.id_categoria = c.id_categoria
            JOIN partida_pregunta pp ON pp.id_pregunta = p.id_pregunta
            JOIN partidas par ON par.id_partida = pp.id_partida
            WHERE par.id_usuario = ?
              AND pp.acerto = 1
            GROUP BY c.id_categoria
            ORDER BY COUNT(*) DESC
            LIMIT 3
        ";
        return $this->db->query($sql, [$id_usuario], "i");
    }

    public function getPosicionRanking(int $id_usuario)
    {
        $sql = "SELECT puntaje_acumulado FROM usuarios WHERE id_usuario = ?";
        $puntaje = $this->db->query($sql, [$id_usuario], "i");

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
                  WHERE id_usuario = ?
              )
        ";

        $resultado = $this->db->query($sql, [$id_usuario], "i");
        return $resultado[0]['posicion'] ?? null;
    }

    public function getTrampitas(int $id_usuario)
    {
        $sql = "
            SELECT cantidad_trampitas
            FROM usuarios
            WHERE id_usuario = ?
        ";
        $res = $this->db->query($sql, [$id_usuario], "i");
        return $res[0]['cantidad_trampitas'] ?? 0;
    }

    public function usarTrampita(int $id_usuario): void
    {
        $sql = "
            UPDATE usuarios
            SET cantidad_trampitas = GREATEST(cantidad_trampitas - 1, 0)
            WHERE id_usuario = ?
        ";

        $this->db->execute($sql, [$id_usuario], "i");
    }

    public function sumarTrampitas(int $id_usuario, int $cantidad): void
    {
        $sql = "
            UPDATE usuarios
            SET cantidad_trampitas = cantidad_trampitas + ?
            WHERE id_usuario = ?
        ";
        $this->db->execute($sql, [$cantidad, $id_usuario], "ii");
    }

    public function registrarCompra(int $id_usuario, int $cantidad, float $monto, string $referencia): void
    {
        $sql = "
            INSERT INTO compras_trampitas (id_usuario, cantidad_comprada, monto_pagado, fecha_compra, referencia_externa)
            VALUES (?, ?, ?, NOW(), ?)
        ";
        $this->db->execute($sql, [$id_usuario, $cantidad, $monto, $referencia], "iids");
    }

    public function compraYaProcesada(string $externalReference): bool
    {
        $sql = "SELECT 1 FROM compras_trampitas WHERE referencia_externa = ? LIMIT 1";
        $result = $this->db->query($sql, [$externalReference], "s");
        return !empty($result);
    }
}
