
<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

// 1. Verificar el método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Debes utilizar el método POST."
    ]);

    exit;
}

// 2. Verificar la sesión
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Debes iniciar sesión para actualizar participantes."
    ]);

    exit;
}

// 3. Recibir los datos
$id = trim($_POST['id'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$numeroRifa = trim((string)($_POST['numero_rifa'] ?? ''));

// 4. Validar los datos
if (
    $id === '' ||
    !ctype_digit($id) ||
    (int)$id <= 0 ||
    $nombre === '' ||
    $apellido === '' ||
    $numeroRifa === '' ||
    !ctype_digit($numeroRifa) ||
    (int)$numeroRifa <= 0
) {
    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Debes ingresar un ID válido, nombre, apellido y número de rifa."
    ]);

    exit;
}

// 5. Ubicar y leer el archivo JSON
$archivo = __DIR__ . '/participantes.json';

if (!file_exists($archivo) || !is_writable($archivo)) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "No se puede acceder al archivo de participantes."
    ]);

    exit;
}

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

// Verificar que el número de rifa no pertenezca a otro participante
foreach ($participantes as $participante) {

    if (
        isset($participante['id'], $participante['numero_rifa']) &&
        (int)$participante['id'] !== (int)$id &&
        (int)$participante['numero_rifa'] === (int)$numeroRifa
    ) {
        http_response_code(409);

        echo json_encode([
            "ok" => false,
            "mensaje" => "El número de rifa ya está asignado a otro participante."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }
}

// 6. Buscar al participante por su ID
$encontrado = false;
$participanteActualizado = null;

foreach ($participantes as &$participante) {

    if (
        isset($participante['id']) &&
        (int)$participante['id'] === (int)$id
    ) {
        $participante['nombre'] = $nombre;
        $participante['apellido'] = $apellido;
        $participante['numero_rifa'] = (int)$numeroRifa;

        $participanteActualizado = $participante;
        $encontrado = true;

        break;
    }
}

unset($participante);

// 7. Comprobar si existe el participante
if (!$encontrado) {
    http_response_code(404);

    echo json_encode([
        "ok" => false,
        "mensaje" => "No se encontró un participante con ese ID."
    ]);

    exit;
}

// 8. Guardar los cambios
$nuevoContenido = json_encode(
    $participantes,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
);

if (file_put_contents($archivo, $nuevoContenido, LOCK_EX) === false) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "No se pudieron guardar los cambios."
    ]);

    exit;
}

// 9. Devolver la respuesta
http_response_code(200);

echo json_encode([
    "ok" => true,
    "mensaje" => "Participante actualizado correctamente.",
    "participante" => $participanteActualizado
], JSON_UNESCAPED_UNICODE);

?>
