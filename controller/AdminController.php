<?php

require_once 'helpers/FechaHelper.php';

class AdminController
{

    private $view;
    private $pdfGenerator;
    private $model;

    public function __construct($view, $pdfGenerator, $model)
    {
        $this->view = $view;
        $this->pdfGenerator = $pdfGenerator;
        $this->model = $model;
    }

    /**
     * @throws JsonException
     */
    public function show(): void
    {
        $filtro = $_GET['filtro'] ?? 'mes';
        $data = $this->prepararDatosPanel($filtro, $_GET);

        $this->view->render("panelAdmin", $data);
    }

    /**
     * @throws JsonException
     */
    public function generarPdfDashboard(): void
    {
        $filtro = $_POST['filtro'] ?? 'mes';
        $data = $this->prepararDatosPanel($filtro, $_POST);

        // Imagenes de los graficos
        $data['grafico_edad'] = $_POST['graficoEdad'] ?? null;
        $data['grafico_genero'] = $_POST['graficoGenero'] ?? null;
        $data['grafico_porcentaje'] = $_POST['graficoPorcentaje'] ?? null;
        $data['grafico_paises'] = $_POST['graficoTortaPaises'] ?? null;

        $html = $this->view->renderToString('panelAdminPdf', $data);
        $this->pdfGenerator->generarPdf($html, "Dashboard.pdf", false);
    }

    /**
     * @throws JsonException
     */
    private function prepararDatosPanel($filtro, $parametros): array
    {
        $rangoFechas = FechaHelper::getRangoFechas($filtro, $parametros);
        $desde = $rangoFechas['desde'];
        $hasta = $rangoFechas['hasta'];

        $edad = ['menor' => 0, 'media' => 0, 'mayor' => 0];
        foreach ($this->model->obtenerDistribucionPorRangoEdad($desde, $hasta) as $fila) {
            switch ($fila['rangoEdad']) {
                case 'Menor':
                    $edad['menor'] = (int)$fila['cantidad'];
                    break;
                case 'Mediana edad':
                    $edad['media'] = (int)$fila['cantidad'];
                    break;
                case 'Mayor':
                    $edad['mayor'] = (int)$fila['cantidad'];
                    break;
                default:
                    error_log("Rango de edad desconocido: " . $fila['rangoEdad']);
                    break;
            }
        }

        $genero = ['femenino' => 0, 'masculino' => 0, 'otro' => 0];
        foreach ($this->model->obtenerDistribucionPorGenero($desde, $hasta) as $fila) {
            switch ($fila['descripcion']) {
                case 'Femenino':
                    $genero['femenino'] = (int)$fila['cantidad'];
                    break;
                case 'Masculino':
                    $genero['masculino'] = (int)$fila['cantidad'];
                    break;
                case 'Prefiero no cargarlo':
                    $genero['otro'] = (int)$fila['cantidad'];
                    break;
                default:
                    error_log("Género desconocido: " . $fila['descripcion']);
                    break;
            }
        }

        $porcentajeGeneral = $this->model->obtenerPorcentajeGeneral($desde, $hasta);
        $usuariosPais = $this->model->obtenerUsuariosPorPaisPorFecha($desde, $hasta);
        $rendimientoUsuarios = $this->model->obtenerRendimientosUsuarios($desde, $hasta);

        return [
            'title' => 'Dashboard',
            'filtro_Actual' => $filtro,
            'filtro_dia' => $filtro === 'dia',
            'filtro_semana' => $filtro === 'semana',
            'filtro_mes' => $filtro === 'mes',
            'filtro_anio' => $filtro === 'anio',
            'filtro_personalizado' => $filtro === 'personalizado',
            'desde' => $desde,
            'hasta' => $hasta,
            'rango_mostrar' => date('d/m/Y', strtotime($desde)) . ' al ' . date('d/m/Y', strtotime($hasta)),

            'total_jugadores' => $this->model->obtenerTotalUsuarios(),
            'total_jugadores_nuevos' => $this->model->obtenerTotalUsuariosNuevosPorFecha($desde, $hasta),
            'partidas_jugadas' => $this->model->obtenerPartidasJugadasPorFecha($desde, $hasta),
            'total_preguntas' => $this->model->obtenerPreguntasActivas(),
            'total_preguntas_creadas' => $this->model->obtenerPreguntasActivasPorFecha($desde, $hasta),

            'edad' => $edad,
            'genero' => $genero,
            'hay_datos_edad' => array_sum($edad) > 0,
            'hay_datos_genero' => array_sum($genero) > 0,

            'porcentaje_general' => $porcentajeGeneral,
            'hay_datos_porcentaje' => isset($porcentajeGeneral[0]['porcentajeCorrectas']),

            'json_paises_usuarios' => json_encode($usuariosPais, JSON_THROW_ON_ERROR),
            'hay_datos_paises' => count($usuariosPais) > 0,

            'rendimiento_usuarios' => $rendimientoUsuarios
        ];
    }
}
