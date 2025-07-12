<?php

use JetBrains\PhpStorm\NoReturn;

class EditorController
{
    private $view;
    private $preguntaModel;
    private $categoriaModel;
    private $sugerenciaPreguntaModel;
    private $reportePreguntaModel;

    private const REPORTES_URL = "/editor/reportes";
    private const SUGERENCIAS_URL = "/editor/sugerencias";
    private const PREGUNTAS_URL = "/editor/gestionarPreguntas";

    public function __construct($view, $preguntaModel, $categoriaModel, $sugerenciaPreguntaModel, $reportePreguntaModel)
    {
        $this->view = $view;
        $this->preguntaModel = $preguntaModel;
        $this->categoriaModel = $categoriaModel;
        $this->sugerenciaPreguntaModel = $sugerenciaPreguntaModel;
        $this->reportePreguntaModel = $reportePreguntaModel;
    }

    public function show(): void
    {
        $this->view->render("panelEditor", [
            'title' => 'Panel Editor'
        ]);
    }

    public function sugerencias(): void
    {
        $filtros = $this->prepararFiltrosYCategorias('sugerencias');

        $preguntasSugeridas = $this->sugerenciaPreguntaModel->getPreguntasSugeridas($filtros['terminoBusqueda'], $filtros['id_categoria'], $filtros['estado']);

        foreach ($preguntasSugeridas as &$pregunta) {
            $pregunta['estadoPendiente'] = ($pregunta['estado'] === 'pendiente');
            $pregunta['esAprobada'] = ($pregunta['estado'] === 'aprobada');
            $pregunta['esRechazada'] = ($pregunta['estado'] === 'rechazada');
            $pregunta['estado'] = ucfirst($pregunta['estado']);
        }
        unset($pregunta);

        $this->view->render("sugerencias", [
            'title' => 'Sugerencias de usuarios',
            'sugeridas' => $preguntasSugeridas,
            'haySugeridas' => !empty($preguntasSugeridas),
            'terminoBusqueda' => $filtros['terminoBusqueda'],
            'categorias' => $filtros['categorias'],
            'categoria_todas' => $filtros['id_categoria'] === 'todasLasCategorias',
            'estado' => $filtros['estado'],
            'estado_pendiente' => $filtros['estado_pendiente'],
            'estado_aprobada' => $filtros['estado_aprobada'],
            'estado_rechazada' => $filtros['estado_rechazada'],
            'estado_todos' => $filtros['estado_todos']
        ]);
    }

    public function verSugerencia(): void
    {
        $id_pregunta = $_GET['id_pregunta'] ?? null;
        $origen = $_GET['origen'] ?? 'sugerencias';

        if (!$id_pregunta) {
            $this->redirectTo(self::SUGERENCIAS_URL);
        }

        $pregunta = $this->preguntaModel->getPreguntaPorId($id_pregunta);
        $respuestas = $this->preguntaModel->getRespuestasPorPregunta($id_pregunta);
        $autor = $this->sugerenciaPreguntaModel->getAutorDePreguntaSugerida($id_pregunta);

        if (!$pregunta) {
            $this->redirectTo(self::SUGERENCIAS_URL);
        }

        $this->view->render("verSugerencia", [
            'title' => 'Ver Pregunta Sugerida',
            'pregunta' => $pregunta,
            'respuestas' => $respuestas,
            'autor' => $autor,
            'volver_a_gestionar' => $origen === 'gestionar'
        ]);
    }

    #[NoReturn] public function aceptarSugerencia(): void
    {
        $id = $_GET['id'];
        $this->sugerenciaPreguntaModel->activarPreguntaSugerida($id);
        $this->sugerenciaPreguntaModel->fechaResolucionSugerencia($id);
        $this->sugerenciaPreguntaModel->actualizarEstadoPregunta($id, 'aprobada');
        $this->redirectTo(self::SUGERENCIAS_URL);
    }

    #[NoReturn] public function descartarSugerencia(): void
    {
        $id = $_GET['id'];
        $this->sugerenciaPreguntaModel->desactivarPreguntaSugerida($id);
        $this->sugerenciaPreguntaModel->fechaResolucionSugerencia($id);
        $this->sugerenciaPreguntaModel->actualizarEstadoPregunta($id, 'rechazada');
        $this->redirectTo(self::SUGERENCIAS_URL);
    }

    public function gestionarPreguntas(): void
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
        unset($pregunta);

        $this->view->render("gestionarPreguntas", [
            'title' => 'Gestión de Preguntas',
            'categorias' => $categorias,
            'categoria_todas' => $filtros['id_categoria'] === 'todasLasCategorias',
            'preguntas' => $preguntas,
            'terminoBusqueda' => $filtros['terminoBusqueda'],
            'hayPreguntas' => !empty($preguntas),
        ]);
    }

    #[NoReturn] public function desactivarPregunta(): void
    {
        $id_pregunta = $_GET['id_pregunta'] ?? '';
        $this->preguntaModel->desactivarPregunta($id_pregunta);

        $this->redirectTo(self::PREGUNTAS_URL);
    }

    #[NoReturn] public function activarPregunta(): void
    {
        $id_pregunta = $_GET['id_pregunta'] ?? '';
        $this->preguntaModel->activarPregunta($id_pregunta);

        $this->redirectTo(self::PREGUNTAS_URL);
    }

    public function editarPregunta(): void
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

    #[NoReturn] public function guardarEdicion(): void
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
            $this->reportePreguntaModel->actualizarEstadoReporte($id_reporte, 'descartado');
            $this->preguntaModel->actualizarEstadoPregunta($id_pregunta, 'activa');
            $this->redirectTo(self::REPORTES_URL);
        }

        $this->redirectTo(self::PREGUNTAS_URL);
    }

    public function reportes(): void
    {
        $filtros = $this->prepararFiltrosYCategorias('reportes');

        $preguntasReportadas = $this->reportePreguntaModel->getPreguntasReportadasConDetalles($filtros['id_categoria'], $filtros['terminoBusqueda'], $filtros['estado']);

        foreach ($preguntasReportadas as &$reporte) {
            $reporte['estadoPendiente'] = ($reporte['estado'] === 'pendiente');
            $reporte['esAprobado'] = ($reporte['estado'] === 'aprobado');
            $reporte['esDescartado'] = ($reporte['estado'] === 'descartado');
            $reporte['estado'] = ucfirst($reporte['estado']);
        }
        unset($reporte);

        $this->view->render('reportes', [
            'title' => 'Preguntas Reportadas',
            'reportes' => $preguntasReportadas,
            'terminoBusqueda' => $filtros['terminoBusqueda'],
            'hayReportes' => !empty($preguntasReportadas),
            'categorias' => $filtros['categorias'],
            'categoria_todas' => $filtros['categoria_todas'],
            'id_categoria' => $filtros['id_categoria'],
            'estado' => $filtros['estado'],
            'estado_pendiente' => $filtros['estado_pendiente'],
            'estado_aprobado' => $filtros['estado_aprobado'],
            'estado_descartado' => $filtros['estado_descartado'],
            'estado_todos' => $filtros['estado_todos']
        ]);
    }

    #[NoReturn] public function aprobarReporte(): void
    {
        $id_reporte = (int)($_POST['id_reporte'] ?? 0);
        $id_pregunta = (int)($_POST['id_pregunta'] ?? 0);

        if ($id_reporte && $id_pregunta) {
            $this->reportePreguntaModel->aprobarReporte($id_pregunta, $id_reporte);
        }

        $this->redirectTo(self::REPORTES_URL);
    }

    #[NoReturn] public function descartarReporte(): void
    {
        $id_reporte = (int)($_POST['id_reporte'] ?? 0);
        $id_pregunta = (int)($_POST['id_pregunta'] ?? 0);

        if ($id_reporte && $id_pregunta) {
            $this->reportePreguntaModel->descartarReporte($id_pregunta, $id_reporte);
        }

        $this->redirectTo(self::REPORTES_URL);
    }

    private function prepararFiltrosYCategorias(string $tipo = 'reportes'): array
    {
        $terminoBusqueda = $_GET['terminoBusqueda'] ?? '';
        $id_categoria = $_GET['categoria'] ?? 'todasLasCategorias';
        $estado = $_GET['estado'] ?? 'pendiente';

        $categorias = $this->categoriaModel->getCategorias();
        foreach ($categorias as &$categoria) {
            $categoria['seleccionada'] = ($categoria['id_categoria'] === $id_categoria);
        }
        unset($categoria);

        $estadosReportes = ['pendiente', 'aprobado', 'descartado', 'todos'];
        $estadosSugerencias = ['pendiente', 'aprobada', 'rechazada', 'todos'];

        if ($tipo === 'sugerencias') {
            $estadoValido = in_array($estado, $estadosSugerencias, true) ? $estado : 'pendiente';

            return [
                'terminoBusqueda' => $terminoBusqueda,
                'id_categoria' => $id_categoria,
                'categorias' => $categorias,
                'categoria_todas' => $id_categoria === 'todasLasCategorias',
                'estado' => $estadoValido,
                'estado_pendiente' => $estadoValido === 'pendiente',
                'estado_aprobada' => $estadoValido === 'aprobada',
                'estado_rechazada' => $estadoValido === 'rechazada',
                'estado_todos' => $estadoValido === 'todos'
            ];
        }

        $estadoValido = in_array($estado, $estadosReportes, true) ? $estado : 'pendiente';

        return [
            'terminoBusqueda' => $terminoBusqueda,
            'id_categoria' => $id_categoria,
            'categorias' => $categorias,
            'categoria_todas' => $id_categoria === 'todasLasCategorias',
            'estado' => $estadoValido,
            'estado_pendiente' => $estadoValido === 'pendiente',
            'estado_aprobado' => $estadoValido === 'aprobado',
            'estado_descartado' => $estadoValido === 'descartado',
            'estado_todos' => $estadoValido === 'todos'
        ];
    }

    #[NoReturn] private function redirectTo($str): void
    {
        header('Location: ' . $str);
        exit();
    }

}
