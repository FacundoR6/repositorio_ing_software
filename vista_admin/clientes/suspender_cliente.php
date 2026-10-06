<?php
include("../../conexion.php");
$cliente = null;
$error = '';
$id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id > 0) {
    $consulta_cliente = $conexion->prepare(
        "SELECT id_cliente, nombre, suspendido FROM clientes WHERE id_cliente = ?"
    );
    $consulta_cliente->execute([$id]);
    $cliente = $consulta_cliente->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $cliente && strtolower((string)$cliente['suspendido']) !== 'si') {
    $observacion = trim($_POST['observacion'] ?? '');
    $fecha_desde = $_POST['fecha_desde'] ?? '';
    $fecha_hasta = $_POST['fecha_hasta'] ?? '';

    if ($observacion !== '' && $fecha_desde !== '' && $fecha_hasta !== '') {
        $conexion->beginTransaction();

        try {
            $stmt_update_cliente = $conexion->prepare(
                "UPDATE clientes SET suspendido = 'Si' WHERE id_cliente = ?"
            );
            $stmt_update_cliente->execute([$id]);

            $stmt = $conexion->prepare(
                "INSERT INTO suspension (id_cliente, observacion, fecha_desde, fecha_hasta)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$id, $observacion, $fecha_desde, $fecha_hasta]);

            $conexion->commit();
            header("Location: ./index.php");
            exit;
        } catch (PDOException $e) {
            $conexion->rollBack();
            $error = 'No se pudo registrar la suspensión. Verifique los datos e inténtelo nuevamente.';
        }
    } else {
        $error = 'Complete todos los campos para suspender al cliente.';
    }
}

if ($cliente && strtolower((string)$cliente['suspendido']) === 'si') {
    header("Location: ./index.php");
    exit;
}

if (!$cliente) {
    header("Location: ./index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/estilos.css">
    <title>Suspender Cliente | Punto Padel</title>
</head>
<body class="bodyAgregar">

    <div class="container">
        <img src="../../img/PuntoPadelCartelSinFondo.png" alt="Logo Punto Padel">
        <div class="header-title">
            <h2>Suspender a <span><?php echo htmlspecialchars($cliente['nombre']); ?></span></h2>
        </div>

        <form method="POST" autocomplete="off">
            <input type="hidden" name="id" value="<?php echo (int) $cliente['id_cliente']; ?>">

            <?php if ($error !== ''): ?>
                <p role="alert"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <div class="input-group">
                <label for="observacion">Motivo de Suspensión</label>
                <input type="text" id="observacion" name="observacion" placeholder="Ej. Incumplimiento de condiciones" value="<?php echo htmlspecialchars($_POST['observacion'] ?? ''); ?>" required minlength="3" maxlength="100">
            </div>

            <div class="input-group">
                <label for="fecha_desde">Fecha de Suspensión</label>
                <input type="date" id="fecha_desde" name="fecha_desde" value="<?php echo htmlspecialchars($_POST['fecha_desde'] ?? ''); ?>" required>
            </div>

            <div class="input-group">
                <label for="fecha_hasta">Fecha de Finalización</label>
                <input type="date" id="fecha_hasta" name="fecha_hasta" value="<?php echo htmlspecialchars($_POST['fecha_hasta'] ?? ''); ?>" required>
            </div>

            
             <button type="submit" class="botonGenerico btn-submit">Suspender Cliente</button>
            
 
            <a href="./index.php" class="btn-back">← Volver al listado</a>
        </form>
    </div>

</body>
</html>
