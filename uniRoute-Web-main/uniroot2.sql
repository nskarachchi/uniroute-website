-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: uniroot
-- ------------------------------------------------------
-- Server version	8.0.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `buses`
--

DROP TABLE IF EXISTS buses;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE buses (
  id int NOT NULL AUTO_INCREMENT,
  bus_id varchar(50) NOT NULL,
  latitude double DEFAULT NULL,
  longitude double DEFAULT NULL,
  last_updated timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` varchar(50) DEFAULT 'active',
  route_id varchar(50) DEFAULT NULL,
  contact_number varchar(45) DEFAULT NULL,
  driver_email varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buses`
--

LOCK TABLES buses WRITE;
/*!40000 ALTER TABLE buses DISABLE KEYS */;
INSERT INTO buses VALUES (1,'1',6.9271,79.8446,'2025-05-03 18:27:31','active','1','121','brendon5860@gmail.com'),(2,'2',6.9147,79.8763,'2025-05-03 18:30:23','active','2','0765366990','brendon5860@gmail.com'),(3,'3',6.9157,79.86,'2025-05-04 02:16:57','active','2','123364','Sheherandinethma@gmail.com'),(4,'4',6.8528,79.865,'2025-05-04 02:16:57','active','3','0743013073','10952848@students.plymouth.ac.uk'),(5,'5',6.921,79.8764,'2025-05-04 02:28:47','active','8','0765366990','Sheherandinethma@gmail.com'),(6,'6',6.921,79.8764,'2025-05-04 02:29:17','active','7','0743013073','10952848@students.plymouth.ac.uk'),(7,'7',6.8528,79.865,'2025-05-04 02:29:20','active','6','0743013073','Sheherandinethma@gmail.com'),(8,'8',6.9271,79.8612,'2025-05-04 05:45:38','active','2','0743013073','abc1@gmail.com'),(9,'9',6.921,79.865,'2025-05-04 07:41:02','active','1','0765366990','brendondon2003@gmail.com'),(10,'10',6.9271,79.865,'2025-05-04 16:46:05','active',NULL,'01121232527','sample12@gmail.com');
/*!40000 ALTER TABLE buses ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=root@localhost*/ /*!50003 TRIGGER after_bus_insert AFTER INSERT ON buses FOR EACH ROW BEGIN
  DECLARE counter INT DEFAULT 1;
  WHILE counter <= 40 DO
    INSERT INTO seats (seat_number, bus_id)
    VALUES (LPAD(counter, 2, '0'), NEW.id);
    SET counter = counter + 1;
  END WHILE;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS customers;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE customers (
  id int NOT NULL AUTO_INCREMENT,
  seat_id int DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  email varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  phone varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  reserved_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  bus_id int DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES customers WRITE;
/*!40000 ALTER TABLE customers DISABLE KEYS */;
INSERT INTO customers VALUES (20,165,'dinethma','brendondon2003@gmail.com','12','2025-05-03 10:49:10',2),(21,165,'dinethma','brendondon2003@gmail.com','12','2025-05-03 10:49:59',2),(22,165,'dinethma','brendondon2003@gmail.com','12','2025-05-03 10:51:27',2),(23,122,'dinethma','brendondon2003@gmail.com','54321','2025-05-03 11:14:05',1),(24,122,'dinethma','brendondon2003@gmail.com','54321','2025-05-03 11:14:56',1),(25,122,'dinethma','brendondon2003@gmail.com','54321','2025-05-03 11:21:18',1),(26,122,'dinethma','brendondon2003@gmail.com','54321','2025-05-03 11:22:30',1),(27,122,'dinethma','brendondon2003@gmail.com','54321','2025-05-03 11:22:52',1),(28,122,'dinethma','brendondon2003@gmail.com','54321','2025-05-03 11:24:09',1),(29,122,'dinethma','brendondon2003@gmail.com','54321','2025-05-03 11:26:58',1),(30,122,'dinethma','brendondon2003@gmail.com','54321','2025-05-03 11:46:30',1),(31,122,'dinethma','brendondon2003@gmail.com','54321','2025-05-03 11:47:24',1),(32,166,'kd brendon','brendondon2003@gmail.com','0743013073','2025-05-03 12:35:06',2),(33,185,'kd brendon','dinethmasheheran@gmail.com','0765366990','2025-05-03 16:53:43',2),(34,403,'admin1','sheherandinethma@gmail.com','0743013073','2025-05-04 05:47:06',8),(35,137,'kd brendon','brendondon2003@gmail.com','0743013073','2025-05-04 08:50:26',1),(36,131,'kd brendon','brendondon2003@gmail.com','0743013073','2025-05-04 09:02:16',1),(37,203,'admin1','brendondon2003@gmail.com','0743013073','2025-05-04 16:51:52',3);
/*!40000 ALTER TABLE customers ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS feedback;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE feedback (
  id int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  contact varchar(10) NOT NULL,
  rating int NOT NULL,
  comments text,
  submitted_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback`
--

LOCK TABLES feedback WRITE;
/*!40000 ALTER TABLE feedback DISABLE KEYS */;
INSERT INTO feedback VALUES (1,'dinethma','brendondon2003@gmail.com','074212123',5,'good','2025-05-04 07:12:55'),(2,'sample1','admin@gmail.com','123',5,'good','2025-05-04 08:20:20'),(3,'sample','brendondon2003@gmail.com','123',5,'good','2025-05-04 16:56:09'),(4,'sample1','admin@gmail.com','123',5,'da','2025-05-04 16:58:13'),(5,'sample1','admin@gmail.com','111111111',2,'121','2025-05-04 17:10:24'),(6,'sample1','admin@gmail.com','111111111',2,'121','2025-05-04 17:10:48'),(7,'sample1','admin@gmail.com','7895',5,'mo','2025-05-04 17:12:46'),(8,'sample1','admin@gmail.com','121211',5,'121','2025-05-04 17:14:42'),(9,'sample1','brendondon2003@gmail.com','1',5,'12','2025-05-04 17:15:13'),(10,'sample1','admin@gmail.com','123',5,'n','2025-05-04 17:15:47');
/*!40000 ALTER TABLE feedback ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loged_user`
--

DROP TABLE IF EXISTS loged_user;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE loged_user (
  user_name int NOT NULL,
  PRIMARY KEY (user_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loged_user`
--

LOCK TABLES loged_user WRITE;
/*!40000 ALTER TABLE loged_user DISABLE KEYS */;
/*!40000 ALTER TABLE loged_user ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS messages;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE messages (
  id int NOT NULL AUTO_INCREMENT,
  sender_name varchar(100) DEFAULT NULL,
  bus_id varchar(20) DEFAULT NULL,
  message text,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES messages WRITE;
/*!40000 ALTER TABLE messages DISABLE KEYS */;
INSERT INTO messages VALUES (1,'Guest','01','hi','2025-04-06 19:03:50'),(2,'Guest','01','hi','2025-04-06 19:04:45'),(3,'Guest','01','hi','2025-04-07 03:51:11'),(4,'Guest','01','hi','2025-04-07 07:45:12'),(5,'Guest','1','hey','2025-05-03 18:58:42');
/*!40000 ALTER TABLE messages ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `new_table`
--

DROP TABLE IF EXISTS new_table;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE new_table (
  id int NOT NULL,
  seat_id int DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  email varchar(100) DEFAULT NULL,
  phone varchar(10) DEFAULT NULL,
  reserved_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `new_table`
--

LOCK TABLES new_table WRITE;
/*!40000 ALTER TABLE new_table DISABLE KEYS */;
/*!40000 ALTER TABLE new_table ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS password_resets;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE password_resets (
  email varchar(255) NOT NULL,
  token varchar(255) NOT NULL,
  expires_at datetime NOT NULL,
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (email),
  CONSTRAINT password_resets_ibfk_1 FOREIGN KEY (email) REFERENCES `user` (email) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES password_resets WRITE;
/*!40000 ALTER TABLE password_resets DISABLE KEYS */;
INSERT INTO password_resets VALUES ('brendon5860@gmail.com','d7767995a54290fe61aa2c414eda84c3e402b49d60b4883c666d50b50d06cf70','2025-04-30 17:36:01','2025-04-30 20:06:01'),('hello@gmail.com','9ff7246f8516df06f1d659d7dd192a596abd11531fd0b3d9c739d8c3dfb0c321','2025-04-07 02:34:33','2025-04-07 05:04:33'),('paw1@gmail.com','31388c3dfc9ca16e38d63e88b51af13a97248adbbdcc78396ee8e8454a85db6e','2025-04-07 02:32:58','2025-04-07 05:02:58');
/*!40000 ALTER TABLE password_resets ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS reservations;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE reservations (
  reservation_id int NOT NULL AUTO_INCREMENT,
  seat_id int NOT NULL,
  bus_id int NOT NULL,
  passenger_name varchar(100) NOT NULL,
  passenger_phone varchar(20) NOT NULL,
  reservation_time int NOT NULL,
  payment_status varchar(20) DEFAULT 'pending',
  PRIMARY KEY (reservation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES reservations WRITE;
/*!40000 ALTER TABLE reservations DISABLE KEYS */;
/*!40000 ALTER TABLE reservations ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `route`
--

DROP TABLE IF EXISTS route;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE route (
  route_id int NOT NULL AUTO_INCREMENT,
  route varchar(255) DEFAULT NULL,
  depature varchar(255) DEFAULT NULL,
  start_location varchar(255) DEFAULT NULL,
  end_location varchar(255) DEFAULT NULL,
  UNIQUE KEY route_id_UNIQUE (route_id)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `route`
--

LOCK TABLES route WRITE;
/*!40000 ALTER TABLE route DISABLE KEYS */;
INSERT INTO route VALUES (1,'maharagama - NSBM','9.00 am',NULL,NULL),(2,'colombo - NSBM','10.00 am',NULL,NULL),(3,'negombo - nsbm','11.00 am',NULL,NULL),(4,'kottawa - NSBM','11.00 am',NULL,NULL),(5,'galle - NSBM','7.00 am',NULL,NULL),(6,'awissawella - NSBM','7.00 am',NULL,NULL),(7,'Kadawatha - NSBM','7.00 am',NULL,NULL),(8,'Horana - NSBM','8.00 am',NULL,NULL),(9,'kelaniya - NSBM','9.00 am',NULL,NULL);
/*!40000 ALTER TABLE route ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seats`
--

DROP TABLE IF EXISTS seats;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE seats (
  seat_id int NOT NULL AUTO_INCREMENT,
  seat_number varchar(10) NOT NULL,
  bus_id int NOT NULL,
  is_reserved tinyint(1) DEFAULT '0',
  PRIMARY KEY (seat_id),
  UNIQUE KEY seat_number (seat_number,bus_id),
  KEY bus_id (bus_id),
  CONSTRAINT seats_ibfk_1 FOREIGN KEY (bus_id) REFERENCES buses (id) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=522 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seats`
--

LOCK TABLES seats WRITE;
/*!40000 ALTER TABLE seats DISABLE KEYS */;
INSERT INTO seats VALUES (122,'01',1,1),(123,'02',1,0),(124,'03',1,0),(125,'04',1,0),(126,'05',1,0),(127,'06',1,0),(128,'07',1,0),(129,'08',1,0),(130,'09',1,0),(131,'10',1,1),(132,'11',1,0),(133,'12',1,0),(134,'13',1,0),(135,'14',1,0),(136,'15',1,0),(137,'16',1,1),(138,'17',1,0),(139,'18',1,0),(140,'19',1,0),(141,'20',1,0),(142,'21',1,0),(143,'22',1,0),(144,'23',1,0),(145,'24',1,0),(146,'25',1,0),(147,'26',1,0),(148,'27',1,0),(149,'28',1,0),(150,'29',1,0),(151,'30',1,0),(152,'31',1,0),(153,'32',1,0),(154,'33',1,0),(155,'34',1,0),(156,'35',1,0),(157,'36',1,0),(158,'37',1,0),(159,'38',1,0),(160,'39',1,0),(161,'40',1,0),(162,'01',2,1),(163,'02',2,0),(164,'03',2,0),(165,'04',2,1),(166,'05',2,1),(167,'06',2,0),(168,'07',2,0),(169,'08',2,0),(170,'09',2,0),(171,'10',2,0),(172,'11',2,0),(173,'12',2,0),(174,'13',2,0),(175,'14',2,0),(176,'15',2,0),(177,'16',2,0),(178,'17',2,0),(179,'18',2,0),(180,'19',2,0),(181,'20',2,0),(182,'21',2,0),(183,'22',2,0),(184,'23',2,0),(185,'24',2,1),(186,'25',2,0),(187,'26',2,0),(188,'27',2,0),(189,'28',2,0),(190,'29',2,0),(191,'30',2,0),(192,'31',2,0),(193,'32',2,0),(194,'33',2,0),(195,'34',2,0),(196,'35',2,0),(197,'36',2,0),(198,'37',2,0),(199,'38',2,0),(200,'39',2,0),(201,'40',2,0),(202,'01',3,0),(203,'02',3,1),(204,'03',3,0),(205,'04',3,0),(206,'05',3,0),(207,'06',3,0),(208,'07',3,0),(209,'08',3,0),(210,'09',3,0),(211,'10',3,0),(212,'11',3,0),(213,'12',3,0),(214,'13',3,0),(215,'14',3,0),(216,'15',3,0),(217,'16',3,0),(218,'17',3,0),(219,'18',3,0),(220,'19',3,0),(221,'20',3,0),(222,'21',3,0),(223,'22',3,0),(224,'23',3,0),(225,'24',3,0),(226,'25',3,0),(227,'26',3,0),(228,'27',3,0),(229,'28',3,0),(230,'29',3,0),(231,'30',3,0),(232,'31',3,0),(233,'32',3,0),(234,'33',3,0),(235,'34',3,0),(236,'35',3,0),(237,'36',3,0),(238,'37',3,0),(239,'38',3,0),(240,'39',3,0),(241,'40',3,0),(242,'01',4,0),(243,'02',4,0),(244,'03',4,0),(245,'04',4,0),(246,'05',4,0),(247,'06',4,0),(248,'07',4,0),(249,'08',4,0),(250,'09',4,0),(251,'10',4,0),(252,'11',4,0),(253,'12',4,0),(254,'13',4,0),(255,'14',4,0),(256,'15',4,0),(257,'16',4,0),(258,'17',4,0),(259,'18',4,0),(260,'19',4,0),(261,'20',4,0),(262,'21',4,0),(263,'22',4,0),(264,'23',4,0),(265,'24',4,0),(266,'25',4,0),(267,'26',4,0),(268,'27',4,0),(269,'28',4,0),(270,'29',4,0),(271,'30',4,0),(272,'31',4,0),(273,'32',4,0),(274,'33',4,0),(275,'34',4,0),(276,'35',4,0),(277,'36',4,0),(278,'37',4,0),(279,'38',4,0),(280,'39',4,0),(281,'40',4,0),(282,'01',5,0),(283,'02',5,0),(284,'03',5,0),(285,'04',5,0),(286,'05',5,0),(287,'06',5,0),(288,'07',5,0),(289,'08',5,0),(290,'09',5,0),(291,'10',5,0),(292,'11',5,0),(293,'12',5,0),(294,'13',5,0),(295,'14',5,0),(296,'15',5,0),(297,'16',5,0),(298,'17',5,0),(299,'18',5,0),(300,'19',5,0),(301,'20',5,0),(302,'21',5,0),(303,'22',5,0),(304,'23',5,0),(305,'24',5,0),(306,'25',5,0),(307,'26',5,0),(308,'27',5,0),(309,'28',5,0),(310,'29',5,0),(311,'30',5,0),(312,'31',5,0),(313,'32',5,0),(314,'33',5,0),(315,'34',5,0),(316,'35',5,0),(317,'36',5,0),(318,'37',5,0),(319,'38',5,0),(320,'39',5,0),(321,'40',5,0),(322,'01',6,0),(323,'02',6,0),(324,'03',6,0),(325,'04',6,0),(326,'05',6,0),(327,'06',6,0),(328,'07',6,0),(329,'08',6,0),(330,'09',6,0),(331,'10',6,0),(332,'11',6,0),(333,'12',6,0),(334,'13',6,0),(335,'14',6,0),(336,'15',6,0),(337,'16',6,0),(338,'17',6,0),(339,'18',6,0),(340,'19',6,0),(341,'20',6,0),(342,'21',6,0),(343,'22',6,0),(344,'23',6,0),(345,'24',6,0),(346,'25',6,0),(347,'26',6,0),(348,'27',6,0),(349,'28',6,0),(350,'29',6,0),(351,'30',6,0),(352,'31',6,0),(353,'32',6,0),(354,'33',6,0),(355,'34',6,0),(356,'35',6,0),(357,'36',6,0),(358,'37',6,0),(359,'38',6,0),(360,'39',6,0),(361,'40',6,0),(362,'01',7,0),(363,'02',7,0),(364,'03',7,0),(365,'04',7,0),(366,'05',7,0),(367,'06',7,0),(368,'07',7,0),(369,'08',7,0),(370,'09',7,0),(371,'10',7,0),(372,'11',7,0),(373,'12',7,0),(374,'13',7,0),(375,'14',7,0),(376,'15',7,0),(377,'16',7,0),(378,'17',7,0),(379,'18',7,0),(380,'19',7,0),(381,'20',7,0),(382,'21',7,0),(383,'22',7,0),(384,'23',7,0),(385,'24',7,0),(386,'25',7,0),(387,'26',7,0),(388,'27',7,0),(389,'28',7,0),(390,'29',7,0),(391,'30',7,0),(392,'31',7,0),(393,'32',7,0),(394,'33',7,0),(395,'34',7,0),(396,'35',7,0),(397,'36',7,0),(398,'37',7,0),(399,'38',7,0),(400,'39',7,0),(401,'40',7,0),(402,'01',8,0),(403,'02',8,1),(404,'03',8,0),(405,'04',8,0),(406,'05',8,0),(407,'06',8,0),(408,'07',8,0),(409,'08',8,0),(410,'09',8,0),(411,'10',8,0),(412,'11',8,0),(413,'12',8,0),(414,'13',8,0),(415,'14',8,0),(416,'15',8,0),(417,'16',8,0),(418,'17',8,0),(419,'18',8,0),(420,'19',8,0),(421,'20',8,0),(422,'21',8,0),(423,'22',8,0),(424,'23',8,0),(425,'24',8,0),(426,'25',8,0),(427,'26',8,0),(428,'27',8,0),(429,'28',8,0),(430,'29',8,0),(431,'30',8,0),(432,'31',8,0),(433,'32',8,0),(434,'33',8,0),(435,'34',8,0),(436,'35',8,0),(437,'36',8,0),(438,'37',8,0),(439,'38',8,0),(440,'39',8,0),(441,'40',8,0),(442,'01',9,0),(443,'02',9,0),(444,'03',9,0),(445,'04',9,0),(446,'05',9,0),(447,'06',9,0),(448,'07',9,0),(449,'08',9,0),(450,'09',9,0),(451,'10',9,0),(452,'11',9,0),(453,'12',9,0),(454,'13',9,0),(455,'14',9,0),(456,'15',9,0),(457,'16',9,0),(458,'17',9,0),(459,'18',9,0),(460,'19',9,0),(461,'20',9,0),(462,'21',9,0),(463,'22',9,0),(464,'23',9,0),(465,'24',9,0),(466,'25',9,0),(467,'26',9,0),(468,'27',9,0),(469,'28',9,0),(470,'29',9,0),(471,'30',9,0),(472,'31',9,0),(473,'32',9,0),(474,'33',9,0),(475,'34',9,0),(476,'35',9,0),(477,'36',9,0),(478,'37',9,0),(479,'38',9,0),(480,'39',9,0),(481,'40',9,0),(482,'01',10,0),(483,'02',10,0),(484,'03',10,0),(485,'04',10,0),(486,'05',10,0),(487,'06',10,0),(488,'07',10,0),(489,'08',10,0),(490,'09',10,0),(491,'10',10,0),(492,'11',10,0),(493,'12',10,0),(494,'13',10,0),(495,'14',10,0),(496,'15',10,0),(497,'16',10,0),(498,'17',10,0),(499,'18',10,0),(500,'19',10,0),(501,'20',10,0),(502,'21',10,0),(503,'22',10,0),(504,'23',10,0),(505,'24',10,0),(506,'25',10,0),(507,'26',10,0),(508,'27',10,0),(509,'28',10,0),(510,'29',10,0),(511,'30',10,0),(512,'31',10,0),(513,'32',10,0),(514,'33',10,0),(515,'34',10,0),(516,'35',10,0),(517,'36',10,0),(518,'37',10,0),(519,'38',10,0),(520,'39',10,0),(521,'40',10,0);
/*!40000 ALTER TABLE seats ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS user;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  id int NOT NULL AUTO_INCREMENT,
  username varchar(45) NOT NULL,
  email varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL,
  contactNum varchar(10) NOT NULL,
  isAdmin tinyint(1) NOT NULL DEFAULT '0',
  image varchar(255) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email_UNIQUE (email)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES user WRITE;
/*!40000 ALTER TABLE user DISABLE KEYS */;
INSERT INTO user VALUES (2,'kd.brendon','brendon5860@gmail.com','$2y$10$dnvE9SjcUs3WL1ZsCYTBNeygLCCQgxV7cpU3N8lecYex4mcxHACv2','1990',0,NULL),(4,'admin','admin@gmail.com','$10$.6o3l7iFdOClyEw1WScyfeSLEw/nNXn3qHE5RbcpkMeaF5ODBZQSa','12',1,NULL),(6,'candleb','hello@gmail.com','$2y$10$VD/LeQ39.c9MNoSSTxyJf.GfoDNMw51pSiMzOPYyZBGGvkOCEgAyO','1234',0,NULL),(8,'john','john1@gmail.com','$2y$10$QjCc1DkJNBqYgWa1kE9RE..oAuR4r3bcogeSQLUcS04jn1J4BhP/i','123',0,NULL),(9,'dinethma DX','din@gmail.com','$2y$10$Gwubo7.Udhmt1yQzf/BeOuiBBxyRjKTWwJmeVM//n/aJIqKV388RO','0765366990',0,NULL),(10,'admin1','admin1@gmail.com','$2y$10$gtpgiff/yTvPF03pEheo4uCu9gLCCZL2yTb8/UVHm2hlFD/ZUgJEu','1234567890',1,NULL),(11,'pawan','paw1@gmail.com','$2y$10$.S/1yRtj83tPHYM1dI6t7uiKZBGBgz0fY/oYSuS/iDlr3R1zh50Ci','123',1,NULL),(13,'sanchi','sanchi@gmail.com','$2y$10$scFApmxS.pZiRG7tAoDnduKgdR3g8YeMmHYv.PQRIclqHtCgnIwiu','123456789',0,NULL),(16,'kd brendon','brendondon2003@gmail.com','$2y$10$QWJSvvziefCFZgBXH6RhdO5CUNeI7UaPfQgHezgsbahE52CfwE97K','0743013073',0,'profile_6811b299a43a5.jpg'),(17,'kdbrendon1','as12@gmail.com','$2y$10$aXqPxFSdVG9VYyMqM9S69e98Nf/dIeHISE6DNR1IiEKnbL7oH9wze','0743013073',0,NULL),(18,'dinethma','dinethma@gmail.com','$2y$10$Zv9wkxAFSzaRA18LQ9LzHuolbyyhv14zIOLPqBBRKuhNRPyASsEsi','0743013073',0,NULL),(22,'admin2','admin2@gmail.com','$2y$10$wNwcFAnsWVIJuF.t0mtAqeINzNqKwck0GhplpKe/rbEdKnNeLjO5q','1331',1,NULL);
/*!40000 ALTER TABLE user ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'uniroot'
--

--
-- Dumping routines for database 'uniroot'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-05  2:25:07
