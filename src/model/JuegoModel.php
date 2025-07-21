<?php

namespace App\model;

use App\core\Database;

class JuegoModel
{
    private Database $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function obtenerPregunta($id_usuario, $id_categoria): array
    {
        $estadisticas = $this->getEstadisticasUsuario($id_usuario);

        $nivelUsuario = $this->getNivelUsuario(
            $estadisticas['entregadas'],
            $estadisticas['acertadas']
        );

        $preguntas = $this->getPreguntasNoVistas($id_usuario, $id_categoria);

        if (empty($preguntas)) {
            $this->limpiarHistorialPreguntasVistas($id_usuario, $id_categoria);
            return $this->obtenerPregunta($id_usuario, $id_categoria);
        }

        $grupos = $this->agruparPorNivel($preguntas);

        return $this->elegirPorNivelUsuario($grupos, $nivelUsuario);
    }

    private function seDebeCalcularNivelUsuario($entregadas): bool
    {
        return $entregadas >= 5;
    }

    private function seDebeCalcularNivelPregunta($pregunta): bool
    {
        return $pregunta["entregadas"] >= 5;
    }

    private function getEstadisticasUsuario(int $id_usuario): array
    {
        $sql = "
            SELECT preguntas_entregadas, preguntas_acertadas
            FROM usuarios
            WHERE id_usuario = ?
        ";
        $result = $this->db->query($sql, [$id_usuario], "i");

        return [
            "entregadas" => $result[0]["preguntas_entregadas"],
            "acertadas" => $result[0]["preguntas_acertadas"]
        ] ?? ['entregadas' => 0, 'acertadas' => 0];
    }

    private function getNivelUsuario($entregadas, $acertadas): string
    {
        $nivel = 'intermedio';
        if (!$this->seDebeCalcularNivelUsuario($entregadas)) {
            return $nivel;
        }

        $ratio = $acertadas / $entregadas;
        if ($ratio > 0.7) {
            $nivel = 'facil';
        }
        if ($ratio < 0.3) {
            $nivel = 'dificil';
        }
        return $nivel;
    }

    public function getDificultadPregunta($pregunta): string
    {
        $dificultad = 'intermedio';
        if (!$this->seDebeCalcularNivelPregunta($pregunta)) {
            return $dificultad;
        }

        $ratio = $pregunta['correctas'] / $pregunta['entregadas'];
        if ($ratio > 0.7) {
            $dificultad = 'facil';
        }
        if ($ratio < 0.3) {
            $dificultad = 'dificil';
        }
        return $dificultad;
    }

    private function getPreguntasNoVistas(int $id_usuario, int $id_categoria): array
    {

        $sql = "
            SELECT p.*
            FROM preguntas p
            LEFT JOIN usuario_pregunta up ON p.id_pregunta = up.idPregunta AND up.idUsuario = ?
            LEFT JOIN preguntas_reportadas pr ON p.id_pregunta = pr.id_pregunta AND pr.id_reportador = ?
            WHERE up.idPregunta IS NULL AND pr.id_reporte IS NULL
            AND p.id_categoria  = ? AND p.estado IN ('activa','reportada')
        ";

        return $this->db->query($sql, [$id_usuario, $id_usuario, $id_categoria], "iii");
    }

    private function limpiarHistorialPreguntasVistas(int $id_usuario, int $id_categoria): void
    {
        $sql = "
            DELETE up
            FROM usuario_pregunta up
            JOIN preguntas p ON up.idPregunta = p.id_pregunta
            WHERE up.idUsuario = ? AND p.id_categoria = ?
        ";
        $this->db->execute($sql, [$id_usuario, $id_categoria], "ii");
    }

    private function agruparPorNivel($preguntas): array
    {
        $grupos = [
            'facil' => [],
            'intermedio' => [],
            'dificil' => [],
        ];
        foreach ($preguntas as $p) {
            $dificultad = $this->getDificultadPregunta($p);
            $grupos[$dificultad][] = $p;
        }
        return $grupos;
    }

    private function elegirPorNivelUsuario($grupos, $nivelUsuario): array
    {
        $order = ['facil', 'intermedio', 'dificil'];
        $idx = array_search($nivelUsuario, $order, true);

        for ($i = $idx, $iMax = count($order); $i < $iMax; $i++) {
            if (!empty($grupos[$order[$i]])) {
                return $grupos[$order[$i]][array_rand($grupos[$order[$i]])];
            }
        }

        for ($i = $idx - 1; $i >= 0; $i--) {
            if (!empty($grupos[$order[$i]])) {
                return $grupos[$order[$i]][array_rand($grupos[$order[$i]])];
            }
        }

        return [];
    }

    public function marcarPreguntaComoVista(int $id_usuario, int $id_pregunta): void
    {
        $sql = "INSERT INTO usuario_pregunta (idUsuario, idPregunta, fechaVisto) VALUES (?, ?, NOW())";
        $this->db->execute($sql, [$id_usuario, $id_pregunta], "ii");
    }
}
