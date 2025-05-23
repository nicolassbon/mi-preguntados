<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Baloo' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
  <link rel="stylesheet" href="styles/styles.css">

  <style>
    .info {
      margin-right: 50px;
    }

    .form-container {
      max-width: 420px;
    }
  </style>
</head>

<body>
  <div class="info">
    <div class="image-container">
      <img src="images/logo.png" alt="Logo de Preguntopolis">
      <h1 class="title">Preguntopolis</h1>
    </div>
    <p>Inicia sesión para continuar tu aventura</p>
  </div>

  <div class="form-container">
    <form>
      <div class="mb-4">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control input" name="email" id="email" placeholder="email@ejemplo.com" required>
      </div>
      <div class="mb-4">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control input" name="password" id="password" placeholder="Contraseña" required>
      </div>
      <button type="submit" class="btn btn-form w-100 mb-5">Iniciar sesión</button>
    </form>
    <div class="text-center">
      <p class="">¿No tienes cuenta?</p>
      <a href="register.php" class="text-white text-decoration-underline">Regístrate</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>