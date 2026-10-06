<?php
include("../../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
    $id = (int)$_POST['id_producto'];
    $stmt = $conexion->prepare("DELETE FROM stock WHERE id_producto = ?");
    $stmt->execute([$id]);
}

header("Location: ../vista_admin/stock/index.php");
exit;
 