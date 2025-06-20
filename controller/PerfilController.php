<?php

class PerfilController
{
  private $model;
  private $view;

  public function __construct($model, $view)
  {
    $this->model = $model;
    $this->view = $view;
  }

  public function show()
  {
      $id_usuario = $_GET['idUsuario'] ?? ($_SESSION['usuario_id'] ?? null);

      if ($id_usuario === null) {
          $this->redirectTo('/login');
      }

      $datos = $this->model->getDatos($id_usuario);

      if (!empty($datos) && is_array($datos)) {
          $usuario = $datos[0];
      } else {
          $usuario = ['nombre_usuario' => 'Invitado'];
      }

      // Obtiene el host dinamico para no estar cambiandolo manualmente
      $host = $_SERVER['HTTP_HOST'];
      $es_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
      $protocolo = $es_https ? 'https' : 'http';

      $url_perfil = "$protocolo://$host/perfil?idUsuario=$id_usuario";

      $this->view->render("perfil", array_merge(
          [
              'title' => 'Perfil Usuario',
              'url_perfil' => $url_perfil
          ],
          $usuario
      ));
  }

  private function redirectTo($str)
  {
    header('Location: ' . $str);
    exit();
  }

  private function isLogueado(): bool
  {
    return !($_SESSION['usuario_id'] === null);
  }

}