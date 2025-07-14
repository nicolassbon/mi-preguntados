<?php

class PartidaModel
{

    private Database $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function crearPartida($id_usuario)
    {

        $id_usuario = (int)$id_usuario;
        $sql = "INSERT INTO partidas (id_usuario) VALUES ($id_usuario)";
        $this->db->execute($sql);
        return $this->db->getLastInsertId();

    }

    public function getTiempoRestante()
    {
        $inicio = $_SESSION['inicio_pregunta'] ?? null;
        if (!$inicio) {
            return 0;
        }

        $ahora = time();
        $tiempo_total = 10;
        $tiempo_pasado = $ahora - $inicio;
        return max(0, $tiempo_total - $tiempo_pasado);
    }

    public function actualizarFechaPartidaFinalizada($id_partida): void
    {

        $sql = "UPDATE partidas SET fecha_fin = NOW() WHERE id_partida = $id_partida ";
        $this->db->execute($sql);

    }

    public function getCantidadPreguntasCorrectas($id_partida)
    {
        $sql = "SELECT correctas FROM partidas WHERE id_partida = $id_partida";
        $resultado = $this->db->query($sql);
        return $resultado[0]['correctas'];
    }

    public function incrementarPuntaje($id_partida, $puntos): void
    {
        $sql = "UPDATE partidas SET puntaje_final = puntaje_final + $puntos  WHERE id_partida = $id_partida ";
        $this->db->execute($sql);
    }

    public function incrementarPreguntaRespondidaCorrectamente($id_partida): void
    {

        $sql = "UPDATE partidas SET correctas = correctas + 1 WHERE id_partida = $id_partida";
        $this->db->execute($sql);
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
}
