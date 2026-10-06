<?php

$host = 'localhost';
$base_de_datos = 'complejo_padel';
$usuario = 'root';
$contraseña = '';

try {
    $conexion = new PDO("mysql:host=$host;dbname=$base_de_datos;charset=utf8", $usuario, $contraseña);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?> 