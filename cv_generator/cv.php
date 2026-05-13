<?php
/**
 * cv.php - Página pública del CV con enlace único
 * Acceso: cv.php?id=CODIGO
 * Generador de Hoja de Vida
 */

require_once 'db.php';

$codigo = isset($_GET['id']) ? trim($_GET['id']) : '';

if ($codigo === '' || !preg_match('/^[a-zA-Z0-9]{4,12}$/', $codigo)) {
    http_response_code(404);
    die('CV no encontrado.');
}

$pdo = obtenerConexion();

// Cargar todos los datos del CV
$stmt = $pdo->prepare('SELECT id FROM cv WHERE codigo = ?');
$stmt->execute([$codigo]);
$cv = $stmt->fetch();

if (!$cv) {
    http_response_code(404);
    die('CV no encontrado.');
}

$cvId = (int) $cv['id'];

$stmt = $pdo->prepare('SELECT * FROM datos_personales WHERE cv_id = ?');
$stmt->execute([$cvId]);
$p = $stmt->fetch() ?: [];

$stmt = $pdo->prepare('SELECT * FROM experiencia WHERE cv_id = ? ORDER BY orden ASC');
$stmt->execute([$cvId]);
$experiencias = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM formacion WHERE cv_id = ? ORDER BY orden ASC');
$stmt->execute([$cvId]);
$formaciones = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM habilidades WHERE cv_id = ?');
$stmt->execute([$cvId]);
$habilidades = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM idiomas WHERE cv_id = ?');
$stmt->execute([$cvId]);
$idiomas = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM secciones_adicionales WHERE cv_id = ?');
$stmt->execute([$cvId]);
$adicionales = $stmt->fetchAll();

// Función para escapar HTML
function esc(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Mapas de niveles
$nivelHab = ['basico' => 33, 'intermedio' => 66, 'avanzado' => 100];
$nivelLabel = ['basico' => 'Básico', 'intermedio' => 'Intermedio', 'avanzado' => 'Avanzado'];
$nivelColor = ['A1'=>'#e8f5e9','A2'=>'#c8e6c9','B1'=>'#a5d6a7','B2'=>'#66bb6a','C1'=>'#43a047','C2'=>'#2e7d32','Nativo'=>'#1b5e20'];

$nombre_completo = esc(($p['nombres'] ?? '') . ' ' . ($p['apellidos'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $nombre_completo ?: 'Hoja de Vida' ?> - CV</title>
  <link rel="stylesheet" href="preview.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    /* Barra de acciones (no se imprime) */
    .barra-acciones {
      position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
      background: #1a1a2e; color: #fff; padding: 12px 24px;
      display: flex; align-items: center; justify-content: space-between;
      font-family: 'Segoe UI', sans-serif; box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    .barra-acciones .logo { font-weight: 700; font-size: 1rem; letter-spacing: 1px; }
    .barra-acciones .acciones { display: flex; gap: 10px; }
    .btn-accion {
      padding: 8px 18px; border: none; border-radius: 6px; cursor: pointer;
      font-size: 0.85rem; font-weight: 600; transition: all 0.2s;
    }
    .btn-editar  { background: #4f46e5; color: #fff; }
    .btn-pdf     { background: #16a34a; color: #fff; }
    .btn-imprimir{ background: #0891b2; color: #fff; }
    .btn-accion:hover { opacity: 0.85; transform: translateY(-1px); }
    .cv-wrapper { margin-top: 60px; }
    @media print { .barra-acciones { display: none !important; } .cv-wrapper { margin-top: 0; } }
  </style>
</head>
<body>

<!-- Barra de acciones -->
<div class="barra-acciones" id="barra">
  <span class="logo">📄 Generador de CV</span>
  <div class="acciones">
    <button class="btn-accion btn-editar"   onclick="editarCV()">✏️ Editar</button>
    <button class="btn-accion btn-pdf"      onclick="descargarPDF()">⬇️ PDF</button>
    <button class="btn-accion btn-imprimir" onclick="window.print()">🖨️ Imprimir</button>
  </div>
</div>

<div class="cv-wrapper">
  <div class="cv-contenedor" id="cv-para-pdf">

    <!-- ENCABEZADO -->
    <header class="cv-header">
      <?php if (!empty($p['foto'])): ?>
        <div class="cv-foto-wrap">
          <img src="<?= esc($p['foto']) ?>" alt="Foto" class="cv-foto">
        </div>
      <?php endif; ?>
      <div class="cv-header-info">
        <h1 class="cv-nombre"><?= $nombre_completo ?></h1>
        <?php if (!empty($p['titulo'])): ?>
          <p class="cv-titulo"><?= esc($p['titulo']) ?></p>
        <?php endif; ?>
        <div class="cv-contacto">
          <?php if (!empty($p['correo'])): ?>
            <span>✉ <?= esc($p['correo']) ?></span>
          <?php endif; ?>
          <?php if (!empty($p['telefono'])): ?>
            <span>📞 <?= esc($p['telefono']) ?></span>
          <?php endif; ?>
          <?php if (!empty($p['ciudad']) || !empty($p['pais'])): ?>
            <span>📍 <?= esc(implode(', ', array_filter([$p['ciudad'] ?? '', $p['pais'] ?? '']))) ?></span>
          <?php endif; ?>
          <?php if (!empty($p['linkedin'])): ?>
            <span>🔗 <a href="<?= esc($p['linkedin']) ?>" target="_blank">LinkedIn</a></span>
          <?php endif; ?>
          <?php if (!empty($p['portafolio'])): ?>
            <span>🌐 <a href="<?= esc($p['portafolio']) ?>" target="_blank">Portafolio</a></span>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <div class="cv-cuerpo">
      <!-- Columna izquierda -->
      <aside class="cv-sidebar">

        <?php if (!empty($habilidades)): ?>
        <section class="cv-seccion">
          <h2 class="cv-sec-titulo">Habilidades</h2>
          <?php
          $tecnicas = array_filter($habilidades, fn($h) => $h['tipo'] === 'tecnica');
          $blandas  = array_filter($habilidades, fn($h) => $h['tipo'] === 'blanda');
          ?>
          <?php if ($tecnicas): ?><p class="hab-subtitulo">Técnicas</p><?php endif; ?>
          <?php foreach ($tecnicas as $h): ?>
            <div class="habilidad-item">
              <span class="hab-nombre"><?= esc($h['nombre']) ?></span>
              <div class="hab-barra-bg">
                <div class="hab-barra" style="width:<?= $nivelHab[$h['nivel']] ?? 50 ?>%"></div>
              </div>
              <span class="hab-nivel"><?= $nivelLabel[$h['nivel']] ?? '' ?></span>
            </div>
          <?php endforeach; ?>
          <?php if ($blandas): ?><p class="hab-subtitulo" style="margin-top:10px">Blandas</p><?php endif; ?>
          <?php foreach ($blandas as $h): ?>
            <div class="habilidad-item">
              <span class="hab-nombre"><?= esc($h['nombre']) ?></span>
              <div class="hab-barra-bg">
                <div class="hab-barra hab-blanda" style="width:<?= $nivelHab[$h['nivel']] ?? 50 ?>%"></div>
              </div>
              <span class="hab-nivel"><?= $nivelLabel[$h['nivel']] ?? '' ?></span>
            </div>
          <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <?php if (!empty($idiomas)): ?>
        <section class="cv-seccion">
          <h2 class="cv-sec-titulo">Idiomas</h2>
          <?php foreach ($idiomas as $i): ?>
            <div class="idioma-item">
              <span class="idioma-nombre"><?= esc($i['idioma']) ?></span>
              <span class="idioma-nivel" style="background:<?= $nivelColor[$i['nivel']] ?? '#eee' ?>"><?= esc($i['nivel']) ?></span>
              <?php if (!empty($i['certificacion'])): ?>
                <div class="idioma-cert">📜 <?= esc($i['certificacion']) ?></div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <?php
        $cursos = array_filter($adicionales, fn($a) => $a['tipo'] === 'curso');
        $herramientas = array_filter($adicionales, fn($a) => $a['tipo'] === 'herramienta');
        ?>

        <?php if ($herramientas): ?>
        <section class="cv-seccion">
          <h2 class="cv-sec-titulo">Herramientas</h2>
          <?php foreach ($herramientas as $a): ?>
            <div class="adicional-chip"><?= esc($a['titulo']) ?></div>
          <?php endforeach; ?>
        </section>
        <?php endif; ?>

      </aside>

      <!-- Columna derecha (contenido principal) -->
      <main class="cv-main">

        <?php if (!empty($p['resumen'])): ?>
        <section class="cv-seccion">
          <h2 class="cv-sec-titulo">Perfil Profesional</h2>
          <p class="cv-resumen"><?= nl2br(esc($p['resumen'])) ?></p>
        </section>
        <?php endif; ?>

        <?php if (!empty($experiencias)): ?>
        <section class="cv-seccion">
          <h2 class="cv-sec-titulo">Experiencia Laboral</h2>
          <?php foreach ($experiencias as $exp): ?>
            <div class="timeline-item">
              <div class="timeline-dot"></div>
              <div class="timeline-contenido">
                <div class="timeline-header">
                  <strong><?= esc($exp['cargo']) ?></strong>
                  <span class="timeline-fecha">
                    <?php
                    $fi = $exp['fecha_inicio'] ? date('M Y', strtotime($exp['fecha_inicio'])) : '';
                    $ff = $exp['actual'] ? 'Presente' : ($exp['fecha_fin'] ? date('M Y', strtotime($exp['fecha_fin'])) : '');
                    echo esc(implode(' – ', array_filter([$fi, $ff])));
                    ?>
                  </span>
                </div>
                <div class="timeline-empresa">
                  <?= esc($exp['empresa']) ?>
                  <?php if (!empty($exp['ubicacion'])): ?> · <?= esc($exp['ubicacion']) ?><?php endif; ?>
                </div>
                <?php if (!empty($exp['responsabilidades'])): ?>
                  <p class="timeline-desc"><?= nl2br(esc($exp['responsabilidades'])) ?></p>
                <?php endif; ?>
                <?php if (!empty($exp['logros'])): ?>
                  <p class="timeline-logros">✓ <?= nl2br(esc($exp['logros'])) ?></p>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <?php if (!empty($formaciones)): ?>
        <section class="cv-seccion">
          <h2 class="cv-sec-titulo">Formación Académica</h2>
          <?php foreach ($formaciones as $f): ?>
            <div class="timeline-item">
              <div class="timeline-dot"></div>
              <div class="timeline-contenido">
                <div class="timeline-header">
                  <strong><?= esc($f['titulo_obtenido']) ?></strong>
                  <?php if (!empty($f['anio_graduacion'])): ?>
                    <span class="timeline-fecha"><?= esc($f['anio_graduacion']) ?></span>
                  <?php endif; ?>
                </div>
                <div class="timeline-empresa"><?= esc($f['institucion']) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <?php if ($cursos): ?>
        <section class="cv-seccion">
          <h2 class="cv-sec-titulo">Cursos y Certificaciones</h2>
          <?php foreach ($cursos as $c): ?>
            <div class="adicional-item">
              <strong><?= esc($c['titulo']) ?></strong>
              <?php if (!empty($c['fecha'])): ?><span class="adicional-fecha"><?= esc($c['fecha']) ?></span><?php endif; ?>
              <?php if (!empty($c['descripcion'])): ?><p><?= esc($c['descripcion']) ?></p><?php endif; ?>
            </div>
          <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <?php
        $voluntariados = array_filter($adicionales, fn($a) => $a['tipo'] === 'voluntariado');
        $otros = array_filter($adicionales, fn($a) => $a['tipo'] === 'otro');
        ?>

        <?php if ($voluntariados): ?>
        <section class="cv-seccion">
          <h2 class="cv-sec-titulo">Voluntariado</h2>
          <?php foreach ($voluntariados as $v): ?>
            <div class="adicional-item">
              <strong><?= esc($v['titulo']) ?></strong>
              <?php if (!empty($v['fecha'])): ?><span class="adicional-fecha"><?= esc($v['fecha']) ?></span><?php endif; ?>
              <?php if (!empty($v['descripcion'])): ?><p><?= esc($v['descripcion']) ?></p><?php endif; ?>
            </div>
          <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <?php if ($otros): ?>
        <section class="cv-seccion">
          <h2 class="cv-sec-titulo">Otros</h2>
          <?php foreach ($otros as $o): ?>
            <div class="adicional-item">
              <strong><?= esc($o['titulo']) ?></strong>
              <?php if (!empty($o['descripcion'])): ?><p><?= esc($o['descripcion']) ?></p><?php endif; ?>
            </div>
          <?php endforeach; ?>
        </section>
        <?php endif; ?>

      </main>
    </div><!-- /.cv-cuerpo -->

  </div><!-- /#cv-para-pdf -->
</div><!-- /.cv-wrapper -->

<script>
  const codigoCV = '<?= esc($codigo) ?>';

  // Editar: redirigir al formulario con el código cargado
  function editarCV() {
    window.location.href = 'index.html?id=' + codigoCV;
  }

  // Descargar PDF con html2pdf.js
  function descargarPDF() {
    const barra = document.getElementById('barra');
    barra.style.display = 'none';

    const elemento = document.getElementById('cv-para-pdf');
    const opciones = {
      margin:       [10, 10, 10, 10],
      filename:     'cv_<?= esc($codigo) ?>.pdf',
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2, useCORS: true, logging: false },
      jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
      pagebreak:    { mode: ['avoid-all', 'css'] }
    };

    html2pdf()
      .set(opciones)
      .from(elemento)
      .save()
      .then(() => { barra.style.display = 'flex'; });
  }
</script>
</body>
</html>
