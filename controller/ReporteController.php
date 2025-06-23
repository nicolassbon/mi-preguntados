<?php

class ReporteController
{

    private $preguntaModel;
    private $view;

    public function __construct($preguntaModel, $view)
    {
        $this->preguntaModel = $preguntaModel;
        $this->view = $view;
    }

    public function crearReporte()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_pregunta'])) {
            echo "error";
            exit();
        }

        $idPregunta = (int)$_POST['id_pregunta'];
        $idReportador = $_SESSION['usuario_id'] ?? null;

        $motivo = trim($_POST['motivo'] ?? '');
        $motivo = $motivo !== '' ? $motivo : 'Sin motivo especificado';

        $this->preguntaModel->insertarReportePregunta($idPregunta, $idReportador, $motivo);
        $this->preguntaModel->actualizarEstadoPregunta($idPregunta, 'reportada');

        $this->limpiarSesionPregunta();

        header("Location: /ruleta/show");
        exit();
    }

    private function limpiarSesionPregunta()
    {
        unset(
            $_SESSION['nombre_categoria'],
            $_SESSION['id_pregunta'],
            $_SESSION['pregunta'],
            $_SESSION['inicio_pregunta']
        );
    }

}