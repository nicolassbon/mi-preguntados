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
use App\core\MercadoPagoService;
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
    private ?MercadoPagoService $mercadoPagoService = null;
    private ?MustachePresenter $viewer = null;
    private ?PdfGenerator $pdfGenerator = null;
    private ?Router $router = null;
    private ?AdminModel $adminModel = null;
    private ?CategoriaModel $categoriaModel = null;
    private ?JuegoModel $juegoModel = null;
    private ?PartidaModel $partidaModel = null;
    private ?RankingModel $rankingModel = null;
    private ?ReportePreguntaModel $reportePreguntaModel = null;
    private ?RolModel $rolModel = null;
    private ?SugerenciaPreguntaModel $sugerenciaPreguntaModel = null;
    private ?UbicacionModel $ubicacionModel = null;
    private ?UsuarioModel $usuarioModel = null;
    private ?PreguntaModel $preguntaModel = null;

    private function getDatabase(): Database
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

    private function getEmailSender(): EmailSender
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

    private function getIniConfig(): bool|array
    {
        return parse_ini_file("configuration/config.ini", true);
    }

    private function getViewer(): MustachePresenter
    {
        if ($this->viewer === null) {
            $this->viewer = new MustachePresenter("view");
        }
        return $this->viewer;
    }

    private function getPdfGenerator(): PdfGenerator
    {
        if ($this->pdfGenerator === null) {
            $this->pdfGenerator = new PdfGenerator();
        }
        return $this->pdfGenerator;
    }

    private function getMercadoPagoService(): MercadoPagoService
    {
        if ($this->mercadoPagoService === null) {
            $config = $this->getIniConfig();
            $this->mercadoPagoService = new MercadoPagoService(
                $config["mercado_pago"]["access_token"],
                $config["mercado_pago"]["base_url"]
            );
        }
        return $this->mercadoPagoService;
    }

    private function getAdminModel(): AdminModel
    {
        if ($this->adminModel === null) {
            $this->adminModel = new AdminModel($this->getDatabase());
        }
        return $this->adminModel;
    }

    private function getCategoriaModel(): CategoriaModel
    {
        if ($this->categoriaModel === null) {
            $this->categoriaModel = new CategoriaModel($this->getDatabase());
        }
        return $this->categoriaModel;
    }

    private function getJuegoModel(): JuegoModel
    {
        if ($this->juegoModel === null) {
            $this->juegoModel = new JuegoModel($this->getDatabase());
        }
        return $this->juegoModel;
    }

    private function getPartidaModel(): PartidaModel
    {
        if ($this->partidaModel === null) {
            $this->partidaModel = new PartidaModel($this->getDatabase());
        }
        return $this->partidaModel;
    }

    private function getPreguntaModel(): PreguntaModel
    {
        if ($this->preguntaModel === null) {
            $this->preguntaModel = new PreguntaModel($this->getDatabase());
        }
        return $this->preguntaModel;
    }

    private function getRankingModel(): RankingModel
    {
        if ($this->rankingModel === null) {
            $this->rankingModel = new RankingModel($this->getDatabase());
        }
        return $this->rankingModel;
    }

    private function getReportePreguntaModel(): ReportePreguntaModel
    {
        if ($this->reportePreguntaModel === null) {
            $this->reportePreguntaModel = new ReportePreguntaModel($this->getDatabase());
        }
        return $this->reportePreguntaModel;
    }

    private function getRolModel(): RolModel
    {
        if ($this->rolModel === null) {
            $this->rolModel = new RolModel($this->getDatabase());
        }
        return $this->rolModel;
    }

    private function getSugerenciaPreguntaModel(): SugerenciaPreguntaModel
    {
        if ($this->sugerenciaPreguntaModel === null) {
            $this->sugerenciaPreguntaModel = new SugerenciaPreguntaModel($this->getDatabase());
        }
        return $this->sugerenciaPreguntaModel;
    }

    private function getUbicacionModel(): UbicacionModel
    {
        if ($this->ubicacionModel === null) {
            $this->ubicacionModel = new UbicacionModel($this->getDatabase());
        }
        return $this->ubicacionModel;
    }

    private function getUsuarioModel(): UsuarioModel
    {
        if ($this->usuarioModel === null) {
            $this->usuarioModel = new UsuarioModel($this->getDatabase());
        }
        return $this->usuarioModel;
    }


    public function getRegistroController(): RegistroController
    {
        return new RegistroController(
            $this->getViewer(),
            $this->getEmailSender(),
            $this->getUsuarioModel(),
            $this->getUbicacionModel(),
            $this->getRolModel()
        );
    }

    public function getRankingController(): RankingController
    {
        return new RankingController(
            $this->getViewer(),
            $this->getRankingModel()
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
            $this->getRolModel()
        );
    }

    public function getEditorController(): EditorController
    {
        return new EditorController(
            $this->getViewer(),
            $this->getPreguntaModel(),
            $this->getCategoriaModel(),
            $this->getSugerenciaPreguntaModel(),
            $this->getReportePreguntaModel()
        );
    }

    public function getAdminController(): AdminController
    {
        return new AdminController(
            $this->getViewer(),
            $this->getPdfGenerator(),
            $this->getAdminModel()
        );
    }

    public function getHomeController(): HomeController
    {
        return new HomeController(
            $this->getViewer(),
            $this->getUsuarioModel()
        );
    }

    public function getRuletaController(): RuletaController
    {
        return new RuletaController(
            $this->getViewer(),
            $this->getCategoriaModel(),
            $this->getUsuarioModel()
        );
    }

    public function getPreguntaController(): PreguntaController
    {
        return new PreguntaController(
            $this->getViewer(),
            $this->getPreguntaModel(),
            $this->getSugerenciaPreguntaModel()
        );
    }

    public function getPartidaController(): PartidaController
    {
        return new PartidaController(
            $this->getViewer(),
            $this->getPartidaModel(),
            $this->getPreguntaModel(),
            $this->getUsuarioModel(),
            $this->getJuegoModel(),
        );
    }

    public function getTrampitasController(): TrampitasController
    {
        return new TrampitasController(
            $this->getViewer(),
            $this->getUsuarioModel(),
            $this->getMercadoPagoService()
        );
    }

    public function getRouter(): Router
    {
        if ($this->router === null) {
            $this->router = new Router("getHomeController", "show", $this);
        }
        return $this->router;
    }
}
