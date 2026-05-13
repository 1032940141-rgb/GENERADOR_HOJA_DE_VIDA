-- ============================================================
-- GENERADOR DE HOJA DE VIDA - Script de Base de Datos
-- DDL: Crear tablas, DML: Datos, DCL: Permisos
-- ============================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS generador_cv
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE generador_cv;

-- ============================================================
-- DDL: CREACIÓN DE TABLAS
-- ============================================================

-- Tabla principal del CV (identificador único)
CREATE TABLE IF NOT EXISTS cv (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  codigo          VARCHAR(10)  NOT NULL UNIQUE,          -- ID alfanumérico público (ej: a3f9k2)
  creado_en       DATETIME     NOT NULL DEFAULT NOW(),
  actualizado_en  DATETIME     NOT NULL DEFAULT NOW() ON UPDATE NOW(),
  INDEX idx_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos personales
CREATE TABLE IF NOT EXISTS datos_personales (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cv_id           INT UNSIGNED NOT NULL,
  nombres         VARCHAR(100) NOT NULL,
  apellidos       VARCHAR(100) NOT NULL,
  titulo          VARCHAR(150),                           -- Título profesional
  telefono        VARCHAR(30),
  correo          VARCHAR(150),
  ciudad          VARCHAR(100),
  pais            VARCHAR(100),
  linkedin        VARCHAR(255),
  portafolio      VARCHAR(255),
  foto            LONGTEXT,                               -- Base64 de la imagen
  resumen         TEXT,                                   -- Perfil profesional
  FOREIGN KEY (cv_id) REFERENCES cv(id) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_cv_id (cv_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Experiencia laboral
CREATE TABLE IF NOT EXISTS experiencia (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cv_id           INT UNSIGNED NOT NULL,
  cargo           VARCHAR(150) NOT NULL,
  empresa         VARCHAR(150) NOT NULL,
  ubicacion       VARCHAR(150),
  fecha_inicio    DATE,
  fecha_fin       DATE,                                   -- NULL = trabajo actual
  actual          TINYINT(1)   NOT NULL DEFAULT 0,
  responsabilidades TEXT,
  logros          TEXT,
  orden           TINYINT UNSIGNED NOT NULL DEFAULT 0,
  FOREIGN KEY (cv_id) REFERENCES cv(id) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_cv_id (cv_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Formación académica
CREATE TABLE IF NOT EXISTS formacion (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cv_id           INT UNSIGNED NOT NULL,
  titulo_obtenido VARCHAR(200) NOT NULL,
  institucion     VARCHAR(200) NOT NULL,
  anio_graduacion YEAR,
  orden           TINYINT UNSIGNED NOT NULL DEFAULT 0,
  FOREIGN KEY (cv_id) REFERENCES cv(id) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_cv_id (cv_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Habilidades
CREATE TABLE IF NOT EXISTS habilidades (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cv_id           INT UNSIGNED NOT NULL,
  nombre          VARCHAR(100) NOT NULL,
  tipo            ENUM('tecnica','blanda') NOT NULL DEFAULT 'tecnica',
  nivel           ENUM('basico','intermedio','avanzado') NOT NULL DEFAULT 'intermedio',
  FOREIGN KEY (cv_id) REFERENCES cv(id) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_cv_id (cv_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Idiomas
CREATE TABLE IF NOT EXISTS idiomas (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cv_id           INT UNSIGNED NOT NULL,
  idioma          VARCHAR(80)  NOT NULL,
  nivel           ENUM('A1','A2','B1','B2','C1','C2','Nativo') NOT NULL,
  certificacion   VARCHAR(200),                           -- Opcional
  FOREIGN KEY (cv_id) REFERENCES cv(id) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_cv_id (cv_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Secciones adicionales (cursos, herramientas, voluntariados, otros)
CREATE TABLE IF NOT EXISTS secciones_adicionales (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cv_id           INT UNSIGNED NOT NULL,
  tipo            ENUM('curso','herramienta','voluntariado','otro') NOT NULL,
  titulo          VARCHAR(200) NOT NULL,
  descripcion     TEXT,
  fecha           VARCHAR(50),                            -- Flexible: "2023", "Ene 2023", etc.
  FOREIGN KEY (cv_id) REFERENCES cv(id) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_cv_id (cv_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DCL: USUARIO DE APLICACIÓN CON MENOR PRIVILEGIO
-- ============================================================
-- Crear usuario solo para la aplicación (no root)
CREATE USER IF NOT EXISTS 'cv_app'@'localhost' IDENTIFIED BY 'CvApp_2024!';

-- Otorgar solo los permisos necesarios (SELECT, INSERT, UPDATE, DELETE)
GRANT SELECT, INSERT, UPDATE, DELETE
  ON generador_cv.*
  TO 'cv_app'@'localhost';

-- Revocar permisos peligrosos explícitamente (DROP, ALTER, CREATE, etc.)
REVOKE CREATE, DROP, ALTER, INDEX, REFERENCES
  ON generador_cv.*
  FROM 'cv_app'@'localhost';

-- Aplicar cambios de privilegios
FLUSH PRIVILEGES;

-- ============================================================
-- DML: DATOS DE EJEMPLO (opcional, para probar)
-- ============================================================
-- Se puede descomentar para insertar un CV de prueba
/*
INSERT INTO cv (codigo) VALUES ('demo01');

INSERT INTO datos_personales (cv_id, nombres, apellidos, titulo, telefono, correo, ciudad, pais, resumen)
VALUES (
  1,
  'María',
  'González López',
  'Desarrolladora de Software Senior',
  '+57 300 123 4567',
  'maria.gonzalez@email.com',
  'Bogotá',
  'Colombia',
  'Desarrolladora con más de 5 años de experiencia en tecnologías web. Apasionada por crear soluciones escalables y de alta calidad. Experiencia liderando equipos ágiles y entregando proyectos en tiempo y forma.'
);
*/
