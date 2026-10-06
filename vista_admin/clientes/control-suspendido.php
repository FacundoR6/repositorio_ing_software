<?php

include ("../../conexion.php");

$sql = "UPDATE clientes c
        SET c.suspendido = 'No'
        WHERE c.suspendido = 'Si'
        AND NOT EXISTS (
        SELECT 1
        FROM suspension s
        WHERE s.id_cliente = c.id_cliente
        AND s.fecha_hasta >= CURDATE())";

$conexion->exec($sql);

?>

