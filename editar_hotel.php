<?php
session_start();
require_once "conexion.php";
require_once "funciones.php";

// Solo el administrador puede editar; el auditor es redirigido
requerirAdministrador();

$id = filter_var($_GET["id"] ?? "", FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
if ($id === false) {
    header("Location: hoteles.php?mensaje=error");
    exit;
}

$hotel = null;
try {
    $consulta = $conexion->query("EXEC consultarHotel");
    foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $fila) {
        if ((int)$fila["IdHotel"] === $id) {
            $hotel = $fila;
            break;
        }
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    mostrarError(
        "No se pudo cargar el hotel",
        "Ocurrió un error al consultar la base de datos.",
        [mensajeSql($e)]
    );
}

if ($hotel === null) {
    mostrarError(
        "Hotel no encontrado",
        "El hotel seleccionado ya no existe o fue eliminado.",
        []
    );
}

$h = fn($t) => htmlspecialchars((string)$t, ENT_QUOTES, "UTF-8");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar hotel | Hotel SensuYork</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
</head>
<body>
<header class="barra-superior">
    <a href="panel.php" class="marca"><i class="fa-solid fa-hotel"></i> Hotel SensuYork</a>
    <div class="usuario-menu">
        <span class="usuario-chip"><i class="fa-solid fa-circle-user"></i> <?= $h($_SESSION["nombre"]) ?></span>
        <a href="logout.php" class="btn-salir"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
    </div>
</header>

<main class="contenedor contenedor-edicion">
    <div class="encabezado-pagina">
        <div>
            <p class="etiqueta">Gestión hotelera</p>
            <h1>Editar hotel</h1>
            <p>Modifica los datos del hotel #<?= (int)$hotel["IdHotel"] ?> y guarda los cambios.</p>
        </div>
    </div>

    <section class="panel-formulario">
        <form action="actualizar_hotel.php" method="POST" class="formulario-grid" id="formHotel">
            <input type="hidden" name="id" value="<?= (int)$hotel["IdHotel"] ?>">

            <div>
                <label for="nombre">Nombre del hotel</label>
                <input type="text" id="nombre" name="nombre" maxlength="100"
                       value="<?= $h($hotel["Nombre"]) ?>" required>
            </div>

            <div>
                <label for="ciudad">Ciudad</label>
                <input type="text" id="ciudad" name="ciudad" maxlength="100"
                       value="<?= $h($hotel["Ciudad"]) ?>" required>
            </div>

            <div>
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" maxlength="150"
                       value="<?= $h($hotel["Direccion"] ?? "") ?>" required>
            </div>

            <div>
                <label for="habitaciones">Número de habitaciones</label>
                <input type="number" id="habitaciones" name="habitaciones" min="1" step="1"
                       value="<?= (int)$hotel["Habitaciones"] ?>" required>
            </div>

            <div>
                <label for="precio">Precio por noche ($)</label>
                <input type="number" id="precio" name="precio" min="0.01" step="0.01"
                       value="<?= $h(number_format((float)$hotel["PrecioNoche"], 2, ".", "")) ?>" required>
            </div>

            <div>
                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <option value="Activo"   <?= $hotel["Estado"] === "Activo"   ? "selected" : "" ?>>Activo</option>
                    <option value="Inactivo" <?= $hotel["Estado"] === "Inactivo" ? "selected" : "" ?>>Inactivo</option>
                </select>
            </div>

            <div class="campo-completo botones-formulario">
                <button type="submit" class="btn-principal">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>
                <a href="hoteles.php" class="btn-secundario">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
            </div>
        </form>
    </section>
</main>

<script src="js/sweetalert2.all.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>
