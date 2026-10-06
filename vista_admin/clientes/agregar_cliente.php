<?php

include("../../conexion.php");

$cliente = null;
$esEdicion = false;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conexion->prepare("SELECT id_cliente, nombre, email, telefono, reservas_realizadas, suspendido FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($cliente) {
        $esEdicion = true;
    } 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;

    if (!empty($_POST['nombre']) && !empty($_POST['email']) && !empty($_POST['telefono'])) {
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $telefono = trim($_POST['telefono']);

        if ($id > 0) {
            $sql = "UPDATE clientes SET nombre = ?, email = ?, telefono = ? WHERE id_cliente = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $email, $telefono, $id]);
        } else {
            $sql = "INSERT INTO clientes(nombre, email, telefono, reservas_realizadas, suspendido)
                VALUES(?, ?, ?, 0, 'No')";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre, $email, $telefono]);
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
    <title><?php echo $esEdicion ? 'Editar Cliente' : 'Asociar Cliente'; ?> | Punto Padel</title>
</head>
<body class="bodyAgregar">

    <div class="container">
        <img src="../../img/PuntoPadelCartelSinFondo.png" alt="Logo Punto Padel">
        <div class="header-title">
            <h2><?php echo $esEdicion ? 'Editar' : 'Nuevo'; ?> <span>Cliente</span></h2>
        </div>

        <form method="POST" autocomplete="off">
            <?php if ($esEdicion && $cliente): ?>
                <input type="hidden" name="id" value="<?php echo (int)$cliente['id_cliente']; ?>">
            <?php endif; ?>

            <div class="input-group">
                <label for="nombre">Nombre y Apellido</label>
                
                <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan Pérez" value="<?php echo htmlspecialchars($cliente['nombre'] ?? ''); ?>" required minLength="3" maxlength="40">
                
            </div>

            <div class="input-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" value="<?php echo htmlspecialchars($cliente['email'] ?? ''); ?>" required>
            </div>

            <div class="input-group">
                <label for="telefono">Teléfono / WhatsApp</label>
                <input type="text" id="telefono" name="telefono" placeholder="Ej. 3764123456" value="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>" required minlength="8" maxlength="10">
            </div>

            <button type="submit" class="botonGenerico btn-submit"><?php echo $esEdicion ? 'Actualizar Cliente' : 'Guardar Cliente'; ?></button>
            
            <a href="./index.php" class="btn-back">← Volver al listado</a>
        </form>
    </div>

</body>
</html>


