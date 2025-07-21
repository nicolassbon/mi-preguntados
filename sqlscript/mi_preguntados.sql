-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-07-2025 a las 19:19:52
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mi_preguntados`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria`
(
    `id_categoria`   int(11)      NOT NULL,
    `nombre`         varchar(100) NOT NULL,
    `foto_categoria` varchar(255) DEFAULT NULL,
    `color`          text         NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre`, `foto_categoria`, `color`)
VALUES (1, 'Gastronomía', '../public/images/personajes/rojo.png', 'darkred'),
       (2, 'Historia', '../public/images/personajes/amarillo.png', 'goldenrod'),
       (3, 'Deporte', '../public/images/personajes/naranja.png', 'chocolate'),
       (4, 'Tecnología', '../public/images/personajes/celeste.png', 'cadetblue'),
       (5, 'Naturaleza', '../public/images/personajes/verde.png', 'darkgreen'),
       (6, 'Geografía', '../public/images/personajes/azul.png', 'darkblue'),
       (7, 'Música', '../public/images/personajes/violeta.png', 'blueviolet'),
       (8, 'Entretenimiento', '../public/images/personajes/rosa.png', 'hotpink');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

CREATE TABLE `ciudades`
(
    `id_ciudad`     int(11)      NOT NULL,
    `nombre_ciudad` varchar(100) NOT NULL,
    `id_pais`       int(11) DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras_trampitas`
--

CREATE TABLE `compras_trampitas`
(
    `id_compra`          int(11)        NOT NULL,
    `id_usuario`         int(11)        NOT NULL,
    `cantidad_comprada`  int(11)        NOT NULL,
    `monto_pagado`       decimal(10, 2) NOT NULL,
    `fecha_compra`       datetime DEFAULT current_timestamp(),
    `referencia_externa` varchar(255)   NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras_trampitas`
--

INSERT INTO `compras_trampitas` (`id_compra`, `id_usuario`, `cantidad_comprada`, `monto_pagado`, `fecha_compra`,
                                 `referencia_externa`)
VALUES (1, 4, 1, 1.00, '2025-07-15 22:59:10', 'trampitas_4_1_1_1752631138'),
       (2, 1, 1, 1.00, '2025-07-15 23:07:01', 'trampitas_1_1_1_1752631611'),
       (3, 1, 5, 5.00, '2025-07-15 23:07:21', 'trampitas_1_5_5_1752631627'),
       (4, 4, 5, 5.00, '2025-07-16 09:23:53', 'trampitas_4_5_5_1752668622'),
       (5, 4, 1, 1.00, '2025-07-16 09:38:59', 'trampitas_4_1_1_1752669530'),
       (6, 4, 1, 1.00, '2025-07-16 10:05:55', 'trampitas_4_1_1_1752671145'),
       (7, 4, 1, 1.00, '2025-07-16 10:34:12', 'trampitas_4_1_1_1752672841'),
       (8, 4, 1, 1.00, '2025-07-16 12:28:26', 'trampitas_4_1_1_1752679695'),
       (9, 1, 1, 1.00, '2025-07-19 16:44:38', 'trampitas_1_1_1_1752954267');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises`
(
    `id_pais`     int(11)      NOT NULL,
    `nombre_pais` varchar(100) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidas`
--

CREATE TABLE `partidas`
(
    `id_partida`    int(11)  NOT NULL,
    `id_usuario`    int(11)  NOT NULL,
    `fecha_inicio`  datetime NOT NULL DEFAULT current_timestamp(),
    `fecha_fin`     datetime          DEFAULT NULL,
    `puntaje_final` int(11)  NOT NULL DEFAULT 0,
    `correctas`     int(11)  NOT NULL DEFAULT 0
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partida_pregunta`
--

CREATE TABLE `partida_pregunta`
(
    `id_partida`           int(11)    NOT NULL,
    `id_pregunta`          int(11)    NOT NULL,
    `id_respuesta_elegida` int(11) DEFAULT NULL,
    `acerto`               tinyint(1) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas`
(
    `id_pregunta`    int(11)                                                NOT NULL,
    `pregunta`       varchar(500)                                           NOT NULL,
    `id_categoria`   int(11)                                                NOT NULL,
    `entregadas`     int(11)                                                NOT NULL DEFAULT 0,
    `correctas`      int(11)                                                NOT NULL DEFAULT 0,
    `estado`         enum ('activa','sugerida','reportada','deshabilitada') NOT NULL DEFAULT 'activa',
    `fecha_registro` datetime                                               NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preguntas`
--

INSERT INTO `preguntas` (`id_pregunta`, `pregunta`, `id_categoria`, `entregadas`, `correctas`, `estado`,
                         `fecha_registro`)
VALUES (1, '¿Cuál es el ingrediente principal del guacamole?', 1, 1, 1, 'activa', '2025-07-11 19:48:00'),
       (2, '¿Qué país es famoso por su plato llamado paella?', 1, 1, 1, 'activa', '2025-07-11 19:48:00'),
       (3, '¿Cuál es el queso que se utiliza tradicionalmente en la pizza?', 1, 1, 0, 'activa', '2025-07-11 19:48:00'),
       (4, '¿Qué tipo de pasta es en forma de tubo largo?', 1, 0, 0, 'activa', '2025-07-11 19:48:00'),
       (5, '¿Cuál es la carne utilizada en el tradicional asado argentino?', 1, 1, 1, 'activa', '2025-07-11 19:48:00'),
       (6, '¿Qué bebida alcohólica se utiliza en la preparación del mojito?', 1, 1, 0, 'activa', '2025-07-11 19:48:00'),
       (7, '¿Cuál es el pescado más común en el sushi?', 1, 0, 0, 'activa', '2025-07-11 19:48:00'),
       (8, '¿Qué país es famoso por el ramen?', 1, 1, 0, 'activa', '2025-07-11 19:48:00'),
       (9, '¿Qué fruto se utiliza para hacer el vino?', 1, 1, 1, 'activa', '2025-07-11 19:48:00'),
       (10, '¿Qué tipo de arroz se usa en la comida japonesa?', 1, 2, 1, 'activa', '2025-07-11 19:48:00'),
       (11, '¿Cuál es la especia que le da el color amarillo al curry?', 1, 2, 0, 'activa', '2025-07-11 19:48:00'),
       (12, '¿Qué es el tofu?', 1, 0, 0, 'activa', '2025-07-11 19:48:00'),
       (13, '¿Qué postre francés consiste en una masa ligera rellena de crema pastelera?', 1, 1, 1, 'activa',
        '2025-07-11 19:48:00'),
       (14, '¿Qué vegetal es base del gazpacho?', 1, 2, 0, 'activa', '2025-07-11 19:48:00'),
       (15, '¿Qué fruto seco se utiliza en el pesto tradicional?', 1, 0, 0, 'activa', '2025-07-11 19:48:00'),
       (16, '¿Qué país es el mayor productor de café del mundo?', 1, 0, 0, 'activa', '2025-07-11 19:48:00'),
       (17, '¿Cómo se llama el pan italiano plano con aceite de oliva y sal?', 1, 0, 0, 'activa',
        '2025-07-11 19:48:00'),
       (18, '¿Qué comida se asocia tradicionalmente con México?', 1, 0, 0, 'activa', '2025-07-11 19:48:00'),
       (19, '¿Qué tipo de cocción usa solo vapor?', 1, 1, 1, 'activa', '2025-07-11 19:48:00'),
       (20, '¿Qué fruta tropical es espinosa por fuera y amarilla por dentro?', 1, 0, 0, 'activa',
        '2025-07-11 19:48:00'),
       (21, '¿Qué cereal es base del whisky?', 1, 0, 0, 'activa', '2025-07-11 19:48:00'),
       (22, '¿Cuál es el ingrediente base del hummus?', 1, 0, 0, 'activa', '2025-07-11 19:48:00'),
       (23, '¿Qué alimento contiene más vitamina C?', 1, 2, 1, 'activa', '2025-07-11 19:48:00'),
       (24, '¿Qué alimento se obtiene de las abejas?', 1, 2, 2, 'activa', '2025-07-11 19:48:00'),
       (25, '¿Cuál es el principal ingrediente del chocolate? ', 1, 0, 0, 'activa', '2025-07-11 19:48:00'),
       (26, '¿En qué año comenzó la Primera Guerra Mundial?', 2, 1, 1, 'activa', '2025-07-11 19:56:41'),
       (27, '¿Quién fue el primer presidente de Estados Unidos?', 2, 1, 0, 'activa', '2025-07-11 19:56:41'),
       (28, '¿Qué civilización construyó las pirámides de Egipto?', 2, 1, 1, 'activa', '2025-07-11 19:56:41'),
       (29, '¿Qué muro cayó en 1989 marcando el fin de la Guerra Fría?', 2, 0, 0, 'activa', '2025-07-11 19:56:41'),
       (30, '¿Quién fue el conquistador del Imperio Azteca?', 2, 0, 0, 'activa', '2025-07-11 19:56:41'),
       (31, '¿En qué país nació Napoleón Bonaparte?', 2, 1, 0, 'activa', '2025-07-11 19:56:41'),
       (32, '¿Qué país fue el primero en abolir la esclavitud?', 2, 0, 0, 'activa', '2025-07-11 19:56:41'),
       (33, '¿Qué guerra enfrentó al norte y al sur de Estados Unidos?', 2, 1, 0, 'activa', '2025-07-11 19:56:41'),
       (34, '¿Qué imperio tenía a Julio César como figura destacada?', 2, 0, 0, 'activa', '2025-07-11 19:56:41'),
       (35, '¿Qué hecho histórico ocurrió el 20 de julio de 1969?', 2, 1, 1, 'activa', '2025-07-11 19:56:41'),
       (36, '¿Quién descubrió América en 1492?', 2, 0, 0, 'activa', '2025-07-11 19:56:41'),
       (37, '¿Qué país sufrió la bomba atómica en Hiroshima?', 2, 1, 1, 'activa', '2025-07-11 19:56:41'),
       (38, '¿Quién fue el líder del Tercer Reich en Alemania?', 2, 1, 1, 'activa', '2025-07-11 19:56:41'),
       (39, '¿Qué famosa reina tuvo un romance con Marco Antonio?', 2, 1, 1, 'activa', '2025-07-11 19:56:41'),
       (40, '¿Qué revolución dio lugar a la Declaración de los Derechos del Hombre?', 2, 1, 0, 'activa',
        '2025-07-11 19:56:41'),
       (41, '¿Qué civilización antigua desarrolló la democracia?', 2, 0, 0, 'activa', '2025-07-11 19:56:41'),
       (42, '¿Cuál fue la causa principal de la Segunda Guerra Mundial?', 2, 1, 1, 'activa', '2025-07-11 19:56:41'),
       (43, '¿Qué acontecimiento marcó el inicio de la Edad Media?', 2, 1, 0, 'activa', '2025-07-11 19:56:41'),
       (44, '¿Qué país lideró la Revolución Industrial?', 2, 0, 0, 'activa', '2025-07-11 19:56:41'),
       (45, '¿Qué general argentino lideró el cruce de los Andes?', 2, 0, 0, 'activa', '2025-07-11 19:56:41'),
       (46, '¿Qué tratado puso fin a la Primera Guerra Mundial?', 2, 1, 1, 'activa', '2025-07-11 19:56:41'),
       (47, '¿Qué nave zarpó desde Inglaterra en 1620 hacia América?', 2, 1, 1, 'activa', '2025-07-11 19:56:41'),
       (48, '¿En qué siglo ocurrió la Revolución Francesa?', 2, 1, 0, 'activa', '2025-07-11 19:56:41'),
       (49, '¿Qué conflicto enfrentó a EEUU y la URSS sin combates directos?', 2, 0, 0, 'activa',
        '2025-07-11 19:56:41'),
       (50, '¿Quién fue el libertador de Venezuela, Colombia y Ecuador?', 2, 0, 0, 'activa', '2025-07-11 19:56:41'),
       (51, '¿Cuántos jugadores tiene un equipo de fútbol en cancha?', 3, 0, 0, 'activa', '2025-07-11 20:04:39'),
       (52, '¿En qué deporte se usa un bate y una pelota pequeña?', 3, 0, 0, 'activa', '2025-07-11 20:04:39'),
       (53, '¿Qué atleta tiene más medallas olímpicas?', 3, 1, 0, 'activa', '2025-07-11 20:04:39'),
       (54, '¿Dónde se celebraron los primeros Juegos Olímpicos modernos?', 3, 1, 1, 'activa', '2025-07-11 20:04:39'),
       (55, '¿Cuál es la distancia oficial de una maratón?', 3, 1, 1, 'activa', '2025-07-11 20:04:39'),
       (56, '¿Qué país ganó la Copa Mundial de Fútbol en 2018?', 3, 1, 1, 'activa', '2025-07-11 20:04:39'),
       (57, '¿En qué deporte se realiza un “slam dunk”?', 3, 1, 1, 'activa', '2025-07-11 20:04:39'),
       (58, '¿Qué deporte se practica en Wimbledon?', 3, 0, 0, 'activa', '2025-07-11 20:04:39'),
       (59, '¿Quién es conocido como “El Rey del Fútbol”?', 3, 1, 1, 'activa', '2025-07-11 20:04:39'),
       (60, '¿Qué deporte utiliza un disco llamado “puck”?', 3, 1, 1, 'activa', '2025-07-11 20:04:39'),
       (61, '¿Cuántos puntos vale un try en rugby?', 3, 0, 0, 'activa', '2025-07-11 20:04:39'),
       (62, '¿Qué país es famoso por su equipo de cricket?', 3, 0, 0, 'activa', '2025-07-11 20:04:39'),
       (63, '¿En qué deporte se usan patines de hielo?', 3, 1, 1, 'activa', '2025-07-11 20:04:39'),
       (64, '¿Quién tiene el récord mundial de 100 metros planos?', 3, 0, 0, 'activa', '2025-07-11 20:04:39'),
       (65, '¿Cuál es la duración de un partido de tenis a cinco sets?', 3, 0, 0, 'activa', '2025-07-11 20:04:39'),
       (66, '¿Qué deporte combina esquí y tiro con rifle?', 3, 0, 0, 'activa', '2025-07-11 20:04:39'),
       (67, '¿En qué país se originó el taekwondo?', 3, 1, 0, 'activa', '2025-07-11 20:04:39'),
       (68, '¿Qué equipo ganó la NBA en 2020?', 3, 0, 0, 'activa', '2025-07-11 20:04:39'),
       (69, '¿Qué deporte se juega en la Super Bowl?', 3, 0, 0, 'activa', '2025-07-11 20:04:39'),
       (70, '¿Cuál es el nombre del torneo de golf más prestigioso?', 3, 1, 0, 'activa', '2025-07-11 20:04:39'),
       (71, '¿Qué país domina el polo a nivel mundial?', 3, 1, 1, 'activa', '2025-07-11 20:04:39'),
       (72, '¿En qué deporte se utiliza un casco y una pelota de cuero en un campo abierto?', 3, 0, 0, 'activa',
        '2025-07-11 20:04:39'),
       (73, '¿Qué ciudad alberga el maratón más famoso del mundo?', 3, 1, 1, 'activa', '2025-07-11 20:04:39'),
       (74, '¿Qué deporte olímpico utiliza una espada?', 3, 1, 1, 'activa', '2025-07-11 20:04:39'),
       (75, '¿Quién fue la primera mujer en ganar una medalla olímpica en atletismo?', 3, 0, 0, 'activa',
        '2025-07-11 20:04:39'),
       (76, '¿Quién es conocido como el padre de la computación?', 4, 0, 0, 'activa', '2025-07-11 20:14:40'),
       (77, '¿Qué significa la sigla “HTTP”?', 4, 2, 2, 'activa', '2025-07-11 20:14:40'),
       (78, '¿Cuál fue el primer sistema operativo de Microsoft?', 4, 0, 0, 'activa', '2025-07-11 20:14:40'),
       (79, '¿Qué lenguaje de programación es conocido por su simplicidad y legibilidad?', 4, 1, 0, 'activa',
        '2025-07-11 20:14:40'),
       (80, '¿Qué es un “firewall”?', 4, 1, 1, 'activa', '2025-07-11 20:14:40'),
       (81, '¿Cuál es el dispositivo principal para ingresar datos a una computadora?', 4, 1, 1, 'activa',
        '2025-07-11 20:14:40'),
       (82, '¿Qué compañía desarrolló el sistema operativo Android?', 4, 0, 0, 'activa', '2025-07-11 20:14:40'),
       (83, '¿Qué significa “CPU”?', 4, 1, 1, 'activa', '2025-07-11 20:14:40'),
       (84, '¿Qué tipo de malware se disfraza como software legítimo?', 4, 0, 0, 'activa', '2025-07-11 20:14:40'),
       (85, '¿Cuál es la función principal de un servidor web?', 4, 1, 0, 'activa', '2025-07-11 20:14:40'),
       (86, '¿Qué es la “nube” en tecnología?', 4, 0, 0, 'activa', '2025-07-11 20:14:40'),
       (87, '¿Qué es un “cookie” en el contexto web?', 4, 0, 0, 'activa', '2025-07-11 20:14:40'),
       (88, '¿Cuál fue el primer motor de búsqueda en internet?', 4, 1, 0, 'activa', '2025-07-11 20:14:40'),
       (89, '¿Qué significa “HTML”?', 4, 0, 0, 'activa', '2025-07-11 20:14:40'),
       (90, '¿Qué es una dirección IP?', 4, 0, 0, 'activa', '2025-07-11 20:14:40'),
       (91, '¿Qué significa “AI” en tecnología?', 4, 1, 1, 'activa', '2025-07-11 20:14:40'),
       (92, '¿Qué es un “blockchain”?', 4, 1, 1, 'activa', '2025-07-11 20:14:40'),
       (93, '¿Quién fundó Apple?', 4, 0, 0, 'activa', '2025-07-11 20:14:40'),
       (94, '¿Qué es la realidad aumentada?', 4, 1, 1, 'activa', '2025-07-11 20:14:40'),
       (95, '¿Qué significa “USB”?', 4, 1, 1, 'activa', '2025-07-11 20:14:40'),
       (96, '¿Qué es un “bug” en programación?', 4, 0, 0, 'activa', '2025-07-11 20:14:40'),
       (97, '¿Qué protocolo se usa para enviar correos electrónicos?', 4, 0, 0, 'activa', '2025-07-11 20:14:40'),
       (98, '¿Qué es un “framework”?', 4, 0, 0, 'activa', '2025-07-11 20:14:40'),
       (99, '¿Qué lenguaje se usa para el desarrollo web del lado del cliente?', 4, 0, 0, 'activa',
        '2025-07-11 20:14:40'),
       (100, '¿Qué es el “Big Data”?', 4, 1, 1, 'activa', '2025-07-11 20:14:40'),
       (101, '¿Cuál es el proceso por el que las plantas producen su alimento?', 5, 1, 1, 'activa',
        '2025-07-11 20:22:09'),
       (102, '¿Qué gas es fundamental para la fotosíntesis?', 5, 1, 1, 'activa', '2025-07-11 20:22:09'),
       (103, '¿Cuál es el animal terrestre más rápido?', 5, 0, 0, 'activa', '2025-07-11 20:22:09'),
       (104, '¿Qué tipo de animal es el delfín?', 5, 0, 0, 'activa', '2025-07-11 20:22:09'),
       (105, '¿Cómo se llama el hábitat natural de un oso polar?', 5, 1, 1, 'activa', '2025-07-11 20:22:09'),
       (106, '¿Qué planta se usa para hacer tequila?', 5, 1, 1, 'activa', '2025-07-11 20:22:09'),
       (107, '¿Qué ecosistema tiene más biodiversidad?', 5, 0, 0, 'activa', '2025-07-11 20:22:09'),
       (108, '¿Cuál es el ave nacional de Argentina?', 5, 1, 1, 'activa', '2025-07-11 20:22:09'),
       (109, '¿Qué es un herbívoro?', 5, 0, 0, 'activa', '2025-07-11 20:22:09'),
       (110, '¿Cuál es la función principal de las raíces en las plantas?', 5, 1, 1, 'activa', '2025-07-11 20:22:09'),
       (111, '¿Qué fenómeno natural causa un tsunami?', 5, 1, 0, 'activa', '2025-07-11 20:22:09'),
       (112, '¿Qué animal es conocido por cambiar de color para camuflarse?', 5, 1, 0, 'activa', '2025-07-11 20:22:09'),
       (113, '¿Qué es la capa de ozono?', 5, 1, 1, 'activa', '2025-07-11 20:22:09'),
       (114, '¿Cuál es el mamífero más grande del planeta?', 5, 0, 0, 'activa', '2025-07-11 20:22:09'),
       (115, '¿Qué tipo de energía produce una planta hidroeléctrica?', 5, 1, 1, 'activa', '2025-07-11 20:22:09'),
       (116, '¿Qué es un bioma?', 5, 1, 1, 'activa', '2025-07-11 20:22:09'),
       (117, '¿Qué animal es símbolo de la conservación en peligro de extinción?', 5, 0, 0, 'activa',
        '2025-07-11 20:22:09'),
       (118, '¿Qué es la desertificación?', 5, 1, 1, 'activa', '2025-07-11 20:22:09'),
       (119, '¿Qué parte de la planta realiza la fotosíntesis?', 5, 0, 0, 'activa', '2025-07-11 20:22:09'),
       (120, '¿Qué es un fósil?', 5, 0, 0, 'activa', '2025-07-11 20:22:09'),
       (121, '¿Cuál es el río más largo del mundo?', 5, 0, 0, 'activa', '2025-07-11 20:22:09'),
       (122, '¿Qué es la biodiversidad?', 5, 0, 0, 'activa', '2025-07-11 20:22:09'),
       (123, '¿Qué animal tiene el cuello más largo?', 5, 1, 1, 'activa', '2025-07-11 20:22:09'),
       (124, '¿Qué tipo de planta produce conos?', 5, 0, 0, 'activa', '2025-07-11 20:22:09'),
       (125, '¿Qué ecosistema se encuentra principalmente en zonas polares?', 5, 0, 0, 'activa', '2025-07-11 20:22:09'),
       (126, '¿Cuál es el continente más grande del mundo?', 6, 0, 0, 'activa', '2025-07-11 20:27:56'),
       (127, '¿Cuál es la capital de Francia?', 6, 1, 1, 'activa', '2025-07-11 20:27:56'),
       (128, '¿En qué continente se encuentra el monte Kilimanjaro?', 6, 0, 0, 'activa', '2025-07-11 20:27:56'),
       (129, '¿En qué país se encuentra la cordillera de los Andes?', 6, 1, 1, 'activa', '2025-07-11 20:27:56'),
       (130, '¿Cuál es el desierto más grande del mundo?', 6, 1, 0, 'activa', '2025-07-11 20:27:56'),
       (131, '¿Qué océano es el más profundo?', 6, 0, 0, 'activa', '2025-07-11 20:27:56'),
       (132, '¿Qué país tiene la mayor población del mundo?', 6, 1, 1, 'activa', '2025-07-11 20:27:56'),
       (133, '¿Cuál es la capital de Australia?', 6, 0, 0, 'activa', '2025-07-11 20:27:56'),
       (134, '¿En qué país está la Torre Eiffel?', 6, 1, 1, 'activa', '2025-07-11 20:27:56'),
       (135, '¿Qué país tiene forma de bota?', 6, 1, 1, 'activa', '2025-07-11 20:27:56'),
       (136, '¿Cuál es la isla más grande del mundo?', 6, 0, 0, 'activa', '2025-07-11 20:27:56'),
       (137, '¿Qué ciudad es conocida como \"La Gran Manzana\"?', 6, 2, 2, 'activa', '2025-07-11 20:27:56'),
       (138, '¿Cuál es la capital de Japón?', 6, 0, 0, 'activa', '2025-07-11 20:27:56'),
       (139, '¿Qué país está dividido en estados y tiene Washington D.C. como capital?', 6, 0, 0, 'activa',
        '2025-07-11 20:27:56'),
       (140, '¿Qué cordillera divide Europa de Asia?', 6, 1, 1, 'activa', '2025-07-11 20:27:56'),
       (141, '¿Cuál es el lago más grande de agua dulce?', 6, 2, 0, 'activa', '2025-07-11 20:27:56'),
       (142, '¿En qué país se encuentra el Machu Picchu?', 6, 0, 0, 'activa', '2025-07-11 20:27:56'),
       (143, '¿Cuál es la capital de Canadá?', 6, 0, 0, 'activa', '2025-07-11 20:27:56'),
       (144, '¿Qué país tiene el idioma oficial más hablado en el mundo?', 6, 2, 1, 'activa', '2025-07-11 20:27:56'),
       (145, '¿Qué continente es conocido por su selva amazónica?', 6, 1, 0, 'activa', '2025-07-11 20:27:56'),
       (146, '¿Cuál es el país más pequeño del mundo?', 6, 0, 0, 'activa', '2025-07-11 20:27:56'),
       (147, '¿Qué océano baña las costas de Argentina?', 6, 1, 1, 'activa', '2025-07-11 20:27:56'),
       (148, '¿Cuál es la capital de Egipto?', 6, 0, 0, 'activa', '2025-07-11 20:27:56'),
       (149, '¿Qué desierto se encuentra en África y es el más grande del continente?', 6, 0, 0, 'activa',
        '2025-07-11 20:27:56'),
       (150, '¿En qué país está la ciudad de Estambul?', 6, 0, 0, 'activa', '2025-07-11 20:27:56'),
       (151, '¿Quién compuso la Novena Sinfonía?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (152, '¿Qué instrumento tiene teclas y cuerdas?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (153, '¿Qué género musical es originario de Jamaica?', 7, 1, 1, 'activa', '2025-07-11 20:40:31'),
       (154, '¿Quién es conocido como “El Rey del Pop”?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (155, '¿Qué nota musical sigue a “Do”?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (156, '¿Qué es un “compás” en música?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (157, '¿Cuál es el instrumento principal en la música clásica india?', 7, 1, 1, 'activa', '2025-07-11 20:40:31'),
       (158, '¿Qué género musical usa mucho el violín?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (159, '¿Qué cantante es famosa por “Like a Virgin”?', 7, 1, 1, 'activa', '2025-07-11 20:40:31'),
       (160, '¿Qué tipo de música es el flamenco?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (161, '¿Qué instrumento se toca con arco?', 7, 1, 1, 'activa', '2025-07-11 20:40:31'),
       (162, '¿Qué banda lanzó el álbum “Abbey Road”?', 7, 1, 1, 'activa', '2025-07-11 20:40:31'),
       (163, '¿Qué es un “octavo” en música?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (164, '¿Qué compositor escribió “Las cuatro estaciones”?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (165, '¿Qué tipo de voz tiene un tenor?', 7, 1, 1, 'activa', '2025-07-11 20:40:31'),
       (166, '¿Qué país es conocido por la música reggae?', 7, 1, 0, 'activa', '2025-07-11 20:40:31'),
       (167, '¿Qué género musical es asociado con Elvis Presley?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (168, '¿Qué instrumento tiene teclas blancas y negras?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (169, '¿Qué músico es famoso por su guitarra eléctrica llamada “Blackie”?', 7, 0, 0, 'activa',
        '2025-07-11 20:40:31'),
       (170, '¿Qué es un “dueto”?', 7, 1, 1, 'activa', '2025-07-11 20:40:31'),
       (171, '¿Qué instrumento es típico en la música celta?', 7, 1, 0, 'deshabilitada', '2025-07-11 20:40:31'),
       (172, '¿Quién compuso la ópera “La Traviata”?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (173, '¿Qué ritmo es típico en la música salsa?', 7, 1, 1, 'activa', '2025-07-11 20:40:31'),
       (174, '¿Qué es una “melodía”?', 7, 1, 1, 'activa', '2025-07-11 20:40:31'),
       (175, '¿Qué género musical usa mucho el saxofón?', 7, 0, 0, 'activa', '2025-07-11 20:40:31'),
       (176, '¿Quién dirigió la película \"Titanic\"?', 8, 1, 0, 'activa', '2025-07-11 20:46:15'),
       (177, '¿Qué serie trata sobre un grupo de científicos que resuelven crímenes?', 8, 0, 0, 'activa',
        '2025-07-11 20:46:15'),
       (178, '¿Cuál es el nombre del mago protagonista en \"Harry Potter\"?', 8, 1, 1, 'activa', '2025-07-11 20:46:15'),
       (179, '¿Qué actor interpreta a Iron Man en el Universo Marvel??', 8, 0, 0, 'activa', '2025-07-11 20:46:15'),
       (180, '¿En qué ciudad se desarrolla la serie \"Friends\"?', 8, 0, 0, 'activa', '2025-07-11 20:46:15'),
       (181, '¿Cuál es el nombre del protagonista de \"El Señor de los Anillos\"?', 8, 0, 0, 'activa',
        '2025-07-11 20:46:15'),
       (182, '¿Qué película ganó el Óscar a Mejor Película en 1994?', 8, 1, 1, 'activa', '2025-07-11 20:46:15'),
       (183, '¿Qué cantante protagonizó la película \"A Star is Born\" en 2018?', 8, 0, 0, 'activa',
        '2025-07-11 20:46:15'),
       (184, '¿Cuál es la plataforma de streaming más popular en 2025?', 8, 0, 0, 'activa', '2025-07-11 20:46:15'),
       (185, '¿Quién es el creador de la serie \"Game of Thrones\"?', 8, 1, 0, 'activa', '2025-07-11 20:46:15'),
       (186, '¿Qué película animada presenta a una princesa llamada Elsa?', 8, 0, 0, 'activa', '2025-07-11 20:46:15'),
       (187, '¿Qué superhéroe usa un escudo con la bandera de Estados Unidos?', 8, 1, 1, 'activa',
        '2025-07-11 20:46:15'),
       (188, '¿Cuál es el nombre del robot en \"Wall-E\"?', 8, 1, 1, 'activa', '2025-07-11 20:46:15'),
       (189, '¿En qué año se estrenó la primera película de \"Star Wars\"?', 8, 0, 0, 'activa', '2025-07-11 20:46:15'),
       (190, '¿Qué actor es conocido como \"El Hombre de Acero\"?', 8, 1, 1, 'activa', '2025-07-11 20:46:15'),
       (191, '¿Cuál es la serie más vista en Netflix?', 8, 0, 0, 'activa', '2025-07-11 20:46:15'),
       (192, '¿Quién es el villano principal en \"Avengers: Infinity War\"?', 8, 1, 1, 'activa', '2025-07-11 20:46:15'),
       (193, '¿Qué serie tiene personajes llamados Eleven y Mike?', 8, 0, 0, 'activa', '2025-07-11 20:46:15'),
       (194, '¿Qué actriz interpreta a Katniss Everdeen en \"Los Juegos del Hambre\"?', 8, 1, 1, 'activa',
        '2025-07-11 20:46:15'),
       (195, '¿Cuál es la saga de películas que incluye a Jack Sparrow?', 8, 1, 1, 'activa', '2025-07-11 20:46:15'),
       (196, '¿Qué película animada tiene personajes llamados Woody y Buzz?', 8, 1, 0, 'activa', '2025-07-11 20:46:15'),
       (197, '¿Quién compuso la banda sonora de \"Piratas del Caribe\"?', 8, 1, 0, 'activa', '2025-07-11 20:46:15'),
       (198, '¿Qué actor protagoniza la serie \"Breaking Bad\"?', 8, 2, 2, 'activa', '2025-07-11 20:46:15'),
       (199, '¿Cuál es el nombre del famoso mago en la serie \"Merlín\"?', 8, 1, 1, 'activa', '2025-07-11 20:46:15'),
       (200, '¿Qué película animada es famosa por la canción \"Let It Go\"?', 8, 0, 0, 'activa', '2025-07-11 20:46:15'),
       (204, '¿En qué serie un maestro de ajedrez entrena a una joven prodigio en los años 60?', 8, 1, 1, 'activa',
        '2025-07-12 11:26:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas_reportadas`
--

CREATE TABLE `preguntas_reportadas`
(
    `id_reporte`    int(11)                                    NOT NULL,
    `id_pregunta`   int(11)                                    NOT NULL,
    `id_reportador` int(11)                                    NOT NULL,
    `fecha_reporte` datetime                                            DEFAULT current_timestamp(),
    `motivo`        varchar(255)                               NOT NULL,
    `estado`        enum ('pendiente','aprobado','descartado') NOT NULL DEFAULT 'pendiente'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas`
--

CREATE TABLE `respuestas`
(
    `id_respuesta` int(11)      NOT NULL,
    `respuesta`    varchar(300) NOT NULL,
    `esCorrecta`   tinyint(1)   NOT NULL,
    `id_pregunta`  int(11)      NOT NULL,
    `activa`       tinyint(1)   NOT NULL DEFAULT 1
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuestas`
--

INSERT INTO `respuestas` (`id_respuesta`, `respuesta`, `esCorrecta`, `id_pregunta`, `activa`)
VALUES (1, 'Palta', 1, 1, 1),
       (2, 'Papa', 0, 1, 1),
       (3, 'Zanahoria', 0, 1, 1),
       (4, 'Tomate', 0, 1, 1),
       (5, 'Italia', 0, 2, 1),
       (6, 'Francia', 0, 2, 1),
       (7, 'México', 0, 2, 1),
       (8, 'España', 1, 2, 1),
       (9, 'Cheddar', 0, 3, 1),
       (10, 'Mozzarella', 1, 3, 1),
       (11, 'Roquefort', 0, 3, 1),
       (12, 'Parmesano', 0, 3, 1),
       (13, 'Spaghetti', 1, 4, 1),
       (14, 'Fusilli', 0, 4, 1),
       (15, 'Ravioli', 0, 4, 1),
       (16, 'Penne', 0, 4, 1),
       (17, 'Vacuna', 1, 5, 1),
       (18, 'Pollo', 0, 5, 1),
       (19, 'Cordero', 0, 5, 1),
       (20, 'Cerdo', 0, 5, 1),
       (21, 'Vodka', 0, 6, 1),
       (22, 'Tequila', 0, 6, 1),
       (23, 'Ron', 1, 6, 1),
       (24, 'Whisky', 0, 6, 1),
       (25, 'Tilapia', 0, 7, 1),
       (26, 'Pez espada', 0, 7, 1),
       (27, 'Trucha', 0, 7, 1),
       (28, 'Salmón', 1, 7, 1),
       (29, 'China', 0, 8, 1),
       (30, 'Corea', 0, 8, 1),
       (31, 'Japón', 1, 8, 1),
       (32, 'Vietnam', 0, 8, 1),
       (33, 'Manzana', 0, 9, 1),
       (34, 'Uva', 1, 9, 1),
       (35, 'Ciruela', 0, 9, 1),
       (36, 'Higo', 0, 9, 1),
       (37, 'Arroz glutinoso', 1, 10, 1),
       (38, 'Arroz basmati', 0, 10, 1),
       (39, 'Arroz jazmín', 0, 10, 1),
       (40, 'Arroz integral', 0, 10, 1),
       (41, 'Pimienta', 0, 11, 1),
       (42, 'Canela', 0, 11, 1),
       (43, 'Nuez moscada', 0, 11, 1),
       (44, 'Cúrcuma', 1, 11, 1),
       (45, 'Queso vegetal', 0, 12, 1),
       (46, 'Leche condensada', 0, 12, 1),
       (47, 'Yogur', 0, 12, 1),
       (48, 'Derivado de la soja', 1, 12, 1),
       (49, 'Eclair', 1, 13, 1),
       (50, 'Madeleine', 0, 13, 1),
       (51, 'Macaron', 0, 13, 1),
       (52, 'Croissant', 0, 13, 1),
       (53, 'Tomate', 1, 14, 1),
       (54, 'Pepino', 0, 14, 1),
       (55, 'Lechuga', 0, 14, 1),
       (56, 'Apio', 0, 14, 1),
       (57, 'Pino', 0, 15, 1),
       (58, 'Almendra', 0, 15, 1),
       (59, 'Nuez', 0, 15, 1),
       (60, 'Albahaca', 1, 15, 1),
       (61, 'Brasil', 1, 16, 1),
       (62, 'Colombia', 0, 16, 1),
       (63, 'Vietnam', 0, 16, 1),
       (64, 'Etiopía', 0, 16, 1),
       (65, 'Focaccia', 1, 17, 1),
       (66, 'Baguette', 0, 17, 1),
       (67, 'Ciabatta', 0, 17, 1),
       (68, 'Panettone', 0, 17, 1),
       (69, 'Taco', 1, 18, 1),
       (70, 'Empanada', 0, 18, 1),
       (71, 'Hamburguesa', 0, 18, 1),
       (72, 'Pizza', 0, 18, 1),
       (73, 'Al vapor', 1, 19, 1),
       (74, 'Hervido', 0, 19, 1),
       (75, 'Frito', 0, 19, 1),
       (76, 'Salteado', 0, 19, 1),
       (77, 'Mango', 0, 20, 1),
       (78, 'Maracuyá', 0, 20, 1),
       (79, 'Banana', 0, 20, 1),
       (80, 'Ananá', 1, 20, 1),
       (81, 'Trigo', 0, 21, 1),
       (82, 'Cebada', 1, 21, 1),
       (83, 'Avena', 0, 21, 1),
       (84, 'Centeno', 0, 21, 1),
       (85, 'Lentejas', 0, 22, 1),
       (86, 'Garbanzos', 1, 22, 1),
       (87, 'Soja', 0, 22, 1),
       (88, 'Porotos', 0, 22, 1),
       (89, 'Guayaba', 1, 23, 1),
       (90, 'Banana', 0, 23, 1),
       (91, 'Kiwi', 0, 23, 1),
       (92, 'Frutilla', 0, 23, 1),
       (93, 'Jalea real', 0, 24, 0),
       (94, 'Polen', 0, 24, 0),
       (95, 'Miel', 1, 24, 0),
       (96, 'Cera', 0, 24, 0),
       (97, 'Café', 0, 25, 1),
       (98, 'Avellanas', 0, 25, 1),
       (99, 'Cacao', 1, 25, 1),
       (100, 'Azúcar', 0, 25, 1),
       (101, '1914', 1, 26, 1),
       (102, '1939', 0, 26, 1),
       (103, '1929', 0, 26, 1),
       (104, '1901', 0, 26, 1),
       (105, 'Abraham Lincoln', 0, 27, 1),
       (106, 'Thomas Jefferson', 0, 27, 1),
       (107, 'George Washington', 1, 27, 1),
       (108, 'John Adams', 0, 27, 1),
       (109, 'Romana', 0, 28, 1),
       (110, 'Maya', 0, 28, 1),
       (111, 'Egipcia', 1, 28, 1),
       (112, 'China', 0, 28, 1),
       (113, 'Muro de Londres', 0, 29, 1),
       (114, 'Muro de Varsovia', 0, 29, 1),
       (115, 'Muro de Viena', 0, 29, 1),
       (116, 'Muro de Berlín', 1, 29, 1),
       (117, 'Francisco Pizarro', 0, 30, 1),
       (118, 'Hernán Cortés', 1, 30, 1),
       (119, 'Cristóbal Colón', 0, 30, 1),
       (120, 'Fernando de Magallanes', 0, 30, 1),
       (121, 'Francia', 1, 31, 1),
       (122, 'España', 0, 31, 1),
       (123, 'Italia', 0, 31, 1),
       (124, 'Portugal', 0, 31, 1),
       (125, 'Estados Unidos', 0, 32, 1),
       (126, 'Francia', 0, 32, 1),
       (127, 'Brasil', 0, 32, 1),
       (128, 'Haití', 1, 32, 1),
       (129, 'Guerra de Independencia', 0, 33, 1),
       (130, 'Guerra Civil', 1, 33, 1),
       (131, 'Guerra de Vietnam', 0, 33, 1),
       (132, 'Guerra Fría', 0, 33, 1),
       (133, 'Imperio Otomano', 0, 34, 1),
       (134, 'Imperio Griego', 0, 34, 1),
       (135, 'Imperio Romano', 1, 34, 1),
       (136, 'Imperio Persa', 0, 34, 1),
       (137, 'Fin de la Segunda Guerra Mundial', 0, 35, 1),
       (138, 'Caída del muro de Berlín', 0, 35, 1),
       (139, 'El hombre llegó a la Luna', 1, 35, 1),
       (140, 'Inicio de la guerra de Vietnam', 0, 35, 1),
       (141, 'Américo Vespucio', 0, 36, 1),
       (142, 'Magallanes', 0, 36, 1),
       (143, 'Marco Polo', 0, 36, 1),
       (144, 'Cristóbal Colón', 1, 36, 1),
       (145, 'China', 0, 37, 1),
       (146, 'Alemania', 0, 37, 1),
       (147, 'Japón', 1, 37, 1),
       (148, 'Corea', 0, 37, 1),
       (149, 'Joseph Stalin', 0, 38, 1),
       (150, 'Benito Mussolini', 0, 38, 1),
       (151, 'Winston Churchill', 0, 38, 1),
       (152, 'Adolf Hitler', 1, 38, 1),
       (153, 'Cleopatra', 1, 39, 1),
       (154, 'Isabel I', 0, 39, 1),
       (155, 'Juana de Arco', 0, 39, 1),
       (156, 'María Antonieta', 0, 39, 1),
       (157, 'Revolución Industrial', 0, 40, 1),
       (158, 'Revolución Francesa', 1, 40, 1),
       (159, 'Revolución Rusa', 0, 40, 1),
       (160, 'Revolución Mexicana', 0, 40, 1),
       (161, 'Roma', 0, 41, 1),
       (162, 'Atenas', 1, 41, 1),
       (163, 'Esparta', 0, 41, 1),
       (164, 'Cartago', 0, 41, 1),
       (165, 'La guerra civil española', 0, 42, 1),
       (166, 'La invasión alemana a Polonia', 1, 42, 1),
       (167, 'El bombardeo de Pearl Harbor', 0, 42, 1),
       (168, 'El tratado de Versalles', 0, 42, 1),
       (169, 'Caída del Imperio Romano', 1, 43, 1),
       (170, 'Descubrimiento de América', 0, 43, 1),
       (171, 'Revolución Francesa', 0, 43, 1),
       (172, 'Fin de la Edad Media', 0, 43, 1),
       (173, 'Francia', 0, 44, 1),
       (174, 'Alemania', 0, 44, 1),
       (175, 'Reino Unido', 1, 44, 1),
       (176, 'Estados Unidos', 0, 44, 1),
       (177, 'Belgrano', 0, 45, 1),
       (178, 'Bolívar', 0, 45, 1),
       (179, 'Rosas', 0, 45, 1),
       (180, 'San Martín', 1, 45, 1),
       (181, 'Tratado de París', 0, 46, 1),
       (182, 'Tratado de Tordesillas', 0, 46, 1),
       (183, 'Tratado de Versalles', 1, 46, 1),
       (184, 'Tratado de Ginebra', 0, 46, 1),
       (185, 'Mayflower', 1, 47, 1),
       (186, 'Santa María', 0, 47, 1),
       (187, 'Victoria', 0, 47, 1),
       (188, 'Endeavour', 0, 47, 1),
       (189, 'Siglo XVII', 0, 48, 1),
       (190, 'Siglo XVIII', 1, 48, 1),
       (191, 'Siglo XIX', 0, 48, 1),
       (192, 'Siglo XVI', 0, 48, 1),
       (193, 'Primera Guerra Mundial', 0, 49, 1),
       (194, 'Guerra del Golfo', 0, 49, 1),
       (195, 'Guerra Civil Española', 0, 49, 1),
       (196, 'Guerra Fría', 1, 49, 1),
       (197, 'José de San Martín', 0, 50, 1),
       (198, 'Francisco de Miranda', 0, 50, 1),
       (199, 'Simón Bolívar', 1, 50, 1),
       (200, 'Antonio José de Sucre', 0, 50, 1),
       (201, '11', 1, 51, 1),
       (202, '10', 0, 51, 1),
       (203, '9', 0, 51, 1),
       (204, '12', 0, 51, 1),
       (205, 'Cricket', 0, 52, 1),
       (206, 'Golf', 0, 52, 1),
       (207, 'Tenis', 0, 52, 1),
       (208, 'Béisbol', 1, 52, 1),
       (209, 'Usain Bolt', 0, 53, 1),
       (210, 'Michael Phelps', 1, 53, 1),
       (211, 'Carl Lewis', 0, 53, 1),
       (212, 'Mark Spitz', 0, 53, 1),
       (213, 'París', 0, 54, 1),
       (214, 'Roma', 0, 54, 1),
       (215, 'Atenas', 1, 54, 1),
       (216, 'Londres', 0, 54, 1),
       (217, '21 km', 0, 55, 1),
       (218, '50 km', 0, 55, 1),
       (219, '10 km', 0, 55, 1),
       (220, '42.195 km', 1, 55, 1),
       (221, 'Francia', 1, 56, 1),
       (222, 'Brasil', 0, 56, 1),
       (223, 'Alemania', 0, 56, 1),
       (224, 'Argentina', 0, 56, 1),
       (225, 'Voleibol', 0, 57, 1),
       (226, 'Baloncesto', 1, 57, 1),
       (227, 'Handball', 0, 57, 1),
       (228, 'Fútbol', 0, 57, 1),
       (229, 'Golf', 0, 58, 1),
       (230, 'Bádminton', 0, 58, 1),
       (231, 'Tenis', 1, 58, 1),
       (232, 'Squash', 0, 58, 1),
       (233, 'Pelé', 1, 59, 1),
       (234, 'Maradona', 0, 59, 1),
       (235, 'Messi', 0, 59, 1),
       (236, 'Cristiano Ronaldo', 0, 59, 1),
       (237, 'Hockey sobre césped', 0, 60, 1),
       (238, 'Curling', 0, 60, 1),
       (239, 'Patinaje artístico', 0, 60, 1),
       (240, 'Hockey sobre hielo', 1, 60, 1),
       (241, '3 puntos', 0, 61, 1),
       (242, '7 puntos', 0, 61, 1),
       (243, '5 puntos', 1, 61, 1),
       (244, '5 puntos', 0, 61, 1),
       (245, 'Australia', 0, 62, 1),
       (246, 'India', 1, 62, 1),
       (247, 'Inglaterra', 0, 62, 1),
       (248, 'Pakistán', 0, 62, 1),
       (249, 'Patinaje sobre ruedas', 0, 63, 1),
       (250, 'Esquí', 0, 63, 1),
       (251, 'Snowboard', 0, 63, 1),
       (252, 'Patinaje sobre hielo', 1, 63, 1),
       (253, 'Carl Lewis', 0, 64, 1),
       (254, 'Usain Bolt', 1, 64, 1),
       (255, 'Michael Johnson', 0, 64, 1),
       (256, 'Asafa Powell', 0, 64, 1),
       (257, '2 horas', 0, 65, 1),
       (258, '3 horas', 0, 65, 1),
       (259, 'Alrededor de 5 horas', 1, 65, 1),
       (260, '1 hora', 0, 65, 1),
       (261, 'Biathlon', 1, 66, 1),
       (262, 'Pentatlón', 0, 66, 1),
       (263, 'Esquí nórdico', 0, 66, 1),
       (264, 'Tiro deportivo', 0, 66, 1),
       (265, 'Corea del Sur', 1, 67, 1),
       (266, 'Japón', 0, 67, 1),
       (267, 'China', 0, 67, 1),
       (268, 'Tailandia', 0, 67, 1),
       (269, 'Miami Heat', 0, 68, 1),
       (270, 'Golden State Warriors', 0, 68, 1),
       (271, 'Boston Celtics', 0, 68, 1),
       (272, 'Los Angeles Lakers', 1, 68, 1),
       (273, 'Fútbol', 0, 69, 1),
       (274, 'Fútbol americano', 1, 69, 1),
       (275, 'Baloncesto', 0, 69, 1),
       (276, 'Béisbol', 0, 69, 1),
       (277, 'Open Británico', 0, 70, 1),
       (278, 'US Open', 0, 70, 1),
       (279, 'Masters de Augusta', 1, 70, 1),
       (280, 'Copa Ryder', 0, 70, 1),
       (281, 'Inglaterra', 0, 71, 1),
       (282, 'Argentina', 1, 71, 1),
       (283, 'Estados Unidos', 0, 71, 1),
       (284, 'Australia', 0, 71, 1),
       (285, 'Cricket', 0, 72, 1),
       (286, 'Golf', 0, 72, 1),
       (287, 'Rugby', 0, 72, 1),
       (288, 'Polo', 1, 72, 1),
       (289, 'Nueva York', 1, 73, 1),
       (290, 'Boston', 0, 73, 1),
       (291, 'Chicago', 0, 73, 1),
       (292, 'Los Ángeles', 0, 73, 1),
       (293, 'Tiro con arco', 0, 74, 1),
       (294, 'Pentatlón', 0, 74, 1),
       (295, 'Lucha libre', 0, 74, 1),
       (296, 'Esgrima', 1, 74, 1),
       (297, 'Fanny Blankers-Koen', 0, 75, 1),
       (298, 'Wilma Rudolph', 0, 75, 1),
       (299, 'Betty Robinson', 1, 75, 1),
       (300, 'Florence Griffith-Joyner', 0, 75, 1),
       (301, 'Alan Turing', 1, 76, 1),
       (302, 'Bill Gates', 0, 76, 1),
       (303, 'Steve Jobs', 0, 76, 1),
       (304, 'Tim Berners-Lee', 0, 76, 1),
       (305, 'HyperText Transmission Protocol', 0, 77, 1),
       (306, 'HyperText Transfer Protocol', 1, 77, 1),
       (307, 'HighText Transfer Protocol', 0, 77, 1),
       (308, 'HyperTool Transfer Protocol', 0, 77, 1),
       (309, 'Windows 95', 0, 78, 1),
       (310, 'Windows XP', 0, 78, 1),
       (311, 'MS-DOS', 1, 78, 1),
       (312, 'OS/2', 0, 78, 1),
       (313, 'C++', 0, 79, 1),
       (314, 'Java', 0, 79, 1),
       (315, 'Ruby', 0, 79, 1),
       (316, 'Python', 1, 79, 1),
       (317, 'Un programa antivirus', 0, 80, 1),
       (318, 'Un dispositivo de almacenamiento', 0, 80, 1),
       (319, 'Un tipo de virus', 0, 80, 1),
       (320, 'Un sistema de seguridad que controla el tráfico de red', 1, 80, 1),
       (321, 'Mouse', 0, 81, 1),
       (322, 'Monitor', 0, 81, 1),
       (323, 'Teclado', 1, 81, 1),
       (324, 'Camara', 0, 81, 1),
       (325, 'Microsoft', 0, 82, 1),
       (326, 'Google', 1, 82, 1),
       (327, 'Apple', 0, 82, 1),
       (328, 'Amazon', 0, 82, 1),
       (329, 'Unidad Central de Procesamiento', 1, 83, 1),
       (330, 'Unidad de Control de Procesos', 0, 83, 1),
       (331, 'Computadora Principal Universal', 0, 83, 1),
       (332, 'Unidad de Potencia de Computación', 0, 83, 1),
       (333, 'Spyware', 0, 84, 1),
       (334, 'Troyano', 1, 84, 1),
       (335, 'Adware', 0, 84, 1),
       (336, 'Ransomware', 0, 84, 1),
       (337, 'Enviar correos electrónicos', 0, 85, 1),
       (338, 'Almacenar datos personales', 0, 85, 1),
       (339, 'Almacenar y entregar páginas web', 1, 85, 1),
       (340, 'Proteger la red', 0, 85, 1),
       (341, 'Una nube en el cielo', 0, 86, 1),
       (342, 'Un programa de edición de fotos', 0, 86, 1),
       (343, 'Un sistema operativo', 0, 86, 1),
       (344, 'Almacenamiento remoto de datos y servicios', 1, 86, 1),
       (345, 'Virus informáticos', 0, 87, 1),
       (346, 'Un tipo de software', 0, 87, 1),
       (347, 'Un lenguaje de programación', 0, 87, 1),
       (348, 'Pequeños archivos almacenados en el navegador', 1, 87, 1),
       (349, 'Google', 0, 88, 1),
       (350, 'AltaVista', 0, 88, 1),
       (351, 'Yahoo!', 1, 88, 1),
       (352, 'Bing', 0, 88, 1),
       (353, 'Hyper Transfer Markup Language', 0, 89, 1),
       (354, 'HyperText Markup Language', 1, 89, 1),
       (355, 'HighText Markup Language', 0, 89, 1),
       (356, 'HyperText Marking Language', 0, 89, 1),
       (357, 'Identificador único de un dispositivo en red', 1, 90, 1),
       (358, 'Código de programación', 0, 90, 1),
       (359, 'Un virus informático', 0, 90, 1),
       (360, 'Un programa', 0, 90, 1),
       (361, 'Internet Avanzado', 0, 91, 1),
       (362, 'Inteligencia Artificial', 1, 91, 1),
       (363, 'Interfaz de Aplicación', 0, 91, 1),
       (364, 'Algoritmo Inteligente', 0, 91, 1),
       (365, 'Un tipo de virus', 0, 92, 1),
       (366, 'Un programa de edición', 0, 92, 1),
       (367, 'Cadena de bloques para almacenar información', 1, 92, 1),
       (368, 'Un lenguaje de programación', 0, 92, 1),
       (369, 'Steve Jobs', 0, 93, 1),
       (370, 'Bill Gates', 0, 93, 1),
       (371, 'Alan Turing', 0, 93, 1),
       (372, 'Steve Wozniak', 1, 93, 1),
       (373, 'Un tipo de virus', 0, 94, 1),
       (374, 'Un programa de edición', 0, 94, 1),
       (375, 'Superposición de elementos virtuales en el mundo real', 1, 94, 1),
       (376, 'Una red social', 0, 94, 1),
       (377, 'Universal Serial Bus', 1, 95, 1),
       (378, 'United System Bus', 0, 95, 1),
       (379, 'Universal Service Bus', 0, 95, 1),
       (380, 'United Serial Board', 0, 95, 1),
       (381, 'Actualización de software', 0, 96, 1),
       (382, 'Error en el código', 1, 96, 1),
       (383, 'Nueva función', 0, 96, 1),
       (384, 'Un virus', 0, 96, 1),
       (385, 'HTTP', 0, 97, 1),
       (386, 'FTP', 0, 97, 1),
       (387, 'SMTP', 1, 97, 1),
       (388, 'POP3', 0, 97, 1),
       (389, 'Un tipo de malware', 0, 98, 1),
       (390, 'Un lenguaje de programación', 0, 98, 1),
       (391, 'Un sistema operativo', 0, 98, 1),
       (392, 'Conjunto de herramientas para desarrollar software', 1, 98, 1),
       (393, 'JavaScript', 1, 99, 1),
       (394, 'Python', 0, 99, 1),
       (395, 'PHP', 0, 99, 1),
       (396, 'Java', 0, 99, 1),
       (397, 'Grandes volúmenes de datos para analizar', 1, 100, 1),
       (398, 'Bases de datos pequeñas', 0, 100, 1),
       (399, 'Un lenguaje de programación', 0, 100, 1),
       (400, 'Un tipo de virus', 0, 100, 1),
       (401, 'Fotosíntesis', 1, 101, 1),
       (402, 'Respiración', 0, 101, 1),
       (403, 'Fermentación', 0, 101, 1),
       (404, 'Digestión', 0, 101, 1),
       (405, 'Oxígeno', 0, 102, 1),
       (406, 'Nitrógeno', 0, 102, 1),
       (407, 'Hidrógeno', 0, 102, 1),
       (408, 'Dióxido de carbono', 1, 102, 1),
       (409, 'León', 0, 103, 1),
       (410, 'Guepardo', 1, 103, 1),
       (411, 'Tigre', 0, 103, 1),
       (412, 'Antílope', 0, 103, 1),
       (413, 'Pez', 0, 104, 1),
       (414, 'Reptil', 0, 104, 1),
       (415, 'Mamífero', 1, 104, 1),
       (416, 'Ave', 0, 104, 1),
       (417, 'Ártico', 1, 105, 1),
       (418, 'Antártida', 0, 105, 1),
       (419, 'Selva', 0, 105, 1),
       (420, 'Desierto', 0, 105, 1),
       (421, 'Cactus', 0, 106, 1),
       (422, 'Palma', 0, 106, 1),
       (423, 'Pino', 0, 106, 1),
       (424, 'Agave', 1, 106, 1),
       (425, 'Desierto', 0, 107, 1),
       (426, 'Selva tropical', 1, 107, 1),
       (427, 'Pradera', 0, 107, 1),
       (428, 'Bosque templado', 0, 107, 1),
       (429, 'Cóndor', 0, 108, 1),
       (430, 'Águila', 0, 108, 1),
       (431, 'Hornero', 1, 108, 1),
       (432, 'Flamenco', 0, 108, 1),
       (433, 'Animal que come carne', 0, 109, 1),
       (434, 'Animal omnívoro', 0, 109, 1),
       (435, 'Animal acuático', 0, 109, 1),
       (436, 'Animal que come plantas', 1, 109, 1),
       (437, 'Absorber agua y nutrientes', 1, 110, 1),
       (438, 'Realizar la fotosíntesis', 0, 110, 1),
       (439, 'Producir semillas', 0, 110, 1),
       (440, 'Proteger la planta', 0, 110, 1),
       (441, 'Huracán', 0, 111, 1),
       (442, 'Tornado', 0, 111, 1),
       (443, 'Erupción volcánica', 0, 111, 1),
       (444, 'Terremoto submarino', 1, 111, 1),
       (445, 'León', 0, 112, 1),
       (446, 'Elefante', 0, 112, 1),
       (447, 'Camaleón', 1, 112, 1),
       (448, 'Tortuga', 0, 112, 1),
       (449, 'Capa de gases tóxicos', 0, 113, 1),
       (450, 'Capa de hielo', 0, 113, 1),
       (451, 'Capa que protege la Tierra de la radiación UV', 1, 113, 1),
       (452, 'Capa de nubes', 0, 113, 1),
       (453, 'Ballena azul', 1, 114, 1),
       (454, 'Elefante', 0, 114, 1),
       (455, 'Jirafa', 0, 114, 1),
       (456, 'Rinoceronte', 0, 114, 1),
       (457, 'Energía solar', 0, 115, 1),
       (458, 'Energía hidráulica', 1, 115, 1),
       (459, 'Energía eólica', 0, 115, 1),
       (460, 'Energía térmica', 0, 115, 1),
       (461, 'Un animal en peligro', 0, 116, 1),
       (462, 'Un tipo de planta', 0, 116, 1),
       (463, 'Un fenómeno climático', 0, 116, 1),
       (464, 'Conjunto de ecosistemas con características comunes', 1, 116, 1),
       (465, 'Tigre de Bengala', 1, 117, 1),
       (466, 'León africano', 0, 117, 1),
       (467, 'Elefante asiático', 0, 117, 1),
       (468, 'Oso pardo', 0, 117, 1),
       (469, 'Crecimiento de bosques', 0, 118, 1),
       (470, 'Degradación de tierras fértiles', 1, 118, 1),
       (471, 'Aumento de la biodiversidad', 0, 118, 1),
       (472, 'Formación de glaciares', 0, 118, 1),
       (473, 'Raíces', 0, 119, 1),
       (474, 'Flores', 0, 119, 1),
       (475, 'Frutos', 0, 119, 1),
       (476, 'Hojas', 1, 119, 1),
       (477, 'Un tipo de roca', 0, 120, 1),
       (478, 'Restos fosilizados de organismos antiguos', 1, 120, 1),
       (479, 'Una planta', 0, 120, 1),
       (480, 'Un animal prehistórico vivo', 0, 120, 1),
       (481, 'Amazonas', 1, 121, 1),
       (482, 'Nilo', 0, 121, 1),
       (483, 'Yangtsé', 0, 121, 1),
       (484, 'Misisipi', 0, 121, 1),
       (485, 'Cantidad de agua en un río', 0, 122, 1),
       (486, 'Altura de montañas', 0, 122, 1),
       (487, 'Tipo de suelo', 0, 122, 1),
       (488, 'Variedad de especies en un ecosistema', 1, 122, 1),
       (489, 'Elefante', 0, 123, 1),
       (490, 'Jirafa', 1, 123, 1),
       (491, 'Cebra', 0, 123, 1),
       (492, 'Rinoceronte', 0, 123, 1),
       (493, 'Coníferas', 1, 124, 1),
       (494, 'Angiospermas', 0, 124, 1),
       (495, 'Musgos', 0, 124, 1),
       (496, 'Helechos', 0, 124, 1),
       (497, 'Selva', 0, 125, 1),
       (498, 'Tundra', 1, 125, 1),
       (499, 'Desierto', 0, 125, 1),
       (500, 'Pradera', 0, 125, 1),
       (501, 'Asia', 1, 126, 1),
       (502, 'África', 0, 126, 1),
       (503, 'Europa', 0, 126, 1),
       (504, 'América', 0, 126, 1),
       (505, 'Londres', 0, 127, 1),
       (506, 'París', 1, 127, 1),
       (507, 'Roma', 0, 127, 1),
       (508, 'Berlín', 0, 127, 1),
       (509, 'Asia', 0, 128, 1),
       (510, 'América', 1, 128, 1),
       (511, 'África', 0, 128, 1),
       (512, 'Europa', 0, 128, 1),
       (513, 'Brasil', 0, 129, 1),
       (514, 'Argentina', 1, 129, 1),
       (515, 'Chile', 0, 129, 1),
       (516, 'Perú', 0, 129, 1),
       (517, 'Gobi', 0, 130, 1),
       (518, 'Kalahari', 0, 130, 1),
       (519, 'Sahara', 1, 130, 1),
       (520, 'Antártico', 0, 130, 1),
       (521, 'Atlántico', 0, 131, 1),
       (522, 'Índico', 0, 131, 1),
       (523, 'Ártico', 0, 131, 1),
       (524, 'Pacífico', 1, 131, 1),
       (525, 'China', 1, 132, 1),
       (526, 'India', 0, 132, 1),
       (527, 'Estados Unidos', 0, 132, 1),
       (528, 'Indonesia', 0, 132, 1),
       (529, 'Sídney', 0, 133, 1),
       (530, 'Canberra', 1, 133, 1),
       (531, 'Melbourne', 0, 133, 1),
       (532, 'Brisbane', 0, 133, 1),
       (533, 'Italia', 0, 134, 1),
       (534, 'España', 0, 134, 1),
       (535, 'Francia', 1, 134, 1),
       (536, 'Alemania', 0, 134, 1),
       (537, 'Grecia', 0, 135, 1),
       (538, 'España', 0, 135, 1),
       (539, 'Portugal', 0, 135, 1),
       (540, 'Italia', 1, 135, 1),
       (541, 'Groenlandia', 1, 136, 1),
       (542, 'Madagascar', 0, 136, 1),
       (543, 'Nueva Guinea', 0, 136, 1),
       (544, 'Borneo', 0, 136, 1),
       (545, 'Los Ángeles', 0, 137, 1),
       (546, 'Nueva York', 1, 137, 1),
       (547, 'Chicago', 0, 137, 1),
       (548, 'San Francisco', 0, 137, 1),
       (549, 'Seúl', 0, 138, 1),
       (550, 'Pekín', 0, 138, 1),
       (551, 'Tokio', 1, 138, 1),
       (552, 'Bangkok', 0, 138, 1),
       (553, 'Canadá', 0, 139, 1),
       (554, 'México', 0, 139, 1),
       (555, 'Brasil', 0, 139, 1),
       (556, 'Estados Unidos', 1, 139, 1),
       (557, 'Montes Urales', 1, 140, 1),
       (558, 'Alpes', 0, 140, 1),
       (559, 'Pirineos', 0, 140, 1),
       (560, 'Cáucaso', 0, 140, 1),
       (561, 'Lago Victoria', 0, 141, 1),
       (562, 'Lago Superior', 1, 141, 1),
       (563, 'Lago Tanganica', 0, 141, 1),
       (564, 'Lago Baikal', 0, 141, 1),
       (565, 'Bolivia', 0, 142, 1),
       (566, 'Chile', 0, 142, 1),
       (567, 'Perú', 1, 142, 1),
       (568, 'Ecuador', 0, 142, 1),
       (569, 'Toronto', 0, 143, 1),
       (570, 'Montreal', 0, 143, 1),
       (571, 'Vancouver', 0, 143, 1),
       (572, 'Ottawa', 1, 143, 1),
       (573, 'Chino', 1, 144, 1),
       (574, 'Inglés', 0, 144, 1),
       (575, 'Español', 0, 144, 1),
       (576, 'Hindi', 0, 144, 1),
       (577, 'América del Norte', 0, 145, 1),
       (578, 'América del Sur', 1, 145, 1),
       (579, 'África', 0, 145, 1),
       (580, 'Asia', 0, 145, 1),
       (581, 'Mónaco', 0, 146, 1),
       (582, 'Nauru', 0, 146, 1),
       (583, 'Vaticano', 1, 146, 1),
       (584, 'San Marino', 0, 146, 1),
       (585, 'Pacífico', 0, 147, 1),
       (586, 'Índico', 0, 147, 1),
       (587, 'Ártico', 0, 147, 1),
       (588, 'Atlántico', 1, 147, 1),
       (589, 'El Cairo', 1, 148, 1),
       (590, 'Alejandría', 0, 148, 1),
       (591, 'Lagos', 0, 148, 1),
       (592, 'Cartago', 0, 148, 1),
       (593, 'Kalahari', 0, 149, 1),
       (594, 'Sahara', 1, 149, 1),
       (595, 'Gobi', 0, 149, 1),
       (596, 'Namib', 0, 149, 1),
       (597, 'Grecia', 0, 150, 1),
       (598, 'Bulgaria', 0, 150, 1),
       (599, 'Turquía', 1, 150, 1),
       (600, 'Irán', 0, 150, 1),
       (601, 'Ludwig van Beethoven', 1, 151, 1),
       (602, 'Wolfgang Amadeus Mozart', 0, 151, 1),
       (603, 'Johann Sebastian Bach', 0, 151, 1),
       (604, 'Franz Schubert', 0, 151, 1),
       (605, 'Guitarra', 0, 152, 1),
       (606, 'Piano', 1, 152, 1),
       (607, 'Violín', 0, 152, 1),
       (608, 'Flauta', 0, 152, 1),
       (609, 'Ska', 0, 153, 1),
       (610, 'Jazz', 0, 153, 1),
       (611, 'Reggae', 1, 153, 1),
       (612, 'Blues', 0, 153, 1),
       (613, 'Elvis Presley', 0, 154, 1),
       (614, 'Prince', 0, 154, 1),
       (615, 'Freddie Mercury', 0, 154, 1),
       (616, 'Michael Jackson', 1, 154, 1),
       (617, 'Re', 1, 155, 1),
       (618, 'Mi', 0, 155, 1),
       (619, 'Fa', 0, 155, 1),
       (620, 'Sol', 0, 155, 1),
       (621, 'Una nota musical', 0, 156, 1),
       (622, 'La medida de tiempo en música', 1, 156, 1),
       (623, 'Un tipo de ritmo', 0, 156, 1),
       (624, 'Un instrumento', 0, 156, 1),
       (625, 'Tabla', 0, 157, 1),
       (626, 'Tanpura', 0, 157, 1),
       (627, 'Sitar', 1, 157, 1),
       (628, 'Bansuri', 0, 157, 1),
       (629, 'Jazz', 0, 158, 1),
       (630, 'Rock', 0, 158, 1),
       (631, 'Pop', 0, 158, 1),
       (632, 'Música clásica', 1, 158, 1),
       (633, 'Madonna', 1, 159, 1),
       (634, 'Lady Gaga', 0, 159, 1),
       (635, 'Beyoncé', 0, 159, 1),
       (636, 'Rihanna', 0, 159, 1),
       (637, 'Jazz', 0, 160, 1),
       (638, 'Folklore español', 1, 160, 1),
       (639, 'Rock', 0, 160, 1),
       (640, 'Pop', 0, 160, 1),
       (641, 'Guitarra', 0, 161, 1),
       (642, 'Piano', 0, 161, 1),
       (643, 'Violín', 1, 161, 1),
       (644, 'Batería', 0, 161, 1),
       (645, 'The Rolling Stones', 0, 162, 1),
       (646, 'Queen', 0, 162, 1),
       (647, 'Led Zeppelin', 0, 162, 1),
       (648, 'The Beatles', 1, 162, 1),
       (649, 'Intervalo de ocho notas', 1, 163, 1),
       (650, 'Intervalo de cinco notas', 0, 163, 1),
       (651, 'Tipo de compás', 0, 163, 1),
       (652, 'Tipo de instrumento', 0, 163, 1),
       (653, 'Johann Sebastian Bach', 0, 164, 1),
       (654, 'Antonio Vivaldi', 1, 164, 1),
       (655, 'Wolfgang Amadeus Mozart', 0, 164, 1),
       (656, 'Ludwig van Beethoven', 0, 164, 1),
       (657, 'Voz femenina baja', 0, 165, 1),
       (658, 'Voz masculina baja', 0, 165, 1),
       (659, 'Voz masculina alta', 1, 165, 1),
       (660, 'Voz femenina alta', 0, 165, 1),
       (661, 'Cuba', 0, 166, 1),
       (662, 'Estados Unidos', 0, 166, 1),
       (663, 'Brasil', 0, 166, 1),
       (664, 'Jamaica', 1, 166, 1),
       (665, 'Rock and Roll', 1, 167, 1),
       (666, 'Jazz', 0, 167, 1),
       (667, 'Blues', 0, 167, 1),
       (668, 'Pop', 0, 167, 1),
       (669, 'Guitarra', 0, 168, 1),
       (670, 'Piano', 1, 168, 1),
       (671, 'Violín', 0, 168, 1),
       (672, 'Batería', 0, 168, 1),
       (673, 'Jimi Hendrix', 0, 169, 1),
       (674, 'Jimmy Page', 0, 169, 1),
       (675, 'Eric Clapton', 1, 169, 1),
       (676, 'Carlos Santana', 0, 169, 1),
       (677, 'Un instrumento musical', 0, 170, 1),
       (678, 'Un género musical', 0, 170, 1),
       (679, 'Una canción larga', 0, 170, 1),
       (680, 'Canción interpretada por dos cantantes', 1, 170, 1),
       (681, 'Gaita', 1, 171, 0),
       (682, 'Flauta', 0, 171, 0),
       (683, 'Arpa', 0, 171, 0),
       (684, 'Violín', 0, 171, 0),
       (685, 'Richard Wagner', 0, 172, 1),
       (686, 'Giuseppe Verdi', 1, 172, 1),
       (687, 'Wolfgang Amadeus Mozart', 0, 172, 1),
       (688, 'Gioachino Rossini', 0, 172, 1),
       (689, 'Compás', 0, 173, 1),
       (690, 'Tempo', 0, 173, 1),
       (691, 'Clave', 1, 173, 1),
       (692, 'Tono', 0, 173, 1),
       (693, 'Un ritmo rápido', 0, 174, 1),
       (694, 'Una canción triste', 0, 174, 1),
       (695, 'Un instrumento musical', 0, 174, 1),
       (696, 'Sucesión de sonidos agradables', 1, 174, 1),
       (697, 'Jazz', 1, 175, 1),
       (698, 'Rock', 0, 175, 1),
       (699, 'Pop', 0, 175, 1),
       (700, 'Clásica', 0, 175, 1),
       (701, 'James Cameron', 1, 176, 1),
       (702, 'Steven Spielberg', 0, 176, 1),
       (703, 'Martin Scorsese', 0, 176, 1),
       (704, 'Christopher Nolan', 0, 176, 1),
       (705, 'Friends', 0, 177, 1),
       (706, 'CSI', 1, 177, 1),
       (707, 'The Big Bang Theory', 0, 177, 1),
       (708, 'Breaking Bad', 0, 177, 1),
       (709, 'Ron Weasley', 0, 178, 1),
       (710, 'Hermione Granger', 0, 178, 1),
       (711, 'Harry Potter', 1, 178, 1),
       (712, 'Draco Malfoy', 0, 178, 1),
       (713, 'Chris Evans', 0, 179, 1),
       (714, 'Chris Hemsworth', 0, 179, 1),
       (715, 'Mark Ruffalo', 0, 179, 1),
       (716, 'Robert Downey Jr.', 1, 179, 1),
       (717, 'Nueva York', 1, 180, 1),
       (718, 'Los Ángeles', 0, 180, 1),
       (719, 'Chicago', 0, 180, 1),
       (720, 'Miami', 0, 180, 1),
       (721, 'Gandalf', 0, 181, 1),
       (722, 'Frodo Bolsón', 1, 181, 1),
       (723, 'Aragorn', 0, 181, 1),
       (724, 'Legolas', 0, 181, 1),
       (725, 'Pulp Fiction', 0, 182, 1),
       (726, 'La lista de Schindler', 0, 182, 1),
       (727, 'Forrest Gump', 1, 182, 1),
       (728, 'Titanic', 0, 182, 1),
       (729, 'Beyoncé', 0, 183, 1),
       (730, 'Adele', 0, 183, 1),
       (731, 'Taylor Swift', 0, 183, 1),
       (732, 'Lady Gaga', 1, 183, 1),
       (733, 'Netflix', 1, 184, 1),
       (734, 'Amazon Prime', 0, 184, 1),
       (735, 'Disney+', 0, 184, 1),
       (736, 'HBO Max', 0, 184, 1),
       (737, 'J.K. Rowling', 0, 185, 1),
       (738, 'George R.R. Martin', 1, 185, 1),
       (739, 'Stephen King', 0, 185, 1),
       (740, 'J.R.R. Tolkien', 0, 185, 1),
       (741, 'Enredados', 0, 186, 1),
       (742, 'Moana', 0, 186, 1),
       (743, 'Frozen', 1, 186, 1),
       (744, 'Coco', 0, 186, 1),
       (745, 'Iron Man', 0, 187, 1),
       (746, 'Thor', 0, 187, 1),
       (747, 'Hulk', 0, 187, 1),
       (748, 'Capitán América', 1, 187, 1),
       (749, 'Wall-E', 1, 188, 1),
       (750, 'EVE', 0, 188, 1),
       (751, 'R2-D2', 0, 188, 1),
       (752, 'C-3PO', 0, 188, 1),
       (753, '1980', 0, 189, 1),
       (754, '1977', 1, 189, 1),
       (755, '1985', 0, 189, 1),
       (756, '1990', 0, 189, 1),
       (757, 'Chris Pratt', 0, 190, 1),
       (758, 'Ben Affleck', 0, 190, 1),
       (759, 'Henry Cavill', 1, 190, 1),
       (760, 'Tom Cruise', 0, 190, 1),
       (761, 'Breaking Bad', 0, 191, 1),
       (762, 'The Crown', 0, 191, 1),
       (763, 'The Witcher', 0, 191, 1),
       (764, 'Stranger Things', 1, 191, 1),
       (765, 'Thanos', 1, 192, 1),
       (766, 'Loki', 0, 192, 1),
       (767, 'Ultron', 0, 192, 1),
       (768, 'Hela', 0, 192, 1),
       (769, 'The Walking Dead', 0, 193, 1),
       (770, 'Stranger Things', 1, 193, 1),
       (771, 'Dark', 0, 193, 1),
       (772, 'Riverdale', 0, 193, 1),
       (773, 'Emma Stone', 0, 194, 1),
       (774, 'Scarlett Johansson', 0, 194, 1),
       (775, 'Jennifer Lawrence', 1, 194, 1),
       (776, 'Natalie Portman', 0, 194, 1),
       (777, 'Harry Potter', 0, 195, 1),
       (778, 'Star Wars', 0, 195, 1),
       (779, 'El Señor de los Anillos', 0, 195, 1),
       (780, 'Piratas del Caribe', 1, 195, 1),
       (781, 'Toy Story', 1, 196, 1),
       (782, 'Shrek', 0, 196, 1),
       (783, 'Cars', 0, 196, 1),
       (784, 'Buscando a Nemo', 0, 196, 1),
       (785, 'John Williams', 0, 197, 1),
       (786, 'Hans Zimmer', 1, 197, 1),
       (787, 'Ennio Morricone', 0, 197, 1),
       (788, 'Danny Elfman', 0, 197, 1),
       (789, 'Aaron Paul', 0, 198, 1),
       (790, 'Bob Odenkirk', 0, 198, 1),
       (791, 'Bryan Cranston', 1, 198, 1),
       (792, 'Jonathan Banks', 0, 198, 1),
       (793, 'Harry Potter', 0, 199, 1),
       (794, 'Gandalf', 0, 199, 1),
       (795, 'Dumbledore', 0, 199, 1),
       (796, 'Merlín', 1, 199, 1),
       (797, 'Enredados', 0, 200, 1),
       (798, 'Moana', 0, 200, 1),
       (799, 'Coco', 0, 200, 1),
       (800, 'Frozen', 1, 200, 1),
       (813, 'The Crown', 0, 204, 1),
       (814, 'Gambito de dama', 1, 204, 1),
       (815, 'The Great', 0, 204, 1),
       (816, 'La maravillosa Sra. Maisel', 0, 204, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles`
(
    `id_rol`     int(11)     NOT NULL,
    `nombre_rol` varchar(50) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`)
VALUES (3, 'admin'),
       (2, 'editor'),
       (1, 'jugador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sexo`
--

CREATE TABLE `sexo`
(
    `id_sexo`     int(11)     NOT NULL,
    `descripcion` varchar(50) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sexo`
--

INSERT INTO `sexo` (`id_sexo`, `descripcion`)
VALUES (2, 'Femenino'),
       (1, 'Masculino'),
       (3, 'Prefiero no cargarlo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sugerencias_preguntas`
--

CREATE TABLE `sugerencias_preguntas`
(
    `id_sugerencia`    int(11)  NOT NULL,
    `id_usuario`       int(11)  NOT NULL,
    `id_pregunta`      int(11)                                   DEFAULT NULL,
    `id_categoria`     int(11)  NOT NULL,
    `fecha_envio`      datetime NOT NULL                         DEFAULT current_timestamp(),
    `estado`           enum ('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
    `fecha_resolucion` datetime                                  DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios`
(
    `id_usuario`           int(11)      NOT NULL,
    `nombre_completo`      varchar(100) NOT NULL,
    `anio_nacimiento`      year(4)      NOT NULL,
    `id_sexo`              int(11)               DEFAULT NULL,
    `id_pais`              int(11)               DEFAULT NULL,
    `id_ciudad`            int(11)               DEFAULT NULL,
    `email`                varchar(100) NOT NULL,
    `contrasena_hash`      varchar(255) NOT NULL,
    `nombre_usuario`       varchar(50)  NOT NULL,
    `puntaje_acumulado`    int(11)               DEFAULT 0,
    `foto_perfil_url`      varchar(255)          DEFAULT NULL,
    `es_validado`          tinyint(1)   NOT NULL DEFAULT 0,
    `preguntas_entregadas` int(11)      NOT NULL DEFAULT 0,
    `preguntas_acertadas`  int(11)      NOT NULL DEFAULT 0,
    `token_verificacion`   varchar(255) NOT NULL,
    `latitud`              decimal(10, 6)        DEFAULT NULL,
    `longitud`             decimal(10, 6)        DEFAULT NULL,
    `id_rol`               int(11)      NOT NULL DEFAULT 1,
    `fecha_registro`       datetime     NOT NULL DEFAULT current_timestamp(),
    `cantidad_trampitas`   int(11)      NOT NULL DEFAULT 0
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_pregunta`
--

CREATE TABLE `usuario_pregunta`
(
    `idUsuario`  int(11) NOT NULL,
    `idPregunta` int(11) NOT NULL,
    `fechaVisto` datetime DEFAULT current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
    ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `ciudades`
--
ALTER TABLE `ciudades`
    ADD PRIMARY KEY (`id_ciudad`),
    ADD KEY `id_pais` (`id_pais`);

--
-- Indices de la tabla `compras_trampitas`
--
ALTER TABLE `compras_trampitas`
    ADD PRIMARY KEY (`id_compra`),
    ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
    ADD PRIMARY KEY (`id_pais`),
    ADD UNIQUE KEY `nombre_pais` (`nombre_pais`);

--
-- Indices de la tabla `partidas`
--
ALTER TABLE `partidas`
    ADD PRIMARY KEY (`id_partida`),
    ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `partida_pregunta`
--
ALTER TABLE `partida_pregunta`
    ADD PRIMARY KEY (`id_partida`, `id_pregunta`),
    ADD KEY `pregunta_ibfk_2` (`id_pregunta`),
    ADD KEY `respuesta_ibfk_3` (`id_respuesta_elegida`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
    ADD PRIMARY KEY (`id_pregunta`),
    ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `preguntas_reportadas`
--
ALTER TABLE `preguntas_reportadas`
    ADD PRIMARY KEY (`id_reporte`),
    ADD KEY `id_pregunta` (`id_pregunta`),
    ADD KEY `id_reportador` (`id_reportador`);

--
-- Indices de la tabla `respuestas`
--
ALTER TABLE `respuestas`
    ADD PRIMARY KEY (`id_respuesta`),
    ADD KEY `id_pregunta` (`id_pregunta`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
    ADD PRIMARY KEY (`id_rol`),
    ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `sexo`
--
ALTER TABLE `sexo`
    ADD PRIMARY KEY (`id_sexo`),
    ADD UNIQUE KEY `descripcion` (`descripcion`);

--
-- Indices de la tabla `sugerencias_preguntas`
--
ALTER TABLE `sugerencias_preguntas`
    ADD PRIMARY KEY (`id_sugerencia`),
    ADD KEY `id_usuario` (`id_usuario`),
    ADD KEY `id_categoria` (`id_categoria`),
    ADD KEY `fk_sugerencias_preguntas_id_pregunta` (`id_pregunta`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
    ADD PRIMARY KEY (`id_usuario`),
    ADD UNIQUE KEY `email` (`email`),
    ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
    ADD KEY `id_sexo` (`id_sexo`),
    ADD KEY `id_pais` (`id_pais`),
    ADD KEY `id_ciudad` (`id_ciudad`),
    ADD KEY `fk_usuarios_roles` (`id_rol`);

--
-- Indices de la tabla `usuario_pregunta`
--
ALTER TABLE `usuario_pregunta`
    ADD PRIMARY KEY (`idUsuario`, `idPregunta`),
    ADD KEY `pregunta_ibfk_1` (`idPregunta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
    MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 9;

--
-- AUTO_INCREMENT de la tabla `ciudades`
--
ALTER TABLE `ciudades`
    MODIFY `id_ciudad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras_trampitas`
--
ALTER TABLE `compras_trampitas`
    MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 10;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
    MODIFY `id_pais` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `partidas`
--
ALTER TABLE `partidas`
    MODIFY `id_partida` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
    MODIFY `id_pregunta` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 205;

--
-- AUTO_INCREMENT de la tabla `preguntas_reportadas`
--
ALTER TABLE `preguntas_reportadas`
    MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `respuestas`
--
ALTER TABLE `respuestas`
    MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 817;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
    MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 4;

--
-- AUTO_INCREMENT de la tabla `sexo`
--
ALTER TABLE `sexo`
    MODIFY `id_sexo` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 4;

--
-- AUTO_INCREMENT de la tabla `sugerencias_preguntas`
--
ALTER TABLE `sugerencias_preguntas`
    MODIFY `id_sugerencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
    MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ciudades`
--
ALTER TABLE `ciudades`
    ADD CONSTRAINT `ciudades_ibfk_1` FOREIGN KEY (`id_pais`) REFERENCES `paises` (`id_pais`);

--
-- Filtros para la tabla `compras_trampitas`
--
ALTER TABLE `compras_trampitas`
    ADD CONSTRAINT `compras_trampitas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `partidas`
--
ALTER TABLE `partidas`
    ADD CONSTRAINT `usuarioPregunta_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `partida_pregunta`
--
ALTER TABLE `partida_pregunta`
    ADD CONSTRAINT `partida_ibfk_1` FOREIGN KEY (`id_partida`) REFERENCES `partidas` (`id_partida`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `pregunta_ibfk_2` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `respuesta_ibfk_3` FOREIGN KEY (`id_respuesta_elegida`) REFERENCES `respuestas` (`id_respuesta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `preguntas`
--
ALTER TABLE `preguntas`
    ADD CONSTRAINT `categoria_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);

--
-- Filtros para la tabla `preguntas_reportadas`
--
ALTER TABLE `preguntas_reportadas`
    ADD CONSTRAINT `preguntas_reportadas_ibfk_1` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`) ON DELETE CASCADE,
    ADD CONSTRAINT `preguntas_reportadas_ibfk_2` FOREIGN KEY (`id_reportador`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `respuestas`
--
ALTER TABLE `respuestas`
    ADD CONSTRAINT `preguntas_ibfk_1` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sugerencias_preguntas`
--
ALTER TABLE `sugerencias_preguntas`
    ADD CONSTRAINT `fk_sugerencias_preguntas_id_pregunta` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`) ON DELETE CASCADE,
    ADD CONSTRAINT `sugerencias_preguntas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
    ADD CONSTRAINT `sugerencias_preguntas_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
    ADD CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`),
    ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_sexo`) REFERENCES `sexo` (`id_sexo`),
    ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_pais`) REFERENCES `paises` (`id_pais`),
    ADD CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id_ciudad`);

--
-- Filtros para la tabla `usuario_pregunta`
--
ALTER TABLE `usuario_pregunta`
    ADD CONSTRAINT `pregunta_ibfk_1` FOREIGN KEY (`idPregunta`) REFERENCES `preguntas` (`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
