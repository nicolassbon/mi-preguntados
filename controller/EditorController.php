<?php

class EditorController
{
    private $view;
    private $preguntaModel;
    private $categoriaModel;
    private $sugerenciaPreguntaModel;
    private $reportePreguntaModel;

    public function __construct($view, $preguntaModel, $categoriaModel, $sugerenciaPreguntaModel, $reportePreguntaModel)
    {
        $this->view = $view;
        $this->preguntaModel = $preguntaModel;
        $this->categoriaModel = $categoriaModel;
        $this->sugerenciaPreguntaModel = $sugerenciaPreguntaModel;
        $this->reportePreguntaModel = $reportePreguntaModel;
    }

    public function show()
    {
        $this->view->render("panelEditor", [
            'title' => 'Panel Editor'
        ]);
    }

    public function sugerencias()
    {
        $filtros = $this->prepararFiltrosYCategorias();

        $preguntasSugeridas = $this->sugerenciaPreguntaModel->getPreguntasSugeridas($filtros['terminoBusqueda'], $filtros['id_categoria']);
        $haySugeridas = !empty($preguntasSugeridas);

        $this->view->render("sugerencias", [
            'title' => 'Sugerencias de usuarios',
            'sugeridas' => $preguntasSugeridas,
            'haySugeridas' => $haySugeridas,
            'terminoBusqueda' => $filtros['terminoBusqueda'],
            'categorias' => $filtros['categorias'],
            'categoria_todas' => $filtros['id_categoria'] === 'todasLasCategorias'
        ]);
    }

    public function verSugerencia()
    {
        $id_pregunta = $_GET['id_pregunta'] ?? null;
        $origen = $_GET['origen'] ?? 'sugerencias';

        if (!$id_pregunta) {
            $this->redirectTo("/editor/sugerencias");
        }

        $pregunta = $this->preguntaModel->getPreguntaPorId($id_pregunta);
        $respuestas = $this->preguntaModel->getRespuestasPorPregunta($id_pregunta);
        $autor = $this->sugerenciaPreguntaModel->getAutorDePreguntaSugerida($id_pregunta);

        if (!$pregunta) {
            $this->redirectTo("/editor/sugerencias");
        }

        $this->view->render("verSugerencia", [
            'title' => 'Ver Pregunta Sugerida',
            'pregunta' => $pregunta,
            'respuestas' => $respuestas,
            'autor' => $autor,
            'volver_a_gestionar' => $origen === 'gestionar'
        ]);
    }

    public function aceptarSugerencia()
    {
        $id = $_GET['id'];
        $this->sugerenciaPreguntaModel->activarPreguntaSugerida($id);
        $this->sugerenciaPreguntaModel->fechaResolucionSugerencia($id);
        $this->sugerenciaPreguntaModel->actualizarEstadoPregunta($id, 'aprobada');
        $this->redirectTo("/editor/sugerencias");
    }

    public function descartarSugerencia()
    {
        $id = $_GET['id'];
        $this->sugerenciaPreguntaModel->desactivarPreguntaSugerida($id);
        $this->sugerenciaPreguntaModel->fechaResolucionSugerencia($id);
        $this->sugerenciaPreguntaModel->actualizarEstadoPregunta($id, 'rechazada');
        $this->redirectTo("/editor/sugerencias");
    }

    public function gestionarPreguntas()
    {
        $filtros = $this->prepararFiltrosYCategorias();
        $categorias = $filtros['categorias'];

        $preguntas = ($filtros['id_categoria'] === 'todasLasCategorias')
            ? $this->preguntaModel->getPreguntas($filtros['terminoBusqueda'])
            : $this->preguntaModel->getPreguntasPorCategoria((int)$filtros['id_categoria'], $filtros['terminoBusqueda']);

        foreach ($preguntas as &$pregunta) {
            $estado = $pregunta['estado'];
            $pregunta['es_activa'] = $estado === 'activa';
            $pregunta['es_deshabilitada'] = $estado === 'deshabilitada';
            $pregunta['es_reportada'] = $estado === 'reportada';
            $pregunta['es_sugerida'] = $estado === 'sugerida';
        }

        $this->view->render("gestionarPreguntas", [
            'title' => 'Gestión de Preguntas',
            'categorias' => $categorias,
            'categoria_todas' => $filtros['id_categoria'] === 'todasLasCategorias',
            'preguntas' => $preguntas,
            'terminoBusqueda' => $filtros['terminoBusqueda'],
            'hayPreguntas' => !empty($preguntas),
        ]);
    }

    public function desactivarPregunta()
    {
        $id_pregunta = $_GET['id_pregunta'] ?? '';
        $this->preguntaModel->desactivarPregunta($id_pregunta);

        $this->redirectTo("/editor/gestionarPreguntas");
    }

    public function activarPregunta()
    {
        $id_pregunta = $_GET['id_pregunta'] ?? '';
        $this->preguntaModel->activarPregunta($id_pregunta);

        $this->redirectTo("/editor/gestionarPreguntas");
    }

    public function editarPregunta()
    {
        $id_pregunta = $_GET['id_pregunta'] ?? '';
        $id_reporte = $_GET['id_reporte'] ?? '';

        $pregunta = $this->preguntaModel->getPreguntaPorId($id_pregunta);
        $respuestas = $this->preguntaModel->getRespuestasPorPregunta($id_pregunta);

        $this->view->render("editarPregunta", [
            'title' => 'Editar Pregunta',
            'pregunta' => $pregunta,
            'respuestas' => $respuestas,
            'id_reporte' => $id_reporte,
        ]);
    }

    public function guardarEdicion()
    {
        $id_pregunta = $_POST['id_pregunta'] ?? null;
        $id_reporte = $_POST['id_reporte'] ?? null;
        $textoPregunta = $_POST['pregunta'] ?? '';
        $respuestas = $_POST['respuestas'] ?? [];
        $ids_respuestas = $_POST['ids_respuestas'] ?? [];

        $this->preguntaModel->actualizarPregunta($id_pregunta, $textoPregunta);

        foreach ($respuestas as $i => $respuesta) {
            if (isset($ids_respuestas[$i])) {
                $this->preguntaModel->actualizarRespuesta((int)$ids_respuestas[$i], $respuesta);
            }
        }

        if ($id_reporte) {
            $this->reportePreguntaModel->actualizarEstadoReporte($id_reporte, 'resuelto');
            $this->preguntaModel->actualizarEstadoPregunta($id_pregunta, 'activa');
            $this->redirectTo("/editor/reportes");
        }

        $this->redirectTo("/editor/gestionarPreguntas");
    }

    public function reportes()
    {
        $filtros = $this->prepararFiltrosYCategorias();

        $preguntasReportadas = $this->reportePreguntaModel->getPreguntasReportadasConDetalles($filtros['terminoBusqueda'], $filtros['id_categoria']);

        $this->view->render('reportes', [
            'title' => 'Preguntas Reportadas',
            'reportes' => $preguntasReportadas,
            'terminoBusqueda' => $filtros['terminoBusqueda'],
            'hayReportes' => !empty($preguntasReportadas),
            'categorias' => $filtros['categorias'],
            'categoria_todas' => $filtros['id_categoria'] === 'todasLasCategorias',
            'id_categoria' => $filtros['id_categoria']
        ]);
    }

    public function aprobarReporte()
    {
        $id_reporte = (int)($_POST['id_reporte'] ?? 0);
        $id_pregunta = (int)($_POST['id_pregunta'] ?? 0);

        if ($id_reporte && $id_pregunta) {
            $this->reportePreguntaModel->aprobarReporte($id_pregunta, $id_reporte);
        }

        $this->redirectTo("/editor/reportes");
    }

    public function descartarReporte()
    {
        $id_reporte = (int)($_POST['id_reporte'] ?? 0);
        $id_pregunta = (int)($_POST['id_pregunta'] ?? 0);

        if ($id_reporte && $id_pregunta) {
            $this->reportePreguntaModel->descartarReporte($id_pregunta, $id_reporte);
        }

        $this->redirectTo("/editor/reportes");
    }

    private function prepararFiltrosYCategorias(): array
    {
        $terminoBusqueda = $_GET['terminoBusqueda'] ?? '';
        $id_categoria = $_GET['categoria'] ?? 'todasLasCategorias';

        $categorias = $this->categoriaModel->getCategorias();
        foreach ($categorias as &$categoria) {
            $categoria['seleccionada'] = ($categoria['id_categoria'] == $id_categoria);
        }

        return [
            'terminoBusqueda' => $terminoBusqueda,
            'id_categoria' => $id_categoria,
            'categorias' => $categorias,
            'categoria_todas' => $id_categoria === 'todasLasCategorias'
        ];
    }

    private function redirectTo($str)
    {
        header('Location: ' . $str);
        exit();
    }

}