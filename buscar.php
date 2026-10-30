<?php
session_start();

if (!isset($_SESSION['usuario_web'])) {
    header('Location: index.php');
    exit;
}
?>
<?php

// Normalizar los textos para comparar sin distinguir mayúsculas
function normalizar($texto) {
    $texto = trim((string)$texto);

    if (function_exists('mb_strtolower')) {
        return mb_strtolower($texto, 'UTF-8');
    }

    return strtolower($texto);
}

// 1. Inicializar variables
$nombre = '';
$apellido = '';
$ciudad = '';
$categoria = '';
$mensaje = '';

// 2. Leer los participantes desde el archivo JSON
$archivo = __DIR__ . '/participantes_busqueda.json';
$participantes = [];

if (file_exists($archivo)) {
    $contenido = file_get_contents($archivo);
    $datos = json_decode($contenido, true);

    if (is_array($datos)) {
        $participantes = $datos;
    }
}

// 3. Procesar la búsqueda mediante GET
if (isset($_GET['buscar'])) {

    $nombre = trim($_GET['nombre'] ?? '');
    $apellido = trim($_GET['apellido'] ?? '');
    $ciudad = trim($_GET['ciudad'] ?? '');
    $categoria = trim($_GET['categoria'] ?? '');

    // 4. Validar que se ingresen los cuatro campos
    if (
        $nombre === '' ||
        $apellido === '' ||
        $ciudad === '' ||
        $categoria === ''
    ) {
        $mensaje = 'Debes completar los cuatro campos.';
    } else {

        // 5. Buscar coincidencia en los cuatro campos
        $encontrado = false;

        foreach ($participantes as $participante) {

            if (
                normalizar($participante['nombre'] ?? '') === normalizar($nombre) &&
                normalizar($participante['apellido'] ?? '') === normalizar($apellido) &&
                normalizar($participante['ciudad'] ?? '') === normalizar($ciudad) &&
                normalizar($participante['categoria'] ?? '') === normalizar($categoria)
            ) {
                $encontrado = true;
                break;
            }
        }

        // 6. Generar el mensaje solicitado por la prueba
        if ($encontrado) {
            $mensaje = 'Participante encontrado';
        } else {
            $mensaje = 'Participante no encontrado';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Buscar participante</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            margin: 0;
            padding: 30px;
        }

        .contenedor {
            max-width: 500px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px #00000015;
        }

        h1 {
            text-align: center;
            font-size: 26px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            border: 1px solid #bbb;
            border-radius: 4px;
        }

        button, .enlace {
            display: inline-block;
            width: 100%;
            box-sizing: border-box;
            margin-top: 20px;
            padding: 12px;
            background: #1769aa;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 15px;
        }

        .resultado {
            margin-top: 20px;
            padding: 14px;
            background: #eef5fc;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .enlace {
            background: #555;
        }
    </style>
</head>

<body>

<div class="contenedor">

    <h1>Buscar participante</h1>

    <form method="GET" action="buscar.php">

        <label for="nombre">Nombre:</label>
        <input
            type="text"
            id="nombre"
            name="nombre"
            value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="apellido">Apellido:</label>
        <input
            type="text"
            id="apellido"
            name="apellido"
            value="<?= htmlspecialchars($apellido, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="ciudad">Ciudad:</label>
        <input
            type="text"
            id="ciudad"
            name="ciudad"
            value="<?= htmlspecialchars($ciudad, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="categoria">Categoría:</label>
        <input
            type="text"
            id="categoria"
            name="categoria"
            value="<?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <button type="submit" name="buscar" value="1">
            Buscar participante
        </button>

    </form>

    <?php if ($mensaje !== ''): ?>
        <div class="resultado">
            <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <a href="panel.php" class="enlace">
        Volver al panel principal
    </a>

</div>

</body>
</html>
