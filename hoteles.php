<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php?error=sesión");
    exit;
}

$esAdministrador = $_SESSION["rol"] === "Administrador";
$mensaje = $_GET["mensaje"] ??"";
$hoteles = [];

try {
    $consulta = $conexion->query("EXEC consultarHotel");
    $hoteles = $consulta->fetchAll(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
    error_log($e->getMessage());
    die("No se pudieron consultar los hoteles");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoteles | HotelManager </title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" >
</head>
<body>
<header class ="barra-superior">
    <a href ="panel.php" class ="marca"><i class = "fa-solid fa-hotel"></i>Hotel SensuYork</a>
    <div class ="usuario-menu">
        <span> <?= htmlspecialchars($_SESSION["nombre"]) ?> </span>
        <a href="logout.php" class ="btn-salir">Cerrar Sesión</a>
    </div>
</header>
<main class ="contenedor">
    <div class = "encabezado-pagina">
        <div>
            <p class ="etiqueta">Gestion Hotelera</p>
            <h1>Hoteles Registrados</h1>
            <p>Consulta la información de hoteles</p>
        </div>
    <a href="panel.php" class="btn-secundario">Volver al Panel Principal</a>
    </div>

    <?php if ($mensaje === "agregado"): ?>
        <div class="alerta exito">Hotel  Registrado Correctamente</div>
    <?php elseif ($mensaje === "actualizado"): ?>
        <div class="alerta exito">Hotel Actualizado Correctamente </div>
    <?php elseif ($mensaje === "eliminado"): ?>
        <div class="alerta exito">Hotel Eliminado Correctamente </div>
    <?php elseif ($mensaje === "error"): ?>
        <div class="alerta error">No se pudo completar la operacion </div>
    <?php endif; ?>   
    
    <?php if ($esAdministrador): ?>
    <section class = "panel-formulario" id="formulario">
        <h2>Registrar nuevo hotel </h2>

        <form action ="guardar_hotel.php" method="POST"
              class = "formulario-grid" id="formHotel">

            <div>
                <label for="nombre">Nombre del hotel</label>
                <input type="text" id="nombre" name="nombre"
                       maxlength="100" required>                        
            </div>        

            <div>
                <label for="ciudad">Ciudad</label>
                <input type="text" id="ciudad" name="ciudad"
                       maxlength="100" required> 
            </div>

            <div>
                <label for="direccion">Direccion</label>
                <input type="text" id="direccion" name="direccion"
                       maxlength="150" required> 
            </div>

            <div>
                <label for="habitaciones">Número de habitaciones</label>
                <input type="number" id="habitaciones" name="habitaciones"
                       min="1" step="1" required> 
            </div>

            <div>
                <label for="precio">Precio por noche ($)</label>
                <input type="number" id="precio" name="precio"
                       min ="0.01" step ="0.01" required> 
            </div>

            <div>
                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>                
            </div>

            <div class="campo-completo">
                <button type="submit" class="btn-principal">Guardar hotel</button>                
            </div>              
            </form> 
    </section>
    <?php endif; ?>

    <section class="tabla-contenedor">
        <div class="tabla-titulo">
            <h2>Listado de hoteles</h2>
            <span class="contador">
                <?= count($hoteles)?> hoteles
            </span>
        </div>

        <div class="tabla-scroll">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Ciudad</th>
                        <th>Direccion</th>
                        <th>Habitaciones</th>
                        <th>Precio/Noche</th>
                        <th>Estado</th>
                        <?php if($esAdministrador): ?>
                            <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if(count($hoteles)>0): ?>
                    <?php $numero = 0; ?>
                    <?php foreach($hoteles as $hotel) : $numero++; ?>
                    <tr>
                        <td><?= $numero ?></td>
                        <td><?= htmlspecialchars ($hotel["Nombre"]) ?></td>
                        <td><?= htmlspecialchars($hotel["Ciudad"]) ?></td>
                        <td><?= htmlspecialchars ($hotel["Direccion"] ?? "") ?></td>
                        <td><?= (int)$hotel["Habitaciones"] ?></td>
                        <td>$<?= number_format((float)$hotel["PrecioNoche"], 2) ?></td>
                        <td>
                            <span class="estado">
                                <?= htmlspecialchars($hotel["Estado"]) ?>
                            </span>
                        </td>   
                        <?php if($esAdministrador): ?>
                        <td class="acciones">
                            <a class="btn-editor"
                            href="editar_hotel.php?id=<?=(int)$hotel["IdHotel"]?>">Editar</a>
                            <form action="eliminar_hotel.php" method="POST"
                                  class="form-eliminar">
                                <input type="hidden" name="id"
                                       value="<?=(int)$hotel["IdHotel"] ?>">
                                <button type="submit" class="btn-eliminar">Eliminar</button>
                            </form>
                        </td> 
                        <?php endif; ?>                    
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $esAdministrador ? 8 : 7 ?>">
                            No hay hoteles registrados.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<script src="js/sweetalert2.all.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>