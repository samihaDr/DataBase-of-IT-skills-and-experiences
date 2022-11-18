DROP DATABASE IF EXISTS `prwb_2122_G01`;
CREATE DATABASE IF NOT EXISTS `prwb_2122_G01` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `prwb_2122_G01`;

CREATE TABLE `User` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Mail` varchar(128) NOT NULL,
  `FullName` varchar(128) NOT NULL,
  `Title` varchar(256) NOT NULL,
  `Password` varchar(256) NOT NULL,
  `RegisteredAt` datetime NOT NULL DEFAULT current_timestamp(),
  `Birthdate` date NOT NULL,
  `Role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  PRIMARY KEY(`ID`),
  UNIQUE(`Mail`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `Skill` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) NOT NULL,
  PRIMARY KEY(`ID`),
  UNIQUE(`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `Mastering` (
  `User` int(11) NOT NULL,
  `Skill` int(11) NOT NULL,
  `Level` int(1) NOT NULL,
  PRIMARY KEY(`User`, `Skill`),
  FOREIGN KEY(`User`) REFERENCES `User`(`ID`),
  FOREIGN KEY(`Skill`) REFERENCES `Skill`(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Place` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) NOT NULL,
  `City` varchar(128) NOT NULL,
  PRIMARY KEY(`ID`),
  UNIQUE(`Name`, `City`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `Experience` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Start` date NOT NULL,
  `Stop` date NULL,
  `Title` varchar(128) NOT NULL,
  `Description` text NULL,
  `User` int(11) NOT NULL,
  `Place` int(11) NOT NULL,
  PRIMARY KEY(`ID`),
  FOREIGN KEY(`User`) REFERENCES `User`(`ID`),
  FOREIGN KEY(`Place`) REFERENCES `Place`(`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `Using` (
  `Experience` int(11) NOT NULL,
  `Skill` int(11) NOT NULL,
  PRIMARY KEY(`Experience`, `Skill`),
  FOREIGN KEY(`Experience`) REFERENCES `Experience`(`ID`),
  FOREIGN KEY(`Skill`) REFERENCES `Skill`(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;