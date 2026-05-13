<?php
/**
 * db.php - Conexión a la base de datos MySQL
 * Generador de Hoja de Vida
 */

// ── Configuración de la base de datos ──────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Usuario de menor privilegio (creado en database.sql)
define('DB_PASS', '');   // Cambiar en producción
define('DB_NAME', 'generador_cv');
define('DB_CHARSET', 'utf8mb4');

// ── Función para obtener la conexión PDO ───────────────────
function obtenerConexion(): PDO {
    static $pdo = null; // Singleton: reutilizar la misma conexión

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST, DB_NAME, DB_CHARSET
        );

        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Lanzar excepciones en errores
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Resultados como arrays asociativos
            PDO::ATTR_EMULATE_PREPARES   => false,                    // Usar prepared statements reales
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        } catch (PDOException $e) {
            // En producción, loguear el error sin exponerlo al usuario
            error_log('Se detecto un erro de conexion en la BD: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error de conexión con la base de datos.']);
            exit;
        }
    }

    return $pdo;
}

// ── Función auxiliar: Respuesta JSON ──────────────────────
function responderJSON(array $datos, int $codigo = 200): void {
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Función: Generar ID único alfanumérico ─────────────────
function generarCodigo(int $longitud = 8): string {
    $pdo = obtenerConexion();
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    do {
        $codigo = '';
        for ($i = 0; $i < $longitud; $i++) {
            $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }
        // Verificar que no exista ya en la base de datos
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM cv WHERE codigo = ?');
        $stmt->execute([$codigo]);
        $existe = (int) $stmt->fetchColumn();
    } while ($existe > 0);

    return $codigo;
}
