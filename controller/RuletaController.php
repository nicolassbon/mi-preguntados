<?php

class RuletaController
{

    private $view;
    private $categoriaModel;
    private $usuarioModel;

    public function __construct($view, $categoriaModel, $usuarioModel){
        $this->view = $view;
        $this->categoriaModel = $categoriaModel;
        $this->usuarioModel = $usuarioModel;
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

        $this->view->render("ruleta", [
            'title' => 'Ruleta',
            'categorias' => $categoriasRepetidas,
            'yaGiro' => $yaGiro,
            'posicionGanadora' => $posicionGanadora,
            'trampitas' => $trampitas,
        ]);
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

    private function calcularPosicionGanadora($categoria,$categorias) : int
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
