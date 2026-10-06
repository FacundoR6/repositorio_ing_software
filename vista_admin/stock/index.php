
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="../../img/PuntoPadelLogo.png" />
  <title>Stock | Punto Padel</title>
 
  <link rel="stylesheet" href="../../css/estilos.css">

</head>
<body class="body-admin">

<header class="headerAdmin">
    <a href="../index.php"><img src="../../img/PuntoPadelCartelSinFondo.png" alt="Logo de Punto Padel" id="logo1"></a>
    <h1 class="tituloPag">Stock | Punto Padel</h1>
    <img src="../../img/PelotaPadel.png" alt="Imagen del Complejo de Pádel" id="logo2">
</header>

   <aside class="aside-admin">
    
        <a href="../index.php">🏠Inicio</a>
        <a href="../Reservas/index.php">📅Reservas</a>
        <a href="../Clientes/index.php">👥Clientes</a>
        <a class="active" href="../Stock/index.php">📦Administrar Stock</a>
        <a href="../DivisorPagos/index.php">✂️Divisor de pagos</a>
        <a href="../Carrito/index.php">🛒Carrito de venta</a>
        <a href="../Ventas/index.php">💰Ventas</a>
        <a href="../../cerrarSesion.php" id="btnCerrarSesion">❌Cerrar Sesión</a>
    
    </aside>

    <?php
    include("../../conexion.php");

    $busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
    if ($busqueda !== '') {
        $sql = "SELECT id_producto, descripcion, cantidad, precio FROM stock WHERE descripcion LIKE :busqueda ORDER BY descripcion";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':busqueda' => '%' . $busqueda . '%']);
    } else {
        $sql = "SELECT id_producto, descripcion, cantidad, precio FROM stock ORDER BY descripcion";
        $stmt = $conexion->query($sql);
    }
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <main class="contenidoGeneral"> 
    <div class="contenidoPrincipal"> 

        
    <form method="GET" class="BuscarRegistro">
        <input type="text" name="buscar" placeholder="Nombre del producto" class="inputBuscar" value="<?php echo htmlspecialchars($busqueda); ?>">
        <button type="submit" class="botonGenerico btnBuscarRegistro">Buscar Producto</button>
    </form>
    <table class="tablaListar" >
        <thead>
            <tr>
                <th>ID del producto</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th align="center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($resultados) > 0): 
                 foreach ($resultados as $fila): ?>
                    <?php 
                      if ($fila['cantidad'] < 5) {
                        $claseStock = 'stockBajo';
                      } elseif ($fila['cantidad'] >= 5 && $fila['cantidad'] < 10) {
                        $claseStock = 'stockMedio';
                      } else {
                        $claseStock = 'stockCorrecto';
                      }; 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['id_producto']); ?></td>
                        <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                        <td class="<?php echo $claseStock; ?>"><?php echo htmlspecialchars($fila['cantidad']); ?></td>
                        <td>
                            <p>$<?php echo htmlspecialchars($fila["precio"]); ?></p>
                        </td>
                        <td id="accionesGrilla">
                            <a href="agregar_producto.php?id=<?php echo (int)$fila['id_producto']; ?>">
                                <button type="button" class="botonGenerico btnEditarRegistro">Editar</button>
                            </a>
                            <form action="eliminar_producto.php" method="POST">
                                <input type="hidden" name="id_producto" value="<?php echo (int)$fila['id_producto']; ?>">
                                <button type="submit" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');" class="botonGenerico btnEditarRegistro">Eliminar</button>
                            </form> 
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php 
            else: 
            ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No hay registros disponibles</td>
                </tr>
            <?php
            endif;
            ?>  
        </tbody>
    </table>
    <div class="botonesCrud">
         <a href="agregar_producto.php">
            <button class="botonGenerico btnCrud">Agregar Producto</button>
        </a>
    </div>


    <h2 class="tituloClientes">Movimientos de Stock</h2>

    <?php
    $sql = "SELECT ms.id_movimiento, ms.id_producto, s.descripcion, ms.cantidad_anterior, ms.cantidad_nueva, ms.diferencia, ms.precio_anterior, ms.precio_nuevo, ms.observacion, ms.tipo_movimiento, ms.nombre_usuario, ms.fecha_movimiento FROM movimientos_stock ms INNER JOIN stock s ON ms.id_producto=s.id_producto ORDER BY ms.fecha_movimiento DESC, ms.id_movimiento DESC";
    $stmt = $conexion->query($sql);
    $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    
    <table class="tablaListar" >
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Stock anterior</th>
                <th>Stock nuevo</th>
                <th>Diferencia</th>
                <th>Precio anterior</th>
                <th>Precio nuevo</th>
                <th>Tipo</th>
                <th>Fecha de Movimiento</th>
                <th>Observación</th>
                <th>Administrador</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($movimientos) > 0): 
                 foreach ($movimientos as $fila): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['descripcion'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo (int)$fila['cantidad_anterior']; ?></td>
                        <td><?php echo (int)$fila['cantidad_nueva']; ?></td>
                        <td><?php  
                            if((int)$fila['cantidad_anterior']<(int)$fila['cantidad_nueva']):
                                echo '+',(int)$fila['diferencia'];
                            else:
                                echo (int)$fila['diferencia'];
                            endif;
                            ?></td>
                        <td><?php echo (int)$fila['precio_anterior']; ?></td>
                        <td><?php echo (int)$fila['precio_nuevo']; ?></td>
                        <td><?php echo htmlspecialchars($fila['tipo_movimiento'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($fila['fecha_movimiento'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($fila['observacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre_usuario'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php 
            else: 
            ?>
                <tr>
                    <td colspan="9" style="text-align: center;">No hay movimientos registrados</td>
                </tr>
            <?php
            endif;
            ?>  
    </table>



    </div>
    <footer>
        <p>&copy; 2026 Punto Padel. Todos los derechos reservados.</p>  
    </footer>
        </main>


</body>
</html>