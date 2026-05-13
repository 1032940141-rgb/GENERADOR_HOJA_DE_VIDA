# 📄 Generador de Hoja de Vida

Plataforma web para crear, editar y compartir hojas de vida (CV) con enlace único.  
Stack: **PHP + MySQL (XAMPP) + HTML/CSS/JS + html2pdf.js**

---

## 🗂 Estructura del proyecto

```
cv_generator/
├── index.html      → Formulario principal (paso a paso)
├── cv.php          → Vista pública del CV (enlace único)
├── save.php        → API: guarda/actualiza datos en MySQL
├── load.php        → API: carga un CV existente por código
├── db.php          → Conexión PDO a MySQL
├── style.css       → Estilos del formulario
├── preview.css     → Estilos del CV (impresión/PDF)
└── database.sql    → Script completo de base de datos
```

---

## ⚙️ Instalación en XAMPP

### 1. Colocar archivos

Copia esta carpeta dentro de `C:\xampp\htdocs\` (Windows) o `/opt/lampp/htdocs/` (Linux/Mac):

```
htdocs/
└── cv_generator/    ← aquí van todos los archivos
```

### 2. Configurar la base de datos

1. Inicia **XAMPP** (Apache + MySQL)
2. Abre **phpMyAdmin**: http://localhost/phpmyadmin
3. Haz clic en **"SQL"** (pestaña superior)
4. Pega todo el contenido de `database.sql` y haz clic en **"Continuar"**

Esto crea:
- La base de datos `generador_cv`
- Todas las tablas con sus relaciones
- El usuario `cv_app` con permisos mínimos (DCL)

### 3. Verificar la conexión

Abre `db.php` y confirma que los datos coinciden con tu XAMPP:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'cv_app');        // Usuario creado en database.sql
define('DB_PASS', 'CvApp_2024!');   // ← Cambiar si lo deseas
define('DB_NAME', 'generador_cv');
```

> **Nota:** Si usas el usuario `root` de XAMPP (sin contraseña), puedes cambiar temporalmente `DB_USER` a `'root'` y `DB_PASS` a `''` para probar. Pero en producción siempre usa un usuario con menor privilegio.

### 4. Abrir la aplicación

http://localhost/cv_generator/index.html

---

## 🚀 Uso

1. **Llenar el formulario** por pasos (Personal → Experiencia → Formación → Habilidades → Idiomas → Adicional)
2. **Vista previa** en tiempo real a la derecha
3. **Guardar** → genera un enlace único: `http://localhost/cv_generator/cv.php?id=CODIGO`
4. Compartir el enlace para que otros vean el CV
5. Entrar al mismo enlace con `index.html?id=CODIGO` para editar
6. En la vista del CV: botones para **descargar PDF** o **imprimir**

---

## 🗄 Estructura de la base de datos (resumen)

| Tabla                  | Descripción                              |
|------------------------|------------------------------------------|
| `cv`                   | Registro principal con código único      |
| `datos_personales`     | Info personal, foto (base64), resumen    |
| `experiencia`          | Empleos con fechas y logros              |
| `formacion`            | Estudios académicos                      |
| `habilidades`          | Técnicas y blandas con nivel             |
| `idiomas`              | Idioma, nivel MCE y certificación        |
| `secciones_adicionales`| Cursos, herramientas, voluntariados, etc.|

### Usuario de aplicación (DCL - menor privilegio)

```sql
-- Solo tiene: SELECT, INSERT, UPDATE, DELETE
-- NO tiene: CREATE, DROP, ALTER, GRANT
CREATE USER 'cv_app'@'localhost' IDENTIFIED BY 'CvApp_2024!';
GRANT SELECT, INSERT, UPDATE, DELETE ON generador_cv.* TO 'cv_app'@'localhost';
```

---

## 🔒 Seguridad implementada

- **Prepared statements** en todas las consultas SQL (previene SQL Injection)
- **htmlspecialchars** en la salida PHP (previene XSS)
- **Validación de inputs** en PHP (tipos, longitudes, enumeraciones)
- **Usuario MySQL de menor privilegio** (principio de mínimo acceso)
- **Transacciones** al guardar (integridad de datos)

---

## 📝 Notas técnicas

- Las fotos se almacenan como **Base64** en la base de datos (simple para XAMPP local; en producción usar almacenamiento de archivos)
- El **autoguardado** se activa al cambiar de paso si ya existe un código
- La **vista previa** usa un `<iframe>` con debounce de 250ms para no saturar el render
- Los PDFs se generan con **html2pdf.js** via CDN (requiere conexión a internet)
- Para **producción**, cambiar contraseñas y configurar HTTPS

---

## 🐛 Solución de problemas

**"Error de conexión con la base de datos"**  
→ Verificar que MySQL está corriendo en XAMPP  
→ Verificar credenciales en `db.php`  
→ Ejecutar `database.sql` en phpMyAdmin

**Las fotos no aparecen en el PDF**  
→ html2pdf.js necesita que las imágenes sean Base64 (ya está implementado) o accesibles por HTTP

**La vista previa no carga los estilos**  
→ Asegurarse de que `preview.css` está en la misma carpeta que `index.html`
