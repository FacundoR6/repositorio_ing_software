<?php
require_once '../../conexion.php';

$clientes = $conexion->query("SELECT id_cliente, nombre FROM clientes WHERE suspendido = 'No' ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="../../img/PuntoPadelLogo.png" />
  <title>Carrito de Venta | Punto Padel</title>
  <link rel="stylesheet" href="../../css/estilos.css">
  <style>
    .pestanas-carritos { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .btn-pestana { padding: 10px 18px; border: 1px solid #d3ff00; background: #ccc8; color: #222; cursor: pointer; border-radius: 5px; font-weight: bold; }
    .btn-pestana.activa { background: #1e1e1e; color: white; border-color: #d3ff00; font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; }
    .panel-carrito { background: #2229; padding: 20px; border-radius: 8px; border: 1px solid #d3ff00; margin-bottom: 35px; }
    .formulario-cobro { display: flex; gap: 15px; align-items: end; flex-wrap: wrap; margin: 18px 0; }
    .campo-cobro { display: flex; flex-direction: column; gap: 8px; color: #fff; font-size: 15px}
    .campo-cobro select { min-width: 270px; padding: 8px; border: 1px solid #d3ff00; background: #ccc; color: #222; border-radius: 6px;  }
    .campo-cobro select:focus { outline: none; border-color: #d3ff00; box-shadow: 0 0 5px #d3ff00; }
    .dialogo-metodo-pago { border: 1px solid #d3ff00; border-radius: 8px; background: #222; color: #fff; padding: 20px; }
    .dialogo-metodo-pago::backdrop { background: rgba(0, 0, 0, .65); }
    .dialogo-metodo-pago select { min-width: 240px; padding: 8px; margin: 12px 0; }
    .acciones-dialogo { display: flex; gap: 10px; justify-content: flex-end; }
    .acciones-dialogo button { width: auto; padding: 8px 14px; }
   </style>
</head>
<body class="body-admin">

<header class="headerAdmin">
    <a href="../index.php"><img src="../../img/PuntoPadelCartelSinFondo.png" alt="Logo de Punto Padel" id="logo1"></a>
    <h1 class="tituloPag">Carrito de venta | Punto Padel</h1>
    <img src="../../img/PelotaPadel.png" alt="Imagen del Complejo de Pádel" id="logo2">
</header>

  <aside class="aside-admin">
    <a href="../index.php">🏠Inicio</a>
    <a href="../Reservas/index.php">📅Reservas</a>
    <a href="../Clientes/index.php">👥Clientes</a>
    <a href="../Stock/index.php">📦Administrar Stock</a>
    <a href="../DivisorPagos/index.php">✂️Divisor de pagos</a>
    <a class="active" href="../Carrito/index.php">🛒Carrito de venta</a>
    <a href="../Ventas/index.php">💰Ventas</a>
    <a href="../../cerrarSesion.php" id="btnCerrarSesion">❌Cerrar Sesión</a>
  </aside>

  <main class="contenidoGeneral">
    <div class="contenidoPrincipal">
      
      <h2>Carritos Abiertos Activos</h2>
      
      <div id="contenedorPestanas" class="pestanas-carritos"></div>

      <div class="panel-carrito">
        <div class="seccionGenerica">
          <h3 id="tituloCarritoSeleccionado" >Seleccione un carrito</h3>
          <button type="button" id="btnEliminarCarritoCompleto" class="btnVaciarCarrito">
            🗑️ Cancelar/Vaciar este Carrito
          </button>
        </div>

        <div class="formulario-cobro">
          <div class="campo-cobro">
            <label for="selectClienteVenta"><strong>Cliente</strong></label>
            <select id="selectClienteVenta" required>
              <option value="">Seleccione un cliente</option>
              <?php foreach ($clientes as $cliente): ?>
                <option value="<?php echo (int)$cliente['id_cliente']; ?>"><?php echo htmlspecialchars($cliente['nombre']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="campo-cobro">
            <label for="selectMetodoPago"><strong>Método de pago</strong></label>
            <select id="selectMetodoPago">
              <option value="Efectivo">Efectivo</option>
              <option value="Tarjeta">Tarjeta</option>
              <option value="Transferencia">Transferencia</option>
            </select>
          </div>
        </div>

        <table class="tablaListar">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Clientes (División)</th>
              <th>Monto p/Persona</th>
              <th>Monto Item</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody id="cuerpoCarritoSeleccionado">
            <tr><td colspan="6" style="text-align:center;">Cargando...</td></tr>
          </tbody>
        </table>

        <div>
          <div>
            <h3>Total Acumulado del Carrito: <span id="txtMontoTotalCarrito" style="color:#28a745;">$0,00</span></h3>
          </div>
          <button type="button" id="btnFinalizarVenta" class="botonGenerico btnFinalizarVenta">
            ✅ Finalizar Venta y Cobrar
          </button>
        </div>
      </div>

      <div id="detalleVentaConfirmada" class="panelResumenVenta"></div>

    </div>
  </main>

  <footer>
    <p>&copy; 2026 Punto Padel. Todos los derechos reservados.</p>
  </footer>

  <dialog id="dialogoMetodoPago" class="dialogo-metodo-pago">
    <form method="dialog">
      <h3>Seleccionar método de pago</h3>
      <label for="selectMetodoPagoIndividual">Método de pago del cliente</label>
      <select id="selectMetodoPagoIndividual" required>
        <option value="">Seleccione un método</option>
        <option value="Efectivo">Efectivo</option>
        <option value="Tarjeta">Tarjeta</option>
        <option value="Transferencia">Transferencia</option>
        <option value="Mercado Pago">Mercado Pago</option>
      </select>
      <div class="acciones-dialogo">
        <button type="submit" class="botonGenerico" value="cancelar">Cancelar</button>
        <button type="submit" class="botonGenerico" value="confirmar">Confirmar</button>
      </div>
    </form>
  </dialog>

 <script>
    document.addEventListener('DOMContentLoaded', function () {
      const contenedorPestanas = document.getElementById('contenedorPestanas');
      const tituloCarrito = document.getElementById('tituloCarritoSeleccionado');
      const cuerpoTabla = document.getElementById('cuerpoCarritoSeleccionado');
      const txtMontoTotal = document.getElementById('txtMontoTotalCarrito');
      const btnFinalizarVenta = document.getElementById('btnFinalizarVenta');
      const btnEliminarCarrito = document.getElementById('btnEliminarCarritoCompleto');
      const contenedorResumen = document.getElementById('detalleVentaConfirmada');
      const selectCliente = document.getElementById('selectClienteVenta');
      const selectMetodo = document.getElementById('selectMetodoPago');
      const dialogoMetodoPago = document.getElementById('dialogoMetodoPago');
      const selectMetodoIndividual = document.getElementById('selectMetodoPagoIndividual');

      let carritoSeleccionadoKey = null;

      function seleccionarMetodoPago() {
        return new Promise(resolve => {
          selectMetodoIndividual.value = '';

          const finalizarSeleccion = () => {
            dialogoMetodoPago.removeEventListener('close', finalizarSeleccion);
            resolve(dialogoMetodoPago.returnValue === 'confirmar'
              ? selectMetodoIndividual.value
              : '');
          };

          dialogoMetodoPago.addEventListener('close', finalizarSeleccion);
          dialogoMetodoPago.showModal();
        });
      }

      function formatearPrecio(valor) {
        return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(valor);
      }

      function obtenerCarritos() {
        return JSON.parse(localStorage.getItem('carritosPadel')) || {};
      }

      function guardarCarritos(carritos) {
        localStorage.setItem('carritosPadel', JSON.stringify(carritos));
      }

      function devolverStock(item) {
        if (!item.reservado) {
          return Promise.resolve();
        }

        return fetch('../stock_carrito.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            accion: 'devolver',
            id_producto: Number(item.id || item.id_producto || 0),
            cantidad: Number(item.cantidad || 0)
          })
        }).then(async response => {
          const data = await response.json().catch(() => ({}));
          if (!response.ok || !data.ok) {
            throw new Error(data.mensaje || 'No se pudo devolver el stock.');
          }
          return data;
        });
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

      function prepararEstadoPagos(item) {
        const cantidadPersonas = Math.max(1, Number.parseInt(item.clientes, 10) || 1);

        if (!Array.isArray(item.clientesAsignados) || item.clientesAsignados.length !== cantidadPersonas) {
          item.clientesAsignados = Array.from(
            { length: cantidadPersonas },
            (_, i) => item.clientesAsignados?.[i] || `Persona ${i + 1}`
          );
        }

        if (!Array.isArray(item.clientesPagados)) {
          item.clientesPagados = [];
        }

        item.clientesPagados = item.clientesPagados
          .slice(0, cantidadPersonas)
          .map(pago => typeof pago === 'string'
            ? { nombre: pago, metodoPago: 'No especificado' }
            : {
                nombre: pago.nombre || 'Sin nombre',
                metodoPago: pago.metodoPago || 'No especificado'
              });
        return {
          totalPersonas: cantidadPersonas,
          personasPagadas: item.clientesPagados.length,
          estaPagado: item.clientesPagados.length >= cantidadPersonas
        };
      }

      function recalcularImportes(item) {
        const cantidad = Math.max(1, Number.parseInt(item.cantidad, 10) || 1);
        const precio = Number(item.precio) || 0;
        const personas = Math.max(1, Number.parseInt(item.clientes, 10) || 1);

        item.cantidad = cantidad;
        item.montoTotal = precio * cantidad;
        item.montoPorPersona = item.montoTotal / personas;
      }

      function enviarVentaAlServidor(items, nombreCarrito) {
        const idCliente = Number(selectCliente.value);
        const metodoPago = selectMetodo.value;

        if (!idCliente) {
          throw new Error('Debe seleccionar un cliente antes de cobrar.');
        }

        const payload = {
          id_cliente: idCliente,
          nombre_carrito: nombreCarrito,
          metodo_pago: metodoPago,
          items: items.map(item => ({
            id: Number(item.id || item.id_producto || 0),
            cantidad: Number(item.cantidad || 0),
            precio: Number(item.precio || 0),
            clientes_pagados: Array.isArray(item.clientesPagados) ? item.clientesPagados : []
          }))
        };

        return fetch('../guardar_venta.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        }).then(async response => {
          const data = await response.json().catch(() => ({}));
          if (!response.ok || !data.ok) {
            throw new Error(data.mensaje || 'No se pudo guardar la venta.');
          }
          return data;
        });
      }

      function renderizarPestanas() {
        const carritos = obtenerCarritos();
        const llaves = Object.keys(carritos);
        contenedorPestanas.innerHTML = '';

        if (llaves.length === 0) {
          tituloCarrito.textContent = 'No hay carritos abiertos';
          cuerpoTabla.innerHTML = '<tr><td colspan="6" style="text-align:center;">No hay carritos activos. Podés crear uno desde el Divisor de Pagos.</td></tr>';
          txtMontoTotal.textContent = '$0,00';
          carritoSeleccionadoKey = null;
          return;
        }

        if (!carritoSeleccionadoKey || !carritos[carritoSeleccionadoKey]) {
          carritoSeleccionadoKey = llaves[0];
        }

        llaves.forEach(nombre => {
          const btn = document.createElement('button');
          btn.className = `btn-pestana ${nombre === carritoSeleccionadoKey ? 'activa' : ''}`;
          btn.textContent = `${nombre} (${carritos[nombre].length})`;
          btn.addEventListener('click', function () {
            carritoSeleccionadoKey = nombre;
            renderizarPestanas();
            renderizarDetalleCarrito();
          });
          contenedorPestanas.appendChild(btn);
        });

        renderizarDetalleCarrito();
      }

      function renderizarDetalleCarrito() {
        if (!carritoSeleccionadoKey) return;

        const carritos = obtenerCarritos();
        const items = carritos[carritoSeleccionadoKey] || [];

        tituloCarrito.textContent = `Detalle de: ${carritoSeleccionadoKey}`;
        cuerpoTabla.innerHTML = '';

        if (items.length === 0) {
          cuerpoTabla.innerHTML = '<tr><td colspan="6" style="text-align:center;">Este carrito no tiene productos cargados.</td></tr>';
          txtMontoTotal.textContent = '$0,00';
          return;
        }

        let acumTotal = 0;

        items.forEach((item, index) => {
          recalcularImportes(item);
          const estadoPagos = prepararEstadoPagos(item);
          acumTotal += item.montoTotal;

          let textoClientes = '';
          textoClientes = estadoPagos.estaPagado
            ? `✅ Pagado (${estadoPagos.personasPagadas}/${estadoPagos.totalPersonas})`
            : `${estadoPagos.personasPagadas}/${estadoPagos.totalPersonas}`;

          const fila = document.createElement('tr');
          fila.innerHTML = `
            <td>${item.descripcion}</td>
            <td><strong>${item.cantidad}</strong></td>
            <td>${textoClientes}</td>
            <td>${formatearPrecio(item.montoPorPersona)}</td>
            <td>${formatearPrecio(item.montoTotal)}</td>
            <td>
              <button type="button" class="botonGenerico" onclick="realizarPagoIndividual(${index})" ${estadoPagos.estaPagado ? 'disabled' : ''} style="background:#ffc107; color:black; border:none; padding:3px 7px; border-radius:3px; cursor:pointer;">Pago Individual</button>
              <button type="button" class="botonGenerico" onclick="aumentarCantidad(${index})" ${estadoPagos.personasPagadas > 0 ? 'disabled' : ''} style="background:#28a745; margin-top:5px; margin-bottom:5px; color:white; border:none; padding:3px 7px; border-radius:3px; cursor:pointer;">+1</button>
              <button type="button" class="botonGenerico" onclick="quitarItem(${index})" ${estadoPagos.personasPagadas > 0 ? 'disabled' : ''} style="background:#dc3545; color:white; border:none; padding:3px 7px; border-radius:3px; cursor:pointer;">Quitar</button>
            </td>
            <td><button type="button" class="botonGenerico" onclick="verDetalle(${index})" style="background:#17a2b8; color:white; border:none; padding:3px 7px; border-radius:3px; cursor:pointer; background-color: #2f3f0f;">👁️</button></td>
            `;
          cuerpoTabla.appendChild(fila);
        });

        guardarCarritos(carritos);
        txtMontoTotal.textContent = formatearPrecio(acumTotal);
      }

      window.aumentarCantidad = function (index) {
        const carritos = obtenerCarritos();
        const items = carritos[carritoSeleccionadoKey];
        const item = items && items[index];

        if (!item) return;

        const estadoPagos = prepararEstadoPagos(item);
        if (estadoPagos.estaPagado) {
          alert('Este producto ya está completamente pagado y no se puede modificar.');
          return;
        }

        const cantidadActual = Math.max(1, Number.parseInt(item.cantidad, 10) || 1);
        if (estadoPagos.personasPagadas > 0) {
          alert('Este producto ya tiene pagos iniciados y no se puede modificar.');
          return;
        }

        const actualizarCantidad = () => {
          item.cantidad = cantidadActual + 1;
          recalcularImportes(item);
          guardarCarritos(carritos);
          renderizarPestanas();
        };

        if (item.reservado) {
          actualizarStock('reservar', item.id || item.id_producto, 1)
            .then(actualizarCantidad)
            .catch(error => alert(error.message));
          return;
        }

        actualizarCantidad();
      };

     
      window.quitarItem = function (index) {
        const carritos = obtenerCarritos();
        const items = carritos[carritoSeleccionadoKey];
        if (items && items[index]) {
          const item = items[index];
          const estadoPagos = prepararEstadoPagos(item);
          if (estadoPagos.personasPagadas > 0) {
            alert('Este producto ya tiene pagos iniciados y no se pueden quitar unidades.');
            return;
          }

          const cantidadActual = Math.max(1, Number.parseInt(item.cantidad, 10) || 1);
          const cantidadIngresada = prompt(`¿Cuántas unidades de "${item.descripcion}" desea quitar? (1-${cantidadActual})`, '1');
          if (cantidadIngresada === null) return;

          const cantidadQuitar = Number.parseInt(cantidadIngresada, 10);
          if (!Number.isInteger(cantidadQuitar) || cantidadQuitar < 1 || cantidadQuitar > cantidadActual) {
            alert(`Ingrese una cantidad entera entre 1 y ${cantidadActual}.`);
            return;
          }

          if (!confirm(`¿Seguro que quiere quitar ${cantidadQuitar} unidad(es) de este producto?`)) return;

          const quitarDelCarrito = () => {
            if (cantidadQuitar === cantidadActual) {
              items.splice(index, 1);
            } else {
              item.cantidad = cantidadActual - cantidadQuitar;
              recalcularImportes(item);
            }
            guardarCarritos(carritos);
            renderizarPestanas();
          };

          if (item.reservado) {
            devolverStock({ ...item, cantidad: cantidadQuitar })
              .then(quitarDelCarrito)
              .catch(error => alert(error.message));
          } else {
            quitarDelCarrito();
          }
        }
      };

        window.verDetalle = function (index) {
        const carritos = obtenerCarritos();
        const items = carritos[carritoSeleccionadoKey];
        if (items && items[index]) {
          const estadoPagos = prepararEstadoPagos(items[index]);
          const pagos = items[index].clientesPagados
            .map(pago => `${pago.nombre} - Método de pago: ${pago.metodoPago}`)
            .join('\n');
          if (estadoPagos.estaPagado) {
            alert('Este producto ya está totalmente completamente pagado.\n\nPersonas que pagaron:\n' + pagos);
            return;
          }
          else
          {
            if (items[index].clientesPagados.length === 0) {
              alert('Ninguna persona ha pagado este producto aún.');
            }
            else{
              alert(`Personas que pagaron este producto:\n${pagos}`);
            }
          }
        }
        guardarCarritos(carritos);
        renderizarPestanas();
      };


      btnEliminarCarrito.addEventListener('click', function () {
        if (!carritoSeleccionadoKey) return;
        if (confirm(`¿Seguro que querés borrar por completo el carrito "${carritoSeleccionadoKey}"?`)) {
          const carritos = obtenerCarritos();
          const items = carritos[carritoSeleccionadoKey] || [];
          Promise.all(items.map(devolverStock))
            .then(() => {
              delete carritos[carritoSeleccionadoKey];
              guardarCarritos(carritos);
              carritoSeleccionadoKey = null;
              renderizarPestanas();
            })
            .catch(error => alert(error.message));
        }
       
      });

      btnFinalizarVenta.addEventListener('click', function () {
        const carritos = obtenerCarritos();
        const items = carritos[carritoSeleccionadoKey] || [];

        if (!carritoSeleccionadoKey || items.length === 0) {
          alert('El carrito actual no tiene productos para cobrar.');
          return;
        }
        if (!selectCliente.value) {
          alert('Debe seleccionar un cliente antes de finalizar la venta.');
          return;
        }
       const tienePagosDivididos = items.some(item =>Number(item.clientes) > 1 && Array.isArray(item.clientesPagados) && item.clientesPagados.length > 0 );

      if (tienePagosDivididos) {
        const continuar = confirm('El carrito contiene productos con pagos divididos pendientes. ¿Quiere asignar el monto pendiente a un solo cliente?');
      if (!continuar) {
        return;
      }
  }

        const totalVenta = items.reduce((acumulado, item) => acumulado + ((Number(item.precio || 0) * Number(item.cantidad || 0))), 0);

        const confirmar = confirm(`¿Deseás finalizar la venta del carrito "${carritoSeleccionadoKey}" por ${formatearPrecio(totalVenta)}?`);
        if (!confirmar) return;

        enviarVentaAlServidor(items, carritoSeleccionadoKey)
          .then((resultado) => {
            const fechaHoraVenta = new Date().toLocaleString('es-AR', {
              day: '2-digit', month: '2-digit', year: 'numeric',
              hour: '2-digit', minute: '2-digit', second: '2-digit'
            });

            contenedorResumen.style.display = 'block';
            contenedorResumen.innerHTML = `
              <h2 style="margin-top:0; color:#28a745;">🎉 ¡Venta Finalizada con Éxito!</h2>
              <p><strong>Carrito:</strong> ${carritoSeleccionadoKey}</p>
              <p><strong>Cliente asociado:</strong> ${selectCliente.options[selectCliente.selectedIndex]?.text || 'Cliente sin nombre'}</p>
              <p><strong>Fecha y Hora de Cierre:</strong> ${fechaHoraVenta}</p>
              <p><strong>Método de pago:</strong> ${selectMetodo.value}</p>
              <p><strong>Venta N°:</strong> ${resultado.id_venta}</p>
              <p style="font-size: 18px;"><strong>Monto Cobrado:</strong> <span style="color:#28a745;">${formatearPrecio(Number(resultado.monto_total || totalVenta))}</span></p>
            `;

            delete carritos[carritoSeleccionadoKey];
            guardarCarritos(carritos);
            carritoSeleccionadoKey = null;
            renderizarPestanas();
          })
          .catch((error) => {
            alert(error.message);
          });
      });

      window.realizarPagoIndividual = async function(index) {  
        const carritos = obtenerCarritos();
        const items = carritos[carritoSeleccionadoKey];
        if (!items || !items[index]) return;

        const item = items[index];
        const estadoPagos = prepararEstadoPagos(item);
        if (estadoPagos.estaPagado) {
          alert('Este producto ya está completamente pagado.');
          return;
        }

        const montoIndividual = Number(item.montoPorPersona || 0);
        alert(`Pagos registrados: ${estadoPagos.personasPagadas}/${estadoPagos.totalPersonas}.\n\nCada persona debe pagar ${formatearPrecio(montoIndividual)}.`);
        
        const nombreQuePago = prompt(`¿A nombre de quién se registra el pago de ${formatearPrecio(montoIndividual)}?`);
        if (!nombreQuePago || nombreQuePago.trim() === '') return;

        const metodoIndividual = await seleccionarMetodoPago();
        if(!metodoIndividual || metodoIndividual.trim() === '') return;

        item.clientesPagados.push({
          nombre: nombreQuePago.trim(),
          metodoPago: metodoIndividual.trim()
        });
        const nuevoEstado = prepararEstadoPagos(item);

        if (nuevoEstado.estaPagado) {
          alert(`✅ Pago registrado a nombre de "${nombreQuePago.trim()}".\n\n🎉 ¡Todos los clientes pagaron este producto!`);

        } 
        else {
          alert(`✅ Pago de ${formatearPrecio(montoIndividual)} registrado a nombre de "${nombreQuePago.trim()}".\n\n⏳ Estado: ${nuevoEstado.personasPagadas}/${nuevoEstado.totalPersonas}.`);
        }

        guardarCarritos(carritos);
        renderizarPestanas();
        renderizarDetalleCarrito();
      };

      renderizarPestanas();
    });

</script>
</body>
</html>