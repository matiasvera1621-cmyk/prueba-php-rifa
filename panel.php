
<?php
session_start();

// Evitar que el navegador almacene el panel en caché
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Comprobar que exista una sesión activa
if (!isset($_SESSION['usuario_web'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel principal - Rifa</title>
</head>
<body>

    <h1>Panel principal de la rifa</h1>

    <p>
        Bienvenido,
        <?= htmlspecialchars($_SESSION['usuario_web'], ENT_QUOTES, 'UTF-8') ?>
    </p>


<p>Inicio de sesión correcto. Acceso autorizado.</p>
<p>
    <a href="rifa.php">Reservar números de la rifa</a>
</p>

<a href="api/logout.php?redirigir=1"
   style="display:inline-block;
          margin-top:20px;
          padding:12px 24px;
          background-color:#dc3545;
          color:white;
          text-decoration:none;
          border-radius:6px;
          font-family:Arial,sans-serif;">
    Cerrar sesión
</a>

</body>
</html>
