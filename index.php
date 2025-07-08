<?php
session_start();

// Rutas accesibles sin estar logueado
$rutasPublicas = [
    'login/show',
    'login/procesar',
    'registro/show',
    'registro/pasoMapa',
    'registro/mapa',
    'registro/getUbicacion',
    'registro/procesar',
    'registro/success',
    'registro/verificar',
    'login/logout',
    'perfil/show',
    'ranking/show',
    'login/logout',
    'login/'
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
        header("Location: /login/show?error=trampa");
        exit();
    }

    if ($rolUsuario !== 'admin' && str_starts_with($ruta, 'admin/')) {
        session_unset();
        session_destroy();
        header("Location: /login/show?error=trampa");
        exit();
    }
}

require_once "Configuration.php";
$configuration = new Configuration();
$router = $configuration->getRouter();

$router->go($controller, $method);
