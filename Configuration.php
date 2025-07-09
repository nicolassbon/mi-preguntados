<?php
require_once "core/Database.php";
require_once "core/MustachePresenter.php";
require_once "core/Router.php";
require_once "core/EmailSender.php";
require_once "core/PdfGenerator.php";

require_once "controller/RegistroController.php";
require_once "controller/LoginController.php";
require_once "controller/PerfilController.php";
require_once "controller/RankingController.php";
require_once "controller/EditorController.php";
require_once "controller/HomeController.php";
require_once "controller/RuletaController.php";
require_once "controller/PartidaController.php";
require_once "controller/PreguntaController.php";
require_once "controller/AdminController.php";

require_once "model/RankingModel.php";
require_once "model/RolModel.php";
require_once "model/CategoriaModel.php";
require_once "model/PartidaModel.php";
require_once "model/PreguntaModel.php";
require_once "model/UbicacionModel.php";
require_once "model/UsuarioModel.php";
require_once "model/SugerenciaPreguntaModel.php";
require_once "model/JuegoModel.php";
require_once "model/ReportePreguntaModel.php";
require_once "model/AdminModel.php";

require_once "controller/MensajeCreadaCorrectamenteController.php";

include_once 'vendor/mustache/src/Mustache/Autoloader.php';

class Configuration
{
    private ?Database $database = null;
    private ?EmailSender $emailSender = null;
    private ?MustachePresenter $viewer = null;
    private ?UsuarioModel $usuarioModel = null;
    private ?PreguntaModel $preguntaModel = null;

    public function getDatabase(): Database
    {
        if ($this->database === null) {
            $config = $this->getIniConfig();
            $this->database = new Database(
                $config["database"]["server"],
                $config["database"]["user"],
                $config["database"]["dbname"],
                $config["database"]["pass"]
            );
        }
        return $this->database;
    }

    public function getEmailSender(): EmailSender
    {
        if ($this->emailSender === null) {
            $config = $this->getIniConfig();
            $this->emailSender = new EmailSender(
                $config["email"]["host"],
                $config["email"]["username"],
                $config["email"]["password"],
                $config["email"]["port"]
            );
        }
        return $this->emailSender;
    }

    public function getIniConfig()
    {
        return parse_ini_file("configuration/config.ini", true);
    }

    public function getViewer(): MustachePresenter
    {
        if ($this->viewer === null) {
            $this->viewer = new MustachePresenter("view");
        }
        return $this->viewer;
    }

    public function getPdfGenerator()
    {
        return new PdfGenerator();
    }

    public function getUsuarioModel(): UsuarioModel
    {
        if ($this->usuarioModel === null) {
            $this->usuarioModel = new UsuarioModel($this->getDatabase());
        }
        return $this->usuarioModel;
    }

    public function getPreguntaModel(): PreguntaModel
    {
        if ($this->preguntaModel === null) {
            $this->preguntaModel = new PreguntaModel($this->getDatabase());
        }
        return $this->preguntaModel;
    }

    public function getRegistroController(): RegistroController
    {
        return new RegistroController(
            $this->getViewer(),
            $this->getEmailSender(),
            $this->getUsuarioModel(),
            new UbicacionModel($this->getDatabase()),
            new RolModel($this->getDatabase())
        );
    }

    public function getRankingController(): RankingController
    {
        return new RankingController(
            $this->getViewer(),
            new RankingModel($this->getDatabase())
        );
    }

    public function getPerfilController(): PerfilController
    {
        return new PerfilController(
            $this->getViewer(),
            $this->getUsuarioModel()
        );
    }

    public function getLoginController(): LoginController
    {
        return new LoginController(
            $this->getViewer(),
            $this->getUsuarioModel(),
            new RolModel($this->getDatabase())
        );
    }

    public function getEditorController(): EditorController
    {
        return new EditorController(
            $this->getViewer(),
            $this->getPreguntaModel(),
            new CategoriaModel($this->getDatabase()),
            new SugerenciaPreguntaModel($this->getDatabase()),
            new ReportePreguntaModel($this->getDatabase())
        );
    }

    public function getAdminController()
    {
        return new AdminController(
            $this->getViewer(),
            $this->getPdfGenerator(),
            new AdminModel($this->getDatabase())
        );
    }

    public function getHomeController(): HomeController
    {
        return new HomeController(
            $this->getViewer()
        );
    }

    public function getRuletaController(): RuletaController
    {
        return new RuletaController(
            $this->getViewer(),
            new CategoriaModel($this->getDatabase())
        );
    }

    public function getPreguntaController(): PreguntaController
    {
        return new PreguntaController(
            $this->getViewer(),
            $this->getPreguntaModel(),
            new SugerenciaPreguntaModel($this->getDatabase())
        );
    }

    public function getPartidaController(): PartidaController
    {
        $db = $this->getDatabase();
        $preguntaModel = $this->getPreguntaModel();
        $partidaModel = new PartidaModel($db);
        $usuarioModel = $this->getUsuarioModel();
        $juegoModel = new JuegoModel($db, $preguntaModel);

        return new PartidaController(
            $this->getViewer(),
            $partidaModel,
            $preguntaModel,
            $usuarioModel,
            $juegoModel
        );
    }

    public function getMensajeCreadaCorrectamenteController(): MensajeCreadaCorrectamenteController
    {
        return new MensajeCreadaCorrectamenteController(
            $this->getViewer()
        );
    }

    public function getRouter(): Router
    {
        return new Router("getHomeController", "show", $this);
    }
}
