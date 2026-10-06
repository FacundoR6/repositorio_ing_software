<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0");

// si no existe la sesión, al login
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}
$tiempo_maximo = 1800; 

if (isset($_SESSION['ultimo_acceso'])) {
    $tiempo_sesion = time() - $_SESSION['ultimo_acceso'];
    
    if ($tiempo_sesion > $tiempo_maximo) {
        session_unset();
        session_destroy();
        header("Location: login.php?error=expirado");
        exit();
    }
}

$_SESSION['ultimo_acceso'] = time();
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
  <link rel="icon" type="image/png" href="../img/PuntoPadelLogo.png" />
  <title>Inicio | Punto Padel</title>
 
  
  <link rel="stylesheet" href="../css/estilos.css">

  
</head>
<body class="body-usuario">
 
<header class="headerUsuario"> 
    <a href="../vista_usuario/index.php"><img src="../img/PuntoPadelCartelSinFondo.png" alt="Logo de Punto Padel" id="logo1"></a>
    <h1 class="tituloPag">Punto Padel | Sitio Oficial</h1>
    <img src="../img/PelotaPadel.png" alt="Pelota Padel" id="logo2">
</header>

    <aside>
    
        <a href="../vista_usuario/index.php">🏠Inicio</a>
        <a href="">📅Reservar</a>
        <a href="">🥎Partidos Abiertos</a>
        <a href="">⚙️Mi Perfil</a>
        <a href="../cerrarSesion.php" id="btnCerrarSesion">❌Cerrar Sesión</a>
    
    </aside>

    <main class="contenidoGeneral">
    
    <div class="contenidoPrincipal">   
       
    </div>

        </div>
    <footer>
        <p>&copy; 2026 Punto Padel. Todos los derechos reservados.</p>  
    </footer>
</main>
</body>

</html>