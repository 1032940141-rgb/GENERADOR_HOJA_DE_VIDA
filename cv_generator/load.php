<?php
/**
 * load.php - Carga los datos de un CV existente por su código único
 * Retorna un JSON con todos los datos del CV
 * Generador de Hoja de Vida
 */

require_once 'db.php';

// Solo aceptar GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responderJSON(['error' => 'Método no permitido.'], 405);
}

// Leer y validar el código del CV
$codigo = isset($_GET['id']) ? trim($_GET['id']) : '';

if ($codigo === '' || !preg_match('/^[a-zA-Z0-9]{4,12}$/', $codigo)) {
    responderJSON(['error' => 'Código de CV inválido.'], 400);
}

$pdo = obtenerConexion();

try {
    // ── 1. Buscar el CV por código ─────────────────────────
    $stmt = $pdo->prepare('SELECT id, codigo, creado_en, actualizado_en FROM cv WHERE codigo = ?');
    $stmt->execute([$codigo]);
    $cv = $stmt->fetch();

    if (!$cv) {
        responderJSON(['error' => 'CV no encontrado.'], 404);
    }

    $cvId = (int) $cv['id'];

    // ── 2. Datos personales ────────────────────────────────
    $stmt = $pdo->prepare('SELECT * FROM datos_personales WHERE cv_id = ?');
    $stmt->execute([$cvId]);
    $personal = $stmt->fetch() ?: [];
    unset($personal['id'], $personal['cv_id']); // Limpiar campos internos

    // ── 3. Experiencia laboral ─────────────────────────────
    $stmt = $pdo->prepare('SELECT * FROM experiencia WHERE cv_id = ? ORDER BY orden ASC');
    $stmt->execute([$cvId]);
    $experiencia = $stmt->fetchAll();
    foreach ($experiencia as &$exp) { unset($exp['id'], $exp['cv_id'], $exp['orden']); }
    unset($exp);

    // ── 4. Formación académica ─────────────────────────────
    $stmt = $pdo->prepare('SELECT * FROM formacion WHERE cv_id = ? ORDER BY orden ASC');
    $stmt->execute([$cvId]);
    $formacion = $stmt->fetchAll();
    foreach ($formacion as &$f) { unset($f['id'], $f['cv_id'], $f['orden']); }
    unset($f);

    // ── 5. Habilidades ─────────────────────────────────────
    $stmt = $pdo->prepare('SELECT nombre, tipo, nivel FROM habilidades WHERE cv_id = ?');
    $stmt->execute([$cvId]);
    $habilidades = $stmt->fetchAll();

    // ── 6. Idiomas ─────────────────────────────────────────
    $stmt = $pdo->prepare('SELECT idioma, nivel, certificacion FROM idiomas WHERE cv_id = ?');
    $stmt->execute([$cvId]);
    $idiomas = $stmt->fetchAll();

    // ── 7. Secciones adicionales ───────────────────────────
    $stmt = $pdo->prepare('SELECT tipo, titulo, descripcion, fecha FROM secciones_adicionales WHERE cv_id = ?');
    $stmt->execute([$cvId]);
    $adicionales = $stmt->fetchAll();

    // ── 8. Armar respuesta completa ────────────────────────
    responderJSON([
        'ok'          => true,
        'codigo'      => $cv['codigo'],
        'creado_en'   => $cv['creado_en'],
        'actualizado' => $cv['actualizado_en'],
        'personal'    => $personal,
        'experiencia' => $experiencia,
        'formacion'   => $formacion,
        'habilidades' => $habilidades,
        'idiomas'     => $idiomas,
        'adicionales' => $adicionales,
    ]);

} catch (Throwable $e) {
    error_log('Error cargando CV: ' . $e->getMessage());
    responderJSON(['error' => 'No se pudo cargar el CV.'], 500);
}
