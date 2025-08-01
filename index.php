<?php

require_once 'vendor/autoload.php';

use App\Configuration;

session_start();

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Rutas accesibles sin estar logueado
$rutasPublicas = [
    'login/',
    'login/procesar',
    'login/logout',
    'registro/',
    'registro/pasoMapa',
    'registro/mapa',
    'registro/getUbicacion',
    'registro/procesar',
    'registro/success',
    'registro/verificar',
    'registro/checkUsuario',
    'registro/checkEmail',
    'perfil/show',
    'ranking/show',
    'trampitas/recibirNotificacion'
];

$controller = $_GET['controller'] ?? null;
$method = $_GET['method'] ?? null;

$ruta = "$controller/$method";

if (!isset($_SESSION['usuario_id']) && !in_array($ruta, $rutasPublicas, true)) {
    header("Location: /login");
    exit();
}

if (isset($_SESSION['usuario_id'])) {
    $rolUsuario = $_SESSION['rol_usuario'] ?? null;


    if ($rolUsuario !== 'editor' && str_starts_with($ruta, 'editor/')) {
        session_unset();
        session_destroy();
        header("Location: /login?error=trampa");
        exit();
    }

    if ($rolUsuario !== 'admin' && str_starts_with($ruta, 'admin/')) {
        session_unset();
        session_destroy();
        header("Location: /login?error=trampa");
        exit();
    }
}

$configuration = new Configuration();
$router = $configuration->getRouter();

$router->go($controller, $method);
