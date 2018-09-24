-- MySQL dump 10.13  Distrib 5.7.17, for macos10.12 (x86_64)
--
-- Host: frances.li    Database: uts_doctor
-- ------------------------------------------------------
-- Server version	5.5.61

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Application`
--

DROP TABLE IF EXISTS `Application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Application` (
  `applicationId` int(11) NOT NULL AUTO_INCREMENT,
  `gp` varchar(45) DEFAULT NULL,
  `gpAddress` varchar(245) DEFAULT NULL,
  `referal` varchar(245) DEFAULT NULL,
  `notes` text,
  `documents` text,
  `appointmentType` varchar(45) DEFAULT NULL,
  `preferedDays` varchar(45) DEFAULT NULL,
  `preferedTime` varchar(45) DEFAULT NULL,
  `appointmentTopics` varchar(45) DEFAULT NULL,
  `specialRequests` text,
  `patientId` int(11) NOT NULL,
  `doctorId` int(11) NOT NULL,
  `status` varchar(45) DEFAULT NULL,
  `reason` text,
  `submitted` date DEFAULT NULL,
  PRIMARY KEY (`applicationId`),
  KEY `fk_Application_User_idx` (`patientId`),
  KEY `fk_Application_User1_idx` (`doctorId`),
  CONSTRAINT `fk_Application_User` FOREIGN KEY (`patientId`) REFERENCES `User` (`userId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Application_User1` FOREIGN KEY (`doctorId`) REFERENCES `User` (`userId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Application`
--

LOCK TABLES `Application` WRITE;
/*!40000 ALTER TABLE `Application` DISABLE KEYS */;
INSERT INTO `Application` VALUES (7,'asdf','asdf','asdf','asdf','asdf','Initial','Monday','9:00','3+','zxcvzxcv',9,14,'Pending',NULL,NULL),(8,'','','','','','Initial','asdf','asdf','3+','',9,12,'Draft',NULL,NULL),(9,'asdf','asdf','','','','Initial','asdf','asdf','3+','',9,12,'Rejected','asdfa','2018-09-24'),(11,'sdf','asdf','','asdf','','Initial','asdf','asdf','3+','asdf',9,12,'Approved',NULL,'2018-09-24');
/*!40000 ALTER TABLE `Application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Appointment`
--

DROP TABLE IF EXISTS `Appointment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Appointment` (
  `appointmentId` int(11) NOT NULL AUTO_INCREMENT,
  `applicationId` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `startTime` time DEFAULT NULL,
  `endTime` time DEFAULT NULL,
  `secretaryId` int(11) NOT NULL,
  `status` varchar(45) DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `doctorId` int(11) DEFAULT NULL,
  `patientId` int(11) DEFAULT NULL,
  PRIMARY KEY (`appointmentId`),
  KEY `fk_Appointment_Application1_idx` (`applicationId`),
  KEY `fk_Appointment_User1_idx` (`secretaryId`),
  KEY `fk_1_idx` (`doctorId`),
  KEY `fk_2_idx` (`patientId`),
  CONSTRAINT `fk_1` FOREIGN KEY (`doctorId`) REFERENCES `User` (`userId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_2` FOREIGN KEY (`patientId`) REFERENCES `User` (`userId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Appointment_Application1` FOREIGN KEY (`applicationId`) REFERENCES `Application` (`applicationId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Appointment_User1` FOREIGN KEY (`secretaryId`) REFERENCES `User` (`userId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Appointment`
--

LOCK TABLES `Appointment` WRITE;
/*!40000 ALTER TABLE `Appointment` DISABLE KEYS */;
INSERT INTO `Appointment` VALUES (1,11,'2018-09-25','08:00:00','13:00:00',17,'Cancelled',NULL,NULL,12,9);
/*!40000 ALTER TABLE `Appointment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Schedule`
--

DROP TABLE IF EXISTS `Schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Schedule` (
  `scheduleId` int(11) NOT NULL AUTO_INCREMENT,
  `doctorId` int(11) NOT NULL,
  `day` date DEFAULT NULL,
  `startTime` time DEFAULT NULL,
  `endTime` time DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  PRIMARY KEY (`scheduleId`),
  KEY `fk_Schedule_User1_idx` (`doctorId`),
  CONSTRAINT `fk_Schedule_User1` FOREIGN KEY (`doctorId`) REFERENCES `User` (`userId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Schedule`
--

LOCK TABLES `Schedule` WRITE;
/*!40000 ALTER TABLE `Schedule` DISABLE KEYS */;
/*!40000 ALTER TABLE `Schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) DEFAULT NULL,
  `firstname` varchar(45) DEFAULT NULL,
  `lastname` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `gender` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `address` varchar(145) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `specialty` text,
  `verification` text,
  `medicareNo` varchar(45) DEFAULT NULL,
  `doctorId` int(11) DEFAULT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `fk_User_User1_idx` (`doctorId`),
  CONSTRAINT `fk_User_User1` FOREIGN KEY (`doctorId`) REFERENCES `User` (`userId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES (2,'Mr','Bob','Dylan','user1@uts.com','1988-01-01',NULL,NULL,'user1',NULL,'patient',NULL,NULL,NULL,NULL),(9,'Mr.','Lee','Li','hi.xinyuli@gmail.com','2018-12-31','1234512345','Male','Unsw1234','asdf','patient',NULL,NULL,'123',NULL),(10,'Mr.','asdf','asdf','hi.xinyuli+1@gmail.com','2018-09-13',NULL,'Male','Unsw1234',NULL,'patient',NULL,NULL,'123123',NULL),(11,'Mr.','admin','staff','admin@uts.com','2018-09-13',NULL,'Male','admin',NULL,'admin',NULL,NULL,NULL,NULL),(12,'Dr.','Martin','Ball','doctor1@uts.com','2000-05-01','12341234','Female','doctor1','asdf','doctor','asdfsadfasdfasdfasdfasdf','asdfasdfasdfasdfasdf',NULL,NULL),(14,'Dr.','Juliana','Ball','doctor2@uts.com','1963-05-01','1231231231','Male','doctor2','asdf','doctor','asdf','asdf',NULL,NULL),(15,'Dr.','Another','Doctor','doctor3@uts.com','2018-12-31','0123412asdf','Female',NULL,'haha','doctor','jijiasdf','hihiasdf',NULL,NULL),(17,'Miss.','Tom','Tom','sec1@uts.com','2018-01-01','1234512345','Female','sec1','haha','secretary',NULL,NULL,NULL,12);
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-24 19:12:47
