<?php

class AdminModel
{

    private Database $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function obtenerDistribucionPorGenero($desde, $hasta): array
    {
        $sql = "
            SELECT s.descripcion, COUNT(*) AS cantidad
            FROM usuarios u JOIN sexo s ON u.id_sexo = s.id_sexo
            WHERE u.fecha_registro BETWEEN '$desde' AND '$hasta'
            GROUP BY s.descripcion
        ";
        return $this->db->query($sql);
    }

    public function obtenerDistribucionPorRangoEdad($desde, $hasta): array
    {
        $sql = "
            SELECT
                CASE
                    WHEN(YEAR(CURDATE()) - anio_nacimiento) < 18 THEN 'Menor'
                    WHEN(YEAR(CURDATE()) - anio_nacimiento) BETWEEN 18 AND 60 THEN 'Mediana edad'
                    ELSE 'Mayor'
                END AS rangoEdad,
                COUNT(*) AS cantidad
            FROM usuarios u
            WHERE u.fecha_registro BETWEEN '$desde' AND '$hasta'
            GROUP BY rangoEdad
        ";
        return $this->db->query($sql);
    }

    public function obtenerUsuariosPorPaisPorFecha($desde, $hasta): array
    {
        $sql = "
            SELECT p.nombre_pais, COUNT(*) AS cantidad
            FROM usuarios u JOIN paises p ON u.id_pais = p.id_pais
            WHERE u.fecha_registro BETWEEN '$desde' AND '$hasta'
            GROUP BY u.id_pais
        ";
        return $this->db->query($sql);
    }

    public function obtenerTotalUsuarios()
    {
        $sql = "SELECT COUNT(*) AS cantidad
                FROM usuarios u
                WHERE u.id_rol = 1";
        return $this->db->query($sql)[0]['cantidad'];
    }

    public function obtenerTotalUsuariosNuevosPorFecha($desde, $hasta)
    {
        $sql = "SELECT COUNT(*) AS cantidad
                FROM usuarios u
                WHERE u.id_rol = 1 AND u.fecha_registro BETWEEN '$desde' AND '$hasta'";
        return $this->db->query($sql)[0]['cantidad'];
    }

    public function obtenerPartidasJugadasPorFecha($desde, $hasta)
    {
        $sql = "SELECT COUNT(*) AS cantidad
                FROM partidas p
                WHERE p.fecha_inicio BETWEEN '$desde' AND '$hasta'";
        return $this->db->query($sql)[0]['cantidad'];
    }

    public function obtenerPreguntasActivasPorFecha($desde, $hasta)
    {
        $sql = "
            SELECT COUNT(*) AS cantidad
            FROM preguntas p
            WHERE p.estado = 'activa' AND p.fecha_registro BETWEEN '$desde' AND '$hasta'
        ";
        return $this->db->query($sql)[0]['cantidad'];
    }

    public function obtenerPreguntasActivas()
    {
        $sql = "
            SELECT COUNT(*) AS cantidad
            FROM preguntas p
            WHERE p.estado = 'activa'
        ";
        return $this->db->query($sql)[0]['cantidad'];
    }

    public function obtenerPorcentajeGeneral($desde, $hasta): array
    {
        $sql = "
            SELECT ROUND(SUM(pp.acerto) / COUNT(*) * 100, 1) AS porcentajeCorrectas,
                ROUND(SUM(1 - pp.acerto) / COUNT(*) * 100, 1) AS porcentajeIncorrectas
            FROM partida_pregunta pp
            JOIN partidas p ON pp.id_partida = p.id_partida
            WHERE p.fecha_inicio BETWEEN '$desde' AND '$hasta'
        ";
        return $this->db->query($sql);
    }

    public function obtenerRendimientosUsuarios($desde, $hasta): array
    {
        $sql = "
            SELECT
                u.nombre_usuario,
                COUNT(DISTINCT p.id_partida) AS partidas_jugadas,
                ROUND(
                    SUM(pp.acerto) / COUNT(*) * 100
                , 1) AS porcentaje_correctas
            FROM partida_pregunta pp
            JOIN partidas p
                ON pp.id_partida = p.id_partida
            JOIN usuarios u
                ON p.id_usuario = u.id_usuario
            WHERE p.fecha_inicio BETWEEN '$desde' AND '$hasta'
            GROUP BY u.id_usuario, u.nombre_usuario
            ORDER BY partidas_jugadas DESC, porcentaje_correctas DESC
            LIMIT 10;
        ";

        return $this->db->query($sql);
    }

}
