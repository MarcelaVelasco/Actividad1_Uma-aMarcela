<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php?error=sesión");
    exit;
}

$esAdministrador = $_SESSION["rol"] === "Administrador";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel | Hotel SensuYork</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" >
</head>
<body>
    <header class="barra-superior">
        <a href="panel.php" class="marca">Hotel SensuYork</a>
        <div class="usuario-menu">
            <span>
                <a href="logout.php" class="btn-salir">Cerrar Sesión</a>
            </span>
        </div>
    </header>

    <main class="contenedor">
        <section class="bienvenida">
            <p class="etiqueta">PANEL PRINCIPAL</p>
            <h1>¡BIENVENIDO/A, <?= htmlspecialchars($_SESSION["usuario"]) ?>!</h1>
            <p>Administra y consulta la información de los hoteles</p>
        </section>

        <section class="tarjetas">
            <article class="tarjeta">
                <div class ="tarjeta-icono"><i class ="fa-solid fa-book-open-reader"></i></div>
                <h2>Hoteles</h2>
                <p>Consulta los hoteles registrados en el sistema</p>
                <a href="hoteles.php" class="btn-principal">Ver Hoteles</a>
            </article>

            <?php if ($esAdministrador): ?>
            <article class="tarjeta">
                <div class="tarjeta-icono"><i class="fa-solid fa-plus"></i></div>
                <h2>Registrar hotel</h2>
                <p>Agrega un nuevo hotel al sistema</p>
                <a href="hoteles.php#formulario" class="btn-principal">Nuevo Hotel</a>
            </article>
            <?php endif; ?>

            <article class="tarjeta">
                <div class="tarjeta-icono"><i class ="fa-solid fa-circle-user"></i></div>
                <h2>Mi cuenta</h2>
                <p>Usuario: <?= htmlspecialchars($_SESSION["usuario"]) ?></p>
                <p>Rol: <?= htmlspecialchars($_SESSION["rol"]) ?></p>
            </article>
        </section>
    </main>

    <script src="js/script.js"></script>
</body>
</html>