CREATE DATABASE IF NOT EXISTS mi_preguntados;
USE mi_preguntados;

CREATE TABLE `categoria`
(
    `id_categoria`   int(11)      NOT NULL,
    `nombre`         varchar(100) NOT NULL,
    `foto_categoria` varchar(255) DEFAULT NULL,
    `color`          text         NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;


INSERT INTO `categoria` (`id_categoria`, `nombre`, `foto_categoria`, `color`)
VALUES (1, 'Gastronomía', '../public/images/personajes/rojo.png', 'darkred'),
       (2, 'Historia', '../public/images/personajes/amarillo.png', 'goldenrod'),
       (3, 'Deporte', '../public/images/personajes/naranja.png', 'chocolate'),
       (4, 'Tecnología', '../public/images/personajes/celeste.png', 'cadetblue'),
       (5, 'Naturaleza', '../public/images/personajes/verde.png', 'darkgreen'),
       (6, 'Geografía', '../public/images/personajes/azul.png', 'darkblue'),
       (7, 'Música', '../public/images/personajes/violeta.png', 'blueviolet'),
       (8, 'Entretenimiento', '../public/images/personajes/rosa.png', 'hotpink');


CREATE TABLE `ciudades`
(
    `id_ciudad`     int(11)      NOT NULL,
    `nombre_ciudad` varchar(100) NOT NULL,
    `id_pais`       int(11) DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

CREATE TABLE `paises`
(
    `id_pais`     int(11)      NOT NULL,
    `nombre_pais` varchar(100) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

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

CREATE TABLE `partida_pregunta`
(
    `id_partida`           int(11)    NOT NULL,
    `id_pregunta`          int(11)    NOT NULL,
    `id_respuesta_elegida` int(11)    NOT NULL,
    `acerto`               tinyint(1) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

CREATE TABLE `preguntas`
(
    `id_pregunta`  int(11)                                                NOT NULL,
    `pregunta`     varchar(500)                                           NOT NULL,
    `id_categoria` int(11)                                                NOT NULL,
    `entregadas`   int(11)                                                NOT NULL DEFAULT 0,
    `correctas`    int(11)                                                NOT NULL DEFAULT 0,
    `estado`       enum ('activa','sugerida','reportada','deshabilitada') NOT NULL DEFAULT 'activa'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

INSERT INTO `preguntas` (`id_pregunta`, `pregunta`, `id_categoria`, `entregadas`, `correctas`, `estado`)
VALUES (1, '¿Qué tipo de queso se usa tradicionalmente en la ensalada griega?', 1, 6, 4, 'activa'),
       (2, '¿En qué año comenzó la Segunda Guerra Mundial?', 2, 1, 1, 'activa'),
       (3, '¿Quién fue el primer emperador romano?', 2, 0, 0, 'activa'),
       (4, '¿Qué civilización construyó Machu Picchu?', 2, 1, 1, 'activa'),
       (5, '¿Qué reina gobernó Inglaterra durante casi 64 años?', 2, 1, 0, 'activa'),
       (6, '¿Cuántos jugadores hay en un equipo de fútbol?', 3, 0, 0, 'activa'),
       (7, '¿En qué deporte se usa una raqueta y una red?', 3, 0, 0, 'activa'),
       (8, '¿Qué país ganó el Mundial de Fútbol en 1998?', 3, 1, 1, 'activa'),
       (9, '¿Cuántos puntos vale un triple en básquet?', 3, 0, 0, 'activa'),
       (10, '¿Qué significa “www” en una dirección web?', 4, 1, 1, 'activa'),
       (11, '¿Quién fundó Microsoft?', 4, 1, 1, 'activa'),
       (12, '¿Qué es el protocolo “HTTPS”?', 4, 0, 0, 'activa'),
       (13, '¿En qué lenguaje está desarrollado el núcleo de Linux?', 4, 1, 0, 'activa'),
       (14, '¿Qué animal es conocido como el rey de la selva?', 5, 1, 1, 'activa'),
       (15, '¿Qué tipo de animal es la ballena azul?', 5, 1, 0, 'reportada'),
       (16, '¿Qué gas usan las plantas para hacer la fotosíntesis?', 5, 3, 2, 'activa'),
       (17, '¿Cómo se llama el fenómeno de pérdida de hojas en otoño?', 5, 1, 1, 'activa'),
       (18, '¿Cuál es el continente más grande del mundo?', 6, 0, 0, 'activa'),
       (19, '¿Qué país tiene forma de bota?', 6, 1, 1, 'activa'),
       (20, '¿Cuál es la capital de Australia?', 6, 0, 0, 'activa'),
       (21, '¿Qué río es el más largo del mundo?', 6, 1, 0, 'activa'),
       (22, '¿Quién fue el “Rey del Pop”?', 7, 2, 2, 'activa'),
       (23, '¿Qué instrumento tiene teclas blancas y negras?', 7, 1, 1, 'activa'),
       (24, '¿Qué banda compuso la canción “Bohemian Rhapsody”?', 7, 0, 0, 'activa'),
       (25, '¿Qué compositor escribió la Novena Sinfonía?', 7, 3, 2, 'activa'),
       (26, '¿Qué personaje de ficción vive en una piña debajo del mar?', 8, 1, 1, 'activa'),
       (27, '¿Cuál es el nombre del parque de diversiones de Disney en Florida?', 8, 0, 0, 'activa'),
       (28, '¿Qué superhéroe tiene un martillo llamado Mjölnir?', 8, 0, 0, 'activa'),
       (29, '¿En qué serie aparece el personaje Sheldon Cooper?', 8, 1, 0, 'activa'),
       (30, '¿Cuál es el apellido de los hermanos en “Stranger Things”?', 8, 2, 0, 'activa'),
       (31, '¿Quien pintó \"La noche estrellada\"?', 8, 0, 0, 'sugerida');

CREATE TABLE `preguntas_reportadas`
(
    `id_reporte`    int(11)                                    NOT NULL,
    `id_pregunta`   int(11)                                    NOT NULL,
    `id_reportador` int(11)                                    NOT NULL,
    `fecha_reporte` datetime                                            DEFAULT current_timestamp(),
    `motivo`        varchar(255)                               NOT NULL,
    `estado`        enum ('pendiente','resuelto','descartado') NOT NULL DEFAULT 'pendiente'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_spanish_ci;

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

INSERT INTO `respuestas` (`id_respuesta`, `respuesta`, `esCorrecta`, `id_pregunta`, `activa`)
VALUES (1, 'Mozzarella', 0, 1, 1),
       (2, 'Roquefort', 0, 1, 1),
       (3, 'Cheddar', 0, 1, 1),
       (4, 'Feta', 1, 1, 1),
       (5, '1945', 0, 2, 1),
       (6, '1939', 1, 2, 1),
       (7, '1914', 0, 2, 1),
       (8, '1929', 0, 2, 1),
       (9, 'Julio César', 0, 3, 1),
       (10, 'Trajano', 0, 3, 1),
       (11, 'Augusto', 1, 3, 1),
       (12, 'Nerón', 0, 3, 1),
       (13, 'Azteca', 0, 4, 1),
       (14, 'Maya', 0, 4, 1),
       (15, 'Inca', 1, 4, 1),
       (16, 'Olmeca', 0, 4, 1),
       (17, 'Isabel II', 0, 5, 1),
       (18, 'Reina Victoria', 1, 5, 1),
       (19, 'María I', 0, 5, 1),
       (20, 'Ana', 0, 5, 1),
       (21, '9', 0, 6, 1),
       (22, '10', 0, 6, 1),
       (23, '11', 1, 6, 1),
       (24, '12', 0, 6, 1),
       (25, 'Golf', 0, 7, 1),
       (26, 'Tenis', 1, 7, 1),
       (27, 'Rugby', 0, 7, 1),
       (28, 'Fútbol', 0, 7, 1),
       (29, 'Brasil', 0, 8, 1),
       (30, 'Alemania', 0, 8, 1),
       (31, 'Francia', 1, 8, 1),
       (32, 'Argentina', 0, 8, 1),
       (33, '2', 0, 9, 1),
       (34, '3', 1, 9, 1),
       (35, '1', 0, 9, 1),
       (36, '4', 0, 9, 1),
       (37, 'Web Wide Web', 0, 10, 1),
       (38, 'World Web Wide', 0, 10, 1),
       (39, 'World Wide Web', 1, 10, 1),
       (40, 'Web World Wide', 0, 10, 1),
       (41, 'Steve Jobs', 0, 11, 1),
       (42, 'Elon Musk', 0, 11, 1),
       (43, 'Bill Gates', 1, 11, 1),
       (44, 'Mark Zuckerberg', 0, 11, 1),
       (45, 'Una app de mensajería', 0, 12, 1),
       (46, 'Un navegador', 0, 12, 1),
       (47, 'Una versión segura de HTTP', 1, 12, 1),
       (48, 'Un formato de video', 0, 12, 1),
       (49, 'Java', 0, 13, 1),
       (50, 'Python', 0, 13, 1),
       (51, 'C++', 0, 13, 1),
       (52, 'C', 1, 13, 1),
       (53, 'Tigre', 0, 14, 1),
       (54, 'León', 1, 14, 1),
       (55, 'Gorila', 0, 14, 1),
       (56, 'Elefante', 0, 14, 1),
       (57, 'Pez', 0, 15, 1),
       (58, 'Reptil', 0, 15, 1),
       (59, 'Mamífero', 1, 15, 1),
       (60, 'Anfibio', 0, 15, 1),
       (61, 'Oxígeno', 0, 16, 1),
       (62, 'Dióxido de carbono', 1, 16, 1),
       (63, 'Nitrógeno', 0, 16, 1),
       (64, 'Hidrógeno', 0, 16, 1),
       (65, 'Foliación', 0, 17, 1),
       (66, 'Clorosis', 0, 17, 1),
       (67, 'Deciduación', 1, 17, 1),
       (68, 'Marchitación', 0, 17, 1),
       (69, 'África', 0, 18, 1),
       (70, 'América', 0, 18, 1),
       (71, 'Asia', 1, 18, 1),
       (72, 'Europa', 0, 18, 1),
       (73, 'Argentina', 0, 19, 1),
       (74, 'Italia', 1, 19, 1),
       (75, 'Francia', 0, 19, 1),
       (76, 'Chile', 0, 19, 1),
       (77, 'Sídney', 0, 20, 1),
       (78, 'Melbourne', 0, 20, 1),
       (79, 'Canberra', 1, 20, 1),
       (80, 'Brisbane', 0, 20, 1),
       (81, 'Amazonas', 1, 21, 1),
       (82, 'Nilo', 0, 21, 1),
       (83, 'Yangtsé', 0, 21, 1),
       (84, 'Misisipi', 0, 21, 1),
       (85, 'Elvis Presley', 0, 22, 1),
       (86, 'Freddie Mercury', 0, 22, 1),
       (87, 'Justin Timberlake', 0, 22, 1),
       (88, 'Michael Jackson', 1, 22, 1),
       (89, 'Guitarra', 0, 23, 1),
       (90, 'Violín', 0, 23, 1),
       (91, 'Piano', 1, 23, 1),
       (92, 'Batería', 0, 23, 1),
       (93, 'The Beatles', 0, 24, 1),
       (94, 'Queen', 1, 24, 1),
       (95, 'Nirvana', 0, 24, 1),
       (96, 'U2', 0, 24, 1),
       (97, 'Mozart', 0, 25, 1),
       (98, 'Bach', 0, 25, 1),
       (99, 'Beethoven', 1, 25, 1),
       (100, 'Chopin', 0, 25, 1),
       (101, 'Mickey Mouse', 0, 26, 1),
       (102, 'Bob Esponja', 1, 26, 1),
       (103, 'Nemo', 0, 26, 1),
       (104, 'Patricio', 0, 26, 1),
       (105, 'Disney World', 1, 27, 1),
       (106, 'Disney Land', 0, 27, 1),
       (107, 'Magic Park', 0, 27, 1),
       (108, 'Universal', 0, 27, 1),
       (109, 'Thor', 1, 28, 1),
       (110, 'Iron Man', 0, 28, 1),
       (111, 'Hulk', 0, 28, 1),
       (112, 'Aquaman', 0, 28, 1),
       (113, 'The Big Bang Theory', 1, 29, 1),
       (114, 'Friends', 0, 29, 1),
       (115, 'How I Met Your Mother', 0, 29, 1),
       (116, 'Modern Family', 0, 29, 1),
       (117, 'Smith', 0, 30, 1),
       (118, 'Byers', 0, 30, 1),
       (119, 'Wheeler', 1, 30, 1),
       (120, 'Cooper', 0, 30, 1),
       (121, 'Pablo Picasso', 0, 31, 0),
       (122, 'Vincent Van Gogh', 1, 31, 0),
       (123, 'Claude Monet', 0, 31, 0),
       (124, 'Salvador Dalí', 0, 31, 0);

CREATE TABLE `roles`
(
    `id_rol`     int(11)     NOT NULL,
    `nombre_rol` varchar(50) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_spanish_ci;

INSERT INTO `roles` (`id_rol`, `nombre_rol`)
VALUES (3, 'admin'),
       (2, 'editor'),
       (1, 'jugador');

CREATE TABLE `sexo`
(
    `id_sexo`     int(11)     NOT NULL,
    `descripcion` varchar(50) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

INSERT INTO `sexo` (`id_sexo`, `descripcion`)
VALUES (2, 'Femenino'),
       (1, 'Masculino'),
       (3, 'Prefiero no cargarlo');

CREATE TABLE `sugerencias_preguntas`
(
    `id_sugerencia`     int(11)  NOT NULL,
    `id_usuario`        int(11)  NOT NULL,
    `pregunta_sugerida` text     NOT NULL,
    `id_categoria`      int(11)  NOT NULL,
    `fecha_envio`       datetime NOT NULL                         DEFAULT current_timestamp(),
    `estado`            enum ('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
    `fecha_resolucion`  datetime                                  DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_spanish_ci;

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
    `longitud`             decimal(10, 6)        DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

CREATE TABLE `usuario_pregunta`
(
    `idUsuario`  int(11) NOT NULL,
    `idPregunta` int(11) NOT NULL,
    `fechaVisto` datetime DEFAULT current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

CREATE TABLE `usuario_rol`
(
    `id_usuario` int(11) NOT NULL,
    `id_rol`     int(11) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_spanish_ci;

ALTER TABLE `categoria`
    ADD PRIMARY KEY (`id_categoria`);

ALTER TABLE `ciudades`
    ADD PRIMARY KEY (`id_ciudad`),
    ADD KEY `id_pais` (`id_pais`);

ALTER TABLE `paises`
    ADD PRIMARY KEY (`id_pais`),
    ADD UNIQUE KEY `nombre_pais` (`nombre_pais`);

ALTER TABLE `partidas`
    ADD PRIMARY KEY (`id_partida`),
    ADD KEY `id_usuario` (`id_usuario`);

ALTER TABLE `partida_pregunta`
    ADD PRIMARY KEY (`id_partida`, `id_pregunta`),
    ADD KEY `pregunta_ibfk_2` (`id_pregunta`),
    ADD KEY `respuesta_ibfk_3` (`id_respuesta_elegida`);

ALTER TABLE `preguntas`
    ADD PRIMARY KEY (`id_pregunta`),
    ADD KEY `id_categoria` (`id_categoria`);

ALTER TABLE `preguntas_reportadas`
    ADD PRIMARY KEY (`id_reporte`),
    ADD KEY `id_pregunta` (`id_pregunta`),
    ADD KEY `id_reportador` (`id_reportador`);

ALTER TABLE `respuestas`
    ADD PRIMARY KEY (`id_respuesta`),
    ADD KEY `id_pregunta` (`id_pregunta`);

ALTER TABLE `roles`
    ADD PRIMARY KEY (`id_rol`),
    ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

ALTER TABLE `sexo`
    ADD PRIMARY KEY (`id_sexo`),
    ADD UNIQUE KEY `descripcion` (`descripcion`);

ALTER TABLE `sugerencias_preguntas`
    ADD PRIMARY KEY (`id_sugerencia`),
    ADD KEY `id_usuario` (`id_usuario`),
    ADD KEY `id_categoria` (`id_categoria`);

ALTER TABLE `usuarios`
    ADD PRIMARY KEY (`id_usuario`),
    ADD UNIQUE KEY `email` (`email`),
    ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
    ADD KEY `id_sexo` (`id_sexo`),
    ADD KEY `id_pais` (`id_pais`),
    ADD KEY `id_ciudad` (`id_ciudad`);

ALTER TABLE `usuario_pregunta`
    ADD PRIMARY KEY (`idUsuario`, `idPregunta`),
    ADD KEY `pregunta_ibfk_1` (`idPregunta`);

ALTER TABLE `usuario_rol`
    ADD PRIMARY KEY (`id_usuario`, `id_rol`),
    ADD KEY `id_rol` (`id_rol`);

ALTER TABLE `categoria`
    MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 9;

ALTER TABLE `ciudades`
    MODIFY `id_ciudad` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `paises`
    MODIFY `id_pais` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `partidas`
    MODIFY `id_partida` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `preguntas`
    MODIFY `id_pregunta` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 32;

ALTER TABLE `preguntas_reportadas`
    MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `respuestas`
    MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 125;

ALTER TABLE `roles`
    MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 4;

ALTER TABLE `sexo`
    MODIFY `id_sexo` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 4;

ALTER TABLE `sugerencias_preguntas`
    MODIFY `id_sugerencia` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `usuarios`
    MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ciudades`
    ADD CONSTRAINT `ciudades_ibfk_1` FOREIGN KEY (`id_pais`) REFERENCES `paises` (`id_pais`);

ALTER TABLE `partidas`
    ADD CONSTRAINT `usuarioPregunta_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `partida_pregunta`
    ADD CONSTRAINT `partida_ibfk_1` FOREIGN KEY (`id_partida`) REFERENCES `partidas` (`id_partida`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `pregunta_ibfk_2` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `respuesta_ibfk_3` FOREIGN KEY (`id_respuesta_elegida`) REFERENCES `respuestas` (`id_respuesta`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `preguntas`
    ADD CONSTRAINT `categoria_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);

ALTER TABLE `preguntas_reportadas`
    ADD CONSTRAINT `preguntas_reportadas_ibfk_1` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`) ON DELETE CASCADE,
    ADD CONSTRAINT `preguntas_reportadas_ibfk_2` FOREIGN KEY (`id_reportador`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

ALTER TABLE `respuestas`
    ADD CONSTRAINT `preguntas_ibfk_1` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sugerencias_preguntas`
    ADD CONSTRAINT `sugerencias_preguntas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
    ADD CONSTRAINT `sugerencias_preguntas_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);

ALTER TABLE `usuarios`
    ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_sexo`) REFERENCES `sexo` (`id_sexo`),
    ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_pais`) REFERENCES `paises` (`id_pais`),
    ADD CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id_ciudad`);

ALTER TABLE `usuario_pregunta`
    ADD CONSTRAINT `pregunta_ibfk_1` FOREIGN KEY (`idPregunta`) REFERENCES `preguntas` (`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `usuario_rol`
    ADD CONSTRAINT `usuario_rol_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
    ADD CONSTRAINT `usuario_rol_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE;
COMMIT;
