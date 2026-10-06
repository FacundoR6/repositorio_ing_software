-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-09-2026 a las 23:47:28
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `complejo_padel`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canchas`
--

CREATE TABLE `canchas` (
  `id_cancha` int(11) NOT NULL,
  `descripcion` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `canchas`
--

INSERT INTO `canchas` (`id_cancha`, `descripcion`) VALUES
(1, 'Cancha 1'),
(2, 'Cancha 2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(30) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `reservas_realizadas` int(11) DEFAULT NULL,
  `suspendido` varchar(2) DEFAULT NULL,
  `nombre_usuario` varchar(20) DEFAULT NULL,
  `contraseña` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre`, `email`, `telefono`, `reservas_realizadas`, `suspendido`, `nombre_usuario`, `contraseña`) VALUES
(1, 'Facundo Rojas', 'facundorojasfacu776@gmail.com', '3765038063', 4, 'No', 'admin', '1234'),
(2, 'Camila Castillo', 'camilagabriela1104@gmail.com', '3765047401', 2, 'No', 'Cami', 'lamaspro'),
(11, 'Laura Valdez', 'laura@gmail.com', '3764699101', 0, 'No', 'laura', 'laura123'),
(12, 'Jose Sanchez', 'jose@gmail.com', '3764975930', 0, 'No', 'jose1', '784512'),
(14, 'Bruno Rojas', 'bruno@gmail.com', '2937492763', 0, 'No', 'bruno1', 'brunito123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id_detalle` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `nombre_carrito` varchar(30) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` int(11) NOT NULL,
  `subtotal` int(10) NOT NULL,
  `clientes_pagados` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id_detalle`, `id_venta`, `nombre_carrito`, `id_producto`, `cantidad`, `precio`, `subtotal`, `clientes_pagados`) VALUES
(1, 1, 'prueba1', 1, 1, 4000, 4000, NULL),
(2, 2, 'mesa2', 1, 3, 6000, 18000, NULL),
(3, 3, 'casual1', 6, 1, 1500, 1500, NULL),
(7, 16, 'prueba2', 3, 1, 2500, 2500, NULL),
(8, 17, 'prueba3', 1, 1, 4500, 4500, NULL),
(9, 17, 'prueba3', 2, 1, 3500, 3500, NULL),
(10, 18, 'cancha1', 1, 1, 5500, 5500, NULL),
(11, 18, 'cancha1', 1, 1, 5500, 5500, NULL),
(12, 18, 'cancha1', 2, 1, 3500, 3500, NULL),
(13, 19, 'cancha1', 1, 1, 5500, 5500, NULL),
(14, 19, 'cancha1', 1, 1, 5500, 5500, NULL),
(15, 19, 'cancha1', 6, 1, 1500, 1500, NULL),
(21, 21, 'cancha1', 1, 1, 5500, 5500, NULL),
(22, 21, 'cancha1', 1, 1, 5500, 5500, NULL),
(25, 23, 'cancaha18', 1, 1, 5500, 5500, NULL),
(26, 24, 'cancha20', 1, 1, 5500, 5500, NULL),
(27, 24, 'cancha20', 3, 1, 2500, 2500, NULL),
(28, 25, 'cancha1', 1, 1, 5500, 5500, NULL),
(29, 25, 'cancha1', 1, 1, 5500, 5500, NULL),
(30, 25, 'cancha1', 4, 1, 3000, 3000, NULL),
(31, 25, 'cancha1', 3, 1, 2500, 2500, NULL),
(32, 25, 'cancha1', 3, 1, 2500, 2500, NULL),
(33, 25, 'cancha1', 2, 1, 3500, 3500, NULL),
(37, 28, 'mesa1', 1, 1, 5500, 5500, NULL),
(38, 28, 'mesa1', 1, 1, 5500, 5500, NULL),
(39, 28, 'mesa1', 2, 1, 3500, 3500, NULL),
(40, 28, 'mesa1', 3, 1, 2500, 2500, NULL),
(41, 28, 'mesa1', 4, 1, 3000, 3000, NULL),
(42, 29, 'prueba', 3, 1, 2500, 2500, NULL),
(43, 29, 'prueba', 3, 1, 2500, 2500, NULL),
(44, 29, 'prueba', 1, 1, 5500, 5500, NULL),
(45, 29, 'prueba', 1, 1, 5500, 5500, NULL),
(46, 30, 'mesa3', 3, 1, 2500, 2500, NULL),
(47, 31, 'p2', 3, 1, 2500, 2500, NULL),
(48, 32, 'p5', 6, 1, 1500, 1500, NULL),
(49, 33, 'prueba/9', 3, 1, 2500, 2500, NULL),
(50, 33, 'prueba/9', 3, 1, 2500, 2500, NULL),
(51, 33, 'prueba/9', 3, 1, 2500, 2500, NULL),
(52, 33, 'prueba/9', 3, 1, 2500, 2500, NULL),
(53, 33, 'prueba/9', 3, 1, 2500, 2500, NULL),
(54, 33, 'prueba/9', 1, 1, 5500, 5500, NULL),
(55, 33, 'prueba/9', 1, 1, 5500, 5500, NULL),
(56, 33, 'prueba/9', 1, 1, 5500, 5500, NULL),
(57, 34, 'Cancha 1 Camila', 1, 1, 5500, 5500, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_stock`
--

CREATE TABLE `movimientos_stock` (
  `id_movimiento` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad_anterior` int(11) NOT NULL,
  `cantidad_nueva` int(11) NOT NULL,
  `diferencia` int(11) NOT NULL,
  `precio_anterior` int(11) NOT NULL,
  `precio_nuevo` int(11) NOT NULL,
  `observacion` varchar(100) NOT NULL,
  `tipo_movimiento` varchar(20) NOT NULL,
  `nombre_usuario` varchar(30) NOT NULL,
  `fecha_movimiento` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos_stock`
--

INSERT INTO `movimientos_stock` (`id_movimiento`, `id_producto`, `cantidad_anterior`, `cantidad_nueva`, `diferencia`, `precio_anterior`, `precio_nuevo`, `observacion`, `tipo_movimiento`, `nombre_usuario`, `fecha_movimiento`) VALUES
(1, 2, 4, 7, 3, 3500, 3500, 'Compra en oferta.', 'ACTUALIZACION', 'admin', '2026-09-15'),
(2, 2, 7, 10, 3, 3500, 3500, 'Compra semanal', 'ACTUALIZACION', 'admin', '2026-09-15'),
(3, 3, 5, 12, 7, 2500, 2500, 'Encontrado en el deposito.', 'ACTUALIZACION', 'admin', '2026-09-19'),
(4, 6, 12, 11, -1, 1500, 1500, 'Pinchado x1/unidad.', 'ACTUALIZACION', 'admin', '2026-09-20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_cancha` int(11) NOT NULL,
  `estado_reserva` varchar(10) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id_reserva`, `id_cliente`, `id_cancha`, `estado_reserva`, `fecha`, `hora`) VALUES
(1, 1, 2, 'Pendiente', '2026-09-08', '18:00:00'),
(2, 2, 2, 'Pagada', '2026-06-22', '18:00:00'),
(4, 2, 2, 'Señada', '2026-08-10', '22:00:00'),
(8, 11, 1, 'Pendiente', '2026-08-27', '18:00:00'),
(10, 1, 1, 'Pendiente', '2026-09-19', '20:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock`
--

CREATE TABLE `stock` (
  `id_producto` int(11) NOT NULL,
  `descripcion` varchar(30) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `stock`
--

INSERT INTO `stock` (`id_producto`, `descripcion`, `cantidad`, `precio`) VALUES
(1, 'Coca Cola 1,5lts', 10, 5500),
(2, 'Sprite 1,5lts', 10, 3500),
(3, 'Fanta 1.5lts', 11, 2500),
(4, 'Manaos Cola 3lts', 7, 3000),
(6, 'Tubo Pelotas', 10, 1500);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suspension`
--

CREATE TABLE `suspension` (
  `id_suspension` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `observacion` varchar(30) DEFAULT NULL,
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `suspension`
--

INSERT INTO `suspension` (`id_suspension`, `id_cliente`, `observacion`, `fecha_desde`, `fecha_hasta`) VALUES
(1, 12, 'Deuda de cancha', '2026-09-08', '2026-09-10'),
(2, 11, 'Falta de respeto al personal', '2026-08-12', '2026-09-12'),
(3, 11, 'Falta de pago', '2026-09-14', '2026-09-21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `fecha_venta` datetime DEFAULT NULL,
  `metodo_pago` varchar(30) NOT NULL,
  `monto_total` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_cliente`, `fecha_venta`, `metodo_pago`, `monto_total`) VALUES
(1, 1, '2026-05-29 21:00:00', 'Efectivo', 4000),
(2, 1, '2026-08-17 21:55:57', 'Mercado Pago', 18000),
(3, 2, '2026-08-17 21:57:40', 'Transferencia', 1500),
(16, 1, '2026-08-18 20:15:08', 'Efectivo', 2500),
(17, 2, '2026-08-18 20:16:02', 'Mercado Pago', 8000),
(18, 1, '2026-08-19 10:49:57', 'Mercado Pago', 14500),
(19, 1, '2026-08-21 07:33:59', 'Mercado Pago', 12500),
(21, 1, '2026-08-24 20:42:43', 'Mercado Pago', 11000),
(23, 2, '2026-08-24 20:48:53', 'Efectivo', 5500),
(24, 1, '2026-08-24 20:49:17', 'Mercado Pago', 8000),
(25, 2, '2026-08-24 21:36:04', 'Transferencia', 22500),
(28, 1, '2026-08-27 20:14:50', 'Mercado Pago', 20000),
(29, 1, '2026-09-07 18:51:27', 'Mercado Pago', 16000),
(30, 2, '2026-09-07 19:02:14', 'Efectivo', 2500),
(31, 11, '2026-09-07 19:03:10', 'Efectivo', 2500),
(32, 2, '2026-09-07 19:07:57', 'Efectivo', 1500),
(33, 1, '2026-09-14 21:02:14', 'Efectivo', 29000),
(34, 2, '2026-09-19 18:54:49', 'Mercado Pago', 5500);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `canchas`
--
ALTER TABLE `canchas`
  ADD PRIMARY KEY (`id_cancha`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `fk_stock_detalle_venta` (`id_producto`),
  ADD KEY `fk_venta_detalle_venta` (`id_venta`) USING BTREE;

--
-- Indices de la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `fk_stock_movimientos_stock` (`id_producto`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `fk_cliente_reserva` (`id_cliente`),
  ADD KEY `fk_cancha_reserva` (`id_cancha`);

--
-- Indices de la tabla `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id_producto`);

--
-- Indices de la tabla `suspension`
--
ALTER TABLE `suspension`
  ADD PRIMARY KEY (`id_suspension`),
  ADD KEY `fk_cliente_suspension` (`id_cliente`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `fk_cliente_ventas` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `canchas`
--
ALTER TABLE `canchas`
  MODIFY `id_cancha` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `stock`
--
ALTER TABLE `stock`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `suspension`
--
ALTER TABLE `suspension`
  MODIFY `id_suspension` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `fk_stock_detalle_venta` FOREIGN KEY (`id_producto`) REFERENCES `stock` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venta_detalle_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  ADD CONSTRAINT `fk_stock_movimientos_stock` FOREIGN KEY (`id_producto`) REFERENCES `stock` (`id_producto`);

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_cancha_reserva` FOREIGN KEY (`id_cancha`) REFERENCES `canchas` (`id_cancha`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cliente_reserva` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `suspension`
--
ALTER TABLE `suspension`
  ADD CONSTRAINT `suspension_id_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_cliente_ventas` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
