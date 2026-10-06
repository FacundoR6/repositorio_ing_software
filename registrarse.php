<?php

include("./conexion.php");

$mensaje = "";

if (isset($_POST["registrarse"])) {
    $nombre = trim($_POST["nombre"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    $sql = "SELECT id_cliente FROM clientes WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$email]);
    $resultado = $stmt->fetchColumn();

    if ($resultado) {
        $mensaje = "Ese correo ya está registrado.";
    } else {
        $sql = "SELECT id_cliente FROM clientes WHERE nombre_usuario = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$usuario]);
        $resultado = $stmt->fetchColumn();

        if ($resultado) {
            $mensaje = "Ese nombre de usuario ya existe.";
        } else {
            $sql = "INSERT INTO clientes(nombre, email, telefono, reservas_realizadas, suspendido, nombre_usuario, contraseña) VALUES (?, ?, ?, 0, 'No', ?, ?)";
            $stmt = $conexion->prepare($sql);

            if ($stmt->execute([$nombre, $email, $telefono, $usuario, $password])) {
                $mensaje = "Cuenta creada correctamente.";
                header("Location: login.php");
                exit();
            } else {
                $mensaje = "Ocurrió un error.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../img/PuntoPadelLogo.png" />
    <title>Registro</title>

    <link rel="stylesheet" href="./css/estilos.css">
</head>

<body class="bodyRegistro">

<div class="contenedor">

    <form action="./registrarse.php" method="POST" class="formulario">
    
        <h2 class="titulo-formulario">Crear cuenta</h2>
        <div class="grupo">
            <label>Nombre completo</label> 
            <input type="text" name="nombre" placeholder="Ingrese su nombre completo" maxlength="80" required>
        </div>
        <div class="grupo">
            <label>Correo electrónico</label>
            <input type="email" name="email" placeholder="Ingrese su correo electrónico" maxlength="100" required>
        </div> 
        <div class="grupo">
            <label>Teléfono</label>
            <input type="text" name="telefono" placeholder="Ingrese su teléfono" maxlength="15" required>
        </div>
        <div class="grupo">
            <label>Nombre de usuario</label>
            <input type="text" name="usuario" placeholder="Ingrese su nombre de usuario" maxlength="30" required>
        </div>
        <div class="grupo">
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="Ingrese su contraseña" minlength="8" required>
        </div>
        
        <a href="./login.php">¿Ya tienes cuenta? Inicia sesión</a> <br> <br>
        <button type="submit" name="registrarse" class="botonGenerico">Registrarme</button>
    </form>
</div>

</body>
</html>