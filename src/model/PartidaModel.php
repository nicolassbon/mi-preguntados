<?php

namespace App\model;

use App\core\Database;

class PartidaModel
{

    private Database $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function crearPartida(int $id_usuario)
    {
        $sql = "INSERT INTO partidas (id_usuario) VALUES (?)";
        $this->db->execute($sql, [$id_usuario], "i");
        return $this->db->getLastInsertId();
    }

    public function actualizarFechaPartidaFinalizada(int $id_partida): void
    {
        $sql = "UPDATE partidas SET fecha_fin = NOW() WHERE id_partida = ?";
        $this->db->execute($sql, [$id_partida], "i");
    }

    public function getCantidadPreguntasCorrectas(int $id_partida)
    {
        $sql = "SELECT correctas FROM partidas WHERE id_partida = ?";
        $resultado = $this->db->query($sql, [$id_partida], "i");
        return $resultado[0]['correctas'];
    }

    public function incrementarPuntaje(int $id_partida, int $puntos): void
    {
        $sql = "UPDATE partidas SET puntaje_final = puntaje_final + ?  WHERE id_partida = ? ";
        $this->db->execute($sql, [$puntos, $id_partida], "ii");
    }

    public function incrementarPreguntaRespondidaCorrectamente(int $id_partida): void
    {
        $sql = "UPDATE partidas SET correctas = correctas + 1 WHERE id_partida = ?";
        $this->db->execute($sql, [$id_partida], "i");
    }

    public function registrarPreguntaRespondida($id_partida, $id_pregunta, $id_respuesta, $acerto): void
    {

        $id_preg = (int)$id_pregunta;
        $id_par = (int)$id_partida;

        // Si ya existe, no la inserto
        if ($this->partidaPreguntaYaRegistrada($id_par, $id_preg)) {
            return;
        }

        $id_resp = is_null($id_respuesta) ? "NULL" : (int)$id_respuesta;
        $acerto = (int)$acerto;

        $sql = "INSERT INTO partida_pregunta (id_partida, id_pregunta, id_respuesta_elegida, acerto)
                VALUES($id_par, $id_preg, $id_resp, $acerto)";

        $this->db->execute($sql);
    }

    private function partidaPreguntaYaRegistrada($id_partida, $id_pregunta): bool
    {
        $sql = "
            SELECT 1
            FROM partida_pregunta
            WHERE id_partida = $id_partida AND id_pregunta = $id_pregunta
            LIMIT 1
        ";

        $res = $this->db->query($sql);

        return !empty($res);
    }

    public function calcularPuntaje(string $dificultad, int $tiempoRestante): int
    {
        $base = match ($dificultad) {
            'facil' => 3,
            'dificil' => 7,
            default => 5
        };

        $bonusTiempo = intdiv($tiempoRestante, 2);

        return $base + $bonusTiempo;
    }

    public function getPuntajeFinalPartida(int $idPartida): int
    {
        $query = "SELECT puntaje_final FROM partidas WHERE id_partida = ? LIMIT 1";
        $resultado = $this->db->query($query, [$idPartida], "i");

        return $resultado[0]['puntaje_final'] ?? 0;
    }
}
