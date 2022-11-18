-- MariaDB dump 10.19  Distrib 10.4.21-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: prwb_2122_G01
-- ------------------------------------------------------
-- Server version	10.4.21-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `experience`
--

DROP TABLE IF EXISTS `experience`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `experience` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Start` date NOT NULL,
  `Stop` date DEFAULT NULL,
  `Title` varchar(128) NOT NULL,
  `Description` text DEFAULT NULL,
  `User` int(11) NOT NULL,
  `Place` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `User` (`User`),
  KEY `Place` (`Place`),
  CONSTRAINT `experience_ibfk_1` FOREIGN KEY (`User`) REFERENCES `user` (`ID`),
  CONSTRAINT `experience_ibfk_2` FOREIGN KEY (`Place`) REFERENCES `place` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `experience`
--

LOCK TABLES `experience` WRITE;
/*!40000 ALTER TABLE `experience` DISABLE KEYS */;
INSERT INTO `experience` VALUES (1,'2009-09-01',NULL,'Teacher',NULL,1,1),(2,'2007-09-01','2011-08-31','Teaching assistant',NULL,1,2),(3,'2002-01-01','2002-09-30','System Administrator','C\'était vraiment très intéressant',1,5),(10,'2002-09-01','2006-06-30','Master en Sciences Informatiques','Faculté des Sciences',1,2),(12,'2021-10-01','2021-10-31','Projet writer','Ecriture d\'un projet pour l\'EPFC',2,1);
/*!40000 ALTER TABLE `experience` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mastering`
--

DROP TABLE IF EXISTS `mastering`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mastering` (
  `User` int(11) NOT NULL,
  `Skill` int(11) NOT NULL,
  `Level` int(1) NOT NULL,
  PRIMARY KEY (`User`,`Skill`),
  KEY `Skill` (`Skill`),
  CONSTRAINT `mastering_ibfk_1` FOREIGN KEY (`User`) REFERENCES `user` (`ID`),
  CONSTRAINT `mastering_ibfk_2` FOREIGN KEY (`Skill`) REFERENCES `skill` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mastering`
--

LOCK TABLES `mastering` WRITE;
/*!40000 ALTER TABLE `mastering` DISABLE KEYS */;
INSERT INTO `mastering` VALUES (1,1,4),(1,5,3),(1,6,5),(2,1,4),(2,4,3),(2,12,5),(2,14,3),(2,16,4);
/*!40000 ALTER TABLE `mastering` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `place`
--

DROP TABLE IF EXISTS `place`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `place` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) NOT NULL,
  `City` varchar(128) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name` (`Name`,`City`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `place`
--

LOCK TABLES `place` WRITE;
/*!40000 ALTER TABLE `place` DISABLE KEYS */;
INSERT INTO `place` VALUES (11,'Colruyt Group','Halle'),(12,'Edpnet','Saint-Nicolas'),(1,'EPFC','Bruxelles'),(10,'Google','Mons'),(7,'Police fédérale','Bruxelles'),(13,'STIB','Bruxelles'),(3,'Technofutur TIC','Gosselies'),(2,'ULB','Bruxelles'),(8,'Ville de Bruxelles','Bruxelles'),(4,'Vision IT','Wavre'),(9,'Volvo','Gand'),(5,'Worldline','Bruxelles');
/*!40000 ALTER TABLE `place` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skill`
--

DROP TABLE IF EXISTS `skill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skill` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skill`
--

LOCK TABLES `skill` WRITE;
/*!40000 ALTER TABLE `skill` DISABLE KEYS */;
INSERT INTO `skill` VALUES (15,'Autonomy'),(4,'C#'),(8,'C++'),(18,'Cobol'),(16,'Creativity'),(10,'Design Patterns'),(13,'GIT'),(1,'Java'),(3,'JavaScript'),(9,'Kotlin'),(14,'Leadership'),(11,'MVC'),(2,'PHP'),(6,'Procrastination'),(5,'SQL'),(12,'Team spirit'),(17,'WinDev');
/*!40000 ALTER TABLE `skill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Mail` varchar(128) NOT NULL,
  `FullName` varchar(128) NOT NULL,
  `Title` varchar(256) NOT NULL,
  `Password` varchar(256) NOT NULL,
  `RegisteredAt` datetime NOT NULL DEFAULT current_timestamp(),
  `Birthdate` date NOT NULL,
  `Role` enum('user','admin') DEFAULT 'user',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Mail` (`Mail`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'boverhaegen@epfc.eu','Boris Verhaegen','Computer Scientist','56ce92d1de4f05017cf03d6cd514d6d1','2021-10-13 12:04:07','1984-04-13','admin'),(2,'bepenelle@epfc.eu','Benoît Penelle','Project Inventor','56ce92d1de4f05017cf03d6cd514d6d1','2021-10-26 09:11:03','1968-05-01','user'),(3,'xapigeolet@epfc.eu','Xavier Pigeolet','System Administrator','56ce92d1de4f05017cf03d6cd514d6d1','2021-10-26 09:20:52','1995-05-26','user');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `using`
--

DROP TABLE IF EXISTS `using`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `using` (
  `Experience` int(11) NOT NULL,
  `Skill` int(11) NOT NULL,
  PRIMARY KEY (`Experience`,`Skill`),
  KEY `Skill` (`Skill`),
  CONSTRAINT `using_ibfk_1` FOREIGN KEY (`Experience`) REFERENCES `experience` (`ID`),
  CONSTRAINT `using_ibfk_2` FOREIGN KEY (`Skill`) REFERENCES `skill` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `using`
--

LOCK TABLES `using` WRITE;
/*!40000 ALTER TABLE `using` DISABLE KEYS */;
INSERT INTO `using` VALUES (1,1),(1,2),(1,3),(2,1),(2,5),(3,5),(10,1),(10,5),(10,6),(12,6);
/*!40000 ALTER TABLE `using` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-11-18 17:48:58
