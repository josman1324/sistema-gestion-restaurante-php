-- phpMyAdmin SQL Dump
-- Version: Portfolio Edition
-- Base de datos: `restaurantephp`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE `cargos` (
  `id_cargo` int(10) UNSIGNED NOT NULL,
  `nombre_cargo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`id_cargo`, `nombre_cargo`) VALUES
(1, 'Administrador'),
(2, 'caja'),
(3, 'mesero'),
(4, 'cocina');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `email_cliente` varchar(100) NOT NULL,
  `clave_cliente` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comandas`
--

CREATE TABLE `comandas` (
  `id_comanda` int(10) UNSIGNED NOT NULL,
  `id_trabajador` int(10) UNSIGNED DEFAULT NULL,
  `id_cliente` int(10) UNSIGNED DEFAULT NULL,
  `numero_mesa` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `estado` enum('En espera','Entregado') DEFAULT 'En espera',
  `total` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comanda_detalle`
--

CREATE TABLE `comanda_detalle` (
  `id_detalle` int(10) UNSIGNED NOT NULL,
  `id_comanda` int(10) UNSIGNED NOT NULL,
  `id_menu` int(10) UNSIGNED NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menus`
--

CREATE TABLE `menus` (
  `id_menu` int(10) UNSIGNED NOT NULL,
  `nombre_menu` varchar(100) NOT NULL,
  `foto_menu` varchar(300) NOT NULL,
  `descripcion_menu` text DEFAULT NULL,
  `tipo_menu` enum('corriente','carta') NOT NULL,
  `precio_menu` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `menus`
--

INSERT INTO `menus` (`id_menu`, `nombre_menu`, `foto_menu`, `descripcion_menu`, `tipo_menu`, `precio_menu`) VALUES
(13, 'Bistec a Caballo', 'https://d2yoo3qu6vrk5d.cloudfront.net/pulzo-lite/images-resized/PP90R-h-o.png', 'Bistec de res con huevo frito, arroz y plátano maduro.', 'carta', 24000.00),
(14, 'Carne Asada', 'https://www.simplyrecipes.com/thmb/qlfuFwSvyzCUVDWoYlHH-s7G-DI=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/__opt__aboutcom__coeus__resources__content_migration__simply_recipes__uploads__2007__05__carne-asada-horiz-a-1400-f1b982e98a6b4a459bee8949b73f1ace.jpg', 'Carne asada acompañada con papas criollas y ensalada.', 'carta', 23000.00),
(15, 'Sobrebarriga en Salsa', 'https://chefgoya.b-cdn.net/wp-content/uploads/2024/11/Receta-de-Sobrebarriga-en-Salsa.png', 'Sobrebarriga sudada con arroz y aguacate.', 'carta', 25000.00),
(16, 'Lomo de Res al Ajillo', 'https://i.ytimg.com/vi/Ff6Fx2FWr30/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLAPE2AvJ3EcflYiK0Cr-0aUEfZZkQ', 'Lomo fino de res salteado en salsa de ajo.', 'carta', 28000.00),
(17, 'Churrasco', 'https://th.bing.com/th/id/OIP.qrEBAmbPvocKCHmGaosU0wHaE7?w=283&h=187&c=7&r=0&o=7&cb=12&dpr=1.3&pid=1.7&rm=3', 'Jugoso churrasco con papas a la francesa y ensalada.', 'carta', 26000.00),
(18, 'Filete de Res', 'https://cdn.shopify.com/s/files/1/0571/7557/2638/products/RES29-3_2b12f01e-9d02-4845-8642-350c18058259.jpg?v=1627841606', 'Filete de res a la plancha con papas y vegetales.', 'carta', 25000.00),
(19, 'Pechuga a la Plancha', 'https://tse3.mm.bing.net/th/id/OIP.nEsb1ePG4Rfg69YJYxSHtgHaE5?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Pechuga de pollo a la plancha con arroz y ensalada.', 'carta', 20000.00),
(20, 'Pollo al Curry', 'https://www.tusdietas.net/wp-content/uploads/2019/01/pollo-al-curry.jpg', 'Pollo en salsa de curry con arroz blanco.', 'carta', 21000.00),
(21, 'Pollo a la Naranja', 'https://tse4.mm.bing.net/th/id/OIP.rnzd4YMcKD40MgbX0TFiMAHaHa?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Pollo salteado con salsa de naranja y arroz jazmín.', 'carta', 23000.00),
(22, 'Brochetas de Pollo', 'https://content-cocina.lecturas.com/medio/2018/07/19/brochetas-de-pollo-con-mostaza-y-miel_c3caefd4_800x800.jpg', 'Brochetas de pollo con vegetales asados y papas rústicas.', 'carta', 22000.00),
(23, 'Arroz con Pollo', 'https://tse4.mm.bing.net/th/id/OIP.hpuw08IAKbdkcmdF1xhfhgHaJ4?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Clásico arroz con pollo y ensalada fresca.', 'carta', 18000.00),
(24, 'Pollo al Horno', 'https://1.bp.blogspot.com/-TBTB5W8jY3U/XyGWLzLCUXI/AAAAAAAAYO4/ue3gqEMYBRo3lTOqDzN8-SYi8l5iNFZBwCNcBGAsYHQ/s1600/pollo%2Bhorno%2B%25281%2529.jpg', 'Muslos de pollo al horno con papas doradas y ensalada.', 'carta', 20000.00),
(25, 'Chuleta Valluna', 'https://tse4.mm.bing.net/th/id/OIP._V5AMEpjTHelbxPPMN5XIAHaE7?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Chuleta de cerdo apanada con arroz, ensalada y patacón.', 'carta', 22000.00),
(26, 'Costillas BBQ', 'https://tse4.mm.bing.net/th/id/OIP.2I5LgEiTXtTAfzipwrTVvwHaHa?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Costillas de cerdo bañadas en salsa BBQ casera.', 'carta', 26000.00),
(27, 'Lomo de Cerdo en Salsa de Piña', 'https://canolalife.com/wp-content/uploads/2020/05/Lomo-de-cerdo-en-salsa-de-pina.webp', 'Lomo de cerdo en salsa agridulce de piña y papas fritas.', 'carta', 25000.00),
(28, 'Cerdo al Ajillo', 'https://cocinaabuenashoras.com/files/solomillo-de-cerdo-al-ajillo.jpg', 'Trozos de cerdo en salsa de ajo con arroz blanco.', 'carta', 24000.00),
(29, 'Chicharrón con Arepa', 'https://th.bing.com/th/id/R.a5332338cd5c27f655aa7f9d6565c89b?rik=3y6hhTW3HNvduA&pid=ImgRaw&r=0', 'Chicharrón crujiente con arepa y limón.', 'carta', 18000.00),
(30, 'Solomito de Cerdo', 'https://th.bing.com/th/id/OIP.HDyS7xTEvBipvWApqb4ScgHaEM?w=283&h=180&c=7&r=0&o=7&cb=12&dpr=1.3&pid=1.7&rm=3', 'Solomito de cerdo a la plancha con puré de papa.', 'carta', 25000.00),
(31, 'Mojarra Frita', 'https://th.bing.com/th/id/OIP.6OU7uGTmM2RFedgeLS23OgHaEw?w=269&h=180&c=7&r=0&o=7&cb=12&dpr=1.3&pid=1.7&rm=3', 'Mojarra frita con arroz con coco, patacón y ensalada.', 'carta', 27000.00),
(32, 'Bagre en Salsa Criolla', 'https://th.bing.com/th/id/OIP._3qVpf-q17xJepsRz5-Z8wHaE7?w=241&h=180&c=7&r=0&o=7&cb=12&dpr=1.3&pid=1.7&rm=3', 'Bagre en salsa criolla con arroz y patacón.', 'carta', 26000.00),
(33, 'Trucha al Ajillo', 'https://th.bing.com/th/id/R.13e88f7d0298dbc35c28b988fbd28f72?rik=8HpY7a9wLIgX4g&pid=ImgRaw&r=0', 'Trucha fresca al ajillo con papas al vapor.', 'carta', 28000.00),
(34, 'Filete de Pescado', 'https://th.bing.com/th/id/R.b47235f5c5b5164297d18a88ce0c4ce0?rik=b6l8p3I7vsMTAw&pid=ImgRaw&r=0', 'Filete de pescado a la plancha con arroz y ensalada.', 'carta', 24000.00),
(35, 'Cazuela de Pescado', 'https://tse3.mm.bing.net/th/id/OIP.VN2ShMDEs_MP5dhqct8ZaQHaEO?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Cazuela de pescado en salsa de coco y vegetales.', 'carta', 29000.00),
(36, 'Pargo Rojo Frito', 'https://th.bing.com/th/id/OIP.rzPo314gKPrZdIE0c35arQHaEK?w=333&h=187&c=7&r=0&o=7&cb=12&dpr=1.3&pid=1.7&rm=3', 'Pargo frito con arroz con coco y patacones.', 'carta', 30000.00),
(37, 'Cazuela de Mariscos', 'https://th.bing.com/th/id/OIP.gv9EQdQBk1BtmuVkkM5bCwHaE8?w=271&h=181&c=7&r=0&o=7&cb=12&dpr=1.3&pid=1.7&rm=3', 'Cazuela de camarones, pulpo y calamar en salsa cremosa.', 'carta', 32000.00),
(38, 'Arroz con Camarones', 'https://th.bing.com/th/id/OIP.ELklhTGxgjVg-uDAQPiZKgHaEA?w=281&h=180&c=7&r=0&o=7&cb=12&dpr=1.3&pid=1.7&rm=3', 'Arroz con camarones, pimentón y cebolla.', 'carta', 28000.00),
(39, 'Camarones al Ajillo', 'https://cdn.colombia.com/gastronomia/2013/02/12/camarones-al-ajillo-2954.jpg', 'Camarones salteados en salsa de ajo con arroz blanco.', 'carta', 30000.00),
(40, 'Pasta con Mariscos', 'https://th.bing.com/th/id/R.ab20bfbb495b3eaf37d122d5272cc23a?rik=l6NcpkFJaKinZw&pid=ImgRaw&r=0', 'Pasta con camarones, calamar y mejillones en salsa blanca.', 'carta', 29000.00),
(41, 'Filete de Salmón', 'https://tse2.mm.bing.net/th/id/OIP.DRs6Ku_lUnR2H0QUFr9dfgHaHa?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Filete de salmón a la plancha con puré de papa.', 'carta', 32000.00),
(42, 'Pulpo a la Gallega', 'https://recetinas.com/wp-content/uploads/2019/02/pulpo-a-feira-1024x682.jpg', 'Pulpo en rodajas con aceite de oliva y paprika.', 'carta', 34000.00),
(43, 'Arroz Mixto', 'https://decoriente.co/wp-content/uploads/2017/12/ARROZ-MIXTO.jpg', 'Arroz con pollo, cerdo y camarones.', 'carta', 24000.00),
(44, 'Arroz Marinero', 'https://cdn.colombia.com/gastronomia/2012/07/12/arroz-marinero-2905.jpg', 'Arroz con mezcla de mariscos y salsa de coco.', 'carta', 27000.00),
(45, 'Paella de Mariscos', 'https://canalcocina.es/medias/images/PaellaDeMariscosBrillante.jpg', 'Paella tradicional española con mariscos frescos.', 'carta', 30000.00),
(46, 'Arroz con Cerdo', 'https://tse1.mm.bing.net/th/id/OIP.A-br5Tmrj6vhMUsfajBlsAHaHa?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Arroz salteado con trozos de cerdo y verduras.', 'carta', 22000.00),
(47, 'Arroz Campesino', 'https://tse3.mm.bing.net/th/id/OIP.-ws0jXnH7EObG9bjEwlJxwHaEK?cb=12&w=1280&h=720&rs=1&pid=ImgDetMain&o=7&rm=3', 'Arroz con carne, pollo, plátano y huevo frito.', 'carta', 23000.00),
(48, 'Arroz Oriental', 'https://tse1.mm.bing.net/th/id/OIP.XjOfbhwVa4S69mDkVUGqmAAAAA?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Arroz estilo oriental con soya y vegetales salteados.', 'carta', 25000.00),
(49, 'Sancocho de Gallina', 'https://cdn.colombia.com/gastronomia/2011/07/28/sancocho-de-gallina-1594.gif', 'Sancocho tradicional con yuca, papa y mazorca.', 'carta', 24000.00),
(50, 'Ajiaco Santafereño', 'https://tse1.mm.bing.net/th/id/OIP.nNk-E2UGxaxmas8jLhPlTAHaE0?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Sopa espesa con pollo, papa criolla y mazorca.', 'carta', 26000.00),
(51, 'Cuchuco con Espinazo', 'https://tse1.mm.bing.net/th/id/OIP.mRHrn6mF3Pml_5pLt61WnQHaE7?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Guiso tradicional de cebada con cerdo.', 'carta', 22000.00),
(52, 'Mondongo Antioqueño', 'https://tse3.mm.bing.net/th/id/OIP.pNYy5sO35U_xZbjjXk3M6wHaEK?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Mondongo con garbanzos y papa criolla.', 'carta', 23000.00),
(53, 'Caldo de Costilla', 'https://tse1.mm.bing.net/th/id/OIP.6zWXtbg8KHJYBaOSATUlZAHaE8?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3', 'Caldo caliente con costilla de res y papa.', 'carta', 20000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_dias`
--

CREATE TABLE `menu_dias` (
  `id_menu_dia` int(11) NOT NULL,
  `id_menu` int(10) UNSIGNED NOT NULL,
  `dia_menu` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `menu_dias`
--

INSERT INTO `menu_dias` (`id_menu_dia`, `id_menu`, `dia_menu`) VALUES
(2, 13, 'Lunes'),
(2, 14, 'Lunes'),
(3, 14, 'Lunes'),
(15, 15, 'Miércoles'),
(21, 16, 'Jueves'),
(27, 17, 'Viernes'),
(33, 18, 'Sábado'),
(4, 19, 'Lunes'),
(3, 20, 'Lunes'),
(16, 21, 'Miércoles'),
(22, 22, 'Jueves'),
(28, 23, 'Viernes'),
(34, 24, 'Sábado'),
(5, 25, 'Lunes'),
(44, 25, 'Domingo'),
(11, 26, 'Martes'),
(17, 27, 'Miércoles'),
(23, 28, 'Jueves'),
(29, 29, 'Viernes'),
(35, 30, 'Sábado'),
(6, 31, 'Lunes'),
(2, 32, 'Lunes'),
(18, 33, 'Miércoles'),
(24, 34, 'Jueves'),
(7, 35, 'Lunes'),
(30, 35, 'Viernes'),
(13, 36, 'Martes'),
(36, 36, 'Sábado'),
(19, 37, 'Miércoles'),
(25, 38, 'Jueves'),
(31, 39, 'Viernes'),
(37, 40, 'Sábado'),
(8, 43, 'Lunes'),
(14, 44, 'Martes'),
(20, 45, 'Miércoles'),
(26, 46, 'Jueves'),
(3, 47, 'Lunes'),
(4, 47, 'Lunes'),
(38, 48, 'Sábado'),
(39, 49, 'Domingo'),
(3, 50, 'Lunes'),
(4, 50, 'Lunes'),
(41, 51, 'Domingo'),
(42, 52, 'Domingo'),
(43, 53, 'Domingo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(10) UNSIGNED NOT NULL,
  `id_comanda` int(10) UNSIGNED NOT NULL,
  `id_cajero` int(10) UNSIGNED DEFAULT NULL,
  `metodo_pago` enum('Efectivo','Tarjeta','Transferencia','Otro') DEFAULT 'Efectivo',
  `total_pagado` decimal(10,2) NOT NULL,
  `fecha_pago` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id_trabajador` int(10) UNSIGNED NOT NULL,
  `nombre_trabajador` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `id_cargo` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trabajadores`
--
-- NOTA PARA DESARROLLADORES:
-- La contraseña de ambos usuarios es: 12345
--
INSERT INTO `trabajadores` (`id_trabajador`, `nombre_trabajador`, `usuario`, `clave`, `id_cargo`) VALUES
(1, 'Administrador Demo', 'admin', '$2y$10$PWOY8JuLmo6X8ybaMJGqCebkZ6EN1bdardI/1X4HfOyTS2ZIsj8wi', 1),
(2, 'Mesero Demo', 'mesero', '$2y$10$PWOY8JuLmo6X8ybaMJGqCebkZ6EN1bdardI/1X4HfOyTS2ZIsj8wi', 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id_cargo`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `email_cliente` (`email_cliente`);

--
-- Indices de la tabla `comandas`
--
ALTER TABLE `comandas`
  ADD PRIMARY KEY (`id_comanda`),
  ADD KEY `id_trabajador` (`id_trabajador`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `comanda_detalle`
--
ALTER TABLE `comanda_detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_comanda` (`id_comanda`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indices de la tabla `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indices de la tabla `menu_dias`
--
ALTER TABLE `menu_dias`
  ADD PRIMARY KEY (`id_menu`,`id_menu_dia`),
  ADD KEY `fk_menu_dia_menu` (`id_menu`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_comanda` (`id_comanda`),
  ADD KEY `id_cajero` (`id_cajero`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`id_trabajador`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `id_cargo` (`id_cargo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

ALTER TABLE `cargos` MODIFY `id_cargo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `clientes` MODIFY `id_cliente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `comandas` MODIFY `id_comanda` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `comanda_detalle` MODIFY `id_detalle` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `menus` MODIFY `id_menu` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
ALTER TABLE `pagos` MODIFY `id_pago` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `trabajadores` MODIFY `id_trabajador` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

ALTER TABLE `comandas`
  ADD CONSTRAINT `comandas_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id_trabajador`),
  ADD CONSTRAINT `comandas_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

ALTER TABLE `comanda_detalle`
  ADD CONSTRAINT `comanda_detalle_ibfk_1` FOREIGN KEY (`id_comanda`) REFERENCES `comandas` (`id_comanda`),
  ADD CONSTRAINT `comanda_detalle_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menus` (`id_menu`);

ALTER TABLE `menu_dias`
  ADD CONSTRAINT `fk_menu_dia_menu` FOREIGN KEY (`id_menu`) REFERENCES `menus` (`id_menu`);

ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_comanda`) REFERENCES `comandas` (`id_comanda`) ON DELETE CASCADE,
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_cajero`) REFERENCES `trabajadores` (`id_trabajador`);

ALTER TABLE `trabajadores`
  ADD CONSTRAINT `trabajadores_ibfk_1` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;