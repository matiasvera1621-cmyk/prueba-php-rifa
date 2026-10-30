<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido. Debes usar POST."
    ]);

    exit;
}

$usuario = $_POST['usuario'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

if ($usuario === 'userupla' && $contrasena === 'up1a123') {

    $_SESSION['usuario'] = $usuario;

    echo json_encode([
        "ok" => true,
        "mensaje" => "Inicio de sesión correcto"
    ]);

} else {

    http_response_code(401);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Usuario o contraseña incorrectos"
    ]);
}

?>