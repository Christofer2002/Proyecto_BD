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
(5, 'El administrador de bases de datos realiza respaldos periódicamente de la base de datos?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(6, '¿El administrador de bases de datos realiza respaldos periódicamente del sistema operativo que soporta la base de datos?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(7, '¿El administrador de bases de datos revisa y prueba periódicamente los respaldos anteriormente mencionados?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(8, '¿El administrador de bases de datos monitorea constantemente y de manera perpetua las bases de datos?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(9, '¿El administrador de bases de datos afina y optimiza las estructuras de almacenamientos, memoria y procesamiento que soportan la base de datos?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(10, '¿El administrador de bases de datos mantiene la plataforma tecnológica en la que se ejecutan las bases de datos en espacios seguros, con mecanismos de climatización y acceso físicos, ya sea que se ubique en instalaciones propias o en la nube?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(11, '¿El administrador de bases de datos mantiene copias de los respaldos en distintas ubicaciones físicas, ya sean locales o en la nube?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(12, '¿El administrador de bases de datos establece e implementa mecanismos de replicación que reduzcan la pérdida de datos, como puede ser la replicaciones de bases de datos en un sitio alterno?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(13, '¿El administrador de bases de datos establece e implementa mecanismos que reduzcan los tiempos de recuperación, por ejemplo virtualización, compartimientos y respaldos nativos?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(14, '¿Existe documentación completa de las bases de datos?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(15, '¿Existen manuales completos para realizar todos los procesos relacionados a la administración de bases de datos?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(16, '¿Existen planes de contingencia para aquellos riesgos que puedan afectar a las bases de datos?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(17, '¿Todas las partes involucradas en la gestión de bases de datos han sido debidamente instruidas con los planes de contingencia?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(18, '¿Existen procesos para revisar la continuidad del servicio de las bases de datos posteriormente a un incidente?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(19, '¿Se realizan auditorías periódicas para evaluar la eficacia de los controles en la gestión de bases de datos?¿La gestión de bases de datos cumple con las regulaciones y normativas relevantes?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(20, '¿Se revisan y actualizan regularmente los procedimientos y controles de gestión de bases de datos?', 5);

INSERT INTO `cuestionario` (`id`, `pregunta`, `contenidoC`) VALUES
(21, '¿Se implementan mejoras basadas en las lecciones aprendidas de incidentes anteriores?', 5);

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
