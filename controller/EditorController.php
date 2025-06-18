<?php

class EditorController
{
    private $view;
    private $preguntaModel;

    public function __construct($view, $preguntaModel)
    {
        $this->view = $view;
        $this->preguntaModel = $preguntaModel;
    }

    public function show()
    {
        $this->view->render("panelEditor", [
            'title' => 'Panel Editor'
        ]);
    }

    public function reportes()
    {
        $terminoBusqueda = $_GET['terminoBusqueda'] ?? '';

        $preguntasReportadas = $this->preguntaModel->getPreguntasReportadasConDetalles($terminoBusqueda);

        $this->view->render('preguntasReportadas', [
            'title' => 'Preguntas Reportadas',
            'reportes' => $preguntasReportadas,
            'terminoBusqueda' => $terminoBusqueda,
            'hayReportes' => !empty($preguntasReportadas)
        ]);

    }

    public function procesarReporte()
    {
        $id_reporte = (int)($_POST['id_reporte'] ?? 0);
        $id_pregunta = (int)($_POST['id_pregunta'] ?? 0);
        $accion = $_POST['accion'] ?? '';

        if ($id_reporte && $id_pregunta && $accion) {
            switch ($accion) {
                case 'descartar':
                    $this->preguntaModel->descartarReporte($id_pregunta, $id_reporte);
                    break;
                case 'aprobar':
                    $this->preguntaModel->aprobarReporte($id_pregunta, $id_reporte);
                    break;
                case 'editar':
                    header("Location: /editor/editarPregunta?id={$id_pregunta}&reporte={$id_reporte}");
                    exit;
            }
        }

        header("Location: /editor/reportes");
        exit;
    }
}