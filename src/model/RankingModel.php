<?php

namespace App\model;

use App\core\Database;

class RankingModel
{
    private Database $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function obtenerRanking($desde, $hasta): array
    {
        $sql = "
            SELECT
                u.id_usuario,
                u.nombre_usuario,
                u.foto_perfil_url,
                u.puntaje_acumulado,
                u.preguntas_acertadas,
                u.preguntas_entregadas,
                ROUND(u.preguntas_acertadas / NULLIF(u.preguntas_entregadas, 0), 2) AS `precision`,
                COUNT(p.id_partida) AS partidas_jugadas
            FROM usuarios u
            JOIN partidas p ON u.id_usuario = p.id_usuario
            WHERE u.id_rol = 1
              AND p.fecha_inicio BETWEEN '$desde' AND '$hasta'
            GROUP BY
                u.id_usuario,
                u.nombre_usuario,
                u.foto_perfil_url,
                u.puntaje_acumulado,
                u.preguntas_acertadas,
                u.preguntas_entregadas
            ORDER BY
                u.puntaje_acumulado DESC,
                `precision` DESC,
                partidas_jugadas DESC
            LIMIT 10
        ";

        return $this->database->query($sql);
    }

    public function obtenerPartidasJugadas($desde, $hasta): array
    {
        $sql = "
            SELECT id_partida, nombre_usuario, fecha_inicio, fecha_fin, puntaje_final, id_usuario
            FROM (
                SELECT p.id_partida,u.nombre_usuario,p.fecha_inicio,p.fecha_fin,p.puntaje_final,u.id_usuario,
                    ROW_NUMBER() OVER (
                        PARTITION BY u.id_usuario
                        ORDER BY p.puntaje_final DESC, p.fecha_fin DESC
                    ) AS rn
                FROM partidas p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE u.id_rol = 1 AND p.fecha_inicio BETWEEN '$desde' AND '$hasta' AND p.fecha_fin IS NOT NULL
            ) AS sub
            WHERE rn = 1
            ORDER BY puntaje_final DESC, fecha_fin DESC
            LIMIT 10;
        ";

        return $this->database->query($sql);
    }

}
