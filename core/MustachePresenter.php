<?php

class MustachePresenter
{
    private $mustache;
    private $partialsPathLoader;

    public function __construct($partialsPathLoader)
    {
        Mustache_Autoloader::register();
        $this->mustache = new Mustache_Engine(
            array(
                'partials_loader' => new Mustache_Loader_FilesystemLoader($partialsPathLoader)
            ));
        $this->partialsPathLoader = $partialsPathLoader;
    }

    public function render($contentFile, $data = array())
    {
        if (isset($_SESSION['usuario_id'])) {
            $data['user'] = $_SESSION['nombre_usuario'];
        }

        // Variables globales de rol desde sesión
        $data['esEditor'] = $_SESSION['esEditor'] ?? false;
        $data['esAdmin'] = $_SESSION['esAdmin'] ?? false;
        $data['esJugador'] = $_SESSION['esJugador'] ?? false;

        echo $this->generateHtml($this->partialsPathLoader . '/' . $contentFile . "View.mustache", $data);
    }

    public function generateHtml($contentFile, $data = array())
    {
        $contentAsString = file_get_contents($this->partialsPathLoader . '/header.mustache');
        $contentAsString .= file_get_contents($contentFile);
        $contentAsString .= file_get_contents($this->partialsPathLoader . '/footer.mustache');
        return $this->mustache->render($contentAsString, $data);
    }
}