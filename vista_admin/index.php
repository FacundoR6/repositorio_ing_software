<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0");

// si no existe la sesión, al login
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}
$tiempo_maximo = 1800; 

if (isset($_SESSION['ultimo_acceso'])) {
    $tiempo_sesion = time() - $_SESSION['ultimo_acceso'];
    
    if ($tiempo_sesion > $tiempo_maximo) {
        session_unset();
        session_destroy();
        header("Location: ../login.php?error=expirado");
        exit();
    }
}

$_SESSION['ultimo_acceso'] = time();
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
  <link rel="icon" type="image/png" href="../img/PuntoPadelLogo.png" />
  <title>Inicio | Punto Padel</title>
 
  
  <link rel="stylesheet" href="../css/estilos.css">

  
</head>
<body class="body-admin">
 
<header class="headerAdmin"> 
    <a href="../vista_admin/index.php"><img src="../img/PuntoPadelCartelSinFondo.png" alt="Logo de Punto Padel" id="logo1"></a>
    <h1 class="tituloPag">Punto Padel | Panel de Administrador</h1>
    <img src="../img/PelotaPadel.png" alt="Pelota Padel" id="logo2">
    
</header>


    <aside class="aside-admin">
    
        <a class="active" href="../vista_admin/index.php">🏠Inicio</a>
        <a href="../vista_admin/Reservas/index.php">📅Reservas</a>
        <a href="../vista_admin/Clientes/index.php">👥Clientes</a>
        <a href="../vista_admin/Stock/index.php">📦Administrar Stock</a>
        <a href="../vista_admin/DivisorPagos/index.php">✂️Divisor de pagos</a>
        <a href="../vista_admin/Carrito/index.php">🛒Carrito de venta</a>
        <a href="../vista_admin/Ventas/index.php">💰Ventas</a>
        <a href="../cerrarSesion.php" id="btnCerrarSesion">❌Cerrar Sesión</a>
    
    </aside>

    <main class="contenidoGeneral">
     <div class="contenidoPrincipal">   
        <div class="contenedor-tarjetas">

    <a href="Reservas/index.php" class="tarjeta-circular">
        <div class="contenido-tarjeta">
            <?php
                include("../conexion.php");
                $sql= "SELECT COUNT(id_reserva) FROM reservas WHERE fecha >= CURDATE() AND estado_reserva!='Finalizada' AND estado_reserva!='Cancelada'"; 
                $stmt=$conexion->query($sql);
                $cantidad_reservas=$stmt->fetch(PDO::FETCH_COLUMN);
            ?> 
            <span class="titulo-tarjeta">Reservas</span>
            <span class="numero-tarjeta"><?php echo htmlspecialchars($cantidad_reservas) ?></span>
            <span class="subtexto-tarjeta">Pendientes</span>
        </div>
    </a>

    <a href="Clientes/index.php" class="tarjeta-circular">
        <div class="contenido-tarjeta">
            <?php
                include("../conexion.php");
                $sql= "SELECT COUNT(id_cliente) FROM clientes ";
                $stmt=$conexion->query($sql);
                $cantidad_clientes=$stmt->fetch(PDO::FETCH_COLUMN);
            ?>
            <span class="titulo-tarjeta">Clientes</span>
            <span class="numero-tarjeta"><?php echo htmlspecialchars($cantidad_clientes) ?></span>
            <span class="subtexto-tarjeta">Asociados</span>
        </div>
    </a>

    <a href="Stock/index.php" class="tarjeta-circular">
        <div class="contenido-tarjeta">
            <?php
                include("../conexion.php");
                $sql= "SELECT COUNT(id_producto) FROM stock where cantidad<5 ";
                $stmt=$conexion->query($sql);
                $bajo_stock=$stmt->fetch(PDO::FETCH_COLUMN);
            ?>
            <span class="titulo-tarjeta">Stock</span>
            <span class="numero-tarjeta"><?php echo htmlspecialchars($bajo_stock) ?></span>
            <span class="subtexto-tarjeta">Producto/s en bajo stock</span>
        </div>
    </a>

    <a href="carrito/index.php" class="tarjeta-circular">
        <div class="contenido-tarjeta">
            <span class="titulo-tarjeta">Carritos</span>
            <span id="cantidadCarritosAbiertos" class="numero-tarjeta">0</span>
            <span class="subtexto-tarjeta">Abierto/s</span>
        </div>
    </a>

</div>
    
    <h2 id="turnosHoy">Reservas del día:</h2><hr> <br><br>
    <?php
    include("../conexion.php");
    $sql = "SELECT id_reserva, c.nombre, c.email, id_cancha, estado_reserva, fecha, hora FROM reservas r JOIN clientes c ON r.id_cliente = c.id_cliente WHERE fecha = CURDATE() AND estado_reserva!='Finalizada' AND estado_reserva!='Cancelada' ORDER BY fecha, hora";
    $stmt = $conexion->query($sql);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <table class="tablaListar">
        <thead>
            <tr>
                <th>Numero de reserva</th>
                <th>Cliente</th>
                <th>Cancha</th>
                <th>Fecha y Hora</th>
                <th>Estado de reserva</th>

            </tr>
        </thead>
        <tbody>
            <?php if (count($resultados) > 0): 
                 foreach ($resultados as $fila): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['id_reserva']); ?></td>
                        <td>
                            <div class="infoCliente">
                            <div class="avatarCliente"><?php echo substr(htmlspecialchars($fila['nombre']), 0, 1); ?></div>
                            <div>
                                <p class="nombreCliente"><?php echo htmlspecialchars($fila['nombre']); ?></p>
                                <p class="emailCliente"><?php echo htmlspecialchars($fila['email']); ?></p>
                            </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($fila['id_cancha']); ?></td>
                        <td>
                            <p class="fechaReserva"><?php echo htmlspecialchars($fila['fecha']); ?></p>
                            <p class="horaReserva"><?php echo htmlspecialchars($fila['hora']); ?></p>
                        </td>
                        <td><span class="estadoReserva"></span><?php echo htmlspecialchars($fila['estado_reserva']); ?></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
            <?php 
            else: 
            ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No hay registros disponibles</td>
                </tr>
            <?php
            endif;
            ?>
        </tbody>
    </table>
    <br><br><br><br><br>


    <div>

            <div class="mitadPantalla">
            <?php
            $rangosHorarios = [
                ['etiqueta' => '18-20hs', 'condicion' => "hora >= '18:00' AND hora < '20:00'"],
                ['etiqueta' => '20-22hs', 'condicion' => "hora >= '20:00' AND hora < '22:00'"],
                ['etiqueta' => '16-18hs', 'condicion' => "hora >= '16:00' AND hora < '18:00'"],
                ['etiqueta' => '14-16hs', 'condicion' => "hora >= '14:00' AND hora < '16:00'"],
                ['etiqueta' => '22-00hs', 'condicion' => "hora >= '22:00' AND hora < '24:00'"],
            ];

            foreach ($rangosHorarios as &$rango) {
                $consulta = "SELECT id_cancha, COUNT(*) AS total
                             FROM reservas
                             WHERE {$rango['condicion']}
                             AND estado_reserva != 'Cancelada'
                             GROUP BY id_cancha";
                $reservasPorCancha = $conexion->query($consulta)->fetchAll(PDO::FETCH_KEY_PAIR);
                $rango['cancha1'] = (int) ($reservasPorCancha[1] ?? 0);
                $rango['cancha2'] = (int) ($reservasPorCancha[2] ?? 0);
                $rango['total'] = $rango['cancha1'] + $rango['cancha2'];
            }
            unset($rango);
            $maximoReservas = max(array_column($rangosHorarios, 'total'));

            usort($rangosHorarios, function ($a, $b){
                return $b['total'] <=> $a['total'];
            })
            
            ?>
            <div class="contenedorStats">
                <h3 style="margin:auto auto 30px; text-transform:uppercase">Reservas por horario:</h3><hr> <br><br>
                <div class="leyenda-cancha">
                    <span><i class="indicador-cancha cancha-1"></i>Cancha 1</span>
                    <span><i class="indicador-cancha cancha-2"></i>Cancha 2</span>
                </div>
                <?php foreach ($rangosHorarios as $rango):
                    $porcentajeCancha1 = $maximoReservas > 0
                        ? ($rango['cancha1'] / $maximoReservas) * 100
                        : 0;
                    $porcentajeCancha2 = $maximoReservas > 0
                        ? ($rango['cancha2'] / $maximoReservas) * 100
                        : 0;
                ?>
                    <div class="bar-row">
                        <span class="bar-label"><?php echo htmlspecialchars($rango['etiqueta']); ?></span>
                        <div class="bar-bg" title="Cancha 1: <?php echo $rango['cancha1']; ?> | Cancha 2: <?php echo $rango['cancha2']; ?>">
                            <div class="bar-fill cancha-1" style="width: <?php echo $porcentajeCancha1; ?>%;"></div>
                            <div class="bar-fill cancha-2" style="width: <?php echo $porcentajeCancha2; ?>%;"></div>
                        </div>
                        <span class="bar-val"><?php echo $rango['total']; ?> <small>(<?php echo $rango['cancha1']; ?> / <?php echo $rango['cancha2']; ?>)</small></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php
            $sqlClientes = "SELECT c.id_cliente, c.nombre, COUNT(r.id_reserva) AS total FROM clientes c INNER JOIN reservas r ON r.id_cliente = c.id_cliente WHERE r.estado_reserva != 'Cancelada' GROUP BY c.id_cliente, c.nombre ORDER BY total DESC;";

            $reservasClientes = $conexion->query($sqlClientes)->fetchAll(PDO::FETCH_ASSOC);

            $maximoReservasCliente = !empty($reservasClientes) ? (int) $reservasClientes[0]['total'] : 0;
            ?>
            <div class="contenedorStats">
                <h3 style="margin:auto auto 30px; text-transform:uppercase">Reservas por Cliente:</h3><hr> <br><br>
                <?php foreach ($reservasClientes as $cliente):
                    $porcentaje = $maximoReservasCliente > 0
                        ? ($cliente['total'] / $maximoReservasCliente) * 100
                        : 0;
                ?>
                    <div class="bar-row">
                        <span class="bar-label"><?php echo htmlspecialchars($cliente['nombre']); ?></span>
                        <div class="bar-bg">
                            <div class="bar-fill" style="width: <?php echo $porcentaje; ?>%;"></div>
                        </div>
                        <span class="bar-val"><?php echo (int) $cliente['total']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
    </div>
    </div>

        </div>
    <footer>
        <p>&copy; 2026 Punto Padel. Todos los derechos reservados.</p>  
    </footer>
</main>
<script>
    function actualizarCantidadCarritosAbiertos() {
        const elementoCantidad = document.getElementById('cantidadCarritosAbiertos');
        const carritos = JSON.parse(localStorage.getItem('carritosPadel') || '{}');

        elementoCantidad.textContent = Object.keys(carritos).length;
    }

    actualizarCantidadCarritosAbiertos();
    window.addEventListener('storage', actualizarCantidadCarritosAbiertos);
</script>
</body>

</html>