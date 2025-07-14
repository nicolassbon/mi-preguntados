<?php

namespace App\controller;

use App\core\MustachePresenter;
use App\model\PreguntaModel;
use App\model\SugerenciaPreguntaModel;
use JetBrains\PhpStorm\NoReturn;

class PreguntaController
{
    private MustachePresenter $view;
    private PreguntaModel $preguntaModel;
    private SugerenciaPreguntaModel $sugerenciaPreguntaModel;

    public function __construct($view, $preguntaModel, $sugerenciaPreguntaModel)
    {
        $this->view = $view;
        $this->preguntaModel = $preguntaModel;
        $this->sugerenciaPreguntaModel = $sugerenciaPreguntaModel;
    }

    public function sugerir(): void
    {
        unset($_SESSION['sugerencia_creada']);

        $this->view->render("sugerencia", [
            'title' => 'Sugerir Pregunta'
        ]);
    }

    #[NoReturn] public function crearSugerencia(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'
            || empty($_POST['pregunta'])
            || empty($_POST['opcion'])
            || empty($_POST['opcion2'])
            || empty($_POST['opcion3'])
            || empty($_POST['opcion4'])
            || empty($_POST['opcionCorrecta'])
            || empty($_POST['categoria'])) {
            exit();
        }

        if (isset($_SESSION['sugerencia_creada'])) {
            header("Location: /pregunta/sugerencia-exitosa");
            exit();
        }

        $id_usuario = $_SESSION['usuario_id'];
        $pregunta = $_POST['pregunta'];
        $opcion = $_POST['opcion'];
        $opcion2 = $_POST['opcion2'];
        $opcion3 = $_POST['opcion3'];
        $opcion4 = $_POST['opcion4'];
        $opcionCorrecta = (int)$_POST['opcionCorrecta'];
        $id_categoria = $_POST['categoria'];

        $this->preguntaModel->agregarPregunta($pregunta, $id_categoria);
        $id_pregunta = $this->preguntaModel->buscarPreguntaCreada($pregunta);
        $this->preguntaModel->agregarRespuestas($id_pregunta, $opcion, $opcion2, $opcion3, $opcion4, $opcionCorrecta);

        $this->sugerenciaPreguntaModel->agregarSugerencia($id_usuario, $id_pregunta, $id_categoria);
        $_SESSION['sugerencia_creada'] = true;

        header("Location: /pregunta/sugerenciaExitosa");
        exit();
    }

    public function sugerenciaExitosa(): void
    {
        $this->view->render("sugerenciaSuccess", [
            'title' => 'Sugerir Pregunta'
        ]);
    }

}
