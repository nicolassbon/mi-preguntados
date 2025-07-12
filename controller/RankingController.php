<?php

require_once 'helpers/FechaHelper.php';

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
        $rangoFechas = FechaHelper::getRangoFechas($filtro, $_GET);
        $desde = $rangoFechas['desde'];
        $hasta = $rangoFechas['hasta'];

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

}
