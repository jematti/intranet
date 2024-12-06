
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS `db_datos` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `db_datos`;

-- Crear tabla `positions`
CREATE TABLE IF NOT EXISTS `positions` (
  `position_id` int(11) NOT NULL AUTO_INCREMENT,
  `position_name` varchar(50) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT '1', -- 1 para activo, 0 para inactivo
  PRIMARY KEY (`position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Crear tabla `repositories`
CREATE TABLE IF NOT EXISTS `repositories` (
  `repository_id` int(11) NOT NULL AUTO_INCREMENT,
  `repository_name` varchar(100) NOT NULL,
  `building` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT '1', -- 1 para activo, 0 para inactivo
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

-- Crear tabla `sections` con relación a `repositories`
CREATE TABLE IF NOT EXISTS `sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(100) NOT NULL,
  `repository_id` int(11) NOT NULL, -- Relación con la tabla repositories
  `status` TINYINT(1) NOT NULL DEFAULT '1', -- 1 para activo, 0 para inactivo
  PRIMARY KEY (`section_id`),
  FOREIGN KEY (`repository_id`) REFERENCES `repositories`(`repository_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Crear tabla unificada `user` que incluye administradores y empleados
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_img` varchar(255) DEFAULT NULL, /* Imagen de perfil */
  `firstname` varchar(50) NOT NULL, /* Primer nombre */
  `middlename` varchar(50) DEFAULT NULL, /* Segundo nombre */
  `thirdname` varchar(50) DEFAULT NULL, /* Tercer nombre */
  `lastname_father` varchar(50) NOT NULL, /* Apellido paterno */
  `lastname_mother` varchar(50) NOT NULL, /* Apellido materno */
  `married_lastname` varchar(50) DEFAULT NULL, /* Apellido de casada */
  `marital_status` varchar(20) DEFAULT NULL, /* Estado civil */
  `username` varchar(20) NOT NULL, /* Nombre de usuario */
  `nationality` varchar(50) DEFAULT NULL, /* Nacionalidad */
  `gender` varchar(10) DEFAULT NULL, /* Sexo */
  `blood_type` varchar(5) DEFAULT NULL, /* Grupo sanguíneo */
  `password` varchar(32) NOT NULL, /* Hash MD5 de la contraseña */
  `email` varchar(100) NOT NULL, /* Correo institucional */
  `personal_email` varchar(100) DEFAULT NULL, /* Correo personal */
  `phone` varchar(15) DEFAULT NULL, /* Teléfono interno repositorio */
  `cell_phone` varchar(15) DEFAULT NULL, /* Celular */
  `landline_phone` varchar(15) DEFAULT NULL, /* Teléfono fijo del usuario */
  `repository_phone` varchar(15) DEFAULT NULL, /* Teléfono fijo del repositorio */
  /*tipo de documento*/
  `document_type` varchar(50) NOT NULL, /* Tipo de documento */
  `document_number` varchar(20) NOT NULL, /* Número de documento */
  `document_expiry_date` DATE DEFAULT NULL, /* Fecha de caducidad del documento */
  `document_issued_in` varchar(50) DEFAULT NULL, /* Lugar de expedición del documento */
  /*ubicacion*/
  `birth_date` DATE DEFAULT NULL, /* Fecha de nacimiento */
  `birth_country` varchar(50) DEFAULT NULL, /* País de nacimiento */
  `birth_city` varchar(50) DEFAULT NULL, /* Ciudad de nacimiento */
  `birth_province` varchar(50) DEFAULT NULL, /* Provincia de nacimiento */
  `residence_department` varchar(50) DEFAULT NULL, /* Departamento de residencia */
  `residence_municipality` varchar(50) DEFAULT NULL, /* Municipio */
  `residence_zone` varchar(50) DEFAULT NULL, /* Zona de domicilio */
  `residence_street` varchar(100) DEFAULT NULL, /* Avenida o calle */
  `residence_number` varchar(10) DEFAULT NULL, /* Número */
  `residence_building` varchar(100) DEFAULT NULL, /* Edificio */
  `residence_floor` varchar(10) DEFAULT NULL, /* Piso */
  `residence_apartment` varchar(10) DEFAULT NULL, /* Número de departamento */
  /*contacto emergencia*/
  `emergency_contact_name` varchar(100) DEFAULT NULL, /* Nombre de contacto de emergencia */
  `emergency_contact_phone` varchar(15) DEFAULT NULL, /* Número de contacto de emergencia */
  `emergency_contact_relationship` varchar(50) DEFAULT NULL, /* Relación con el contacto de emergencia */
  /*educacion basica*/
  `basic_education_course` varchar(100) DEFAULT NULL, /* Último curso vencido en educación básica */
  `basic_education_year` YEAR DEFAULT NULL, /* Año del último curso vencido en educación básica */
  `basic_education_institution` varchar(100) DEFAULT NULL, /* Colegio o institución donde cursó la educación básica */
  `has_basic_degree` TINYINT(1) DEFAULT NULL, /* Obtuvo título de educación básica */
  `basic_education_location` varchar(100) DEFAULT NULL, /* Lugar donde se cursó la educación básica */
  /*datos usuarios*/
  `status` varchar(20) NOT NULL, /* Estado (activo/inactivo, etc.) */
  `active_status` TINYINT(1) NOT NULL DEFAULT '1', /* 1 para activo, 0 para inactivo */
  `position_id` int(11) DEFAULT NULL, /* Relación con la tabla positions */
  `repository_id` int(11) DEFAULT NULL, /* Relación con la tabla repositories */
  `section_id` int(11) DEFAULT NULL, /* Relación con la tabla sections */
  `role_id` int(11) NOT NULL, /* Relación con la tabla roles */
  PRIMARY KEY (`user_id`),
  FOREIGN KEY (`position_id`) REFERENCES `positions`(`position_id`) ON DELETE SET NULL,
  FOREIGN KEY (`repository_id`) REFERENCES `repositories`(`repository_id`) ON DELETE SET NULL,
  FOREIGN KEY (`section_id`) REFERENCES `sections`(`section_id`) ON DELETE SET NULL,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE CASCADE
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
INSERT INTO `positions` (`position_id`, `position_name`, `status`) VALUES
(1, 'PERSONAL DE APOYO JURIDICO',1),
(2, 'MEDIADORA CULTURAL',1),
(3, 'RESPONSABLE DE COMUNICACIÓN',1),
(4, 'RESPONSABLE DE RECURSOS HUMANOS ai',1),
(5, 'TECNICO ADMINISTRATIVO - TECNICO III',1),
(6, 'AUDITOR INTERNO',1),
(7, 'ENCARGADO DE TIENDA',1),
(8, 'JEFE NACIONAL DE GESTION CULTURAL ai',1),
(9, 'COMUNICADORA CRC',1),
(10, 'RESPONSABLE PLANIFICACION',1),
(11, 'RESPONSABLE DE ANALISIS JURIDICO ai',1),
(12, 'TECNICO III EN COMUNICACIÓN',1),
(13, 'PROFESIONAL AUDITORIA',1),
(14, 'CHOFER MENSAJERO',1),
(15, 'PROFESIONAL EN GESTION INSTITUCIONAL ai',1),
(16, 'PERSONAL DE APOYO ADMINISTRATIVO',1),
(17, 'TECNICO II EN AUDITORIA',1),
(18, 'MEDIADOR CULTURAL',1),
(19, 'JEFE NACIONAL DE GESTION DE INFRAESTRUCTURA',1),
(20, 'TECNICO EN CONTABILIDAD AI',1),
(21, 'TECNICO EN ACTIVOS FIJOS Y ALMACENES ai',1),
(22, 'GESTOR CULTURAL DE PROYECTOS',1),
(23, 'PERSONAL DE APOYO INFORMATICO',1),
(24, 'PERSONAL DE APOYO PRESIDENCIA',1),
(25, 'RECEPCIONISTA ai',1),
(26, 'PERSONAL DE APOYO I',1),
(27, 'TECNICO INFORMATICO ai',1),
(28, 'TECNICO EN SEGUIMIENTO INSTITUCIONAL ai',1),
(29, 'INGENIERO CIVIL III',1),
(30, 'PRESIDENTE FCBCB',1),
(31, 'AUXILIAR JURIDICO TECNICO III',1),
(32, 'TECNICO III EN RECURSOS HUMANOS',1),
(33, 'PROFESIONAL EN AUDITORIA',1),
(34, 'DISENADOR TECNICO II',1),
(35, 'TECNICO EN RECURSOS HUMANO ai',1),
(36, 'RESPONSABLE DE CONTRATACIONES ai',1),
(37, 'GESTOR CULTURAL DE DESARROLLO DE PROYECTOS',1),
(38, 'GESTORA CULTURAL DE PROYECTOS',1),
(39, 'CONTADOR GENERAL ai',1),
(40, 'PROFESIONAL EN ADMINISTRACION ai',1),
(41, 'GESTOR CULTURAL II',1),
(42, 'CONSULTOR ARQUITECTO PROFESIONAL III',1),
(43, 'PROFESIONAL III INGENIERO CIVIL',1),
(44, 'ARQUITECTO PROYECTISTA',1),
(45, 'ARCHIVISTA TECNICO III',1),
(46, 'DIRECTOR GENERAL',1),
(47, 'ARQUITECTO II',1),
(48, 'PERSONAL DE APOYO PARA ARCHIVO',1),
(49, 'JEFE DE TRANSPARENCIA Y LUCHA CONTRA LA CORRUPCION',1),
(50, 'SECRETARIA DE PRESIDENCIA',1),
(51, 'JEFE NACIONAL DE ASUNTOS JURIDICOS ai',1),
(52, 'SUBJEFE NACIONAL ADMINISTRATIVO ai',1),
(53, 'TECNICO III DISEÑADOR',1),
(54, 'RESPONSABLE DE PRESUPUESTOS',1),
(55, 'TECNICO EN CONTRATACIONES',1),
(56, 'RESPONSABLE DE ACTIVOS FIJOS Y ALMACENES ai',1),
(57, 'JEFE NACIONAL DE ADMINISTRACION Y FINANZAS',1),
(58, 'RESPONSABLE GESTION JURIDICA',1);

-- Insertar datos de ejemplo en `repositories`
INSERT INTO `repositories` (`repository_id`, `repository_name`, `building`, `department`, `status` ) VALUES
(1, 'Consejo de Administración y Presidencia', 'La Paz', 'La Paz',1),
(2, 'Archivo y Bibliotecas Nacionales de Bolivia', 'Sucre', 'Chuquisaca',1),
(3, 'Casa de la Libertad', 'Sucre', 'Chuquisaca',1),
(4, 'Casa de la Moneda', 'Potosi', 'Potosi',1),
(5, 'Museo Nacional de Etnografía y Folklore', 'La Paz', 'La Paz',1),
(6, 'Museo Nacional de Arte', 'La Paz', 'La Paz',1),
(7, 'Centro de la Cultura Plurinacional', 'Santa Cruz', 'Santa Cruz',1),
(8, 'Museo Fernando Montes', 'La Paz', 'La Paz',1),
(9, 'Centro de la Revolución Cultural', 'La Paz', 'La Paz',1),
(10, 'Casa Museo Marina Núñez del Prado', 'La Paz', 'La Paz',1);

-- Insertar roles de ejemplo
INSERT INTO `roles` (`role_name`) VALUES
('Super Admin'),
('Administrador de Página'),
('Empleado'),
('Administrador Repositorio');

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
('Presidencia', 1, 1),
('Unid. de Trans. y Lucha Contra la Corrupción', 1, 1),
('Dirección General', 1, 1),
('Unidad Nacional de Asuntos Juridicos', 1, 1),
('Unidad Nacional de Gestión Cultural', 1, 1),
('Unidad Nacional de Gestión de Infraestructura', 1, 1),
('Unidad Nacional de Administración y Finanzas', 1, 1),
('Sección de Recursos Humanos', 1, 1),
('Sub Unidad Nacional Administrativa', 1, 1),
('Auditoria Interna', 1, 1),
('Museo Fernando Montes', 1, 1),
('Sección de Sistemas', 1, 1),
('Sección de Contratación', 1, 1),
('Sub Unidad Nacional Financiera', 1, 1),
('Unidad de Archivo', 2, 1),
('Unidad de Adminitración y Finanzas', 2, 1),
('Unidad de Biblioteca Pública', 2, 1),
('Unidad de Biblioteca', 2, 1),
('Dirección', 2, 1),
('Unidad de Administración y Finanzas', 3, 1),
('Unidad de Museo', 3, 1),
('Presidencia', 3, 1),
('Dirección', 3, 1),
('Unidad de Administración y Finanzas', 4, 1),
('Dirección', 4, 1),
('Unidad de Museo', 4, 1),
('Unidad de Archivo', 4, 1),
('Unidad de Extensión', 5, 1),
('Unidad de Adminitración y Finanzas', 5, 1),
('Unidad de Investigación', 5, 1),
('Unidad de Museo', 5, 1),
('Dirección', 5, 1),
('Regional Sucre', 5, 1),
('Dirección General', 6, 1),
('Unidad de Dirección', 6, 1),
('Unidad de Museo', 6, 1),
('Unidad de Administración y Finanzas', 6, 1),
('Presidencia de la FC-BCB', 7, 1),
('Dirección', 7, 1),
('Jefatura de la Unidad de Administración y Finanzas', 7, 1),
('Jefatura de la Unidad de Cultura', 7, 1),
('Dirección', 7, 1),
('Administración', 8, 1),
('Administración', 9, 1),
('Administración', 10, 1);


INSERT INTO `user` (`firstname`, `lastname_father`, `username`, `password`, `status`, `active_status`, `role_id`, `position_id`, `repository_id`, `section_id`)
VALUES
('Juan', 'Pérez', 'juanp', MD5('password123'), 'activo', 1, 1, 23, 1, 12);



CREATE TABLE `family_members` (
    `family_member_id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL, -- Relación con el usuario
    `relationship` VARCHAR(50) NOT NULL, -- Relación (padre, madre, hermano, etc.)
    `first_name` VARCHAR(50) NOT NULL,
    `lastname_father` varchar(50) NOT NULL, -- Apellido paterno
    `lastname_mother` varchar(50) NOT NULL, -- Apellido materno
    `gender` ENUM('M', 'F') NOT NULL,
    `birth_date` DATE NOT NULL,
    `birth_place` VARCHAR(100) NOT NULL, -- Lugar de nacimiento
    `document_type` VARCHAR(50) DEFAULT NULL, -- Tipo de documento
    `document_number` VARCHAR(20) DEFAULT NULL, -- Número de documento
    PRIMARY KEY (`family_member_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `educational_background` (
    `education_id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL, -- Relación con el usuario
    `education_level` VARCHAR(50) NOT NULL, -- Nivel educativo (bachiller, licenciatura, etc.)
    `institution` VARCHAR(100) NOT NULL, -- Nombre de la institución
    `year_completed` YEAR DEFAULT NULL, -- Año de finalización
    `degree_obtained` TINYINT(1) NOT NULL DEFAULT 0, -- 1 para título obtenido
    `location` VARCHAR(100) NOT NULL, -- Lugar de la institución
    PRIMARY KEY (`education_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `work_experience` (
    `work_id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL, -- Relación con el usuario
    `institution_name` VARCHAR(100) NOT NULL,
    `position` VARCHAR(50) NOT NULL, -- Puesto desempeñado
    `start_date` DATE NOT NULL, -- Fecha de inicio
    `end_date` DATE DEFAULT NULL, -- Fecha de finalización
    `reason_for_leaving` VARCHAR(100) DEFAULT NULL, -- Motivo de salida
    PRIMARY KEY (`work_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
