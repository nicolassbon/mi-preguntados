<?php

namespace App\model;

use App\core\Database;

class AdminModel
{

    private Database $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function obtenerDistribucionPorGenero(string $desde, string $hasta): array
    {
        $sql = "
            SELECT s.descripcion, COUNT(*) AS cantidad
            FROM usuarios u JOIN sexo s ON u.id_sexo = s.id_sexo
            WHERE u.fecha_registro BETWEEN ? AND ?
            GROUP BY s.descripcion
        ";
        return $this->queryConFechas($sql, $desde, $hasta);
    }

    public function obtenerDistribucionPorRangoEdad(string $desde, string $hasta): array
    {
        $sql = "
            SELECT
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, anio_nacimiento, CURDATE()) < 18 THEN 'Menor'
                    WHEN TIMESTAMPDIFF(YEAR, anio_nacimiento, CURDATE()) BETWEEN 18 AND 60 THEN 'Mediana edad'
                    ELSE 'Mayor'
                END AS rangoEdad,
                COUNT(*) AS cantidad
            FROM usuarios u
            WHERE u.fecha_registro BETWEEN ? AND ?
            GROUP BY rangoEdad
        ";
        return $this->queryConFechas($sql, $desde, $hasta);
    }

    public function obtenerUsuariosPorPaisPorFecha(string $desde, string $hasta): array
    {
        $sql = "
            SELECT p.nombre_pais, COUNT(*) AS cantidad
            FROM usuarios u JOIN paises p ON u.id_pais = p.id_pais
            WHERE u.fecha_registro BETWEEN ? AND ?
            GROUP BY u.id_pais
        ";
        return $this->queryConFechas($sql, $desde, $hasta);
    }

    public function obtenerTotalUsuarios(): int
    {
        $sql = "SELECT COUNT(*) AS cantidad
                FROM usuarios u
                WHERE u.id_rol = 1";

        return $this->contarConFechas($sql);
    }

    public function obtenerTotalUsuariosNuevosPorFecha(string $desde, string $hasta): int
    {
        $sql = "SELECT COUNT(*) AS cantidad
                FROM usuarios u
                WHERE u.id_rol = 1 AND u.fecha_registro BETWEEN ? AND ?";
        return $this->contarConFechas($sql, $desde, $hasta);
    }

    public function obtenerPartidasJugadasPorFecha(string $desde, string $hasta): int
    {
        $sql = "SELECT COUNT(*) AS cantidad
                FROM partidas p
                WHERE p.fecha_inicio BETWEEN ? AND ?";
        return $this->contarConFechas($sql, $desde, $hasta);
    }

    public function obtenerPreguntasActivasPorFecha(string $desde, string $hasta): int
    {
        $sql = "SELECT COUNT(*) AS cantidad FROM preguntas p WHERE p.estado = 'activa' AND p.fecha_registro BETWEEN ? AND ?";
        return $this->contarConFechas($sql, $desde, $hasta);
    }

    public function obtenerPreguntasActivas(): int
    {
        $sql = "SELECT COUNT(*) AS cantidad FROM preguntas p WHERE p.estado = 'activa'";
        return $this->contarConFechas($sql);
    }

    public function obtenerRendimientosUsuarios(string $desde, string $hasta): array
    {
        $sql = "
            SELECT
                u.nombre_usuario,
                COUNT(DISTINCT p.id_partida) AS partidas_jugadas,
                ROUND(SUM(pp.acerto) / NULLIF(COUNT(*), 0) * 100, 1) AS porcentaje_correctas
            FROM partida_pregunta pp
            JOIN partidas p
                ON pp.id_partida = p.id_partida
            JOIN usuarios u
                ON p.id_usuario = u.id_usuario
            WHERE p.fecha_inicio BETWEEN ? AND ?
            GROUP BY u.id_usuario, u.nombre_usuario
            ORDER BY partidas_jugadas DESC, porcentaje_correctas DESC
            LIMIT 10;
        ";

        return $this->queryConFechas($sql, $desde, $hasta);
    }

    public function obtenerBalanceTrampitasPorUsuarioConFecha(string $desde, string $hasta): array
    {
        $sql = "
            SELECT
                u.nombre_usuario,
                u.cantidad_trampitas,
                SUM(c.monto_pagado) AS total_gastado
            FROM usuarios u
            INNER JOIN compras_trampitas c ON u.id_usuario = c.id_usuario
            WHERE c.fecha_compra BETWEEN ? AND ?
            GROUP BY u.id_usuario
            ORDER BY total_gastado DESC
        ";

        return $this->queryConFechas($sql, $desde, $hasta);
    }

    public function obtenerGananciaTotalTrampitas(string $desde, string $hasta): float
    {
        $sql = "
            SELECT SUM(c.monto_pagado) as total
            FROM compras_trampitas c
            WHERE c.fecha_compra BETWEEN ? AND ?
        ";
        $result = $this->db->query($sql, [$desde, $hasta], "ss");
        return (float)$result[0]['total'];
    }

    private function queryConFechas(string $sql, string $desde, string $hasta): array
    {
        return $this->db->query($sql, [$desde, $hasta], "ss");
    }

    private function contarConFechas(string $sql, string $desde = null, string $hasta = null): int
    {
        $params = [];
        $types = "";
        if ($desde && $hasta) {
            $params = [$desde, $hasta];
            $types = "ss";
        }
        return (int)$this->db->query($sql, $params, $types)[0]['cantidad'];
    }

}
