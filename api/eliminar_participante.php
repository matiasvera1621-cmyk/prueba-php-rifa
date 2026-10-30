
<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

// 1. Verificar el método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Debes utilizar el método GET."
    ]);

    exit;
}

// 2. Verificar que exista una sesión iniciada
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Debes iniciar sesión para eliminar participantes."
    ]);

    exit;
}

// 3. Obtener y validar el ID
$id = $_GET['id'] ?? '';

if ($id === '' || !ctype_digit($id) || (int)$id <= 0) {
    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Debes proporcionar un ID válido."
    ]);

    exit;
}

// 4. Ubicar el archivo JSON
$archivo = __DIR__ . '/participantes.json';

if (!file_exists($archivo) || !is_writable($archivo)) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "No se puede acceder al archivo de participantes."
    ]);

    exit;
}

// 5. Leer los participantes
$contenido = file_get_contents($archivo);
$participantes = json_decode($contenido, true);

if (!is_array($participantes)) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "El archivo JSON no contiene datos válidos."
    ]);

    exit;
}

// 6. Buscar y eliminar al participante
$encontrado = false;

foreach ($participantes as $indice => $participante) {

    if (
        isset($participante['id']) &&
        (int)$participante['id'] === (int)$id
    ) {
        unset($participantes[$indice]);
        $encontrado = true;
        break;
    }
}

// 7. Verificar que exista
if (!$encontrado) {
    http_response_code(404);

    echo json_encode([
        "ok" => false,
        "mensaje" => "No se encontró un participante con ese ID."
    ]);

    exit;
}

// 8. Reorganizar los índices y guardar
$participantes = array_values($participantes);

$nuevoContenido = json_encode(
    $participantes,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
);

if (file_put_contents($archivo, $nuevoContenido, LOCK_EX) === false) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "No se pudo guardar la eliminación."
    ]);

    exit;
}

// 9. Confirmar la eliminación
http_response_code(200);

echo json_encode([
    "ok" => true,
    "mensaje" => "Participante eliminado correctamente.",
    "id" => (int)$id
], JSON_UNESCAPED_UNICODE);

?>
