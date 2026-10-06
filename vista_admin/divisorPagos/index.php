<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="../../img/PuntoPadelLogo.png" />
  <title>Divisor de pagos | Punto Padel</title>
  <link rel="stylesheet" href="../../css/estilos.css">
</head>
<body class="body-admin">

<header class="headerAdmin">
    <a href="../index.php"><img src="../../img/PuntoPadelCartelSinFondo.png" alt="Logo de Punto Padel" id="logo1"></a>
    <h1 class="tituloPag">Divisor de Pagos | Punto Padel</h1>
    <img src="../../img/PelotaPadel.png" alt="Imagen del Complejo de Pádel" id="logo2">
</header>

  <aside class="aside-admin">
    <a href="../index.php">🏠Inicio</a>
    <a href="../Reservas/index.php">📅Reservas</a>
    <a href="../Clientes/index.php">👥Clientes</a>
    <a href="../Stock/index.php">📦Administrar Stock</a>
    <a class="active" href="../DivisorPagos/index.php">✂️Divisor de pagos</a>
    <a href="../Carrito/index.php">🛒Carrito de venta</a>
    <a href="../Ventas/index.php">💰Ventas</a>
    <a href="../../cerrarSesion.php" id="btnCerrarSesion">❌Cerrar Sesión</a>
  </aside>

  <?php
  include("../../conexion.php");
  
  // Consultar Productos en Stock
  $sql = "SELECT id_producto, descripcion, cantidad, precio FROM stock";
  $stmt = $conexion->query($sql);
  $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // No se necesita lista de clientes para división por cantidad
  ?>

  <main class="contenidoGeneral">
    <div class="contenidoPrincipal">
      
      <!-- BARRA SUPERIOR: SELECCIÓN DE CARRITO Y ACCESOS -->
      <div class="barraAccionesCarrito">
        
          <label for="selectCarritoDestino"><strong>Enviar al Carrito:</strong></label>
          <select id="selectCarritoDestino" class="selectorCarrito"></select>
        <div style="display: flex; align-items: center; gap: 10px;">
          <button type="button" id="btnNuevoCarrito" class="botonGenerico">+ Nuevo Carrito</button>
        </div>
        <a href="../Carrito/index.php" class="btnVerCarritos">
          🛒 Ver Carritos Activos
        </a>
      </div>

      <div class="contenedorTabla">
        <!-- TABLA PRODUCTOS DISPONIBLES -->
        <div class="tablaPanel">
          <h2 class="subtituloTabla">Productos disponibles</h2>
          <table class="tablaListar tablaProductos">
            <thead>
              <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($resultados) > 0):
                  foreach ($resultados as $fila): 
                    $claseStock = ($fila['cantidad'] < 5) ? 'stockBajo' : (($fila['cantidad'] < 10) ? 'stockMedio' : 'stockCorrecto');
                  ?>
                  <tr>
                    <td><?php echo htmlspecialchars($fila['id_producto']); ?></td>
                    <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                    <td class="<?php echo $claseStock; ?>"><?php echo htmlspecialchars($fila['cantidad']); ?></td>
                    <td>$<?php echo htmlspecialchars($fila['precio']); ?></td>
                    <td>
                      <button type="button" class="botonGenerico btnAñadirCarrito"
                              data-id="<?php echo (int)$fila['id_producto']; ?>"
                              data-descripcion="<?php echo htmlspecialchars($fila['descripcion'], ENT_QUOTES); ?>"
                              data-precio="<?php echo number_format((float)$fila['precio'], 2, '.', ''); ?>">
                        Añadir
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5">No hay productos en stock</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- TABLA DIVISOR DE PAGOS (BORRADOR) -->
        <div class="tablaPanel">
          <div class="BuscarRegistro">
            <h2 class="subtituloTabla">Dividir Consumo</h2>
            <button type="button" id="btnVenderTodos" class="botonGenerico ">Enviar Todo al Carrito</button>
          </div>
          
          <table class="tablaListar tablaResumen">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Clientes</th>
                <th>Precio p/p</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="detalleVentaBody">
              <tr id="filaVacia">
                <td colspan="5" class="celdaVacia">Aún no agregaste productos.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2026 Punto Padel. Todos los derechos reservados.</p>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const cuerpoTabla = document.getElementById('detalleVentaBody');
      const selectCarrito = document.getElementById('selectCarritoDestino');
      const btnNuevoCarrito = document.getElementById('btnNuevoCarrito');
      const btnVenderTodos = document.getElementById('btnVenderTodos');
      let filaVacia = document.getElementById('filaVacia');

      // --- MANEJO DE LOCALSTORAGE PARA CARRITOS MULTIPLES ---
      function obtenerCarritos() {
        const almacenados = localStorage.getItem('carritosPadel');
        if (!almacenados) {
          const inicial = { "Cancha 1": [] };
          localStorage.setItem('carritosPadel', JSON.stringify(inicial));
          return inicial;
        }
        return JSON.parse(almacenados);
      }

      function guardarCarritos(carritos) {
        localStorage.setItem('carritosPadel', JSON.stringify(carritos));
      }

      function actualizarSelectCarritos() {
        const carritos = obtenerCarritos();
        selectCarrito.innerHTML = '';
        Object.keys(carritos).forEach(nombre => {
          const option = document.createElement('option');
          option.value = nombre;
          option.textContent = nombre;
          selectCarrito.appendChild(option);
        });
      }

      btnNuevoCarrito.addEventListener('click', function () {
        const nombre = prompt('Ingrese el nombre del nuevo carrito (ej: Cancha 2, Nombre del cliente):');
        if (nombre && nombre.trim() !== '') {
          const carritos = obtenerCarritos();
          if (!carritos[nombre.trim()]) {
            carritos[nombre.trim()] = [];
            guardarCarritos(carritos);
            actualizarSelectCarritos();
            selectCarrito.value = nombre.trim();
          } else {
            alert('Ya existe un carrito con ese nombre.');
          }
        }
      });

      // --- FUNCIONES DEL DIVISOR ---
      function formatearPrecio(valor) {
        return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(valor);
      }

      function limpiarFilaVacia() {
        if (filaVacia && cuerpoTabla.contains(filaVacia)) filaVacia.remove();
      }

      function verificarTablaVacia() {
        if (cuerpoTabla.querySelectorAll('tr[data-producto-id]').length === 0) {
          cuerpoTabla.innerHTML = '<tr id="filaVacia"><td colspan="5" class="celdaVacia">Aún no agregaste productos.</td></tr>';
          filaVacia = document.getElementById('filaVacia');
        }
      }

      function actualizarTotales() {
        const filas = cuerpoTabla.querySelectorAll('tr[data-producto-id]');
        filas.forEach(function (fila) {
          const precio = parseFloat(fila.dataset.precio || 0);
          const cantidad = parseInt(fila.querySelector('.inputCantidad').value || 1, 10);
          const numPersonas = Math.max(1, parseInt(fila.querySelector('.inputPersonas').value || 1, 10));
          const monto = (precio * cantidad) / numPersonas;
          fila.querySelector('.montoPorPersona').textContent = formatearPrecio(monto);
        });
      }

      function actualizarStockVisual(idProducto, cantidad) {
        const boton = document.querySelector('.btnAñadirCarrito[data-id="' + idProducto + '"]');
        const fila = boton ? boton.closest('tr') : null;
        const celdaCantidad = fila ? fila.cells[2] : null;
        if (celdaCantidad) {
          const cantidadActual = parseInt(celdaCantidad.textContent, 10) || 0;
          celdaCantidad.textContent = Math.max(0, cantidadActual - cantidad);
        }
      }

      function actualizarStock(accion, idProducto, cantidad) {
        return fetch('../stock_carrito.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            accion: accion,
            id_producto: Number(idProducto),
            cantidad: Number(cantidad)
          })
        }).then(async response => {
          const data = await response.json().catch(() => ({}));
          if (!response.ok || !data.ok) {
            throw new Error(data.mensaje || 'No se pudo actualizar el stock.');
          }
          return data;
        });
      }

      function agregarProducto(boton) {
        const id = boton.dataset.id;
        const descripcion = boton.dataset.descripcion;
        const precio = parseFloat(boton.dataset.precio || 0);

        limpiarFilaVacia();

        const filaExistente = cuerpoTabla.querySelector('tr[data-producto-id="' + id + '"]');
        if (filaExistente) {
          const cantidadInput = filaExistente.querySelector('.inputCantidad');
          cantidadInput.value = parseInt(cantidadInput.value || 0, 10) + 1;
        } else {
          const fila = document.createElement('tr');
          fila.dataset.productoId = id;
          fila.dataset.precio = precio.toFixed(2);
          
          fila.innerHTML = `
            <td>${descripcion}</td>
            <td><input type="number" min="1" value="1" class="inputCantidad" style="width:50px;"></td>
            <td><input type="number" min="1" value="1" class="inputPersonas" style="width:50px;"></td>
            <td class="montoPorPersona">$0,00</td>
            <td>
              <button type="button" class="botonGenerico btnEnviarItem">Enviar</button>
              <button type="button" class="botonGenerico btnEliminarProducto">Eliminar</button>
            </td>
          `;

          cuerpoTabla.appendChild(fila);
        }
        actualizarTotales();
      }

      function enviarFilaAlCarrito(fila) {
        const carritoDestino = selectCarrito.value;
        if (!carritoDestino) {
          alert('Por favor creá o seleccioná un carrito de destino.');
          return;
        }

        const id = fila.dataset.productoId;
        const precio = parseFloat(fila.dataset.precio || 0);
        const descripcion = fila.cells[0].textContent.trim();
        const cantidad = parseInt(fila.querySelector('.inputCantidad').value || 1, 10);
        const numPersonas = Math.max(1, parseInt(fila.querySelector('.inputPersonas').value || 1, 10));

        const item = {
          id: id,
          descripcion: descripcion,
          precio: precio,
          cantidad: cantidad,
          clientes: numPersonas,
          clientesAsignados: [],
          montoPorPersona: (precio * cantidad) / numPersonas,
          montoTotal: precio * cantidad,
          reservado: true
        };

        actualizarStock('reservar', id, cantidad)
          .then(() => {
            const carritos = obtenerCarritos();
            if (!carritos[carritoDestino]) carritos[carritoDestino] = [];

            carritos[carritoDestino].push(item);
            guardarCarritos(carritos);

            fila.remove();
            actualizarStockVisual(id, cantidad);
            verificarTablaVacia();
            actualizarTotales();
            alert(`Producto enviado a "${carritoDestino}".`);
          })
          .catch(error => alert(error.message));
      }

      // --- LISTENERS ---
      document.querySelectorAll('.btnAñadirCarrito').forEach(boton => {
        boton.addEventListener('click', function () { agregarProducto(this); });
      });

      cuerpoTabla.addEventListener('input', function (e) {
        if (e.target.matches('.inputCantidad')) actualizarTotales();
      });

      cuerpoTabla.addEventListener('input', function (e) {
        if (e.target.matches('.inputCantidad') || e.target.matches('.inputPersonas')) actualizarTotales();
      });

      cuerpoTabla.addEventListener('click', function (e) {
        if (e.target.matches('.btnEliminarProducto')) {
          e.target.closest('tr').remove();
          verificarTablaVacia();
          actualizarTotales();
        } else if (e.target.matches('.btnEnviarItem')) {
          enviarFilaAlCarrito(e.target.closest('tr'));
        }
      });

      btnVenderTodos.addEventListener('click', function () {
        const filas = cuerpoTabla.querySelectorAll('tr[data-producto-id]');
        if (filas.length === 0) {
          alert('No hay productos para enviar.');
          return;
        }
        filas.forEach(fila => enviarFilaAlCarrito(fila));
      });

      // Inicialización
      actualizarSelectCarritos();
      actualizarTotales();
    });
  </script>
</body>
</html>