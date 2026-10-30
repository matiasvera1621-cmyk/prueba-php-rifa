<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

// Verificar el método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido. Debes usar GET."
    ]);

    exit;
}

// Verificar que exista una sesión iniciada
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Debes iniciar sesión para consultar los participantes."
    ]);

    exit;
}

// Ubicación del archivo JSON
$archivo = __DIR__ . '/participantes.json';

// Verificar que exista el archivo
if (!file_exists($archivo)) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "No se encontró el archivo de participantes."
    ]);

    exit;
}

// Leer los participantes
$contenido = file_get_contents($archivo);
$participantes = json_decode($contenido, true);

// Verificar que el JSON sea válido
if (!is_array($participantes)) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "El archivo de participantes no contiene JSON válido."
    ]);

    exit;
}

// Devolver los participantes
echo json_encode([
    "ok" => true,
    "participantes" => $participantes
], JSON_UNESCAPED_UNICODE);

?>