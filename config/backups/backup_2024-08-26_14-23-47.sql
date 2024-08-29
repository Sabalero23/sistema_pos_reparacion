-- Copia de seguridad de la base de datos repair
-- Generada el 2024-08-26 14:23:47

DROP TABLE IF EXISTS `budget_items`;


CREATE TABLE `budget_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `budget_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `budget_id` (`budget_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `budget_items_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `budget_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `budget_items` VALUES("1","1","8","1","85000.00");
INSERT INTO `budget_items` VALUES("2","1","32","1","15000.00");
INSERT INTO `budget_items` VALUES("3","1","12","1","15000.00");
INSERT INTO `budget_items` VALUES("4","2","8","1","85000.00");
INSERT INTO `budget_items` VALUES("5","2","32","1","15000.00");
INSERT INTO `budget_items` VALUES("6","2","12","1","15000.00");



DROP TABLE IF EXISTS `budgets`;


CREATE TABLE `budgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `budget_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pendiente','aprobado','rechazado','expirado') DEFAULT 'pendiente',
  `validity_period` int(11) DEFAULT '30',
  `notes` text,
  `view_token` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `view_token` (`view_token`),
  KEY `customer_id` (`customer_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `budgets_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `budgets` VALUES("1","6","1","2024-08-18 23:37:57","115000.00","expirado","7","Sujeto a modificación cotización Dólar Blue!","a3f46bbf67304585bfe6aff03d1506cf39ecc176773b93a6dc136cb0c937530c");
INSERT INTO `budgets` VALUES("2","4","1","2024-08-25 22:15:43","115000.00","aprobado","2","Presupuesto sujeto a modificación a la cotización del dólar Blue.","24405dc3a24097b329c4bdc0e8a1cb5bfec26aee8af486bac859e6b79e7c1183");



DROP TABLE IF EXISTS `cash_register_movements`;


CREATE TABLE `cash_register_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cash_register_session_id` int(11) NOT NULL,
  `movement_type` enum('sale','purchase','cash_in','cash_out') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `notes` text,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_register_session_id` (`cash_register_session_id`),
  CONSTRAINT `cash_register_movements_ibfk_1` FOREIGN KEY (`cash_register_session_id`) REFERENCES `cash_register_sessions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

INSERT INTO `cash_register_movements` VALUES("1","1","cash_out","64000.00",NULL,"Ganancia","2024-08-12 15:03:25");
INSERT INTO `cash_register_movements` VALUES("2","1","cash_out","14500.00",NULL,"Ganancia","2024-08-12 20:30:44");
INSERT INTO `cash_register_movements` VALUES("3","2","cash_in","50000.00",NULL,"Reserva Celular Samsung A50 Reacondicionado","2024-08-13 08:32:17");
INSERT INTO `cash_register_movements` VALUES("4","2","cash_out","207000.00",NULL,"Ganancia","2024-08-13 19:47:54");
INSERT INTO `cash_register_movements` VALUES("5","3","cash_in","8000.00",NULL,"Se&ntilde;a Bateria J2 Prime","2024-08-14 11:10:11");
INSERT INTO `cash_register_movements` VALUES("6","4","cash_out","38400.00",NULL,"Ganancia","2024-08-15 20:40:41");
INSERT INTO `cash_register_movements` VALUES("7","5","cash_out","1500.00",NULL,"Mandado","2024-08-16 10:48:35");
INSERT INTO `cash_register_movements` VALUES("8","5","cash_out","38000.00",NULL,"Compra Modulo A50","2024-08-16 11:47:25");
INSERT INTO `cash_register_movements` VALUES("9","5","cash_out","115000.00",NULL,"Ganancia","2024-08-16 20:47:55");
INSERT INTO `cash_register_movements` VALUES("10","6","cash_out","2000.00",NULL,"Mandado","2024-08-17 11:57:52");
INSERT INTO `cash_register_movements` VALUES("11","6","cash_out","7000.00",NULL,"Ganancia","2024-08-17 12:57:17");
INSERT INTO `cash_register_movements` VALUES("12","7","cash_out","600.00",NULL,"Compra trapo de piso.","2024-08-19 08:28:41");
INSERT INTO `cash_register_movements` VALUES("13","7","cash_in","25000.00",NULL,"Se&ntilde;a reparaci&oacute;n Modulo M13","2024-08-19 12:40:28");
INSERT INTO `cash_register_movements` VALUES("14","7","cash_out","236500.00",NULL,"Ganancia","2024-08-19 20:24:05");
INSERT INTO `cash_register_movements` VALUES("15","8","cash_out","2000.00",NULL,"Mandado Oreste","2024-08-20 11:20:49");
INSERT INTO `cash_register_movements` VALUES("16","8","cash_out","25000.00",NULL,"Ganancia","2024-08-20 12:06:10");
INSERT INTO `cash_register_movements` VALUES("17","8","cash_out","34000.00",NULL,"Ganancia","2024-08-20 22:21:34");
INSERT INTO `cash_register_movements` VALUES("18","9","cash_out","50000.00",NULL,"Ganancia","2024-08-21 12:10:43");
INSERT INTO `cash_register_movements` VALUES("19","9","cash_out","2000.00",NULL,"Mandado","2024-08-21 17:45:48");
INSERT INTO `cash_register_movements` VALUES("20","9","cash_in","25000.00",NULL,"Se&ntilde;a Orden ORD20240821165854541","2024-08-21 20:28:08");
INSERT INTO `cash_register_movements` VALUES("21","9","cash_out","8000.00",NULL,"Ganancia","2024-08-21 20:34:34");
INSERT INTO `cash_register_movements` VALUES("22","10","cash_in","6000.00",NULL,"Seña de Morzan Gustavo de la orden ORD20240822175708609","2024-08-22 17:57:11");
INSERT INTO `cash_register_movements` VALUES("23","10","cash_out","2000.00",NULL,"Compra Queso","2024-08-22 20:09:56");
INSERT INTO `cash_register_movements` VALUES("24","10","cash_out","4000.00",NULL,"Pin de carga","2024-08-22 21:10:41");
INSERT INTO `cash_register_movements` VALUES("25","10","cash_out","59000.00",NULL,"Ganancia","2024-08-22 23:11:22");
INSERT INTO `cash_register_movements` VALUES("26","11","cash_out","39000.00",NULL,"Cierre Correcto","2024-08-23 20:10:36");
INSERT INTO `cash_register_movements` VALUES("27","12","cash_out","10000.00",NULL,"Ganancia","2024-08-24 12:01:51");
INSERT INTO `cash_register_movements` VALUES("28","13","cash_in","32000.00",NULL,"Seña de Eichemberger Giovanni de la orden ORD20240826103935854","2024-08-26 10:39:39");
INSERT INTO `cash_register_movements` VALUES("29","13","cash_out","2000.00",NULL,"Mandado","2024-08-26 11:14:44");
INSERT INTO `cash_register_movements` VALUES("30","13","cash_out","45000.00",NULL,"Ganancia","2024-08-26 12:05:52");



DROP TABLE IF EXISTS `cash_register_sessions`;


CREATE TABLE `cash_register_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `opening_date` datetime NOT NULL,
  `closing_date` datetime DEFAULT NULL,
  `opening_balance` decimal(10,2) NOT NULL,
  `closing_balance` decimal(10,2) DEFAULT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `cash_register_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

INSERT INTO `cash_register_sessions` VALUES("1","1","2024-08-12 08:39:27","2024-08-12 21:15:10","4500.00","2000.00","closed","Inicio Caja Comercio\n\n\nCierre de caja correcto");
INSERT INTO `cash_register_sessions` VALUES("2","1","2024-08-13 22:24:35","2024-08-13 20:49:01","2000.00","2000.00","closed","Inicio caja\nCierre Correcto");
INSERT INTO `cash_register_sessions` VALUES("3","1","2024-08-14 08:04:06","2024-08-14 20:13:18","2000.00","20000.00","closed","Inicio Caja\nCierre correcto");
INSERT INTO `cash_register_sessions` VALUES("4","1","2024-08-15 08:15:04","2024-08-15 20:41:13","20000.00","2100.00","closed","Inicio caja\nCierre Correcto");
INSERT INTO `cash_register_sessions` VALUES("5","1","2024-08-16 08:41:59","2024-08-16 22:51:38","2100.00","1100.00","closed","Inicio caja\nCierre correcto");
INSERT INTO `cash_register_sessions` VALUES("6","1","2024-08-17 08:53:44","2024-08-17 12:58:36","1100.00","1100.00","closed","Inicio caja\nCierre correcto");
INSERT INTO `cash_register_sessions` VALUES("7","1","2024-08-19 02:10:14","2024-08-19 20:24:24","1100.00","1300.00","closed","Apertura\nCaja correcta");
INSERT INTO `cash_register_sessions` VALUES("8","1","2024-08-20 08:09:40","2024-08-20 22:21:55","1300.00","2300.00","closed","Inicio de caja\nCierre correcto.");
INSERT INTO `cash_register_sessions` VALUES("9","1","2024-08-21 12:03:08","2024-08-21 20:34:44","2300.00","2200.00","closed","Inicio Caja\nCierre Correcto");
INSERT INTO `cash_register_sessions` VALUES("10","1","2024-08-22 08:28:24","2024-08-22 23:11:40","2200.00","2100.00","closed","Inicio Caja\nCierre correcto.");
INSERT INTO `cash_register_sessions` VALUES("11","1","2024-08-23 08:16:36","2024-08-23 20:10:58","2100.00","1100.00","closed","Inicio Caja\nCierre correcto");
INSERT INTO `cash_register_sessions` VALUES("12","1","2024-08-24 10:19:39","2024-08-24 12:02:10","1100.00","1100.00","closed","Inicio Caja\nCierre Correcto");
INSERT INTO `cash_register_sessions` VALUES("13","1","2024-08-26 08:16:46",NULL,"1100.00",NULL,"open","Inicio caja.");



DROP TABLE IF EXISTS `categories`;


CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

INSERT INTO `categories` VALUES("1","Cargadores","Cargadores turbo 1ra Calidad");
INSERT INTO `categories` VALUES("2","Glass","Protectores Glass");
INSERT INTO `categories` VALUES("3","Software","Instalación Software / Sistemas Operativos");
INSERT INTO `categories` VALUES("4","Reparaciones","Reparaciones en general");
INSERT INTO `categories` VALUES("5","FRP/F4","Cuentas Google y Reparación de Señal");
INSERT INTO `categories` VALUES("6","Asistencia","Costo asistencia");
INSERT INTO `categories` VALUES("7","Cables","Cables 1ra calidad");
INSERT INTO `categories` VALUES("8","Auriculares","Auriculares 1ra calidad");
INSERT INTO `categories` VALUES("9","Cámaras","Cámaras 1ra calidad");
INSERT INTO `categories` VALUES("10","Almacenamiento","Dispositivos de almacenamiento");
INSERT INTO `categories` VALUES("11","Módulos","Módulos 1a calidad");
INSERT INTO `categories` VALUES("12","Celulares","Celulares Nuevos");
INSERT INTO `categories` VALUES("13","Celulares Reacondicionados","Celulares reacondicionados con garantía");
INSERT INTO `categories` VALUES("14","Varios","Categoría Varios");
INSERT INTO `categories` VALUES("15","Baterias","Baterías nuevas");
INSERT INTO `categories` VALUES("16","Placas","Placas de carga, Main.");
INSERT INTO `categories` VALUES("17","General","Categoría general para productos");



DROP TABLE IF EXISTS `company_info`;


CREATE TABLE `company_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `website` varchar(100) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT '/uploads/logo.png',
  `legal_info` text NOT NULL,
  `receipt_footer` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

INSERT INTO `company_info` VALUES("1","Cellcom Technology","Calle 9 Nro 539","543482549555","info@cellcomweb.com.ar","https://www.cellcomweb.com.ar","/public/uploads/66c627b635d77.png","CUIT: 20-30100538-6 | Ingresos Brutos: entrámite","Gracias por visitarnos. ¡Esperamos verlo pronto!");



DROP TABLE IF EXISTS `customer_accounts`;


CREATE TABLE `customer_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `balance` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `customer_accounts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `customers`;


CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

INSERT INTO `customers` VALUES("1","Consumidor Final","info@cellcomweb.com.ar","543482549555","Calle 9 Nro 539","2024-08-20 14:31:00");
INSERT INTO `customers` VALUES("2","Segovia Norma","orden@taller.cellcomweb.com.ar","543482306272","Pje 126/128 Nro 73","2024-08-21 10:25:31");
INSERT INTO `customers` VALUES("3","Bordon Horacio","orden@taller.cellcomweb.com.ar","543482241406","Calle 10 Nro 292","2024-08-21 11:27:07");
INSERT INTO `customers` VALUES("4","Lopez Faustino","lopezfaustino@gmail.com","543482332687","Calle 311 Nro 540","2024-08-19 23:08:35");
INSERT INTO `customers` VALUES("5","Flores Julio","orden@taller.cellcomweb.com.ar","543482625707","Calle 13 nro 62","2024-08-21 19:58:00");
INSERT INTO `customers` VALUES("6","Rodriguez Claudia","claudia.alicia.rodriguez@gmail.com","543482628711","Alvear y Pasaje 36/38","2024-08-21 20:03:16");
INSERT INTO `customers` VALUES("7","Medina Hugo","orden@cellcomweb.com.ar","543482598515","Calle 21 Nro 1035","2024-08-22 11:42:18");
INSERT INTO `customers` VALUES("8","Morzan Gustavo","gmorzan3177@gmail.com","543482244010","Pje 18 nro 35","2024-08-22 17:56:34");
INSERT INTO `customers` VALUES("9","Alegre Héctor","hectoralegre13@gmail.com","543482527825","Calle 330 Nro 14","2024-08-22 20:06:17");
INSERT INTO `customers` VALUES("10","Sandrigo Cristian","orden@cellcomweb.com.ar","543482576101","Ausonia","2024-08-22 23:02:34");
INSERT INTO `customers` VALUES("11","Herrera Micaela","orden@cellcomweb.com.ar","543482634853","Calle 121 nro 981","2024-08-23 17:09:01");
INSERT INTO `customers` VALUES("12","Eichemberger Giovanni","giovanni.eichen@gmail.com","543482313715","Calle 24 nro 935","2024-08-26 10:38:43");



DROP TABLE IF EXISTS `home_visits`;


CREATE TABLE `home_visits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `visit_time` time NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('programada','completada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'programada',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_home_visits_customer_id` (`customer_id`),
  CONSTRAINT `fk_home_visits_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS `inventory_adjustment_items`;


CREATE TABLE `inventory_adjustment_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_before` int(11) NOT NULL,
  `quantity_after` int(11) NOT NULL,
  `reason` enum('dañado','perdido','correccion','otro') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `adjustment_id` (`adjustment_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `inventory_adjustment_items_ibfk_1` FOREIGN KEY (`adjustment_id`) REFERENCES `inventory_adjustments` (`id`),
  CONSTRAINT `inventory_adjustment_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8;

INSERT INTO `inventory_adjustment_items` VALUES("1","1","52","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("2","1","13","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("3","1","19","-7","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("4","1","1","-5","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("5","1","22","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("6","1","48","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("7","1","55","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("8","1","56","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("9","1","51","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("10","1","23","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("11","1","24","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("12","1","25","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("13","1","26","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("14","1","27","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("15","1","43","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("16","1","42","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("17","1","8","2","2","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("18","1","36","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("19","1","2","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("20","1","4","-1","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("21","1","3","-1","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("22","1","5","-11","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("23","1","28","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("24","1","29","-1","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("25","1","30","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("26","1","31","-2","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("27","1","21","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("28","1","6","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("29","1","53","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("30","1","49","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("31","1","7","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("32","1","9","-1","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("33","1","14","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("34","1","46","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("35","1","45","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("36","1","15","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("37","1","16","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("38","1","17","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("39","1","18","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("40","1","38","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("41","1","39","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("42","1","47","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("43","1","37","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("44","1","10","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("45","1","11","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("46","1","12","-1","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("47","1","32","-1","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("48","1","33","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("49","1","34","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("50","1","44","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("51","1","50","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("52","1","20","-2","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("53","1","35","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("54","1","54","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("55","1","40","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("56","1","41","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("57","2","52","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("58","2","13","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("59","2","19","-2","0","correccion");
INSERT INTO `inventory_adjustment_items` VALUES("60","2","1","-7","0","correccion");
INSERT INTO `inventory_adjustment_items` VALUES("61","2","22","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("62","2","48","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("63","2","55","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("64","2","56","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("65","2","51","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("66","2","23","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("67","2","24","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("68","2","25","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("69","2","26","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("70","2","27","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("71","2","43","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("72","2","42","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("73","2","8","2","0","correccion");
INSERT INTO `inventory_adjustment_items` VALUES("74","2","36","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("75","2","2","1","0","correccion");
INSERT INTO `inventory_adjustment_items` VALUES("76","2","4","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("77","2","3","-1","0","correccion");
INSERT INTO `inventory_adjustment_items` VALUES("78","2","5","-11","0","correccion");
INSERT INTO `inventory_adjustment_items` VALUES("79","2","28","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("80","2","29","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("81","2","30","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("82","2","31","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("83","2","21","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("84","2","6","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("85","2","53","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("86","2","49","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("87","2","7","-2","0","correccion");
INSERT INTO `inventory_adjustment_items` VALUES("88","2","9","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("89","2","14","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("90","2","46","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("91","2","45","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("92","2","15","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("93","2","16","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("94","2","17","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("95","2","18","-1","0","correccion");
INSERT INTO `inventory_adjustment_items` VALUES("96","2","38","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("97","2","39","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("98","2","47","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("99","2","37","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("100","2","10","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("101","2","11","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("102","2","12","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("103","2","32","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("104","2","57","1","0","correccion");
INSERT INTO `inventory_adjustment_items` VALUES("105","2","33","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("106","2","34","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("107","2","44","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("108","2","50","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("109","2","20","-2","0","correccion");
INSERT INTO `inventory_adjustment_items` VALUES("110","2","35","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("111","2","54","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("112","2","40","0","0","dañado");
INSERT INTO `inventory_adjustment_items` VALUES("113","2","41","0","0","dañado");



DROP TABLE IF EXISTS `inventory_adjustments`;


CREATE TABLE `inventory_adjustments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `inventory_adjustments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `inventory_adjustments` VALUES("1","2024-08-21 15:27:42","1","Correcciones");
INSERT INTO `inventory_adjustments` VALUES("2","2024-08-24 09:37:41","1","Correccion");



DROP TABLE IF EXISTS `payment_distributions`;


CREATE TABLE `payment_distributions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `sale_id` (`sale_id`),
  CONSTRAINT `payment_distributions_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_distributions_ibfk_2` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `payments`;


CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('efectivo','tarjeta','transferencia','otros') NOT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `payments` VALUES("1","4","115000.00","2024-08-19","efectivo","");
INSERT INTO `payments` VALUES("2","10","8000.00","2024-08-23","efectivo","Pagado");
INSERT INTO `payments` VALUES("3","8","6000.00","2024-08-23","efectivo","Pagado");



DROP TABLE IF EXISTS `permissions`;


CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8;

INSERT INTO `permissions` VALUES("1","users_view","Ver usuarios");
INSERT INTO `permissions` VALUES("2","users_create","Crear usuarios");
INSERT INTO `permissions` VALUES("3","users_edit","Editar usuarios");
INSERT INTO `permissions` VALUES("4","users_delete","Eliminar usuarios");
INSERT INTO `permissions` VALUES("5","products_view","Ver productos");
INSERT INTO `permissions` VALUES("6","products_create","Crear productos");
INSERT INTO `permissions` VALUES("7","products_edit","Editar productos");
INSERT INTO `permissions` VALUES("8","products_delete","Eliminar productos");
INSERT INTO `permissions` VALUES("9","categories_view","Ver categorías");
INSERT INTO `permissions` VALUES("10","categories_create","Crear categorías");
INSERT INTO `permissions` VALUES("11","categories_edit","Editar categorías");
INSERT INTO `permissions` VALUES("12","categories_delete","Eliminar categorías");
INSERT INTO `permissions` VALUES("13","sales_view","Ver ventas");
INSERT INTO `permissions` VALUES("14","sales_create","Crear ventas");
INSERT INTO `permissions` VALUES("15","sales_edit","Editar ventas");
INSERT INTO `permissions` VALUES("16","sales_cancel","Cancelar ventas");
INSERT INTO `permissions` VALUES("17","reports_view","Ver reportes");
INSERT INTO `permissions` VALUES("18","reports_generate","Generar reportes");
INSERT INTO `permissions` VALUES("19","roles_manage","Gestionar roles y permisos");
INSERT INTO `permissions` VALUES("20","suppliers_view","Ver proveedores");
INSERT INTO `permissions` VALUES("21","suppliers_create","Crear proveedores");
INSERT INTO `permissions` VALUES("22","suppliers_edit","Editar proveedores");
INSERT INTO `permissions` VALUES("23","suppliers_delete","Eliminar proveedores");
INSERT INTO `permissions` VALUES("24","customers_view","Ver clientes");
INSERT INTO `permissions` VALUES("25","customers_create","Crear clientes");
INSERT INTO `permissions` VALUES("26","customers_edit","Editar clientes");
INSERT INTO `permissions` VALUES("27","customers_delete","Eliminar clientes");
INSERT INTO `permissions` VALUES("28","reservations_view","Ver reservas");
INSERT INTO `permissions` VALUES("29","reservations_create","Crear reservas");
INSERT INTO `permissions` VALUES("30","reservations_edit","Editar reservas");
INSERT INTO `permissions` VALUES("31","reservations_delete","Eliminar reservas");
INSERT INTO `permissions` VALUES("32","reservations_confirm","Confirmar reservas");
INSERT INTO `permissions` VALUES("33","reservations_cancel","Cancelar reservas");
INSERT INTO `permissions` VALUES("34","reservations_convert","Convertir reservas a ventas");
INSERT INTO `permissions` VALUES("35","promotions_view","Ver promociones");
INSERT INTO `permissions` VALUES("36","promotions_create","Crear promociones");
INSERT INTO `permissions` VALUES("37","promotions_edit","Editar promociones");
INSERT INTO `permissions` VALUES("38","promotions_delete","Eliminar promociones");
INSERT INTO `permissions` VALUES("39","promotions_apply","Aplicar promociones");
INSERT INTO `permissions` VALUES("40","inventory_view","Ver inventario");
INSERT INTO `permissions` VALUES("41","inventory_view_movements","Ver Movimientos de Inventario");
INSERT INTO `permissions` VALUES("42","inventory_view_low_stock","Ver Inventario Bajo Stock");
INSERT INTO `permissions` VALUES("43","inventory_update","Actualizar inventario");
INSERT INTO `permissions` VALUES("44","inventory_adjust","Ajustar inventario");
INSERT INTO `permissions` VALUES("45","purchases_view","Ver compras");
INSERT INTO `permissions` VALUES("46","purchases_create","Crear compras");
INSERT INTO `permissions` VALUES("47","purchases_edit","Editar compras");
INSERT INTO `permissions` VALUES("48","purchases_delete","Eliminar compras");
INSERT INTO `permissions` VALUES("49","purchases_receive","Recibir compras");
INSERT INTO `permissions` VALUES("50","purchases_view_movements","Ver movimientos de compras");
INSERT INTO `permissions` VALUES("51","settings_view","Ver configuración del sistema");
INSERT INTO `permissions` VALUES("52","settings_edit","Editar configuración del sistema");
INSERT INTO `permissions` VALUES("53","company_view","Ver Configración de la Empresa");
INSERT INTO `permissions` VALUES("54","audit_view","Ver registros de auditoría");
INSERT INTO `permissions` VALUES("55","backup_create","Crear copias de seguridad");
INSERT INTO `permissions` VALUES("56","backup_restore","Restaurar copias de seguridad");
INSERT INTO `permissions` VALUES("57","backup_delete","Eliminar copias de seguridad");
INSERT INTO `permissions` VALUES("58","backup_download","Descargar copias de seguridad");
INSERT INTO `permissions` VALUES("59","budget_view","Ver presupuestos");
INSERT INTO `permissions` VALUES("60","budget_create","Crear presupuestos");
INSERT INTO `permissions` VALUES("61","budget_edit","Editar presupuestos");
INSERT INTO `permissions` VALUES("62","budget_delete","Eliminar Presupuestos");
INSERT INTO `permissions` VALUES("63","cash_register_manage","Gestionar caja (abrir, cerrar, ver movimientos)");
INSERT INTO `permissions` VALUES("64","cash_register_open","Abrir caja");
INSERT INTO `permissions` VALUES("65","cash_register_close","Cerrar caja");
INSERT INTO `permissions` VALUES("66","cash_register_movement","Registrar movimientos de caja");
INSERT INTO `permissions` VALUES("67","customer_accounts_view","Ver cuentas de clientes");
INSERT INTO `permissions` VALUES("68","customer_accounts_adjust","Ajustar cuentas de clientes");
INSERT INTO `permissions` VALUES("69","payments_view","Ver pagos");
INSERT INTO `permissions` VALUES("70","payments_create","Crear pagos");
INSERT INTO `permissions` VALUES("71","payments_edit","Editar pagos");
INSERT INTO `permissions` VALUES("72","payments_delete","Eliminar pagos");
INSERT INTO `permissions` VALUES("73","services_view","Ver órdenes de servicio");
INSERT INTO `permissions` VALUES("74","services_create","Crear órdenes de servicio");
INSERT INTO `permissions` VALUES("75","services_edit","Editar órdenes de servicio");
INSERT INTO `permissions` VALUES("76","services_delete","Eliminar órdenes de servicio");
INSERT INTO `permissions` VALUES("77","services_update_status","Actualizar estado de órdenes de servicio");
INSERT INTO `permissions` VALUES("78","home_visits_view","Ver visitas a domicilio");
INSERT INTO `permissions` VALUES("79","home_visits_create","Crear visitas a domicilio");
INSERT INTO `permissions` VALUES("80","home_visits_edit","Editar visitas a domicilio");
INSERT INTO `permissions` VALUES("81","home_visits_delete","Eliminar visitas a domicilio");
INSERT INTO `permissions` VALUES("82","budget_change_status","Cambiar estado del Presupuesto");
INSERT INTO `permissions` VALUES("83","calendar_view","Ver calendario");
INSERT INTO `permissions` VALUES("84","company_settings_view","Configurar Datos Empresa");



DROP TABLE IF EXISTS `products`;


CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `sku` varchar(50) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT '0',
  `min_stock` int(11) NOT NULL DEFAULT '5',
  `max_stock` int(11) NOT NULL DEFAULT '100',
  `reorder_level` int(11) NOT NULL DEFAULT '10',
  `supplier_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `category_id` (`category_id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8;

INSERT INTO `products` VALUES("1","Asistencia 2","Asistencia general.","000022","6","2000.00","0.00","0","10","200","50","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("2","Cambio Modulo","Mano de obra cambio de módulos","00020","6","60000.00","21000.00","0","5","100","2","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("3","Cambio Pin de Carga Tipo V8","Mano de Obra cambio pin de carga","00016","6","12000.00","4000.00","0","5","100","2","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("4","Cambio Pin de Carga Tipo C","Mano de obra cambio pin de carga","00017","6","14000.00","6000.00","0","5","100","2","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("5","Carga Saldo","Carga Personal-Claro","00018","3","1000.00","100.00","-2","5","100","50","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("6","Celular Reacondicionado","Celular reacondicionado con garantía","00010","13","100000.00","0.00","0","5","100","2","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("7","Chip Personal/Claro","Chip prepago Personal/Claro","00011","14","1000.00","0.00","0","5","100","2","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("8","Cámara Domo Exterior Motorizado","Cámara domo motorizado exterior","00012","9","85000.00","42000.00","0","5","100","1","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("9","Desbloqueo Netbook","Mano de obra desbloqueo Netbook","00013","6","20000.00","9000.00","0","5","100","2","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("10","Instalación Cámaras","Mano de Obra Instalación","00002","6","10000.00","0.00","0","5","100","2","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("11","Instalación S.O.","Mano de obra instalación Sistemas Operativos","00004","6","25000.00","0.00","0","5","100","2","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("12","Mano de Obra Instalación","Instalación de cámaras de seguridad puesta a punto.","34234","6","15000.00","0.00","0","5","100","5","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("13","Armado PC + Instalación Sistema Operativo","Mano de obra armado e instalación","231231231","6","25000.00","0.00","0","5","100","2","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("14","Diagnóstico TV","Desarmado testeo armado diagnóstico.","34332144444","6","5000.00","0.00","0","5","100","2","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("15","F4 Señal CF","F4 para consumidor final","67454665","3","25000.00","0.00","0","5","100","3","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("16","F4 Señal Técnicos","F4 para técnicos","43534537","3","20000.00","0.00","0","5","100","3","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("17","FRP CF","FRP para consumidor final","87978","3","15000.00","0.00","0","5","100","3","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("18","FRP Técnicos","FRP para técnicos","67467","3","8000.00","0.00","0","5","100","3","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("19","Asistencia 1","Asistencia","0000011","6","1000.00","0.00","0","5","100","5","1","2024-08-17 11:58:49");
INSERT INTO `products` VALUES("20","Protector Glass","Glass protector templado","213125432","2","3000.00","600.00","35","5","100","10","2","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("21","Cargador Turbo Tipo V8","Cargador turbo V8","0723540565661","1","5000.00","3000.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("22","Auricular Manos Libres","Tipo Samsung","65675","8","3500.00","2000.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("23","Cabezal Cargador Turbo","Cabezal cargador conector tipo C","7790839914267","1","4000.00","2000.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("24","Cable Auxiliar","Cable tipo auxiliar","00021","7","3000.00","1000.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("25","Cable Iphone Tipo C ","Cable para Iphone 1ra calidad","190198914491","7","5000.00","2000.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("26","Cable USB Tipo C","Cable USB Tipo C 1ra Calidad","8945637653477","7","3500.00","2000.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("27","Cable USB tipo V8","Cable USB V8 1ra Calidad","7985461188968","7","3500.00","2000.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("28","Cargador 12v 1ra Calidad s/cables ","Cargador auto","0011","1","4500.00","2000.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("29","Cargador 12v c/cable Tipo C ","Cagador auto","7796350508602","1","5000.00","2000.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("30","Cargador 12v Samsung c/cable V8","Cargador auto","6958784248757","1","5000.00","2500.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("31","Cargador Turbo Tipo C","Cargador 1ra calidad turbo tipo C","0723540566286","1","5500.00","2500.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("32","Micro SD 32gb","Micro SD 1ra calidad","00005","10","15000.00","7000.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("33","OTG Tipo C","Conector OTG","6983646411987","7","3000.00","1500.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("34","OTG Tipo V8","Conector OTG","7893646417898","7","3000.00","1500.00","0","5","100","2","8","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("35","Protector TPU Varios","Protectores para celulares","00006","14","6000.00","3000.00","0","5","100","2","2","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("36","Camara Seguridad Hikvision","Full Hd 1080p 16d0t-exipf Ext 2.8","8078686","9","41500.00","34000.00","0","5","100","2","9","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("37","Grabadora Dvr Hikvision 8 Canales","8can+2IP Turbo Hd 720p","35635638","9","110000.00","90000.00","0","5","100","1","9","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("38","Fuente 12v 2a","Fuente Alimentacion Camaras Cctv Dvr","231243","1","7500.00","3500.00","0","5","100","2","9","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("39","Fuente 12v 4a","Alimentacion Camaras Cctv Dvr","23423","1","9000.00","4500.00","0","5","100","2","9","2024-08-17 12:11:01");
INSERT INTO `products` VALUES("40","Splitter Pulpo Alimentación 1x4","1x4 Cctv Cámaras","2321312","7","3000.00","1500.00","0","5","100","2","9","2024-08-17 12:11:02");
INSERT INTO `products` VALUES("41","Splitter Pulpo Alimentación 1x8","1x8 Cctv Cámaras","34425234","7","6000.00","2000.00","0","5","100","2","9","2024-08-17 12:11:02");
INSERT INTO `products` VALUES("42","Cable UTP 50m exterior","50m UTP cat5","344534","7","30000.00","15000.00","0","5","100","2","9","2024-08-17 12:11:02");
INSERT INTO `products` VALUES("43","Cable UTP 100m exterior","100m UTP cat5","34132412","7","53000.00","30000.00","0","5","100","2","9","2024-08-17 12:11:02");
INSERT INTO `products` VALUES("44","Pack Par Balun + Plug Macho + Hembra","Conector video calidad HD","341231","7","4500.00","2500.00","0","5","100","5","9","2024-08-17 12:11:02");
INSERT INTO `products` VALUES("45","Estanca 80x80x50","Caja Plastica De Paso","234214","14","5000.00","2500.00","0","5","100","2","9","2024-08-17 12:11:02");
INSERT INTO `products` VALUES("46","Disco duro 1TB","Para DVR / PC","342342","10","95000.00","70000.00","0","5","100","1","9","2024-08-17 12:11:02");
INSERT INTO `products` VALUES("47","Fuente 12v 5a","Homologada Certificada para CCTV","324132","1","12000.00","6000.00","0","5","100","1","9","2024-08-17 12:11:02");
INSERT INTO `products` VALUES("48","Auricular Samsung","Auricular Samsung 1ra calidad en caja","2312312","8","5000.00","3500.00","0","5","100","3","8","2024-08-17 12:11:02");
INSERT INTO `products` VALUES("49","Celular Samsung J2 Prime Reacondicionado","celular reacondicionado con garantía","21312413","13","60000.00","0.00","0","5","100","2","1","2024-08-17 14:48:53");
INSERT INTO `products` VALUES("50","Placa de carga","Cambio placa de carga","3564","16","15000.00","5000.00","0","5","100","2","4","2024-08-17 15:07:20");
INSERT INTO `products` VALUES("51","Batería Samsung","Baterias Originales","23621967902178","15","18000.00","8600.00","0","5","100","2","7","2024-08-17 18:55:55");
INSERT INTO `products` VALUES("52","Android TV Box","Android TV Box configurados","453246247","14","70000.00","35000.00","0","5","100","1","8","2024-08-17 19:21:15");
INSERT INTO `products` VALUES("53","Celular Samsung A12 Reacondicionado","Celular reacondicionado con garantía","3471893743\'12","13","80000.00","0.00","0","5","100","0","1","2024-08-17 19:22:45");
INSERT INTO `products` VALUES("54","Samsung A50 Reacondicionado","Celular Samsung A5o reacondicionado con garantía","327612936w01","13","100000.00","0.00","0","5","100","0","1","2024-08-17 22:44:18");
INSERT INTO `products` VALUES("55","Bandeja Porta Sim","Bandeja porta SIM varios modelos","34723842080","14","8000.00","0.00","0","5","100","2","4","2024-08-19 16:24:06");
INSERT INTO `products` VALUES("56","Batería Motorola","Baterias Originales","2352376875","15","25000.00","8600.00","0","5","100","2","7","2024-08-21 12:09:21");
INSERT INTO `products` VALUES("57","Modulo Samsung","Modulos originales 1ra calidad.","20000","11","25000.00","20000.00","0","5","100","1","3","2024-08-21 20:30:09");
INSERT INTO `products` VALUES("58","Módulo Motorola","Módulos originales","23424523423","11","50000.00","20000.00","2","5","100","1","6","2024-08-26 11:02:56");
INSERT INTO `products` VALUES("59","Bateria Alcatel","Bateria Original","52362562","15","28000.00","15000.00","0","5","100","2","1","2024-08-26 11:42:47");



DROP TABLE IF EXISTS `promotions`;


CREATE TABLE `promotions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `discount_type` enum('porcentaje','fijo') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `promotions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `purchase_items`;


CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `received_quantity` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO `purchase_items` VALUES("2","1","51","2","7600.00","2");
INSERT INTO `purchase_items` VALUES("3","2","5","20","100.00","20");
INSERT INTO `purchase_items` VALUES("4","3","2","1","18000.00","1");
INSERT INTO `purchase_items` VALUES("5","4","8","1","42000.00","1");
INSERT INTO `purchase_items` VALUES("6","5","2","1","20000.00","1");
INSERT INTO `purchase_items` VALUES("7","6","56","1","8600.00","1");
INSERT INTO `purchase_items` VALUES("8","7","2","1","21000.00","1");
INSERT INTO `purchase_items` VALUES("9","8","57","1","20000.00","1");
INSERT INTO `purchase_items` VALUES("10","9","20","35","0.00","35");
INSERT INTO `purchase_items` VALUES("11","10","58","1","18600.00","1");



DROP TABLE IF EXISTS `purchase_movements`;


CREATE TABLE `purchase_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movement_type` enum('creacion','recepcion','cancelacion','modificacion') NOT NULL,
  `details` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `purchase_movements_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_movements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

INSERT INTO `purchase_movements` VALUES("2","1","1","creacion","Compra creada","2024-08-17 19:08:05");
INSERT INTO `purchase_movements` VALUES("3","1","1","recepcion","Compra recibida","2024-08-17 19:08:11");
INSERT INTO `purchase_movements` VALUES("4","2","1","creacion","Compra creada","2024-08-17 19:08:43");
INSERT INTO `purchase_movements` VALUES("5","2","1","recepcion","Compra recibida","2024-08-17 19:08:50");
INSERT INTO `purchase_movements` VALUES("6","3","1","creacion","Compra creada","2024-08-19 12:38:10");
INSERT INTO `purchase_movements` VALUES("7","3","1","recepcion","Compra recibida","2024-08-19 12:38:50");
INSERT INTO `purchase_movements` VALUES("8","4","1","creacion","Compra creada","2024-08-19 20:20:50");
INSERT INTO `purchase_movements` VALUES("9","4","1","recepcion","Compra recibida","2024-08-19 20:20:56");
INSERT INTO `purchase_movements` VALUES("10","5","1","creacion","Compra creada","2024-08-19 20:22:27");
INSERT INTO `purchase_movements` VALUES("11","5","1","recepcion","Compra recibida","2024-08-19 20:22:30");
INSERT INTO `purchase_movements` VALUES("12","6","1","creacion","Compra creada","2024-08-21 12:09:42");
INSERT INTO `purchase_movements` VALUES("13","7","1","creacion","Compra creada","2024-08-21 12:10:05");
INSERT INTO `purchase_movements` VALUES("14","8","1","creacion","Compra creada","2024-08-21 20:33:14");
INSERT INTO `purchase_movements` VALUES("15","6","1","recepcion","Compra recibida","2024-08-21 20:33:17");
INSERT INTO `purchase_movements` VALUES("16","7","1","recepcion","Compra recibida","2024-08-21 20:33:23");
INSERT INTO `purchase_movements` VALUES("17","8","1","recepcion","Compra recibida","2024-08-21 20:33:29");
INSERT INTO `purchase_movements` VALUES("18","9","1","creacion","Compra creada","2024-08-24 10:33:24");
INSERT INTO `purchase_movements` VALUES("19","9","1","recepcion","Compra recibida. Total actualizado: $0.00","2024-08-24 10:33:31");
INSERT INTO `purchase_movements` VALUES("20","10","1","creacion","Compra creada","2024-08-26 11:18:08");
INSERT INTO `purchase_movements` VALUES("21","10","1","recepcion","Compra recibida. Total actualizado: $18,600.00","2024-08-26 11:18:14");



DROP TABLE IF EXISTS `purchases`;


CREATE TABLE `purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `purchase_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pendiente','recibido','cancelado') DEFAULT 'pendiente',
  `received_date` timestamp NULL DEFAULT NULL,
  `cash_register_session_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `user_id` (`user_id`),
  KEY `purchases_cash_register_session_fk` (`cash_register_session_id`),
  CONSTRAINT `purchases_cash_register_session_fk` FOREIGN KEY (`cash_register_session_id`) REFERENCES `cash_register_sessions` (`id`),
  CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `purchases_ibfk_3` FOREIGN KEY (`cash_register_session_id`) REFERENCES `cash_register_sessions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

INSERT INTO `purchases` VALUES("1","7","1","2024-08-12 05:08:05","15200.00","recibido","2024-08-12 05:09:35","1");
INSERT INTO `purchases` VALUES("2","10","1","2024-08-12 05:08:43","2000.00","recibido","2024-08-12 05:08:50","1");
INSERT INTO `purchases` VALUES("3","3","1","2024-08-19 12:38:10","18000.00","recibido","2024-08-19 12:38:50","7");
INSERT INTO `purchases` VALUES("4","9","1","2024-08-19 20:20:50","42000.00","recibido","2024-08-19 20:20:56","7");
INSERT INTO `purchases` VALUES("5","3","1","2024-08-19 20:22:27","20000.00","recibido","2024-08-19 20:22:30","7");
INSERT INTO `purchases` VALUES("6","7","1","2024-08-21 12:09:42","8600.00","recibido","2024-08-21 20:33:17","9");
INSERT INTO `purchases` VALUES("7","6","1","2024-08-21 12:10:05","21000.00","recibido","2024-08-21 20:33:23","9");
INSERT INTO `purchases` VALUES("8","3","1","2024-08-21 20:33:14","20000.00","recibido","2024-08-21 20:33:29","9");
INSERT INTO `purchases` VALUES("9","2","1","2024-08-24 10:33:24","0.00","recibido","2024-08-24 10:33:31","12");
INSERT INTO `purchases` VALUES("10","6","1","2024-08-26 11:18:08","18600.00","recibido","2024-08-26 11:18:14","13");



DROP TABLE IF EXISTS `reservation_items`;


CREATE TABLE `reservation_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reservation_id` (`reservation_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `reservation_items_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservation_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `reservations`;


CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `reservation_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pendiente','confirmado','cancelado','convertido') DEFAULT 'pendiente',
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `role_permissions`;


CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `role_permissions` VALUES("1","1");
INSERT INTO `role_permissions` VALUES("2","1");
INSERT INTO `role_permissions` VALUES("5","1");
INSERT INTO `role_permissions` VALUES("1","2");
INSERT INTO `role_permissions` VALUES("2","2");
INSERT INTO `role_permissions` VALUES("5","2");
INSERT INTO `role_permissions` VALUES("1","3");
INSERT INTO `role_permissions` VALUES("2","3");
INSERT INTO `role_permissions` VALUES("5","3");
INSERT INTO `role_permissions` VALUES("1","4");
INSERT INTO `role_permissions` VALUES("2","4");
INSERT INTO `role_permissions` VALUES("1","5");
INSERT INTO `role_permissions` VALUES("2","5");
INSERT INTO `role_permissions` VALUES("4","5");
INSERT INTO `role_permissions` VALUES("5","5");
INSERT INTO `role_permissions` VALUES("1","6");
INSERT INTO `role_permissions` VALUES("2","6");
INSERT INTO `role_permissions` VALUES("4","6");
INSERT INTO `role_permissions` VALUES("5","6");
INSERT INTO `role_permissions` VALUES("1","7");
INSERT INTO `role_permissions` VALUES("2","7");
INSERT INTO `role_permissions` VALUES("4","7");
INSERT INTO `role_permissions` VALUES("5","7");
INSERT INTO `role_permissions` VALUES("1","8");
INSERT INTO `role_permissions` VALUES("1","9");
INSERT INTO `role_permissions` VALUES("2","9");
INSERT INTO `role_permissions` VALUES("4","9");
INSERT INTO `role_permissions` VALUES("5","9");
INSERT INTO `role_permissions` VALUES("1","10");
INSERT INTO `role_permissions` VALUES("2","10");
INSERT INTO `role_permissions` VALUES("4","10");
INSERT INTO `role_permissions` VALUES("5","10");
INSERT INTO `role_permissions` VALUES("1","11");
INSERT INTO `role_permissions` VALUES("2","11");
INSERT INTO `role_permissions` VALUES("4","11");
INSERT INTO `role_permissions` VALUES("5","11");
INSERT INTO `role_permissions` VALUES("1","12");
INSERT INTO `role_permissions` VALUES("1","13");
INSERT INTO `role_permissions` VALUES("2","13");
INSERT INTO `role_permissions` VALUES("4","13");
INSERT INTO `role_permissions` VALUES("5","13");
INSERT INTO `role_permissions` VALUES("1","14");
INSERT INTO `role_permissions` VALUES("2","14");
INSERT INTO `role_permissions` VALUES("4","14");
INSERT INTO `role_permissions` VALUES("5","14");
INSERT INTO `role_permissions` VALUES("1","15");
INSERT INTO `role_permissions` VALUES("2","15");
INSERT INTO `role_permissions` VALUES("4","15");
INSERT INTO `role_permissions` VALUES("5","15");
INSERT INTO `role_permissions` VALUES("1","16");
INSERT INTO `role_permissions` VALUES("2","16");
INSERT INTO `role_permissions` VALUES("1","17");
INSERT INTO `role_permissions` VALUES("2","17");
INSERT INTO `role_permissions` VALUES("5","17");
INSERT INTO `role_permissions` VALUES("1","18");
INSERT INTO `role_permissions` VALUES("2","18");
INSERT INTO `role_permissions` VALUES("5","18");
INSERT INTO `role_permissions` VALUES("1","19");
INSERT INTO `role_permissions` VALUES("1","20");
INSERT INTO `role_permissions` VALUES("2","20");
INSERT INTO `role_permissions` VALUES("4","20");
INSERT INTO `role_permissions` VALUES("5","20");
INSERT INTO `role_permissions` VALUES("1","21");
INSERT INTO `role_permissions` VALUES("2","21");
INSERT INTO `role_permissions` VALUES("4","21");
INSERT INTO `role_permissions` VALUES("5","21");
INSERT INTO `role_permissions` VALUES("1","22");
INSERT INTO `role_permissions` VALUES("2","22");
INSERT INTO `role_permissions` VALUES("4","22");
INSERT INTO `role_permissions` VALUES("5","22");
INSERT INTO `role_permissions` VALUES("1","23");
INSERT INTO `role_permissions` VALUES("1","24");
INSERT INTO `role_permissions` VALUES("2","24");
INSERT INTO `role_permissions` VALUES("4","24");
INSERT INTO `role_permissions` VALUES("5","24");
INSERT INTO `role_permissions` VALUES("1","25");
INSERT INTO `role_permissions` VALUES("2","25");
INSERT INTO `role_permissions` VALUES("4","25");
INSERT INTO `role_permissions` VALUES("5","25");
INSERT INTO `role_permissions` VALUES("1","26");
INSERT INTO `role_permissions` VALUES("2","26");
INSERT INTO `role_permissions` VALUES("4","26");
INSERT INTO `role_permissions` VALUES("1","27");
INSERT INTO `role_permissions` VALUES("1","28");
INSERT INTO `role_permissions` VALUES("2","28");
INSERT INTO `role_permissions` VALUES("4","28");
INSERT INTO `role_permissions` VALUES("5","28");
INSERT INTO `role_permissions` VALUES("1","29");
INSERT INTO `role_permissions` VALUES("2","29");
INSERT INTO `role_permissions` VALUES("4","29");
INSERT INTO `role_permissions` VALUES("5","29");
INSERT INTO `role_permissions` VALUES("1","30");
INSERT INTO `role_permissions` VALUES("2","30");
INSERT INTO `role_permissions` VALUES("4","30");
INSERT INTO `role_permissions` VALUES("5","30");
INSERT INTO `role_permissions` VALUES("1","31");
INSERT INTO `role_permissions` VALUES("2","31");
INSERT INTO `role_permissions` VALUES("5","31");
INSERT INTO `role_permissions` VALUES("1","32");
INSERT INTO `role_permissions` VALUES("2","32");
INSERT INTO `role_permissions` VALUES("4","32");
INSERT INTO `role_permissions` VALUES("5","32");
INSERT INTO `role_permissions` VALUES("1","33");
INSERT INTO `role_permissions` VALUES("2","33");
INSERT INTO `role_permissions` VALUES("4","33");
INSERT INTO `role_permissions` VALUES("5","33");
INSERT INTO `role_permissions` VALUES("1","34");
INSERT INTO `role_permissions` VALUES("2","34");
INSERT INTO `role_permissions` VALUES("4","34");
INSERT INTO `role_permissions` VALUES("5","34");
INSERT INTO `role_permissions` VALUES("1","35");
INSERT INTO `role_permissions` VALUES("1","36");
INSERT INTO `role_permissions` VALUES("1","37");
INSERT INTO `role_permissions` VALUES("1","38");
INSERT INTO `role_permissions` VALUES("1","39");
INSERT INTO `role_permissions` VALUES("1","40");
INSERT INTO `role_permissions` VALUES("2","40");
INSERT INTO `role_permissions` VALUES("4","40");
INSERT INTO `role_permissions` VALUES("5","40");
INSERT INTO `role_permissions` VALUES("1","41");
INSERT INTO `role_permissions` VALUES("1","42");
INSERT INTO `role_permissions` VALUES("1","43");
INSERT INTO `role_permissions` VALUES("1","44");
INSERT INTO `role_permissions` VALUES("1","45");
INSERT INTO `role_permissions` VALUES("2","45");
INSERT INTO `role_permissions` VALUES("5","45");
INSERT INTO `role_permissions` VALUES("1","46");
INSERT INTO `role_permissions` VALUES("2","46");
INSERT INTO `role_permissions` VALUES("5","46");
INSERT INTO `role_permissions` VALUES("1","47");
INSERT INTO `role_permissions` VALUES("2","47");
INSERT INTO `role_permissions` VALUES("5","47");
INSERT INTO `role_permissions` VALUES("1","48");
INSERT INTO `role_permissions` VALUES("1","49");
INSERT INTO `role_permissions` VALUES("2","49");
INSERT INTO `role_permissions` VALUES("5","49");
INSERT INTO `role_permissions` VALUES("1","50");
INSERT INTO `role_permissions` VALUES("1","51");
INSERT INTO `role_permissions` VALUES("1","52");
INSERT INTO `role_permissions` VALUES("1","54");
INSERT INTO `role_permissions` VALUES("1","55");
INSERT INTO `role_permissions` VALUES("2","55");
INSERT INTO `role_permissions` VALUES("5","55");
INSERT INTO `role_permissions` VALUES("1","56");
INSERT INTO `role_permissions` VALUES("1","57");
INSERT INTO `role_permissions` VALUES("1","58");
INSERT INTO `role_permissions` VALUES("1","59");
INSERT INTO `role_permissions` VALUES("2","59");
INSERT INTO `role_permissions` VALUES("4","59");
INSERT INTO `role_permissions` VALUES("1","60");
INSERT INTO `role_permissions` VALUES("2","60");
INSERT INTO `role_permissions` VALUES("4","60");
INSERT INTO `role_permissions` VALUES("1","61");
INSERT INTO `role_permissions` VALUES("2","61");
INSERT INTO `role_permissions` VALUES("4","61");
INSERT INTO `role_permissions` VALUES("1","62");
INSERT INTO `role_permissions` VALUES("2","62");
INSERT INTO `role_permissions` VALUES("1","63");
INSERT INTO `role_permissions` VALUES("2","63");
INSERT INTO `role_permissions` VALUES("4","63");
INSERT INTO `role_permissions` VALUES("1","64");
INSERT INTO `role_permissions` VALUES("2","64");
INSERT INTO `role_permissions` VALUES("3","64");
INSERT INTO `role_permissions` VALUES("4","64");
INSERT INTO `role_permissions` VALUES("1","65");
INSERT INTO `role_permissions` VALUES("2","65");
INSERT INTO `role_permissions` VALUES("3","65");
INSERT INTO `role_permissions` VALUES("4","65");
INSERT INTO `role_permissions` VALUES("1","66");
INSERT INTO `role_permissions` VALUES("2","66");
INSERT INTO `role_permissions` VALUES("3","66");
INSERT INTO `role_permissions` VALUES("4","66");
INSERT INTO `role_permissions` VALUES("1","67");
INSERT INTO `role_permissions` VALUES("2","67");
INSERT INTO `role_permissions` VALUES("4","67");
INSERT INTO `role_permissions` VALUES("1","68");
INSERT INTO `role_permissions` VALUES("2","68");
INSERT INTO `role_permissions` VALUES("4","68");
INSERT INTO `role_permissions` VALUES("1","69");
INSERT INTO `role_permissions` VALUES("2","69");
INSERT INTO `role_permissions` VALUES("4","69");
INSERT INTO `role_permissions` VALUES("1","70");
INSERT INTO `role_permissions` VALUES("2","70");
INSERT INTO `role_permissions` VALUES("4","70");
INSERT INTO `role_permissions` VALUES("1","71");
INSERT INTO `role_permissions` VALUES("2","71");
INSERT INTO `role_permissions` VALUES("4","71");
INSERT INTO `role_permissions` VALUES("1","72");
INSERT INTO `role_permissions` VALUES("1","73");
INSERT INTO `role_permissions` VALUES("2","73");
INSERT INTO `role_permissions` VALUES("1","74");
INSERT INTO `role_permissions` VALUES("2","74");
INSERT INTO `role_permissions` VALUES("1","75");
INSERT INTO `role_permissions` VALUES("2","75");
INSERT INTO `role_permissions` VALUES("1","76");
INSERT INTO `role_permissions` VALUES("2","76");
INSERT INTO `role_permissions` VALUES("1","77");
INSERT INTO `role_permissions` VALUES("2","77");
INSERT INTO `role_permissions` VALUES("1","78");
INSERT INTO `role_permissions` VALUES("1","79");
INSERT INTO `role_permissions` VALUES("1","80");
INSERT INTO `role_permissions` VALUES("1","81");
INSERT INTO `role_permissions` VALUES("1","82");
INSERT INTO `role_permissions` VALUES("1","83");
INSERT INTO `role_permissions` VALUES("1","84");



DROP TABLE IF EXISTS `roles`;


CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `roles` VALUES("1","Administrador");
INSERT INTO `roles` VALUES("3","Cajero");
INSERT INTO `roles` VALUES("2","Gerente");
INSERT INTO `roles` VALUES("5","Técnico-Vendedor");
INSERT INTO `roles` VALUES("4","Vendedor");



DROP TABLE IF EXISTS `sale_items`;


CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;

INSERT INTO `sale_items` VALUES("1","1","5","1","1000.00");
INSERT INTO `sale_items` VALUES("2","2","7","2","500.00");
INSERT INTO `sale_items` VALUES("3","3","5","2","1000.00");
INSERT INTO `sale_items` VALUES("4","4","49","1","60000.00");
INSERT INTO `sale_items` VALUES("6","6","3","1","12000.00");
INSERT INTO `sale_items` VALUES("7","7","50","1","15000.00");
INSERT INTO `sale_items` VALUES("8","8","22","1","3500.00");
INSERT INTO `sale_items` VALUES("9","9","52","1","70000.00");
INSERT INTO `sale_items` VALUES("10","10","27","1","3500.00");
INSERT INTO `sale_items` VALUES("11","11","53","1","80000.00");
INSERT INTO `sale_items` VALUES("12","12","21","1","5000.00");
INSERT INTO `sale_items` VALUES("13","13","5","1","1000.00");
INSERT INTO `sale_items` VALUES("14","14","7","2","500.00");
INSERT INTO `sale_items` VALUES("15","15","5","1","1000.00");
INSERT INTO `sale_items` VALUES("16","16","20","1","2500.00");
INSERT INTO `sale_items` VALUES("18","18","1","1","2000.00");
INSERT INTO `sale_items` VALUES("19","19","16","1","20000.00");
INSERT INTO `sale_items` VALUES("20","20","19","1","1000.00");
INSERT INTO `sale_items` VALUES("21","21","5","1","1000.00");
INSERT INTO `sale_items` VALUES("22","22","5","2","1000.00");
INSERT INTO `sale_items` VALUES("23","23","54","1","100000.00");
INSERT INTO `sale_items` VALUES("24","24","3","1","12000.00");
INSERT INTO `sale_items` VALUES("25","25","20","1","2500.00");
INSERT INTO `sale_items` VALUES("26","26","20","1","2500.00");
INSERT INTO `sale_items` VALUES("27","27","48","1","5000.00");
INSERT INTO `sale_items` VALUES("28","28","20","1","2500.00");
INSERT INTO `sale_items` VALUES("29","29","20","1","2500.00");
INSERT INTO `sale_items` VALUES("30","30","1","1","2000.00");
INSERT INTO `sale_items` VALUES("31","31","19","1","1000.00");
INSERT INTO `sale_items` VALUES("32","32","31","1","5500.00");
INSERT INTO `sale_items` VALUES("33","33","2","1","65000.00");
INSERT INTO `sale_items` VALUES("40","34","55","1","8000.00");
INSERT INTO `sale_items` VALUES("41","35","7","1","1000.00");
INSERT INTO `sale_items` VALUES("42","35","5","1","1000.00");
INSERT INTO `sale_items` VALUES("43","36","5","1","1000.00");
INSERT INTO `sale_items` VALUES("44","37","19","2","1000.00");
INSERT INTO `sale_items` VALUES("45","38","8","1","85000.00");
INSERT INTO `sale_items` VALUES("46","38","32","1","15000.00");
INSERT INTO `sale_items` VALUES("47","38","12","1","15000.00");
INSERT INTO `sale_items` VALUES("48","39","2","1","90000.00");
INSERT INTO `sale_items` VALUES("49","40","31","1","5500.00");
INSERT INTO `sale_items` VALUES("50","41","5","1","1000.00");
INSERT INTO `sale_items` VALUES("51","42","5","1","1000.00");
INSERT INTO `sale_items` VALUES("52","43","3","1","12000.00");
INSERT INTO `sale_items` VALUES("53","44","20","2","2500.00");
INSERT INTO `sale_items` VALUES("54","45","4","1","14000.00");
INSERT INTO `sale_items` VALUES("55","46","19","5","1000.00");
INSERT INTO `sale_items` VALUES("56","46","29","1","5000.00");
INSERT INTO `sale_items` VALUES("57","47","1","1","2000.00");
INSERT INTO `sale_items` VALUES("58","48","9","1","20000.00");
INSERT INTO `sale_items` VALUES("59","49","2","1","60000.00");
INSERT INTO `sale_items` VALUES("60","50","5","1","1000.00");
INSERT INTO `sale_items` VALUES("61","51","7","1","1000.00");
INSERT INTO `sale_items` VALUES("62","51","5","1","1000.00");
INSERT INTO `sale_items` VALUES("63","52","5","2","1000.00");
INSERT INTO `sale_items` VALUES("64","53","5","1","1000.00");
INSERT INTO `sale_items` VALUES("65","54","1","1","2000.00");
INSERT INTO `sale_items` VALUES("66","55","20","1","3000.00");
INSERT INTO `sale_items` VALUES("67","55","1","1","2000.00");
INSERT INTO `sale_items` VALUES("68","56","7","1","1000.00");
INSERT INTO `sale_items` VALUES("69","56","5","1","1000.00");
INSERT INTO `sale_items` VALUES("70","57","57","1","25000.00");
INSERT INTO `sale_items` VALUES("71","57","20","1","3000.00");
INSERT INTO `sale_items` VALUES("72","58","18","1","8000.00");
INSERT INTO `sale_items` VALUES("73","59","19","1","5000.00");
INSERT INTO `sale_items` VALUES("74","60","3","1","6000.00");
INSERT INTO `sale_items` VALUES("75","61","1","1","2000.00");
INSERT INTO `sale_items` VALUES("76","62","56","1","25000.00");
INSERT INTO `sale_items` VALUES("77","63","52","1","10000.00");
INSERT INTO `sale_items` VALUES("78","64","5","1","3300.00");
INSERT INTO `sale_items` VALUES("79","65","5","1","3300.00");
INSERT INTO `sale_items` VALUES("80","66","59","1","28000.00");



DROP TABLE IF EXISTS `sale_payments`;


CREATE TABLE `sale_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `payment_id` (`payment_id`),
  CONSTRAINT `sale_payments_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  CONSTRAINT `sale_payments_ibfk_2` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `sale_payments` VALUES("1","38","1","115000.00","2024-08-19 23:26:31");
INSERT INTO `sale_payments` VALUES("2","58","2","8000.00","2024-08-23 19:22:52");
INSERT INTO `sale_payments` VALUES("3","60","3","6000.00","2024-08-23 23:22:47");



DROP TABLE IF EXISTS `sales`;


CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `sale_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('efectivo','tarjeta','transferencia','credito','otros') NOT NULL,
  `status` enum('completado','cancelado') DEFAULT 'completado',
  `is_credit` tinyint(1) DEFAULT '0',
  `amount_paid` decimal(10,2) DEFAULT '0.00',
  `balance` decimal(10,2) DEFAULT '0.00',
  `cash_register_session_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `user_id` (`user_id`),
  KEY `sales_cash_register_session_fk` (`cash_register_session_id`),
  CONSTRAINT `sales_cash_register_session_fk` FOREIGN KEY (`cash_register_session_id`) REFERENCES `cash_register_sessions` (`id`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`cash_register_session_id`) REFERENCES `cash_register_sessions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

INSERT INTO `sales` VALUES("1","1","1","2024-08-12 14:41:48","3000.00","efectivo","completado","0","0.00","0.00","1");
INSERT INTO `sales` VALUES("2","1","1","2024-08-12 14:46:44","1000.00","efectivo","completado","0","0.00","0.00","1");
INSERT INTO `sales` VALUES("3","1","1","2024-08-12 14:47:03","2200.00","efectivo","completado","0","0.00","0.00","1");
INSERT INTO `sales` VALUES("4","1","1","2024-08-12 14:49:12","60000.00","efectivo","completado","0","0.00","0.00","1");
INSERT INTO `sales` VALUES("6","1","1","2024-08-12 15:04:50","12000.00","efectivo","completado","0","0.00","0.00","1");
INSERT INTO `sales` VALUES("7","1","1","2024-08-12 15:07:35","15000.00","efectivo","completado","0","0.00","0.00","1");
INSERT INTO `sales` VALUES("8","1","1","2024-08-13 07:36:03","3500.00","efectivo","completado","0","0.00","0.00","2");
INSERT INTO `sales` VALUES("9","1","1","2024-08-13 07:36:24","70000.00","efectivo","completado","0","0.00","0.00","2");
INSERT INTO `sales` VALUES("10","1","1","2024-08-13 08:36:32","3500.00","efectivo","completado","0","0.00","0.00","2");
INSERT INTO `sales` VALUES("11","1","1","2024-08-13 16:36:37","80000.00","efectivo","completado","0","0.00","0.00","2");
INSERT INTO `sales` VALUES("12","1","1","2024-08-14 05:06:26","5000.00","efectivo","completado","0","0.00","0.00","3");
INSERT INTO `sales` VALUES("13","1","1","2024-08-14 05:06:47","1000.00","efectivo","completado","0","0.00","0.00","3");
INSERT INTO `sales` VALUES("14","1","1","2024-08-14 06:07:15","1000.00","efectivo","completado","0","0.00","0.00","3");
INSERT INTO `sales` VALUES("15","1","1","2024-08-14 07:07:38","3000.00","efectivo","completado","0","0.00","0.00","3");
INSERT INTO `sales` VALUES("16","1","1","2024-08-15 08:23:29","2500.00","efectivo","completado","0","0.00","0.00","4");
INSERT INTO `sales` VALUES("18","1","1","2024-08-15 08:39:09","18000.00","efectivo","completado","0","0.00","0.00","4");
INSERT INTO `sales` VALUES("19","1","1","2024-08-16 08:41:44","20000.00","efectivo","completado","0","0.00","0.00","5");
INSERT INTO `sales` VALUES("20","1","1","2024-08-16 08:42:02","1000.00","efectivo","completado","0","0.00","0.00","5");
INSERT INTO `sales` VALUES("21","1","1","2024-08-16 09:42:27","3300.00","efectivo","completado","0","0.00","0.00","5");
INSERT INTO `sales` VALUES("22","1","1","2024-08-16 09:42:45","2200.00","efectivo","completado","0","0.00","0.00","5");
INSERT INTO `sales` VALUES("23","1","1","2024-08-16 10:50:20","100000.00","efectivo","completado","0","0.00","0.00","5");
INSERT INTO `sales` VALUES("24","1","1","2024-08-16 10:44:56","12000.00","efectivo","completado","0","0.00","0.00","5");
INSERT INTO `sales` VALUES("25","1","1","2024-08-16 11:50:32","2500.00","efectivo","completado","0","0.00","0.00","5");
INSERT INTO `sales` VALUES("26","1","1","2024-08-16 13:45:30","2500.00","efectivo","completado","0","0.00","0.00","5");
INSERT INTO `sales` VALUES("27","1","1","2024-08-16 14:45:45","5000.00","efectivo","completado","0","0.00","0.00","5");
INSERT INTO `sales` VALUES("28","1","1","2024-08-16 14:46:00","2500.00","efectivo","completado","0","0.00","0.00","5");
INSERT INTO `sales` VALUES("29","1","1","2024-08-16 14:46:10","2500.00","efectivo","completado","0","0.00","0.00","5");
INSERT INTO `sales` VALUES("30","1","1","2024-08-17 07:56:26","8000.00","efectivo","completado","0","0.00","0.00","6");
INSERT INTO `sales` VALUES("31","1","1","2024-08-17 08:54:58","1000.00","efectivo","completado","0","0.00","0.00","6");
INSERT INTO `sales` VALUES("32","1","1","2024-08-19 08:23:30","5500.00","efectivo","completado","0","0.00","0.00","7");
INSERT INTO `sales` VALUES("33","1","1","2024-08-19 08:25:50","65000.00","transferencia","completado","0","0.00","0.00","7");
INSERT INTO `sales` VALUES("34","1","1","2024-08-19 17:05:52","8000.00","efectivo","completado","0","0.00","0.00","7");
INSERT INTO `sales` VALUES("35","1","1","2024-08-19 18:09:55","4000.00","efectivo","completado","0","0.00","0.00","7");
INSERT INTO `sales` VALUES("36","1","1","2024-08-19 18:58:22","3300.00","efectivo","completado","0","0.00","0.00","7");
INSERT INTO `sales` VALUES("37","1","1","2024-08-19 19:16:55","1500.00","efectivo","completado","0","0.00","0.00","7");
INSERT INTO `sales` VALUES("38","4","1","2024-08-19 19:17:53","115000.00","efectivo","completado","0","0.00","0.00","7");
INSERT INTO `sales` VALUES("39","1","1","2024-08-19 19:30:15","90000.00","tarjeta","completado","0","0.00","0.00","7");
INSERT INTO `sales` VALUES("40","1","1","2024-08-20 10:09:40","5500.00","efectivo","completado","0","0.00","0.00","8");
INSERT INTO `sales` VALUES("41","1","1","2024-08-20 10:48:35","3300.00","efectivo","completado","0","0.00","0.00","8");
INSERT INTO `sales` VALUES("42","1","1","2024-08-20 10:53:31","2200.00","efectivo","completado","0","0.00","0.00","8");
INSERT INTO `sales` VALUES("43","1","1","2024-08-20 10:53:57","12000.00","efectivo","completado","0","0.00","0.00","8");
INSERT INTO `sales` VALUES("44","1","1","2024-08-20 11:30:41","5000.00","efectivo","completado","0","0.00","0.00","8");
INSERT INTO `sales` VALUES("45","1","1","2024-08-20 19:27:47","14000.00","efectivo","completado","0","0.00","0.00","8");
INSERT INTO `sales` VALUES("46","1","1","2024-08-20 19:47:32","10000.00","efectivo","completado","0","0.00","0.00","8");
INSERT INTO `sales` VALUES("47","1","1","2024-08-20 22:20:17","10000.00","efectivo","completado","0","0.00","0.00","8");
INSERT INTO `sales` VALUES("48","1","1","2024-08-21 12:04:10","20000.00","efectivo","completado","0","0.00","0.00","9");
INSERT INTO `sales` VALUES("49","1","1","2024-08-21 12:06:45","60000.00","efectivo","completado","0","0.00","0.00","9");
INSERT INTO `sales` VALUES("50","1","1","2024-08-21 17:38:40","500.00","efectivo","completado","0","0.00","0.00","9");
INSERT INTO `sales` VALUES("51","1","1","2024-08-21 18:24:15","4000.00","efectivo","completado","0","0.00","0.00","9");
INSERT INTO `sales` VALUES("52","1","1","2024-08-22 09:54:15","1600.00","efectivo","completado","0","0.00","0.00","10");
INSERT INTO `sales` VALUES("53","1","1","2024-08-22 17:46:33","3300.00","efectivo","completado","0","0.00","0.00","10");
INSERT INTO `sales` VALUES("54","1","1","2024-08-22 19:00:38","6000.00","efectivo","completado","0","0.00","0.00","10");
INSERT INTO `sales` VALUES("55","1","1","2024-08-22 19:02:11","8000.00","efectivo","completado","0","0.00","0.00","10");
INSERT INTO `sales` VALUES("56","1","1","2024-08-22 19:55:14","4000.00","efectivo","completado","0","0.00","0.00","10");
INSERT INTO `sales` VALUES("57","1","1","2024-08-22 20:09:10","28000.00","efectivo","completado","0","0.00","0.00","10");
INSERT INTO `sales` VALUES("58","10","1","2024-08-22 23:02:55","8000.00","efectivo","completado","0","0.00","0.00","10");
INSERT INTO `sales` VALUES("59","1","1","2024-08-23 16:39:14","5000.00","efectivo","completado","0","0.00","0.00","11");
INSERT INTO `sales` VALUES("60","8","1","2024-08-23 19:15:40","6000.00","efectivo","completado","0","0.00","0.00","11");
INSERT INTO `sales` VALUES("61","1","1","2024-08-23 20:04:56","2000.00","efectivo","completado","0","0.00","0.00","11");
INSERT INTO `sales` VALUES("62","1","1","2024-08-23 20:09:36","25000.00","transferencia","completado","0","0.00","0.00","11");
INSERT INTO `sales` VALUES("63","1","1","2024-08-24 10:34:42","10000.00","efectivo","completado","0","0.00","0.00","12");
INSERT INTO `sales` VALUES("64","1","1","2024-08-26 10:10:18","3300.00","efectivo","completado","0","0.00","0.00","13");
INSERT INTO `sales` VALUES("65","1","1","2024-08-26 10:17:37","3300.00","efectivo","completado","0","0.00","0.00","13");
INSERT INTO `sales` VALUES("66","1","1","2024-08-26 11:43:08","28000.00","efectivo","completado","0","0.00","0.00","13");



DROP TABLE IF EXISTS `service_devices`;


CREATE TABLE `service_devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_order_id` int(11) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_order_id` (`service_order_id`),
  CONSTRAINT `service_devices_ibfk_1` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT INTO `service_devices` VALUES("1","1","Motorla","E7i Power","355969783852038");
INSERT INTO `service_devices` VALUES("2","2","Xiaomi","Mi A2 lite","35xxxxxxxxxxxxx");
INSERT INTO `service_devices` VALUES("3","3","Samsung","A04e","35xxxxxxxxxxxxx");
INSERT INTO `service_devices` VALUES("4","4","Samsung","J2 Prime","352940096371951");
INSERT INTO `service_devices` VALUES("5","5","LG","22","35xxxxxxxxxxxx");
INSERT INTO `service_devices` VALUES("6","6","Alcatel","Idol","35xxxxxxxxxxxx");
INSERT INTO `service_devices` VALUES("7","7","LG","X230AR","353885083914942");
INSERT INTO `service_devices` VALUES("8","8","Motorola","G20","35xxxxxxxxxxxx");



DROP TABLE IF EXISTS `service_items`;


CREATE TABLE `service_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_order_id` int(11) DEFAULT NULL,
  `description` text,
  `cost` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_order_id` (`service_order_id`),
  CONSTRAINT `service_items_ibfk_1` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT INTO `service_items` VALUES("1","1","Modulo","50000.00");
INSERT INTO `service_items` VALUES("2","2","Modulo","60000.00");
INSERT INTO `service_items` VALUES("3","3","Cambio de módulo","50000.00");
INSERT INTO `service_items` VALUES("4","4","Cambio de Pin de carga","12000.00");
INSERT INTO `service_items` VALUES("5","5","Cambio de Pin de carga","12000.00");
INSERT INTO `service_items` VALUES("6","6","No enciende","3000.00");
INSERT INTO `service_items` VALUES("7","7","FRP","15000.00");
INSERT INTO `service_items` VALUES("8","8","Cambio de módulo","60000.00");



DROP TABLE IF EXISTS `service_order_notes`;


CREATE TABLE `service_order_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_order_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `note` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `service_order_id` (`service_order_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `service_order_notes_ibfk_1` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`),
  CONSTRAINT `service_order_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `service_order_notes` VALUES("1","1","1","Reparación para fines de septiembre","2024-08-21 13:49:52");
INSERT INTO `service_order_notes` VALUES("2","3","1","Se cambió pin de carga y termistor","2024-08-22 10:38:28");
INSERT INTO `service_order_notes` VALUES("3","7","1","El cliente avisa por Whatsapp si se procesa.","2024-08-23 17:11:15");



DROP TABLE IF EXISTS `service_order_status_history`;


CREATE TABLE `service_order_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_order_id` int(11) DEFAULT NULL,
  `status` enum('abierto','en_progreso','cerrado','cancelado') DEFAULT NULL,
  `changed_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `changed_by` int(11) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `service_order_id` (`service_order_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `service_order_status_history_ibfk_1` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`),
  CONSTRAINT `service_order_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

INSERT INTO `service_order_status_history` VALUES("1","3","cerrado","2024-08-22 10:38:03","1","Reparado.");
INSERT INTO `service_order_status_history` VALUES("2","2","en_progreso","2024-08-22 11:40:20","1","");
INSERT INTO `service_order_status_history` VALUES("3","4","cerrado","2024-08-22 11:45:25","1","Terminado");
INSERT INTO `service_order_status_history` VALUES("4","4","abierto","2024-08-22 14:14:23","1","");
INSERT INTO `service_order_status_history` VALUES("5","4","cerrado","2024-08-22 14:14:33","1","");
INSERT INTO `service_order_status_history` VALUES("6","2","cerrado","2024-08-22 18:56:18","1","Terminado");
INSERT INTO `service_order_status_history` VALUES("7","5","cerrado","2024-08-22 21:18:20","1","Reparado");
INSERT INTO `service_order_status_history` VALUES("8","7","cerrado","2024-08-26 09:12:56","1","Terminado");
INSERT INTO `service_order_status_history` VALUES("9","8","cerrado","2024-08-26 12:18:25","1","Terminado.");



DROP TABLE IF EXISTS `service_orders`;


CREATE TABLE `service_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(20) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `status` enum('abierto','en_progreso','cerrado','cancelado') DEFAULT 'abierto',
  `warranty` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) DEFAULT '0.00',
  `prepaid_amount` decimal(10,2) DEFAULT '0.00',
  `balance` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `service_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT INTO `service_orders` VALUES("1","ORD20240821102933266","2","abierto","0","2024-08-21 10:29:33","2024-08-21 10:29:33","50000.00","0.00","50000.00");
INSERT INTO `service_orders` VALUES("2","ORD20240821112807366","3","cerrado","0","2024-08-21 11:28:07","2024-08-22 18:56:18","60000.00","60000.00","0.00");
INSERT INTO `service_orders` VALUES("3","ORD20240821165854541","5","cerrado","0","2024-08-21 16:58:54","2024-08-22 10:38:03","50000.00","25000.00","25000.00");
INSERT INTO `service_orders` VALUES("4","ORD20240822114343680","7","cerrado","0","2024-08-22 11:43:43","2024-08-22 14:14:33","12000.00","10000.00","2000.00");
INSERT INTO `service_orders` VALUES("5","ORD20240822175708609","8","cerrado","0","2024-08-22 17:57:08","2024-08-22 21:18:20","12000.00","6000.00","6000.00");
INSERT INTO `service_orders` VALUES("6","ORD20240822193803245","3","abierto","0","2024-08-22 19:38:03","2024-08-22 19:38:03","3000.00","0.00","3000.00");
INSERT INTO `service_orders` VALUES("7","ORD20240823171039994","11","cerrado","0","2024-08-23 17:10:39","2024-08-26 09:12:56","15000.00","0.00","15000.00");
INSERT INTO `service_orders` VALUES("8","ORD20240826103935854","12","cerrado","0","2024-08-26 10:39:35","2024-08-26 12:18:25","60000.00","32000.00","28000.00");



DROP TABLE IF EXISTS `service_parts`;


CREATE TABLE `service_parts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_order_id` int(11) DEFAULT NULL,
  `part_name` varchar(100) DEFAULT NULL,
  `part_number` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_order_id` (`service_order_id`),
  CONSTRAINT `service_parts_ibfk_1` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `service_parts` VALUES("1","4","Cubrelente Cámara","0","1","4000.00");
INSERT INTO `service_parts` VALUES("2","8","Glass","00","1","3000.00");



DROP TABLE IF EXISTS `service_terms`;


CREATE TABLE `service_terms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text,
  `active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `service_terms` VALUES("1","Si el producto no fuera retirado dentro del término de 90 días a contar de la fecha de recepción del mismo por parte de Cellcom Technology, será considerado abandonado en término de los artículos 2375, 2525 y 2526 del código civil, quedando Cellcom Technology facultado a darle el destino que considere pertinente sin necesidad de informarlo previamente al cliente. La reparación efectuada cuenta con una GARANTÍA de 30 días corridos a partir de la fecha de entrega del PRODUCTO, tanto la mano de obra como el material empleado en esta GARANTÍA no ampara los defectos originados por el acarreo, transporte, incendio, inundaciones, tormentas eléctricas, golpes o accidentes de cualquier naturaleza. Asimismo la presente GARANTÍA quedará automáticamente cancelada cuando se efectúen intervenciones por terceros no autorizados. .","1","2024-08-20 19:13:54");



DROP TABLE IF EXISTS `settings`;


CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(100) NOT NULL DEFAULT 'Sistema POS',
  `timezone` varchar(100) NOT NULL DEFAULT 'America/Argentina/Buenos_Aires',
  `currency` varchar(10) NOT NULL DEFAULT 'ARS',
  `admin_email` varchar(100) NOT NULL,
  `items_per_page` int(11) NOT NULL DEFAULT '20',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.21',
  `logo_path` varchar(255) DEFAULT '/public/uploads/logo.png',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `settings` VALUES("1","Ordenes de Trabajo","America/Argentina/Buenos_Aires","ARS","taller@cellcomweb.com.ar","20","0.21","/uploads/logo_1724551009.png");



DROP TABLE IF EXISTS `stock_movements`;


CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `movement_type` enum('compra','venta','ajuste','devolución') NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8;

INSERT INTO `stock_movements` VALUES("1","5","-3","venta","1","Venta de producto","2024-08-17 14:41:48","1");
INSERT INTO `stock_movements` VALUES("2","7","-2","venta","2","Venta de producto","2024-08-17 14:46:44","1");
INSERT INTO `stock_movements` VALUES("3","5","-2","venta","3","Venta de producto","2024-08-17 14:47:03","1");
INSERT INTO `stock_movements` VALUES("4","49","-1","venta","4","Venta de producto","2024-08-17 14:49:12","1");
INSERT INTO `stock_movements` VALUES("5","5","-2","venta","5","Venta de producto","2024-08-17 14:49:33","1");
INSERT INTO `stock_movements` VALUES("6","3","-1","venta","6","Venta de producto","2024-08-17 15:04:50","1");
INSERT INTO `stock_movements` VALUES("7","50","-1","venta","7","Venta de producto","2024-08-17 15:07:35","1");
INSERT INTO `stock_movements` VALUES("8","51","2","compra","1","Recepción de compra","2024-08-17 19:08:11","1");
INSERT INTO `stock_movements` VALUES("9","5","2","compra","2","Recepción de compra","2024-08-17 19:08:50","1");
INSERT INTO `stock_movements` VALUES("10","22","-1","venta","8","Venta de producto","2024-08-17 19:26:48","1");
INSERT INTO `stock_movements` VALUES("11","52","-1","venta","9","Venta de producto","2024-08-17 19:30:46","1");
INSERT INTO `stock_movements` VALUES("12","27","-1","venta","10","Venta de producto","2024-08-17 19:31:12","1");
INSERT INTO `stock_movements` VALUES("13","53","-1","venta","11","Venta de producto","2024-08-17 19:31:45","1");
INSERT INTO `stock_movements` VALUES("14","21","-1","venta","12","Venta de producto","2024-08-17 20:06:26","1");
INSERT INTO `stock_movements` VALUES("15","5","-1","venta","13","Venta de producto","2024-08-17 20:06:47","1");
INSERT INTO `stock_movements` VALUES("16","7","-2","venta","14","Venta de producto","2024-08-17 20:07:15","1");
INSERT INTO `stock_movements` VALUES("17","5","-3","venta","15","Venta de producto","2024-08-17 20:07:38","1");
INSERT INTO `stock_movements` VALUES("18","20","-1","venta","16","Venta de producto","2024-08-17 20:23:29","1");
INSERT INTO `stock_movements` VALUES("19","1","-9","venta","18","Venta de producto","2024-08-17 20:39:09","1");
INSERT INTO `stock_movements` VALUES("20","16","-1","venta","19","Venta de producto","2024-08-17 22:41:44","1");
INSERT INTO `stock_movements` VALUES("21","19","-1","venta","20","Venta de producto","2024-08-17 22:42:02","1");
INSERT INTO `stock_movements` VALUES("22","5","-3","venta","21","Venta de producto","2024-08-17 22:42:27","1");
INSERT INTO `stock_movements` VALUES("23","5","-2","venta","22","Venta de producto","2024-08-17 22:42:45","1");
INSERT INTO `stock_movements` VALUES("24","54","-1","venta","23","Venta de producto","2024-08-17 22:44:38","1");
INSERT INTO `stock_movements` VALUES("25","3","-1","venta","24","Venta de producto","2024-08-17 22:44:56","1");
INSERT INTO `stock_movements` VALUES("26","20","-1","venta","25","Venta de producto","2024-08-17 22:45:14","1");
INSERT INTO `stock_movements` VALUES("27","20","-1","venta","26","Venta de producto","2024-08-17 22:45:30","1");
INSERT INTO `stock_movements` VALUES("28","48","-1","venta","27","Venta de producto","2024-08-17 22:45:45","1");
INSERT INTO `stock_movements` VALUES("29","20","-1","venta","28","Venta de producto","2024-08-17 22:46:00","1");
INSERT INTO `stock_movements` VALUES("30","20","-1","venta","29","Venta de producto","2024-08-17 22:46:10","1");
INSERT INTO `stock_movements` VALUES("31","1","-4","venta","30","Venta de producto","2024-08-17 22:54:19","1");
INSERT INTO `stock_movements` VALUES("32","19","-1","venta","31","Venta de producto","2024-08-17 22:54:58","1");
INSERT INTO `stock_movements` VALUES("33","8","-1","venta","32","Venta de producto","2024-08-18 00:06:46","1");
INSERT INTO `stock_movements` VALUES("34","31","-1","venta","32","Venta de producto","2024-08-19 08:23:30","1");
INSERT INTO `stock_movements` VALUES("35","2","-1","venta","33","Venta de producto","2024-08-19 08:25:50","1");
INSERT INTO `stock_movements` VALUES("36","19","-1","venta","35","Venta de producto","2024-08-19 11:52:34","1");
INSERT INTO `stock_movements` VALUES("37","19","-1","venta","36","Venta de producto","2024-08-19 11:55:43","1");
INSERT INTO `stock_movements` VALUES("38","19","-1","venta","37","Venta de producto","2024-08-19 11:56:09","1");
INSERT INTO `stock_movements` VALUES("39","2","1","compra","3","Recepción de compra","2024-08-19 12:38:50","1");
INSERT INTO `stock_movements` VALUES("40","55","-1","venta","34","Venta de producto","2024-08-19 17:05:52","1");
INSERT INTO `stock_movements` VALUES("41","7","-1","venta","35","Venta de producto","2024-08-19 18:09:55","1");
INSERT INTO `stock_movements` VALUES("42","5","-3","venta","35","Venta de producto","2024-08-19 18:09:55","1");
INSERT INTO `stock_movements` VALUES("43","5","-3","venta","36","Venta de producto","2024-08-19 18:58:22","1");
INSERT INTO `stock_movements` VALUES("44","19","-2","venta","37","Venta de producto","2024-08-19 19:16:55","1");
INSERT INTO `stock_movements` VALUES("45","8","-1","venta","38","Venta de producto","2024-08-19 19:17:53","1");
INSERT INTO `stock_movements` VALUES("46","32","-1","venta","38","Venta de producto","2024-08-19 19:17:53","1");
INSERT INTO `stock_movements` VALUES("47","12","-1","venta","38","Venta de producto","2024-08-19 19:17:53","1");
INSERT INTO `stock_movements` VALUES("48","2","-1","venta","39","Venta de producto","2024-08-19 19:30:15","1");
INSERT INTO `stock_movements` VALUES("49","8","1","compra","4","Recepción de compra","2024-08-19 20:20:56","1");
INSERT INTO `stock_movements` VALUES("50","2","1","compra","5","Recepción de compra","2024-08-19 20:22:30","1");
INSERT INTO `stock_movements` VALUES("51","31","-1","venta","40","Venta de producto","2024-08-20 10:09:40","1");
INSERT INTO `stock_movements` VALUES("52","5","-3","venta","41","Venta de producto","2024-08-20 10:48:35","1");
INSERT INTO `stock_movements` VALUES("53","5","-2","venta","42","Venta de producto","2024-08-20 10:53:31","1");
INSERT INTO `stock_movements` VALUES("54","3","-1","venta","43","Venta de producto","2024-08-20 10:53:57","1");
INSERT INTO `stock_movements` VALUES("55","20","-2","venta","44","Venta de producto","2024-08-20 11:30:41","1");
INSERT INTO `stock_movements` VALUES("56","4","-1","venta","45","Venta de producto","2024-08-20 19:27:47","1");
INSERT INTO `stock_movements` VALUES("57","19","-5","venta","46","Venta de producto","2024-08-20 19:47:32","1");
INSERT INTO `stock_movements` VALUES("58","29","-1","venta","46","Venta de producto","2024-08-20 19:47:32","1");
INSERT INTO `stock_movements` VALUES("59","1","-5","venta","47","Venta de producto","2024-08-20 22:20:17","1");
INSERT INTO `stock_movements` VALUES("60","9","-1","venta","48","Venta de producto","2024-08-21 12:04:10","1");
INSERT INTO `stock_movements` VALUES("61","2","-1","venta","49","Venta de producto","2024-08-21 12:06:45","1");
INSERT INTO `stock_movements` VALUES("62","52","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("63","13","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("64","19","7","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("65","1","5","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("66","22","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("67","48","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("68","55","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("69","56","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("70","51","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("71","23","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("72","24","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("73","25","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("74","26","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("75","27","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("76","43","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("77","42","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("78","8","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("79","36","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("80","2","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("81","4","1","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("82","3","1","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("83","5","11","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("84","28","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("85","29","1","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("86","30","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("87","31","2","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("88","21","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("89","6","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("90","53","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("91","49","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("92","7","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("93","9","1","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("94","14","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("95","46","0","ajuste",NULL,"dañado","2024-08-21 15:27:42","1");
INSERT INTO `stock_movements` VALUES("96","45","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("97","15","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("98","16","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("99","17","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("100","18","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("101","38","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("102","39","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("103","47","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("104","37","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("105","10","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("106","11","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("107","12","1","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("108","32","1","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("109","33","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("110","34","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("111","44","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("112","50","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("113","20","2","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("114","35","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("115","54","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("116","40","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("117","41","0","ajuste",NULL,"dañado","2024-08-21 15:27:43","1");
INSERT INTO `stock_movements` VALUES("118","5","-1","venta","50","Venta de producto","2024-08-21 17:38:40","1");
INSERT INTO `stock_movements` VALUES("119","7","-1","venta","51","Venta de producto","2024-08-21 18:24:15","1");
INSERT INTO `stock_movements` VALUES("120","5","-3","venta","51","Venta de producto","2024-08-21 18:24:15","1");
INSERT INTO `stock_movements` VALUES("121","56","1","compra","6","Recepción de compra","2024-08-21 20:33:17","1");
INSERT INTO `stock_movements` VALUES("122","2","1","compra","7","Recepción de compra","2024-08-21 20:33:23","1");
INSERT INTO `stock_movements` VALUES("123","57","1","compra","8","Recepción de compra","2024-08-21 20:33:29","1");
INSERT INTO `stock_movements` VALUES("124","5","-2","venta","52","Venta de producto","2024-08-22 09:54:15","1");
INSERT INTO `stock_movements` VALUES("125","5","-3","venta","53","Venta de producto","2024-08-22 17:46:33","1");
INSERT INTO `stock_movements` VALUES("126","1","-3","venta","54","Venta de producto","2024-08-22 19:00:38","1");
INSERT INTO `stock_movements` VALUES("127","20","-1","venta","55","Venta de producto","2024-08-22 19:02:11","1");
INSERT INTO `stock_movements` VALUES("128","1","-3","venta","55","Venta de producto","2024-08-22 19:02:11","1");
INSERT INTO `stock_movements` VALUES("129","7","-1","venta","56","Venta de producto","2024-08-22 19:55:14","1");
INSERT INTO `stock_movements` VALUES("130","5","-3","venta","56","Venta de producto","2024-08-22 19:55:14","1");
INSERT INTO `stock_movements` VALUES("131","57","-1","venta","57","Venta de producto","2024-08-22 20:09:10","1");
INSERT INTO `stock_movements` VALUES("132","20","-1","venta","57","Venta de producto","2024-08-22 20:09:10","1");
INSERT INTO `stock_movements` VALUES("133","18","-1","venta","58","Venta de producto","2024-08-22 23:02:55","1");
INSERT INTO `stock_movements` VALUES("134","19","-1","venta","59","Venta de producto","2024-08-23 10:24:40","1");
INSERT INTO `stock_movements` VALUES("135","19","-1","venta","59","Venta de producto","2024-08-23 16:39:14","1");
INSERT INTO `stock_movements` VALUES("136","3","-1","venta","60","Venta de producto","2024-08-23 19:15:40","1");
INSERT INTO `stock_movements` VALUES("137","1","-1","venta","61","Venta de producto","2024-08-23 20:04:56","1");
INSERT INTO `stock_movements` VALUES("138","56","-1","venta","62","Venta de producto","2024-08-23 20:09:36","1");
INSERT INTO `stock_movements` VALUES("139","52","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("140","13","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("141","19","2","ajuste",NULL,"correccion","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("142","1","7","ajuste",NULL,"correccion","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("143","22","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("144","48","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("145","55","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("146","56","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("147","51","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("148","23","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("149","24","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("150","25","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("151","26","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("152","27","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("153","43","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("154","42","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("155","8","-2","ajuste",NULL,"correccion","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("156","36","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("157","2","-1","ajuste",NULL,"correccion","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("158","4","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("159","3","1","ajuste",NULL,"correccion","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("160","5","11","ajuste",NULL,"correccion","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("161","28","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("162","29","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("163","30","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("164","31","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("165","21","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("166","6","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("167","53","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("168","49","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("169","7","2","ajuste",NULL,"correccion","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("170","9","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("171","14","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("172","46","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("173","45","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("174","15","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("175","16","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("176","17","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("177","18","1","ajuste",NULL,"correccion","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("178","38","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("179","39","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("180","47","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("181","37","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("182","10","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("183","11","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("184","12","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("185","32","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("186","57","-1","ajuste",NULL,"correccion","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("187","33","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("188","34","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("189","44","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("190","50","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("191","20","2","ajuste",NULL,"correccion","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("192","35","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("193","54","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("194","40","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("195","41","0","ajuste",NULL,"dañado","2024-08-24 09:37:41","1");
INSERT INTO `stock_movements` VALUES("196","20","35","compra","9","Recepción de compra","2024-08-24 10:33:31","1");
INSERT INTO `stock_movements` VALUES("197","52","-1","venta","63","Venta de producto","2024-08-24 10:34:42","1");
INSERT INTO `stock_movements` VALUES("198","5","-1","venta","64","Venta de producto","2024-08-26 10:10:18","1");
INSERT INTO `stock_movements` VALUES("199","5","-1","venta","65","Venta de producto","2024-08-26 10:17:38","1");
INSERT INTO `stock_movements` VALUES("200","58","1","compra","10","Recepción de compra","2024-08-26 11:18:14","1");
INSERT INTO `stock_movements` VALUES("201","59","-1","venta","66","Venta de producto","2024-08-26 11:43:08","1");



DROP TABLE IF EXISTS `suppliers`;


CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

INSERT INTO `suppliers` VALUES("1","Cellcom","Cellcom","info@cellcomweb.com.ar","3482549555","Calle 9 Nro 539","2024-08-16 23:26:12");
INSERT INTO `suppliers` VALUES("2","Redcell","Mayra","info@redcell.com.ar","3482763835","Irigoyen y Moreno","2024-08-16 01:53:34");
INSERT INTO `suppliers` VALUES("3","Delbón Luciano","Luciano","luciano@delbon.com.ar","3482649896","Calle 16 Nro 680","2024-08-16 01:54:37");
INSERT INTO `suppliers` VALUES("4","JJE Mayorista","Bali","info@jje.com.ar","3482523222","Habegger 1443","2024-08-16 01:55:48");
INSERT INTO `suppliers` VALUES("5","Celuce","César","ventas@celucedistribuciones.com.ar","3482603030","9 de julio 1212","2024-08-16 01:57:26");
INSERT INTO `suppliers` VALUES("6","Educom","Eduardo","info@educom.com.ar","3482210411","Mitre 727","2024-08-16 01:58:46");
INSERT INTO `suppliers` VALUES("7","Hello","Federico","info@hello.com.ar","3482386055","Olessio 1360","2024-08-16 01:59:43");
INSERT INTO `suppliers` VALUES("8","GyG Argentina Juegos","Gustavo","info@gyg.com.ar","3482412280","Calle 9 Nro 425","2024-08-16 02:02:05");
INSERT INTO `suppliers` VALUES("9","Mercado Libre","ML","info@ml.com.ar","3482000000","Calle 9 Nro 539","2024-08-16 20:48:37");
INSERT INTO `suppliers` VALUES("10","Claro","Daiana","info@claro.com.ar","3482255131","Avellaneda","2024-08-17 13:58:03");



DROP TABLE IF EXISTS `user_profiles`;


CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bio` text,
  `location` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_profiles` VALUES("1","1","Soy un Capo","Avellaneda","https://www.cellcomweb.com.ar","2024-08-19 13:45:17");



DROP TABLE IF EXISTS `users`;


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `users` VALUES("1","Cellcom Technology","info@cellcomweb.com.ar","$2y$10$e47bpB.dGC8ZaJH8E7u0b.1.PsHjCq8PFPfXPQ9q1xht5WA1qjTay","1","2024-08-20 14:31:01");
INSERT INTO `users` VALUES("2","Cellcom Technology","tecnico@cellcomweb.com.ar","$2y$10$phr4kw52xwLHstmXBA2Mz.La1V2TER0VoW2quc2u6tjVzWYaAh9sG","5","2024-08-20 19:28:54");
INSERT INTO `users` VALUES("3","Cellcom Technology","ventas@cellcomweb.com.ar","$2y$10$ClblXMepZMsuq7nvi2HdlO.POfnmf27q6imSAHXxzPoDk2cd7Jo02","4","2024-08-24 00:18:39");



