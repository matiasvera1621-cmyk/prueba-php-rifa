
<?php
session_start();

// Generar un CAPTCHA con dos números aleatorios.
function generarCaptcha(): void {
    $numero1 = random_int(1, 9);
    $numero2 = random_int(1, 9);

    $_SESSION['captcha_numero1'] = $numero1;
    $_SESSION['captcha_numero2'] = $numero2;
    $_SESSION['captcha_respuesta'] = $numero1 + $numero2;
}

// Si ya inició sesión, ir al panel.
if (isset($_SESSION['usuario_web'])) {
    header('Location: panel.php');
    exit;
}

// Crear el primer CAPTCHA.
if (!isset($_SESSION['captcha_respuesta'])) {
    generarCaptcha();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = trim($_POST['usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $respuesta = trim($_POST['captcha'] ?? '');

    // Comprobar el resultado del CAPTCHA actual.
    $captchaCorrecto =
        $respuesta !== '' &&
        ctype_digit($respuesta) &&
        (int)$respuesta === (int)$_SESSION['captcha_respuesta'];

    // Comprobar las credenciales exigidas por la prueba.
    $credencialesCorrectas =
        $usuario === 'userupla' &&
        $contrasena === 'upla123';

    // Generar un CAPTCHA nuevo después de cada intento.
    generarCaptcha();

    if ($credencialesCorrectas && $captchaCorrecto) {

        session_regenerate_id(true);
        $_SESSION['usuario_web'] = $usuario;

        header('Location: panel.php');
        exit;

    } else {
        $error = 'Usuario, contraseña o CAPTCHA incorrectos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión - Rifa</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f4f8;
            margin: 0;
            padding: 40px 15px;
        }

        .contenedor {
            max-width: 380px;
            margin: auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 12px #00000018;
        }

        h1 {
            text-align: center;
            font-size: 24px;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            margin-top: 6px;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #1769aa;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .error {
            color: #b42318;
            background: #fef3f2;
            padding: 10px;
            border-radius: 5px;
        }

        .captcha {
            background: #eef5fb;
            padding: 12px;
            margin-top: 18px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="contenedor">

    <h1>Inicio de sesión</h1>

    <?php if ($error !== ''): ?>
        <p class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="">

        <label for="usuario">Usuario</label>
        <input
            type="text"
            id="usuario"
            name="usuario"
            required
        >

        <label for="contrasena">Contraseña</label>
        <input
            type="password"
            id="contrasena"
            name="contrasena"
            required
        >

        <div class="captcha">
            Resuelve:
            <?= (int)$_SESSION['captcha_numero1'] ?>
            +
            <?= (int)$_SESSION['captcha_numero2'] ?>
            = ?
        </div>

        <label for="captcha">Resultado del CAPTCHA</label>
        <input
            type="number"
            id="captcha"
            name="captcha"
            required
        >

        <button type="submit">Ingresar</button>

    </form>

</div>

</body>
</html>
