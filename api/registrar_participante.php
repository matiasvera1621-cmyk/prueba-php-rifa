
<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

// 1. Verificar que el método sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Debes utilizar el método POST."
    ]);

    exit;
}

// 2. Verificar que exista una sesión iniciada
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Debes iniciar sesión para registrar participantes."
    ]);

    exit;
}

// 3. Recibir los datos del participante
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$numeroRifa = trim((string)($_POST['numero_rifa'] ?? ''));

// 4. Validar los datos recibidos
if (
    $nombre === '' ||
    $apellido === '' ||
    $numeroRifa === '' ||
    !ctype_digit($numeroRifa) ||
    (int)$numeroRifa <= 0
) {
    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Debes ingresar nombre, apellido y un número de rifa válido."
    ]);

    exit;
}

// 5. Ubicar el archivo JSON
$archivo = __DIR__ . '/participantes.json';

if (!file_exists($archivo) || !is_writable($archivo)) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "No se puede acceder al archivo de participantes."
    ]);

    exit;
}

// 6. Leer los participantes existentes
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

// Verificar que el número de rifa no esté ocupado
foreach ($participantes as $participante) {

    if (
        isset($participante['numero_rifa']) &&
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

// 7. Generar un ID nuevo
$ultimoId = 0;

foreach ($participantes as $participante) {
    if (isset($participante['id'])) {
        $id = (int)$participante['id'];

        if ($id > $ultimoId) {
            $ultimoId = $id;
        }
    }
}

// 8. Crear el nuevo participante
$nuevoParticipante = [
    "id" => $ultimoId + 1,
    "nombre" => $nombre,
    "apellido" => $apellido,
    "numero_rifa" => (int)$numeroRifa
];

// 9. Agregarlo a la lista
$participantes[] = $nuevoParticipante;

// 10. Guardar los cambios en el JSON
$nuevoContenido = json_encode(
    $participantes,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
);

if (file_put_contents($archivo, $nuevoContenido, LOCK_EX) === false) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "No se pudo guardar el participante."
    ]);

    exit;
}

// 11. Responder con el resultado
http_response_code(201);

echo json_encode([
    "ok" => true,
    "mensaje" => "Participante registrado correctamente.",
    "participante" => $nuevoParticipante
], JSON_UNESCAPED_UNICODE);

?>
