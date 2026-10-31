<?php
    session_start();

    if(isset($_SESSION['Usuario'])){
        header("Location: panel.php");
        exit;
    }

    $error = $_GET["error"] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOTEL</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" >
</head>    
<body class ="login-body">

    <div class="login-container">
    <div class="login-icon"> <i class="fa-solid fa-hotel"></i></div>

        <h1>Hotel SensuYork</h1>
        <p class="subtitulo">Sistema de Gestión de Hoteles</p>

        <!-- Código para validaciones en el formualrio de inicio de sesión -->

        <?php if($error ==='Credenciales incorrectas'): ?>
        <div class = "alerta error" >
            Usuario o contraseña incorrectos. Por favor, inténtelo de nuevo.
        </div>

        <?php elseif($error === 'sesión'): ?>
        <div class = "alerta error" >
            Debe iniciar sesión para acceder a esta página.
        </div>

        <?php elseif($error === 'rol'): ?>
        <div class = "alerta error" >
            No tiene permisos para acceder a esta página.
        </div>
        <?php endif; ?>

        <!-- Código para el fomulario de inicio de sesión -->

        <form action="login.php" method="post">
        
        <label for="usuario">Ingrese el Usuario</label>
        <input type="text" id="usuario" name="usuario" placeholder="Ingrese usuario" 
        maxlength="50" required>

        <label for="clave">Ingrese la contraseña</label>
        <input type="password" id="clave" name="clave" placeholder="Ingrese la contraseña"
        required autocomplete="current-password">

        <button type="submit" class = "btn-principal">Iniciar Sesión</button>
        </form>
        <p class="pie-login">Proyecto Hotel - Administración Base de Datos</p>
        </div>
        <script src="js/scripts.js"></script>
</body>
</html>