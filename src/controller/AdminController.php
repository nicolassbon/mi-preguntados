<?php

namespace App\controller;

use App\core\MustachePresenter;
use App\core\PdfGenerator;
use App\helpers\FechaHelper;
use App\model\AdminModel;
use JsonException;

class AdminController
{

    private MustachePresenter $view;
    private PdfGenerator $pdfGenerator;
    private AdminModel $adminModel;

    public function __construct($view, $pdfGenerator, $adminModel)
    {
        $this->view = $view;
        $this->pdfGenerator = $pdfGenerator;
        $this->adminModel = $adminModel;
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

        $data['grafico_edad'] = $_POST['graficoEdad'] ?? null;
        $data['grafico_genero'] = $_POST['graficoGenero'] ?? null;
        $data['grafico_paises'] = $_POST['graficoTortaPaises'] ?? null;

        $data['hay_graficos'] = !empty($data['grafico_edad']) || !empty($data['grafico_genero']) || !empty($data['grafico_paises']);

        $html = $this->view->renderToString('panelAdminPdf', $data);
        $this->pdfGenerator->generarPdf($html, "Dashboard.pdf");
    }

    /**
     * @throws JsonException
     */
    private function prepararDatosPanel($filtro, $parametros): array
    {
        $rangoFechas = FechaHelper::getRangoFechas($filtro, $parametros);
        $desde = $rangoFechas['desde'];
        $hasta = $rangoFechas['hasta'];

        $edad = $this->mapearEdad($this->adminModel->obtenerDistribucionPorRangoEdad($desde, $hasta));
        $genero = $this->mapearGenero($this->adminModel->obtenerDistribucionPorGenero($desde, $hasta));

        $usuariosPais = $this->adminModel->obtenerUsuariosPorPaisPorFecha($desde, $hasta);
        $rendimientoUsuarios = $this->adminModel->obtenerRendimientosUsuarios($desde, $hasta);
        $balanceTrampitas = $this->adminModel->obtenerBalanceTrampitasPorUsuarioConFecha($desde, $hasta);
        $gananciaTrampitas = $this->adminModel->obtenerGananciaTotalTrampitas($desde, $hasta);

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

            'total_jugadores' => $this->adminModel->obtenerTotalUsuarios(),
            'total_jugadores_nuevos' => $this->adminModel->obtenerTotalUsuariosNuevosPorFecha($desde, $hasta),
            'partidas_jugadas' => $this->adminModel->obtenerPartidasJugadasPorFecha($desde, $hasta),
            'total_preguntas' => $this->adminModel->obtenerPreguntasActivas(),
            'total_preguntas_creadas' => $this->adminModel->obtenerPreguntasActivasPorFecha($desde, $hasta),

            'edad' => $edad,
            'genero' => $genero,
            'hay_datos_edad' => array_sum($edad) > 0,
            'hay_datos_genero' => array_sum($genero) > 0,

            'json_paises_usuarios' => json_encode($usuariosPais, JSON_THROW_ON_ERROR),
            'hay_datos_paises' => count($usuariosPais) > 0,

            'rendimiento_usuarios' => $rendimientoUsuarios,
            'hay_rendimiento_usuarios' => count($rendimientoUsuarios) > 0,

            'balance_trampitas' => $balanceTrampitas,
            'hay_balance_trampitas' => count($balanceTrampitas) > 0,
            'ganancia_trampitas' => $gananciaTrampitas
        ];
    }

    private function mapearEdad(array $datos): array
    {
        $edad = ['menor' => 0, 'media' => 0, 'mayor' => 0];
        foreach ($datos as $fila) {
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
        return $edad;
    }

    private function mapearGenero(array $datos): array
    {
        $genero = ['femenino' => 0, 'masculino' => 0, 'otro' => 0];
        foreach ($datos as $fila) {
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
        return $genero;
    }
}
