<?php
require_once __DIR__ . '/../conexion.php';

header('Content-Type: application/json; charset=utf-8');

function responderStock($ok, $mensaje, $codigo = 200) {
    http_response_code($codigo);
    echo json_encode(['ok' => $ok, 'mensaje' => $mensaje]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderStock(false, 'Método no permitido.', 405);
}

$datos = json_decode(file_get_contents('php://input'), true);
$accion = $datos['accion'] ?? '';
$idProducto = isset($datos['id_producto']) ? (int)$datos['id_producto'] : 0;
$cantidad = isset($datos['cantidad']) ? (int)$datos['cantidad'] : 0;

if (!in_array($accion, ['reservar', 'devolver'], true) || $idProducto <= 0 || $cantidad <= 0) {
    responderStock(false, 'Datos de stock inválidos.', 400);
}

try {
    if ($accion === 'reservar') {
        $stmt = $conexion->prepare(
            'UPDATE stock
             SET cantidad = cantidad - :cantidad
             WHERE id_producto = :id_producto AND cantidad >= :cantidad'
        );
        $stmt->execute([
            ':cantidad' => $cantidad,
            ':id_producto' => $idProducto
        ]);

        if ($stmt->rowCount() !== 1) {
            responderStock(false, 'No hay stock suficiente para ese producto.', 409);
        }
    } else {
        $stmt = $conexion->prepare(
            'UPDATE stock
             SET cantidad = cantidad + :cantidad
             WHERE id_producto = :id_producto'
        );
        $stmt->execute([
            ':cantidad' => $cantidad,
            ':id_producto' => $idProducto
        ]);

        if ($stmt->rowCount() !== 1) {
            responderStock(false, 'El producto ya no existe en stock.', 404);
        }
    }

    responderStock(true, 'Stock actualizado.');
} catch (Throwable $e) {
    responderStock(false, 'No se pudo actualizar el stock.', 500);
}