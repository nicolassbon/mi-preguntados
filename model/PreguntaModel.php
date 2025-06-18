<?php

class PreguntaModel
{

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function insertarReportePregunta($id_pregunta, $id_reportador, $motivo) {
        $sql = "INSERT INTO `preguntas_reportadas` (`id_pregunta`, `id_reportador`, `motivo`) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $id_pregunta, $id_reportador, $motivo);
        $stmt->execute();
    }

    public function actualizarEstadoPregunta($id_pregunta, $nuevo_estado) {
        $sql = "UPDATE `preguntas` SET `estado` = ? WHERE `id_pregunta` = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nuevo_estado, $id_pregunta);
        $stmt->execute();
    }

}