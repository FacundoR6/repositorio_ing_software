<?php

include("../../conexion.php");
$id = $_POST['id'];

$sql = "DELETE FROM reservas WHERE id_reserva = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$reserva_actualizada = "UPDATE clientes c JOIN reservas r ON c.id_cliente = r.id_cliente SET c.reservas_realizadas = c.reservas_realizadas - 1 WHERE r.id_reserva = ?";

$stmt = $conexion->prepare($reserva_actualizada);
$stmt->execute([$id]);

header("Location: ./index.php");
exit;

?> 