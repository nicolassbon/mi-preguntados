<?php

use JetBrains\PhpStorm\NoReturn;

class RegistroController
{
    private $view;
    private $emailSender;
    private $usuarioModel;
    private $ubicacionModel;
    private $rolModel;


    public function __construct($view, $emailSender, $usuarioModel, $ubicacionModel, $rolModel)
    {
        $this->usuarioModel = $usuarioModel;
        $this->ubicacionModel = $ubicacionModel;
        $this->rolModel = $rolModel;
        $this->view = $view;
        $this->emailSender = $emailSender;
    }

    public function show(): void
    {
        $this->view->render("register", [
            'title' => 'Registrarse'
        ]);
    }

    #[NoReturn] public function pasoMapa(): void
    {
        $password = $_POST['password'] ?? '';

        if (!$this->esPasswordValida($password)) {
            $this->view->render("register", [
                'title' => 'Registrarse',
                'error' => 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una minúscula y un número.',
                'datosPrevios' => $_POST
            ]);
            return;
        }

        $_SESSION['registro'] = [
            'nombre' => $_POST['nombre'],
            'fecha_nac' => $_POST['fecha-nac'],
            'email' => $_POST['email'],
            'usuario' => $_POST['usuario'],
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'sexo' => $_POST['sexo']
        ];

        if (isset($_FILES['foto']) && is_uploaded_file($_FILES['foto']['tmp_name'])) {
            $fotoTmpNombre = basename($_FILES['foto']['name']);
            $destinoTmp = __DIR__ . '/../public/uploads/tmp/' . $fotoTmpNombre; // ruta absoluta

            if (!is_dir(dirname($destinoTmp))) {
                mkdir(dirname($destinoTmp), 0755, true);
            }

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destinoTmp)) {
                $_SESSION['foto_temp'] = $destinoTmp;
            }
        }

        $this->redirectTo("/registro/mapa");
    }

    private function esPasswordValida(string $password): bool
    {
        $minLength = 8;
        $tieneMayuscula = preg_match('/[A-Z]/', $password);
        $tieneMinuscula = preg_match('/[a-z]/', $password);
        $tieneNumero = preg_match('/[0-9]/', $password);

        return strlen($password) >= $minLength && $tieneMayuscula && $tieneMinuscula && $tieneNumero;
    }

    public function mapa(): void
    {
        $this->view->render("mapaRegistro", [
            'title' => 'Elige tu ubicación'
        ]);
    }

    /**
     * @throws JsonException
     */
    public function getUbicacion(): void
    {
        if (!isset($_GET['lat'], $_GET['lng'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Coordenadas no válidas'], JSON_THROW_ON_ERROR);
            return;
        }

        $lat = $_GET['lat'];
        $lng = $_GET['lng'];

        // Usar la API de Nominatim
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lng&zoom=10&addressdetails=1";

        $opts = [
            "http" => [
                "header" => "User-Agent: /1.0\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            http_response_code(500);
            echo json_encode(['error' => 'No se pudo obtener ubicación'], JSON_THROW_ON_ERROR);
            return;
        }

        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        $pais = $data['address']['country'] ?? 'Desconocido';
        $provincia = $data['address']['state'] ?? 'Desconocido';

        $this->responderJson([
            'pais' => $pais,
            'provincia' => $provincia
        ]);
    }

    public function procesar(): void
    {
        // Recuperar datos del registro que estan en session
        $r = $_SESSION['registro'] ?? null;

        // Datos mapa (POST)
        $lat = $_POST['latitud'] ?? null;
        $lng = $_POST['longitud'] ?? null;

        // Obtener país y ciudad desde backend
        $ubicacion = $this->ubicacionModel->obtenerPaisYCiudadPorCoordenadas($lat, $lng);

        if (!$ubicacion || $ubicacion['pais'] === 'Desconocido' || $ubicacion['ciudad'] === 'Desconocido') {
            $this->view->render("mapaRegistro", [
                'title' => 'Elige tu ubicacion',
                'error' => 'Seleccioná una ubicación válida en el mapa.'
            ]);
            return;
        }

        // Crear / Obtener ids
        $idPais = $this->ubicacionModel->obtenerOCrearPais($ubicacion['pais']);
        $idCiudad = $this->ubicacionModel->obtenerOCrearCiudad($ubicacion['ciudad'], $idPais);

        $fotoNombre = null;
        if (isset($_SESSION['foto_temp']) && file_exists($_SESSION['foto_temp'])) {
            $fotoNombre = basename($_SESSION['foto_temp']);
            $destinoFinal = "public/uploads/" . $fotoNombre;
            rename($_SESSION['foto_temp'], $destinoFinal);
        }

        $sexo = 3;
        if ($r['sexo'] === 'masculino') {
            $sexo = 1;
        } elseif ($r['sexo'] === 'femenino') {
            $sexo = 2;
        }

        $res = $this->usuarioModel->registrarUsuario(
            $r['nombre'],
            $r['fecha_nac'],
            $sexo,
            $idPais,
            $idCiudad,
            $r['email'],
            $r['password'],
            $r['usuario'],
            $fotoNombre,
            $lat,
            $lng
        );
        if (!$res['idUsuario']) {
            $this->renderErrorView('Error', 'No pudimos crear tu cuenta.');
            return;
        }

        // Asignar rol y enviar email
        $this->rolModel->asignarRolJugador($res['idUsuario']);
        $body = $this->generateEmailBodyFor($res['nombreUsuario'], $res['token'], $res['idUsuario']);
        $this->emailSender->send($res['email'], $body);

        $_SESSION['id_usuario'] = $res['idUsuario'];
        $_SESSION['email'] = $res['email'];
        unset($_SESSION['registro'], $_SESSION['foto_temp']);
        $this->redirectTo('/registro/success');
    }

    public function success(): void
    {
        $email = $_SESSION['email'];
        $this->view->render("registroSuccess", [
            'title' => 'Validacion',
            'email' => $email
        ]);
    }

    public function verificar(): void
    {
        $idVerificador = $_GET["idVerificador"];
        $idUsuario = $_GET["idUsuario"];

        // EN LA BASE TENGO PARA ESE USUARIO UN VALOR RANDOM
        // Si coincide el random con el idUsuario de la bdd, voy a cambiar el validado a true
        $verificado = $this->usuarioModel->verificarEmailUsuario($idVerificador, $idUsuario);

        if (!$verificado) {
            $this->renderErrorView('Error de validación', 'Ocurrio un error en la validacion del correo. Verifica tus credenciales.');
            return;
        }

        $this->view->render("verificacionSuccess", [
            'title' => '¡Cuenta verificada!',
            'message' => 'Tu correo ha sido validado correctamente. Ya podés ingresar al sistema.'
        ]);
    }

    #[NoReturn] private function redirectTo($str): void
    {
        header('Location: ' . $str);
        exit();
    }

    private function generateEmailBodyFor($userName, $token, $idUsuario): string
    {
        $url = "http://localhost/registro/verificar?idUsuario=$idUsuario&idVerificador=$token";
        return "
    <body>
      <p>Hola $userName,</p>
      <p>Gracias por registrarte. Por favor, valida tu cuenta haciendo click en el siguiente enlace:</p>
      <p><a href='$url'>Validar cuenta</a></p>
    </body>
  ";
    }

    private function renderErrorView($title, $message): void
    {
        $this->view->render("error", [
            'title' => $title,
            'message' => $message
        ]);
    }

    /**
     * @throws JsonException
     */
    public function checkEmail(): void
    {
        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            $this->responderJson(['exists' => false]);
            return;
        }

        $exists = $this->usuarioModel->existeEmail($email);
        $this->responderJson(['exists' => $exists]);
    }

    /**
     * @throws JsonException
     */
    public function checkUsuario(): void
    {
        $usuario = $_POST['usuario'] ?? '';

        if (empty($usuario)) {
            $this->responderJson(['exists' => false]);
            return;
        }

        $exists = $this->usuarioModel->existeUsuario($usuario);
        $this->responderJson(['exists' => $exists]);
    }

    /**
     * @throws JsonException
     */
    private function responderJson(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }
}

