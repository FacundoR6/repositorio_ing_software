<?php
require_once '../../conexion.php';

$sqlVentas = "
    SELECT v.id_venta, v.fecha_venta, v.metodo_pago, v.monto_total, c.id_cliente, c.nombre AS cliente,
           GROUP_CONCAT(DISTINCT dv.nombre_carrito SEPARATOR ', ') AS nombre_carrito
    FROM ventas v
    INNER JOIN clientes c ON c.id_cliente = v.id_cliente
    LEFT JOIN detalle_venta dv ON dv.id_venta = v.id_venta
    GROUP BY v.id_venta
    ORDER BY v.fecha_venta DESC
";

$ventas = $conexion->query($sqlVentas)->fetchAll(PDO::FETCH_ASSOC);

$idVentaSolicitada = filter_input(INPUT_GET, 'id_venta', FILTER_VALIDATE_INT);
$ventaSeleccionada = $ventas[0] ?? null;

if ($idVentaSolicitada !== false && $idVentaSolicitada !== null) {
  foreach ($ventas as $venta) {
    if ((int) $venta['id_venta'] === $idVentaSolicitada) {
      $ventaSeleccionada = $venta;
      break;
    }
  }
}

$detalleVenta = [];

if ($ventaSeleccionada) {
    $stmtDetalle = $conexion->prepare("
       SELECT 
        dv.id_venta,
        dv.id_producto,
        COALESCE(s.descripcion, 'Turno de cancha (2 horas)') AS descripcion,
        dv.precio,
        SUM(dv.cantidad) AS cantidad,
        SUM(dv.subtotal) AS subtotal
    FROM detalle_venta dv
    LEFT JOIN stock s ON s.id_producto = dv.id_producto
    WHERE dv.id_venta = :id_venta
    GROUP BY dv.id_venta, dv.id_producto, dv.precio, descripcion
      ORDER BY dv.id_detalle ASC
    ");
    $stmtDetalle->execute([':id_venta' => $ventaSeleccionada['id_venta']]);
    $detalleVenta = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="../../img/PuntoPadelLogo.png" />
  <title>Ventas | Punto Padel</title>
  <link rel="stylesheet" href="../../css/estilos.css">
</head>
<body class="body-admin">

<header class="headerAdmin">
  <a href="../index.php"><img src="../../img/PuntoPadelCartelSinFondo.png" alt="Logo de Punto Padel" id="logo1"></a>
  <h1 class="tituloPag">Ventas | Punto Padel</h1>
  <img src="../../img/PelotaPadel.png" alt="Imagen del Complejo de Pádel" id="logo2">
</header>

  <aside class="aside-admin">
    <a href="../index.php">🏠Inicio</a>
    <a href="../Reservas/index.php">📅Reservas</a>
    <a href="../Clientes/index.php">👥Clientes</a>
    <a href="../Stock/index.php">📦Administrar Stock</a>
    <a href="../DivisorPagos/index.php">✂️Divisor de pagos</a>
    <a href="../Carrito/index.php">🛒Carrito de venta</a>
    <a class="active" href="../Ventas/index.php">💰Ventas</a>
    <a href="../../cerrarSesion.php" id="btnCerrarSesion">❌Cerrar Sesión</a>
  </aside>

  <main class="contenidoGeneral">
    <div class="contenidoPrincipal">
      <div class="ventas-layout">
        <section class="panel-ventas">
          <h2>Ventas Finalizadas</h2>

          <?php if (empty($ventas)): ?>
            <div class="sin-ventas">Todavía no hay ventas registradas.</div>
          <?php else: ?>
            <ul class="lista-ventas">
              <?php foreach ($ventas as $venta): ?>
                <li>
                  <button
                    type="button"
                    class="venta-item <?php echo ((int) $venta['id_venta'] === (int) ($ventaSeleccionada['id_venta'] ?? 0)) ? 'activa' : ''; ?>"
                    onclick="window.location.href = '?id_venta=<?php echo (int) $venta['id_venta']; ?>';"
                  >
                    <strong>Venta N° <?php echo (int)$venta['id_venta']; ?></strong>
                    <span><?php echo htmlspecialchars($venta['cliente']); ?></span><br>
                    <small><?php echo date('d/m/Y H:i', strtotime($venta['fecha_venta'])); ?></small><br>
                    <strong>Total: $<?php echo number_format((float)$venta['monto_total'], 2, ',', '.'); ?></strong>
                  </button>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>

        <section class="panel-ventas detalle-venta">
          <?php if ($ventaSeleccionada): ?>
            <h2>Detalles de la Venta N° <?php echo (int)$ventaSeleccionada['id_venta']; ?></h2>
            
            <div class="info-venta-header">
              <p><strong>Cliente:</strong> <?php echo htmlspecialchars($ventaSeleccionada['cliente']); ?></p>
              <?php if (!empty($ventaSeleccionada['nombre_carrito'])): ?>
                <p> <strong>Nombre del carrito:</strong> <?php echo htmlspecialchars($ventaSeleccionada['nombre_carrito']); ?> </p> <?php endif; ?>
              <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($ventaSeleccionada['fecha_venta'])); ?></p>
              <p><strong>Método de pago:</strong> <?php echo htmlspecialchars($ventaSeleccionada['metodo_pago']); ?></p>
            </div>

            <?php if (empty($detalleVenta)): ?>
              <div class="sin-ventas">No hay productos asociados a esta venta.</div>
            <?php else: ?>
              <table>
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($detalleVenta as $item): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($item['descripcion']); ?></td>
                      <td><?php echo (int)$item['cantidad']; ?></td>
                      <td>$<?php echo number_format((float)$item['precio'], 2, ',', '.'); ?></td>
                      <td>$<?php echo number_format((float)$item['subtotal'], 2, ',', '.'); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>

            <div class="totales-venta">
              Monto total: $<?php echo number_format((float)$ventaSeleccionada['monto_total'], 2, ',', '.'); ?>
            </div>
          <?php else: ?>
            <div class="sin-ventas">Seleccione una venta para ver sus productos.</div>
          <?php endif; ?>
        </section>
      </div>

      <a href="../ventas/index.php" class="tarjeta-circular">
        <div class="contenido-tarjeta">
            <?php
                
                $sql= "SELECT SUM(monto_total) FROM ventas where fecha_venta >= CURDATE() - INTERVAL 7 DAY";
                $stmt=$conexion->query($sql);
                $ventasSemana=$stmt->fetch(PDO::FETCH_COLUMN);
            ?>
            <span class="titulo-tarjeta">Ventas</span>
            <span class="numero-tarjeta">$<?php echo number_format((float)$ventasSemana, 2, ',', '.'); ?></span>
            <span class="subtexto-tarjeta">Esta semana</span>
        </div>
    </a>


    </div>
  </main>

  <footer>
    <p>&copy; 2026 Punto Padel. Todos los derechos reservados.</p>
  </footer>
</body>
</html>