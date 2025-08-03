<?php

namespace App\model;

use App\core\Database;

class DesafioModel
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function crearDesafio(int $idDesafiante, int $idDesafiado, int $idPartidaDesafiante): int
    {
        $sql = "
            INSERT INTO partidas_desafiadas (
                id_usuario_desafiante,
                id_partida_desafiante,
                id_usuario_desafiado,
                estado,
                fecha_expiracion
            )
            VALUES (?, ?, ?, 'pendiente', ?)
        ";

        $fechaExpiracion = date("Y-m-d H:i:s", strtotime("+3 days"));

        $this->db->execute($sql, [$idDesafiante, $idPartidaDesafiante, $idDesafiado, $fechaExpiracion], "iiis");

        return $this->db->getLastInsertId();
    }

    public function rechazarDesafio(int $idDesafio): bool
    {
        $sql = "UPDATE partidas_desafiadas SET estado = 'rechazado' WHERE id_desafio = ?";
        return $this->db->execute($sql, [$idDesafio], "i");
    }

    public function obtenerDesafioPorId(int $idDesafio): ?array
    {
        $query = "SELECT d.*, u.nombre_usuario as nombre_desafiante
                  FROM partidas_desafiadas d
                  JOIN usuarios u ON d.id_usuario_desafiante = u.id_usuario
                  WHERE d.id_desafio = ? LIMIT 1";
        $resultado = $this->db->query($query, [$idDesafio], "i");
        return $resultado[0] ?? null;
    }

    public function obtenerDesafiosPorUsuario(int $idUsuario, ?string $estadoFiltro = null, int $limit = 10, int $offset = 0): array
    {
        [$filtroSql, $finalParams, $finalTypes] = $this->prepararFiltrosYParametros($idUsuario, $estadoFiltro);

        $sql = "
            (SELECT d.*, u.nombre_usuario as nombre_oponente, u.foto_perfil_url as foto_oponente, 'desafiado' as rol,
                    p.puntaje_final as puntaje_desafiante, pd.puntaje_final as puntaje_desafiado
             FROM partidas_desafiadas d
             JOIN usuarios u ON d.id_usuario_desafiante = u.id_usuario
             JOIN partidas p ON d.id_partida_desafiante = p.id_partida
             LEFT JOIN partidas pd ON d.id_partida_desafiado = pd.id_partida
             WHERE d.id_usuario_desafiado = ? " . ($estadoFiltro ? str_replace("?", "?", $filtroSql) : "") . "
            )
            UNION ALL
            (SELECT d.*, u.nombre_usuario as nombre_oponente, u.foto_perfil_url as foto_oponente, 'desafiante' as rol,
                    p.puntaje_final as puntaje_desafiante, pd.puntaje_final as puntaje_desafiado
             FROM partidas_desafiadas d
             JOIN usuarios u ON d.id_usuario_desafiado = u.id_usuario
             JOIN partidas p ON d.id_partida_desafiante = p.id_partida
             LEFT JOIN partidas pd ON d.id_partida_desafiado = pd.id_partida
             WHERE d.id_usuario_desafiante = ? " . ($estadoFiltro ? str_replace("?", "?", $filtroSql) : "") . "
            )
            ORDER BY fecha_creacion DESC
            LIMIT ? OFFSET ?";

        $finalParams[] = $limit;
        $finalParams[] = $offset;
        $finalTypes .= "ii";

        $resultados = $this->db->query($sql, $finalParams, $finalTypes);

        // Asignar nombres segun rol
        foreach ($resultados as &$res) {
            if ($res['rol'] === 'desafiado') {
                $res['nombre_desafiante'] = $res['nombre_oponente'];
                $res['foto_desafiante'] = $res['foto_oponente'];
            } else {
                $res['nombre_desafiado'] = $res['nombre_oponente'];
                $res['foto_desafiado'] = $res['foto_oponente'];
            }
        }
        unset($res);

        return $resultados ?: [];
    }

    public function contarDesafiosPorUsuario(int $idUsuario, ?string $estadoFiltro = null): int
    {
        [$filtroSql, $finalParams, $finalTypes] = $this->prepararFiltrosYParametros($idUsuario, $estadoFiltro);

        $sql = "SELECT COUNT(*) as total FROM (
            (SELECT d.id_desafio FROM partidas_desafiadas d WHERE d.id_usuario_desafiado = ? " . str_replace("?", "?", $filtroSql) . ")
            UNION ALL
            (SELECT d.id_desafio FROM partidas_desafiadas d WHERE d.id_usuario_desafiante = ? " . str_replace("?", "?", $filtroSql) . ")
        ) as desafios_totales";

        $resultado = $this->db->query($sql, $finalParams, $finalTypes);
        return $resultado[0]['total'] ?? 0;
    }

    public function vincularPartidaDesafiado(int $idDesafio, int $idPartidaDesafiado): bool
    {
        $sql = "UPDATE partidas_desafiadas
            SET id_partida_desafiado = ?,
                estado = 'en_curso'
            WHERE id_desafio = ?";

        return $this->db->execute($sql, [$idPartidaDesafiado, $idDesafio], "ii");
    }

    public function finalizarDesafio(int $idDesafio, string $resultado): bool
    {
        // Obtener los IDs de los usuarios del desafío
        $desafio = $this->obtenerDesafioPorId($idDesafio);

        if (!$desafio) {
            return false;
        }

        $idUsuarioGanador = null;

        // Determinar el ganador según el resultado
        if ($resultado === 'gano_desafiante') {
            $idUsuarioGanador = $desafio['id_usuario_desafiante'];
        } elseif ($resultado === 'gano_desafiado') {
            $idUsuarioGanador = $desafio['id_usuario_desafiado'];
        }

        $sql = "UPDATE partidas_desafiadas 
            SET estado = 'finalizado',
                id_usuario_ganador = ?,
                resultado = ?,
                fecha_finalizacion = CURRENT_TIMESTAMP
            WHERE id_desafio = ?";

        return $this->db->execute($sql, [$idUsuarioGanador, $resultado, $idDesafio], "isi");
    }

    public function obtenerIdDesafioPorPartida(int $idPartida)
    {
        // Buscar primero como partida de desafiado
        $sql = "SELECT id_desafio FROM partidas_desafiadas WHERE id_partida_desafiado = ? LIMIT 1";
        $resultado = $this->db->query($sql, [$idPartida], "i");

        if (!empty($resultado)) {
            return $resultado[0]['id_desafio'];
        }

        // Si no encuentra, buscar como partida de desafiante
        $sql = "SELECT id_desafio FROM partidas_desafiadas WHERE id_partida_desafiante = ? LIMIT 1";
        $resultado = $this->db->query($sql, [$idPartida], "i");

        return $resultado[0]['id_desafio'] ?? null;
    }

    public function determinarResultadoDesafio(int $idPartidaDesafiado, int $idDesafio, int $puntajeDesafiado, int $puntajeDesafiante): array
    {
        $desafio = $this->obtenerDesafioPorId($idDesafio);

        if (!$desafio || !isset($desafio['id_partida_desafiante'])) {
            return [
                'resultado' => null,
                'puntajeDesafiante' => 0,
                'puntajeDesafiado' => 0
            ];
        }

        // Determinar ganador
        if ($puntajeDesafiado > $puntajeDesafiante) {
            $resultado = 'gano_desafiado';
        } elseif ($puntajeDesafiado < $puntajeDesafiante) {
            $resultado = 'gano_desafiante';
        } else {
            $resultado = 'empate';
        }

        return [
            'resultado' => $resultado,
            'puntajeDesafiante' => $puntajeDesafiante,
            'puntajeDesafiado' => $puntajeDesafiado
        ];
    }

    public function actualizarDesafiosExpirados(): bool
    {
        $sql = "UPDATE partidas_desafiadas
            SET estado = 'expirado'
            WHERE estado = 'pendiente'
            AND fecha_expiracion < NOW()";

        return $this->db->execute($sql);
    }

    public function obtenerDesafioEnCurso(int $idUsuario): ?array
    {
        $sql = "SELECT
                d.*,
                IF(d.id_usuario_desafiante = ?, 'desafiante', 'desafiado') as rol
            FROM partidas_desafiadas d
            WHERE (d.id_usuario_desafiante = ? OR d.id_usuario_desafiado = ?)
            AND (d.estado = 'aceptado' OR d.estado = 'en_progreso')
            ORDER BY d.fecha_creacion DESC
            LIMIT 1";

        $params = [$idUsuario, $idUsuario, $idUsuario];
        $result = $this->db->query($sql, $params);

        return $result ? $result[0] : null;
    }

    private function prepararFiltrosYParametros(int $idUsuario, ?string $estadoFiltro): array
    {
        $params = [];
        $types = "";
        $filtroSql = "";

        if ($estadoFiltro) {
            if ($estadoFiltro === 'finalizado') {
                $filtroSql = " AND d.estado IN ('finalizado', 'expirado') ";
            } else {
                $filtroSql = " AND d.estado = ? ";
                $params[] = $estadoFiltro;
                $types .= "s";
            }
        }

        $finalParams = [];
        $finalTypes = "";

        $finalParams[] = $idUsuario;
        $finalTypes .= "i";
        if ($estadoFiltro && $estadoFiltro !== 'finalizado') {
            $finalParams[] = $estadoFiltro;
            $finalTypes .= "s";
        }

        $finalParams[] = $idUsuario;
        $finalTypes .= "i";
        if ($estadoFiltro && $estadoFiltro !== 'finalizado') {
            $finalParams[] = $estadoFiltro;
            $finalTypes .= "s";
        }

        return [$filtroSql, $finalParams, $finalTypes];
    }

}
