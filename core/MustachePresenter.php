<?php

class MustachePresenter
{
    private Mustache_Engine $mustache;
    private String $partialsPathLoader;

    public function __construct($partialsPathLoader)
    {
        $this->mustache = new Mustache_Engine(
            array(
                'partials_loader' => new Mustache_Loader_FilesystemLoader($partialsPathLoader)
            ));
        $this->partialsPathLoader = $partialsPathLoader;
    }

    public function render($contentFile, $data = array()): void
    {
        if (isset($_SESSION['usuario_id'])) {
            $data['user'] = $_SESSION['nombre_usuario'];
        }

        // Variables globales de rol desde sesión
        if (isset($_SESSION['rol_usuario'])) {
            $data['esEditor'] = $_SESSION['rol_usuario'] === 'editor' ?? false;
            $data['esAdmin'] = $_SESSION['rol_usuario'] === 'admin' ?? false;
            $data['esJugador'] = $_SESSION['rol_usuario'] === 'jugador' ?? false;
        }

        echo $this->generateHtml($this->partialsPathLoader . '/' . $contentFile . "View.mustache", $data);
    }

    public function generateHtml($contentFile, $data = array()): string
    {
        $contentAsString = file_get_contents($this->partialsPathLoader . '/header.mustache');
        $contentAsString .= file_get_contents($contentFile);
        $contentAsString .= file_get_contents($this->partialsPathLoader . '/footer.mustache');
        return $this->mustache->render($contentAsString, $data);
    }

    public function renderToString($contentFile, $data = array()): string
    {
        $contentAsString = file_get_contents($this->partialsPathLoader . '/' . $contentFile . "View.mustache");
        return $this->mustache->render($contentAsString, $data);
    }
}
