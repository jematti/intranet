
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS `db_datos` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `db_datos`;

-- Crear tabla `positions`
CREATE TABLE IF NOT EXISTS `positions` (
  `position_id` int(11) NOT NULL AUTO_INCREMENT,
  `position_name` varchar(50) NOT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Crear tabla `repositories`
CREATE TABLE IF NOT EXISTS `repositories` (
  `repository_id` int(11) NOT NULL AUTO_INCREMENT,
  `repository_name` varchar(100) NOT NULL,
  `building` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  PRIMARY KEY (`repository_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Crear tabla `roles`
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Crear tabla `permissions`
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(100) NOT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Crear tabla intermedia `role_permissions` para asignar permisos a los roles
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`permission_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Crear tabla unificada `user` que incluye administradores y empleados
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `ci` varchar(20) NOT NULL, -- Cédula de Identidad
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL, -- Almacenamiento del hash MD5 de la contraseña
  `email` varchar(100) NOT NULL, -- Correo institucional
  `personal_email` varchar(100) DEFAULT NULL, -- Correo personal
  `phone` varchar(15) DEFAULT NULL, -- Celular
  `cell_phone` varchar(15) DEFAULT NULL, -- Celular
  `birth_date` DATE DEFAULT NULL, -- Fecha de nacimiento
  `address` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `profile_img` varchar(255) DEFAULT NULL,
  `active_status` TINYINT(1) NOT NULL DEFAULT '1', -- 1 para activo, 0 para inactivo
  `position_id` int(11) DEFAULT NULL, -- Relación con la tabla positions (si es empleado)
  `repository_id` int(11) DEFAULT NULL, -- Relación con la tabla repositories (si es empleado)
  `role_id` int(11) NOT NULL, -- Relación con la tabla roles
  PRIMARY KEY (`user_id`),
  FOREIGN KEY (`position_id`) REFERENCES `positions`(`position_id`) ON DELETE SET NULL,
  FOREIGN KEY (`repository_id`) REFERENCES `repositories`(`repository_id`) ON DELETE SET NULL,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Crear tabla `sections` con relación a `repositories`
CREATE TABLE IF NOT EXISTS `sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(100) NOT NULL,
  `repository_id` int(11) NOT NULL, -- Relación con la tabla repositories
  `status` TINYINT(1) NOT NULL DEFAULT '1', -- 1 para activo, 0 para inactivo
  PRIMARY KEY (`section_id`),
  FOREIGN KEY (`repository_id`) REFERENCES `repositories`(`repository_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Crear tabla `categories` con referencia a `sections`
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL,
  `section_id` int(11) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT '1', -- 1 para activo, 0 para inactivo
  PRIMARY KEY (`category_id`),
  FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Crear tabla `storage` con relaciones a `user`, `repositories`, `sections` y `categories`
CREATE TABLE IF NOT EXISTS `storage` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(100) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `date_uploaded` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL, -- Relación con la tabla `user`
  `repository_id` int(11) NOT NULL, -- Relación con la tabla `repositories`
  `section_id` int(11) NOT NULL, -- Relación con la tabla `sections`
  `category_id` int(11) NOT NULL, -- Relación con la tabla `categories`
  `uploaded_by` int(11) NOT NULL, -- Relación con la tabla `user`
  `deleted_by` int(11) DEFAULT NULL, -- Relación con la tabla `user`
  `status` tinyint(1) NOT NULL DEFAULT 1, -- 1 para activo, 0 para inactivo
  PRIMARY KEY (`store_id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`repository_id`) REFERENCES `repositories` (`repository_id`) ON DELETE CASCADE,
  FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`deleted_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insertar datos de ejemplo en `positions`
INSERT INTO `positions` (`position_id`, `position_name`) VALUES
(1, 'PERSONAL DE APOYO JURIDICO'),
(2, 'MEDIADORA CULTURAL'),
(3, 'RESPONSABLE DE COMUNICACIÓN'),
(4, 'RESPONSABLE DE RECURSOS HUMANOS ai'),
(5, 'TECNICO ADMINISTRATIVO - TECNICO III'),
(6, 'AUDITOR INTERNO'),
(7, 'Encargado de Tienda'),
(8, 'JEFE NACIONAL DE GESTION CULTURAL ai'),
(9, 'COMUNICADORA CRC'),
(10, 'RESPONSABLE PLANIFICACION'),
(11, 'RESPONSABLE DE ANALISIS JURIDICO ai'),
(12, 'TECNICO III EN COMUNICACIÓN'),
(13, 'PROFESIONAL AUDITORIA'),
(14, 'CHOFER MENSAJERO'),
(15, 'PROFESIONAL EN GESTION INSTITUCIONAL ai'),
(16, 'PERSONAL DE APOYO ADMINISTRATIVO'),
(17, 'TECNICO II EN AUDITORIA'),
(18, 'MEDIADOR CULTURAL'),
(19, 'JEFE NACIONAL DE GESTION DE INFRAESTRUCTURA'),
(20, 'Tecnico en Contabilidad ai'),
(21, 'TECNICO EN ACTIVOS FIJOS Y ALMACENES ai'),
(22, 'GESTOR CULTURAL DE PROYECTOS'),
(23, 'PERSONAL DE APOYO INFORMATICO'),
(24, 'PERSONAL DE APOYO PRESIDENCIA'),
(25, 'RECEPCIONISTA ai'),
(26, 'PERSONAL DE APOYO I'),
(27, 'TECNICO INFORMATICO ai'),
(28, 'TECNICO EN SEGUIMIENTO INSTITUCIONAL ai'),
(29, 'INGENIERO CIVIL III'),
(30, 'PRESIDENTE FCBCB'),
(31, 'AUXILIAR JURIDICO TECNICO III'),
(32, 'TECNICO III EN RECURSOS HUMANOS'),
(33, 'PROFESIONAL EN AUDITORIA'),
(34, 'DISENADOR TECNICO II'),
(35, 'TECNICO EN RECURSOS HUMANO ai'),
(36, 'RESPONSABLE DE CONTRATACIONES ai'),
(37, 'GESTOR CULTURAL DE DESARROLLO DE PROYECTOS'),
(38, 'GESTORA CULTURAL DE PROYECTOS'),
(39, 'CONTADOR GENERAL ai'),
(40, 'PROFESIONAL EN ADMINISTRACION ai'),
(41, 'GESTOR CULTURAL II'),
(42, 'CONSULTOR ARQUITECTO PROFESIONAL III'),
(43, 'PROFESIONAL III INGENIERO CIVIL'),
(44, 'ARQUITECTO PROYECTISTA'),
(45, 'ARCHIVISTA TECNICO III'),
(46, 'DIRECTOR GENERAL'),
(47, 'ARQUITECTO II'),
(48, 'PERSONAL DE APOYO PARA ARCHIVO'),
(49, 'JEFE DE TRANSPARENCIA Y LUCHA CONTRA LA CORRUPCION'),
(50, 'SECRETARIA DE PRESIDENCIA'),
(51, 'JEFE NACIONAL DE ASUNTOS JURIDICOS ai'),
(52, 'SUBJEFE NACIONAL ADMINISTRATIVO ai'),
(53, 'TECNICO III DISEÑADOR'),
(54, 'RESPONSABLE DE PRESUPUESTOS'),
(55, 'TECNICO EN CONTRATACIONES'),
(56, 'RESPONSABLE DE ACTIVOS FIJOS Y ALMACENES ai'),
(57, 'JEFE NACIONAL DE ADMINISTRACION Y FINANZAS'),
(58, 'RESPONSABLE GESTION JURIDICA');

-- Insertar datos de ejemplo en `repositories`
INSERT INTO `repositories` (`repository_id`, `repository_name`, `building`, `department`) VALUES
(1, 'Fundación Cultural del Banco Central de Bolivia', 'La Paz', 'La Paz'),
(2, 'Archivo y Bibliotecas Nacionales de Bolivia', 'Sucre', 'Chuquisaca'),
(3, 'Casa de la Libertad', 'Sucre', 'Chuquisaca'),
(4, 'Casa de la Moneda', 'Potosi', 'Potosi'),
(5, 'Museo Nacional de Etnografía y Folklore', 'La Paz', 'La Paz'),
(6, 'Museo Nacional de Arte', 'La Paz', 'La Paz'),
(7, 'Centro de la Cultura Plurinacional', 'Santa Cruz', 'Santa Cruz'),
(8, 'Museo Fernando Montes', 'La Paz', 'La Paz'),
(9, 'Centro de la Revolución Cultural', 'La Paz', 'La Paz'),
(10, 'Casa Museo Marina Núñez del Prado', 'La Paz', 'La Paz');

-- Insertar roles de ejemplo
INSERT INTO `roles` (`role_name`) VALUES
('Super Admin'),
('Administrador de Página'),
('Empleado');

-- Insertar permisos de ejemplo
INSERT INTO `permissions` (`permission_name`) VALUES
('manage_users_add'),
('manage_users_delete'),
('manage_files_upload'),
('view_reports');

-- Asignar permisos a los roles
-- Super Admin tiene todos los permisos
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4);

-- Administrador de Página puede agregar usuarios y subir archivos
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(2, 1), (2, 3);

-- Empleado solo puede ver reportes
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3, 4);

-- Insertar datos de ejemplo en `sections` (relacionado con repositorios)
INSERT INTO `sections` (`section_name`, `repository_id`, `status`) VALUES
('Recursos Humanos', 1, 1),
('Documentación y Normativas', 2, 1);

-- Insertar datos de ejemplo en `categories` (relacionado con secciones)
INSERT INTO `categories` (`category_name`, `section_id`, `status`) VALUES
('Políticas y Procedimientos', 1, 1),
('Circulares', 1, 1);


-- Insertar usuarios de prueba en `user`
INSERT INTO `user` (`ci`, `firstname`, `lastname`, `username`, `password`, `email`, `phone`, `address`, `status`, `active_status`, `role_id`, `position_id`, `repository_id`)
VALUES
('6799225', 'Adrian', 'Villarreal', '6799225', MD5('123456'), 'adrian@empresa.com', '1402', '', 'active', 1, 3, 1, 1), -- Empleado
('4796382', 'Adriana', 'Sandalio Viscarra', '4796382', MD5('123456'), 'adriana@empresa.com', '1504', '', 'active', 1, 3, 2, 1), -- Empleado
('4922527', 'Angela', 'Aduviri Arroyo', '4922527', MD5('123456'), 'angela@empresa.com', '1104', '', 'active', 1, 3, 3, 1), -- Empleado
('4329603', 'Beatriz Lidia', 'Mamani Abelo', '4329603', MD5('123456'), 'beatriz@empresa.com', '1308', '', 'active', 1, 3, 4, 1), -- Empleado
('3407802', 'Carola', 'Gutierrez Soto', '3407802', MD5('123456'), 'carola@empresa.com', '1308', '', 'active', 1, 3, 5, 1), -- Empleado
('2682167', 'Cristobal', 'Apaza Bautista', '2682167', MD5('123456'), 'cristobal@empresa.com', '1321', '', 'active', 1, 3, 6, 1), -- Empleado
('6736666', 'Daniel Sergio', 'Aramayo Villarroel', '6736666', MD5('123456'), 'daniel@empresa.com', '1328', '', 'active', 1, 3, 7, 1), -- Empleado
('3358957', 'David', 'Aruquipa Pérez', '3358957', MD5('123456'), 'david@empresa.com', '1501', '', 'active', 1, 3, 8, 1), -- Empleado
('6190120', 'Denisse', 'Velásquez Silva', '6190120', MD5('123456'), 'denisse@empresa.com', '0', '', 'active', 1, 3, 9, 7), -- Empleado
('6985867', 'Elian', 'Álvarez Gómez', '6985867', MD5('123456'), 'elian@empresa.com', '1205', '', 'active', 1, 3, 10, 1), -- Empleado
('3423767', 'Erika', 'Gómez', '3423767', MD5('123456'), 'erika@empresa.com', '1403', '', 'active', 1, 3, 11, 1), -- Empleado
('7050731', 'Estefani', 'Huiza Fernández', '7050731', MD5('123456'), 'estefani@empresa.com', '1322', '', 'active', 1, 3, 12, 1), -- Empleado
('3325512', 'Estela', 'Ojeda Loza', '3325512', MD5('123456'), 'estela@empresa.com', '1327', '', 'active', 1, 3, 13, 1), -- Empleado
('2689803', 'Eustaquio', 'Vera Copa', '2689803', MD5('123456'), 'eustaquio@empresa.com', '1102', '', 'active', 1, 3, 14, 1), -- Empleado
('6123695', 'Evelin', 'Troche Espinoza', '6123695', MD5('123456'), 'evelin@empresa.com', '1204', '', 'active', 1, 3, 15, 1), -- Empleado
('6876773', 'Franco', 'Villatarco Zambrana', '6876773', MD5('123456'), 'franco@empresa.com', '1320', '', 'active', 1, 3, 16, 1), -- Empleado
('7008958', 'Gabriela', 'Fuentes Ramos', '7008958', MD5('123456'), 'gabriela@empresa.com', '1325', '', 'active', 1, 3, 17, 1), -- Empleado
('2543742', 'Grover', 'Choque Quispe', '2543742', MD5('123456'), 'grover@empresa.com', '0', '', 'active', 1, 3, 18, 7), -- Empleado
('5480490', 'Guadalupe', 'Chavez Choque', '5480490', MD5('123456'), 'guadalupe@empresa.com', '1601', '', 'active', 1, 3, 19, 1), -- Empleado
('3462509', 'Hector', 'Sempertegui Alvarez', '3462509', MD5('123456'), 'hector@empresa.com', '1313', '', 'active', 1, 3, 20, 1), -- Empleado
('4898121', 'Hernan Sandro', 'Aquino Churqui', '4898121', MD5('123456'), 'hernan@empresa.com', '1306', '', 'active', 1, 3, 21, 1), -- Empleado
('5989585', 'Janela Ingrid', 'Vargas Vasquez', '5989585', MD5('123456'), 'janela@empresa.com', '0', '', 'active', 1, 3, 22, 1), -- Empleado
('8324905', 'Javier Edson', 'Zapana', '8324905', MD5('123456'), 'javier@empresa.com', '1312', 'La Paz Bolivia', 'active', 1, 1, 23, 1), -- ADMIN
('3487543', 'Juan', 'Ramos', '3487543', MD5('123456'), 'juan@empresa.com', '1503', '', 'active', 1, 3, 24, 1), -- Empleado
('9209737', 'Karina', 'Saravia Flores', '9209737', MD5('123456'), 'karina@empresa.com', '1303', '', 'active', 1, 3, 25, 1), -- Empleado
('9070081', 'Katerine', 'Isidro Queso', '9070081', MD5('123456'), 'katerine@empresa.com', '1202', '', 'active', 1, 3, 26, 1), -- Empleado
('5762453', 'Luis Alberto', 'Fernandez Orellana', '5762453', MD5('123456'), 'luis.alberto@empresa.com', '1311', '', 'active', 1, 2, 27, 1), -- Empleado
('4791448', 'Luis', 'Arequipa Apaza', '4791448', MD5('123456'), 'luis.arequipa@empresa.com', '1405', '', 'active', 1, 3, 28, 1), -- Empleado
('4909891', 'Luis Daniel', 'Amezaga Bejarano', '4909891', MD5('123456'), 'luis.daniel@empresa.com', '1602', '', 'active', 1, 3, 29, 1), -- Empleado
('2220126', 'Luis', 'Oporto Ordoñez', '2220126', MD5('123456'), 'luis.oporto@empresa.com', '1101', '', 'active', 1, 3, 30, 1), -- Empleado
('4741713', 'Mabel', 'Belzu García', '4741713', MD5('123456'), 'mabel@empresa.com', '1404', '', 'active', 1, 3, 31, 1), -- Empleado
('2630284', 'Magali', 'Macias Bohorquez', '2630284', MD5('123456'), 'magali@empresa.com', '1304', '', 'active', 1, 3, 32, 1), -- Empleado
('5078422', 'Magali', 'Uribe García', '5078422', MD5('123456'), 'magali.uribe@empresa.com', '1323', '', 'active', 1, 3, 33, 1), -- Empleado
('6185880', 'Maria Alejandra', 'Cornejo Valdez', '6185880', MD5('123456'), 'maria.alejandra@empresa.com', '0', '', 'active', 1, 3, 34, 7), -- Empleado
('6081183', 'Maria Delina', 'Carvajal Duran', '6081183', MD5('123456'), 'maria.delina@empresa.com', '1309', '', 'active', 1, 3, 35, 1), -- Empleado
('4376835', 'Maria Guadalupe', 'Quintanilla Quelca', '4376835', MD5('123456'), 'maria.guadalupe@empresa.com', '1319', '', 'active', 1, 3, 36, 1), -- Empleado
('3375385', 'Mariana', 'Vargas Toro', '3375385', MD5('123456'), 'mariana@empresa.com', '0', '', 'active', 1, 3, 37, 7), -- Empleado
('5974311', 'Marianela', 'España Mita', '5974311', MD5('123456'), 'marianela@empresa.com', '0', '', 'active', 1, 3, 38, 7), -- Empleado
('4844721', 'Mario', 'Marca Honorio', '4844721', MD5('123456'), 'mario@empresa.com', '1315', '', 'active', 1, 3, 39, 1), -- Empleado
('3484596', 'Marisabel', 'Zubieta Salas', '3484596', MD5('123456'), 'marisabel@empresa.com', '1317', '', 'active', 1, 3, 40, 1), -- Empleado
('4878229', 'Mary Carmen', 'Molina Ergueta', '4878229', MD5('123456'), 'mary.carmen@empresa.com', '1504', '', 'active', 1, 3, 41, 1), -- Empleado
('6144712', 'Mauricio Fernando', 'Castillo Arratia', '6144712', MD5('123456'), 'mauricio@empresa.com', '1603', '', 'active', 1, 3, 42, 1), -- Empleado
('6783260', 'Melina Maribel', 'Maldonado Rios', '6783260', MD5('123456'), 'melina@empresa.com', '1602', '', 'active', 1, 3, 43, 1), -- Empleado
('2150176', 'Pablo Ernesto', 'Mansilla Salinas', '2150176', MD5('123456'), 'pablo@empresa.com', '1603', '', 'active', 1, 3, 44, 1), -- Empleado
('8412357', 'Patricia', 'Humana Lluta', '8412357', MD5('123456'), 'patricia@empresa.com', '0', '', 'active', 1, 3, 45, 7), -- Empleado
('3336972', 'Pavel', 'Pérez Armata', '3336972', MD5('123456'), 'pavel@empresa.com', '1201', '', 'active', 1, 3, 46, 1), -- Empleado
('4262272', 'Ramiro', 'Marquez Gallardo', '4262272', MD5('123456'), 'ramiro@empresa.com', '1604', '', 'active', 1, 3, 47, 1), -- Empleado
('4842254', 'Reyna', 'Roque Ortega', '4842254', MD5('123456'), 'reyna@empresa.com', '0', '', 'active', 1, 3, 48, 1), -- Empleado
('2058080', 'Ricardo', 'Aguilar Asin', '2058080', MD5('123456'), 'ricardo@empresa.com', '1105', '', 'active', 1, 3, 49, 1), -- Empleado
('4918718', 'Rita Lizeth', 'Quiroz Suarez', '4918718', MD5('123456'), 'rita@empresa.com', '1102', '', 'active', 1, 3, 50, 1), -- Empleado
('3251519', 'Rolando', 'Paniagua Espinoza', '3251519', MD5('123456'), 'rolando@empresa.com', '1401', '', 'active', 1, 3, 51, 1), -- Empleado
('8264230', 'Rosa Adelaida', 'Quispe Calle', '8264230', MD5('123456'), 'rosa@empresa.com', '1307', '', 'active', 1, 3, 52, 1), -- Empleado
('6965732', 'Silvia', 'Condori Mamani', '6965732', MD5('123456'), 'silvia@empresa.com', '1322', '', 'active', 1, 3, 53, 1), -- Empleado
('4985716', 'Silvia', 'Huanca Calle', '4985716', MD5('123456'), 'silvia.huanca@empresa.com', '1316', '', 'active', 1, 3, 54, 1), -- Empleado
('3431083', 'Willy', 'Quispe Lipa', '3431083', MD5('123456'), 'willy@empresa.com', '0', '', 'active', 1, 3, 55, 7), -- Empleado
('4831236', 'Yecid Gustavo', 'Sanchez Velasco', '4831236', MD5('123456'), 'yecid@empresa.com', '1310', '', 'active', 1, 3, 56, 1), -- Empleado
('4848702', 'Yussela Saleth', 'Goyzueta Ramos', '4848702', MD5('123456'), 'yussela@empresa.com', '1301', '', 'active', 1, 3, 57, 1), -- Empleado
('6102450', 'Waldo', 'Vaca Alvarez', '6102450', MD5('123456'), 'waldo@empresa.com', '1407', '', 'active', 1, 3, 58, 1); -- Empleado

-- Insertar datos de ejemplo en la tabla `storage`
INSERT INTO `storage` 
(`filename`, `file_type`, `date_uploaded`, `user_id`, `repository_id`, `section_id`, `category_id`, `uploaded_by`, `deleted_by`, `status`) 
VALUES
('documento1.pdf', 'application/pdf', '2024-09-10', 1, 1, 1, 1, 1, NULL, 1), -- Archivo subido por el usuario 1 en el repositorio 1, sección 1, categoría 1
('imagen2.png', 'image/png', '2024-09-11', 2, 1, 1, 1, 2, NULL, 1), -- Archivo subido por el usuario 2
('video3.mp4', 'video/mp4', '2024-09-12', 3, 2, 2, 2, 3, NULL, 1); -- Archivo subido por el usuario 3
