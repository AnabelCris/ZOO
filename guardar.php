<?php
/* ============================================================
   guardar.php — Guarda los mensajes del formulario de Contacto
   Requiere una BD `zoo` con la tabla:

   CREATE TABLE contactos (
     id        INT AUTO_INCREMENT PRIMARY KEY,
     nombre    VARCHAR(120) NOT NULL,
     correo    VARCHAR(150) NOT NULL,
     telefono  VARCHAR(40),
     tipo      VARCHAR(40),
     mensaje   TEXT NOT NULL,
     creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
   ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ============================================================ */

// Mostrar errores mientras desarrollas (quítalo en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

/* ---------- Conexión ----------
   OJO: cambia el puerto a 3306 si tu MySQL/XAMPP usa el puerto por defecto.
   no cambiar los puertos (eso complica as cosas) */
$host   = '127.0.0.1';
$user   = 'root';
$pass   = '';
$db     = 'zoo';
$puerto = 3306;

$conexion = @mysqli_connect($host, $user, $pass, $db, $puerto);
if (!$conexion) {
    http_response_code(500);
    exit('Error de conexión: ' . mysqli_connect_error());
}

// Asegura UTF-8 (acentos, ñ)
mysqli_set_charset($conexion, 'utf8mb4');

/* ---------- Datos del formulario ---------- */
$nombre   = trim($_POST['nombre']   ?? '');
$correo   = trim($_POST['correo']   ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$tipo     = trim($_POST['tipo']     ?? '');
$mensaje  = trim($_POST['mensaje']  ?? '');

if ($nombre === '' || $correo === '' || $mensaje === '') {
    http_response_code(400);
    exit('Faltan campos obligatorios.');
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Correo no válido.');
}

/* ---------- Insert con prepared statement (evita SQL injection) ---------- */
$sql  = "INSERT INTO contactos (nombre, correo, telefono, tipo, mensaje)
         VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conexion, $sql);
if (!$stmt) {
    http_response_code(500);
    exit('Error preparando la consulta: ' . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt, 'sssss', $nombre, $correo, $telefono, $tipo, $mensaje);

if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);
    exit('Error al guardar: ' . mysqli_stmt_error($stmt));
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);

/* ---------- Respuesta ---------- */
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mensaje recibido</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="section" style="text-align:center;">
    <h2 style="color:#F9A825;font-family:'Cinzel',serif;">¡Gracias por escribirnos!</h2>
    <p>Tu mensaje fue recibido correctamente. Te responderemos lo antes posible.</p>
    <a href="index.html#contacto" class="btn btn-primary">← Volver</a>
  </main>
</body>
</html>
