<?php

class RankingController
{
    private $view;
    private $model;

    public function __construct($view, $model)
    {
        $this->view = $view;
        $this->model = $model;
    }

    public function show(): void
    {
        $filtro = $_GET['filtro'] ?? 'mes';
        $fechas = $this->getRangoFechas($filtro, $_GET);
        $desde = $fechas['desde'];
        $hasta = $fechas['hasta'];

        $ranking = $this->model->obtenerRanking($desde, $hasta);
        $partidas = $this->model->obtenerPartidasJugadas($desde, $hasta);

        foreach ($ranking as $i => $jugador) {
            $ranking[$i]['posicionJugador'] = ($i + 1) . '°';
        }

        foreach ($partidas as $i => $partida) {
            $partidas[$i]['posicionPartida'] = ($i + 1) . '°';
        }

        $this->view->render("ranking", [
            'title' => 'Ranking de jugadores',
            'title2' => 'Top partidas',
            'ranking' => $ranking,
            'partidas' => $partidas,
            // flags para el filtro
            'filtro_Actual' => $filtro,
            'filtro_dia' => $filtro === 'dia',
            'filtro_semana' => $filtro === 'semana',
            'filtro_mes' => $filtro === 'mes',
            'filtro_anio' => $filtro === 'anio',
            'filtro_personalizado' => $filtro === 'personalizado',
            'desde' => substr($desde, 0, 10),
            'hasta' => substr($hasta, 0, 10)
        ]);
    }

    private function getRangoFechas($filtro, $parametros): array
    {
        switch ($filtro) {
            case 'dia':
                $desde = (new DateTime())->setTime(0, 0)->format('Y-m-d H:i:s');
                $hasta = (new DateTime())->setTime(23, 59, 59)->format('Y-m-d H:i:s');
                break;
            case 'semana':
                $lunes = new DateTime('monday this week');
                $domingo = new DateTime('sunday this week');
                $desde = $lunes->format('Y-m-d 00:00:00');
                $hasta = $domingo->format('Y-m-d 23:59:59');
                break;
            case 'anio':
                $desde = date('Y-01-01 00:00:00');
                $hasta = date('Y-12-31 23:59:59');
                break;
            case 'personalizado':
                $desde = $parametros['desde'] ?? '';
                $hasta = $parametros['hasta'] ?? '';
                if ($desde && $hasta) {
                    $desde .= ' 00:00:00';
                    $hasta .= ' 23:59:59';
                } else {
                    // Mes por defecto cuando selecciona personalizado
                    $desde = date('Y-m-01 00:00:00');
                    $hasta = date('Y-m-t 23:59:59');
                }
                break;
            case 'mes':
            default:
                $desde = date('Y-m-01 00:00:00');
                $hasta = date('Y-m-t 23:59:59');
                break;
        }

        return ['desde' => $desde, 'hasta' => $hasta];
    }

}
