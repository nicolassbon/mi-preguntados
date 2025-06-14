<?php

class RankingModel
{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function obtenerRanking(){
        $sql = "SELECT id_usuario, nombre_usuario , foto_perfil_url, puntaje_acumulado
        FROM usuarios
        ORDER BY puntaje_acumulado DESC";
        return $this->database->query($sql);
    }

    public function obtenerPartidasJugadas(){
        $sql = "SELECT p.id_partida, u.nombre_usuario, p.fecha_inicio, p.fecha_fin, p.puntaje_final
        FROM partidas p
        JOIN usuarios u ON p.id_usuario = u.id_usuario
        ORDER BY p.fecha_inicio DESC";
        return $this->database->query($sql);
    }
}