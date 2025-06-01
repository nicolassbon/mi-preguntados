<?php

class RegistroController
{
  private $registroModel;
  private $ubicacionModel;
  private $view;

  public function __construct($registroModel, $ubicacionModel, $view)
  {
    $this->registroModel = $registroModel;
    $this->ubicacionModel = $ubicacionModel;
    $this->view = $view;
  }

  public function show()
  {
    $this->view->render("register", [
      'title' => 'Registrarse',
      'extra_css' => '<link rel="stylesheet" href="http://localhost/Preguntados/public/css/styles.css">
                      <link rel="stylesheet" href="http://localhost/Preguntados/public/css/register.css">'
    ]);
  }

  public function pasoMapa()
  {
    $_SESSION['registro'] = [
      'nombre' => $_POST['nombre'],
      'fecha_nac' => $_POST['fecha-nac'],
      'email' => $_POST['email'],
      'usuario' => $_POST['usuario'],
      'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
      'sexo' => $_POST['sexo']
    ];

    if (isset($_FILES['foto']) && is_uploaded_file($_FILES['foto']['tmp_name'])) {
      $fotoTmpNombre = basename($_FILES['foto']['name']);
      $destinoTmp = "uploads/tmp/" . $fotoTmpNombre;
      move_uploaded_file($_FILES['foto']['tmp_name'], $destinoTmp);
      $_SESSION['foto_temp'] = $destinoTmp;
    }

    header("Location: index.php?controller=registro&method=mostrarMapa");
  }

  public function mostrarMapa()
  {
    $this->view->render("mapa", [
      'title' => 'Registrarse',
      'extra_css' => '<link rel="stylesheet" href="http://localhost/Preguntados/public/css/styles.css">
                      <link rel="stylesheet" href="http://localhost/Preguntados/public/css/register.css">
                      <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">'
    ]);
  }

  public function guardarUbicacion()
  {
    error_log("📍 Entró al método guardarUbicacion");

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if ($data && isset($data['pais']) && isset($data['provincia'])) {
      $_SESSION['registro']['pais'] = $data['pais'];
      $_SESSION['registro']['provincia'] = $data['provincia'];
    } else {
      echo "Error al guardar la ubicacion";
      echo json_encode(['error' => 'Datos inválidos']);
    }
  }

  public function registrar()
  {

    $data = $_SESSION['registro'] ?? null;

    if (!$data) {
      echo "Datos incompletos.";
      return;
    }

    $nombreCompleto = $data['nombre'];
    $fechaNac = $data['fecha_nac'];
    $correo = $data['email'];
    $usuario = $data['usuario'];
    $contrasenaHash = $data['password'];
    $sexo = $data['sexo'];
    $sexoId = ($sexo == "masculino") ? 1 : (($sexo == "femenino") ? 2 : 3);
    $paisNombre = $data['pais'];
    $ciudadNombre = $data['provincia'];

    $fotoNombre = null;
    if (isset($_SESSION['foto_temp']) && file_exists($_SESSION['foto_temp'])) {
      $fotoNombre = basename($_SESSION['foto_temp']);
      $destinoFinal = "uploads/" . $fotoNombre;
      rename($_SESSION['foto_temp'], $destinoFinal);
    }

    $idPais = $this->ubicacionModel->obtenerOCrearPais($paisNombre);

    $idCiudad = $this->ubicacionModel->obtenerOCrearCiudad($ciudadNombre, $idPais);

    $idUsuario = $this->registroModel->registrarUsuario(
      $nombreCompleto,
      $fechaNac,
      $sexoId,
      $idPais,
      $idCiudad,
      $correo,
      $contrasenaHash,
      $usuario,
      $fotoNombre
    );


    if ($idUsuario !== null) {
      $_SESSION['id_usuario'] = $idUsuario;
      header('Location: ../email/show');
    } else {
      echo "Error al registrar usuario.";
      // Podrías redirigir a una vista de error si preferís
    }
  }
}