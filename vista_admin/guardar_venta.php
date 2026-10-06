<?php
require_once __DIR__ . '/../conexion.php';

header('Content-Type: application/json; charset=utf-8');

function responderJson($ok, $mensaje, $extra = [], $codigo = 200) {
    http_response_code($codigo);
    echo json_encode(array_merge(['ok' => $ok, 'mensaje' => $mensaje], $extra));
    exit;
}

try {
    $conexion->exec(
        "CREATE TABLE IF NOT EXISTS ventas (
            id_venta INT AUTO_INCREMENT PRIMARY KEY,
            id_cliente INT NOT NULL,
            fecha_venta  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            metodo_pago VARCHAR(50) NOT NULL DEFAULT 'Efectivo',
            monto_total DECIMAL(10,2) NOT NULL DEFAULT 0
        )"
    );

    $conexion->exec(
        "CREATE TABLE IF NOT EXISTS detalle_venta (
            id_detalle INT AUTO_INCREMENT PRIMARY KEY,
            id_venta INT NOT NULL,
            nombre_carrito VARCHAR(100) NOT NULL,
            id_producto INT NOT NULL,
            cantidad INT NOT NULL,
            precio DECIMAL(10,2) NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL,
            clientes_pagados TEXT NULL,
            FOREIGN KEY (id_venta) REFERENCES ventas(id_venta) ON DELETE CASCADE
        )"
    );

    $columnasDetalle = $conexion->query("SHOW COLUMNS FROM detalle_venta LIKE 'clientes_pagados'")->fetch();
    if (!$columnasDetalle) {
        $conexion->exec("ALTER TABLE detalle_venta ADD COLUMN clientes_pagados TEXT NULL");
    }
} catch (PDOException $e) {
    responderJson(false, 'No se pudo preparar la estructura de ventas.', ['error' => $e->getMessage()], 500);
}

if (isset($_GET['accion']) && $_GET['accion'] === 'clientes') {
    try {
        $stmt = $conexion->query("SELECT id_cliente, nombre FROM clientes WHERE suspendido = 'No' ORDER BY nombre ASC");
        responderJson(true, 'Clientes cargados.', ['clientes' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (PDOException $e) {
        responderJson(false, 'No se pudo cargar la lista de clientes.', ['error' => $e->getMessage()], 500);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJson(false, 'Método no permitido.', [], 405);
}

$entrada = file_get_contents('php://input');
$datos = json_decode($entrada, true);

if (!is_array($datos)) {
    responderJson(false, 'No se recibieron datos de venta.', [], 400);
}

$idCliente = isset($datos['id_cliente']) ? (int)$datos['id_cliente'] : 0;
$nombreCarrito = trim((string)($datos['nombre_carrito'] ?? ''));
$metodoPago = trim((string)($datos['metodo_pago'] ?? 'Efectivo'));
$items = is_array($datos['items'] ?? null) ? $datos['items'] : [];

if ($idCliente <= 0 || $nombreCarrito === '' || empty($items)) {
    responderJson(false, 'Faltan datos requeridos para guardar la venta.', [], 400);
}

$stmtCliente = $conexion->prepare("SELECT suspendido FROM clientes WHERE id_cliente = ?");
$stmtCliente->execute([$idCliente]);
$suspendido = $stmtCliente->fetchColumn();

if ($suspendido === false) {
    responderJson(false, 'El cliente seleccionado no existe.', [], 404);
}

if (strtolower((string)$suspendido) === 'si') {
    responderJson(false, 'No se puede realizar una venta a un cliente suspendido.', [], 403);
}

$itemsAgrupados = [];
foreach ($items as $item) {
    $idProducto = isset($item['id']) ? (int)$item['id'] : (isset($item['id_producto']) ? (int)$item['id_producto'] : 0);
    $cantidad = isset($item['cantidad']) ? (int)$item['cantidad'] : 0;
    $precio = isset($item['precio']) ? (float)$item['precio'] : 0;
    $tipo = isset($item['tipo']) ? strtolower((string)$item['tipo']) : 'producto';
    $clientesPagados = is_array($item['clientes_pagados'] ?? null) ? $item['clientes_pagados'] : [];

    if ($cantidad <= 0) {
        continue;
    }

    if ($idProducto === -1) {
        $precioFinal = $precio;
        $estadoTurno = strtolower(trim((string)($item['estado_reserva'] ?? 'Pendiente')));
        if ($estadoTurno === 'señada' || $estadoTurno === 'senada') {
            $precioFinal = 7500.00;
        }
        if ($estadoTurno === 'pagada') {
            $precioFinal = 15000.00;
        }

        $itemsAgrupados[-1] = [
            'id_producto' => -1,
            'cantidad' => $cantidad,
            'precio' => $precioFinal,
            'descripcion' => 'Turno de cancha (2 horas)',
            'clientes_pagados' => $clientesPagados,
            'estado_reserva' => ucfirst($estadoTurno)
        ];
        continue;
    }

    if ($idProducto <= 0) {
        continue;
    }

    if (!isset($itemsAgrupados[$idProducto])) {
        $itemsAgrupados[$idProducto] = [
            'id_producto' => $idProducto,
            'cantidad' => 0,
            'precio' => $precio,
            'clientes_pagados' => []
        ];
    }

    $itemsAgrupados[$idProducto]['cantidad'] += $cantidad;
    $itemsAgrupados[$idProducto]['precio'] = $precio;
    $itemsAgrupados[$idProducto]['clientes_pagados'] = array_merge(
        $itemsAgrupados[$idProducto]['clientes_pagados'],
        $clientesPagados
    );
}

if (empty($itemsAgrupados)) {
    responderJson(false, 'La venta no tiene productos válidos.', [], 400);
}

$subtotal = 0.0;
foreach ($itemsAgrupados as $item) {
    $subtotal += $item['precio'] * $item['cantidad'];
}

if ($subtotal <= 0) {
    responderJson(false, 'La venta no tiene un monto válido.', [], 400);
}

$conexion->beginTransaction();

try {
    $stmtVenta = $conexion->prepare(
        "INSERT INTO ventas (id_cliente, fecha_venta, metodo_pago, monto_total)
         VALUES (?, NOW(), ?, ?)"
    );
    $stmtVenta->execute([$idCliente, $metodoPago, number_format($subtotal, 2, '.', '')]);
    $idVenta = (int)$conexion->lastInsertId();

    $stmtDetalle = $conexion->prepare(
        "INSERT INTO detalle_venta (id_venta, nombre_carrito, id_producto, cantidad, precio, subtotal, clientes_pagados)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    foreach ($itemsAgrupados as $item) {
        $idProducto = (int)$item['id_producto'];
        $cantidad = (int)$item['cantidad'];
        $precio = (float)$item['precio'];

        if ($idProducto !== -1 && $idProducto <= 0) {
            continue;
        }

        $precioFormateado = number_format($precio, 2, '.', '');
        $subtotalItem = number_format($precio * $cantidad, 2, '.', '');
        $pagosJson = json_encode($item['clientes_pagados'] ?? [], JSON_UNESCAPED_UNICODE);
        $stmtDetalle->execute([
            $idVenta,
            $nombreCarrito,
            $idProducto,
            $cantidad,
            $precioFormateado,
            $subtotalItem,
            $pagosJson
        ]);
    }

    foreach ($items as $item) {
        $tipo = isset($item['tipo']) ? strtolower((string)$item['tipo']) : 'producto';
        if ($tipo !== 'turno') {
            continue;
        }

        $fechaTurno = trim((string)($item['fecha'] ?? ''));
        $horaTurno = trim((string)($item['hora'] ?? ''));
        $canchaTurno = isset($item['cancha']) ? (int)$item['cancha'] : 1;
        $estadoReserva = trim((string)($item['estado_reserva'] ?? 'Pendiente'));

        if ($fechaTurno === '' || $horaTurno === '') {
            continue;
        }

        $stmtReserva = $conexion->prepare(
            "INSERT INTO reservas (id_cliente, id_cancha, fecha, hora, estado_reserva)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmtReserva->execute([$idCliente, $canchaTurno, $fechaTurno, $horaTurno, $estadoReserva]);

        $conexion->prepare(
            "UPDATE clientes SET reservas_realizadas = reservas_realizadas + 1 WHERE id_cliente = ?"
        )->execute([$idCliente]);
    }

    $conexion->commit();
    responderJson(true, 'Venta guardada correctamente.', [
        'id_venta' => $idVenta,
        'monto_total' => number_format($subtotal, 2, '.', '')
    ]);
} catch (Throwable $e) {
    $conexion->rollBack();
    responderJson(false, 'Error al guardar la venta.', ['error' => $e->getMessage()], 500);
}
