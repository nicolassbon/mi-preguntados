<?php

class crearPreguntaController
{
    private $view;
    private $model;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function show()
    {
        $this->view->render("crearPregunta", [
            'title' => 'Crear Pregunta'
        ]);
    }

    public function guardarPregunta(){

        $id_usuario = $_SESSION['usuario_id'];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST'
            || empty($_POST['pregunta'])
            || empty($_POST['opcion'])
            || empty($_POST['opcion2'])
            || empty($_POST['opcion3'])
            || empty($_POST['opcion4'])
            || empty($_POST['opcionCorrecta'])
            || empty($_POST['categoria'])){
            exit();
        }


        //traer datos del form
        $pregunta = $_POST['pregunta'];
        $opcion = $_POST['opcion'];
        $opcion2 = $_POST['opcion2'];
        $opcion3 = $_POST['opcion3'];
        $opcion4 = $_POST['opcion4'];

        $opcionCorrecta = $_POST['opcionCorrecta'];
        $id_categoria = $_POST['categoria'];

        //agregar pregunta a la tabla PREGUNTAS pero con un estado de SUGERIDA
        $this->model->agregarPregunta($pregunta, $id_categoria);

        //se busca el id por pregunta llegada al post para asignar a respuestas ese id
        $id_pregunta = $this->model->buscarPreguntaCreada($pregunta);

        //creo respuestas con estado ACTIVO = 0
        $this->model->agregarRespuestas($id_pregunta, $opcion, $opcion2, $opcion3, $opcion4, $opcionCorrecta);

        //agrego PREGUNTAS a sugeridas para usarlas luego en graficos de admin
        $this->model->agregarPreguntaASugeridas($id_usuario, $pregunta, $id_categoria);

        header("Location: /mensajeCreadaCorrectamente/show");
        exit;
    }




}