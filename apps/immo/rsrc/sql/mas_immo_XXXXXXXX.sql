-- phpMyAdmin SQL Dump
-- version 4.4.15.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 16, 2016 at 08:59 PM
-- Server version: 5.6.21
-- PHP Version: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET @clientId = '1a2b3c4d' ;
SET @dbName = CONCAT( "mas_immo_", @clientId) ;
-- SELECT @dbName ;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mas_immo_1a2b3c4d`
--
CREATE DATABASE IF NOT EXISTS `mas_immo_1a2b3c4d` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `mas_immo_1a2b3c4d`;

-- --------------------------------------------------------

--
-- Table structure for table `Property`
--

DROP TABLE IF EXISTS `Property`;
CREATE TABLE IF NOT EXISTS `Property` (
  `Id` int(8) NOT NULL,
  `PropertyNo` varchar(32) NOT NULL,
  `Description1` varchar(40) NOT NULL,
  `Description2` varchar(40) NOT NULL,
  `Description3` varchar(40) NOT NULL,
  `ZIP` varchar(8) NOT NULL,
  `City` varchar(32) NOT NULL,
  `Street` varchar(32) NOT NULL,
  `Number` varchar(6) NOT NULL,
  `Country` varchar(32) NOT NULL,
  `Remark` mediumtext NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Property`
--

INSERT INTO `Property` (`Id`, `PropertyNo`, `Description1`, `Description2`, `Description3`, `ZIP`, `City`, `Street`, `Number`, `Country`, `Remark`) VALUES
(1, '10000001', 'Einfamilienhaus', '', '', '51580', 'Reichshof', 'Im Löhbusch', '13b', 'de', '20160826/225736: miskhwe: Property updated\n'),
(2, '10000002', 'Einfamilienhaus', 'mit Einliegerwohnung', '', '51580', 'Reichshof', 'Im Löhbusch', '13c', 'de', '20160826/225901: miskhwe: Property updated\n'),
(3, '10000003', 'Mehrfamilienhaus', '6 WE   2 ELW', '', '51580', 'Reichshof', 'Im Löhbusch', '19', 'de', '');

-- --------------------------------------------------------

--
-- Table structure for table `Proprietor`
--

DROP TABLE IF EXISTS `Proprietor`;
CREATE TABLE IF NOT EXISTS `Proprietor` (
  `Id` int(8) NOT NULL,
  `ProprietorNo` varchar(32) NOT NULL DEFAULT '',
  `AddressType` varchar(1) NOT NULL DEFAULT '',
  `AddressNo` varchar(5) NOT NULL DEFAULT '',
  `ProprietorName1` varchar(64) NOT NULL,
  `ProprietorName2` varchar(64) NOT NULL,
  `ProprietorName3` varchar(64) NOT NULL,
  `ZIP` varchar(16) NOT NULL,
  `City` varchar(32) NOT NULL,
  `Street` varchar(32) NOT NULL,
  `Number` varchar(6) NOT NULL,
  `Country` varchar(32) NOT NULL DEFAULT 'de',
  `Language` varchar(10) NOT NULL DEFAULT 'de_de',
  `Phone` varchar(32) NOT NULL,
  `Fax` varchar(32) NOT NULL,
  `Cellphone` varchar(32) NOT NULL,
  `URL` varchar(64) NOT NULL,
  `eMail` varchar(64) NOT NULL,
  `TaxId` varchar(32) NOT NULL,
  `MandNr` varchar(16) NOT NULL,
  `UserName` varchar(16) NOT NULL,
  `Password` varchar(128) NOT NULL,
  `Currency` varchar(4) NOT NULL DEFAULT 'EUR',
  `Tax` int(1) NOT NULL DEFAULT '1',
  `ProprietorType` varchar(8) NOT NULL DEFAULT '0' COMMENT 'Proprietor type, 0=consumer, 1=business',
  `Organization` varchar(48) NOT NULL DEFAULT 'Sonstige',
  `Remark` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Proprietor`
--

INSERT INTO `Proprietor` (`Id`, `ProprietorNo`, `AddressType`, `AddressNo`, `ProprietorName1`, `ProprietorName2`, `ProprietorName3`, `ZIP`, `City`, `Street`, `Number`, `Country`, `Language`, `Phone`, `Fax`, `Cellphone`, `URL`, `eMail`, `TaxId`, `MandNr`, `UserName`, `Password`, `Currency`, `Tax`, `ProprietorType`, `Organization`, `Remark`) VALUES
(1, '00000001', '', '', 'Karl-Heinz', 'Welter', '', '51580', 'Reichshof', 'Im Löhbusch', '13b', 'de', 'de_DE', '02296 999', '', '0171 4494070', '', 'khwelter@icloud.com', 'sdfsdfds', '', '', '', 'EUR', 1, '0', '_none', '20160906/211616: miskhwe: Proprietor updated\n20160826/222306: miskhwe: Proprietor updated\n20160826/222231: miskhwe: Proprietor updated\n20160826/222210: miskhwe: Proprietor updated\n'),
(2, '00000002', '', '', 'sdfsdfdfsd', '', '', '', '', '', '', 'de', 'de_DE', '', '', '', '', '', '', '', '', '', 'EUR', 1, '', '_none', '');

-- --------------------------------------------------------

--
-- Table structure for table `ProprietorContact`
--

DROP TABLE IF EXISTS `ProprietorContact`;
CREATE TABLE IF NOT EXISTS `ProprietorContact` (
  `Id` int(8) NOT NULL,
  `ProprietorNo` varchar(32) NOT NULL,
  `ProprietorContactNo` varchar(32) NOT NULL,
  `Function` varchar(12) NOT NULL,
  `Address` varchar(32) NOT NULL,
  `Salutation` varchar(8) NOT NULL,
  `Title` varchar(16) NOT NULL,
  `FirstName` varchar(32) NOT NULL,
  `LastName` varchar(32) NOT NULL,
  `Phone` varchar(32) NOT NULL,
  `Fax` varchar(32) NOT NULL,
  `Cellphone` varchar(32) NOT NULL,
  `eMail` varchar(64) NOT NULL,
  `UserName` varchar(16) NOT NULL,
  `Password` varchar(128) NOT NULL,
  `ActivationKey` varchar(100) NOT NULL,
  `Remark` varchar(250) NOT NULL,
  `Mailing` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Rentable`
--

DROP TABLE IF EXISTS `Rentable`;
CREATE TABLE IF NOT EXISTS `Rentable` (
  `Id` int(8) NOT NULL,
  `RentableNo` varchar(32) NOT NULL,
  `Desription1` varchar(40) NOT NULL,
  `Desription2` varchar(40) NOT NULL,
  `Desription3` varchar(40) NOT NULL,
  `ZIP` varchar(8) NOT NULL,
  `City` varchar(32) NOT NULL,
  `Street` varchar(32) NOT NULL,
  `Number` varchar(6) NOT NULL,
  `Country` varchar(32) NOT NULL,
  `Remark` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_PropertySurvey`
--
DROP VIEW IF EXISTS `v_PropertySurvey`;
CREATE TABLE IF NOT EXISTS `v_PropertySurvey` (
`Id` int(8)
,`PropertyNo` varchar(32)
,`PropertyName` varchar(81)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_ProprietorContactSurvey`
--
DROP VIEW IF EXISTS `v_ProprietorContactSurvey`;
CREATE TABLE IF NOT EXISTS `v_ProprietorContactSurvey` (
`Id` int(8)
,`ProprietorNo` varchar(32)
,`ProprietorContactNo` varchar(32)
,`FirstName` varchar(32)
,`LastName` varchar(32)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_ProprietorProprietorContactSurvey`
--
DROP VIEW IF EXISTS `v_ProprietorProprietorContactSurvey`;
CREATE TABLE IF NOT EXISTS `v_ProprietorProprietorContactSurvey` (
`Id` int(8)
,`ProprietorNo` varchar(32)
,`ProprietorContactNo` varchar(32)
,`ProprietorName` varchar(129)
,`Name` varchar(65)
);

-- --------------------------------------------------------

--
-- Structure for view `v_PropertySurvey`
--
DROP TABLE IF EXISTS `v_PropertySurvey`;

CREATE VIEW `v_PropertySurvey` AS select `P`.`Id` AS `Id`,`P`.`PropertyNo` AS `PropertyNo`,concat(`P`.`Description1`,' ',`P`.`Description2`) AS `PropertyName` from `Property` `P` order by `P`.`PropertyNo`;

-- --------------------------------------------------------

--
-- Structure for view `v_ProprietorContactSurvey`
--
DROP TABLE IF EXISTS `v_ProprietorContactSurvey`;

CREATE VIEW `v_ProprietorContactSurvey` AS select `CC`.`Id` AS `Id`,`CC`.`ProprietorNo` AS `ProprietorNo`,`CC`.`ProprietorContactNo` AS `ProprietorContactNo`,`CC`.`FirstName` AS `FirstName`,`CC`.`LastName` AS `LastName` from `ProprietorContact` `CC` order by `CC`.`ProprietorContactNo`;

-- --------------------------------------------------------

--
-- Structure for view `v_ProprietorProprietorContactSurvey`
--
DROP TABLE IF EXISTS `v_ProprietorProprietorContactSurvey`;

CREATE VIEW `v_ProprietorProprietorContactSurvey` AS select `S`.`Id` AS `Id`,`S`.`ProprietorNo` AS `ProprietorNo`,`SC`.`ProprietorContactNo` AS `ProprietorContactNo`,concat(`S`.`ProprietorName1`,' ',`S`.`ProprietorName2`) AS `ProprietorName`,concat(`SC`.`FirstName`,' ',`SC`.`LastName`) AS `Name` from (`Proprietor` `S` left join `ProprietorContact` `SC` on((`SC`.`ProprietorNo` = `S`.`ProprietorNo`))) order by `S`.`ProprietorNo`,`SC`.`ProprietorContactNo`;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Property`
--
ALTER TABLE `Property`
  ADD UNIQUE KEY `Id` (`Id`);

--
-- Indexes for table `Proprietor`
--
ALTER TABLE `Proprietor`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Id` (`Id`);

--
-- Indexes for table `Rentable`
--
ALTER TABLE `Rentable`
  ADD UNIQUE KEY `Id` (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Property`
--
ALTER TABLE `Property`
  MODIFY `Id` int(8) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `Proprietor`
--
ALTER TABLE `Proprietor`
  MODIFY `Id` int(8) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `Rentable`
--
ALTER TABLE `Rentable`
  MODIFY `Id` int(8) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
