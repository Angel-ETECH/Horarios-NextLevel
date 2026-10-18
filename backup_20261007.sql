-- MySQL dump 10.13  Distrib 9.3.0, for Win64 (x86_64)
--
-- Host: localhost    Database: horarios_next_level
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumnos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_paterno` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_materno` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dni` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `direccion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genero` enum('masculino','femenino','otro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grado_id` bigint unsigned NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `estado` enum('activo','inactivo','trasladado','retirado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumnos_codigo_unique` (`codigo`),
  UNIQUE KEY `alumnos_dni_unique` (`dni`),
  KEY `alumnos_grado_id_index` (`grado_id`),
  KEY `alumnos_estado_index` (`estado`),
  KEY `alumnos_dni_index` (`dni`),
  KEY `alumnos_codigo_index` (`codigo`),
  CONSTRAINT `alumnos_grado_id_foreign` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumnos`
--

LOCK TABLES `alumnos` WRITE;
/*!40000 ALTER TABLE `alumnos` DISABLE KEYS */;
INSERT INTO `alumnos` VALUES (1,'ALU-001','Pedro','González','Reyes','11111111','pedro.gonzalez@example.com','987111111','2015-05-15','Av. Principal 123','masculino',1,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(2,'ALU-002','María','Luna','Díaz','22222222','maria.luna@example.com','987222222','2016-08-22','Calle Las Flores 456','femenino',1,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(3,'ALU-003','Carlos','Mendoza','Silva','33333333','carlos.mendoza@example.com','987333333','2014-11-10','Jr. Los Olivos 789','masculino',2,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(4,'ALU-004','Ana','Quispe','Huamán','44444444','ana.quispe@example.com','987444444','2009-04-20','Av. Los Pinos 321','femenino',8,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(5,'ALU-005','Javier','Pormachi','Gomez','55555555','javier.pormachi@example.com','987555555','2010-07-15','Calle Los Álamos 654','masculino',9,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(6,'ALU-006','Lucía','Fernández','Ramos','66666666','lucia.fernandez@example.com','987666666','2017-03-10','Av. Las Palmeras 987','femenino',3,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(7,'ALU-007','Diego','Salazar','Torres','77777777','diego.salazar@example.com','987777777','2008-09-25','Jr. Los Naranjos 147','masculino',10,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(8,'ALU-008','Camila','Rojas','Vega','88888888','camila.rojas@example.com','987888888','2018-12-01','Av. Los Cedros 258','femenino',4,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(9,'ALU-009','Andrés','Navarro','Paredes','99999999','andres.navarro@example.com','987999999','2007-06-18','Calle Los Robles 369','masculino',12,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(10,'ALU-010','Valentina','Cruz','Mamani','10101010','valentina.cruz@example.com','987101010','2019-02-28','Jr. Los Sauces 741','femenino',5,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(11,'ALU-011','Mateo','Herrera','Paredes','12121212','mateo.herrera@example.com','987121212','2016-07-20','Av. Los Ángeles 159','masculino',6,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(12,'ALU-012','Sofía','Vega','Ramos','13131313','sofia.vega@example.com','987131313','2015-09-12','Calle Los Girasoles 753','femenino',7,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(13,'ALU-013','Emilio','Rojas','Flores','14141414','emilio.rojas@example.com','987141414','2009-11-05','Jr. Los Tulipanes 852','masculino',11,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(14,'ALU-014','Valeria','Mendoza','Silva','15151515','valeria.mendoza@example.com','987151515','2010-04-25','Av. Los Rosales 963','femenino',13,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(15,'ALU-015','Nicolás','Lozano','Torres','16161616','nicolas.lozano@example.com','987161616','2018-08-15','Calle Los Claveles 147','masculino',2,'2024-03-01','activo',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL);
/*!40000 ALTER TABLE `alumnos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aulas`
--

DROP TABLE IF EXISTS `aulas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aulas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidad` int NOT NULL DEFAULT '30',
  `tipo` enum('aula_normal','taller','auditorio','virtual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aula_normal',
  `nivel` enum('primaria','secundaria','academia','todos') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'todos',
  `edificio` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `piso` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equipamiento` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `aulas_codigo_unique` (`codigo`),
  KEY `aulas_codigo_index` (`codigo`),
  KEY `aulas_tipo_index` (`tipo`),
  KEY `aulas_nivel_index` (`nivel`),
  KEY `aulas_activo_index` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aulas`
--

LOCK TABLES `aulas` WRITE;
/*!40000 ALTER TABLE `aulas` DISABLE KEYS */;
INSERT INTO `aulas` VALUES (1,'A-101','Aula 101',30,'aula_normal','todos',NULL,NULL,'Proyector, Pizarra acrílica',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(2,'A-102','Aula 102',35,'aula_normal','todos',NULL,NULL,'Proyector, Aire acondicionado',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(3,'A-201','Aula 201',25,'aula_normal','secundaria',NULL,NULL,'Proyector, Aire acondicionado',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(4,'A-202','Aula 202',30,'aula_normal','secundaria',NULL,NULL,'Proyector, Pizarra digital',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(5,'LAB-01','Taller de Karate',20,'taller','todos',NULL,NULL,'Microscopios, Reactivos, Proyector',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(6,'LAB-02','Auditorio de Computación',25,'auditorio','todos',NULL,NULL,'30 PCs, Proyector, Aire acondicionado',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(7,'TALLER-01','Taller de Arte',20,'taller','todos',NULL,NULL,'Mesas de trabajo, Materiales de arte',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(8,'AUD-01','Auditorio Principal',100,'auditorio','todos',NULL,NULL,'Proyector, Sonido, Aire acondicionado',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(9,'VIR-01','Aula Virtual 1',50,'virtual','todos',NULL,NULL,'Plataforma Zoom, Pizarra digital',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(10,'A-301','Aula 301',40,'aula_normal','academia',NULL,NULL,'Proyector, Aire acondicionado',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(11,'A-103','Aula 103',25,'aula_normal','academia',NULL,NULL,'Ventilador, proyector',1,'Aula exclusivo de academia','2026-10-06 13:52:13','2026-10-06 13:52:13',NULL);
/*!40000 ALTER TABLE `aulas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bloques_horarios`
--

DROP TABLE IF EXISTS `bloques_horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bloques_horarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `configuracion_horario_id` bigint unsigned NOT NULL,
  `orden` int NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `tipo` enum('clase','receso') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_bloque` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bloques_horarios_configuracion_horario_id_orden_index` (`configuracion_horario_id`,`orden`),
  CONSTRAINT `bloques_horarios_configuracion_horario_id_foreign` FOREIGN KEY (`configuracion_horario_id`) REFERENCES `configuracion_horarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=210 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bloques_horarios`
--

LOCK TABLES `bloques_horarios` WRITE;
/*!40000 ALTER TABLE `bloques_horarios` DISABLE KEYS */;
INSERT INTO `bloques_horarios` VALUES (137,1,1,'07:00:00','07:45:00','clase','Clase 1',1,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(138,1,2,'07:45:00','08:30:00','clase','Clase 2',2,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(139,1,3,'08:30:00','09:00:00','clase','Clase 3',3,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(140,1,4,'09:00:00','09:30:00','receso','Receso mañana 1',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(141,1,5,'09:30:00','10:15:00','clase','Clase 4',4,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(142,1,6,'10:15:00','11:00:00','clase','Clase 5',5,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(143,1,7,'11:00:00','11:30:00','receso','Receso mañana 2',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(144,1,8,'11:30:00','12:00:00','clase','Clase 6',6,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(145,2,1,'07:00:00','07:45:00','clase','Clase 1',1,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(146,2,2,'07:45:00','08:30:00','clase','Clase 2',2,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(147,2,3,'08:30:00','09:00:00','clase','Clase 3',3,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(148,2,4,'09:00:00','09:30:00','receso','Receso mañana 1',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(149,2,5,'09:30:00','10:15:00','clase','Clase 4',4,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(150,2,6,'10:15:00','11:00:00','clase','Clase 5',5,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(151,2,7,'11:00:00','11:30:00','receso','Receso mañana 2',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(152,2,8,'11:30:00','12:00:00','clase','Clase 6',6,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(153,3,1,'07:00:00','07:45:00','clase','Clase 1',1,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(154,3,2,'07:45:00','08:30:00','clase','Clase 2',2,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(155,3,3,'08:30:00','09:00:00','clase','Clase 3',3,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(156,3,4,'09:00:00','09:30:00','receso','Receso mañana 1',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(157,3,5,'09:30:00','10:15:00','clase','Clase 4',4,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(158,3,6,'10:15:00','11:00:00','clase','Clase 5',5,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(159,3,7,'11:00:00','11:30:00','receso','Receso mañana 2',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(160,3,8,'11:30:00','12:00:00','clase','Clase 6',6,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(161,4,1,'13:00:00','14:30:00','clase','Clase 1',1,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(162,4,2,'14:30:00','15:00:00','clase','Clase 2',2,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(163,4,3,'15:00:00','15:30:00','receso','Receso tarde 1',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(164,4,4,'15:30:00','17:00:00','clase','Clase 3',3,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(165,4,5,'17:00:00','17:30:00','receso','Receso tarde 2',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(166,4,6,'17:30:00','19:00:00','clase','Clase 4',4,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(167,4,7,'19:00:00','20:00:00','clase','Clase 5',5,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(168,5,1,'07:00:00','07:45:00','clase','Clase 1',1,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(169,5,2,'07:45:00','08:30:00','clase','Clase 2',2,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(170,5,3,'08:30:00','09:00:00','clase','Clase 3',3,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(171,5,4,'09:00:00','09:30:00','receso','Receso mañana 1',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(172,5,5,'09:30:00','10:15:00','clase','Clase 4',4,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(173,5,6,'10:15:00','11:00:00','clase','Clase 5',5,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(174,5,7,'11:00:00','11:30:00','receso','Receso mañana 2',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(175,5,8,'11:30:00','12:15:00','clase','Clase 6',6,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(176,5,9,'12:15:00','13:00:00','clase','Clase 7',7,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(177,5,10,'13:00:00','13:45:00','clase','Clase 8',8,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(178,5,11,'13:45:00','14:30:00','clase','Clase 9',9,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(179,5,12,'14:30:00','15:00:00','clase','Clase 10',10,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(180,5,13,'15:00:00','15:30:00','receso','Receso tarde 1',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(181,5,14,'15:30:00','16:15:00','clase','Clase 11',11,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(182,5,15,'16:15:00','17:00:00','clase','Clase 12',12,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(183,5,16,'17:00:00','17:30:00','receso','Receso tarde 2',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(184,5,17,'17:30:00','18:15:00','clase','Clase 13',13,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(185,5,18,'18:15:00','19:00:00','clase','Clase 14',14,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(186,5,19,'19:00:00','19:45:00','clase','Clase 15',15,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(187,5,20,'19:45:00','20:00:00','clase','Clase 16',16,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(188,6,1,'13:00:00','13:45:00','clase','Clase 1',1,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(189,6,2,'13:45:00','14:30:00','clase','Clase 2',2,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(190,6,3,'14:30:00','15:00:00','clase','Clase 3',3,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(191,6,4,'15:00:00','15:30:00','receso','Receso tarde 1',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(192,6,5,'15:30:00','16:15:00','clase','Clase 4',4,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(193,6,6,'16:15:00','17:00:00','clase','Clase 5',5,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(194,6,7,'17:00:00','17:30:00','receso','Receso tarde 2',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(195,6,8,'17:30:00','18:15:00','clase','Clase 6',6,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(196,6,9,'18:15:00','19:00:00','clase','Clase 7',7,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(197,6,10,'19:00:00','19:45:00','clase','Clase 8',8,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(198,6,11,'19:45:00','20:00:00','clase','Clase 9',9,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(199,7,1,'13:00:00','13:45:00','clase','Clase 1',1,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(200,7,2,'13:45:00','14:30:00','clase','Clase 2',2,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(201,7,3,'14:30:00','15:00:00','clase','Clase 3',3,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(202,7,4,'15:00:00','15:30:00','receso','Receso tarde 1',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(203,7,5,'15:30:00','16:15:00','clase','Clase 4',4,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(204,7,6,'16:15:00','17:00:00','clase','Clase 5',5,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(205,7,7,'17:00:00','17:30:00','receso','Receso tarde 2',NULL,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(206,7,8,'17:30:00','18:15:00','clase','Clase 6',6,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(207,7,9,'18:15:00','19:00:00','clase','Clase 7',7,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(208,7,10,'19:00:00','19:45:00','clase','Clase 8',8,'2026-10-07 13:32:16','2026-10-07 13:32:16'),(209,7,11,'19:45:00','20:00:00','clase','Clase 9',9,'2026-10-07 13:32:16','2026-10-07 13:32:16');
/*!40000 ALTER TABLE `bloques_horarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion_horarios`
--

DROP TABLE IF EXISTS `configuracion_horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracion_horarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `institucion` enum('colegio','academia') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel` enum('primaria','secundaria','academia') COLLATE utf8mb4_unicode_ci NOT NULL,
  `turno` enum('mañana','tarde','completo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `duracion_bloque_minutos` int NOT NULL DEFAULT '45',
  `año_academico` int NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_config_horario` (`institucion`,`nivel`,`turno`,`año_academico`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion_horarios`
--

LOCK TABLES `configuracion_horarios` WRITE;
/*!40000 ALTER TABLE `configuracion_horarios` DISABLE KEYS */;
INSERT INTO `configuracion_horarios` VALUES (1,'colegio','primaria','mañana','Primaria Mañana 2026','07:00:00','12:00:00',45,2026,1,NULL,'2026-09-21 14:51:49','2026-10-07 13:32:16',NULL),(2,'colegio','secundaria','mañana','Secundaria Mañana 2026','07:00:00','12:00:00',45,2026,1,NULL,'2026-09-21 14:51:49','2026-10-07 13:32:16',NULL),(3,'academia','academia','mañana','Academia Mañana 2026','07:00:00','12:00:00',45,2026,1,NULL,'2026-09-21 14:51:49','2026-10-07 13:32:16',NULL),(4,'academia','academia','tarde','Academia Tarde 2026','13:00:00','20:00:00',90,2026,1,NULL,'2026-09-21 14:51:49','2026-10-07 13:32:16',NULL),(5,'academia','academia','completo','Academia Completo 2026','07:00:00','20:00:00',45,2026,1,NULL,'2026-09-21 14:51:49','2026-10-07 13:32:16',NULL),(6,'colegio','primaria','tarde','Primaria Tarde 2026','13:00:00','20:00:00',45,2026,1,NULL,'2026-10-07 13:32:15','2026-10-07 13:32:16',NULL),(7,'colegio','secundaria','tarde','Secundaria Tarde 2026','13:00:00','20:00:00',45,2026,1,NULL,'2026-10-07 13:32:15','2026-10-07 13:32:16',NULL);
/*!40000 ALTER TABLE `configuracion_horarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cursos`
--

DROP TABLE IF EXISTS `cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cursos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `horas_semanales` int NOT NULL DEFAULT '4',
  `duracion_minutos` int NOT NULL DEFAULT '60',
  `nivel` enum('primaria','secundaria','academia','todos') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'todos',
  `tipo` enum('obligatorio','electivo','taller') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'obligatorio',
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3490dc',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cursos_codigo_unique` (`codigo`),
  KEY `cursos_codigo_index` (`codigo`),
  KEY `cursos_nombre_index` (`nombre`),
  KEY `cursos_nivel_index` (`nivel`),
  KEY `cursos_activo_index` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cursos`
--

LOCK TABLES `cursos` WRITE;
/*!40000 ALTER TABLE `cursos` DISABLE KEYS */;
INSERT INTO `cursos` VALUES (1,'MAT-PRI','Matemáticas Primaria','Matemáticas básicas para primaria',6,60,'primaria','obligatorio','#3490dc',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(2,'COM-PRI','Comunicación Primaria','Lengua y literatura para primaria',6,60,'primaria','obligatorio','#e74c3c',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(3,'CT-PRI','Ciencia y Tecnología Primaria','Ciencias naturales y tecnología',4,60,'primaria','obligatorio','#2ecc71',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(4,'PER-PRI','Personal Social Primaria','Historia, geografía y formación cívica',3,60,'primaria','obligatorio','#f39c12',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(5,'ING-PRI','Inglés Primaria','Inglés nivel básico',3,60,'primaria','obligatorio','#9b59b6',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(6,'ART-PRI','Arte y Cultura','Arte, música y cultura',2,60,'primaria','electivo','#1abc9c',1,NULL,'2026-09-21 14:51:49','2026-09-28 16:39:35',NULL),(7,'MAT-SEC','Matemáticas Secundaria','Matemáticas avanzadas para secundaria',6,60,'secundaria','obligatorio','#3490dc',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(8,'COM-SEC','Comunicación Secundaria','Lengua y literatura avanzada',5,60,'secundaria','obligatorio','#e74c3c',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(9,'ING-SEC','Inglés Secundaria','Inglés nivel intermedio-avanzado',4,60,'secundaria','obligatorio','#f39c12',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(10,'CIE-SEC','Ciencias Naturales Secundaria','Biología, química y física',4,60,'secundaria','obligatorio','#2ecc71',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(11,'SOC-SEC','Historia y Geografía Secundaria','Historia, geografía y ciencias sociales',4,60,'secundaria','obligatorio','#9b59b6',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(12,'MAT-ACA','Matemáticas Academia','Matemáticas pre-universitaria',8,90,'academia','obligatorio','#3490dc',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(13,'COM-ACA','Comunicación Academia','Lengua y literatura pre-universitaria',6,90,'academia','obligatorio','#e74c3c',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(14,'ING-ACA','Inglés Academia','Inglés nivel avanzado',4,90,'academia','obligatorio','#f39c12',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(15,'CIE-ACA','Ciencias Integradas Academia','Ciencias integradas pre-universitaria',6,90,'academia','obligatorio','#2ecc71',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(16,'ANT-ACA','Anatomia','Enseña sobre le cuerpo humano',6,45,'todos','electivo','#ff0026',1,NULL,'2026-10-06 13:47:53','2026-10-06 13:47:53',NULL);
/*!40000 ALTER TABLE `cursos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disponibilidad_profesor`
--

DROP TABLE IF EXISTS `disponibilidad_profesor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disponibilidad_profesor` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `profesor_id` bigint unsigned NOT NULL,
  `dia_semana` enum('lunes','martes','miércoles','jueves','viernes','sábado','domingo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `tipo` enum('disponible','no_disponible') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponible',
  `turno` enum('mañana','tarde','noche') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `institucion` enum('colegio','academia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'colegio',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `disp_prof_unique` (`profesor_id`,`dia_semana`,`hora_inicio`,`institucion`),
  KEY `disponibilidad_profesor_profesor_id_index` (`profesor_id`),
  KEY `disponibilidad_profesor_dia_semana_index` (`dia_semana`),
  KEY `disponibilidad_profesor_turno_index` (`turno`),
  CONSTRAINT `disponibilidad_profesor_profesor_id_foreign` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disponibilidad_profesor`
--

LOCK TABLES `disponibilidad_profesor` WRITE;
/*!40000 ALTER TABLE `disponibilidad_profesor` DISABLE KEYS */;
INSERT INTO `disponibilidad_profesor` VALUES (1,1,'lunes','07:00:00','13:00:00','disponible','mañana','colegio','Disponible todo el día lunes','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(2,1,'martes','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(3,1,'miércoles','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(4,1,'jueves','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(5,1,'viernes','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(6,2,'lunes','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(7,2,'martes','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(8,2,'miércoles','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(9,2,'jueves','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(10,2,'viernes','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(11,3,'lunes','13:00:00','19:00:00','disponible','tarde','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(12,3,'martes','13:00:00','19:00:00','disponible','tarde','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(13,3,'miércoles','13:00:00','19:00:00','disponible','tarde','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(14,3,'jueves','13:00:00','19:00:00','disponible','tarde','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(15,3,'viernes','13:00:00','19:00:00','disponible','tarde','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(16,4,'lunes','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(17,4,'martes','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(18,4,'jueves','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(19,4,'viernes','07:00:00','13:00:00','disponible','mañana','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(20,5,'lunes','13:00:00','19:00:00','disponible','tarde','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(21,5,'miércoles','13:00:00','19:00:00','disponible','tarde','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(22,5,'viernes','13:00:00','19:00:00','disponible','tarde','colegio',NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(23,3,'jueves','06:30:00','08:00:00','disponible','mañana','academia',NULL,'2026-09-28 16:48:17','2026-09-28 16:51:13',NULL),(24,3,'jueves','17:00:00','18:00:00','disponible','tarde','academia',NULL,'2026-09-28 16:51:13','2026-09-28 16:51:13',NULL),(25,3,'viernes','16:00:00','17:00:00','disponible','tarde','academia',NULL,'2026-09-28 16:51:13','2026-09-28 16:51:13',NULL),(26,6,'jueves','16:00:00','17:00:00','disponible','tarde','academia',NULL,'2026-09-28 16:58:19','2026-09-28 16:58:19',NULL),(27,6,'viernes','15:00:00','16:00:00','disponible','tarde','academia',NULL,'2026-09-28 16:58:19','2026-09-28 16:58:19',NULL),(28,6,'viernes','17:00:00','18:00:00','disponible','tarde','academia',NULL,'2026-09-28 16:58:20','2026-09-28 16:58:20',NULL),(29,7,'jueves','15:00:00','16:00:00','disponible','tarde','academia',NULL,'2026-09-28 17:24:20','2026-09-28 17:24:20',NULL),(30,7,'viernes','16:00:00','18:00:00','disponible','tarde','academia',NULL,'2026-09-28 17:24:20','2026-09-28 17:24:20',NULL),(31,8,'jueves','13:00:00','14:00:00','disponible','tarde','academia',NULL,'2026-10-06 13:57:36','2026-10-06 13:57:36',NULL),(32,8,'jueves','15:00:00','16:00:00','disponible','tarde','academia',NULL,'2026-10-06 13:57:36','2026-10-06 13:57:36',NULL),(33,8,'viernes','09:00:00','10:00:00','disponible','mañana','academia',NULL,'2026-10-06 13:57:37','2026-10-06 13:57:37',NULL),(34,8,'viernes','16:00:00','18:00:00','disponible','tarde','academia',NULL,'2026-10-06 13:57:37','2026-10-06 13:57:37',NULL),(35,8,'sábado','09:00:00','10:00:00','disponible','mañana','academia',NULL,'2026-10-06 13:57:37','2026-10-06 13:57:37',NULL);
/*!40000 ALTER TABLE `disponibilidad_profesor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grados`
--

DROP TABLE IF EXISTS `grados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grados` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel` enum('primaria','secundaria','academia') COLLATE utf8mb4_unicode_ci NOT NULL,
  `grado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seccion` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_completo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidad_maxima` int NOT NULL DEFAULT '35',
  `numero_estudiantes` int NOT NULL DEFAULT '0',
  `año_academico` int NOT NULL,
  `turno` enum('mañana','tarde','noche','completo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mañana',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grados_codigo_unique` (`codigo`),
  UNIQUE KEY `grados_nivel_grado_seccion_año_academico_unique` (`nivel`,`grado`,`seccion`,`año_academico`),
  KEY `grados_nivel_index` (`nivel`),
  KEY `grados_año_academico_index` (`año_academico`),
  KEY `grados_activo_index` (`activo`),
  KEY `grados_codigo_index` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grados`
--

LOCK TABLES `grados` WRITE;
/*!40000 ALTER TABLE `grados` DISABLE KEYS */;
INSERT INTO `grados` VALUES (1,'1P-A','primaria','1ro','A','1ro Primaria A',30,28,2024,'mañana',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(2,'1P-B','primaria','1ro','B','1ro Primaria B',30,25,2024,'tarde',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(3,'2P-A','primaria','2do','A','2do Primaria A',30,30,2024,'mañana',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(4,'3P-A','primaria','3ro','A','3ro Primaria A',30,27,2024,'mañana',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(5,'4P-A','primaria','4to','A','4to Primaria A',30,29,2024,'mañana',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(6,'5P-A','primaria','5to','A','5to Primaria A',30,26,2024,'mañana',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(7,'6P-A','primaria','6to','A','6to Primaria A',30,24,2024,'mañana',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(8,'1S-A','secundaria','1ro','A','1ro Secundaria A',35,32,2024,'mañana',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(9,'1S-B','secundaria','1ro','B','1ro Secundaria B',35,30,2024,'tarde',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(10,'2S-A','secundaria','2do','A','2do Secundaria A',35,33,2024,'mañana',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(11,'3S-A','secundaria','3ro','A','3ro Secundaria A',35,31,2024,'mañana',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(12,'4S-A','secundaria','4to','A','4to Secundaria A',35,28,2024,'mañana',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(13,'5S-A','secundaria','5to','A','5to Secundaria A',35,27,2024,'mañana',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(14,'ACA-A','academia','Pre','A','Pre Academia A',45,40,2024,'noche',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(15,'ACA-B','academia','1er','A','1er Academia A',45,38,2024,'noche',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(16,'ACA-C','academia','2do','A','2do Academia A',45,35,2024,'noche',1,NULL,'2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(17,'ACA-D','academia','3ro','A','3ro Academia A',25,22,2026,'completo',1,NULL,'2026-10-06 13:56:05','2026-10-06 13:56:05',NULL);
/*!40000 ALTER TABLE `grados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_cambios`
--

DROP TABLE IF EXISTS `historial_cambios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_cambios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `horario_id` bigint unsigned DEFAULT NULL,
  `usuario_id` bigint unsigned DEFAULT NULL,
  `accion` enum('crear','actualizar','eliminar','restaurar','revertir') COLLATE utf8mb4_unicode_ci NOT NULL,
  `datos_anteriores` json DEFAULT NULL,
  `datos_nuevos` json DEFAULT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_usuario` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `historial_cambios_horario_id_index` (`horario_id`),
  KEY `historial_cambios_usuario_id_index` (`usuario_id`),
  KEY `historial_cambios_accion_index` (`accion`),
  KEY `historial_cambios_created_at_index` (`created_at`),
  CONSTRAINT `historial_cambios_horario_id_foreign` FOREIGN KEY (`horario_id`) REFERENCES `horarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `historial_cambios_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_cambios`
--

LOCK TABLES `historial_cambios` WRITE;
/*!40000 ALTER TABLE `historial_cambios` DISABLE KEYS */;
INSERT INTO `historial_cambios` VALUES (1,18,3,'actualizar','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"07:45\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"miércoles\", \"updated_at\": \"2026-09-21T14:51:49.000000Z\", \"hora_inicio\": \"07:00\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": null, \"periodo_academico\": \"2026-2027\"}','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"07:55\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:02:31.000000Z\", \"hora_inicio\": \"07:10\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:02:32','2026-09-28 15:02:32'),(2,9,3,'actualizar','{\"id\": 9, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 6, \"version\": 1, \"curso_id\": 2, \"grado_id\": 2, \"hora_fin\": \"08:30\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"martes\", \"updated_at\": \"2026-09-21T14:51:49.000000Z\", \"hora_inicio\": \"07:45\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": null, \"periodo_academico\": \"2026-2027\"}','{\"id\": 9, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 6, \"version\": 1, \"curso_id\": 2, \"grado_id\": 2, \"hora_fin\": \"08:55\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:02:39.000000Z\", \"hora_inicio\": \"08:10\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:02:39','2026-09-28 15:02:39'),(3,18,3,'actualizar','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"07:55\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:02:31.000000Z\", \"hora_inicio\": \"07:10\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"07:45\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:02:46.000000Z\", \"hora_inicio\": \"07:00\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:02:46','2026-09-28 15:02:46'),(4,18,3,'actualizar','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"07:45\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:02:46.000000Z\", \"hora_inicio\": \"07:00\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"10:30\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:02:59.000000Z\", \"hora_inicio\": \"09:45\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:02:59','2026-09-28 15:02:59'),(5,9,3,'actualizar','{\"id\": 9, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 6, \"version\": 1, \"curso_id\": 2, \"grado_id\": 2, \"hora_fin\": \"08:55\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:02:39.000000Z\", \"hora_inicio\": \"08:10\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','{\"id\": 9, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 6, \"version\": 1, \"curso_id\": 2, \"grado_id\": 2, \"hora_fin\": \"07:50\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:03:07.000000Z\", \"hora_inicio\": \"07:05\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:03:07','2026-09-28 15:03:07'),(6,9,3,'actualizar','{\"id\": 9, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 6, \"version\": 1, \"curso_id\": 2, \"grado_id\": 2, \"hora_fin\": \"07:50\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:03:07.000000Z\", \"hora_inicio\": \"07:05\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','{\"id\": 9, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 6, \"version\": 1, \"curso_id\": 2, \"grado_id\": 2, \"hora_fin\": \"07:45\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:03:11.000000Z\", \"hora_inicio\": \"07:00\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:03:11','2026-09-28 15:03:11'),(7,9,3,'actualizar','{\"id\": 9, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 6, \"version\": 1, \"curso_id\": 2, \"grado_id\": 2, \"hora_fin\": \"07:45\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:03:11.000000Z\", \"hora_inicio\": \"07:00\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','{\"id\": 9, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 6, \"version\": 1, \"curso_id\": 2, \"grado_id\": 2, \"hora_fin\": \"08:30\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"martes\", \"updated_at\": \"2026-09-28T15:04:03.000000Z\", \"hora_inicio\": \"07:45\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:04:03','2026-09-28 15:04:03'),(8,18,3,'actualizar','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"10:30\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:02:59.000000Z\", \"hora_inicio\": \"09:45\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"07:45\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"miercoles\", \"updated_at\": \"2026-09-28T15:04:12.000000Z\", \"hora_inicio\": \"07:00\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:04:12','2026-09-28 15:04:12'),(9,19,3,'actualizar','{\"id\": 19, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"08:30\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"miércoles\", \"updated_at\": \"2026-09-21T14:51:49.000000Z\", \"hora_inicio\": \"07:45\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": null, \"periodo_academico\": \"2026-2027\"}','{\"id\": 19, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"08:55\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:04:20.000000Z\", \"hora_inicio\": \"08:10\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:04:20','2026-09-28 15:04:20'),(10,18,3,'actualizar','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"07:45\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"miércoles\", \"updated_at\": \"2026-09-28T15:04:12.000000Z\", \"hora_inicio\": \"07:00\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"08:20\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"miercoles\", \"updated_at\": \"2026-09-28T15:04:24.000000Z\", \"hora_inicio\": \"07:35\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:04:24','2026-09-28 15:04:24'),(11,18,3,'actualizar','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"08:20\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"miércoles\", \"updated_at\": \"2026-09-28T15:04:24.000000Z\", \"hora_inicio\": \"07:35\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"07:45\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"miercoles\", \"updated_at\": \"2026-09-28T15:04:32.000000Z\", \"hora_inicio\": \"07:00\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:04:32','2026-09-28 15:04:32'),(12,19,3,'actualizar','{\"id\": 19, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"08:55\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:04:20.000000Z\", \"hora_inicio\": \"08:10\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','{\"id\": 19, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"08:30\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"miercoles\", \"updated_at\": \"2026-09-28T15:04:35.000000Z\", \"hora_inicio\": \"07:45\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:04:35','2026-09-28 15:04:35'),(13,18,3,'actualizar','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"07:45\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"miércoles\", \"updated_at\": \"2026-09-28T15:04:32.000000Z\", \"hora_inicio\": \"07:00\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"09:00\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:08:50.000000Z\", \"hora_inicio\": \"08:15\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:08:50','2026-09-28 15:08:50'),(14,18,3,'actualizar','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"09:00\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"jueves\", \"updated_at\": \"2026-09-28T15:08:50.000000Z\", \"hora_inicio\": \"08:15\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','{\"id\": 18, \"tipo\": \"regular\", \"turno\": \"mañana\", \"estado\": \"activo\", \"semana\": 1, \"aula_id\": 1, \"version\": 1, \"curso_id\": 8, \"grado_id\": 9, \"hora_fin\": \"07:45\", \"creado_por\": \"sistema_automatico\", \"created_at\": \"2026-09-21T14:51:49.000000Z\", \"deleted_at\": null, \"dia_semana\": \"miercoles\", \"updated_at\": \"2026-09-28T15:09:04.000000Z\", \"hora_inicio\": \"07:00\", \"institucion\": \"colegio\", \"observacion\": \"Generado automáticamente\", \"profesor_id\": 2, \"modificado_por\": \"frontend\", \"periodo_academico\": \"2026-2027\"}','Cambio manual mediante arrastrar y soltar','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-28 15:09:04','2026-09-28 15:09:04');
/*!40000 ALTER TABLE `historial_cambios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios`
--

DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `profesor_id` bigint unsigned NOT NULL,
  `curso_id` bigint unsigned NOT NULL,
  `aula_id` bigint unsigned NOT NULL,
  `grado_id` bigint unsigned NOT NULL,
  `dia_semana` enum('lunes','martes','miércoles','jueves','viernes','sábado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `turno` enum('mañana','tarde','noche') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `institucion` enum('colegio','academia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'colegio',
  `tipo` enum('regular','recuperacion','cambio') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'regular',
  `semana` int NOT NULL DEFAULT '1',
  `periodo_academico` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('activo','modificado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `version` int NOT NULL DEFAULT '1',
  `creado_por` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modificado_por` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_profesor_horario` (`profesor_id`,`dia_semana`,`hora_inicio`,`periodo_academico`,`institucion`),
  UNIQUE KEY `unique_aula_horario` (`aula_id`,`dia_semana`,`hora_inicio`,`periodo_academico`,`institucion`),
  UNIQUE KEY `unique_grado_horario` (`grado_id`,`dia_semana`,`hora_inicio`,`periodo_academico`,`institucion`),
  KEY `horarios_curso_id_foreign` (`curso_id`),
  KEY `horarios_dia_semana_index` (`dia_semana`),
  KEY `horarios_turno_index` (`turno`),
  KEY `horarios_estado_index` (`estado`),
  KEY `horarios_periodo_academico_index` (`periodo_academico`),
  KEY `horarios_profesor_id_dia_semana_index` (`profesor_id`,`dia_semana`),
  KEY `horarios_aula_id_dia_semana_index` (`aula_id`,`dia_semana`),
  KEY `horarios_grado_id_dia_semana_index` (`grado_id`,`dia_semana`),
  CONSTRAINT `horarios_aula_id_foreign` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `horarios_curso_id_foreign` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `horarios_grado_id_foreign` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `horarios_profesor_id_foreign` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES (1,2,2,1,1,'lunes','07:00:00','07:45:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(2,2,2,1,1,'lunes','07:45:00','08:30:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(3,2,2,1,1,'lunes','08:50:00','09:35:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(4,2,2,1,1,'lunes','09:35:00','10:20:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(5,2,2,1,1,'lunes','10:40:00','11:25:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(6,2,2,1,1,'lunes','11:25:00','12:15:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(7,2,2,6,2,'lunes','12:15:00','12:55:00','tarde','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(8,2,2,6,2,'martes','07:00:00','07:45:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(9,2,2,6,2,'martes','07:45:00','08:30:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico','frontend','Generado automáticamente','2026-09-21 14:51:49','2026-09-28 15:04:03',NULL),(10,2,2,6,2,'martes','08:50:00','09:35:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(11,2,2,6,2,'martes','09:35:00','10:20:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(12,2,2,6,2,'martes','10:40:00','11:25:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(13,2,8,2,8,'lunes','08:30:00','09:15:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(14,2,8,2,8,'lunes','10:25:00','11:10:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(15,2,8,2,8,'martes','08:30:00','09:15:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(16,2,8,2,8,'martes','10:25:00','11:10:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(17,2,8,2,8,'martes','11:25:00','12:15:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(18,2,8,1,9,'miércoles','07:00:00','07:45:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico','frontend','Generado automáticamente','2026-09-21 14:51:49','2026-09-28 15:09:04',NULL),(19,2,8,1,9,'miércoles','07:45:00','08:30:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico','frontend','Generado automáticamente','2026-09-21 14:51:49','2026-09-28 15:04:35',NULL),(20,2,8,1,9,'miércoles','08:30:00','09:15:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(21,2,8,1,9,'miércoles','09:35:00','10:25:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(22,2,8,1,9,'miércoles','10:25:00','11:10:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(23,1,7,2,8,'miércoles','07:00:00','07:45:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(24,1,7,2,8,'miércoles','07:45:00','08:30:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(25,1,7,2,8,'miércoles','08:30:00','09:15:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(26,1,7,2,8,'miércoles','09:35:00','10:25:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(27,4,9,2,8,'jueves','07:00:00','07:45:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(28,4,9,2,8,'jueves','07:45:00','08:30:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(29,4,9,2,8,'jueves','08:30:00','09:15:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(30,4,9,2,8,'jueves','09:35:00','10:25:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(31,1,1,2,3,'lunes','07:00:00','07:45:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(32,1,1,2,3,'lunes','07:45:00','08:30:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(33,1,1,2,3,'lunes','08:50:00','09:35:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(34,1,1,2,3,'lunes','09:35:00','10:20:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(35,1,7,1,9,'lunes','08:30:00','09:15:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(36,1,7,1,9,'lunes','10:25:00','11:10:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(37,1,7,4,9,'lunes','11:25:00','12:15:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(38,1,7,1,9,'martes','07:00:00','07:45:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(39,1,1,1,1,'martes','07:45:00','08:30:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(40,1,1,1,1,'martes','08:50:00','09:35:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(41,1,1,1,1,'martes','09:35:00','10:20:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(42,1,1,1,1,'martes','10:40:00','11:25:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(43,1,1,6,2,'miércoles','08:50:00','09:35:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(44,1,1,6,2,'miércoles','10:40:00','11:25:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(45,1,1,6,2,'miércoles','11:25:00','12:15:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL),(46,1,1,6,2,'miércoles','12:15:00','12:55:00','tarde','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(47,4,5,2,3,'martes','07:00:00','07:45:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(48,4,5,2,3,'martes','07:45:00','08:30:00','mañana','colegio','regular',1,'2026-2027','activo',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(49,4,5,2,3,'martes','08:50:00','09:35:00','mañana','colegio','regular',1,'2026-2027','cancelado',1,'sistema_automatico',NULL,'Generado automáticamente','2026-09-21 14:51:49','2026-10-07 13:32:15',NULL);
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_08_17_135415_create_profesores_table',1),(5,'2026_08_17_135523_create_grados_table',1),(6,'2026_08_17_135524_create_alumnos_table',1),(7,'2026_08_17_135525_create_cursos_table',1),(8,'2026_08_17_135526_create_aulas_table',1),(9,'2026_08_17_135527_create_disponibilidad_profesor_table',1),(10,'2026_08_17_135529_create_profesor_curso_table',1),(11,'2026_08_17_135530_create_horarios_table',1),(12,'2026_08_17_135531_create_historial_cambios_table',1),(13,'2026_08_31_111542_add_institucion_to_profesores_table',1),(14,'2026_08_31_111543_add_institucion_to_disponibilidad_profesor_table',1),(15,'2026_08_31_111544_add_institucion_to_horarios_table',1),(16,'2026_08_31_111545_add_institucion_to_profesor_curso_table',1),(17,'2026_09_21_000003_create_configuracion_horarios_table',1),(18,'2026_09_21_000004_create_bloques_horarios_table',1),(19,'2026_09_22_091535_create_personal_access_tokens_table',2),(20,'2026_09_28_000001_add_colegio_tarde_configuraciones',3),(21,'2026_09_29_000001_preserve_historial_when_horario_is_deleted',3),(22,'2026_09_29_000002_add_location_fields_to_aulas',3),(23,'2026_09_29_000003_normalize_recess_blocks',3),(24,'2026_09_30_000001_expand_historial_accion_enum',3),(25,'2026_10_05_000001_repair_schedule_blocks_before_recess',3),(26,'2026_10_05_000002_add_aula_to_profesor_curso_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
INSERT INTO `password_reset_tokens` VALUES ('admin@nextlevel.com','$2y$12$6l3x/XaCsrLLXwvqUI1BkuYY/glYqnxtsw1v7mtXxQEC/WdHxfzQq','2026-09-23 16:14:31'),('angelgonzalesyt18@gmail.com','$2y$12$6tnfcxDcXEJCg9ZwCgRaneDa/nz5W5XLqkDPLZff1nINW6cmyeOZ.','2026-10-06 13:29:07');
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (18,'App\\Models\\User',3,'auth_token','7e13bcaed4fe656a7ef09257f0873a82304d88aaedbad41c0c50cae5cc437e8e','[\"*\"]','2026-10-07 14:19:18',NULL,'2026-10-07 14:12:02','2026-10-07 14:19:18');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profesor_curso`
--

DROP TABLE IF EXISTS `profesor_curso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profesor_curso` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `profesor_id` bigint unsigned NOT NULL,
  `curso_id` bigint unsigned NOT NULL,
  `grado_id` bigint unsigned NOT NULL,
  `aula_id` bigint unsigned DEFAULT NULL,
  `horas_asignadas` int NOT NULL DEFAULT '0',
  `rol` enum('titular','asistente','suplente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'titular',
  `institucion` enum('colegio','academia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'colegio',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_asignacion` (`profesor_id`,`curso_id`,`grado_id`,`institucion`),
  KEY `profesor_curso_profesor_id_index` (`profesor_id`),
  KEY `profesor_curso_curso_id_index` (`curso_id`),
  KEY `profesor_curso_grado_id_index` (`grado_id`),
  KEY `profesor_curso_aula_id_foreign` (`aula_id`),
  CONSTRAINT `profesor_curso_aula_id_foreign` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `profesor_curso_curso_id_foreign` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profesor_curso_grado_id_foreign` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profesor_curso_profesor_id_foreign` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesor_curso`
--

LOCK TABLES `profesor_curso` WRITE;
/*!40000 ALTER TABLE `profesor_curso` DISABLE KEYS */;
INSERT INTO `profesor_curso` VALUES (1,1,1,1,NULL,4,'titular','colegio',1,'Matemáticas 1ro Primaria A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(2,1,1,2,NULL,4,'titular','colegio',1,'Matemáticas 1ro Primaria B','2026-09-21 14:51:49','2026-09-21 14:51:49'),(3,1,1,3,NULL,4,'titular','colegio',1,'Matemáticas 2do Primaria A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(4,1,7,8,NULL,4,'titular','colegio',1,'Matemáticas 1ro Secundaria A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(5,1,7,9,NULL,4,'titular','colegio',1,'Matemáticas 1ro Secundaria B','2026-09-21 14:51:49','2026-09-21 14:51:49'),(6,1,12,14,NULL,8,'titular','colegio',1,'Matemáticas Pre Academia A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(7,2,2,1,NULL,6,'titular','colegio',1,'Comunicación 1ro Primaria A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(8,2,2,2,NULL,6,'titular','colegio',1,'Comunicación 1ro Primaria B','2026-09-21 14:51:49','2026-09-21 14:51:49'),(9,2,8,8,NULL,5,'titular','colegio',1,'Comunicación 1ro Secundaria A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(10,2,8,9,NULL,5,'titular','colegio',1,'Comunicación 1ro Secundaria B','2026-09-21 14:51:49','2026-09-21 14:51:49'),(11,2,13,14,NULL,6,'titular','colegio',1,'Comunicación Pre Academia A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(12,3,3,3,NULL,4,'titular','colegio',1,'Ciencia y Tecnología 2do Primaria A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(13,3,10,10,NULL,4,'titular','colegio',1,'Ciencias Naturales 2do Secundaria A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(14,3,15,14,NULL,6,'titular','colegio',1,'Ciencias Integradas Pre Academia A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(15,4,5,3,NULL,3,'titular','colegio',1,'Inglés 2do Primaria A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(16,4,9,8,NULL,4,'titular','colegio',1,'Inglés 1ro Secundaria A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(17,4,14,14,NULL,4,'titular','colegio',1,'Inglés Pre Academia A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(18,5,4,4,NULL,3,'titular','colegio',1,'Personal Social 3ro Primaria A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(19,5,11,10,NULL,4,'titular','colegio',1,'Historia y Geografía 2do Secundaria A','2026-09-21 14:51:49','2026-09-21 14:51:49'),(20,3,6,4,NULL,2,'titular','colegio',1,NULL,'2026-09-28 16:45:24','2026-09-28 16:45:24'),(21,6,14,16,NULL,3,'titular','academia',1,'No le gustan los niños','2026-09-28 16:59:57','2026-09-28 16:59:57'),(22,7,14,15,NULL,3,'titular','academia',1,'Usan diccionarios','2026-09-28 17:25:33','2026-09-28 17:25:33'),(23,8,16,17,NULL,4,'titular','academia',1,NULL,'2026-10-06 13:58:49','2026-10-06 13:58:49');
/*!40000 ALTER TABLE `profesor_curso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profesores`
--

DROP TABLE IF EXISTS `profesores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profesores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_paterno` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_materno` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dni` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sexo` enum('masculino','femenino') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `especialidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carga_horaria_maxima` int NOT NULL DEFAULT '30',
  `estado` enum('activo','inactivo','licencia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `institucion` enum('colegio','academia','ambos') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ambos',
  `carga_horaria_actual` int NOT NULL DEFAULT '0',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profesores_codigo_unique` (`codigo`),
  UNIQUE KEY `profesores_email_unique` (`email`),
  UNIQUE KEY `profesores_dni_unique` (`dni`),
  KEY `profesores_nombre_apellido_paterno_index` (`nombre`,`apellido_paterno`),
  KEY `profesores_email_index` (`email`),
  KEY `profesores_estado_index` (`estado`),
  KEY `profesores_especialidad_index` (`especialidad`),
  KEY `profesores_dni_index` (`dni`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesores`
--

LOCK TABLES `profesores` WRITE;
/*!40000 ALTER TABLE `profesores` DISABLE KEYS */;
INSERT INTO `profesores` VALUES (1,'DOC-001','Carlos','García','Pérez','carlos.garcia@academia.edu','987654321','12345678','masculino','1985-06-15','Matemáticas',30,'activo','academia',24,'Profesor con 10 años de experiencia','2026-09-21 14:51:49','2026-10-06 14:02:40',NULL),(2,'DOC-002','María','Rodríguez','López','maria.rodriguez@academia.edu','987654322','87654321','femenino','1990-03-20','Comunicación',30,'activo','academia',20,'Especialista en lingüística','2026-09-21 14:51:49','2026-10-06 14:03:21',NULL),(3,'DOC-003','Juan','Martínez','Sánchez','juan.martinez@academia.edu','987654323','23456789','masculino','1988-11-05','Ciencias Naturales',32,'activo','ambos',16,'Biólogo con maestría','2026-09-21 14:51:49','2026-09-28 16:45:24',NULL),(4,'DOC-004','Ana','Torres','Flores','ana.torres@academia.edu','987654324','34567890','femenino','1992-08-30','Inglés',25,'activo','ambos',20,'Certificada en Cambridge','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(5,'DOC-005','Luis','Ramírez','Gómez','luis.ramirez@academia.edu','987654325','45678901','masculino','1978-01-12','Historia',26,'activo','ambos',18,'Historiador con 15 años','2026-09-21 14:51:49','2026-09-21 14:51:49',NULL),(6,'PROF-006','MIGUEL ÁNGEL','GONZALES','INUMA','Angelgonzalesyt18@gmail.com','901076529','60185375','masculino','2026-06-05','Progracacion Con AI',30,'activo','academia',3,'Crea correos nuevos para tener ChatGPT Plus','2026-09-28 16:55:40','2026-09-28 17:04:12','2026-09-28 17:04:12'),(7,'PROF-007','Isacc','Perez','Aspajo','isaacperez@gmail.com','901076527','50987641','masculino','2026-08-05','Ingles',30,'activo','academia',3,NULL,'2026-09-28 17:23:36','2026-10-06 14:06:26','2026-10-06 14:06:26'),(8,'PROF-008','Juliana Cristina','Ramirez','Loreano','julianaramirez@gmail.com','956874123','62147893','femenino','1990-05-02','Anatomia',28,'activo','ambos',4,'Esta casada con un crespo que le fue infiel.','2026-10-06 13:44:47','2026-10-06 16:32:26','2026-10-06 16:32:26');
/*!40000 ALTER TABLE `profesores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador','admin@nextlevel.com',NULL,'$2y$12$9HWC7D2xHPLi31YtC9Pk.eNWOGcwrOGtcpC2RoKk8MUAewNkKnpwq',NULL,'2026-09-23 13:00:41','2026-10-07 14:00:59'),(2,'Jhosep','jhosep@nextlevel.com',NULL,'$2y$12$Ol.cpSEpQy3G2k4oBT9oWuzpOF1yZKG1gFycsZuHLh36uwtK2Klt.',NULL,'2026-09-23 13:00:42','2026-10-07 14:00:59'),(3,'Jhosep Huiza','jhosephuiza480@gmail.com',NULL,'$2y$12$1.bIr/e1RRxUNpzskqIWs.F1pV8Ctq.yuOvHcH2q6CIqvhLbn9O4S','mLIDPoTGGFSh1UnA8wqjViTZWthJiN56VyDbx65Kt7Keug4SB0aFGZzHJ5kt','2026-09-23 16:31:39','2026-10-06 12:50:11'),(4,'Angel Gonzales','angelgonzalesyt18@gmail.com',NULL,'$2y$12$cgACy2VwV/WgeyYj5WlP/O4Nc6RNm8EkKHlJDS8pxjN0jjgDTM0We',NULL,'2026-10-06 13:28:06','2026-10-06 13:28:06');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-07  9:35:03
