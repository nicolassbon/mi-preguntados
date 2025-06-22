<?php

class EditorController
{
    private $view;
    private $model;
    private $preguntaModel;

    public function __construct($view,$model, $preguntaModel)
    {
        $this->model = $model;
        $this->view = $view;
        $this->preguntaModel = $preguntaModel;
    }

    public function gestionarPreguntas()
    {
        $id_categoria = $_GET['categoria'] ?? 'todasLasCategorias';

        $categorias = $this->model->getCategorias();

        foreach ($categorias as &$categoria) {
            $categoria['seleccionada'] = ($categoria['id_categoria'] == $id_categoria);
        }

        if ($id_categoria === 'todasLasCategorias') {
            $preguntas = $this->model->getPreguntas();
        } else {
            $preguntas = $this->model->getPreguntasPorCategoria((int)$id_categoria);
        }

        $this->view->render("gestionarPreguntas", [
            'title' => 'Gestión de Preguntas',
            'categorias' => $categorias,
            'categoria_todas' => $id_categoria === 'todasLasCategorias',
            'preguntas' => $preguntas
        ]);
    }

    public function desactivar(){
        $id_pregunta = $_GET['id_pregunta'] ?? '';
        $pregunta = $this->model->desactivarPregunta($id_pregunta);

        header("Location: /editor/gestionarPreguntas");
        exit;
    }

    public function activar(){
        $id_pregunta = $_GET['id_pregunta'] ?? '';
        $pregunta = $this->model->activarPregunta($id_pregunta);

        header("Location: /editor/gestionarPreguntas");
        exit;
    }

    public function editar(){
        $id_pregunta = $_GET['id_pregunta'] ?? '';

        $pregunta = $this->model->getPreguntaPorId($id_pregunta);
        $pregunta = $pregunta[0] ?? null;
        $respuestas = $this->model->getRespuestasPorPregunta($id_pregunta);

        $this->view->render("editarPregunta", [
            'title' => 'Editar Pregunta',
            'pregunta' => $pregunta,
            'respuestas' => $respuestas
        ]);
    }

    public function guardarEdicion()
    {
        $id_pregunta = $_POST['id_pregunta'] ?? null;
        $textoPregunta = $_POST['pregunta'] ?? '';
        $respuestas = $_POST['respuestas'] ?? [];
        $ids_respuestas = $_POST['ids_respuestas'] ?? [];

        $this->model->actualizarPregunta($id_pregunta, $textoPregunta);

        foreach ($respuestas as $i => $respuesta) {
            if (isset($ids_respuestas[$i])) {
                $this->model->actualizarRespuesta((int)$ids_respuestas[$i], $respuesta);
            }
        }

        header("Location: /editor/gestionarPreguntas");
        exit;
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