<?php
session_start();
require_once "conexion.php";
require_once "funciones.php";

// Solo el administrador puede eliminar hoteles
requerirAdministrador();

// La eliminación solo se acepta por POST (nunca por URL)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: hoteles.php");
    exit;
}

$id = filter_var($_POST["id"] ?? "", FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
if ($id === false) {
    mostrarError(
        "Hotel no válido",
        "No se recibió un identificador de hotel válido para eliminar.",
        []
    );
}

try {
    $consulta = $conexion->prepare("EXEC dbo.eliminarHotel @IdHotel = ?");
    $consulta->execute([$id]);

    header("Location: hoteles.php?mensaje=eliminado");
    exit;
} catch (PDOException $e) {
    error_log($e->getMessage());
    mostrarError(
        "Error al eliminar hotel",
        "SQL Server no pudo eliminar el registro.",
        [mensajeSql($e)]
    );
} catch (Throwable $e) {
    error_log($e->getMessage());
    mostrarError(
        "Error inesperado",
        "Ocurrió un problema al eliminar el hotel.",
        [$e->getMessage()]
    );
}
