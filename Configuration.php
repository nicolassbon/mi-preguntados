<?php
require_once "core/Database.php";
require_once "core/MustachePresenter.php";
require_once "core/Router.php";
require_once "core/EmailSender.php";

require_once "controller/HomeController.php";
require_once "controller/RegistroController.php";
require_once "controller/LoginController.php";
require_once "controller/PerfilController.php";
require_once "controller/RankingController.php";
require_once "controller/EditorController.php";
require_once "controller/LobbyController.php";
require_once "controller/RuletaController.php";
require_once "controller/InicioController.php";
require_once "controller/PartidaController.php";
require_once "controller/PerdioController.php";
require_once "controller/ReporteController.php";

require_once "model/RegistroModel.php";
require_once "model/LoginModel.php";
require_once "model/EmailModel.php";
require_once "model/PerfilModel.php";
require_once "model/RankingModel.php";
require_once "model/RolModel.php";
require_once "model/RuletaModel.php";
require_once "model/PartidaModel.php";
require_once "model/PreguntaModel.php";
require_once "model/EditorModel.php";
require_once "model/UbicacionModel.php";

require_once "controller/CrearPreguntaController.php";
require_once "model/CrearPreguntaModel.php";

require_once "controller/MensajeCreadaCorrectamenteController.php";

include_once 'vendor/mustache/src/Mustache/Autoloader.php';

class Configuration
{
    public function getDatabase(): Database
    {
        $config = $this->getIniConfig();

        return new Database(
            $config["database"]["server"],
            $config["database"]["user"],
            $config["database"]["dbname"],
            $config["database"]["pass"]
        );
    }

    public function getEmailSender(): EmailSender
    {
        $config = $this->getIniConfig();

        return new EmailSender(
            $config["email"]["host"],
            $config["email"]["username"],
            $config["email"]["password"],
            $config["email"]["port"]
        );
    }

    public function getIniConfig()
    {
        return parse_ini_file("configuration/config.ini", true);
    }

    public function getRegistroController(): RegistroController
    {
        return new RegistroController(
            new RegistroModel($this->getDatabase()),
            new UbicacionModel($this->getDatabase()),
            new RolModel($this->getDatabase()),
            $this->getViewer(),
            $this->getEmailSender()
        );
    }

    public function getRankingController(): RankingController
    {
        return new RankingController(
            new RankingModel($this->getDatabase()),
            $this->getViewer()
        );
    }

    public function getPerfilController(): PerfilController
    {
        return new PerfilController(
            new PerfilModel($this->getDatabase()),
            $this->getViewer()
        );
    }

    public function getLoginController(): LoginController
    {
        return new LoginController(
            new LoginModel($this->getDatabase()),
            $this->getViewer(),
            new RolModel($this->getDatabase())
        );
    }

    public function getEditorController(): EditorController
    {
        return new EditorController(
            $this->getViewer(),
            new EditorModel($this->getDatabase()),
            new PreguntaModel($this->getDatabase())
        );
    }

    public function getReporteController(): ReporteController
    {
        return new ReporteController(
            new PreguntaModel($this->getDatabase()),
            $this->getViewer()
        );
    }

    public function getHomeController(): HomeController
    {
        return new HomeController($this->getViewer());
    }

    public function getLobbyController(): LobbyController
    {
        return new LobbyController(
            $this->getViewer()
        );
    }

    public function getRuletaController(): RuletaController
    {
        return new RuletaController(
            new RuletaModel($this->getDatabase()),
            $this->getViewer()
        );
    }

    public function getCrearPreguntaController(): crearPreguntaController
    {
        return new CrearPreguntaController(
            new CrearPreguntaModel($this->getDatabase()),
            $this->getViewer()
        );
    }

    public function getPartidaController(): PartidaController
    {
        return new PartidaController(
            new PartidaModel($this->getDatabase()),
            $this->getViewer()
        );
    }

    public function getPerdioController(): PerdioController
    {
        return new PerdioController(
            $this->getViewer()
        );
    }

    public function getMensajeCreadaCorrectamenteController(): MensajeCreadaCorrectamenteController
    {
        return new MensajeCreadaCorrectamenteController(
            $this->getViewer()
        );
    }

    public function getInicioController(): InicioController
    {
        return new InicioController(
            $this->getViewer()
        );
    }

    public function getRouter(): Router
    {
        return new Router("getHomeController", "show", $this);
    }

    public function getViewer(): MustachePresenter
    {
        return new MustachePresenter("view");
    }
}
