<?php

namespace App\helpers;

use DateTime;

class FechaHelper
{
    public static function getRangoFechas(string $filtro, array $parametros = []): array
    {
        switch ($filtro) {
            case 'dia':
                $desde = (new DateTime())->setTime(0, 0)->format('Y-m-d H:i:s');
                $hasta = (new DateTime())->setTime(23, 59, 59)->format('Y-m-d H:i:s');
                break;
            case 'semana':
                $lunes = new DateTime('monday this week');
                $domingo = new DateTime('sunday this week');
                $desde = $lunes->format('Y-m-d 00:00:00');
                $hasta = $domingo->format('Y-m-d 23:59:59');
                break;
            case 'anio':
                $desde = date('Y-01-01 00:00:00');
                $hasta = date('Y-12-31 23:59:59');
                break;
            case 'personalizado':
                $desde = $parametros['desde'] ?? '';
                $hasta = $parametros['hasta'] ?? '';
                if ($desde && $hasta) {
                    $desde .= ' 00:00:00';
                    $hasta .= ' 23:59:59';
                } else {
                    $desde = date('Y-m-01 00:00:00');
                    $hasta = date('Y-m-t 23:59:59');
                }
                break;
            case 'mes':
            default:
                $desde = date('Y-m-01 00:00:00');
                $hasta = date('Y-m-t 23:59:59');
                break;
        }

        return ['desde' => $desde, 'hasta' => $hasta];
    }
}
