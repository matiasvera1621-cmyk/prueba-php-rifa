
<?php
session_start();

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario_web'])) {
    header('Location: index.php');
    exit;
}

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        isset($_FILES['comprobante']) &&
        $_FILES['comprobante']['error'] === UPLOAD_ERR_OK
    ) {
        $archivo = $_FILES['comprobante'];

        // Limitar el archivo a 5 MB
        if ($archivo['size'] > 5 * 1024 * 1024) {
            $mensaje = 'El archivo supera el límite de 5 MB.';
            $tipoMensaje = 'error';
        } else {

            // Permitir PDF e imágenes
            $extension = strtolower(
                pathinfo($archivo['name'], PATHINFO_EXTENSION)
            );

            $permitidas = ['pdf', 'jpg', 'jpeg', 'png'];

            if (!in_array($extension, $permitidas, true)) {
                $mensaje = 'Formato no permitido. Usa PDF, JPG o PNG.';
                $tipoMensaje = 'error';
            } else {

                // Crear la carpeta si no existe
                $carpeta = __DIR__ . '/comprobantes/';

                if (
                    !is_dir($carpeta) &&
                    !mkdir($carpeta, 0755, true)
                ) {
                    $mensaje = 'No se pudo crear la carpeta de comprobantes.';
                    $tipoMensaje = 'error';
                } else {

                    // Generar un nombre único para evitar sobrescribir archivos
                    $nombreOriginal = basename($archivo['name']);
                    $nombreSeguro = preg_replace(
                        '/[^A-Za-z0-9._-]/',
                        '_',
                        $nombreOriginal
                    );

                    $nombreFinal = date('Ymd_His') . '_' .
                        bin2hex(random_bytes(4)) . '_' .
                        $nombreSeguro;

                    $destino = $carpeta . $nombreFinal;

                    if (move_uploaded_file($archivo['tmp_name'], $destino)) {
                        $mensaje = 'Comprobante cargado correctamente.';
                        $tipoMensaje = 'exito';
                    } else {
                        $mensaje = 'Ocurrió un error al guardar el archivo.';
                        $tipoMensaje = 'error';
                    }
                }
            }
        }
    } else {
        $mensaje = 'Selecciona un archivo válido para cargar.';
        $tipoMensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir comprobante - Rifa</title>

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
            max-width: 600px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        h1 {
            text-align: center;
            font-size: 27px;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button, .enlace {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            border: none;
            border-radius: 5px;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 15px;
            cursor: pointer;
        }

        button {
            background: #2167a8;
        }

        .enlace {
            background: #555;
        }

        .mensaje {
            padding: 14px;
            margin: 18px 0;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .exito {
            background: #d9f2df;
            color: #17652b;
        }

        .error {
            background: #fbe0e0;
            color: #a51f1f;
        }
    </style>
</head>

<body>
    <main class="contenedor">

        <h1>Subir comprobante</h1>

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

        <p>Selecciona un archivo PDF o una imagen de hasta 5 MB.</p>

        <?php if ($mensaje !== ''): ?>
            <div class="mensaje <?= $tipoMensaje ?>">
                <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="comprobante">Archivo del comprobante:</label>

            <input
                type="file"
                id="comprobante"
                name="comprobante"
                accept=".pdf,.jpg,.jpeg,.png"
                required
            >

            <button type="submit">Cargar comprobante</button>
        </form>

        <a class="enlace" href="archivos.php">
            Ver archivos cargados
        </a>

        <a class="enlace" href="panel.php">
            Volver al panel principal
        </a>

    </main>
</body>
</html>
