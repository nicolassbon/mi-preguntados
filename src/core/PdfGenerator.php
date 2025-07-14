<?php

namespace App\core;

use Dompdf\Dompdf;
use Dompdf\Exception;

class PdfGenerator
{
    private Dompdf $dompdf;

    public function __construct()
    {
        $this->dompdf = new Dompdf();
    }

    public function generarPdf($html, $nombreArchivo = "documento.pdf", $descargar = false): void
    {
        try {
            $this->dompdf->loadHtml($html);

            $this->dompdf->setPaper('A4');

            $this->dompdf->render();

            $this->dompdf->stream($nombreArchivo, ["Attachment" => $descargar]);

            exit;
        } catch (Exception $e) {
            error_log("PdfGenerator error: " . $e->getMessage());
        }
    }

}
