<?php

session_start();
require_once "conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php?error=sesión");
    exit;
}

if($_SESSION["rol"] !== "Administrador"){
    die("No tiene permisos para registrar hoteles");
}

if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: hoteles.php");
    exit;
}

$nombre = trim($_POST["nombre"] ?? "");
$ciudad = trim($_POST["ciudad"] ??"");
$direccion = trim($_POST["direccion"] ??"");

$habitaciones = trim($_POST["habitaciones"] ??"");

if ($habitaciones === ""){
    $habitaciones = null;
} else{
    $habitaciones = filter_var($habitaciones,FILTER_VALIDATE_INT);
}

$precio = trim($_POST["precio"] ?? "");
$estado = trim($_POST["estado"] ?? "");

//Validaciones php
//permitimos temporalmente habitaciones negativas para
//comprobar el trigger de SQL Server las rechace.       PUNTO TRIGGER
if(
    $nombre === "" ||
    $ciudad === ""||
    $direccion === "" ||
    $habitaciones === false ||
    !is_numeric($precio) ||
    (float)$precio <= 0 ||  
    !in_array($estado, ["Activo","Inactivo"], true) 
){
    die(
        "Verifica los datos: completa los campos obligatorios" .
        "y asegúrate de que el precio sea válido."
    );
}

try{
    $sql="EXEC dbo.agregarHotel
          @Nombre = ?,
          @Ciudad = ?,
          @Direccion = ?,
          @Habitaciones = ?,
          @PrecioNoche = ?,
          @Estado =?";
          $consulta = $conexion->prepare($sql);
          $consulta ->execute([
            $nombre,
            $ciudad,
            $direccion,
            $habitaciones,
            $precio,
            $estado
          ]);

          header("Location: hoteles.php?mensaje=agregado");
          exit;
}catch(PDOException $e){

    error_log($e->getMessage());

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error al registrar Hoteles</title>
        <link rel="stylesheet" href="css/estilos.css">
    </head>
    <body>

    <div class="contenedor">
    <h2>Error al registrar hotel </h2>
    <p>
        SQL NO PROCESO LA CONSULTA O HUBO UN ERROR.
    </p>
    <div style ="background:#fff0f0";
                 border: 1px solid #dc2626;
                 padding: 15px;
                 border-radius:8px;
                 overflow-wrap:anywhere,">
        <strong>Mensaje recibido:</strong>
        <pre style="white-space:pre-wrap;">'.htmlspecialchars($e->getMessage(),ENT_QUOTES,"UFT-8").'</pre>
    </div>

    <p>
         <a href="hoteles.php">Volver a hoteles </a>
    </p>
    </body>
    </hmtl>';


}



?>