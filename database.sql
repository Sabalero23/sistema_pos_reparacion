-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 29, 2024 at 11:07 AM
-- Server version: 5.7.44-log
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `repair`
--

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `budget_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pendiente','aprobado','rechazado','expirado') DEFAULT 'pendiente',
  `validity_period` int(11) DEFAULT '30',
  `notes` text,
  `view_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `customer_id`, `user_id`, `budget_date`, `total_amount`, `status`, `validity_period`, `notes`, `view_token`) VALUES
(1, 6, 1, '2024-08-19 02:37:57', '120000.00', 'pendiente', 7, 'Sujeto a modificación cotización Dólar Blue!', 'a3f46bbf67304585bfe6aff03d1506cf39ecc176773b93a6dc136cb0c937530c'),
(2, 4, 1, '2024-08-26 01:15:43', '115000.00', 'aprobado', 2, 'Presupuesto sujeto a modificación a la cotización del dólar Blue.', '24405dc3a24097b329c4bdc0e8a1cb5bfec26aee8af486bac859e6b79e7c1183');

-- --------------------------------------------------------

--
-- Table structure for table `budget_items`
--

CREATE TABLE `budget_items` (
  `id` int(11) NOT NULL,
  `budget_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `budget_items`
--

INSERT INTO `budget_items` (`id`, `budget_id`, `product_id`, `quantity`, `price`) VALUES
(4, 2, 8, 1, '85000.00'),
(5, 2, 32, 1, '15000.00'),
(6, 2, 12, 1, '15000.00'),
(13, 1, 8, 1, '90000.00'),
(14, 1, 32, 1, '15000.00'),
(15, 1, 12, 1, '15000.00');

-- --------------------------------------------------------

--
-- Table structure for table `cash_register_movements`
--

CREATE TABLE `cash_register_movements` (
  `id` int(11) NOT NULL,
  `cash_register_session_id` int(11) NOT NULL,
  `movement_type` enum('sale','purchase','cash_in','cash_out') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `notes` text,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cash_register_movements`
--

INSERT INTO `cash_register_movements` (`id`, `cash_register_session_id`, `movement_type`, `amount`, `reference_id`, `notes`, `created_at`) VALUES
(1, 1, 'cash_out', '64000.00', NULL, 'Ganancia', '2024-08-12 15:03:25'),
(2, 1, 'cash_out', '14500.00', NULL, 'Ganancia', '2024-08-12 20:30:44'),
(3, 2, 'cash_in', '50000.00', NULL, 'Reserva Celular Samsung A50 Reacondicionado', '2024-08-13 08:32:17'),
(4, 2, 'cash_out', '207000.00', NULL, 'Ganancia', '2024-08-13 19:47:54'),
(5, 3, 'cash_in', '8000.00', NULL, 'Se&ntilde;a Bateria J2 Prime', '2024-08-14 11:10:11'),
(6, 4, 'cash_out', '38400.00', NULL, 'Ganancia', '2024-08-15 20:40:41'),
(7, 5, 'cash_out', '1500.00', NULL, 'Mandado', '2024-08-16 10:48:35'),
(8, 5, 'cash_out', '38000.00', NULL, 'Compra Modulo A50', '2024-08-16 11:47:25'),
(9, 5, 'cash_out', '115000.00', NULL, 'Ganancia', '2024-08-16 20:47:55'),
(10, 6, 'cash_out', '2000.00', NULL, 'Mandado', '2024-08-17 11:57:52'),
(11, 6, 'cash_out', '7000.00', NULL, 'Ganancia', '2024-08-17 12:57:17'),
(12, 7, 'cash_out', '600.00', NULL, 'Compra trapo de piso.', '2024-08-19 08:28:41'),
(13, 7, 'cash_in', '25000.00', NULL, 'Se&ntilde;a reparaci&oacute;n Modulo M13', '2024-08-19 12:40:28'),
(14, 7, 'cash_out', '236500.00', NULL, 'Ganancia', '2024-08-19 20:24:05'),
(15, 8, 'cash_out', '2000.00', NULL, 'Mandado Oreste', '2024-08-20 11:20:49'),
(16, 8, 'cash_out', '25000.00', NULL, 'Ganancia', '2024-08-20 12:06:10'),
(17, 8, 'cash_out', '34000.00', NULL, 'Ganancia', '2024-08-20 22:21:34'),
(18, 9, 'cash_out', '50000.00', NULL, 'Ganancia', '2024-08-21 12:10:43'),
(19, 9, 'cash_out', '2000.00', NULL, 'Mandado', '2024-08-21 17:45:48'),
(20, 9, 'cash_in', '25000.00', NULL, 'Se&ntilde;a Orden ORD20240821165854541', '2024-08-21 20:28:08'),
(21, 9, 'cash_out', '8000.00', NULL, 'Ganancia', '2024-08-21 20:34:34'),
(22, 10, 'cash_in', '6000.00', NULL, 'Seña de Morzan Gustavo de la orden ORD20240822175708609', '2024-08-22 17:57:11'),
(23, 10, 'cash_out', '2000.00', NULL, 'Compra Queso', '2024-08-22 20:09:56'),
(24, 10, 'cash_out', '4000.00', NULL, 'Pin de carga', '2024-08-22 21:10:41'),
(25, 10, 'cash_out', '59000.00', NULL, 'Ganancia', '2024-08-22 23:11:22'),
(26, 11, 'cash_out', '39000.00', NULL, 'Cierre Correcto', '2024-08-23 20:10:36'),
(27, 12, 'cash_out', '10000.00', NULL, 'Ganancia', '2024-08-24 12:01:51'),
(28, 13, 'cash_in', '32000.00', NULL, 'Seña de Eichemberger Giovanni de la orden ORD20240826103935854', '2024-08-26 10:39:39'),
(29, 13, 'cash_out', '2000.00', NULL, 'Mandado', '2024-08-26 11:14:44'),
(30, 13, 'cash_out', '45000.00', NULL, 'Ganancia', '2024-08-26 12:05:52'),
(31, 13, 'cash_out', '5000.00', NULL, 'Ganancia.', '2024-08-26 20:02:53'),
(32, 14, 'cash_in', '30000.00', NULL, 'Seña de Ayala Maria de la orden ORD20240827181558269', '2024-08-27 18:16:00'),
(33, 14, 'cash_out', '52000.00', NULL, 'Ganancia', '2024-08-27 20:14:41'),
(34, 15, 'cash_out', '15000.00', NULL, 'Ganancia', '2024-08-28 21:10:58');

-- --------------------------------------------------------

--
-- Table structure for table `cash_register_sessions`
--

CREATE TABLE `cash_register_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `opening_date` datetime NOT NULL,
  `closing_date` datetime DEFAULT NULL,
  `opening_balance` decimal(10,2) NOT NULL,
  `closing_balance` decimal(10,2) DEFAULT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cash_register_sessions`
--

INSERT INTO `cash_register_sessions` (`id`, `user_id`, `opening_date`, `closing_date`, `opening_balance`, `closing_balance`, `status`, `notes`) VALUES
(1, 1, '2024-08-12 08:39:27', '2024-08-12 21:15:10', '4500.00', '2000.00', 'closed', 'Inicio Caja Comercio\n\n\nCierre de caja correcto'),
(2, 1, '2024-08-13 22:24:35', '2024-08-13 20:49:01', '2000.00', '2000.00', 'closed', 'Inicio caja\nCierre Correcto'),
(3, 1, '2024-08-14 08:04:06', '2024-08-14 20:13:18', '2000.00', '20000.00', 'closed', 'Inicio Caja\nCierre correcto'),
(4, 1, '2024-08-15 08:15:04', '2024-08-15 20:41:13', '20000.00', '2100.00', 'closed', 'Inicio caja\nCierre Correcto'),
(5, 1, '2024-08-16 08:41:59', '2024-08-16 22:51:38', '2100.00', '1100.00', 'closed', 'Inicio caja\nCierre correcto'),
(6, 1, '2024-08-17 08:53:44', '2024-08-17 12:58:36', '1100.00', '1100.00', 'closed', 'Inicio caja\r\nCierre correcto'),
(7, 1, '2024-08-19 02:10:14', '2024-08-19 20:24:24', '1100.00', '1300.00', 'closed', 'Apertura\nCaja correcta'),
(8, 1, '2024-08-20 08:09:40', '2024-08-20 22:21:55', '1300.00', '2300.00', 'closed', 'Inicio de caja\nCierre correcto.'),
(9, 1, '2024-08-21 12:03:08', '2024-08-21 20:34:44', '2300.00', '2200.00', 'closed', 'Inicio Caja\nCierre Correcto'),
(10, 1, '2024-08-22 08:28:24', '2024-08-22 23:11:40', '2200.00', '2100.00', 'closed', 'Inicio Caja\nCierre correcto.'),
(11, 1, '2024-08-23 08:16:36', '2024-08-23 20:10:58', '2100.00', '1100.00', 'closed', 'Inicio Caja\nCierre correcto'),
(12, 1, '2024-08-24 10:19:39', '2024-08-24 12:02:10', '1100.00', '1100.00', 'closed', 'Inicio Caja\nCierre Correcto'),
(13, 1, '2024-08-26 08:16:46', '2024-08-26 20:03:04', '1100.00', '2200.00', 'closed', 'Inicio caja.\nCierre correcto.'),
(14, 1, '2024-08-27 12:04:30', '2024-08-27 20:14:50', '2200.00', '1200.00', 'closed', 'Inicio de caja\nCierre Correcto'),
(15, 1, '2024-08-28 08:20:21', '2024-08-28 21:11:07', '1200.00', '1200.00', 'closed', 'Inicio Caja\nCierre Correcto');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Cargadores', 'Cargadores turbo 1ra Calidad'),
(2, 'Glass', 'Protectores Glass'),
(3, 'Software', 'Instalación Software / Sistemas Operativos'),
(4, 'Reparaciones', 'Reparaciones en general'),
(5, 'FRP/F4', 'Cuentas Google y Reparación de Señal'),
(6, 'Asistencia', 'Costo asistencia'),
(7, 'Cables', 'Cables 1ra calidad'),
(8, 'Auriculares', 'Auriculares 1ra calidad'),
(9, 'Cámaras', 'Cámaras 1ra calidad'),
(10, 'Almacenamiento', 'Dispositivos de almacenamiento'),
(11, 'Módulos', 'Módulos 1a calidad'),
(12, 'Celulares', 'Celulares Nuevos'),
(13, 'Celulares Reacondicionados', 'Celulares reacondicionados con garantía'),
(14, 'Varios', 'Categoría Varios'),
(15, 'Baterias', 'Baterías nuevas'),
(16, 'Placas', 'Placas de carga, Main.'),
(17, 'General', 'Categoría general para productos');

-- --------------------------------------------------------

--
-- Table structure for table `company_info`
--

CREATE TABLE `company_info` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `website` varchar(100) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT '/uploads/logo.png',
  `legal_info` text NOT NULL,
  `receipt_footer` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `company_info`
--

INSERT INTO `company_info` (`id`, `name`, `address`, `phone`, `email`, `website`, `logo_path`, `legal_info`, `receipt_footer`) VALUES
(1, 'Cellcom Technology', 'Calle 9 Nro 539', '543482549555', 'info@cellcomweb.com.ar', 'https://www.cellcomweb.com.ar', '/public/uploads/66c627b635d77.png', 'CUIT: 20-30100538-6 | Ingresos Brutos: entrámite', 'Gracias por visitarnos. ¡Esperamos verlo pronto!');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `created_at`) VALUES
(1, 'Consumidor Final', 'info@cellcomweb.com.ar', '543482549555', 'Calle 9 Nro 539', '2024-08-20 17:31:00'),
(2, 'Segovia Norma', 'orden@taller.cellcomweb.com.ar', '543482306272', 'Pje 126/128 Nro 73', '2024-08-21 13:25:31'),
(3, 'Bordon Horacio', 'orden@taller.cellcomweb.com.ar', '543482241406', 'Calle 10 Nro 292', '2024-08-21 14:27:07'),
(4, 'Lopez Faustino', 'lopezfaustino@gmail.com', '543482332687', 'Calle 311 Nro 540', '2024-08-20 02:08:35'),
(5, 'Flores Julio', 'orden@taller.cellcomweb.com.ar', '543482625707', 'Calle 13 nro 62', '2024-08-21 22:58:00'),
(6, 'Rodriguez Claudia', 'claudia.alicia.rodriguez@gmail.com', '543482628711', 'Alvear y Pasaje 36/38', '2024-08-21 23:03:16'),
(7, 'Medina Hugo', 'orden@cellcomweb.com.ar', '543482598515', 'Calle 21 Nro 1035', '2024-08-22 14:42:18'),
(8, 'Morzan Gustavo', 'gmorzan3177@gmail.com', '543482244010', 'Pje 18 nro 35', '2024-08-22 20:56:34'),
(9, 'Alegre Héctor', 'hectoralegre13@gmail.com', '543482527825', 'Calle 330 Nro 14', '2024-08-22 23:06:17'),
(10, 'Sandrigo Cristian', 'orden@cellcomweb.com.ar', '543482576101', 'Ausonia', '2024-08-23 02:02:34'),
(11, 'Herrera Micaela', 'orden@cellcomweb.com.ar', '543482634853', 'Calle 121 nro 981', '2024-08-23 20:09:01'),
(12, 'Eichemberger Giovanni', 'giovanni.eichen@gmail.com', '543482313715', 'Calle 24 nro 935', '2024-08-26 13:38:43'),
(13, 'Vera Maria Teresa', 'orden@cellcomweb.com.ar', '543482235285', 'Calle 311 Nro 628', '2024-08-26 21:24:07'),
(14, 'Gallo Brian', 'orden@cellcomweb.com.ar', '543482374360', 'Calle 21 nro 283', '2024-08-26 21:28:20'),
(15, 'Echavarria Sergio', 'orden@cellcomweb.com.ar', '543482644994', 'Calle 117 Nro 815', '2024-08-27 11:46:36'),
(16, 'Salinas Andy', 'orden@taller.cellcomweb.com.ar', '543482243278', 'Pje sur nro 1075', '2024-08-27 14:16:25'),
(17, 'Ayala Maria', 'orden@cellcomweb.com.ar', '543482209941', 'Pasaje 126/128 nro 361', '2024-08-27 21:15:22'),
(18, 'Lopez Hermelinda', 'orden@cellcomweb.com.ar', '543482539629', 'Pje 22 nro 205', '2024-08-28 14:17:45'),
(19, 'Navarro Damian', 'orden@cellcomweb.com.ar', '543482673214', 'Calle 332 nro 714', '2024-08-28 19:59:42'),
(20, 'Villagra Nahuel', 'orden@cellcomweb.com.ar', '543482549555', 'Pje 114/116 nro 806', '2024-08-28 21:28:52');

-- --------------------------------------------------------

--
-- Table structure for table `customer_accounts`
--

CREATE TABLE `customer_accounts` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `balance` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `home_visits`
--

CREATE TABLE `home_visits` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `visit_time` time NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('programada','completada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'programada',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_adjustments`
--

CREATE TABLE `inventory_adjustments` (
  `id` int(11) NOT NULL,
  `adjustment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `inventory_adjustments`
--

INSERT INTO `inventory_adjustments` (`id`, `adjustment_date`, `user_id`, `notes`) VALUES
(1, '2024-08-21 18:27:42', 1, 'Correcciones'),
(2, '2024-08-24 12:37:41', 1, 'Correccion');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_adjustment_items`
--

CREATE TABLE `inventory_adjustment_items` (
  `id` int(11) NOT NULL,
  `adjustment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_before` int(11) NOT NULL,
  `quantity_after` int(11) NOT NULL,
  `reason` enum('dañado','perdido','correccion','otro') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `inventory_adjustment_items`
--

INSERT INTO `inventory_adjustment_items` (`id`, `adjustment_id`, `product_id`, `quantity_before`, `quantity_after`, `reason`) VALUES
(1, 1, 52, 0, 0, 'dañado'),
(2, 1, 13, 0, 0, 'dañado'),
(3, 1, 19, -7, 0, 'dañado'),
(4, 1, 1, -5, 0, 'dañado'),
(5, 1, 22, 0, 0, 'dañado'),
(6, 1, 48, 0, 0, 'dañado'),
(7, 1, 55, 0, 0, 'dañado'),
(8, 1, 56, 0, 0, 'dañado'),
(9, 1, 51, 0, 0, 'dañado'),
(10, 1, 23, 0, 0, 'dañado'),
(11, 1, 24, 0, 0, 'dañado'),
(12, 1, 25, 0, 0, 'dañado'),
(13, 1, 26, 0, 0, 'dañado'),
(14, 1, 27, 0, 0, 'dañado'),
(15, 1, 43, 0, 0, 'dañado'),
(16, 1, 42, 0, 0, 'dañado'),
(17, 1, 8, 2, 2, 'dañado'),
(18, 1, 36, 0, 0, 'dañado'),
(19, 1, 2, 0, 0, 'dañado'),
(20, 1, 4, -1, 0, 'dañado'),
(21, 1, 3, -1, 0, 'dañado'),
(22, 1, 5, -11, 0, 'dañado'),
(23, 1, 28, 0, 0, 'dañado'),
(24, 1, 29, -1, 0, 'dañado'),
(25, 1, 30, 0, 0, 'dañado'),
(26, 1, 31, -2, 0, 'dañado'),
(27, 1, 21, 0, 0, 'dañado'),
(28, 1, 6, 0, 0, 'dañado'),
(29, 1, 53, 0, 0, 'dañado'),
(30, 1, 49, 0, 0, 'dañado'),
(31, 1, 7, 0, 0, 'dañado'),
(32, 1, 9, -1, 0, 'dañado'),
(33, 1, 14, 0, 0, 'dañado'),
(34, 1, 46, 0, 0, 'dañado'),
(35, 1, 45, 0, 0, 'dañado'),
(36, 1, 15, 0, 0, 'dañado'),
(37, 1, 16, 0, 0, 'dañado'),
(38, 1, 17, 0, 0, 'dañado'),
(39, 1, 18, 0, 0, 'dañado'),
(40, 1, 38, 0, 0, 'dañado'),
(41, 1, 39, 0, 0, 'dañado'),
(42, 1, 47, 0, 0, 'dañado'),
(43, 1, 37, 0, 0, 'dañado'),
(44, 1, 10, 0, 0, 'dañado'),
(45, 1, 11, 0, 0, 'dañado'),
(46, 1, 12, -1, 0, 'dañado'),
(47, 1, 32, -1, 0, 'dañado'),
(48, 1, 33, 0, 0, 'dañado'),
(49, 1, 34, 0, 0, 'dañado'),
(50, 1, 44, 0, 0, 'dañado'),
(51, 1, 50, 0, 0, 'dañado'),
(52, 1, 20, -2, 0, 'dañado'),
(53, 1, 35, 0, 0, 'dañado'),
(54, 1, 54, 0, 0, 'dañado'),
(55, 1, 40, 0, 0, 'dañado'),
(56, 1, 41, 0, 0, 'dañado'),
(57, 2, 52, 0, 0, 'dañado'),
(58, 2, 13, 0, 0, 'dañado'),
(59, 2, 19, -2, 0, 'correccion'),
(60, 2, 1, -7, 0, 'correccion'),
(61, 2, 22, 0, 0, 'dañado'),
(62, 2, 48, 0, 0, 'dañado'),
(63, 2, 55, 0, 0, 'dañado'),
(64, 2, 56, 0, 0, 'dañado'),
(65, 2, 51, 0, 0, 'dañado'),
(66, 2, 23, 0, 0, 'dañado'),
(67, 2, 24, 0, 0, 'dañado'),
(68, 2, 25, 0, 0, 'dañado'),
(69, 2, 26, 0, 0, 'dañado'),
(70, 2, 27, 0, 0, 'dañado'),
(71, 2, 43, 0, 0, 'dañado'),
(72, 2, 42, 0, 0, 'dañado'),
(73, 2, 8, 2, 0, 'correccion'),
(74, 2, 36, 0, 0, 'dañado'),
(75, 2, 2, 1, 0, 'correccion'),
(76, 2, 4, 0, 0, 'dañado'),
(77, 2, 3, -1, 0, 'correccion'),
(78, 2, 5, -11, 0, 'correccion'),
(79, 2, 28, 0, 0, 'dañado'),
(80, 2, 29, 0, 0, 'dañado'),
(81, 2, 30, 0, 0, 'dañado'),
(82, 2, 31, 0, 0, 'dañado'),
(83, 2, 21, 0, 0, 'dañado'),
(84, 2, 6, 0, 0, 'dañado'),
(85, 2, 53, 0, 0, 'dañado'),
(86, 2, 49, 0, 0, 'dañado'),
(87, 2, 7, -2, 0, 'correccion'),
(88, 2, 9, 0, 0, 'dañado'),
(89, 2, 14, 0, 0, 'dañado'),
(90, 2, 46, 0, 0, 'dañado'),
(91, 2, 45, 0, 0, 'dañado'),
(92, 2, 15, 0, 0, 'dañado'),
(93, 2, 16, 0, 0, 'dañado'),
(94, 2, 17, 0, 0, 'dañado'),
(95, 2, 18, -1, 0, 'correccion'),
(96, 2, 38, 0, 0, 'dañado'),
(97, 2, 39, 0, 0, 'dañado'),
(98, 2, 47, 0, 0, 'dañado'),
(99, 2, 37, 0, 0, 'dañado'),
(100, 2, 10, 0, 0, 'dañado'),
(101, 2, 11, 0, 0, 'dañado'),
(102, 2, 12, 0, 0, 'dañado'),
(103, 2, 32, 0, 0, 'dañado'),
(104, 2, 57, 1, 0, 'correccion'),
(105, 2, 33, 0, 0, 'dañado'),
(106, 2, 34, 0, 0, 'dañado'),
(107, 2, 44, 0, 0, 'dañado'),
(108, 2, 50, 0, 0, 'dañado'),
(109, 2, 20, -2, 0, 'correccion'),
(110, 2, 35, 0, 0, 'dañado'),
(111, 2, 54, 0, 0, 'dañado'),
(112, 2, 40, 0, 0, 'dañado'),
(113, 2, 41, 0, 0, 'dañado');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('efectivo','tarjeta','transferencia','otros') NOT NULL,
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `customer_id`, `amount`, `payment_date`, `payment_method`, `notes`) VALUES
(1, 4, '115000.00', '2024-08-19', 'efectivo', ''),
(2, 10, '8000.00', '2024-08-23', 'efectivo', 'Pagado'),
(3, 8, '6000.00', '2024-08-23', 'efectivo', 'Pagado');

-- --------------------------------------------------------

--
-- Table structure for table `payment_distributions`
--

CREATE TABLE `payment_distributions` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1, 'users_view', 'Ver usuarios'),
(2, 'users_create', 'Crear usuarios'),
(3, 'users_edit', 'Editar usuarios'),
(4, 'users_delete', 'Eliminar usuarios'),
(5, 'products_view', 'Ver productos'),
(6, 'products_create', 'Crear productos'),
(7, 'products_edit', 'Editar productos'),
(8, 'products_delete', 'Eliminar productos'),
(9, 'categories_view', 'Ver categorías'),
(10, 'categories_create', 'Crear categorías'),
(11, 'categories_edit', 'Editar categorías'),
(12, 'categories_delete', 'Eliminar categorías'),
(13, 'sales_view', 'Ver ventas'),
(14, 'sales_create', 'Crear ventas'),
(15, 'sales_edit', 'Editar ventas'),
(16, 'sales_cancel', 'Cancelar ventas'),
(17, 'reports_view', 'Ver reportes'),
(18, 'reports_generate', 'Generar reportes'),
(19, 'roles_manage', 'Gestionar roles y permisos'),
(20, 'suppliers_view', 'Ver proveedores'),
(21, 'suppliers_create', 'Crear proveedores'),
(22, 'suppliers_edit', 'Editar proveedores'),
(23, 'suppliers_delete', 'Eliminar proveedores'),
(24, 'customers_view', 'Ver clientes'),
(25, 'customers_create', 'Crear clientes'),
(26, 'customers_edit', 'Editar clientes'),
(27, 'customers_delete', 'Eliminar clientes'),
(28, 'reservations_view', 'Ver reservas'),
(29, 'reservations_create', 'Crear reservas'),
(30, 'reservations_edit', 'Editar reservas'),
(31, 'reservations_delete', 'Eliminar reservas'),
(32, 'reservations_confirm', 'Confirmar reservas'),
(33, 'reservations_cancel', 'Cancelar reservas'),
(34, 'reservations_convert', 'Convertir reservas a ventas'),
(35, 'promotions_view', 'Ver promociones'),
(36, 'promotions_create', 'Crear promociones'),
(37, 'promotions_edit', 'Editar promociones'),
(38, 'promotions_delete', 'Eliminar promociones'),
(39, 'promotions_apply', 'Aplicar promociones'),
(40, 'inventory_view', 'Ver inventario'),
(41, 'inventory_view_movements', 'Ver Movimientos de Inventario'),
(42, 'inventory_view_low_stock', 'Ver Inventario Bajo Stock'),
(43, 'inventory_update', 'Actualizar inventario'),
(44, 'inventory_adjust', 'Ajustar inventario'),
(45, 'purchases_view', 'Ver compras'),
(46, 'purchases_create', 'Crear compras'),
(47, 'purchases_edit', 'Editar compras'),
(48, 'purchases_delete', 'Eliminar compras'),
(49, 'purchases_receive', 'Recibir compras'),
(50, 'purchases_view_movements', 'Ver movimientos de compras'),
(51, 'settings_view', 'Ver configuración del sistema'),
(52, 'settings_edit', 'Editar configuración del sistema'),
(53, 'company_view', 'Ver Configración de la Empresa'),
(54, 'audit_view', 'Ver registros de auditoría'),
(55, 'backup_create', 'Crear copias de seguridad'),
(56, 'backup_restore', 'Restaurar copias de seguridad'),
(57, 'backup_delete', 'Eliminar copias de seguridad'),
(58, 'backup_download', 'Descargar copias de seguridad'),
(59, 'budget_view', 'Ver presupuestos'),
(60, 'budget_create', 'Crear presupuestos'),
(61, 'budget_edit', 'Editar presupuestos'),
(62, 'budget_delete', 'Eliminar Presupuestos'),
(63, 'cash_register_manage', 'Gestionar caja (abrir, cerrar, ver movimientos)'),
(64, 'cash_register_open', 'Abrir caja'),
(65, 'cash_register_close', 'Cerrar caja'),
(66, 'cash_register_movement', 'Registrar movimientos de caja'),
(67, 'customer_accounts_view', 'Ver cuentas de clientes'),
(68, 'customer_accounts_adjust', 'Ajustar cuentas de clientes'),
(69, 'payments_view', 'Ver pagos'),
(70, 'payments_create', 'Crear pagos'),
(71, 'payments_edit', 'Editar pagos'),
(72, 'payments_delete', 'Eliminar pagos'),
(73, 'services_view', 'Ver órdenes de servicio'),
(74, 'services_create', 'Crear órdenes de servicio'),
(75, 'services_edit', 'Editar órdenes de servicio'),
(76, 'services_delete', 'Eliminar órdenes de servicio'),
(77, 'services_update_status', 'Actualizar estado de órdenes de servicio'),
(78, 'home_visits_view', 'Ver visitas a domicilio'),
(79, 'home_visits_create', 'Crear visitas a domicilio'),
(80, 'home_visits_edit', 'Editar visitas a domicilio'),
(81, 'home_visits_delete', 'Eliminar visitas a domicilio'),
(82, 'budget_change_status', 'Cambiar estado del Presupuesto'),
(83, 'calendar_view', 'Ver calendario'),
(84, 'company_settings_view', 'Configurar Datos Empresa');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `sku`, `category_id`, `price`, `cost_price`, `stock_quantity`, `min_stock`, `max_stock`, `reorder_level`, `supplier_id`, `created_at`) VALUES
(1, 'Asistencia 2', 'Asistencia general.', '000022', 6, '2000.00', '0.00', -1, 10, 200, 50, 1, '2024-08-17 14:58:49'),
(2, 'Cambio Modulo', 'Mano de obra cambio de módulos', '00020', 6, '60000.00', '21000.00', 0, 5, 100, 2, 1, '2024-08-17 14:58:49'),
(3, 'Cambio Pin de Carga Tipo V8', 'Mano de Obra cambio pin de carga', '00016', 6, '12000.00', '4000.00', 0, 5, 100, 2, 1, '2024-08-17 14:58:49'),
(4, 'Cambio Pin de Carga Tipo C', 'Mano de obra cambio pin de carga', '00017', 6, '14000.00', '6000.00', 0, 5, 100, 2, 1, '2024-08-17 14:58:49'),
(5, 'Carga Saldo', 'Carga Personal-Claro', '00018', 3, '1000.00', '100.00', -4, 5, 100, 50, 1, '2024-08-17 14:58:49'),
(6, 'Celular Reacondicionado', 'Celular reacondicionado con garantía', '00010', 13, '100000.00', '0.00', 0, 5, 100, 2, 1, '2024-08-17 14:58:49'),
(7, 'Chip Personal/Claro', 'Chip prepago Personal/Claro', '00011', 14, '1000.00', '0.00', -1, 5, 100, 2, 1, '2024-08-17 14:58:49'),
(8, 'Cámara Domo Exterior Motorizado', 'Cámara domo motorizado exterior', '00012', 9, '85000.00', '42000.00', 0, 5, 100, 1, 1, '2024-08-17 14:58:49'),
(9, 'Desbloqueo Netbook', 'Mano de obra desbloqueo Netbook', '00013', 6, '20000.00', '9000.00', 0, 5, 100, 2, 1, '2024-08-17 14:58:49'),
(10, 'Instalación Cámaras', 'Mano de Obra Instalación', '00002', 6, '10000.00', '0.00', 0, 5, 100, 2, 1, '2024-08-17 14:58:49'),
(11, 'Instalación S.O.', 'Mano de obra instalación Sistemas Operativos', '00004', 6, '25000.00', '0.00', 0, 5, 100, 2, 1, '2024-08-17 14:58:49'),
(12, 'Mano de Obra Instalación', 'Instalación de cámaras de seguridad puesta a punto.', '34234', 6, '15000.00', '0.00', -1, 5, 100, 5, 1, '2024-08-17 14:58:49'),
(13, 'Armado PC + Instalación Sistema Operativo', 'Mano de obra armado e instalación', '231231231', 6, '25000.00', '0.00', 0, 5, 100, 2, 1, '2024-08-17 14:58:49'),
(14, 'Diagnóstico TV', 'Desarmado testeo armado diagnóstico.', '34332144444', 6, '5000.00', '0.00', 0, 5, 100, 2, 1, '2024-08-17 14:58:49'),
(15, 'F4 Señal CF', 'F4 para consumidor final', '67454665', 3, '25000.00', '0.00', 0, 5, 100, 3, 1, '2024-08-17 14:58:49'),
(16, 'F4 Señal Técnicos', 'F4 para técnicos', '43534537', 3, '20000.00', '0.00', 0, 5, 100, 3, 1, '2024-08-17 14:58:49'),
(17, 'FRP CF', 'FRP para consumidor final', '87978', 3, '15000.00', '0.00', 0, 5, 100, 3, 1, '2024-08-17 14:58:49'),
(18, 'FRP Técnicos', 'FRP para técnicos', '67467', 3, '8000.00', '0.00', 0, 5, 100, 3, 1, '2024-08-17 14:58:49'),
(19, 'Asistencia 1', 'Asistencia', '0000011', 6, '1000.00', '0.00', -3, 5, 100, 5, 1, '2024-08-17 14:58:49'),
(20, 'Protector Glass', 'Glass protector templado', '213125432', 2, '3000.00', '600.00', 35, 5, 100, 10, 2, '2024-08-17 15:11:01'),
(21, 'Cargador Turbo Tipo V8', 'Cargador turbo V8', '0723540565661', 1, '5000.00', '3000.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(22, 'Auricular Manos Libres', 'Tipo Samsung', '65675', 8, '3500.00', '2000.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(23, 'Cabezal Cargador Turbo', 'Cabezal cargador conector tipo C', '7790839914267', 1, '4000.00', '2000.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(24, 'Cable Auxiliar', 'Cable tipo auxiliar', '00021', 7, '3000.00', '1000.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(25, 'Cable Iphone Tipo C ', 'Cable para Iphone 1ra calidad', '190198914491', 7, '5000.00', '2000.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(26, 'Cable USB Tipo C', 'Cable USB Tipo C 1ra Calidad', '8945637653477', 7, '3500.00', '2000.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(27, 'Cable USB tipo V8', 'Cable USB V8 1ra Calidad', '7985461188968', 7, '3500.00', '2000.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(28, 'Cargador 12v 1ra Calidad s/cables ', 'Cargador auto', '0011', 1, '4500.00', '2000.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(29, 'Cargador 12v c/cable Tipo C ', 'Cagador auto', '7796350508602', 1, '5000.00', '2000.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(30, 'Cargador 12v Samsung c/cable V8', 'Cargador auto', '6958784248757', 1, '5000.00', '2500.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(31, 'Cargador Turbo Tipo C', 'Cargador 1ra calidad turbo tipo C', '0723540566286', 1, '5500.00', '2500.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(32, 'Micro SD 32gb', 'Micro SD 1ra calidad', '00005', 10, '15000.00', '7000.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(33, 'OTG Tipo C', 'Conector OTG', '6983646411987', 7, '3000.00', '1500.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(34, 'OTG Tipo V8', 'Conector OTG', '7893646417898', 7, '3000.00', '1500.00', 0, 5, 100, 2, 8, '2024-08-17 15:11:01'),
(35, 'Protector TPU Varios', 'Protectores para celulares', '00006', 14, '6000.00', '3000.00', 0, 5, 100, 2, 2, '2024-08-17 15:11:01'),
(36, 'Camara Seguridad Hikvision', 'Full Hd 1080p 16d0t-exipf Ext 2.8', '8078686', 9, '41500.00', '34000.00', 0, 5, 100, 2, 9, '2024-08-17 15:11:01'),
(37, 'Grabadora Dvr Hikvision 8 Canales', '8can+2IP Turbo Hd 720p', '35635638', 9, '110000.00', '90000.00', 0, 5, 100, 1, 9, '2024-08-17 15:11:01'),
(38, 'Fuente 12v 2a', 'Fuente Alimentacion Camaras Cctv Dvr', '231243', 1, '7500.00', '3500.00', 0, 5, 100, 2, 9, '2024-08-17 15:11:01'),
(39, 'Fuente 12v 4a', 'Alimentacion Camaras Cctv Dvr', '23423', 1, '9000.00', '4500.00', 0, 5, 100, 2, 9, '2024-08-17 15:11:01'),
(40, 'Splitter Pulpo Alimentación 1x4', '1x4 Cctv Cámaras', '2321312', 7, '3000.00', '1500.00', 0, 5, 100, 2, 9, '2024-08-17 15:11:02'),
(41, 'Splitter Pulpo Alimentación 1x8', '1x8 Cctv Cámaras', '34425234', 7, '6000.00', '2000.00', 0, 5, 100, 2, 9, '2024-08-17 15:11:02'),
(42, 'Cable UTP 50m exterior', '50m UTP cat5', '344534', 7, '30000.00', '15000.00', 0, 5, 100, 2, 9, '2024-08-17 15:11:02'),
(43, 'Cable UTP 100m exterior', '100m UTP cat5', '34132412', 7, '53000.00', '30000.00', 0, 5, 100, 2, 9, '2024-08-17 15:11:02'),
(44, 'Pack Par Balun + Plug Macho + Hembra', 'Conector video calidad HD', '341231', 7, '4500.00', '2500.00', 0, 5, 100, 5, 9, '2024-08-17 15:11:02'),
(45, 'Estanca 80x80x50', 'Caja Plastica De Paso', '234214', 14, '5000.00', '2500.00', 0, 5, 100, 2, 9, '2024-08-17 15:11:02'),
(46, 'Disco duro 1TB', 'Para DVR / PC', '342342', 10, '95000.00', '70000.00', 0, 5, 100, 1, 9, '2024-08-17 15:11:02'),
(47, 'Fuente 12v 5a', 'Homologada Certificada para CCTV', '324132', 1, '12000.00', '6000.00', 0, 5, 100, 1, 9, '2024-08-17 15:11:02'),
(48, 'Auricular Samsung', 'Auricular Samsung 1ra calidad en caja', '2312312', 8, '5000.00', '3500.00', 0, 5, 100, 3, 8, '2024-08-17 15:11:02'),
(49, 'Celular Samsung J2 Prime Reacondicionado', 'celular reacondicionado con garantía', '21312413', 13, '60000.00', '0.00', 0, 5, 100, 2, 1, '2024-08-17 17:48:53'),
(50, 'Placa de carga', 'Cambio placa de carga', '3564', 16, '15000.00', '5000.00', 0, 5, 100, 2, 4, '2024-08-17 18:07:20'),
(51, 'Batería Samsung', 'Baterias Originales', '23621967902178', 15, '18000.00', '8600.00', 0, 5, 100, 2, 7, '2024-08-17 21:55:55'),
(52, 'Android TV Box', 'Android TV Box configurados', '453246247', 14, '70000.00', '35000.00', 0, 5, 100, 1, 8, '2024-08-17 22:21:15'),
(53, 'Celular Samsung A12 Reacondicionado', 'Celular reacondicionado con garantía', '3471893743\'12', 13, '80000.00', '0.00', 0, 5, 100, 0, 1, '2024-08-17 22:22:45'),
(54, 'Samsung A50 Reacondicionado', 'Celular Samsung A5o reacondicionado con garantía', '327612936w01', 13, '100000.00', '0.00', 0, 5, 100, 0, 1, '2024-08-18 01:44:18'),
(55, 'Bandeja Porta Sim', 'Bandeja porta SIM varios modelos', '34723842080', 14, '8000.00', '0.00', 0, 5, 100, 2, 4, '2024-08-19 19:24:06'),
(56, 'Batería Motorola', 'Baterias Originales', '2352376875', 15, '25000.00', '8600.00', 0, 5, 100, 2, 7, '2024-08-21 15:09:21'),
(57, 'Modulo Samsung', 'Modulos originales 1ra calidad.', '20000', 11, '25000.00', '20000.00', 0, 5, 100, 1, 3, '2024-08-21 23:30:09'),
(58, 'Módulo Motorola', 'Módulos originales', '23424523423', 11, '50000.00', '20000.00', 3, 5, 100, 1, 6, '2024-08-26 14:02:56'),
(59, 'Bateria Alcatel', 'Bateria Original', '52362562', 15, '28000.00', '15000.00', 0, 5, 100, 2, 1, '2024-08-26 14:42:47');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `discount_type` enum('porcentaje','fijo') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `purchase_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pendiente','recibido','cancelado') DEFAULT 'pendiente',
  `received_date` timestamp NULL DEFAULT NULL,
  `cash_register_session_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `supplier_id`, `user_id`, `purchase_date`, `total_amount`, `status`, `received_date`, `cash_register_session_id`) VALUES
(1, 7, 1, '2024-08-12 08:08:05', '15200.00', 'recibido', '2024-08-12 08:09:35', 1),
(2, 10, 1, '2024-08-12 08:08:43', '2000.00', 'recibido', '2024-08-12 08:08:50', 1),
(3, 3, 1, '2024-08-19 15:38:10', '18000.00', 'recibido', '2024-08-19 15:38:50', 7),
(4, 9, 1, '2024-08-19 23:20:50', '42000.00', 'recibido', '2024-08-19 23:20:56', 7),
(5, 3, 1, '2024-08-19 23:22:27', '20000.00', 'recibido', '2024-08-19 23:22:30', 7),
(6, 7, 1, '2024-08-21 15:09:42', '8600.00', 'recibido', '2024-08-21 23:33:17', 9),
(7, 6, 1, '2024-08-21 15:10:05', '21000.00', 'recibido', '2024-08-21 23:33:23', 9),
(8, 3, 1, '2024-08-21 23:33:14', '20000.00', 'recibido', '2024-08-21 23:33:29', 9),
(9, 2, 1, '2024-08-24 13:33:24', '0.00', 'recibido', '2024-08-24 13:33:31', 12),
(10, 6, 1, '2024-08-26 14:18:08', '18600.00', 'recibido', '2024-08-26 14:18:14', 13),
(11, 3, 1, '2024-08-27 23:13:54', '18000.00', 'recibido', '2024-08-27 23:13:57', 14);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `received_quantity` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_id`, `product_id`, `quantity`, `price`, `received_quantity`) VALUES
(2, 1, 51, 2, '7600.00', 2),
(3, 2, 5, 20, '100.00', 20),
(4, 3, 2, 1, '18000.00', 1),
(5, 4, 8, 1, '42000.00', 1),
(6, 5, 2, 1, '20000.00', 1),
(7, 6, 56, 1, '8600.00', 1),
(8, 7, 2, 1, '21000.00', 1),
(9, 8, 57, 1, '20000.00', 1),
(10, 9, 20, 35, '0.00', 35),
(11, 10, 58, 1, '18600.00', 1),
(12, 11, 58, 1, '18000.00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_movements`
--

CREATE TABLE `purchase_movements` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movement_type` enum('creacion','recepcion','cancelacion','modificacion') NOT NULL,
  `details` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `purchase_movements`
--

INSERT INTO `purchase_movements` (`id`, `purchase_id`, `user_id`, `movement_type`, `details`, `created_at`) VALUES
(2, 1, 1, 'creacion', 'Compra creada', '2024-08-17 22:08:05'),
(3, 1, 1, 'recepcion', 'Compra recibida', '2024-08-17 22:08:11'),
(4, 2, 1, 'creacion', 'Compra creada', '2024-08-17 22:08:43'),
(5, 2, 1, 'recepcion', 'Compra recibida', '2024-08-17 22:08:50'),
(6, 3, 1, 'creacion', 'Compra creada', '2024-08-19 15:38:10'),
(7, 3, 1, 'recepcion', 'Compra recibida', '2024-08-19 15:38:50'),
(8, 4, 1, 'creacion', 'Compra creada', '2024-08-19 23:20:50'),
(9, 4, 1, 'recepcion', 'Compra recibida', '2024-08-19 23:20:56'),
(10, 5, 1, 'creacion', 'Compra creada', '2024-08-19 23:22:27'),
(11, 5, 1, 'recepcion', 'Compra recibida', '2024-08-19 23:22:30'),
(12, 6, 1, 'creacion', 'Compra creada', '2024-08-21 15:09:42'),
(13, 7, 1, 'creacion', 'Compra creada', '2024-08-21 15:10:05'),
(14, 8, 1, 'creacion', 'Compra creada', '2024-08-21 23:33:14'),
(15, 6, 1, 'recepcion', 'Compra recibida', '2024-08-21 23:33:17'),
(16, 7, 1, 'recepcion', 'Compra recibida', '2024-08-21 23:33:23'),
(17, 8, 1, 'recepcion', 'Compra recibida', '2024-08-21 23:33:29'),
(18, 9, 1, 'creacion', 'Compra creada', '2024-08-24 13:33:24'),
(19, 9, 1, 'recepcion', 'Compra recibida. Total actualizado: $0.00', '2024-08-24 13:33:31'),
(20, 10, 1, 'creacion', 'Compra creada', '2024-08-26 14:18:08'),
(21, 10, 1, 'recepcion', 'Compra recibida. Total actualizado: $18,600.00', '2024-08-26 14:18:14'),
(22, 11, 1, 'creacion', 'Compra creada', '2024-08-27 23:13:54'),
(23, 11, 1, 'recepcion', 'Compra recibida. Total actualizado: $18,000.00', '2024-08-27 23:13:57');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `reservation_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pendiente','confirmado','cancelado','convertido') DEFAULT 'pendiente',
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `reservation_items`
--

CREATE TABLE `reservation_items` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Administrador'),
(3, 'Cajero'),
(2, 'Gerente'),
(5, 'Técnico-Vendedor'),
(4, 'Vendedor');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(2, 1),
(1, 2),
(2, 2),
(1, 3),
(2, 3),
(1, 4),
(2, 4),
(1, 5),
(2, 5),
(4, 5),
(1, 6),
(2, 6),
(4, 6),
(1, 7),
(2, 7),
(4, 7),
(1, 8),
(1, 9),
(2, 9),
(4, 9),
(1, 10),
(2, 10),
(4, 10),
(1, 11),
(2, 11),
(4, 11),
(1, 12),
(1, 13),
(2, 13),
(4, 13),
(1, 14),
(2, 14),
(4, 14),
(1, 15),
(2, 15),
(4, 15),
(1, 16),
(2, 16),
(1, 17),
(2, 17),
(1, 18),
(2, 18),
(1, 19),
(1, 20),
(2, 20),
(4, 20),
(1, 21),
(2, 21),
(4, 21),
(1, 22),
(2, 22),
(4, 22),
(1, 23),
(1, 24),
(2, 24),
(4, 24),
(5, 24),
(1, 25),
(2, 25),
(4, 25),
(5, 25),
(1, 26),
(2, 26),
(4, 26),
(5, 26),
(1, 27),
(1, 28),
(2, 28),
(4, 28),
(1, 29),
(2, 29),
(4, 29),
(1, 30),
(2, 30),
(4, 30),
(1, 31),
(2, 31),
(1, 32),
(2, 32),
(4, 32),
(1, 33),
(2, 33),
(4, 33),
(1, 34),
(2, 34),
(4, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(2, 40),
(4, 40),
(5, 40),
(1, 41),
(1, 42),
(1, 43),
(1, 44),
(1, 45),
(2, 45),
(1, 46),
(2, 46),
(1, 47),
(2, 47),
(1, 48),
(1, 49),
(2, 49),
(1, 50),
(1, 51),
(5, 51),
(1, 52),
(1, 54),
(1, 55),
(2, 55),
(5, 55),
(1, 56),
(1, 57),
(1, 58),
(1, 59),
(2, 59),
(4, 59),
(1, 60),
(2, 60),
(4, 60),
(1, 61),
(2, 61),
(4, 61),
(1, 62),
(2, 62),
(1, 63),
(2, 63),
(4, 63),
(1, 64),
(2, 64),
(3, 64),
(4, 64),
(1, 65),
(2, 65),
(3, 65),
(4, 65),
(1, 66),
(2, 66),
(3, 66),
(4, 66),
(1, 67),
(2, 67),
(4, 67),
(1, 68),
(2, 68),
(4, 68),
(1, 69),
(2, 69),
(4, 69),
(1, 70),
(2, 70),
(4, 70),
(1, 71),
(2, 71),
(4, 71),
(1, 72),
(1, 73),
(2, 73),
(5, 73),
(1, 74),
(2, 74),
(5, 74),
(1, 75),
(2, 75),
(5, 75),
(1, 76),
(2, 76),
(1, 77),
(2, 77),
(5, 77),
(1, 78),
(5, 78),
(1, 79),
(5, 79),
(1, 80),
(5, 80),
(1, 81),
(1, 82),
(1, 83),
(5, 83),
(1, 84);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `sale_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('efectivo','tarjeta','transferencia','credito','otros') NOT NULL,
  `status` enum('completado','cancelado') DEFAULT 'completado',
  `is_credit` tinyint(1) DEFAULT '0',
  `amount_paid` decimal(10,2) DEFAULT '0.00',
  `balance` decimal(10,2) DEFAULT '0.00',
  `cash_register_session_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `customer_id`, `user_id`, `sale_date`, `total_amount`, `payment_method`, `status`, `is_credit`, `amount_paid`, `balance`, `cash_register_session_id`) VALUES
(1, 1, 1, '2024-08-12 17:41:48', '3000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 1),
(2, 1, 1, '2024-08-12 17:46:44', '1000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 1),
(3, 1, 1, '2024-08-12 17:47:03', '2200.00', 'efectivo', 'completado', 0, '0.00', '0.00', 1),
(4, 1, 1, '2024-08-12 17:49:12', '60000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 1),
(6, 1, 1, '2024-08-12 18:04:50', '12000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 1),
(7, 1, 1, '2024-08-12 18:07:35', '15000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 1),
(8, 1, 1, '2024-08-13 10:36:03', '3500.00', 'efectivo', 'completado', 0, '0.00', '0.00', 2),
(9, 1, 1, '2024-08-13 10:36:24', '70000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 2),
(10, 1, 1, '2024-08-13 11:36:32', '3500.00', 'efectivo', 'completado', 0, '0.00', '0.00', 2),
(11, 1, 1, '2024-08-13 19:36:37', '80000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 2),
(12, 1, 1, '2024-08-14 08:06:26', '5000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 3),
(13, 1, 1, '2024-08-14 08:06:47', '1000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 3),
(14, 1, 1, '2024-08-14 09:07:15', '1000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 3),
(15, 1, 1, '2024-08-14 10:07:38', '3000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 3),
(16, 1, 1, '2024-08-15 11:23:29', '2500.00', 'efectivo', 'completado', 0, '0.00', '0.00', 4),
(18, 1, 1, '2024-08-15 11:39:09', '18000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 4),
(19, 1, 1, '2024-08-16 11:41:44', '20000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 5),
(20, 1, 1, '2024-08-16 11:42:02', '1000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 5),
(21, 1, 1, '2024-08-16 12:42:27', '3300.00', 'efectivo', 'completado', 0, '0.00', '0.00', 5),
(22, 1, 1, '2024-08-16 12:42:45', '2200.00', 'efectivo', 'completado', 0, '0.00', '0.00', 5),
(23, 1, 1, '2024-08-16 13:50:20', '100000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 5),
(24, 1, 1, '2024-08-16 13:44:56', '12000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 5),
(25, 1, 1, '2024-08-16 14:50:32', '2500.00', 'efectivo', 'completado', 0, '0.00', '0.00', 5),
(26, 1, 1, '2024-08-16 16:45:30', '2500.00', 'efectivo', 'completado', 0, '0.00', '0.00', 5),
(27, 1, 1, '2024-08-16 17:45:45', '5000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 5),
(28, 1, 1, '2024-08-16 17:46:00', '2500.00', 'efectivo', 'completado', 0, '0.00', '0.00', 5),
(29, 1, 1, '2024-08-16 17:46:10', '2500.00', 'efectivo', 'completado', 0, '0.00', '0.00', 5),
(30, 1, 1, '2024-08-17 10:56:26', '8000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 6),
(31, 1, 1, '2024-08-17 11:54:58', '1000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 6),
(32, 1, 1, '2024-08-19 11:23:30', '5500.00', 'efectivo', 'completado', 0, '0.00', '0.00', 7),
(33, 1, 1, '2024-08-19 11:25:50', '65000.00', 'transferencia', 'completado', 0, '0.00', '0.00', 7),
(34, 1, 1, '2024-08-19 20:05:52', '8000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 7),
(35, 1, 1, '2024-08-19 21:09:55', '4000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 7),
(36, 1, 1, '2024-08-19 21:58:22', '3300.00', 'efectivo', 'completado', 0, '0.00', '0.00', 7),
(37, 1, 1, '2024-08-19 22:16:55', '1500.00', 'efectivo', 'completado', 0, '0.00', '0.00', 7),
(38, 4, 1, '2024-08-19 22:17:53', '115000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 7),
(39, 1, 1, '2024-08-19 22:30:15', '90000.00', 'tarjeta', 'completado', 0, '0.00', '0.00', 7),
(40, 1, 1, '2024-08-20 13:09:40', '5500.00', 'efectivo', 'completado', 0, '0.00', '0.00', 8),
(41, 1, 1, '2024-08-20 13:48:35', '3300.00', 'efectivo', 'completado', 0, '0.00', '0.00', 8),
(42, 1, 1, '2024-08-20 13:53:31', '2200.00', 'efectivo', 'completado', 0, '0.00', '0.00', 8),
(43, 1, 1, '2024-08-20 13:53:57', '12000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 8),
(44, 1, 1, '2024-08-20 14:30:41', '5000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 8),
(45, 1, 1, '2024-08-20 22:27:47', '14000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 8),
(46, 1, 1, '2024-08-20 22:47:32', '10000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 8),
(47, 1, 1, '2024-08-21 01:20:17', '10000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 8),
(48, 1, 1, '2024-08-21 15:04:10', '20000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 9),
(49, 1, 1, '2024-08-21 15:06:45', '60000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 9),
(50, 1, 1, '2024-08-21 20:38:40', '500.00', 'efectivo', 'completado', 0, '0.00', '0.00', 9),
(51, 1, 1, '2024-08-21 21:24:15', '4000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 9),
(52, 1, 1, '2024-08-22 12:54:15', '1600.00', 'efectivo', 'completado', 0, '0.00', '0.00', 10),
(53, 1, 1, '2024-08-22 20:46:33', '3300.00', 'efectivo', 'completado', 0, '0.00', '0.00', 10),
(54, 1, 1, '2024-08-22 22:00:38', '6000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 10),
(55, 1, 1, '2024-08-22 22:02:11', '8000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 10),
(56, 1, 1, '2024-08-22 22:55:14', '4000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 10),
(57, 1, 1, '2024-08-22 23:09:10', '28000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 10),
(58, 10, 1, '2024-08-23 02:02:55', '8000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 10),
(59, 1, 1, '2024-08-23 19:39:14', '5000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 11),
(60, 8, 1, '2024-08-23 22:15:40', '6000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 11),
(61, 1, 1, '2024-08-23 23:04:56', '2000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 11),
(62, 1, 1, '2024-08-23 23:09:36', '25000.00', 'transferencia', 'completado', 0, '0.00', '0.00', 11),
(63, 1, 1, '2024-08-24 13:34:42', '10000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 12),
(64, 1, 1, '2024-08-26 13:10:18', '3300.00', 'efectivo', 'completado', 0, '0.00', '0.00', 13),
(65, 1, 1, '2024-08-26 13:17:37', '3300.00', 'efectivo', 'completado', 0, '0.00', '0.00', 13),
(66, 1, 1, '2024-08-26 14:43:08', '28000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 13),
(67, 1, 1, '2024-08-26 20:48:06', '4000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 13),
(68, 1, 1, '2024-08-26 21:18:24', '1100.00', 'efectivo', 'completado', 0, '0.00', '0.00', 13),
(69, 1, 1, '2024-08-27 15:04:56', '25000.00', 'transferencia', 'completado', 0, '0.00', '0.00', 14),
(70, 1, 1, '2024-08-27 20:51:52', '1000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 14),
(71, 1, 1, '2024-08-27 20:55:27', '12000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 14),
(72, 1, 1, '2024-08-27 21:39:43', '1000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 14),
(73, 1, 1, '2024-08-28 12:51:44', '15000.00', 'efectivo', 'completado', 0, '0.00', '0.00', 15);

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 5, 1, '1000.00'),
(2, 2, 7, 2, '500.00'),
(3, 3, 5, 2, '1000.00'),
(4, 4, 49, 1, '60000.00'),
(6, 6, 3, 1, '12000.00'),
(7, 7, 50, 1, '15000.00'),
(8, 8, 22, 1, '3500.00'),
(9, 9, 52, 1, '70000.00'),
(10, 10, 27, 1, '3500.00'),
(11, 11, 53, 1, '80000.00'),
(12, 12, 21, 1, '5000.00'),
(13, 13, 5, 1, '1000.00'),
(14, 14, 7, 2, '500.00'),
(15, 15, 5, 1, '1000.00'),
(16, 16, 20, 1, '2500.00'),
(18, 18, 1, 1, '2000.00'),
(19, 19, 16, 1, '20000.00'),
(20, 20, 19, 1, '1000.00'),
(21, 21, 5, 1, '1000.00'),
(22, 22, 5, 2, '1000.00'),
(23, 23, 54, 1, '100000.00'),
(24, 24, 3, 1, '12000.00'),
(25, 25, 20, 1, '2500.00'),
(26, 26, 20, 1, '2500.00'),
(27, 27, 48, 1, '5000.00'),
(28, 28, 20, 1, '2500.00'),
(29, 29, 20, 1, '2500.00'),
(30, 30, 1, 1, '2000.00'),
(31, 31, 19, 1, '1000.00'),
(32, 32, 31, 1, '5500.00'),
(33, 33, 2, 1, '65000.00'),
(40, 34, 55, 1, '8000.00'),
(41, 35, 7, 1, '1000.00'),
(42, 35, 5, 1, '1000.00'),
(43, 36, 5, 1, '1000.00'),
(44, 37, 19, 2, '1000.00'),
(45, 38, 8, 1, '85000.00'),
(46, 38, 32, 1, '15000.00'),
(47, 38, 12, 1, '15000.00'),
(48, 39, 2, 1, '90000.00'),
(49, 40, 31, 1, '5500.00'),
(50, 41, 5, 1, '1000.00'),
(51, 42, 5, 1, '1000.00'),
(52, 43, 3, 1, '12000.00'),
(53, 44, 20, 2, '2500.00'),
(54, 45, 4, 1, '14000.00'),
(55, 46, 19, 5, '1000.00'),
(56, 46, 29, 1, '5000.00'),
(57, 47, 1, 1, '2000.00'),
(58, 48, 9, 1, '20000.00'),
(59, 49, 2, 1, '60000.00'),
(60, 50, 5, 1, '1000.00'),
(61, 51, 7, 1, '1000.00'),
(62, 51, 5, 1, '1000.00'),
(63, 52, 5, 2, '1000.00'),
(64, 53, 5, 1, '1000.00'),
(65, 54, 1, 1, '2000.00'),
(66, 55, 20, 1, '3000.00'),
(67, 55, 1, 1, '2000.00'),
(68, 56, 7, 1, '1000.00'),
(69, 56, 5, 1, '1000.00'),
(70, 57, 57, 1, '25000.00'),
(71, 57, 20, 1, '3000.00'),
(72, 58, 18, 1, '8000.00'),
(73, 59, 19, 1, '5000.00'),
(74, 60, 3, 1, '6000.00'),
(75, 61, 1, 1, '2000.00'),
(76, 62, 56, 1, '25000.00'),
(77, 63, 52, 1, '10000.00'),
(78, 64, 5, 1, '3300.00'),
(79, 65, 5, 1, '3300.00'),
(80, 66, 59, 1, '28000.00'),
(81, 67, 7, 1, '1000.00'),
(82, 67, 5, 1, '3000.00'),
(83, 68, 5, 1, '1100.00'),
(84, 69, 19, 1, '25000.00'),
(85, 70, 19, 1, '1000.00'),
(86, 71, 1, 1, '12000.00'),
(87, 72, 19, 1, '1000.00'),
(88, 73, 12, 1, '15000.00');

-- --------------------------------------------------------

--
-- Table structure for table `sale_payments`
--

CREATE TABLE `sale_payments` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sale_payments`
--

INSERT INTO `sale_payments` (`id`, `sale_id`, `payment_id`, `amount`, `payment_date`) VALUES
(1, 38, 1, '115000.00', '2024-08-20 02:26:31'),
(2, 58, 2, '8000.00', '2024-08-23 22:22:52'),
(3, 60, 3, '6000.00', '2024-08-24 02:22:47');

-- --------------------------------------------------------

--
-- Table structure for table `service_devices`
--

CREATE TABLE `service_devices` (
  `id` int(11) NOT NULL,
  `service_order_id` int(11) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_devices`
--

INSERT INTO `service_devices` (`id`, `service_order_id`, `brand`, `model`, `serial_number`) VALUES
(1, 1, 'Motorla', 'E7i Power', '355969783852038'),
(2, 2, 'Xiaomi', 'Mi A2 lite', '35xxxxxxxxxxxxx'),
(3, 3, 'Samsung', 'A04e', '35xxxxxxxxxxxxx'),
(4, 4, 'Samsung', 'J2 Prime', '352940096371951'),
(5, 5, 'LG', '22', '35xxxxxxxxxxxx'),
(6, 6, 'Alcatel', 'Idol', '35xxxxxxxxxxxx'),
(7, 7, 'LG', 'X230AR', '353885083914942'),
(8, 8, 'Motorola', 'G20', '35xxxxxxxxxxxx'),
(9, 9, 'Motorola', 'E22', '35xxxxxxxxxxxx'),
(10, 10, 'Educ.ar', 'Gobierno', 'AA5831018037'),
(11, 11, 'Motorola', 'G41', '357359551017840'),
(12, 12, 'Motorola', 'E6 Plus', '357238103660580'),
(13, 13, 'Motorola', 'E13', '35xxxxxxxxxxxx'),
(14, 14, 'Samsung', 'J8', '35xxxxxxxxxxxx'),
(15, 15, 'Samsung', 'A03', '35xxxxxxxxxxxx'),
(16, 16, 'Samsung', 'J7 2016', '359592077548207');

-- --------------------------------------------------------

--
-- Table structure for table `service_items`
--

CREATE TABLE `service_items` (
  `id` int(11) NOT NULL,
  `service_order_id` int(11) DEFAULT NULL,
  `description` text,
  `cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_items`
--

INSERT INTO `service_items` (`id`, `service_order_id`, `description`, `cost`) VALUES
(1, 1, 'Cambio de módulo', '50000.00'),
(2, 2, 'Cambio de módulo', '60000.00'),
(3, 3, 'Cambio de módulo', '50000.00'),
(4, 4, 'Cambio de Pin de carga', '12000.00'),
(5, 5, 'Cambio de Pin de carga', '12000.00'),
(6, 6, 'No enciende', '3000.00'),
(7, 7, 'FRP', '15000.00'),
(8, 8, 'Cambio de módulo', '60000.00'),
(9, 9, 'FRP', '15000.00'),
(10, 10, 'Desbloqueo', '30000.00'),
(11, 11, 'No enciende', '3000.00'),
(12, 12, 'Cambio de Módulo', '50000.00'),
(13, 13, 'Cambio de módulo', '50000.00'),
(14, 14, 'FRP', '15000.00'),
(15, 15, 'Cambio de módulo', '50000.00'),
(16, 16, 'Cambio de Placa Main', '30000.00');

-- --------------------------------------------------------

--
-- Table structure for table `service_orders`
--

CREATE TABLE `service_orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `status` enum('abierto','en_progreso','cerrado','cancelado') DEFAULT 'abierto',
  `warranty` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) DEFAULT '0.00',
  `prepaid_amount` decimal(10,2) DEFAULT '0.00',
  `balance` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_orders`
--

INSERT INTO `service_orders` (`id`, `order_number`, `customer_id`, `status`, `warranty`, `created_at`, `updated_at`, `total_amount`, `prepaid_amount`, `balance`) VALUES
(1, 'ORD20240821102933266', 2, 'abierto', 0, '2024-08-21 10:29:33', '2024-08-21 10:29:33', '50000.00', '0.00', '50000.00'),
(2, 'ORD20240821112807366', 3, 'cerrado', 0, '2024-08-21 11:28:07', '2024-08-22 18:56:18', '60000.00', '60000.00', '0.00'),
(3, 'ORD20240821165854541', 5, 'cerrado', 0, '2024-08-21 16:58:54', '2024-08-22 10:38:03', '50000.00', '25000.00', '25000.00'),
(4, 'ORD20240822114343680', 7, 'cerrado', 0, '2024-08-22 11:43:43', '2024-08-22 14:14:33', '12000.00', '10000.00', '2000.00'),
(5, 'ORD20240822175708609', 8, 'cerrado', 0, '2024-08-22 17:57:08', '2024-08-22 21:18:20', '12000.00', '6000.00', '6000.00'),
(6, 'ORD20240822193803245', 3, 'abierto', 0, '2024-08-22 19:38:03', '2024-08-22 19:38:03', '3000.00', '0.00', '3000.00'),
(7, 'ORD20240823171039994', 11, 'cerrado', 0, '2024-08-23 17:10:39', '2024-08-26 09:12:56', '15000.00', '0.00', '15000.00'),
(8, 'ORD20240826103935854', 12, 'cerrado', 0, '2024-08-26 10:39:35', '2024-08-26 12:18:25', '60000.00', '32000.00', '28000.00'),
(9, 'ORD20240826182506935', 13, 'cerrado', 0, '2024-08-26 18:25:06', '2024-08-26 19:30:20', '15000.00', '0.00', '15000.00'),
(10, 'ORD20240826182936164', 14, 'abierto', 0, '2024-08-26 18:29:36', '2024-08-26 18:29:36', '30000.00', '0.00', '30000.00'),
(11, 'ORD20240827084804664', 15, 'abierto', 0, '2024-08-27 08:48:04', '2024-08-27 08:48:04', '3000.00', '0.00', '3000.00'),
(12, 'ORD20240827112031799', 16, 'abierto', 0, '2024-08-27 11:20:31', '2024-08-27 11:20:31', '50000.00', '0.00', '50000.00'),
(13, 'ORD20240827181558269', 17, 'cerrado', 0, '2024-08-27 18:15:58', '2024-08-27 20:15:21', '50000.00', '30000.00', '20000.00'),
(14, 'ORD20240828111819229', 18, 'cerrado', 0, '2024-08-28 11:18:19', '2024-08-28 11:48:52', '15000.00', '0.00', '15000.00'),
(15, 'ORD20240828170025478', 19, 'abierto', 0, '2024-08-28 17:00:25', '2024-08-28 17:00:25', '50000.00', '0.00', '50000.00'),
(16, 'ORD20240828183012460', 20, 'abierto', 0, '2024-08-28 18:30:12', '2024-08-28 18:30:12', '30000.00', '0.00', '30000.00');

-- --------------------------------------------------------

--
-- Table structure for table `service_order_notes`
--

CREATE TABLE `service_order_notes` (
  `id` int(11) NOT NULL,
  `service_order_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `note` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_order_notes`
--

INSERT INTO `service_order_notes` (`id`, `service_order_id`, `user_id`, `note`, `created_at`, `image_path`) VALUES
(1, 1, 1, 'Reparación para fines de septiembre', '2024-08-21 13:49:52', NULL),
(2, 3, 1, 'Se cambió pin de carga y termistor', '2024-08-22 10:38:28', NULL),
(3, 7, 1, 'El cliente avisa por Whatsapp si se procesa.', '2024-08-23 17:11:15', NULL),
(4, 10, 1, 'Retira el 7 de septiembre.', '2024-08-26 18:31:36', NULL),
(5, 12, 1, 'Necesita cambio de módulo y posiblemente cambio de batería', '2024-08-27 11:21:10', NULL),
(6, 13, 1, 'Reparado', '2024-08-28 10:07:55', '/uploads/service_notes/66cf212b6cb51_1000042834.jpg'),
(7, 14, 1, 'Listo para retirar.', '2024-08-28 11:50:39', '/uploads/service_notes/66cf393fcc555_1000043151.jpg'),
(8, 15, 1, 'Abona Viernes y retira sábado', '2024-08-28 17:02:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_order_status_history`
--

CREATE TABLE `service_order_status_history` (
  `id` int(11) NOT NULL,
  `service_order_id` int(11) DEFAULT NULL,
  `status` enum('abierto','en_progreso','cerrado','cancelado') DEFAULT NULL,
  `changed_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `changed_by` int(11) DEFAULT NULL,
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_order_status_history`
--

INSERT INTO `service_order_status_history` (`id`, `service_order_id`, `status`, `changed_at`, `changed_by`, `notes`) VALUES
(1, 3, 'cerrado', '2024-08-22 10:38:03', 1, 'Reparado.'),
(2, 2, 'en_progreso', '2024-08-22 11:40:20', 1, ''),
(3, 4, 'cerrado', '2024-08-22 11:45:25', 1, 'Terminado'),
(4, 4, 'abierto', '2024-08-22 14:14:23', 1, ''),
(5, 4, 'cerrado', '2024-08-22 14:14:33', 1, ''),
(6, 2, 'cerrado', '2024-08-22 18:56:18', 1, 'Terminado'),
(7, 5, 'cerrado', '2024-08-22 21:18:20', 1, 'Reparado'),
(8, 7, 'cerrado', '2024-08-26 09:12:56', 1, 'Terminado'),
(9, 8, 'cerrado', '2024-08-26 12:18:25', 1, 'Terminado.'),
(10, 9, 'cerrado', '2024-08-26 19:30:20', 1, 'Terminado.'),
(11, 13, 'cerrado', '2024-08-27 20:15:21', 1, 'Reparado. Listo para retirar.'),
(12, 14, 'cerrado', '2024-08-28 11:48:52', 1, 'Terminado\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `service_parts`
--

CREATE TABLE `service_parts` (
  `id` int(11) NOT NULL,
  `service_order_id` int(11) DEFAULT NULL,
  `part_name` varchar(100) DEFAULT NULL,
  `part_number` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_parts`
--

INSERT INTO `service_parts` (`id`, `service_order_id`, `part_name`, `part_number`, `quantity`, `cost`) VALUES
(1, 4, 'Cubrelente Cámara', '0', 1, '4000.00'),
(2, 8, 'Glass', '00', 1, '3000.00');

-- --------------------------------------------------------

--
-- Table structure for table `service_terms`
--

CREATE TABLE `service_terms` (
  `id` int(11) NOT NULL,
  `content` text,
  `active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_terms`
--

INSERT INTO `service_terms` (`id`, `content`, `active`, `created_at`) VALUES
(1, 'Si el producto no fuera retirado dentro del término de 90 días a contar de la fecha de recepción del mismo por parte de Cellcom Technology, será considerado abandonado en término de los artículos 2375, 2525 y 2526 del código civil, quedando Cellcom Technology facultado a darle el destino que considere pertinente sin necesidad de informarlo previamente al cliente. La reparación efectuada cuenta con una GARANTÍA de 30 días corridos a partir de la fecha de entrega del PRODUCTO, tanto la mano de obra como el material empleado en esta GARANTÍA no ampara los defectos originados por el acarreo, transporte, incendio, inundaciones, tormentas eléctricas, golpes o accidentes de cualquier naturaleza. Asimismo la presente GARANTÍA quedará automáticamente cancelada cuando se efectúen intervenciones por terceros no autorizados. .', 1, '2024-08-20 19:13:54');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `app_name` varchar(100) NOT NULL DEFAULT 'Sistema POS',
  `timezone` varchar(100) NOT NULL DEFAULT 'America/Argentina/Buenos_Aires',
  `currency` varchar(10) NOT NULL DEFAULT 'ARS',
  `admin_email` varchar(100) NOT NULL,
  `items_per_page` int(11) NOT NULL DEFAULT '20',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.21',
  `logo_path` varchar(255) DEFAULT '/public/uploads/logo.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `app_name`, `timezone`, `currency`, `admin_email`, `items_per_page`, `tax_rate`, `logo_path`) VALUES
(1, 'Ordenes de Trabajo', 'America/Argentina/Buenos_Aires', 'ARS', 'info@cellcomweb.com.ar', 20, '0.21', '/uploads/logo_1724874559.png');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `movement_type` enum('compra','venta','ajuste','devolución') NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `quantity`, `movement_type`, `reference_id`, `notes`, `created_at`, `user_id`) VALUES
(1, 5, -3, 'venta', 1, 'Venta de producto', '2024-08-17 17:41:48', 1),
(2, 7, -2, 'venta', 2, 'Venta de producto', '2024-08-17 17:46:44', 1),
(3, 5, -2, 'venta', 3, 'Venta de producto', '2024-08-17 17:47:03', 1),
(4, 49, -1, 'venta', 4, 'Venta de producto', '2024-08-17 17:49:12', 1),
(5, 5, -2, 'venta', 5, 'Venta de producto', '2024-08-17 17:49:33', 1),
(6, 3, -1, 'venta', 6, 'Venta de producto', '2024-08-17 18:04:50', 1),
(7, 50, -1, 'venta', 7, 'Venta de producto', '2024-08-17 18:07:35', 1),
(8, 51, 2, 'compra', 1, 'Recepción de compra', '2024-08-17 22:08:11', 1),
(9, 5, 2, 'compra', 2, 'Recepción de compra', '2024-08-17 22:08:50', 1),
(10, 22, -1, 'venta', 8, 'Venta de producto', '2024-08-17 22:26:48', 1),
(11, 52, -1, 'venta', 9, 'Venta de producto', '2024-08-17 22:30:46', 1),
(12, 27, -1, 'venta', 10, 'Venta de producto', '2024-08-17 22:31:12', 1),
(13, 53, -1, 'venta', 11, 'Venta de producto', '2024-08-17 22:31:45', 1),
(14, 21, -1, 'venta', 12, 'Venta de producto', '2024-08-17 23:06:26', 1),
(15, 5, -1, 'venta', 13, 'Venta de producto', '2024-08-17 23:06:47', 1),
(16, 7, -2, 'venta', 14, 'Venta de producto', '2024-08-17 23:07:15', 1),
(17, 5, -3, 'venta', 15, 'Venta de producto', '2024-08-17 23:07:38', 1),
(18, 20, -1, 'venta', 16, 'Venta de producto', '2024-08-17 23:23:29', 1),
(19, 1, -9, 'venta', 18, 'Venta de producto', '2024-08-17 23:39:09', 1),
(20, 16, -1, 'venta', 19, 'Venta de producto', '2024-08-18 01:41:44', 1),
(21, 19, -1, 'venta', 20, 'Venta de producto', '2024-08-18 01:42:02', 1),
(22, 5, -3, 'venta', 21, 'Venta de producto', '2024-08-18 01:42:27', 1),
(23, 5, -2, 'venta', 22, 'Venta de producto', '2024-08-18 01:42:45', 1),
(24, 54, -1, 'venta', 23, 'Venta de producto', '2024-08-18 01:44:38', 1),
(25, 3, -1, 'venta', 24, 'Venta de producto', '2024-08-18 01:44:56', 1),
(26, 20, -1, 'venta', 25, 'Venta de producto', '2024-08-18 01:45:14', 1),
(27, 20, -1, 'venta', 26, 'Venta de producto', '2024-08-18 01:45:30', 1),
(28, 48, -1, 'venta', 27, 'Venta de producto', '2024-08-18 01:45:45', 1),
(29, 20, -1, 'venta', 28, 'Venta de producto', '2024-08-18 01:46:00', 1),
(30, 20, -1, 'venta', 29, 'Venta de producto', '2024-08-18 01:46:10', 1),
(31, 1, -4, 'venta', 30, 'Venta de producto', '2024-08-18 01:54:19', 1),
(32, 19, -1, 'venta', 31, 'Venta de producto', '2024-08-18 01:54:58', 1),
(33, 8, -1, 'venta', 32, 'Venta de producto', '2024-08-18 03:06:46', 1),
(34, 31, -1, 'venta', 32, 'Venta de producto', '2024-08-19 11:23:30', 1),
(35, 2, -1, 'venta', 33, 'Venta de producto', '2024-08-19 11:25:50', 1),
(36, 19, -1, 'venta', 35, 'Venta de producto', '2024-08-19 14:52:34', 1),
(37, 19, -1, 'venta', 36, 'Venta de producto', '2024-08-19 14:55:43', 1),
(38, 19, -1, 'venta', 37, 'Venta de producto', '2024-08-19 14:56:09', 1),
(39, 2, 1, 'compra', 3, 'Recepción de compra', '2024-08-19 15:38:50', 1),
(40, 55, -1, 'venta', 34, 'Venta de producto', '2024-08-19 20:05:52', 1),
(41, 7, -1, 'venta', 35, 'Venta de producto', '2024-08-19 21:09:55', 1),
(42, 5, -3, 'venta', 35, 'Venta de producto', '2024-08-19 21:09:55', 1),
(43, 5, -3, 'venta', 36, 'Venta de producto', '2024-08-19 21:58:22', 1),
(44, 19, -2, 'venta', 37, 'Venta de producto', '2024-08-19 22:16:55', 1),
(45, 8, -1, 'venta', 38, 'Venta de producto', '2024-08-19 22:17:53', 1),
(46, 32, -1, 'venta', 38, 'Venta de producto', '2024-08-19 22:17:53', 1),
(47, 12, -1, 'venta', 38, 'Venta de producto', '2024-08-19 22:17:53', 1),
(48, 2, -1, 'venta', 39, 'Venta de producto', '2024-08-19 22:30:15', 1),
(49, 8, 1, 'compra', 4, 'Recepción de compra', '2024-08-19 23:20:56', 1),
(50, 2, 1, 'compra', 5, 'Recepción de compra', '2024-08-19 23:22:30', 1),
(51, 31, -1, 'venta', 40, 'Venta de producto', '2024-08-20 13:09:40', 1),
(52, 5, -3, 'venta', 41, 'Venta de producto', '2024-08-20 13:48:35', 1),
(53, 5, -2, 'venta', 42, 'Venta de producto', '2024-08-20 13:53:31', 1),
(54, 3, -1, 'venta', 43, 'Venta de producto', '2024-08-20 13:53:57', 1),
(55, 20, -2, 'venta', 44, 'Venta de producto', '2024-08-20 14:30:41', 1),
(56, 4, -1, 'venta', 45, 'Venta de producto', '2024-08-20 22:27:47', 1),
(57, 19, -5, 'venta', 46, 'Venta de producto', '2024-08-20 22:47:32', 1),
(58, 29, -1, 'venta', 46, 'Venta de producto', '2024-08-20 22:47:32', 1),
(59, 1, -5, 'venta', 47, 'Venta de producto', '2024-08-21 01:20:17', 1),
(60, 9, -1, 'venta', 48, 'Venta de producto', '2024-08-21 15:04:10', 1),
(61, 2, -1, 'venta', 49, 'Venta de producto', '2024-08-21 15:06:45', 1),
(62, 52, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(63, 13, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(64, 19, 7, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(65, 1, 5, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(66, 22, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(67, 48, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(68, 55, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(69, 56, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(70, 51, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(71, 23, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(72, 24, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(73, 25, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(74, 26, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(75, 27, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(76, 43, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(77, 42, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(78, 8, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(79, 36, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(80, 2, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(81, 4, 1, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(82, 3, 1, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(83, 5, 11, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(84, 28, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(85, 29, 1, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(86, 30, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(87, 31, 2, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(88, 21, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(89, 6, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(90, 53, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(91, 49, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(92, 7, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(93, 9, 1, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(94, 14, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(95, 46, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:42', 1),
(96, 45, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(97, 15, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(98, 16, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(99, 17, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(100, 18, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(101, 38, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(102, 39, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(103, 47, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(104, 37, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(105, 10, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(106, 11, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(107, 12, 1, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(108, 32, 1, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(109, 33, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(110, 34, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(111, 44, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(112, 50, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(113, 20, 2, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(114, 35, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(115, 54, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(116, 40, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(117, 41, 0, 'ajuste', NULL, 'dañado', '2024-08-21 18:27:43', 1),
(118, 5, -1, 'venta', 50, 'Venta de producto', '2024-08-21 20:38:40', 1),
(119, 7, -1, 'venta', 51, 'Venta de producto', '2024-08-21 21:24:15', 1),
(120, 5, -3, 'venta', 51, 'Venta de producto', '2024-08-21 21:24:15', 1),
(121, 56, 1, 'compra', 6, 'Recepción de compra', '2024-08-21 23:33:17', 1),
(122, 2, 1, 'compra', 7, 'Recepción de compra', '2024-08-21 23:33:23', 1),
(123, 57, 1, 'compra', 8, 'Recepción de compra', '2024-08-21 23:33:29', 1),
(124, 5, -2, 'venta', 52, 'Venta de producto', '2024-08-22 12:54:15', 1),
(125, 5, -3, 'venta', 53, 'Venta de producto', '2024-08-22 20:46:33', 1),
(126, 1, -3, 'venta', 54, 'Venta de producto', '2024-08-22 22:00:38', 1),
(127, 20, -1, 'venta', 55, 'Venta de producto', '2024-08-22 22:02:11', 1),
(128, 1, -3, 'venta', 55, 'Venta de producto', '2024-08-22 22:02:11', 1),
(129, 7, -1, 'venta', 56, 'Venta de producto', '2024-08-22 22:55:14', 1),
(130, 5, -3, 'venta', 56, 'Venta de producto', '2024-08-22 22:55:14', 1),
(131, 57, -1, 'venta', 57, 'Venta de producto', '2024-08-22 23:09:10', 1),
(132, 20, -1, 'venta', 57, 'Venta de producto', '2024-08-22 23:09:10', 1),
(133, 18, -1, 'venta', 58, 'Venta de producto', '2024-08-23 02:02:55', 1),
(134, 19, -1, 'venta', 59, 'Venta de producto', '2024-08-23 13:24:40', 1),
(135, 19, -1, 'venta', 59, 'Venta de producto', '2024-08-23 19:39:14', 1),
(136, 3, -1, 'venta', 60, 'Venta de producto', '2024-08-23 22:15:40', 1),
(137, 1, -1, 'venta', 61, 'Venta de producto', '2024-08-23 23:04:56', 1),
(138, 56, -1, 'venta', 62, 'Venta de producto', '2024-08-23 23:09:36', 1),
(139, 52, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(140, 13, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(141, 19, 2, 'ajuste', NULL, 'correccion', '2024-08-24 12:37:41', 1),
(142, 1, 7, 'ajuste', NULL, 'correccion', '2024-08-24 12:37:41', 1),
(143, 22, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(144, 48, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(145, 55, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(146, 56, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(147, 51, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(148, 23, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(149, 24, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(150, 25, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(151, 26, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(152, 27, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(153, 43, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(154, 42, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(155, 8, -2, 'ajuste', NULL, 'correccion', '2024-08-24 12:37:41', 1),
(156, 36, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(157, 2, -1, 'ajuste', NULL, 'correccion', '2024-08-24 12:37:41', 1),
(158, 4, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(159, 3, 1, 'ajuste', NULL, 'correccion', '2024-08-24 12:37:41', 1),
(160, 5, 11, 'ajuste', NULL, 'correccion', '2024-08-24 12:37:41', 1),
(161, 28, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(162, 29, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(163, 30, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(164, 31, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(165, 21, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(166, 6, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(167, 53, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(168, 49, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(169, 7, 2, 'ajuste', NULL, 'correccion', '2024-08-24 12:37:41', 1),
(170, 9, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(171, 14, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(172, 46, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(173, 45, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(174, 15, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(175, 16, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(176, 17, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(177, 18, 1, 'ajuste', NULL, 'correccion', '2024-08-24 12:37:41', 1),
(178, 38, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(179, 39, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(180, 47, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(181, 37, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(182, 10, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(183, 11, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(184, 12, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(185, 32, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(186, 57, -1, 'ajuste', NULL, 'correccion', '2024-08-24 12:37:41', 1),
(187, 33, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(188, 34, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(189, 44, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(190, 50, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(191, 20, 2, 'ajuste', NULL, 'correccion', '2024-08-24 12:37:41', 1),
(192, 35, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(193, 54, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(194, 40, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(195, 41, 0, 'ajuste', NULL, 'dañado', '2024-08-24 12:37:41', 1),
(196, 20, 35, 'compra', 9, 'Recepción de compra', '2024-08-24 13:33:31', 1),
(197, 52, -1, 'venta', 63, 'Venta de producto', '2024-08-24 13:34:42', 1),
(198, 5, -1, 'venta', 64, 'Venta de producto', '2024-08-26 13:10:18', 1),
(199, 5, -1, 'venta', 65, 'Venta de producto', '2024-08-26 13:17:38', 1),
(200, 58, 1, 'compra', 10, 'Recepción de compra', '2024-08-26 14:18:14', 1),
(201, 59, -1, 'venta', 66, 'Venta de producto', '2024-08-26 14:43:08', 1),
(202, 7, -1, 'venta', 67, 'Venta de producto', '2024-08-26 20:48:06', 1),
(203, 5, -1, 'venta', 67, 'Venta de producto', '2024-08-26 20:48:06', 1),
(204, 5, -1, 'venta', 68, 'Venta de producto', '2024-08-26 21:18:24', 1),
(205, 19, -1, 'venta', 69, 'Venta de producto', '2024-08-27 15:04:56', 1),
(206, 19, -1, 'venta', 70, 'Venta de producto', '2024-08-27 20:51:52', 1),
(207, 1, -1, 'venta', 71, 'Venta de producto', '2024-08-27 20:55:27', 1),
(208, 19, -1, 'venta', 72, 'Venta de producto', '2024-08-27 21:39:43', 1),
(209, 58, 1, 'compra', 11, 'Recepción de compra', '2024-08-27 23:13:57', 1),
(210, 12, -1, 'venta', 73, 'Venta de producto', '2024-08-28 12:51:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_person`, `email`, `phone`, `address`, `created_at`) VALUES
(1, 'Cellcom', 'Cellcom', 'info@cellcomweb.com.ar', '3482549555', 'Calle 9 Nro 539', '2024-08-17 02:26:12'),
(2, 'Redcell', 'Mayra', 'info@redcell.com.ar', '3482763835', 'Irigoyen y Moreno', '2024-08-16 04:53:34'),
(3, 'Delbón Luciano', 'Luciano', 'luciano@delbon.com.ar', '3482649896', 'Calle 16 Nro 680', '2024-08-16 04:54:37'),
(4, 'JJE Mayorista', 'Bali', 'info@jje.com.ar', '3482523222', 'Habegger 1443', '2024-08-16 04:55:48'),
(5, 'Celuce', 'César', 'ventas@celucedistribuciones.com.ar', '3482603030', '9 de julio 1212', '2024-08-16 04:57:26'),
(6, 'Educom', 'Eduardo', 'info@educom.com.ar', '3482210411', 'Mitre 727', '2024-08-16 04:58:46'),
(7, 'Hello', 'Federico', 'info@hello.com.ar', '3482386055', 'Olessio 1360', '2024-08-16 04:59:43'),
(8, 'GyG Argentina Juegos', 'Gustavo', 'info@gyg.com.ar', '3482412280', 'Calle 9 Nro 425', '2024-08-16 05:02:05'),
(9, 'Mercado Libre', 'ML', 'info@ml.com.ar', '3482000000', 'Calle 9 Nro 539', '2024-08-16 23:48:37'),
(10, 'Claro', 'Daiana', 'info@claro.com.ar', '3482255131', 'Avellaneda', '2024-08-17 16:58:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `created_at`) VALUES
(1, 'Cellcom Technology', 'info@cellcomweb.com.ar', '$2y$10$e47bpB.dGC8ZaJH8E7u0b.1.PsHjCq8PFPfXPQ9q1xht5WA1qjTay', 1, '2024-08-20 17:31:01'),
(2, 'Cellcom Technology', 'tecnico@cellcomweb.com.ar', '$2y$10$phr4kw52xwLHstmXBA2Mz.La1V2TER0VoW2quc2u6tjVzWYaAh9sG', 5, '2024-08-20 22:28:54'),
(3, 'Cellcom Technology', 'ventas@cellcomweb.com.ar', '$2y$10$ClblXMepZMsuq7nvi2HdlO.POfnmf27q6imSAHXxzPoDk2cd7Jo02', 4, '2024-08-24 03:18:39');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bio` text,
  `location` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `bio`, `location`, `website`, `created_at`) VALUES
(1, 1, 'Soy un Capo', 'Avellaneda', 'https://www.cellcomweb.com.ar', '2024-08-19 16:45:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `view_token` (`view_token`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `budget_items`
--
ALTER TABLE `budget_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budget_id` (`budget_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `cash_register_movements`
--
ALTER TABLE `cash_register_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_register_session_id` (`cash_register_session_id`);

--
-- Indexes for table `cash_register_sessions`
--
ALTER TABLE `cash_register_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_info`
--
ALTER TABLE `company_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_accounts`
--
ALTER TABLE `customer_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `home_visits`
--
ALTER TABLE `home_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_home_visits_customer_id` (`customer_id`);

--
-- Indexes for table `inventory_adjustments`
--
ALTER TABLE `inventory_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `inventory_adjustment_items`
--
ALTER TABLE `inventory_adjustment_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `adjustment_id` (`adjustment_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `payment_distributions`
--
ALTER TABLE `payment_distributions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `purchases_cash_register_session_fk` (`cash_register_session_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchase_movements`
--
ALTER TABLE `purchase_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reservation_items`
--
ALTER TABLE `reservation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sales_cash_register_session_fk` (`cash_register_session_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `service_devices`
--
ALTER TABLE `service_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_order_id` (`service_order_id`);

--
-- Indexes for table `service_items`
--
ALTER TABLE `service_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_order_id` (`service_order_id`);

--
-- Indexes for table `service_orders`
--
ALTER TABLE `service_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `service_order_notes`
--
ALTER TABLE `service_order_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_order_id` (`service_order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `service_order_status_history`
--
ALTER TABLE `service_order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_order_id` (`service_order_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `service_parts`
--
ALTER TABLE `service_parts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_order_id` (`service_order_id`);

--
-- Indexes for table `service_terms`
--
ALTER TABLE `service_terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `budget_items`
--
ALTER TABLE `budget_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `cash_register_movements`
--
ALTER TABLE `cash_register_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `cash_register_sessions`
--
ALTER TABLE `cash_register_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `company_info`
--
ALTER TABLE `company_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `customer_accounts`
--
ALTER TABLE `customer_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `home_visits`
--
ALTER TABLE `home_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_adjustments`
--
ALTER TABLE `inventory_adjustments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_adjustment_items`
--
ALTER TABLE `inventory_adjustment_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_distributions`
--
ALTER TABLE `payment_distributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `purchase_movements`
--
ALTER TABLE `purchase_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservation_items`
--
ALTER TABLE `reservation_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `sale_payments`
--
ALTER TABLE `sale_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `service_devices`
--
ALTER TABLE `service_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `service_items`
--
ALTER TABLE `service_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `service_orders`
--
ALTER TABLE `service_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `service_order_notes`
--
ALTER TABLE `service_order_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `service_order_status_history`
--
ALTER TABLE `service_order_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `service_parts`
--
ALTER TABLE `service_parts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_terms`
--
ALTER TABLE `service_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `budgets_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `budget_items`
--
ALTER TABLE `budget_items`
  ADD CONSTRAINT `budget_items_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budget_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `cash_register_movements`
--
ALTER TABLE `cash_register_movements`
  ADD CONSTRAINT `cash_register_movements_ibfk_1` FOREIGN KEY (`cash_register_session_id`) REFERENCES `cash_register_sessions` (`id`);

--
-- Constraints for table `cash_register_sessions`
--
ALTER TABLE `cash_register_sessions`
  ADD CONSTRAINT `cash_register_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `customer_accounts`
--
ALTER TABLE `customer_accounts`
  ADD CONSTRAINT `customer_accounts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `home_visits`
--
ALTER TABLE `home_visits`
  ADD CONSTRAINT `fk_home_visits_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inventory_adjustments`
--
ALTER TABLE `inventory_adjustments`
  ADD CONSTRAINT `inventory_adjustments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `inventory_adjustment_items`
--
ALTER TABLE `inventory_adjustment_items`
  ADD CONSTRAINT `inventory_adjustment_items_ibfk_1` FOREIGN KEY (`adjustment_id`) REFERENCES `inventory_adjustments` (`id`),
  ADD CONSTRAINT `inventory_adjustment_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `payment_distributions`
--
ALTER TABLE `payment_distributions`
  ADD CONSTRAINT `payment_distributions_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_distributions_ibfk_2` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `promotions`
--
ALTER TABLE `promotions`
  ADD CONSTRAINT `promotions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_cash_register_session_fk` FOREIGN KEY (`cash_register_session_id`) REFERENCES `cash_register_sessions` (`id`),
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchases_ibfk_3` FOREIGN KEY (`cash_register_session_id`) REFERENCES `cash_register_sessions` (`id`);

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `purchase_movements`
--
ALTER TABLE `purchase_movements`
  ADD CONSTRAINT `purchase_movements_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_movements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reservation_items`
--
ALTER TABLE `reservation_items`
  ADD CONSTRAINT `reservation_items_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_cash_register_session_fk` FOREIGN KEY (`cash_register_session_id`) REFERENCES `cash_register_sessions` (`id`),
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`cash_register_session_id`) REFERENCES `cash_register_sessions` (`id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD CONSTRAINT `sale_payments_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `sale_payments_ibfk_2` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`);

--
-- Constraints for table `service_devices`
--
ALTER TABLE `service_devices`
  ADD CONSTRAINT `service_devices_ibfk_1` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`);

--
-- Constraints for table `service_items`
--
ALTER TABLE `service_items`
  ADD CONSTRAINT `service_items_ibfk_1` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`);

--
-- Constraints for table `service_orders`
--
ALTER TABLE `service_orders`
  ADD CONSTRAINT `service_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `service_order_notes`
--
ALTER TABLE `service_order_notes`
  ADD CONSTRAINT `service_order_notes_ibfk_1` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`),
  ADD CONSTRAINT `service_order_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `service_order_status_history`
--
ALTER TABLE `service_order_status_history`
  ADD CONSTRAINT `service_order_status_history_ibfk_1` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`),
  ADD CONSTRAINT `service_order_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `service_parts`
--
ALTER TABLE `service_parts`
  ADD CONSTRAINT `service_parts_ibfk_1` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
