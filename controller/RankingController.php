<?php

use Couchbase\View;

class RankingController
{
    private $view;
    private $model;

    public function __construct($view, $model){
        $this->view = $view;
        $this->model = $model;
    }

    public function show(){

        $ranking = $this->model->obtenerRanking();
        $partidas = $this->model->obtenerPartidasJugadas();

        foreach ($ranking as $i => &$jugador) {
            $jugador['posicionJugador'] = ($i + 1) . '°';
        }

        foreach ($partidas as $i => &$partida) {
            $partida['posicionPartida'] = ($i + 1) . '°';
        }

        $this->view->render("ranking", [
            'title' => 'Ranking de jugadores',
            'ranking' => $ranking,
            'title2' => 'Top partidas',
            'partidas' => $partidas
        ]);
    }

}