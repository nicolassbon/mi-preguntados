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


        $this->view->render("ranking", [
            'title' => 'Ranking de jugadores',
            'css' => '<link rel="stylesheet" href="/public/css/ranking.css">',
            'ranking' => $ranking,
            'partidas' => $partidas
        ]);
    }

}