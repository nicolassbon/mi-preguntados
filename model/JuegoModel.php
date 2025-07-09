<?php

class JuegoModel
{
    private $db;
    private $preguntaModel;

    public function __construct($db, PreguntaModel $preguntaModel)
    {
        $this->db = $db;
        $this->preguntaModel = $preguntaModel;
    }

    /*
    * 1. Dificultad adecuada según su nivel (ratio: correctas / entregadas)
    * 2. Haya sido entregada al menos 5 veces
    * 3. El usuario no la vio (usuario_pregunta)
    * 4. Sea de esa categoria
    */
    public function obtenerPregunta($id_usuario, $id_categoria)
    {
        // estadisticas del usuario
        $estadisticas = $this->getEstadisticasUsuario($id_usuario);

        // Nivel del usuario
        $nivelUsuario = $this->getNivelUsuario(
            $estadisticas['entregadas'],
            $estadisticas['acertadas']
        );

        // Preguntas no vistas y con estado 'activa'
        // Con estado 'reportada' trae las que no fueron reportadas por ese usuario
        $preguntas = $this->getPreguntasNoVistas($id_usuario, $id_categoria);

        // si se acabaron → limpio historial y recursión
        if (empty($preguntas)) {
            // Solo limpia las de esa categoria
            $this->limpiarHistorialPreguntasVistas($id_usuario, $id_categoria);
            return $this->obtenerPregunta($id_usuario, $id_categoria);
        }

        // agrupo y selecciono según nivel
        $grupos = $this->agruparPorNivel($preguntas);

        $preg = $this->elegirPorNivelUsuario($grupos, $nivelUsuario);
        return $preg;
    }

    private function seDebeCalcularNivelUsuario($entregadas)
    {
        return $entregadas >= 5;
    }

    private function seDebeCalcularNivelPregunta($pregunta)
    {
        return $pregunta["entregadas"] >= 5;
    }

    private function getEstadisticasUsuario($id_usuario): array
    {
        $result = $this->db->query("
            SELECT preguntas_entregadas, preguntas_acertadas
            FROM usuarios
            WHERE id_usuario = $id_usuario
        ");

        return [
            "entregadas" => $result[0]["preguntas_entregadas"],
            "acertadas" => $result[0]["preguntas_acertadas"]
        ] ?? ['entregadas' => 0, 'acertadas' => 0];
    }

    private function getNivelUsuario($entregadas, $acertadas): string
    {
        if (!$this->seDebeCalcularNivelUsuario($entregadas)) {
            return "intermedio";
        }

        $ratio = $acertadas / $entregadas;
        if ($ratio > 0.7) {
            return 'facil';
        }
        if ($ratio < 0.3) {
            return 'dificil';
        }
        return 'intermedio';
    }

    private function getDificultadPregunta($pregunta): string
    {
        if (!$this->seDebeCalcularNivelPregunta($pregunta)) {
            return 'intermedio';
        }

        $ratio = $pregunta['correctas'] / $pregunta['entregadas'];
        if ($ratio > 0.7) {
            return 'facil';
        }
        if ($ratio < 0.3) {
            return 'dificil';
        }
        return 'intermedio';
    }

    // Trae preguntas que el usuario no vio, de la categoría dada,
    // con estado activa/reportada (pero no las que él mismo reportó),
    private function getPreguntasNoVistas($id_usuario, $id_categoria): array
    {

        $sql = "
        SELECT p.*
          FROM preguntas p

          LEFT JOIN usuario_pregunta up
            ON p.id_pregunta = up.idPregunta
           AND up.idUsuario   = $id_usuario

          LEFT JOIN preguntas_reportadas pr
            ON p.id_pregunta      = pr.id_pregunta
           AND pr.id_reportador   = $id_usuario
           
        WHERE 
            up.idPregunta IS NULL                           
            AND pr.id_reporte IS NULL                       
            AND p.id_categoria  = $id_categoria                         
            AND p.estado IN ('activa','reportada')                               
    ";

        return $this->db->query($sql);
    }

    private function limpiarHistorialPreguntasVistas($id_usuario, $id_categoria)
    {
        $this->db->execute("
        DELETE up
          FROM usuario_pregunta up
          JOIN preguntas p ON up.idPregunta = p.id_pregunta
         WHERE up.idUsuario = $id_usuario
           AND p.id_categoria = $id_categoria
    ");
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

        // Intento desde el mismo nivel hacia niveles más difíciles
        for ($i = $idx, $iMax = count($order); $i < $iMax; $i++) {
            if (!empty($grupos[$order[$i]])) {
                return $grupos[$order[$i]][array_rand($grupos[$order[$i]])];
            }
        }
        // Si aún vacío, busco niveles más fáciles
        for ($i = $idx - 1; $i >= 0; $i--) {
            if (!empty($grupos[$order[$i]])) {
                return $grupos[$order[$i]][array_rand($grupos[$order[$i]])];
            }
        }

        // No debería llegar aquí
        return [];
    }

    public function marcarPreguntaComoVista(int $id_usuario, int $id_pregunta)
    {
        $sql = "INSERT INTO usuario_pregunta (idUsuario, idPregunta, fechaVisto) VALUES (?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $id_usuario, $id_pregunta);
        $stmt->execute();
    }
}