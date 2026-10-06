<?php
// PASO 1: Conectarse a la sesión activa (Obligatorio)
// Si no la inicias, PHP no sabrá qué sesión debe destruir.
session_start();

// PASO 2: Limpiar las variables en la memoria actual
$_SESSION = array();

// PASO 3: Destruir el archivo físico en el servidor
// Esto invalida el ID de sesión para siempre.
session_destroy();

// PASO 4: Redirigir al usuario (Obligatorio)
header("Location: ./login.php");
exit();
?>
 