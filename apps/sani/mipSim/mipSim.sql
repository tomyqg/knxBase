# noinspection SqlNoDataSourceInspectionForFile

-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               5.1.46-community - MySQL Community Server (GPL)
-- Server Betriebssystem:        Win32
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Exportiere Datenbank Struktur für mipsim
CREATE DATABASE IF NOT EXISTS `mipsim` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci */;
USE `mipsim`;

-- Exportiere Struktur von Tabelle mipsim.anhang
CREATE TABLE IF NOT EXISTS `anhang` (
  `Id` int(11) DEFAULT NULL,
  `ERPNrKostenvoranschlag` int(11) DEFAULT NULL,
  `LfdNr` int(11) DEFAULT NULL,
  `name` varchar(128) COLLATE latin1_general_ci DEFAULT NULL,
  `datei` varchar(64) COLLATE latin1_general_ci DEFAULT NULL,
  `typ` varchar(64) COLLATE latin1_general_ci DEFAULT NULL,
  `art` int(8) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Exportiere Daten aus Tabelle mipsim.anhang: 0 rows
/*!40000 ALTER TABLE `anhang` DISABLE KEYS */;
/*!40000 ALTER TABLE `anhang` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle mipsim.kostenvoranschlag
CREATE TABLE IF NOT EXISTS `kostenvoranschlag` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ERPNr` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `intKvID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Exportiere Daten aus Tabelle mipsim.kostenvoranschlag: 1 rows
/*!40000 ALTER TABLE `kostenvoranschlag` DISABLE KEYS */;
INSERT INTO `kostenvoranschlag` (`Id`, `ERPNr`, `intKvID`) VALUES
	(31, '001000000001', 999901);
/*!40000 ALTER TABLE `kostenvoranschlag` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle mipsim.position
CREATE TABLE IF NOT EXISTS `position` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ERPNrKostenvoranschlag` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `LfdNr` int(5) DEFAULT NULL,
  `intPosID` int(11) DEFAULT NULL,
  `hmvz` varchar(16) COLLATE latin1_general_ci DEFAULT NULL,
  `versorgungsbeginn` int(11) DEFAULT NULL,
  `versorgungsende` int(11) DEFAULT NULL,
  `hmkz` int(11) DEFAULT NULL,
  `produktbesonderheit` int(11) DEFAULT NULL,
  `beratungsdatum` int(11) DEFAULT NULL,
  `preis` int(11) DEFAULT NULL,
  `mwstkz` int(11) DEFAULT NULL,
  `beschreibung` int(11) DEFAULT NULL,
  `menge` float(8,3) DEFAULT NULL,
  `rabatt` int(11) DEFAULT NULL,
  `vertragsnummer` int(11) DEFAULT NULL,
  `versicherungsart` int(11) DEFAULT NULL,
  `posZuzID` int(11) DEFAULT NULL,
  `posEigID` int(11) DEFAULT NULL,
  `betragEigenanteil` float(8,2) DEFAULT NULL,
  `betragZuzahlung` float(8,2) DEFAULT NULL,
  `kzZuzahlung` float(8,2) DEFAULT NULL,
  `merkmale` int(11) DEFAULT NULL,
  `zusatzanschiften` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Exportiere Daten aus Tabelle mipsim.position: 0 rows
/*!40000 ALTER TABLE `position` DISABLE KEYS */;
/*!40000 ALTER TABLE `position` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle mipsim.versicherter
CREATE TABLE IF NOT EXISTS `versicherter` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ERPNrKostenvoranschlag` int(32) DEFAULT NULL,
  `kvnummer` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `kvanrede` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `kvname` varchar(64) COLLATE latin1_general_ci DEFAULT NULL,
  `kvvorname` varchar(64) COLLATE latin1_general_ci DEFAULT NULL,
  `kvstrasse` varchar(64) COLLATE latin1_general_ci DEFAULT NULL,
  `kvplz` varchar(16) COLLATE latin1_general_ci DEFAULT NULL,
  `kvort` varchar(64) COLLATE latin1_general_ci DEFAULT NULL,
  `kvgeburtsdatum` date DEFAULT NULL,
  `kvtelefon` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `kvtelefax` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `kvmobil` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `kvemal` varchar(64) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Exportiere Daten aus Tabelle mipsim.versicherter: 2 rows
/*!40000 ALTER TABLE `versicherter` DISABLE KEYS */;
INSERT INTO `versicherter` (`Id`, `ERPNrKostenvoranschlag`, `kvnummer`, `kvanrede`, `kvname`, `kvvorname`, `kvstrasse`, `kvplz`, `kvort`, `kvgeburtsdatum`, `kvtelefon`, `kvtelefax`, `kvmobil`, `kvemal`) VALUES
	(1, 1000000001, '0', '0', '0', '0', '0', '51580', '0', '1963-02-15', '0', '0', '0', '0'),
	(2, 1000000001, '0', '0', '0', '0', '0', '51580', '0', '1963-02-15', '0', '0', '0', '0'),
	(3, 1000000001, 'VERSNR4712', 'Herr', 'Mit 300000/-', 'KHW, 101575519/1x/-', 'Im Löhbusch 13b', '51580', 'Reichshof', '1963-02-15', '', '', '', ''),
	(4, 1000000001, 'VERSNR4712', 'Herr', 'Mit 300000/-', 'KHW, 101575519/1x/-', 'Im Löhbusch 13b', '51580', 'Reichshof', '1963-02-15', '', '', '', '');
/*!40000 ALTER TABLE `versicherter` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle mipsim.versorgung
CREATE TABLE IF NOT EXISTS `versorgung` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ERPNrKostenvoranschlag` varchar(32) COLLATE latin1_general_ci DEFAULT '0' COMMENT 'Diese Nummer ist eindeutug für jeden empfangen KV',
  `kostentraegerID` int(9) DEFAULT NULL,
  `kostentraegerIK` int(9) DEFAULT NULL,
  `versorgungsart` int(11) DEFAULT NULL,
  `verordnungsarzt` int(9) DEFAULT NULL,
  `bstnummer` int(11) DEFAULT NULL,
  `verordnungsdatum` date DEFAULT NULL,
  `opdatum` date DEFAULT NULL,
  `unfalldatum` date DEFAULT NULL,
  `unfallkz` int(11) DEFAULT NULL,
  `bvg` int(11) DEFAULT NULL,
  `versichertendatenIdRef` varchar(32) COLLATE latin1_general_ci DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Exportiere Daten aus Tabelle mipsim.versorgung: 3 rows
/*!40000 ALTER TABLE `versorgung` DISABLE KEYS */;
INSERT INTO `versorgung` (`Id`, `ERPNrKostenvoranschlag`, `kostentraegerID`, `kostentraegerIK`, `versorgungsart`, `verordnungsarzt`, `bstnummer`, `verordnungsdatum`, `opdatum`, `unfalldatum`, `unfallkz`, `bvg`, `versichertendatenIdRef`) VALUES
	(6, '001000000001', 999, 109999999, 1, 4712, 100300001, '2017-12-04', '0000-00-00', '0000-00-00', 0, 0, ''),
	(5, '001000000001', 999, 109999999, 1, 4712, 100300001, '2017-12-04', '0000-00-00', '0000-00-00', 0, 0, ''),
	(7, '001000000001', 999, 109999999, 1, 4712, 100300001, '2017-12-04', '0000-00-00', '0000-00-00', 0, 0, ''),
	(8, '001000000001', 999, 109999999, 1, 4712, 100300001, '2017-12-04', '0000-00-00', '0000-00-00', 0, 0, '');
/*!40000 ALTER TABLE `versorgung` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle mipsim.zusatzanschrift
CREATE TABLE IF NOT EXISTS `zusatzanschrift` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ERPNrKostenvoranschlag` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `LfdNr` int(5) DEFAULT NULL,
  `typ` int(11) DEFAULT NULL,
  `name` int(11) DEFAULT NULL,
  `vorname` int(11) DEFAULT NULL,
  `strasse` int(11) DEFAULT NULL,
  `plz` int(11) DEFAULT NULL,
  `ort` int(11) DEFAULT NULL,
  `anrede` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Exportiere Daten aus Tabelle mipsim.zusatzanschrift: 0 rows
/*!40000 ALTER TABLE `zusatzanschrift` DISABLE KEYS */;
/*!40000 ALTER TABLE `zusatzanschrift` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
