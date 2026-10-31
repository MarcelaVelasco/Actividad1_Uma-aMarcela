<?php
session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$usuario = trim($_POST["usuario"] ?? "");
$clave   = $_POST["clave"] ?? "";

if ($usuario === "" || $clave === "") {
    header("Location: index.php?error=" . urlencode("Credenciales incorrectas"));
    exit;
}

try {
    $sql = "EXEC Recibirlogin @Usuario = ?, @Clave = ?";
    $consulta = $conexion->prepare($sql);
    $consulta->execute([$usuario, $clave]);

    $datos = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($datos) {
        session_regenerate_id(true);
        $_SESSION["idUsuario"] = $datos["IdUsuario"];
        $_SESSION["usuario"]   = $datos["Usuario"];
        $_SESSION["nombre"]    = $datos["Nombre"];
        $_SESSION["rol"]       = $datos["Rol"];

        header("Location: panel.php");
        exit;
    }

    header("Location: index.php?error=" . urlencode("Credenciales incorrectas"));
    exit;
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo "Ocurrió un error al validar las credenciales. Contacta al administrador del sistema.";
}