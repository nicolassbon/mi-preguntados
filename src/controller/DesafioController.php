<?php

namespace App\controller;

use App\core\MustachePresenter;
use App\model\DesafioModel;
use App\model\PartidaModel;
use App\model\UsuarioModel;
use DateMalformedStringException;
use DateTime;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

class DesafioController
{
    private MustachePresenter $view;
    private DesafioModel $desafioModel;
    private UsuarioModel $usuarioModel;
    private PartidaModel $partidaModel;

    public function __construct(MustachePresenter $view, DesafioModel $desafioModel, UsuarioModel $usuarioModel, PartidaModel $partidaModel)
    {
        $this->view = $view;
        $this->desafioModel = $desafioModel;
        $this->usuarioModel = $usuarioModel;
        $this->partidaModel = $partidaModel;
    }

    public function show(): void
    {
        $idUsuario = $_SESSION['usuario_id'] ?? null;
        $usuariosDisponibles = $this->usuarioModel->getUsuariosDisponiblesParaDesafiar($idUsuario);

        $idsUsuarios = array_column($usuariosDisponibles, 'id_usuario');
        $rankings = $this->usuarioModel->obtenerRankingsPorUsuarios($idsUsuarios);

        foreach ($usuariosDisponibles as &$usuario) {
            if (isset($rankings[$usuario['id_usuario']])) {
                $usuario['posicion_ranking'] = $rankings[$usuario['id_usuario']]['posicion'];
                $usuario['puntaje_total'] = $rankings[$usuario['id_usuario']]['puntaje_acumulado'];
            }
        }
        unset($usuario);

        $this->view->render("listaJugadoresDesafio", [
            'title' => 'Desafiar Jugador',
            'usuarios' => $usuariosDisponibles
        ]);
    }

    #[NoReturn] public function crear(): void
    {
        $idDesafiante = $_SESSION['usuario_id'] ?? null;
        $idDesafiado = (int)($_POST['id_usuario_desafiado'] ?? null);

        $idPartida = $this->partidaModel->crearPartida($idDesafiante);
        $idDesafio = $this->desafioModel->crearDesafio($idDesafiante, $idDesafiado, $idPartida);

        $this->inicializarSesionDesafio($idPartida, $idDesafio, true);

        $this->redirectTo("/ruleta");
    }

    #[NoReturn] public function aceptar(): void
    {
        $idUsuario = $_SESSION['usuario_id'] ?? null;
        $idDesafio = (int)($_POST['id_desafio'] ?? null);

        $desafio = $this->desafioModel->obtenerDesafioPorId($idDesafio);

        if (!$desafio || $desafio['id_usuario_desafiado'] !== $idUsuario) {
            $this->redirectToErrorPage();
        }

        $idPartida = $this->partidaModel->crearPartida($idUsuario);
        $this->desafioModel->vincularPartidaDesafiado($idDesafio, $idPartida);

        $this->inicializarSesionDesafio($idPartida, $idDesafio, false);

        $this->redirectTo("/ruleta");
    }

    #[NoReturn] public function rechazar(): void
    {
        $idUsuario = $_SESSION['usuario_id'] ?? null;
        $idDesafio = (int)($_POST['id_desafio'] ?? null);

        $desafio = $this->desafioModel->obtenerDesafioPorId($idDesafio);

        if (!$desafio || $desafio['id_usuario_desafiado'] !== $idUsuario) {
            $this->redirectToErrorPage();
        }

        $resultado = $this->desafioModel->rechazarDesafio($idDesafio);

        if (!$resultado) {
            $this->redirectToErrorPage();
        }

        $this->redirectTo("/desafio/listarDesafios?rechazado=1");
    }

    /**
     * @throws DateMalformedStringException
     */
    public function listarDesafios(): void
    {
        $idUsuario = $_SESSION['usuario_id'] ?? null;
        $this->desafioModel->actualizarDesafiosExpirados();

        $filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : 'pendiente';

        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $desafiosPorPagina = 5;
        $offset = ($paginaActual - 1) * $desafiosPorPagina;

        $totalDesafios = $this->desafioModel->contarDesafiosPorUsuario($idUsuario, $filtro);
        $totalPaginas = ceil($totalDesafios / $desafiosPorPagina);

        // Generar array de números de página
        $numerosPagina = [];
        for ($i = 1; $i <= $totalPaginas; $i++) {
            $numerosPagina[] = [
                'numero' => $i,
                'es_actual' => $i === $paginaActual
            ];
        }

        $desafios = $this->obtenerDesafiosFiltrados($idUsuario, $filtro, $desafiosPorPagina, $offset);
        $desafios = $this->prepararDatosDesafios($desafios);

        $this->view->render("listaDesafios", [
            'title' => 'Mis Desafíos',
            'desafios' => $desafios,
            'filtro' => $filtro,
            'filtro_pendiente' => $filtro === 'pendiente',
            'filtro_rechazado' => $filtro === 'rechazado',
            'filtro_finalizado' => $filtro === 'finalizado',
            'rechazado' => isset($_GET['rechazado']),
            'error' => isset($_GET['error']),
            'paginacion' => [
                'hay_paginas' => $totalPaginas > 1,
                'total_paginas' => $totalPaginas,
                'pagina_actual' => $paginaActual,
                'pagina_anterior' => $paginaActual > 1 ? $paginaActual - 1 : null,
                'pagina_siguiente' => $paginaActual < $totalPaginas ? $paginaActual + 1 : null,
                'numeros_pagina' => $numerosPagina,
            ]
        ]);
    }

    public function buscarUsuarios(): void
    {
        $idUsuario = $_SESSION['usuario_id'] ?? null;
        $buscar = $_GET['buscar'] ?? '';
        $usuarios = $this->usuarioModel->buscarUsuariosParaDesafiar($idUsuario, trim($buscar));

        $idsUsuarios = array_column($usuarios, 'id_usuario');
        $rankings = $this->usuarioModel->obtenerRankingsPorUsuarios($idsUsuarios);

        foreach ($usuarios as &$usuario) {
            $usuario['posicion_ranking'] = $rankings[$usuario['id_usuario']]['posicion'] ?? null;
            $usuario['puntaje_total'] = $rankings[$usuario['id_usuario']]['puntaje_acumulado'] ?? null;
        }
        unset($usuario);

        $html = '';
        foreach ($usuarios as $usuario) {
            $html .= $this->view->renderPartial("usuarioCard", $usuario);
        }

        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }


    private function inicializarSesionDesafio(int $idPartida, int $idDesafio, bool $esDesafiante): void
    {
        $_SESSION['puntaje'] = 0;
        $_SESSION['id_partida'] = $idPartida;
        $_SESSION['es_desafio'] = true;
        $_SESSION['es_usuario_desafiante'] = $esDesafiante;
        $_SESSION['desafio_id'] = $idDesafio;
    }

    private function obtenerDesafiosFiltrados(int $idUsuario, string $filtro, int $desafiosPorPagina, int $offset): array
    {
        return $this->desafioModel->obtenerDesafiosPorUsuario($idUsuario, $filtro, $desafiosPorPagina, $offset);
    }

    /**
     * @throws DateMalformedStringException
     */
    private function prepararDatosDesafios(array $desafios): array
    {
        foreach ($desafios as &$desafio) {
            $this->asignarDatosRol($desafio);
            $this->asignarEstadoYFechas($desafio);
            $this->procesarDesafioFinalizado($desafio);
            $this->procesarDesafioExpirado($desafio);
        }
        unset($desafio);

        return $desafios;
    }

    private function asignarDatosRol(array &$desafio): void
    {
        $desafio['rol_desafiado'] = $desafio['rol'] === 'desafiado';
        $desafio['rol_desafiante'] = $desafio['rol'] === 'desafiante';

        $desafio['mostrar_botones'] = $desafio['estado'] === 'pendiente' && $desafio['rol'] === 'desafiado';
    }

    /**
     * @throws DateMalformedStringException
     */
    private function asignarEstadoYFechas(array &$desafio): void
    {
        $desafio['color_estado'] = match ($desafio['estado']) {
            'rechazado' => 'danger',
            'finalizado' => 'success',
            default => 'warning'
        };

        $desafio['fecha_expiracion'] = (new DateTime())->diff(new DateTime($desafio['fecha_expiracion']))->days . ' días';

        if (isset($desafio['fecha_finalizacion'])) {
            $desafio['fecha_finalizacion'] = date('Y-m-d', strtotime($desafio['fecha_finalizacion']));
        }
    }

    private function procesarDesafioFinalizado(array &$desafio): void
    {
        if ($desafio['estado'] === 'finalizado' && isset($desafio['resultado'])) {
            $desafio['gano'] = ($desafio['rol'] === 'desafiado' && $desafio['resultado'] === 'gano_desafiado') ||
                ($desafio['rol'] === 'desafiante' && $desafio['resultado'] === 'gano_desafiante');

            $desafio['perdio'] = ($desafio['rol'] === 'desafiado' && $desafio['resultado'] === 'gano_desafiante') ||
                ($desafio['rol'] === 'desafiante' && $desafio['resultado'] === 'gano_desafiado');

            $desafio['empato'] = $desafio['resultado'] === 'empate';
            $desafio['es_finalizado'] = true;
        }
    }

    private function procesarDesafioExpirado(array &$desafio): void
    {
        if ($desafio['estado'] === 'expirado') {
            $desafio['es_expirado'] = true;
        }
    }

    #[NoReturn] private function redirectTo(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    #[NoReturn] private function redirectToErrorPage(): void
    {
        header('Location: /desafio/listarDesafios?error=1');
        exit;
    }

}
