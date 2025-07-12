<?php

use JetBrains\PhpStorm\NoReturn;

class PerfilController
{
    private $view;
    private $usuarioModel;

    public function __construct($view, $usuarioModel)
    {
        $this->view = $view;
        $this->usuarioModel = $usuarioModel;
    }

    public function show(): void
    {
        $id_usuario = $_GET['idUsuario'] ?? ($_SESSION['usuario_id'] ?? null);

        if (!$id_usuario) {
            $this->redirectTo("/error");
        }

        $datos = $this->usuarioModel->getDatosPerfil($id_usuario);

        if (empty($datos)) {
            $_SESSION['error_message'] = "Usuario no encontrado";
            $this->redirectTo("/home/error");
        }

        if ($datos[0]['nombre_rol'] === 'admin' || $datos[0]['nombre_rol'] === 'editor') {
            $this->redirectTo("/home/error");
        }

        $usuario = $datos[0];

        $cantidadPartidas = $this->usuarioModel->getCantidadPartidasJugadas($id_usuario);
        $tieneEstadisticas = $cantidadPartidas !== "0";

        $totalPreguntas = $this->usuarioModel->getTotalPreguntasRespondidas($id_usuario);
        $porcentajeAcierto = $this->usuarioModel->getPorcentajeAcierto($id_usuario);
        $mayorPuntaje = $this->usuarioModel->getMayorPuntajePartida($id_usuario);
        $categoriasDestacadas = $this->usuarioModel->getCategoriasDestacadas($id_usuario);
        $posicionRanking = $this->usuarioModel->getPosicionRanking($id_usuario);

        // Construir la URL del perfil
        $host = $_SERVER['HTTP_HOST'];
        $es_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $protocolo = $es_https ? 'https' : 'http';
        $url_perfil = "$protocolo://$host/perfil?idUsuario=$id_usuario";

        // Renderizar vista
        $this->view->render("perfil", array_merge(
            [
                'title' => 'Perfil Usuario',
                'url_perfil' => $url_perfil,
                'cantidad_partidas' => $cantidadPartidas,
                'total_preguntas' => $totalPreguntas,
                'porcentaje_acierto' => $porcentajeAcierto,
                'mayor_puntaje' => $mayorPuntaje,
                'categorias_destacadas' => $categoriasDestacadas,
                'posicion_ranking' => $posicionRanking,
                'tiene_posicion' => $posicionRanking !== null,
                'tiene_estadisticas' => $tieneEstadisticas
            ],
            $usuario
        ));
    }

    #[NoReturn] private function redirectTo($str): void
    {
        header('Location: ' . $str);
        exit();
    }

}
