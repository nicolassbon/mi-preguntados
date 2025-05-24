<?php

class RegistroController
{
    private $model;
    private $view;

    public function __construct($model, $view){
        $this->model = $model;
        $this->view = $view;
    }

    public function show()
    {
        $this->view->render("register");
    }

    public function registrar(){
        $nombreCompleto = $_POST['nombre'];
        $fechaNac = $_POST['fecha-nac'];
        $correo = $_POST['email'];
        $usuario = $_POST['usuario'];
        $contrasenaHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sexo = $_POST["sexo"];
        $sexoId = ($sexo == "masculino") ? 1 : (($sexo == "femenino") ? 2 : 3);
        $idPais = 1; // por ahora hardcodeado
        $idCiudad = 1;

        $foto = $_FILES["foto"]["name"];
        $temp = $_FILES["foto"]["tmp_name"];
        $destino = "uploads/" . basename($foto);
        move_uploaded_file($temp, $destino);

        $this->model->registrarUsuario(
            $nombreCompleto,
            $fechaNac,
            $sexoId,
            $idPais,
            $idCiudad,
            $correo,
            $contrasenaHash,
            $usuario,
            $foto
        );

        echo "¡Usuario registrado con éxito!";

    }
}