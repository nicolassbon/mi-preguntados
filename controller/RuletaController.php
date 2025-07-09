<?php

class RuletaController
{

    private $view;
    private $categoriaModel;

    public function __construct($view, $categoriaModel){
        $this->view = $view;
        $this->categoriaModel = $categoriaModel;
    }

    public function show(){

        $categorias = $this->categoriaModel->getCategorias();
        $categoriasRepetidas = $this->repetirCategorias($categorias, 5);

        $yaGiro = isset($_SESSION['categoria']);
        $posicionGanadora = null;

        if ($yaGiro) {
            $posicionGanadora = $this->calcularPosicionGanadora($_SESSION['categoria'], $categorias);
        }

        $this->view->render("ruleta", [
            'title' => 'Ruleta',
            'categorias' => $categoriasRepetidas,
            'yaGiro' => $yaGiro,
            'posicionGanadora' => $posicionGanadora
        ]);
    }

    public function girar()
    {
        $categoria = $this->categoriaModel->getCategoriaAleatoria();
        $_SESSION["categoria"] = $categoria;
        $categorias = $this->categoriaModel->getCategorias();

        $posicionGanadora = $this->calcularPosicionGanadora($categoria, $categorias);

        echo json_encode(['posicion' => $posicionGanadora], JSON_THROW_ON_ERROR);
    }

    private function repetirCategorias($categorias, $veces): array
    {
        $resultado = [];
        for ($i = 0; $i < $veces; $i++) {
            $resultado = array_merge($resultado, $categorias);
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