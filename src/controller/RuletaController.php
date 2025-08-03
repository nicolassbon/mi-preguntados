<?php

namespace App\controller;

use App\core\MustachePresenter;
use App\model\CategoriaModel;
use App\model\DesafioModel;
use App\model\PartidaModel;
use App\model\UsuarioModel;
use JsonException;

class RuletaController
{

    private MustachePresenter $view;
    private CategoriaModel $categoriaModel;
    private UsuarioModel $usuarioModel;
    private DesafioModel $desafioModel;
    private PartidaModel $partidaModel;

    public function __construct($view, $categoriaModel, $usuarioModel, $desafioModel, $partidaModel)
    {
        $this->view = $view;
        $this->categoriaModel = $categoriaModel;
        $this->usuarioModel = $usuarioModel;
        $this->desafioModel = $desafioModel;
        $this->partidaModel = $partidaModel;
    }

    public function show(): void
    {
        $categorias = $this->categoriaModel->getCategorias();
        $categoriasRepetidas = $this->repetirCategorias($categorias, 5);

        $yaGiro = isset($_SESSION['categoria']);
        $posicionGanadora = null;

        if ($yaGiro) {
            $posicionGanadora = $this->calcularPosicionGanadora($_SESSION['categoria'], $categorias);
        }

        $trampitas = $this->usuarioModel->getTrampitas($_SESSION['usuario_id']);

        $data = [
            'title' => 'Ruleta',
            'categorias' => $categoriasRepetidas,
            'yaGiro' => $yaGiro,
            'posicionGanadora' => $posicionGanadora,
            'trampitas' => $trampitas,
            'es_desafio' => false,
            'es_usuario_desafiante' => false,
            'ha_superado_puntaje' => false
        ];

        if (isset($_SESSION['es_desafio'], $_SESSION['desafio_id']) && $_SESSION['es_desafio'] === true) {
            $esDesafiante = $_SESSION['es_usuario_desafiante'] ?? false;
            $desafio = $this->desafioModel->obtenerDesafioPorId($_SESSION['desafio_id']);

            if ($desafio) {
                $data['es_desafio'] = true;
                $puntajeActual = $_SESSION['puntaje'] ?? 0;
                $data['puntaje_actual'] = $puntajeActual;

                if ($esDesafiante) {
                    $data['es_usuario_desafiante'] = true;
                } else {
                    $puntajeOponente = $this->partidaModel->getPuntajeFinalPartida($desafio['id_partida_desafiante']);
                    $data['puntaje_oponente'] = $puntajeOponente;
                    $data['nombre_oponente'] = $desafio['nombre_desafiante'];

                    // Lógica para verificar si se superó el puntaje
                    if ($puntajeActual > $puntajeOponente) {
                        $data['haSuperadoPuntaje'] = true;
                    }
                }
            }
        }

        $this->view->render("ruleta", $data);
    }

    /**
     * @throws JsonException
     */
    public function girar(): void
    {
        $id_partida = $_SESSION['id_partida'] ?? null;
        $categoria = $this->categoriaModel->elegirCategoriaParaPartida($id_partida);
        $_SESSION["categoria"] = $categoria;
        $categorias = $this->categoriaModel->getCategorias();

        $posicionGanadora = $this->calcularPosicionGanadora($categoria, $categorias);

        echo json_encode(['posicion' => $posicionGanadora], JSON_THROW_ON_ERROR);
    }

    private function repetirCategorias(array $categorias, int $veces): array
    {
        $resultado = [];
        for ($i = 0; $i < $veces; $i++) {
            foreach ($categorias as $item) {
                $resultado[] = $item;
            }
        }
        return $resultado;
    }

    private function calcularPosicionGanadora($categoria, $categorias): int
    {
        $indiceOriginal = array_search(
            $categoria["id_categoria"],
            array_column($categorias, "id_categoria"),
            true
        );

        $vuelta = rand(2, 5 - 2);

        return $indiceOriginal + $vuelta * count($categorias);
    }
}
