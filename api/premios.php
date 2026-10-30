
<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

// Verificar el método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido. Debes usar GET."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Verificar que exista una sesión iniciada
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Debes iniciar sesión para consultar los premios."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Ubicación del archivo JSON
$archivo = __DIR__ . '/premios.json';

// Verificar que exista el archivo
if (!file_exists($archivo)) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "No se encontró el archivo de premios."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Leer los premios
$contenido = file_get_contents($archivo);
$premios = json_decode($contenido, true);

// Verificar que el JSON sea válido
if (json_last_error() !== JSON_ERROR_NONE || !is_array($premios)) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "El archivo de premios no contiene JSON válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Devolver los premios
echo json_encode([
    "ok" => true,
    "premios" => $premios
], JSON_UNESCAPED_UNICODE);

?>
