<?php
/**
 * Funciones compartidas del proyecto Hotel SensuYork.
 * Centraliza control de acceso, validaciones y pantalla de errores.
 */

/** Exige que exista una sesión iniciada. */
function requerirSesion(): void
{
    if (!isset($_SESSION["usuario"])) {
        header("Location: index.php?error=" . urlencode("sesión"));
        exit;
    }
}

/** Exige rol Administrador; el Auditor solo puede consultar. */
function requerirAdministrador(): void
{
    requerirSesion();
    if (($_SESSION["rol"] ?? "") !== "Administrador") {
        header("Location: hoteles.php?mensaje=permiso");
        exit;
    }
}

/**
 * Valida los datos del formulario de hotel (registro y edición).
 * Devuelve [datos, errores].
 */
function validarDatosHotel(array $post): array
{
    $errores = [];

    $nombre    = trim($post["nombre"] ?? "");
    $ciudad    = trim($post["ciudad"] ?? "");
    $direccion = trim($post["direccion"] ?? "");
    $precio    = trim($post["precio"] ?? "");
    $estado    = trim($post["estado"] ?? "");

    if ($nombre === "" || mb_strlen($nombre) > 100) {
        $errores[] = "El nombre es obligatorio (máximo 100 caracteres).";
    }
    if ($ciudad === "" || mb_strlen($ciudad) > 100) {
        $errores[] = "La ciudad es obligatoria (máximo 100 caracteres).";
    }
    if ($direccion === "" || mb_strlen($direccion) > 150) {
        $errores[] = "La dirección es obligatoria (máximo 150 caracteres).";
    }

    $habitaciones = filter_var(
        trim($post["habitaciones"] ?? ""),
        FILTER_VALIDATE_INT,
        ["options" => ["min_range" => 1, "max_range" => 10000]]
    );
    if ($habitaciones === false) {
        $errores[] = "El número de habitaciones debe ser un entero mayor que 0.";
    }

    if (!is_numeric($precio) || (float)$precio <= 0 || (float)$precio > 99999999) {
        $errores[] = "El precio por noche debe ser un número mayor que 0.";
    }

    if (!in_array($estado, ["Activo", "Inactivo"], true)) {
        $errores[] = "El estado debe ser Activo o Inactivo.";
    }

    $datos = [
        "nombre"       => $nombre,
        "ciudad"       => $ciudad,
        "direccion"    => $direccion,
        "habitaciones" => $habitaciones,
        "precio"       => $precio,
        "estado"       => $estado,
    ];

    return [$datos, $errores];
}

/** Extrae el mensaje legible que devolvió SQL Server (RAISERROR, etc.). */
function mensajeSql(PDOException $e): string
{
    $mensaje = $e->getMessage();
    if (preg_match('/\[SQL Server\](.*)$/s', $mensaje, $coincidencia)) {
        return trim($coincidencia[1]);
    }
    return $mensaje;
}

/** Muestra una página de error con el estilo del sistema y termina. */
function mostrarError(
    string $titulo,
    string $mensaje,
    array $detalles = [],
    string $volverUrl = "hoteles.php",
    string $volverTexto = "Volver a hoteles"
): void {
    $h = fn($t) => htmlspecialchars((string)$t, ENT_QUOTES, "UTF-8");
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $h($titulo) ?> | Hotel SensuYork</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
</head>
<body>
<header class="barra-superior">
    <a href="panel.php" class="marca"><i class="fa-solid fa-hotel"></i> Hotel SensuYork</a>
</header>
<main class="contenedor contenedor-edicion">
    <section class="panel-error">
        <div class="error-icono"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h1><?= $h($titulo) ?></h1>
        <p><?= $h($mensaje) ?></p>
        <?php if ($detalles): ?>
            <div class="error-detalle">
                <strong>Detalle:</strong>
                <ul>
                    <?php foreach ($detalles as $d): ?>
                        <li><?= $h($d) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <a href="<?= $h($volverUrl) ?>" class="btn-principal">
            <i class="fa-solid fa-arrow-left"></i> <?= $h($volverTexto) ?>
        </a>
    </section>
</main>
</body>
</html>
    <?php
    exit;
}
