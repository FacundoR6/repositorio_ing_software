<?php

include("../../conexion.php");

$reserva = null;
$esEdicion = false;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conexion->prepare("SELECT id_reserva, id_cliente, id_cancha, fecha, hora, estado_reserva FROM reservas WHERE id_reserva = ?");
    $stmt->execute([$id]);
    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($reserva) {
        $esEdicion = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;

    if (!empty($_POST['id_cliente']) && !empty($_POST['id_cancha']) && !empty($_POST['fecha']) && !empty($_POST['hora']) && !empty($_POST['estado_reserva'])) {
        $id_cliente = trim($_POST['id_cliente']);
        $id_cancha = trim($_POST['id_cancha']);
        $fecha = trim($_POST['fecha']);
        $hora = trim($_POST['hora']);
        $estado_reserva = trim($_POST['estado_reserva']);

        $stmtCliente = $conexion->prepare("SELECT suspendido FROM clientes WHERE id_cliente = ?");
        $stmtCliente->execute([$id_cliente]);
        $suspendido = $stmtCliente->fetchColumn();

        if ($suspendido === false || strtolower((string)$suspendido) === 'si') {
            die('No se puede crear o actualizar una reserva para un cliente suspendido.');
        }

        if ($id > 0) {
            $sql = "UPDATE reservas SET id_cliente = ?, id_cancha = ?, fecha = ?, hora = ?, estado_reserva = ? WHERE id_reserva = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$id_cliente, $id_cancha, $fecha, $hora, $estado_reserva, $id]);
        } else {
            $sql = "INSERT INTO reservas(id_cliente, id_cancha, fecha, hora, estado_reserva)
                VALUES(?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$id_cliente, $id_cancha, $fecha, $hora, $estado_reserva]);
        }

        header("Location: ./index.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/estilos.css">
    <title><?php echo $esEdicion ? 'Editar Reserva' : 'Asociar Reserva'; ?> | Punto Padel</title>
</head>
<body class="bodyAgregar">

    <div class="container">
        <img src="../../img/PuntoPadelCartelSinFondo.png" alt="Logo Punto Padel">
        <div class="header-title">
            <h2><?php echo $esEdicion ? 'Editar' : 'Nueva'; ?> <span>Reserva</span></h2>
        </div>

        <form method="POST" autocomplete="off">
            <?php if ($esEdicion && $reserva): ?>
                <input type="hidden" name="id" value="<?php echo (int)$reserva['id_reserva']; ?>">
            <?php endif; ?>

            <div class="input-group">
                <label for="id_cliente">Cliente</label>
                <select id="id_cliente" name="id_cliente" required>
                    <option value="">Seleccione un cliente</option>
                    <?php
                    $sql = "SELECT id_cliente, nombre FROM clientes WHERE suspendido = 'No'";
                    $stmt = $conexion->query($sql);
                    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($clientes as $c): ?>
                        <option value="<?php echo (int)$c['id_cliente']; ?>" <?php echo (isset($reserva['id_cliente']) && $reserva['id_cliente'] == $c['id_cliente']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <label for="id_cancha">Cancha</label>
                <select id="id_cancha" name="id_cancha" required>
                    <option value="">Seleccione una cancha</option>
                    <option value="1" <?php echo (isset($reserva['id_cancha']) && $reserva['id_cancha'] == 1) ? 'selected' : ''; ?>>Cancha 1</option>
                    <option value="2" <?php echo (isset($reserva['id_cancha']) && $reserva['id_cancha'] == 2) ? 'selected' : ''; ?>>Cancha 2</option>
                </select>
            </div>

            <div class="input-group">
                <label for="fecha">Fecha</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo isset($reserva['fecha']) ? $reserva['fecha'] : ''; ?>" required>
            </div>
 
            <div class="input-group">
                <label for="hora">Hora</label>
                <input type="time" id="hora" name="hora" value="<?php echo isset($reserva['hora']) ? $reserva['hora'] : ''; ?>" required>
            </div>

            <div class="input-group">
                <label for="estado_reserva">Estado de la Reserva</label>
                <select id="estado_reserva" name="estado_reserva" required>
                    <option value="">Seleccione un estado</option>
                    <option value="Pagada" <?php echo (isset($reserva['estado_reserva']) && $reserva['estado_reserva'] == 'Pagada') ? 'selected' : ''; ?>>Pagada</option>
                    <option value="Señada" <?php echo (isset($reserva['estado_reserva']) && $reserva['estado_reserva'] == 'Señada') ? 'selected' : ''; ?>>Señada</option>
                    <option value="Pendiente" <?php echo (isset($reserva['estado_reserva']) && $reserva['estado_reserva'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="Cancelada" <?php echo (isset($reserva['estado_reserva']) && $reserva['estado_reserva'] == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                    <option value="Finalizada" <?php echo (isset($reserva['estado_reserva']) && $reserva['estado_reserva'] == 'Finalizada') ? 'selected' : ''; ?>>Finalizada</option>
                </select>
            </div>

            <button type="submit" class="botonGenerico btn-submit"><?php echo $esEdicion ? 'Actualizar Reserva' : 'Guardar Reserva'; ?></button>
            
            <a href="./index.php" class="btn-back">← Volver al listado</a>
        </form>
    </div>

</body>
</html>