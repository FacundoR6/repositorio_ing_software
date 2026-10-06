<?php 
    include ("../../conexion.php");
    include ("./control-suspendido.php");

    $sql = "SELECT id_suspension, c.nombre, observacion, fecha_desde, fecha_hasta FROM suspension s INNER JOIN clientes c ON c.id_cliente=s.id_cliente ORDER BY fecha_desde;";
    $stmt = $conexion->query($sql);

    $suspendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);




?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="../../img/PuntoPadelLogo.png" />
  <title>Clientes | Punto Padel</title>
 
  <link rel="stylesheet" href="../../css/estilos.css">

  
</head>
<body class="body-admin">

<header class="headerAdmin">
    <a href="../index.php"><img src="../../img/PuntoPadelCartelSinFondo.png" alt="Logo de Punto Padel" id="logo1"></a>
    <h1 class="tituloPag">Clientes | Punto Padel</h1>
    <img src="../../img/PelotaPadel.png" alt="Imagen del Complejo de Pádel" id="logo2">
</header>
   <aside class="aside-admin">
    
        <a href="../index.php">🏠Inicio</a>
        <a href="../Reservas/index.php">📅Reservas</a>
        <a class="active" href="../Clientes/index.php">👥Clientes</a>
        <a href="../Stock/index.php">📦Administrar Stock</a>
        <a href="../DivisorPagos/index.php">✂️Divisor de pagos</a>
        <a href="../Carrito/index.php">🛒Carrito de venta</a>
        <a href="../Ventas/index.php">💰Ventas</a>
        <a href="../../cerrarSesion.php" id="btnCerrarSesion">❌Cerrar Sesión</a>
    
    </aside>

    <?php
    include("../../conexion.php"); 

    $busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
    if ($busqueda !== '') {
        $sql = "SELECT id_cliente, nombre, email, telefono, reservas_realizadas, suspendido FROM clientes WHERE nombre LIKE :busqueda ORDER BY nombre";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':busqueda' => '%' . $busqueda . '%']);
    } else {
        $sql = "SELECT id_cliente, nombre, email, telefono, reservas_realizadas, suspendido FROM clientes ORDER BY nombre";
        $stmt = $conexion->query($sql);
    }
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <main class="contenidoGeneral">
    <div class="contenidoPrincipal"> 

        <h2 class="tituloClientes">Clientes asociados al complejo</h2>
    <form method="GET" class="BuscarRegistro">
        <input type="text" name="buscar" placeholder="Nombre del Cliente" class="inputBuscar" value="<?php echo htmlspecialchars($busqueda); ?>">
        <button type="submit" class="botonGenerico btnBuscarRegistro">Buscar Cliente</button>
    </form>
    <table class="tablaListar">
        <thead>
            <tr>
                <th>Numero de Cliente</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Telefono</th>
                <th>Reservas Realizadas</th>
                <th>Supendido</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($resultados) > 0): 
                 foreach ($resultados as $fila): ?>
                    <tr>
                        <td class="datosTabla"><?php echo htmlspecialchars($fila['id_cliente']); ?></td>
                        <td>
                            <div class="infoCliente"> 
                            <div class="avatarCliente"><?php echo substr(htmlspecialchars($fila['nombre']), 0, 1); ?></div>

                            <div>
                                <p class="nombreCliente"><?php echo htmlspecialchars($fila['nombre']); ?></p>
                            </div>
                            </div>
                        </td>
                        <td class="emailCliente"><span></span><?php echo htmlspecialchars($fila['email']); ?></td>
                        <td>
                            <p class="telefonoCliente"><?php echo htmlspecialchars($fila['telefono']); ?></p>
                        </td>
                        <td><span class="datosTabla"><?php echo htmlspecialchars($fila['reservas_realizadas']); ?></span></td>
                        <td><span class="datosTabla"><?php echo htmlspecialchars($fila['suspendido']); ?></span></td>
                        <td id="accionesGrilla">
                            <a href="agregar_cliente.php?id=<?php echo (int)$fila['id_cliente']; ?>">
                                <button type="button" class="botonGenerico btnEditarRegistro">Editar</button>
                            </a>
                            <form action='suspender_cliente.php' method='POST'>
                                <input type='hidden' name='id' value='<?php echo (int)$fila['id_cliente']; ?>'>
                                <button type='submit' class='botonGenerico btnEditarRegistro' <?php echo strtolower((string)$fila['suspendido']) === 'si' ? 'disabled title="El cliente ya está suspendido"' : ''; ?>>
                                    <?php echo strtolower((string)$fila['suspendido']) === 'si' ? 'Suspendido' : 'Suspender'; ?>
                                </button>
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
        <a href="agregar_cliente.php">
            <button class="botonGenerico btnCrud">Asociar Cliente</button>
        </a>
    </div>


    <h2 class="tituloClientes">Historial de clientes suspendidos</h2>

     

    <table class="tablaListar">
        <thead>
            <tr>
                <th>ID Suspensión</th>
                <th>Nombre</th>
                <th>Motivo de Suspensión</th>
                <th>Fecha de Suspensión</th>
                <th>Fecha Hasta</th>
                
            </tr>
        </thead>
        <tbody>
            <?php  
                if (count($suspendidos) > 0): 
                    foreach ($suspendidos as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['id_suspension']) ?></td>
                            <td><?php echo htmlspecialchars($fila['nombre']) ?></td>
                            <td><?php echo htmlspecialchars($fila['observacion']) ?></td>
                            <td><?php echo htmlspecialchars($fila['fecha_desde']) ?></td>
                            <td><?php echo htmlspecialchars($fila['fecha_hasta']) ?></td>
                        </tr>

                    <?php 
                    endforeach;  
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

    </div>
    <footer>
        <p>&copy; 2026 Punto Padel. Todos los derechos reservados.</p>  
    </footer>
    </main>
  

 


</body>
</html>