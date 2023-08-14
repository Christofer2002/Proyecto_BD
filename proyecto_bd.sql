-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 14-08-2023 a las 19:04:08
-- Versión del servidor: 8.0.30
-- Versión de PHP: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyecto_bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE IF NOT EXISTS `categoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `descripcion`) VALUES
(1, 'Quality'),
(2, 'Risk');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido`
--

DROP TABLE IF EXISTS `contenido`;
CREATE TABLE IF NOT EXISTS `contenido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `contenido`
--

INSERT INTO `contenido` (`id`, `descripcion`) VALUES
(5, 'Internal Control in Database'),
(6, 'Database Administration'),
(9, 'Network Environment Configuration'),
(10, 'Memory Management and Support Processes'),
(11, 'Management of Database Storage Structures'),
(12, 'User Account and Security Administration'),
(13, 'Database Backup and Recovery'),
(14, 'Database Activity Monitoring with Auditing');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuestionario`
--

DROP TABLE IF EXISTS `cuestionario`;
CREATE TABLE IF NOT EXISTS `cuestionario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pregunta` varchar(300) DEFAULT NULL,
  `id_categoria` int NOT NULL,
  `id_contenido` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `foreignkeyCuestionarioContenido` (`id_contenido`),
  KEY `foreignkeyCuestionarioCategoria` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `cuestionario`
--

INSERT INTO `cuestionario` (`id`, `pregunta`, `id_categoria`, `id_contenido`) VALUES
(5, 'Database administrator does database backups frequently', 1, 5),
(6, 'Database administrator frequently backups the operating system which the databases are running on', 2, 5),
(7, 'Database administrator checks and tests on a daily basis all the backups related to the databases (e.g: Operating system and databases backups)', 2, 5),
(8, 'Database administrator looks at database indicators regularly', 2, 5),
(9, 'Database administrator changes and optimizes data structures, memory and processing supported by the database according to different indicators.', 2, 5),
(10, 'Database administrator keeps all the hardware the database is running on in a secure, climatized and restricted place', 2, 5),
(11, 'Database administrator keeps backups in separated places, including local places or the cloud', 2, 5),
(12, 'Database administrator designs and implements replication mechanisms in order to reduce data loss. E.g. Database replications in another site.', 2, 5),
(13, 'Database administrator establishes and implements  mechanisms to reduce restoring time such as virtualization, docking or native backups', 2, 5),
(14, 'Full database documentation exists', 2, 5),
(15, 'There are complete manuals to do all the tasks related to database administration.', 2, 5),
(16, 'Plans for reducing the impact of any database related risk exists.', 2, 5),
(17, 'Every member of the database management department is correctly trained with contingency plans', 2, 5),
(18, 'A process to check database continuity after an incident exists', 2, 5),
(19, 'Database management processes are periodically under review in order to improve effectiveness.', 2, 5),
(20, 'Database management procedures and controls are regularly reviewed and updated', 2, 5),
(21, 'Upgrades based on previous eventualities are implemented.', 2, 5),
(22, 'Database management processes are compliant with the law, current regulations and normatives.', 2, 5);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cuestionario`
--
ALTER TABLE `cuestionario`
  ADD CONSTRAINT `foreignkeyCuestionarioCategoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`),
  ADD CONSTRAINT `foreignkeyCuestionarioContenido` FOREIGN KEY (`id_contenido`) REFERENCES `contenido` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
