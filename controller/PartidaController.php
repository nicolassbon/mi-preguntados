<?php

class PartidaController
{
    private $view;
    private $partidaModel;
    private $preguntaModel;
    private $usuarioModel;
    private $juegoModel;

    public function __construct($view, $partidaModel, $preguntaModel, $usuarioModel, $juegoModel)
    {
        $this->view = $view;
        $this->partidaModel = $partidaModel;
        $this->preguntaModel = $preguntaModel;
        $this->usuarioModel = $usuarioModel;
        $this->juegoModel = $juegoModel;
    }

    public function crearPartida()
    {
        $id_usuario = $_SESSION['usuario_id'] ?? null;

        if (isset($_SESSION['id_partida'])) {
            header('Location: /ruleta/show');
            exit();
        }

        $_SESSION['puntaje'] = 0;
        $id_partida = $this->partidaModel->crearPartida($id_usuario);
        $_SESSION['id_partida'] = $id_partida;

        header('Location: /ruleta');
        exit();
    }

    public function jugar()
    {

        $id_usuario = $_SESSION['usuario_id'] ?? null;
        $categoria = $_SESSION['categoria'] ?? null;

        if (isset($_SESSION['pregunta'])) {
            $id_pregunta = $_SESSION['id_pregunta'];
            $pregunta_texto = $_SESSION['pregunta'];
            $respuestas = $this->preguntaModel->getRespuestasPorPregunta($id_pregunta);
            $user = $_SESSION["nombre_usuario"];
            $tiempo_restante = $this->partidaModel->getTiempoRestante();

            $this->view->render("partida", [
                'title' => 'Partida',
                'usuario_id' => $id_usuario,
                'pregunta' => $pregunta_texto,
                'categoria' => $categoria['nombre'],
                'respuestas' => $respuestas,
                'id_partida' => $_SESSION['id_partida'],
                'tiempo_restante' => $tiempo_restante,
                'fondo' => $categoria['color'],
                'foto' => $categoria['foto_categoria'],
                'user' => $user
            ]);
            return;
        }

        $pregunta = $this->juegoModel->obtenerPregunta($id_usuario, $categoria["id_categoria"]);
        $id_pregunta = $pregunta["id_pregunta"];
        $pregunta_texto = $pregunta["pregunta"];

        $respuestas = $this->preguntaModel->getRespuestasPorPregunta($id_pregunta);

        $_SESSION['id_pregunta'] = $id_pregunta;
        $_SESSION['pregunta'] = $pregunta_texto;
        $_SESSION['opciones'] = $respuestas;
        $_SESSION['inicio_pregunta'] = time();

        $user = $_SESSION["nombre_usuario"];

        $tiempo_restante = $this->partidaModel->getTiempoRestante();

        $this->preguntaModel->incrementarEntregadasPregunta($id_pregunta);
        $this->juegoModel->marcarPreguntaComoVista($id_usuario, $id_pregunta);
        $this->usuarioModel->incrementarEntregadasUsuario($id_usuario);

        $this->view->render("partida", [
            'title' => 'Partida',
            'usuario_id' => $id_usuario,
            'pregunta' => $pregunta_texto,
            'id_pregunta' => $id_pregunta,
            'categoria' => $categoria['nombre'],
            'respuestas' => $respuestas,
            'id_partida' => $_SESSION['id_partida'],
            'tiempo_restante' => $tiempo_restante,
            'fondo' => $categoria['color'],
            'foto' => $categoria['foto_categoria'],
            'user' => $user
        ]);

    }

    public function responder()
    {
        $this->detectarTrampaYExpulsar();

        $id_usuario = $_SESSION['usuario_id'] ?? null;
        $id_pregunta = $_SESSION['id_pregunta'];
        $id_partida = $_SESSION['id_partida'];

        $inicio = $_SESSION['inicio_pregunta'] ?? null;
        $tiempo_agotado = $inicio !== null && (time() - $inicio) > 10;

        $respuestaCorrecta = false;
        $texto = $tiempo_agotado ? '¡TIEMPO AGOTADO!' : '¡INCORRECTA!';
        $color = $tiempo_agotado ? 'text-warning' : 'text-danger';
        $id_respuesta = $_POST['id_respuesta'] ?? null;

        if ($tiempo_agotado || $id_respuesta === -1) {
            $this->partidaModel->registrarPreguntaRespondida($id_partida, $id_pregunta, null, 0);
            $this->finalizarPartida();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_respuesta'])) {
            $respuestas = $this->preguntaModel->getRespuestasPorPregunta($id_pregunta);

            foreach ($respuestas as $respuesta) {
                if ($respuesta['esCorrecta']) {
                    if ($respuesta['id_respuesta'] == $_POST['id_respuesta']) {
                        $respuestaCorrecta = true;
                        $texto = "¡CORRECTA!";
                        $color = 'text-success';

                        $_SESSION['puntaje'] += 5;
                        $this->partidaModel->incrementarPuntaje($id_partida);
                        $this->partidaModel->incrementarPreguntaRespondidaCorrectamente($id_partida);
                        $this->partidaModel->registrarPreguntaRespondida($id_partida, $id_pregunta, $respuesta['id_respuesta'], 1);
                        $this->preguntaModel->incrementarCorrectasPregunta($id_pregunta);
                        $this->usuarioModel->sumarPuntajeUsuario($id_usuario);
                        $this->usuarioModel->incrementarCorrectasUsuario($id_usuario);
                        break;
                    }
                }
            }

            if (!$respuestaCorrecta) {
                $this->partidaModel->registrarPreguntaRespondida($id_partida, $id_pregunta, $_POST['id_respuesta'], 0);
                $this->finalizarPartida();
            }
        } else {
            echo 'error';
        }

        $_SESSION['cantidad'] = (int)$this->partidaModel->getCantidadPreguntasCorrectas($id_partida);
        $this->mostrarVistaRespuesta($id_usuario, $id_pregunta, $respuestaCorrecta, $texto, $color, false);
    }

    public function perdio()
    {
        $this->view->render("perdio", [
            'title' => 'Partida Perdida',
            'puntaje' => $_SESSION['puntaje'],
            'cantidad' => $_SESSION['cantidad'] ?? 0
        ]);

        unset($_SESSION['puntaje'], $_SESSION['cantidad']);
    }

    public function reportarPregunta()
    {
        $idPregunta = (int)$_POST['id_pregunta'];
        $idUsuario = $_SESSION['usuario_id'] ?? null;
        $idPartida = $_SESSION['id_partida'] ?? null;
        $motivo = trim($_POST['motivo'] ?? '') ?: 'Sin motivo especificado';

        $this->preguntaModel->insertarReportePregunta($idPregunta, $idUsuario, $motivo);
        $this->preguntaModel->actualizarEstadoPregunta($idPregunta, 'reportada');

        if ($idPartida !== null) {
            $this->finalizarPartida();
        }

        $this->view->render("reporteCreado", [
            'title' => 'Reporte enviado',
            'mensaje' => 'Gracias por reportar la pregunta. Será revisada por un editor.',
            'puntaje' => $_SESSION['puntaje'] ?? 0,
            'cantidad' => $_SESSION['cantidad'] ?? 0
        ]);
    }

    private function mostrarVistaRespuesta($id_usuario, $id_pregunta, $respuestaCorrecta, $texto, $color, $reportado)
    {
        $respuestas = $this->preguntaModel->getRespuestasPorPregunta($id_pregunta);

        foreach ($respuestas as &$respuesta) {
            $respuesta['id'] = $respuesta['id_respuesta'];
            $respuesta['texto_respuesta'] = $respuesta['respuesta'];

            if ($respuesta['esCorrecta']) {
                $respuesta['clase'] = 'bg-success';
            } else {
                $respuesta['clase'] = ($respuesta['id_respuesta'] == ($_POST['id_respuesta'] ?? null))
                    ? 'bg-danger'
                    : 'bg-light';
            }

            $respuesta['disabled'] = true;
        }

        $categoria = $_SESSION['categoria'] ?? null;
        $user = $_SESSION["nombre_usuario"];

        $this->view->render("partida", [
            'title' => 'Partida',
            'usuario_id' => $id_usuario,
            'pregunta' => $_SESSION['pregunta'],
            'respuestas' => $respuestas,
            'categoria' => $categoria['nombre'],
            'correcto' => $respuestaCorrecta,
            'respondido' => true,
            'texto' => $texto,
            'color' => $color,
            'puntaje' => $_SESSION['puntaje'],
            'ocultar' => 'display:none',
            'fondo' => $categoria['color'],
            'foto' => $categoria['foto_categoria'],
            'user' => $user,
            'reportado' => $reportado,
            'id_pregunta' => $id_pregunta
        ]);

        $this->limpiarSesionPregunta();
    }

    private function limpiarSesionPregunta()
    {
        unset(
            $_SESSION['categoria'],
            $_SESSION['id_pregunta'],
            $_SESSION['pregunta'],
            $_SESSION['inicio_pregunta']
        );
    }

    private function detectarTrampaYExpulsar()
    {
        if (!isset($_SESSION['id_pregunta'])) {
            if (!empty($_SESSION['id_partida'])) {
                $this->partidaModel->actualizarFechaPartidaFinalizada($_SESSION['id_partida']);
            }
            session_destroy();
            header("Location: /login?error=trampa");
            exit;
        }
    }

    private function finalizarPartida()
    {
        if (isset($_SESSION['id_partida'])) {
            $this->partidaModel->actualizarFechaPartidaFinalizada($_SESSION['id_partida']);
            unset($_SESSION['id_partida']);
        }
    }
}