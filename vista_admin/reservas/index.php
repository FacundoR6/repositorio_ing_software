
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="../../img/PuntoPadelLogo.png" />
  <title>Reservas | Punto Padel</title>
 
  <link rel="stylesheet" href="../../css/estilos.css">

</head>
<body class="body-admin">

<header class="headerAdmin">
    <a href="../index.php"><img src="../../img/PuntoPadelCartelSinFondo.png" alt="Logo de Punto Padel" id="logo1"></a>
    <h1 class="tituloPag">Reservas | Punto Padel</h1>
    <img src="../../img/PelotaPadel.png" alt="Imagen del Complejo de Pádel" id="logo2">
</header>

   <aside class="aside-admin">
    
        <a href="../index.php">🏠Inicio</a>
        <a class="active" href="../reservas/index.php">📅Reservas</a>
        <a href="../clientes/index.php">👥Clientes</a>
        <a href="../stock/index.php">📦Administrar Stock</a>
        <a href="../divisorPagos/index.php">✂️Divisor de pagos</a>
        <a href="../carrito/index.php">🛒Carrito de venta</a>
        <a href="../ventas/index.php">💰Ventas</a>
        <a href="../../cerrarSesion.php" id="btnCerrarSesion">❌Cerrar Sesión</a>
    
    </aside>

    
        
    <?php
    include("../../conexion.php");
    $sql = "SELECT id_reserva, c.nombre, c.email, id_cancha, estado_reserva, fecha, hora FROM reservas r JOIN clientes c ON r.id_cliente = c.id_cliente  ORDER BY fecha, hora";
    $stmt = $conexion->query($sql);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <main class="contenidoGeneral">
    <div class="contenidoPrincipal">  

    <table class="tablaListar">
        <thead>
            <tr>
                <th>Numero de reserva</th>
                <th>Cliente</th>
                <th>Cancha</th>
                <th>Fecha y Hora</th>
                <th>Estado de reserva</th>
                <th align="center">Acciones</th>

            </tr>
        </thead>
        <tbody>
            <?php if (count($resultados) > 0): 
                 foreach ($resultados as $fila): ?>
                    <tr>
                        <td class="datosTabla"><?php echo htmlspecialchars($fila['id_reserva']); ?></td>
                        <td>
                            <div class="infoCliente">
                            <div class="avatarCliente"><?php echo substr(htmlspecialchars($fila['nombre']), 0, 1); ?></div>
                            <div>
                                <p class="nombreCliente"><?php echo htmlspecialchars($fila['nombre']); ?></p>
                                <p class="emailCliente"><?php echo htmlspecialchars($fila['email']); ?></p>
                            </div>
                            </div>
                        </td>
                        <td class="datosTabla"><?php echo htmlspecialchars($fila['id_cancha'] == 2 ? 'Cancha 2' : 'Cancha 1'); ?></td>
                        <td>
                            <p class="fechaReserva"><?php echo htmlspecialchars($fila['fecha']); ?></p>
                            <p class="horaReserva"><?php echo htmlspecialchars($fila['hora']); ?></p>
                        </td>
                        <td><span class="datosTabla"><?php echo htmlspecialchars($fila['estado_reserva']); ?></span></td>
                        <td id="accionesGrilla">
                            <form action="finalizar_reserva.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $fila['id_reserva']; ?>">
                                <button type="submit" class="botonGenerico btnEditarRegistro">Finalizar</button>
                            </form> 
                            <a href="agregar_reserva.php?id=<?php echo $fila['id_reserva']; ?>">
                                <button class="botonGenerico btnEditarRegistro">Editar</button>
                            </a>
                            <form action="eliminar_reserva.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $fila['id_reserva']; ?>">
                                <button type="submit" class="botonGenerico btnEditarRegistro">Eliminar</button>
                            </form>
                              
                        </td>
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
    <div class="botonesCrud">
        <a href="agregar_reserva.php">
        <button name="btnCrearReserva" type="submit" class="botonGenerico btnCrud">Nueva Reserva</button>
        </a>
    </div>
    </div>
    <footer>
        <p>&copy; 2026 Punto Padel. Todos los derechos reservados.</p>  
    </footer>
        </main>
    
  
</body>
</html>