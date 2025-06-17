<?php

use Couchbase\View;

class RankingController
{
    private $model;
    private $view;

    public function __construct($model, $view){
        $this->model = $model;
        $this->view = $view;
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