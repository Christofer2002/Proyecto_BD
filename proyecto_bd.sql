-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 08-08-2023 a las 20:44:53
-- Versión del servidor: 8.0.31
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
DROP DATABASE IF EXISTS proyecto_bd;
CREATE database IF NOT EXISTS  proyecto_bd; 
USE proyecto_bd;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido`
--

DROP TABLE IF EXISTS `contenido`;
CREATE TABLE IF NOT EXISTS `contenido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `contenido`
--

INSERT INTO `contenido` (`id`, `descripcion`) VALUES
(5, 'Control interno bases de datos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuestionario`
--

DROP TABLE IF EXISTS `cuestionario`;
CREATE TABLE IF NOT EXISTS `cuestionario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pregunta` varchar(300) DEFAULT NULL, -- Se tuvo que aumentar la cantidad de bits para que las preguntas puedan entrar completas en las tablas
  `contenidoC` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `foreignkeyCuestionarioContenido` (`contenidoC`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `cuestionario`
--

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(5, 'Database administrator does database backups frequently', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(6, 'Database administrator frequently backups the operating system which the databases are running on', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(7, 'Database administrator checks and tests on a daily basis all the backups related to the databases (e.g: Operating system and databases backups)', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(8, 'Database administrator looks at database indicators regularly', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(9, 'Database administrator changes and optimizes data structures, memory and processing supported by the database according to different indicators.', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(10, 'Database administrator keeps all the hardware the database is running on in a secure, climatized and restricted place', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(11, 'Database administrator keeps backups in separated places, including local places or the cloud', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(12, 'Database administrator designs and implements replication mechanisms in order to reduce data loss. E.g. Database replications in another site.', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(13, 'Database administrator establishes and implements  mechanisms to reduce restoring time such as virtualization, docking or native backups', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(14, 'Full database documentation exists', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(15, 'There are complete manuals to do all the tasks related to database administration.', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(16, 'Plans for reducing the impact of any database related risk exists.', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(17, 'Every member of the database management department is correctly trained with contingency plans', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(18, 'A process to check database continuity after an incident exists', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(19, 'Database management processes are periodically under review in order to improve effectiveness.', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(20, 'Database management procedures and controls are regularly reviewed and updated', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(21, 'Upgrades based on previous eventualities are implemented.', 5);

INSERT INTO `cuestionario`(`id`,`pregunta`,`contenidoC`) VALUES
(22,'Database management processes are compliant with the law, current regulations and normatives.',5);

--
/*Plantilla de sentencia SQL para seguir insertando preguntas, no borrar*/
/*INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(21, '', 5);*/
-- -- -- -- -- -- -- -- -- --

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cuestionario`
--
ALTER TABLE `cuestionario`
  ADD CONSTRAINT `foreignkeyCuestionarioContenido` FOREIGN KEY (`contenidoC`) REFERENCES `contenido` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
