<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registro compacto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Baloo" rel="stylesheet" />
  <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
  <link rel="stylesheet" href="styles/styles.css">

  <style>
    .info {
      margin-left: 70px;
    }

    .form-container {
      max-width: 500px;
    }

    .sexo-group {
      margin-bottom: 1rem;
      color: #fff;
      font-weight: 500;
    }

    .sexo-group legend {
      font-size: 1rem;
      margin-bottom: 0.5rem;
    }

    .sexo-group label {
      margin-right: 15px;
      cursor: pointer;
      font-weight: 400;
      font-size: 0.9rem;
    }
  </style>
</head>

<body>
  <div class="form-container">
    <form>
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre Completo</label>
        <input type="text" class="form-control input" name="nombre" id="nombre" placeholder="Tu nombre completo" required />
      </div>

      <div class="mb-3">
        <label for="fecha-nac" class="form-label">Fecha de nacimiento</label>
        <input type="date" class="form-control input" name="fecha-nac" id="fecha-nac" required />
      </div>

      <fieldset class="sexo-group">
        <legend>Sexo</legend>
        <label><input type="radio" name="sexo" value="masculino" required /> Masculino</label>
        <label><input type="radio" name="sexo" value="femenino" /> Femenino</label>
        <label><input type="radio" name="sexo" value="prefiero-no" /> Prefiero no cargarlo</label>
      </fieldset>

      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control input" name="email" id="email" placeholder="email@ejemplo.com" required />
      </div>

      <div class="row">
        <div class="mb-3 col-md-6">
          <label for="usuario" class="form-label">Usuario</label>
          <input type="text" class="form-control input" name="usuario" id="usuario" placeholder="Nombre de usuario" required />
        </div>

        <div class="mb-3 col-md-6">
          <label for="foto" class="form-label">Foto de perfil</label>
          <input type="file" class="form-control input" name="foto" id="foto" accept="image/*" />
        </div>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control input" name="password" id="password" placeholder="Contraseña" required />
      </div>

      <button type="submit" class="btn btn-form w-100 mb-3">Registrarse</button>
    </form>
    <div class="text-center">
      <p class="">¿Ya tienes cuenta?</p>
      <a href="login.php" class="text-white text-decoration-underline">Inicia sesión</a>
    </div>
  </div>

  <div class="info">
    <div class="image-container">
      <img src="images/logo.png" alt="Logo de Preguntopolis" />
      <h1 class="title">Preguntopolis</h1>
    </div>
    <p>Regístrate para comenzar tu aventura</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>