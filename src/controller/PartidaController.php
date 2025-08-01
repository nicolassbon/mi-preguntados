<?php

namespace App\controller;

use App\core\MustachePresenter;
use App\model\JuegoModel;
use App\model\PartidaModel;
use App\model\PreguntaModel;
use App\model\UsuarioModel;
use JetBrains\PhpStorm\NoReturn;

class PartidaController
{
    private MustachePresenter $view;
    private PartidaModel $partidaModel;
    private PreguntaModel $preguntaModel;
    private UsuarioModel $usuarioModel;
    private JuegoModel $juegoModel;

    public function __construct($view, $partidaModel, $preguntaModel, $usuarioModel, $juegoModel)
    {
        $this->view = $view;
        $this->partidaModel = $partidaModel;
        $this->preguntaModel = $preguntaModel;
        $this->usuarioModel = $usuarioModel;
        $this->juegoModel = $juegoModel;
    }

    #[NoReturn] public function crearPartida(): void
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

    public function jugar(): void
    {

        $id_usuario = $_SESSION['usuario_id'] ?? null;
        $categoria = $_SESSION['categoria'] ?? null;

        if (isset($_SESSION['pregunta'])) {
            $id_pregunta = $_SESSION['id_pregunta'];
            $pregunta_texto = $_SESSION['pregunta'];
            $respuestas = $this->preguntaModel->getRespuestasPorPregunta($id_pregunta);
            $user = $_SESSION["nombre_usuario"];
            $trampitas = $this->usuarioModel->getTrampitas($_SESSION['usuario_id']);

            $this->view->render("partida", [
                'title' => 'Partida',
                'usuario_id' => $id_usuario,
                'pregunta' => $pregunta_texto,
                'categoria' => $categoria['nombre'],
                'respuestas' => $respuestas,
                'id_partida' => $_SESSION['id_partida'],
                'inicio_pregunta' => $_SESSION['inicio_pregunta'],
                'tiempo_maximo' => 10,
                'fondo' => $categoria['color'],
                'foto' => $categoria['foto_categoria'],
                'user' => $user,
                'trampitas' => $trampitas,
                'puede_usar_trampita' => $trampitas > 0,
                'respondido' => false
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

        $this->preguntaModel->incrementarEntregadasPregunta($id_pregunta);
        $this->juegoModel->marcarPreguntaComoVista($id_usuario, $id_pregunta);
        $this->usuarioModel->incrementarEntregadasUsuario($id_usuario);

        $trampitas = $this->usuarioModel->getTrampitas($_SESSION['usuario_id']);

        $this->view->render("partida", [
            'title' => 'Partida',
            'usuario_id' => $id_usuario,
            'pregunta' => $pregunta_texto,
            'id_pregunta' => $id_pregunta,
            'categoria' => $categoria['nombre'],
            'respuestas' => $respuestas,
            'id_partida' => $_SESSION['id_partida'],
            'inicio_pregunta' => $_SESSION['inicio_pregunta'],
            'tiempo_maximo' => 10,
            'fondo' => $categoria['color'],
            'foto' => $categoria['foto_categoria'],
            'user' => $user,
            'trampitas' => $trampitas,
            'puede_usar_trampita' => $trampitas > 0,
            'respondido' => false,
            'reset_timer' => true
        ]);

    }

    public function responder(): void
    {
        $this->detectarTrampaYExpulsar();

        $id_usuario = $_SESSION['usuario_id'] ?? null;
        $id_pregunta = $_SESSION['id_pregunta'];
        $id_partida = $_SESSION['id_partida'];
        $id_respuesta = isset($_POST['id_respuesta']) ? (int)$_POST['id_respuesta'] : null;


        if ($id_respuesta === -1) {
            $texto = '¡TIEMPO AGOTADO!';
            $color = 'text-warning';
            $respuestaCorrecta = false;

            $this->procesarTiempoAgotado($id_partida, $id_pregunta);
            $this->finalizarPartida();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_respuesta'])) {
            $texto = '¡INCORRECTA!';
            $color = 'text-danger';
            $respuestaCorrecta = $this->procesarRespuesta((int)$id_respuesta, $id_pregunta, $id_partida, $id_usuario, $texto, $color);
        } else {
            echo "Error: solicitud inválida.";
            return;
        }

        $_SESSION['cantidad'] = (int)$this->partidaModel->getCantidadPreguntasCorrectas($id_partida);
        $this->mostrarVistaRespuesta($id_usuario, $id_pregunta, $respuestaCorrecta, $texto, $color);
    }

    public function usarTrampita(): void
    {
        $this->detectarTrampaYExpulsar();

        $id_usuario = $_SESSION['usuario_id'];
        $id_partida = $_SESSION['id_partida'];
        $id_pregunta = $_SESSION['id_pregunta'];

        $trampitas = $this->usuarioModel->getTrampitas($id_usuario);
        if ($trampitas <= 0) {
            session_destroy();
            header("Location: /login?error=trampa");
            exit;
        }

        $respuestas = $this->preguntaModel->getRespuestasPorPregunta($id_pregunta);
        $respuestaCorrecta = null;
        foreach ($respuestas as $respuesta) {
            if ($respuesta['esCorrecta']) {
                $respuestaCorrecta = $respuesta;
                break;
            }
        }

        $this->procesarCorrecta($respuestaCorrecta, $id_pregunta, $id_partida, $id_usuario, true);
        $this->usuarioModel->usarTrampita($id_usuario);
        $_SESSION['cantidad'] = (int)$this->partidaModel->getCantidadPreguntasCorrectas($id_partida);
        $this->mostrarVistaRespuesta($id_usuario, $id_pregunta, true, "¡USASTE UNA TRAMPITA!", "text-warning");
    }

    private function procesarTiempoAgotado($id_partida, $id_pregunta): void
    {
        $this->partidaModel->registrarPreguntaRespondida($id_partida, $id_pregunta, null, 0);
    }

    private function procesarRespuesta(int $id_respuesta, int $id_pregunta, int $id_partida, int $id_usuario, string &$texto, string &$color): bool
    {
        $respuestas = $this->preguntaModel->getRespuestasPorPregunta($id_pregunta);

        foreach ($respuestas as $respuesta) {
            $respuesta_id = (int)$respuesta['id_respuesta'];

            if ($respuesta['esCorrecta'] && $respuesta_id === $id_respuesta) {
                $this->procesarCorrecta($respuesta, $id_pregunta, $id_partida, $id_usuario);
                $texto = "¡CORRECTA!";
                $color = 'text-success';
                return true;
            }
        }

        $this->procesarIncorrecta($id_partida, $id_pregunta, $id_respuesta);
        return false;
    }

    private function procesarCorrecta(array $respuesta, int $id_pregunta, int $id_partida, int $id_usuario, bool $es_trampita = false): void
    {
        $this->partidaModel->incrementarPreguntaRespondidaCorrectamente($id_partida);
        $this->partidaModel->registrarPreguntaRespondida($id_partida, $id_pregunta, $respuesta['id_respuesta'], 1);
        $this->preguntaModel->incrementarCorrectasPregunta($id_pregunta);
        $this->usuarioModel->incrementarCorrectasUsuario($id_usuario);
        $this->sumarPuntaje($id_pregunta, $id_partida, $id_usuario, $es_trampita);
    }

    private function procesarIncorrecta(int $id_partida, int $id_pregunta, int $id_respuesta): void
    {
        $id_respuesta_incorrecta = ($id_respuesta === -1) ? null : $id_respuesta;

        $this->partidaModel->registrarPreguntaRespondida($id_partida, $id_pregunta, $id_respuesta_incorrecta, 0);
        $this->finalizarPartida();
    }

    private function sumarPuntaje(int $id_pregunta, int $id_partida, int $id_usuario, bool $es_trampita): void
    {
        if (!$es_trampita) {
            $pregunta = $this->preguntaModel->getPreguntaPorId($id_pregunta);
            $dificultad = $this->juegoModel->getDificultadPregunta($pregunta);

            $inicio = $_SESSION['inicio_pregunta'] ?? time();
            $tiempo_total = 10;
            $tiempo_transcurrido = time() - $inicio;
            $tiempo_restante = max(0, $tiempo_total - $tiempo_transcurrido);

            $puntos = $this->partidaModel->calcularPuntaje($dificultad, $tiempo_restante);
            $_SESSION['puntaje'] += $puntos;

            $this->partidaModel->incrementarPuntaje($id_partida, $puntos);
            $this->usuarioModel->sumarPuntajeUsuario($id_usuario, $puntos);
        } else {
            $_SESSION['puntaje'] += 3;
            $this->partidaModel->incrementarPuntaje($id_partida, 3);
            $this->usuarioModel->sumarPuntajeUsuario($id_usuario, 3);
        }

    }

    public function perdio(): void
    {
        $trampitas = $this->usuarioModel->getTrampitas($_SESSION['usuario_id']);
        $this->view->render("perdio", [
            'title' => 'Partida Perdida',
            'puntaje' => $_SESSION['puntaje'],
            'cantidad' => $_SESSION['cantidad'],
            'trampitas' => $trampitas,
        ]);
        unset($_SESSION['puntaje'], $_SESSION['cantidad']);
    }

    public function reportarPregunta(): void
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
            'puntaje' => $_SESSION['puntaje'],
            'cantidad' => $_SESSION['cantidad']
        ]);
    }

    private function mostrarVistaRespuesta($id_usuario, $id_pregunta, $respuestaCorrecta, $texto, $color): void
    {
        $respuestas = $this->preguntaModel->getRespuestasPorPregunta($id_pregunta);

        foreach ($respuestas as &$respuesta) {
            $respuesta_id = (int)$respuesta['id_respuesta'];
            $post_id = isset($_POST['id_respuesta']) ? (int)$_POST['id_respuesta'] : null;

            if ($respuesta['esCorrecta']) {
                $respuesta['clase'] = 'bg-success';
            } else {
                $respuesta['clase'] = ($respuesta_id === $post_id)
                    ? 'bg-danger'
                    : 'bg-light';
            }
            $respuesta['disabled'] = true;
        }
        unset($respuesta);

        $categoria = $_SESSION['categoria'] ?? null;
        $user = $_SESSION["nombre_usuario"];
        $trampitas = $this->usuarioModel->getTrampitas($id_usuario);

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
            'id_pregunta' => $id_pregunta,
            'trampitas' => $trampitas,
            'puede_usar_trampita' => false
        ]);

        $this->limpiarSesionPregunta();
    }

    private function limpiarSesionPregunta(): void
    {
        unset(
            $_SESSION['categoria'],
            $_SESSION['id_pregunta'],
            $_SESSION['pregunta'],
            $_SESSION['inicio_pregunta']
        );
    }

    private function detectarTrampaYExpulsar(): void
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

    private function finalizarPartida(): void
    {
        if (isset($_SESSION['id_partida'])) {
            $this->partidaModel->actualizarFechaPartidaFinalizada($_SESSION['id_partida']);
            unset($_SESSION['id_partida']);
        }
    }
}
