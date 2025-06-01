<?php

class PerfilModel
{
  private $database;

  public function __construct($database)
  {
    $this->database = $database;
  }

  public function getDatos($id_usuario)
  {

    $resultado = $this->database->query("SELECT * FROM usuarios WHERE id_usuario = $id_usuario");

    return $resultado ?? [];

  }


}