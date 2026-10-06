<?php
session_start();

if (isset($_SESSION['usuario']) && $_SESSION['usuario'] === 'admin') {
    header("Location: ./vista_admin/index.php");
    exit();
}

include("./conexion.php");

if (!empty($_POST['btniniciarsesion'])) {
    $usuario = trim($_POST['usuario'] ?? '');
    $contraseña = $_POST['contraseña'] ?? '';

    if ($usuario === '' || $contraseña === '') {
        echo "<div>Los campos son obligatorios.</div>";
    } else {
        $sql = "SELECT * FROM clientes WHERE nombre_usuario = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$usuario]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($datos && (password_verify($contraseña, $datos['contraseña']) || $datos['contraseña'] === $contraseña)) {
            $_SESSION['usuario'] = $datos['nombre_usuario'];
            $_SESSION['ultimo_acceso'] = time();
            if($_SESSION['usuario']=== "admin"){

                header("Location: ./vista_admin/index.php");
            }
            else{
                header("Location: ./vista_usuario/index.php");
            }
            exit();
        } else {
            echo "<div>Usuario o contraseña incorrectos.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="./img/PuntoPadelLogo.png" />
    <title>Iniciar Sesión | Punto Padel</title>
    <link rel="stylesheet" href="./css/estilos.css">

 
</head>
<body class="bodyLogin">
    <img src="./img/PuntoPadelCartelSinFondo.png" alt="Logo de Punto Padel" id="logoLogin">
    <div class="formLogin">
        <h3>Bienvenido | Punto Padel</h3>
        <form action="./login.php" method="post">
                <div class="LoginInput">
                 <input type="text" name="usuario" placeholder="Nombre de Usuario" required>
            </div>
            <div class="LoginInput">
                <input type="password" name="contraseña" placeholder="Contraseña" required>
            </div>
            <br> 
            <a href="./registrarse.php">¿No tienes cuenta? Regístrate</a>
            <input name="btniniciarsesion" type="submit" id="btnLogearse" value="Iniciar Sesión">
        </form>
        
    </div>

</body>
</html>

