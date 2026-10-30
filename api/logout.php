
<?php

session_start();

// Eliminar los datos de la sesión
$_SESSION = [];

// Eliminar la cookie de sesión
if (ini_get('session.use_cookies')) {
    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $parametros['path'],
        $parametros['domain'],
        $parametros['secure'],
        $parametros['httponly']
    );
}

// Destruir la sesión
session_destroy();

// Si se accede desde el botón del panel,
// volver al formulario de inicio de sesión
if (
    isset($_GET['redirigir']) &&
    $_GET['redirigir'] === '1'
) {
    header('Location: ../index.php');
    exit;
}

// Si se utiliza desde Postman, responder con JSON
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    "ok" => true,
    "mensaje" => "Sesión cerrada correctamente."
], JSON_UNESCAPED_UNICODE);

?>
