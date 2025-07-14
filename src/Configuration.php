<?php

namespace App;

use App\controller\AdminController;
use App\controller\EditorController;
use App\controller\HomeController;
use App\controller\LoginController;
use App\controller\PartidaController;
use App\controller\PerfilController;
use App\controller\PreguntaController;
use App\controller\RankingController;
use App\controller\RegistroController;
use App\controller\RuletaController;
use App\controller\TrampitasController;
use App\core\Database;
use App\core\EmailSender;
use App\core\MustachePresenter;
use App\core\PdfGenerator;
use App\core\Router;
use App\model\AdminModel;
use App\model\CategoriaModel;
use App\model\JuegoModel;
use App\model\PartidaModel;
use App\model\PreguntaModel;
use App\model\RankingModel;
use App\model\ReportePreguntaModel;
use App\model\RolModel;
use App\model\SugerenciaPreguntaModel;
use App\model\UbicacionModel;
use App\model\UsuarioModel;

class Configuration
{
    private ?Database $database = null;
    private ?EmailSender $emailSender = null;
    private ?MustachePresenter $viewer = null;
    private ?UsuarioModel $usuarioModel = null;
    private ?PreguntaModel $preguntaModel = null;
    private ?PdfGenerator $pdfGenerator = null;

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

    public function getIniConfig(): bool|array
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

    public function getPdfGenerator(): PdfGenerator
    {
        if ($this->pdfGenerator === null) {
            $this->pdfGenerator = new PdfGenerator();
        }
        return $this->pdfGenerator;
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

    public function getAdminController(): AdminController
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
            $this->getViewer(),
            new UsuarioModel($this->getDatabase())
        );
    }

    public function getRuletaController(): RuletaController
    {
        return new RuletaController(
            $this->getViewer(),
            new CategoriaModel($this->getDatabase()),
            new UsuarioModel($this->getDatabase())
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
        $juegoModel = new JuegoModel($db);

        return new PartidaController(
            $this->getViewer(),
            $partidaModel,
            $preguntaModel,
            $usuarioModel,
            $juegoModel
        );
    }

    public function getTrampitasController(): TrampitasController
    {
        return new TrampitasController(
            $this->getViewer(),
            new UsuarioModel($this->getDatabase())
        );
    }

    public function getRouter(): Router
    {
        return new Router("getHomeController", "show", $this);
    }
}
