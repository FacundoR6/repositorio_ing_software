<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include("../../conexion.php");

$producto = null;
$esEdicion = false;
$mensajeError = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conexion->prepare("SELECT id_producto, descripcion, cantidad, precio FROM stock WHERE id_producto = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($producto) {
        $esEdicion = true;
    }
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;

    if (!empty($_POST['descripcion']) && isset($_POST['cantidad']) && isset($_POST['precio'])) {
        $descripcion = trim($_POST['descripcion']);
        $cantidad = (int)$_POST['cantidad'];
        $precio = (float)$_POST['precio'];
        $observacion = trim($_POST['observacion'] ?? '');
        $fecha_movimiento= date("Y-m-d");

        try {
            $conexion->beginTransaction();

            if ($id > 0) {
                $stmt = $conexion->prepare(
                    "SELECT descripcion, cantidad, precio
                     FROM stock
                     WHERE id_producto = ?
                     FOR UPDATE"
                );
                $stmt->execute([$id]);
                $anterior = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$anterior) {
                    throw new RuntimeException('El producto no existe.');
                }

                $stmt = $conexion->prepare(
                    "UPDATE stock
                     SET descripcion = ?, cantidad = ?, precio = ?
                     WHERE id_producto = ?"
                );
                $stmt->execute([$descripcion, $cantidad, $precio, $id]);

                $stmt = $conexion->prepare(
                    "INSERT INTO movimientos_stock
                    (id_producto, cantidad_anterior, cantidad_nueva, diferencia,
                    precio_anterior, precio_nuevo, observacion, tipo_movimiento,
                    nombre_usuario, fecha_movimiento)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'ACTUALIZACION', ?, ?)"
                    );
                $stmt->execute([
                    $id,
                    $anterior['cantidad'],
                    $cantidad,
                    $cantidad - $anterior['cantidad'],
                    $anterior['precio'],
                    $precio,
                    $observacion,
                    $_SESSION['usuario'],
                    $fecha_movimiento
                ]);
            } else {
                $stmt = $conexion->prepare(
                    "INSERT INTO stock(descripcion, cantidad, precio)
                     VALUES(?, ?, ?)"
                );
                $stmt->execute([$descripcion, $cantidad, $precio]);
                $idNuevo = $conexion->lastInsertId();

                $stmt = $conexion->prepare(
                    "INSERT INTO movimientos_stock
                     (id_producto, descripcion_producto, cantidad_anterior,
                      cantidad_nueva, diferencia, precio_nuevo, observacion,
                      tipo_movimiento, usuario)
                     VALUES (?, ?, 0, ?, ?, ?, ?, 'ALTA', ?)"
                );
                $stmt->execute([
                    $idNuevo,
                    $descripcion,
                    $cantidad,
                    $cantidad,
                    $precio,
                    $observacion,
                    $_SESSION['usuario']
                ]);
            }

            $conexion->commit();
            header("Location: ./index.php");
            exit;
        } catch (Throwable $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            $mensajeError = 'No se pudo guardar el producto y su movimiento.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/estilos.css">
    <title><?php echo $esEdicion ? 'Editar Producto' : 'Agregar Producto'; ?> | Punto Padel</title>
</head>
<body class="bodyAgregar">
    <div class="container">
        <div class="header-title">
            <h2><?php echo $esEdicion ? 'Editar' : 'Nuevo'; ?> <span>Producto</span></h2>
        </div>

        <?php if ($mensajeError !== ''): ?>
            <p><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <?php if ($esEdicion && $producto): ?>
                <input type="hidden" name="id" value="<?php echo (int)$producto['id_producto']; ?>">
            <?php endif; ?>

            <div class="input-group">
                <label for="descripcion">Nombre del Producto / Descripción</label>
                <input type="text" id="descripcion" name="descripcion" placeholder="Ej. Tubo de Pelotas Penn" value="<?php echo htmlspecialchars($producto['descripcion'] ?? ''); ?>" required>
            </div>

            <div class="input-group">
                <label for="cantidad">Cantidad de Ingreso</label>
                <input type="number" id="cantidad" name="cantidad" min="0" placeholder="<?php echo htmlspecialchars($producto['cantidad'] ?? ''); ?>" value="" required>
            </div>

            <div class="input-group">
                <label for="precio">Precio de Venta ($)</label>
                <input type="number" id="precio" name="precio" min="0" step="0.01" placeholder="0.00" value="<?php echo htmlspecialchars($producto['precio'] ?? ''); ?>" required>
            </div>

            <div class="input-group">
                <label for="observacion">Observación del movimiento</label>
                <textarea id="observacion" name="observacion" maxlength="100" placeholder="Motivo del alta o actualización" required></textarea>
            </div>

            <button type="submit" class="botonGenerico btn-submit"><?php echo $esEdicion ? 'Actualizar Producto' : 'Guardar Producto'; ?></button>
            
            <a href="./index.php" class="btn-back">← Volver al Stock</a>
        </form>
    </div>
</body>
</html>