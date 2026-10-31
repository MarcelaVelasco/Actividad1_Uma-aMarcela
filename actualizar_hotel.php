<?php
session_start();
require_once "conexion.php";
require_once "funciones.php";

// Solo el administrador puede modificar hoteles
requerirAdministrador();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: hoteles.php");
    exit;
}

$id = filter_var($_POST["id"] ?? "", FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
if ($id === false) {
    mostrarError(
        "Hotel no válido",
        "No se recibió un identificador de hotel válido.",
        []
    );
}

[$datos, $errores] = validarDatosHotel($_POST);

if ($errores) {
    mostrarError(
        "Verifica los datos",
        "No se actualizó el hotel porque algunos campos no son válidos.",
        $errores,
        "editar_hotel.php?id=" . $id,
        "Volver a la edición"
    );
}

try {
    $sql = "EXEC dbo.actualizarHotel
            @idHotel = ?,
            @Nombre = ?,
            @Ciudad = ?,
            @Direccion = ?,
            @Habitaciones = ?,
            @PrecioNoche = ?,
            @Estado = ?";
    $consulta = $conexion->prepare($sql);
    $consulta->execute([
        $id,
        $datos["nombre"],
        $datos["ciudad"],
        $datos["direccion"],
        $datos["habitaciones"],
        $datos["precio"],
        $datos["estado"],
    ]);

    header("Location: hoteles.php?mensaje=actualizado");
    exit;
} catch (PDOException $e) {
    error_log($e->getMessage());
    mostrarError(
        "Error al actualizar hotel",
        "SQL Server no procesó la actualización.",
        [mensajeSql($e)],
        "editar_hotel.php?id=" . $id,
        "Volver a la edición"
    );
} catch (Throwable $e) {
    error_log($e->getMessage());
    mostrarError(
        "Error inesperado",
        "Ocurrió un problema al actualizar el hotel.",
        [$e->getMessage()]
    );
}
