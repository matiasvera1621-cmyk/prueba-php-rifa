
<?php
session_start();

// Proteger la página mediante sesión
if (!isset($_SESSION['usuario_web'])) {
    header('Location: index.php');
    exit;
}

// Ubicación de los comprobantes
$carpeta = __DIR__ . '/comprobantes/';
$archivos = [];

if (is_dir($carpeta)) {
    $elementos = scandir($carpeta);

    foreach ($elementos as $elemento) {
        if ($elemento === '.' || $elemento === '..') {
            continue;
        }

        if (is_file($carpeta . $elemento)) {
            $archivos[] = $elemento;
        }
    }
}

sort($archivos, SORT_NATURAL | SORT_FLAG_CASE);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archivos cargados - Rifa</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f4f9;
            margin: 0;
            padding: 30px 15px;
        }

        .contenedor {
            max-width: 700px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        h1 {
            text-align: center;
            font-size: 27px;
        }

        .archivo {
            background: #edf4fc;
            padding: 14px;
            margin: 10px 0;
            border-radius: 5px;
            overflow-wrap: anywhere;
        }

        .mensaje {
            background: #edf4fc;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .enlace {
            display: block;
            padding: 12px;
            margin-top: 12px;
            border-radius: 5px;
            color: white;
            background: #2167a8;
            text-decoration: none;
            text-align: center;
        }

        .secundario {
            background: #555;
        }
    </style>
</head>

<body>
    <main class="contenedor">

        <h1>Comprobantes cargados</h1>

        <p>
            Usuario:
            <strong>
                <?= htmlspecialchars(
                    $_SESSION['usuario_web'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </strong>
        </p>

        <h3>Archivos disponibles</h3>

        <?php if (count($archivos) > 0): ?>

            <?php foreach ($archivos as $archivo): ?>
                <div class="archivo">
                    <?= htmlspecialchars(
                        $archivo,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endforeach; ?>

        <?php else: ?>

            <p class="mensaje">
                No hay comprobantes cargados.
            </p>

        <?php endif; ?>

        <a class="enlace" href="subir_comprobante.php">
            Subir otro comprobante
        </a>

        <a class="enlace secundario" href="panel.php">
            Volver al panel principal
        </a>

    </main>
</body>
</html>
