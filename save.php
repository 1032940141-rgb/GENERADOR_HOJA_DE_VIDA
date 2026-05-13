<?php
/**
 * save.php - Guarda o actualiza los datos del CV en MySQL
 * Recibe un JSON con todos los datos del formulario por POST
 * Generador de Hoja de Vida
 */

require_once 'db.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJSON(['error' => 'Método no permitido.'], 405);
}

// Leer el cuerpo JSON de la petición
$body = file_get_contents('php://input');
$datos = json_decode($body, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($datos)) {
    responderJSON(['error' => 'JSON inválido.'], 400);
}

$pdo = obtenerConexion();

try {
    $pdo->beginTransaction();

    // ── 1. Obtener o crear el registro principal del CV ────
    $codigo = isset($datos['codigo']) ? trim($datos['codigo']) : '';

    if ($codigo !== '') {
        // Verificar que el código existe
        $stmt = $pdo->prepare('SELECT id FROM cv WHERE codigo = ?');
        $stmt->execute([$codigo]);
        $fila = $stmt->fetch();

        if (!$fila) {
            responderJSON(['error' => 'CV no encontrado.'], 404);
        }
        $cvId = (int) $fila['id'];
    } else {
        // Crear nuevo CV con código único
        $codigo = generarCodigo();
        $stmt = $pdo->prepare('INSERT INTO cv (codigo) VALUES (?)');
        $stmt->execute([$codigo]);
        $cvId = (int) $pdo->lastInsertId();
    }

    // ── 2. Datos personales ────────────────────────────────
    if (isset($datos['personal'])) {
        $p = $datos['personal'];

        // Verificar si ya existe registro de datos personales
        $stmt = $pdo->prepare('SELECT id FROM datos_personales WHERE cv_id = ?');
        $stmt->execute([$cvId]);
        $existe = $stmt->fetch();

        if ($existe) {
            $stmt = $pdo->prepare('
                UPDATE datos_personales SET
                    nombres    = ?, apellidos = ?, titulo     = ?,
                    telefono   = ?, correo    = ?, ciudad     = ?,
                    pais       = ?, linkedin  = ?, portafolio = ?,
                    foto       = ?, resumen   = ?
                WHERE cv_id = ?
            ');
        } else {
            $stmt = $pdo->prepare('
                INSERT INTO datos_personales
                    (nombres, apellidos, titulo, telefono, correo, ciudad,
                     pais, linkedin, portafolio, foto, resumen, cv_id)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
            ');
        }

        $stmt->execute([
            $p['nombres']    ?? '',
            $p['apellidos']  ?? '',
            $p['titulo']     ?? '',
            $p['telefono']   ?? '',
            $p['correo']     ?? '',
            $p['ciudad']     ?? '',
            $p['pais']       ?? '',
            $p['linkedin']   ?? '',
            $p['portafolio'] ?? '',
            $p['foto']       ?? null,
            $p['resumen']    ?? '',
            $cvId,
        ]);
    }

    // ── 3. Experiencia laboral ─────────────────────────────
    if (isset($datos['experiencia']) && is_array($datos['experiencia'])) {
        // Borrar las existentes y reinsertar (más simple y seguro)
        $pdo->prepare('DELETE FROM experiencia WHERE cv_id = ?')->execute([$cvId]);

        $stmt = $pdo->prepare('
            INSERT INTO experiencia
                (cv_id, cargo, empresa, ubicacion, fecha_inicio, fecha_fin, actual, responsabilidades, logros, orden)
            VALUES (?,?,?,?,?,?,?,?,?,?)
        ');

        foreach ($datos['experiencia'] as $idx => $exp) {
            $stmt->execute([
                $cvId,
                $exp['cargo']             ?? '',
                $exp['empresa']           ?? '',
                $exp['ubicacion']         ?? '',
                $exp['fecha_inicio']      ?: null,
                $exp['fecha_fin']         ?: null,
                (int)($exp['actual']      ?? 0),
                $exp['responsabilidades'] ?? '',
                $exp['logros']            ?? '',
                $idx,
            ]);
        }
    }

    // ── 4. Formación académica ─────────────────────────────
    if (isset($datos['formacion']) && is_array($datos['formacion'])) {
        $pdo->prepare('DELETE FROM formacion WHERE cv_id = ?')->execute([$cvId]);

        $stmt = $pdo->prepare('
            INSERT INTO formacion (cv_id, titulo_obtenido, institucion, anio_graduacion, orden)
            VALUES (?,?,?,?,?)
        ');

        foreach ($datos['formacion'] as $idx => $f) {
            $stmt->execute([
                $cvId,
                $f['titulo_obtenido']  ?? '',
                $f['institucion']      ?? '',
                $f['anio_graduacion']  ?: null,
                $idx,
            ]);
        }
    }

    // ── 5. Habilidades ─────────────────────────────────────
    if (isset($datos['habilidades']) && is_array($datos['habilidades'])) {
        $pdo->prepare('DELETE FROM habilidades WHERE cv_id = ?')->execute([$cvId]);

        $stmt = $pdo->prepare('
            INSERT INTO habilidades (cv_id, nombre, tipo, nivel)
            VALUES (?,?,?,?)
        ');

        $tiposValidos  = ['tecnica', 'blanda'];
        $nivelesValidos = ['basico', 'intermedio', 'avanzado'];

        foreach ($datos['habilidades'] as $h) {
            $tipo  = in_array($h['tipo']  ?? '', $tiposValidos)   ? $h['tipo']  : 'tecnica';
            $nivel = in_array($h['nivel'] ?? '', $nivelesValidos) ? $h['nivel'] : 'intermedio';
            $stmt->execute([$cvId, $h['nombre'] ?? '', $tipo, $nivel]);
        }
    }

    // ── 6. Idiomas ─────────────────────────────────────────
    if (isset($datos['idiomas']) && is_array($datos['idiomas'])) {
        $pdo->prepare('DELETE FROM idiomas WHERE cv_id = ?')->execute([$cvId]);

        $stmt = $pdo->prepare('
            INSERT INTO idiomas (cv_id, idioma, nivel, certificacion)
            VALUES (?,?,?,?)
        ');

        $nivelesValidos = ['A1','A2','B1','B2','C1','C2','Nativo'];

        foreach ($datos['idiomas'] as $i) {
            $nivel = in_array($i['nivel'] ?? '', $nivelesValidos) ? $i['nivel'] : 'B1';
            $stmt->execute([$cvId, $i['idioma'] ?? '', $nivel, $i['certificacion'] ?? null]);
        }
    }

    // ── 7. Secciones adicionales ───────────────────────────
    if (isset($datos['adicionales']) && is_array($datos['adicionales'])) {
        $pdo->prepare('DELETE FROM secciones_adicionales WHERE cv_id = ?')->execute([$cvId]);

        $stmt = $pdo->prepare('
            INSERT INTO secciones_adicionales (cv_id, tipo, titulo, descripcion, fecha)
            VALUES (?,?,?,?,?)
        ');

        $tiposValidos = ['curso','herramienta','voluntariado','otro'];

        foreach ($datos['adicionales'] as $a) {
            $tipo = in_array($a['tipo'] ?? '', $tiposValidos) ? $a['tipo'] : 'otro';
            $stmt->execute([
                $cvId,
                $tipo,
                $a['titulo']      ?? '',
                $a['descripcion'] ?? '',
                $a['fecha']       ?? '',
            ]);
        }
    }

    $pdo->commit();

    // Responder con el código del CV para construir la URL
    responderJSON([
        'ok'     => true,
        'codigo' => $codigo,
        'url'    => 'cv.php?id=' . $codigo,
    ]);

} catch (Throwable $e) {
    $pdo->rollBack();
    error_log('Error guardando CV: ' . $e->getMessage());
    responderJSON(['error' => 'No se pudo guardar el CV. Intenta de nuevo.'], 500);
}
