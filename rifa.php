
<?php
session_start();

// Verificar que exista una sesión iniciada
if (!isset($_SESSION['usuario_web'])) {
    header('Location: index.php');
    exit;
}

// Inicializar la cantidad de números reservados
if (!isset($_SESSION['cantidad_numeros'])) {
    $_SESSION['cantidad_numeros'] = 0;
}

// Precio de cada número
$precioNumero = 2000;

// Procesar las acciones del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';

    if ($accion === 'agregar') {
        $_SESSION['cantidad_numeros']++;

    } elseif ($accion === 'quitar') {

        if ($_SESSION['cantidad_numeros'] > 0) {
            $_SESSION['cantidad_numeros']--;
        }
    }

    // Evitar que F5 repita el envío del formulario
    header('Location: rifa.php');
    exit;
}

// Calcular cantidad y total
$cantidad = (int) $_SESSION['cantidad_numeros'];
$total = $cantidad * $precioNumero;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Reserva de números - Rifa</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            margin: 0;
            padding: 30px;
        }

        .contenedor {
            max-width: 550px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px #00000015;
        }

        h1 {
            text-align: center;
            color: #222;
        }

        .dato {
            padding: 12px;
            margin: 12px 0;
            background: #eef5fc;
            border-radius: 5px;
        }

        .total {
            font-size: 22px;
            font-weight: bold;
            color: #1769aa;
        }

        form {
            display: inline-block;
            margin: 8px 4px 8px 0;
        }

        button, .enlace {
            display: inline-block;
            padding: 12px 18px;
            border: none;
            border-radius: 5px;
            background: #1769aa;
            color: white;
            cursor: pointer;
            text-decoration: none;
            font-size: 15px;
        }

        .quitar {
            background: #dc3545;
        }

        .enlace {
            background: #555;
            margin-top: 15px;
        }
    </style>
</head>

<body>

<div class="contenedor">

    <h1>Reserva de números de la rifa</h1>

    <div class="dato">
        <strong>Usuario:</strong>
        <?= htmlspecialchars(
            $_SESSION['usuario_web'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>

    <div class="dato">
        <strong>Cantidad de números reservados:</strong>
        <?= $cantidad ?>
    </div>

    <div class="dato">
        <strong>Precio por número:</strong>
        $<?= number_format($precioNumero, 0, ',', '.') ?>
    </div>

    <div class="dato total">
        Total a pagar:
        $<?= number_format($total, 0, ',', '.') ?>
    </div>

    <form method="POST" action="rifa.php">
        <button type="submit" name="accion" value="agregar">
            Agregar 1 número
        </button>
    </form>

    <form method="POST" action="rifa.php">
        <button type="submit" name="accion" value="quitar"
                class="quitar">
            Quitar 1 número
        </button>
    </form>

    <br>

    <a href="panel.php" class="enlace">
        Volver al panel principal
    </a>

</div>

</body>
</html>
