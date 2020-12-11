-- phpMyAdmin SQL Dump
-- version 4.4.15.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 16, 2016 at 08:13 PM
-- Server version: 5.6.21
-- PHP Version: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET @clientId = '1a2b3c4d' ;
SET @dbName = CONCAT( "mas_immo_", @clientId, "_sys") ;
-- SELECT @dbName ;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mas_immo_1a2b3c4d_sys`
--
CREATE DATABASE IF NOT EXISTS mas_immo_1a2b3c4d_sys DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE mas_immo_1a2b3c4d_sys;
-- CREATE DATABASE IF NOT EXISTS @dbName DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
-- USE @dbName;

-- --------------------------------------------------------

--
-- Table structure for table `AppConfigObj`
--

DROP TABLE IF EXISTS `AppConfigObj`;
CREATE TABLE IF NOT EXISTS `AppConfigObj` (
  `Id` int(11) NOT NULL,
  `ApplicationSystemId` varchar(32) NOT NULL,
  `ApplicationId` varchar(32) NOT NULL,
  `ClientId` varchar(32) NOT NULL,
  `Class` varchar(32) NOT NULL DEFAULT '',
  `Section` varchar(32) NOT NULL DEFAULT '',
  `Parameter` varchar(64) NOT NULL,
  `Value` varchar(128) NOT NULL,
  `Help` varchar(64) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `AppConfigObj`
--

INSERT INTO `AppConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Section`, `Parameter`, `Value`, `Help`) VALUES
(1, '', '', '', '', '', 'CompanyName', 'wimtecc - Karl-Heinz Welter', ''),
(2, '', '', '', '', '', 'UStId', 'DE12345678', ''),
(3, '', '', '', '', '', '', 'Kreissparkasse Köln', ''),
(4, '', '', '', '', '', 'Bank1', 'asdasdasd', ''),
(5, '', '', '', '', '', 'Geschäftsführer', 'Karl-Heinz Welter', ''),
(6, '', '', '', '', '', 'Street', 'Robert-Bosch-Str. 1', ''),
(7, '', '', '', '', '', 'PLZOrt', '51674 Wiehl - Bomig', ''),
(8, '', '', '', '', '', 'ZIP-City', '51674 Wiehl - Bomig', ''),
(9, '', '', '', '', '', 'Company', 'wimtecc - Karl-Heinz Welter', ''),
(14, '', '', '', 'office-sinspert', 'ColiLblPrn', 'PrnCmd', '-o PageSize=w288h432 ', ''),
(15, '', '', '', 'office-sinspert', 'ColiLblPrn', 'PrnName', 'Sinspert_Office__ZeLP2844_1', ''),
(16, '', '', '', 'office-bomig', 'ColiLblPrn', 'PrnName', 'Bomig_Shipping__ZeLP2844_1', ''),
(17, '', '', '', 'office-bomig', 'ColiLblPrn', 'PrnCmd', '-o PageSize=w288h432 ', ''),
(18, '', '', '', 'office-sinspert', 'CuInvcPrn', 'PrnName', 'Sinspert_Office__BrHL5350DN_1', ''),
(19, '', '', '', 'office-sinspert', 'CuInvcPrn', 'PrnOpt', '-o InputSlot=Tray2', ''),
(21, '', '', '', 'office-bomig', 'CuInvcPrn', 'PrnName', 'C220_B_W', ''),
(22, '', '', '', 'office-bomig', 'CuInvcPrn', 'PrnOpt', '-o KMInputSlot=Tray2 -o PageColorOption=Black -o ColorModel=Gray', ''),
(23, '', '', '', 'office-sinspert', 'CuCommPrn', 'PrnName', 'Sinspert_Office__BrHL5350DN_1', ''),
(24, '', '', '', 'office-sinspert', 'CuCommPrn', 'PrnOpt', '-o InputSlot=Tray1', ''),
(25, '', '', '', 'office-sinspert', 'CuCommPrn', 'AutoPrint', 'yes', ''),
(26, '', '', '', 'www.wimtecc.de.local', 'shop', 'siteName', 'wimtecc.de', ''),
(27, '', '', '', 'www.wimtecc.de.local', 'main', 'siteName', 'wimtecc.de', ''),
(28, '', '', '', 'www.wimtecc.de.local', 'main', 'title', 'wimtecc - Demo Site', ''),
(29, '', '', '', 'www.wimtecc.de.local', 'main', 'urlContact', 'http://www.wimtecc.de.local/Kontakt.html', ''),
(30, '', '', '', 'www.wimtecc.de.local', 'shop', 'cookieName', 'SessionId', ''),
(31, '', '', '', 'www.wimtecc.de.local', 'shop', 'cookieTime', '0', ''),
(32, '', '', '', 'www.wimtecc.de.local', 'shop', 'cookieDomain', 'www.wimtecc.de.local', ''),
(33, '', '', '', 'www.wimtecc.de.local', 'cuCart', 'debug', 'true', ''),
(34, '', '', '', 'shop.wimtecc.de.local', 'backend', 'title', 'wimtecc ERP R3', ''),
(35, '', '', '', 'shop.wimtecc.de.local', 'backend', 'siteName', 'wimtecc', ''),
(36, '', '', '', 'shop.wimtecc.de.local', 'backend', 'fullSiteName', 'wimtecc.de', ''),
(37, '', '', '', 'shop.wimtecc.de.local', 'backend', 'url', 'erp_r3.wimtecc.de', ''),
(38, '', '', '', 'erp', 'ui', 'showLeftMenu', 'true', ''),
(39, '', '', '', 'shop.wimtecc.de.local', 'path', 'Archive', '/srv/www/vhosts/wimtecc.de.local/Archive/', ''),
(40, '', '', '', '', 'base', 'DUMMY', 'DUMMY', ''),
(41, '', '', '', 'shop.wimtecc.de.local', 'path', 'Logos', '/srv/www/vhosts/wimtecc.de/mas_r1/rsrc/logos/', ''),
(42, '', '', '', 'shop.wimtecc.de.local', 'url', 'Archive', '/Archive/', ''),
(43, '', '', '', '', 'pdf', 'concatTool', 'pdftk', ''),
(44, '', '', '', '', 'pdf', 'overlayTool', 'pdftk', ''),
(45, '', '', '', '', 'path', 'Scripts', '/srv/www/vhosts/wimtecc.de/erp_r4/Scripts/', ''),
(46, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterLeft1', 'FooterLeft1', ''),
(47, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterLeft1', 'FooterLeft1', ''),
(48, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterLeft2', 'FooterLeft2', ''),
(49, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterLeft3', 'FooterLeft3', ''),
(50, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterLeft4', 'FooterLeft4', ''),
(51, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterMid1', 'FooterMid1', ''),
(52, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterMid2', 'FooterMid2', ''),
(53, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterMid3', 'FooterMid3', ''),
(54, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterMid4', 'FooterMid4', ''),
(55, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterRight1', 'FooterRight1', ''),
(56, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterRight2', 'FooterRight2', ''),
(57, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterRight3', 'FooterRight3', ''),
(58, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'FooterRight4', 'FooterRight4', ''),
(59, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'ReturnAddressLine', 'ReturnAddressLine', ''),
(60, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'HeaderRight1', 'HeaderRight1', ''),
(61, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'HeaderRight2', 'HeaderRight2', ''),
(62, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'HeaderRight3', 'HeaderRight3', ''),
(63, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'HeaderRight4', 'HeaderRight4', ''),
(64, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'HeaderRight5', 'HeaderRight5', ''),
(65, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'HeaderRight6', 'HeaderRight6', ''),
(66, '', '', '', 'BDocRegLetter', 'DocRegLetter', 'HeaderRight7', 'HeaderRight7', ''),
(67, '', '', '', 'www.wimtecc.de.local', 'path', 'Pictures', '/srv/www/vhosts/wimtecc.de/Bilder/', ''),
(68, '', '', '', 'www.wimtecc.de.local', 'path', 'Modules', '/srv/www/vhosts/wimtecc.de.local/erp_r3/Classes/modules', ''),
(69, '', '', '', 'www.wimtecc.de.local', 'siteeMail', 'Sales', 'Verkauf <khwelter@me.com>', ''),
(70, '', '', '', 'www.wimtecc.de.local', 'siteeMail', 'Archive', 'Acrhiv <khwelter@me.com>', ''),
(71, '', '', '', 'www.wimtecc.de.local', 'siteeMail', 'boundary', '____wimtecc.de---MM-DATA____', ''),
(73, '', '', '', 'www.wimtecc.de.local', 'mode', 'eMail', 'true', ''),
(74, '', '', '', 'www.wimtecc.de.local', 'siteeMail', 'Signature', 'Ihre MODIS GmbH', ''),
(76, '', '', '', 'mas.wimtecc.de.local', 'siteeMail', 'Archive', 'archive@flaschen24.eu', ''),
(77, '', '', '', 'mas.wimtecc.de.local', 'siteeMail', 'boundary', '____wimtecc.de---MM-DATA____', ''),
(78, '', '', '', 'mas.wimtecc.de.local', 'siteeMail', 'Sales', 'sales@flaschen24.eu', ''),
(79, '', '', '', 'mas.wimtecc.de.local', 'siteeMail', 'Signature', 'Ihre MODIS GmbH', ''),
(80, '', '', '', 'mas.wimtecc.de.local', 'mode', 'eMail', 'true', ''),
(81, '', '', '', 'www.wimtecc.de.local', 'shop', 'offline', '1', ''),
(82, '', '', '', '', 'path', 'Modules', '/srv/www/vhosts/wimtecc.de/mas_r1/lib/mod', ''),
(83, '', '', '', 'mas.wimtecc.de.local', 'erp', 'currency', 'EUR', ''),
(84, '', '', '', '', 'path', 'Catalog', '/srv/www/vhosts/wimtecc.de/Archive/Catalog', ''),
(85, '', '', '', '', 'path', 'Styles', '/srv/www/vhosts/wimtecc.de/mas_r1/rsrc/styles', ''),
(86, '', '', '', 'www.wimtecc.de.local', 'shop', 'defaultMarketId', 'shop', ''),
(87, '', '', '', '', 'path', 'Images', '/srv/www/vhosts/wimtecc.de.local/Images/', ''),
(88, '', '', '', '', 'server', 'os', 'MacOS', ''),
(89, '', '', '', '', '', '', '', ''),
(90, '', '', '', '', 'Adr', 'nsStart', '87000000', ''),
(91, '', '', '', '', 'Adr', 'nsEnd', '87009999', ''),
(92, '', '', '', '', 'CuInvc', 'nsStart', '46000000', ''),
(93, '', '', '', '', 'CuInvc', 'nsEnd', '46999999', ''),
(94, '', '', '', 'shop.wimtecc.de.local', 'shop', 'offline', '1', ''),
(95, '', '', '', 'shop.wimtecc.de.local', 'cuCart', 'debug', 'true', ''),
(96, '', '', '', 'shop.wimtecc.de.local', 'main', 'siteName', 'wimtecc.de', ''),
(97, '', '', '', 'shop.wimtecc.de.local', 'main', 'title', 'wimtecc - Demo Site', ''),
(98, '', '', '', 'shop.wimtecc.de.local', 'main', 'urlContact', 'http://www.wimtecc.de.local/Kontakt.html', ''),
(99, '', '', '', 'shop.wimtecc.de.local', 'mode', 'eMail', 'true', ''),
(100, '', '', '', 'shop.wimtecc.de.local', 'path', 'Modules', '/srv/www/vhosts/wimtecc.de/mas_r1/lib/mod', ''),
(101, '', '', '', 'shop.wimtecc.de.local', 'path', 'Pictures', '/srv/www/vhosts/wimtecc.de/Images/', ''),
(102, '', '', '', 'shop.wimtecc.de.local', 'shop', 'cookieDomain', 'shop.wimtecc.de.local', ''),
(103, '', '', '', 'shop.wimtecc.de.local', 'shop', 'cookieName', 'SessionId', ''),
(104, '', '', '', 'shop.wimtecc.de.local', 'shop', 'cookieTime', '0', ''),
(105, '', '', '', 'shop.wimtecc.de.local', 'shop', 'defaultMarketId', 'shop', ''),
(106, '', '', '', 'shop.wimtecc.de.local', 'shop', 'siteName', 'wimtecc.de', ''),
(107, '', '', '', 'shop.wimtecc.de.local', 'siteeMail', 'Archive', 'Acrhiv <khwelter@me.com>', ''),
(108, '', '', '', 'shop.wimtecc.de.local', 'siteeMail', 'boundary', '____wimtecc.de---MM-DATA____', ''),
(109, '', '', '', 'shop.wimtecc.de.local', 'siteeMail', 'Sales', 'Verkauf <khwelter@me.com>', ''),
(110, '', '', '', 'shop.wimtecc.de.local', 'siteeMail', 'Signature', 'Ihre MODIS GmbH', ''),
(111, '', '', '', 'shop.wimtecc.de.local', 'url', 'fullShop', 'http://shop.wimtecc.de.local/', ''),
(112, '', '', '', 'mas.wimtecc.de.local', 'path', 'xml', '/srv/www/vhosts/wimtecc.de/Archive/', ''),
(113, '', '', '', '', 'CashInvoice', 'nsStart', '99000000', ''),
(114, '', '', '', '', 'CashInvoice', 'nsEnd', '99009999', ''),
(115, '', '', '', 'shop.wimtecc.de.local', 'shop', 'templatesPath', '/clients/1a2b3c4d/apps/erm/shop/templates/', ''),
(116, '', '', '', 'def', 'trans', 'dbAlias', 'ui', ''),
(117, '', '', '', '', 'path', 'Archive', '/srv/www/vhosts/wimtecc.de.local/Archive/', ''),
(118, '', '', '', '', 'path', 'Logos', '/srv/www/vhosts/wimtecc.de.local/mas_r1/rsrc/logos/', ''),
(119, '', '', '', '', 'backend', 'fullSiteName', 'wimtecc.de', '');

-- --------------------------------------------------------

--
-- Table structure for table `AppOption`
--

DROP TABLE IF EXISTS `AppOption`;
CREATE TABLE IF NOT EXISTS `AppOption` (
  `Id` int(10) unsigned NOT NULL,
  `Class` varchar(64) NOT NULL,
  `OptionName` varchar(45) NOT NULL,
  `Key` varchar(45) NOT NULL,
  `Value` varchar(45) NOT NULL,
  `Symbol` varchar(16) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `AppOption`
--

INSERT INTO `AppOption` (`Id`, `Class`, `OptionName`, `Key`, `Value`, `Symbol`) VALUES
(1, '', 'Flag', '0', 'No', ''),
(2, '', 'Flag', '1', 'Yes', ''),
(3, '', 'Salutation', 'Mr.', 'Mr.', ''),
(4, '', 'Salutation', 'Mrs.', 'Mrs.', ''),
(5, '', 'Salutation', 'Herr', 'Herr', ''),
(6, '', 'Salutation', 'Frau', 'Frau', ''),
(7, '', 'Application', 'A', 'Application', 'fdgfdgdf'),
(8, '', 'Application', 'S', 'System', ''),
(9, '', 'Language', 'de', 'deutsch', ''),
(10, '', 'Language', 'de_DE', 'deutsch/Deutschland', ''),
(11, '', 'Language', 'en', 'english', ''),
(12, '', 'Language', 'en_US', 'englich/USA', ''),
(13, '', 'Language', 'fr', 'französisch', ''),
(14, '', 'Language', 'fr_FR', 'französisch/Frankreich', ''),
(15, '', 'Language', 'es', 'spanisch', ''),
(16, '', 'Language', 'es_ES', 'spanisch/Spanien', ''),
(17, '', 'Language', 'es_CL', 'spanisch/Chile', ''),
(18, '', 'Language', 'en_UK', 'englisch/England', ''),
(19, '', 'Language', 'en_CA', 'englisch/Kanada', ''),
(20, '', 'Language', 'fr_CA', 'französisch/Kanada', ''),
(21, '', 'Language', 'de_AU', 'deutsch/Österreich', ''),
(22, '', 'Language', 'de_CH', 'deutsch/Schweiz', ''),
(23, '', 'Currency', 'EUR', 'Euro', ''),
(24, '', 'Currency', 'USD', 'US-$', ''),
(25, '', 'Currency', 'CLP', 'Chilean Peso', ''),
(26, '', 'Title', '', '', ''),
(27, '', 'Title', 'Dr.', 'Dr.', ''),
(28, '', 'Title', 'Prof.', 'Prof.', ''),
(29, '', 'Title', 'Prof.-Dr.', 'Prof.-Dr.', ''),
(30, '', 'Currency', 'GBP', 'Britische Pfund', ''),
(31, '', 'Country', 'de', 'Deutschland', ''),
(32, '', 'Country', 'dk', 'Dänemark', ''),
(33, '', 'Country', 'at', 'Österreich', ''),
(34, '', 'Country', 'uk', 'England', ''),
(35, '', 'Country', 'nl', 'Niederlande', ''),
(36, '', 'Country', 'be', 'Belgien', ''),
(37, '', 'Country', 'es', 'Spanien', ''),
(38, '', 'Country', 'pt', 'Portugal', ''),
(39, '', 'Country', 'se', 'Schweden', ''),
(40, '', 'Country', 'fi', 'Finnland', ''),
(41, '', 'Country', 'no', 'Norwegen', ''),
(42, '', 'Country', 'fr', 'Frankreich', ''),
(43, '', 'Country', 'it', 'Italien', ''),
(44, '', 'Country', 'ch', 'Schweiz', ''),
(45, '', 'Country', 'us', 'Vereinigte Staaten', ''),
(46, '', 'Country', 'cl', 'Chile', ''),
(47, '', 'Tax', 'A', '19', ''),
(48, '', 'Tax', 'A-', '19', ''),
(49, '', 'Tax', 'B', '7', ''),
(50, '', 'Tax', 'B-', '7', ''),
(51, '', 'PageType', '1', 'Type_1', ''),
(52, '', 'PageType', '2', 'Type_2', ''),
(53, '', 'PageType', '3', 'Simple w/ fulltext', ''),
(54, '', 'PageType', '6', 'Simple w/ file reference', ''),
(55, '', 'PageType', '7', 'Redirect to Page No', ''),
(56, '', 'Title', 'Dipl.-Ing.', 'Dipl.-Ing.', ''),
(57, '', 'Title', 'Dipl.-Phys.', 'Dipl.-Phys.', ''),
(58, '', 'ArtShopVis', '0', 'No, by no means!', ''),
(59, '', 'ArtShopVis', '3', 'Search only for special customers', ''),
(60, '', 'ArtShopVis', '6', 'Search only for special customers', ''),
(61, '', 'ArtShopVis', '9', 'Yes for everybody', ''),
(62, '', 'ArtCatVis', '0', 'No, by no means!', ''),
(63, '', 'ArtCatVis', '7', 'Special customers catalogs', ''),
(64, '', 'ArtCatVis', '9', 'Yes in all versions', ''),
(65, '', 'ArticleRights', '1', 'AnyUser', ''),
(66, '', 'ArticleRights', '2', 'LoggedInUser', ''),
(67, '', 'ArticleRights', '4', 'B2B', ''),
(68, '', 'ArticleRights', '8', 'B2C', ''),
(69, '', 'ArticleRights', '16', 'ViewStock', ''),
(70, '', 'ArticleRights', '32', '', ''),
(71, '', 'PriceType', '0', 'Standard', ''),
(72, '', 'PriceType', '2', 'Special', ''),
(73, '', 'PriceType', '1', 'Manual', ''),
(74, '', 'StatPayment', '0', 'Open', ''),
(75, '', 'StatPayment', '80', 'Pending', ''),
(76, '', 'StatPayment', '90', 'Closed', ''),
(77, '', 'ModePay', '1', 'Pay on Order', ''),
(78, '', 'ModePay', '2', 'Invoice', ''),
(79, '', 'Market', 'default', 'default', ''),
(80, '', 'Market', 'ebay_DE', 'ebay_DE', ''),
(81, '', 'Market', 'shop', 'shop', ''),
(82, '', 'Organization', 'gym', 'Gymnasium', ''),
(83, '', 'Organization', 'hs', 'Hauptschule', ''),
(84, '', 'Organization', 'rs', 'Realschule', ''),
(85, '', 'Organization', 'uni', 'Universität', ''),
(86, '', 'Organization', 'th', 'Technische Hochschule', ''),
(87, '', 'CustomerType', 'b2b', 'Business', ''),
(88, '', 'CustomerType', 'b2c', 'Consumer', ''),
(89, '', 'ModeDelivery', 'dc', 'Don''t care', ''),
(90, '', 'ModeDelivery', 'dpca', 'Deliver parts at cost', ''),
(91, '', 'ModeDelivery', 'dp', 'Deliver parts', ''),
(92, '', 'ModeDelivery', 'dc', 'Deliver complete only', ''),
(93, '', '', '', '', ''),
(94, '', 'ModeInvoice', 'ic', 'Invoice complete only', ''),
(95, '', 'ModeInvoice', 'dc', 'Don''t care', ''),
(96, '', 'ModeInvoice', 'ip', 'Invoice partially', ''),
(97, '', 'ArticleSupplyStatus', '0', 'Normal', ''),
(98, '', 'ArticleSupplyStatus', '5', 'Limited', ''),
(99, '', 'ArticleSupplyStatus', '7', 'No new orders', ''),
(100, '', 'ArticleSupplyStatus', '8', 'Replaced', ''),
(101, '', 'ArticleSupplyStatus', '9', 'Discontinued', ''),
(102, 'Article', 'ArticleCompositionType', '0', 'Normal (monolitic item)', 'norm'),
(103, 'Article', 'ArticleCompositionType', '10', 'Composite with Item List', 'comwl'),
(104, 'Article', 'ArticleCompositionType', '5', 'Monolitic with Item List', 'monwl'),
(105, '', 'ArticleSPCalculation', '0', 'Manual only', ''),
(106, '', 'ArticleSPCalculation', '1', 'Automatic', ''),
(107, '', 'ArticleSPCalculation', '2', 'MSRP only', ''),
(108, '', 'OrderUnit', 'BG', 'Bag', ''),
(109, '', 'OrderUnit', 'BJ', 'Basket', ''),
(110, '', 'OrderUnit', 'BK', 'Basket', ''),
(111, '', 'OrderUnit', 'BO', 'Bottle', ''),
(112, '', 'OrderUnit', 'CA', 'Canister', ''),
(113, '', 'OrderUnit', 'CMK', 'square cm', ''),
(114, '', 'OrderUnit', 'CMQ', 'Cubic cm', ''),
(115, '', 'OrderUnit', 'CMT', 'cm', ''),
(116, '', 'OrderUnit', 'CR', 'Create', ''),
(117, '', 'OrderUnit', 'CS', 'Case', ''),
(118, '', 'OrderUnit', 'CT', 'Carton', ''),
(119, '', 'OrderUnit', 'DAY', 'Day', ''),
(120, '', 'OrderUnit', 'DR', 'Drum', ''),
(121, '', 'OrderUnit', 'GRM', 'gramm', ''),
(122, '', 'OrderUnit', 'HUR', 'Hour', ''),
(123, '', 'OrderUnit', 'KAN', 'Canister', ''),
(124, '', 'OrderUnit', 'KGM', 'kilogramm', ''),
(125, '', 'OrderUnit', 'KMT', 'kilometer', ''),
(126, '', 'OrderUnit', 'LE', 'Leistungseinheit', ''),
(127, '', 'OrderUnit', 'LTR', 'Liter', ''),
(128, '', 'OrderUnit', 'M1', 'mg/L', ''),
(129, '', 'OrderUnit', 'MGM', 'milligramm', ''),
(130, '', 'OrderUnit', 'MLT', 'mL', ''),
(131, '', 'OrderUnit', 'MMT', 'mm', ''),
(132, '', 'OrderUnit', 'MTK', 'square meter', ''),
(133, '', 'OrderUnit', 'MTQ', 'cubic meter', ''),
(134, '', 'OrderUnit', 'MTR', 'meter', ''),
(135, '', 'OrderUnit', 'PA', 'Packet', ''),
(136, '', 'OrderUnit', 'PCE', 'piece', ''),
(137, '', 'OrderUnit', 'PF', 'palette', ''),
(138, '', 'OrderUnit', 'PK', 'pack', ''),
(139, '', 'OrderUnit', 'PR', 'pair', ''),
(140, '', 'OrderUnit', 'RO', 'reel', ''),
(141, '', 'OrderUnit', 'SA', 'sack', ''),
(142, '', 'CCFunction', 'GEN', 'General', ''),
(143, '', 'CCFunction', 'FBPH', 'Fachbereich Physik', ''),
(144, '', 'CCFunction', 'FBBI', 'Fachbereich Biologie', ''),
(145, '', 'CCFunction', 'FBCH', 'Fachbereich Chemie', ''),
(146, '', 'CCFunction', 'FBLPH', 'Fachbereichsleiter Physik', ''),
(147, '', 'CCFunction', 'FBLBI', 'Fachbereichsleiter Physik', ''),
(148, '', 'CCFunction', 'FBLCH', 'Fachbereichsleiter Chemie', ''),
(149, '', 'CCFunction', 'SCHL', 'Schulleiter', ''),
(150, '', 'CCFunction', 'SCHLL', 'Schulleitung', ''),
(151, '', 'CCFunction', 'BSCH', 'Beschaffer', ''),
(152, '', 'CCFunction', 'FVV', 'Foritzende(r) Förderverein', ''),
(153, '', 'SCFunction', 'GEN', 'General', ''),
(154, '', 'SCFunction', 'RCPT', 'Recption', ''),
(155, '', 'SCFunction', 'SECR', 'Secretary', ''),
(156, '', 'SCFunction', 'SALES', 'Sales', ''),
(157, '', 'SCFunction', 'PURCH', 'Purchasing', ''),
(158, '', 'SCFunction', 'MNGR', 'Manager (general)', ''),
(159, '', 'SCFuncrion', 'OWNR', 'Owner', ''),
(160, '', 'SCFunction', 'CFO', 'Chief Financial Officer', ''),
(161, '', 'SCFunction', 'CEO', 'Chief Executive Officer', ''),
(162, '', 'SCFunction', 'HHR', 'Head of Human Resources', ''),
(163, '', 'Application', '', '', ''),
(164, '', 'Application', '', '', ''),
(165, '', 'Organization', '_none', 'Andere', ''),
(166, '', 'AuthObjectAttribute', 'grant', 'Grant', ''),
(167, '', 'AuthObjectAttribute', 'revoke', 'Revoke', ''),
(168, '', 'AuthObjectType', 'scr', 'Screen Mask', ''),
(169, '', 'AuthObjectType', 'dbt', 'Database Table', ''),
(170, '', 'AuthObjectType', 'fnc', 'Object Function', ''),
(171, '', 'AuthObjectType', 'dbv', 'Database Value', ''),
(172, '', 'AuthObjectAttribute', 'revokevalue', 'Revoke by Value', ''),
(173, '', 'AuthObjectAttribute', 'grantvalue', 'Grant by Value', ''),
(174, '', 'AuthObjectContext', 'oper', 'Operation Management', ''),
(175, '', 'AuthObjectContext', 'admin', 'System Administration', ''),
(176, '', 'AuthObjectContext', 'app', 'Application', ''),
(177, 'CustomerOrder', 'Status', '0', 'New', 'new'),
(178, 'CustomerOrder', 'Status', '30', 'Confirmed', 'conf'),
(179, 'CustomerOrder', 'Status', '50', 'Ongoing', 'ongoing'),
(180, 'CustomerOrder', 'Status', '90', 'Closed', 'closed'),
(181, 'CustomerOrder', 'Status', '990', 'Cancelled', 'canc'),
(182, 'CustomerOrderItem', 'ItemType', '0', 'Normal', ''),
(183, 'CustomerOrderItem', 'ItemType', '1', 'Invoice only', ''),
(184, 'CustomerOrderItem', 'ItemType', '2', 'Delivery only', ''),
(185, 'CustomerOrder', 'Status', '970', 'Exported', 'export'),
(186, 'CustomerOrder', 'Status', '980', 'On-hold', 'hold'),
(187, 'CustomerOrder', 'Status', '0', 'New', 'new'),
(188, 'CustomerOrder', 'Status', '0', 'New', 'new'),
(189, 'CustomerOrder', 'CommStatus', '0', 'no', 'no'),
(190, 'CustomerOrder', 'CommStatus', '50', 'partial', 'partial'),
(191, 'CustomerOrder', 'CommStatus', '90', 'full', 'full'),
(193, 'CustomerOrder', 'DeliveryStatus', '0', 'no', 'no'),
(194, 'CustomerOrder', 'DeliveryStatus', '50', 'partial', 'partial'),
(195, 'CustomerOrder', 'DeliveryStatus', '90', 'all', 'all'),
(196, 'CustomerOrder', 'InvoiceStatus', '0', 'no', 'no'),
(197, 'CustomerOrder', 'InvoiceStatus', '50', 'partial', 'partial'),
(198, 'CustomerOrder', 'InvoiceStatus', '90', 'all', 'all');

-- --------------------------------------------------------

--
-- Table structure for table `AppTrans`
--

DROP TABLE IF EXISTS `AppTrans`;
CREATE TABLE IF NOT EXISTS `AppTrans` (
  `Id` int(8) NOT NULL,
  `Name` varchar(32) NOT NULL,
  `RefNr` varchar(128) NOT NULL,
  `Language` varchar(10) NOT NULL,
  `Fulltext` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Fulltext2` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `UseCount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `AppUser`
--

DROP TABLE IF EXISTS `AppUser`;
CREATE TABLE IF NOT EXISTS `AppUser` (
  `Id` int(8) NOT NULL,
  `ClientId` varchar(32) NOT NULL,
  `UserId` varchar(16) NOT NULL DEFAULT '',
  `ApplicationSystemId` varchar(32) NOT NULL DEFAULT 'erpdemo',
  `ApplicationId` varchar(32) NOT NULL,
  `OrgName1` varchar(32) NOT NULL DEFAULT '',
  `OrgName2` varchar(32) NOT NULL DEFAULT '',
  `LastName` varchar(16) DEFAULT NULL,
  `FirstName` varchar(16) DEFAULT NULL,
  `MailId` varchar(64) NOT NULL DEFAULT '',
  `Password` varchar(34) NOT NULL DEFAULT '',
  `MD5Password` varchar(64) NOT NULL DEFAULT '',
  `Street` varchar(32) DEFAULT NULL,
  `Number` varchar(32) DEFAULT NULL,
  `City` varchar(32) DEFAULT NULL,
  `ZIP` varchar(8) DEFAULT NULL,
  `Province` varchar(32) DEFAULT NULL,
  `Country` varchar(32) DEFAULT NULL,
  `Telephone` varchar(24) DEFAULT NULL,
  `Cellphone` varchar(24) DEFAULT NULL,
  `FAX` varchar(24) DEFAULT NULL,
  `Registration` date NOT NULL DEFAULT '0000-00-00',
  `Type` smallint(1) NOT NULL DEFAULT '0',
  `Lang` varchar(8) NOT NULL DEFAULT '',
  `Confirmed` smallint(6) NOT NULL DEFAULT '0',
  `Level` int(3) NOT NULL DEFAULT '0',
  `ValidFrom` date NOT NULL,
  `ValidTo` date NOT NULL,
  `DateReg` date NOT NULL,
  `DateLastAcc` date NOT NULL,
  `Packages` varchar(256) NOT NULL DEFAULT '*',
  `Modules` varchar(256) NOT NULL DEFAULT '*'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Uniquely Identifies Users in the ETE/BETE Systems';

--
-- Dumping data for table `AppUser`
--

INSERT INTO `AppUser` (`Id`, `ClientId`, `UserId`, `ApplicationSystemId`, `ApplicationId`, `OrgName1`, `OrgName2`, `LastName`, `FirstName`, `MailId`, `Password`, `MD5Password`, `Street`, `Number`, `City`, `ZIP`, `Province`, `Country`, `Telephone`, `Cellphone`, `FAX`, `Registration`, `Type`, `Lang`, `Confirmed`, `Level`, `ValidFrom`, `ValidTo`, `DateReg`, `DateLastAcc`, `Packages`, `Modules`) VALUES
(1, '1a2b3c4d', 'miskhwe', 'immo', 'man', 'a', 'b', 'd', 'c', '', 'psion0', '', NULL, NULL, NULL, NULL, NULL, 'de', NULL, NULL, NULL, '2015-01-01', 1, 'de', 0, 0, '2015-01-01', '2099-12-31', '2099-12-31', '2099-12-31', '*', '*');

-- --------------------------------------------------------

--
-- Table structure for table `AppUserRole`
--

DROP TABLE IF EXISTS `AppUserRole`;
CREATE TABLE IF NOT EXISTS `AppUserRole` (
  `Id` int(10) NOT NULL,
  `UserId` varchar(32) NOT NULL,
  `RoleId` varchar(32) NOT NULL,
  `Active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `AppUserRole`
--

INSERT INTO `AppUserRole` (`Id`, `UserId`, `RoleId`, `Active`) VALUES
(1, 'miskhwe', 'admin', 1),
(6, 'miskhwe', 'sysAdmin', 1),
(10, 'miskhwe', '__basic', 1),
(12, 'miskhwe', '_basicERM', 1),
(13, 'miskhwe', 'buyer', 1),
(14, 'miskhwe', 'seller', 1),
(15, 'miskhwe', 'logistic', 1),
(24, 'miskhwe', 'erpMaster', 1),
(25, 'shop', '__basic', 1),
(27, 'shop', 'shop', 1),
(28, 'miskhwe', 'sysOp', 1),
(29, 'miskhwe', '_RoleAdmin', 1),
(30, 'miskhwe', '_userAdmin', 1),
(31, 'miskhwe', '_ProfileAdmin', 1),
(32, 'miskhweNEU', '__basic', 1),
(33, 'miskhweNEU', '_basicERM', 1),
(34, 'miskhweNEU', 'buyer', 1);

-- --------------------------------------------------------

--
-- Table structure for table `AuthObject`
--

DROP TABLE IF EXISTS `AuthObject`;
CREATE TABLE IF NOT EXISTS `AuthObject` (
  `Id` int(11) NOT NULL,
  `AuthObjectId` varchar(128) NOT NULL,
  `AuthObjectType` varchar(8) NOT NULL,
  `ObjectName` varchar(128) NOT NULL,
  `ObjectAttribute` varchar(128) NOT NULL,
  `AttrValue` varchar(64) NOT NULL,
  `Description` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=294 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `AuthObject`
--

INSERT INTO `AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`) VALUES
(1, 'dbt_AppUser_store', 'dbt', 'appSys.AppUser.store', 'grant', '', ''),
(2, 'fnc_AppUser_create', 'fnc', 'AppUser.add', 'grant', '', ''),
(3, 'fnc_AppUser_update', 'fnc', 'AppUser.upd', 'grant', '', ''),
(4, 'fnc_Role_create', 'fnc', 'Role.add', 'grant', '', ''),
(5, 'fnc_Role_update', 'fnc', 'Role.upd', 'grant', '', ''),
(6, 'fnc_Profile_create', 'fnc', 'Profile.add', 'grant', '', ''),
(7, 'fnc_Profile_update', 'fnc', 'Profile.upd', 'grant', '', ''),
(8, 'fnc_AuthObject_*', 'fnc', 'AuthObject.*', 'grant', '', ''),
(9, 'dbt_RoleProfile_store', 'dbt', 'appSys.RoleProfile.store', 'grant', '', ''),
(10, 'dbt_RoleProfile_update', 'dbt', 'appSys.RoleProfile.update', 'grant', '', ''),
(11, 'scr_ModAdmin_Client', 'scr', 'ModAdmin.client', 'grant', '', ''),
(12, 'dbt_AppUserRole_store', 'dbt', 'appSys.AppUserRole.store', 'grant', '', ''),
(13, 'scr_ModAdmin', 'scr', 'ModAdmin', 'grant', '', ''),
(14, 'scr_ModAdmin_AppUser', 'scr', 'ModAdmin.appUser', 'grant', '', ''),
(15, 'scr_ModAdmin_Role', 'scr', 'ModAdmin.Role', 'grant', '', ''),
(16, 'scr_ModBase', 'scr', 'ModBase', 'grant', '', ''),
(17, 'scr_ModBase_*', 'scr', 'ModBase.*', 'grant', '', ''),
(18, 'scr_ModAdmin_Profile', 'scr', 'ModAdmin.Profile', 'grant', '', ''),
(19, 'scr_ModAdmin_AuthObject', 'scr', 'ModAdmin.authObject', 'grant', '', ''),
(20, 'dbt_Role_*', 'dbt', 'appSys.Role.*', 'grant', '', ''),
(21, 'dbt_AuthObject_*', 'dbt', 'appSys.AuthObject.*', 'grant', '', ''),
(22, 'dbt_Profile_*', 'dbt', 'appSys.Profile.*', 'grant', '', ''),
(23, 'dbt_ProfileAuthObject_*', 'dbt', 'appSys.ProfileAuthObject.*', 'grant', '', ''),
(24, 'fnc_Profile_*', 'fnc', 'Profile.*', 'grant', '', ''),
(25, 'dbt_AppTrans_store', 'dbt', 'appSys.AppTrans.store', 'grant', '123', ''),
(26, 'scr_ModOperator_*', 'scr', 'ModOperator.*', 'grant', '', ''),
(27, 'scr_ModOperator', 'scr', 'ModOperator', 'grant', '', ''),
(28, 'scr_ModAdmin_AuthObject_gridAuthObjectOV', 'scr', 'ModAdmin.authObject.gridAuthObjectOV', 'grant', '', ''),
(29, 'scr_ModAdmin_*', 'scr', 'ModAdmin.*', 'grant', '', ''),
(30, 'scr_ModAdmin_AppUser_*', 'scr', 'ModAdmin.appUser.*', 'grant', '', ''),
(31, 'scr_ModAdmin_Role_*', 'scr', 'ModAdmin.Role.*', 'grant', '', ''),
(32, 'scr_ModAdmin_Profile_*', 'scr', 'ModAdmin.Profile.*', 'grant', '', ''),
(33, 'scr_ModAdmin_Client_*', 'scr', 'ModAdmin.client.*', 'grant', '', ''),
(34, 'scr_ModAdmin_Profile_hide_gridProfileAuthObjectsFNC', 'scr', 'ModAdmin.Profile.gridProfileAuthObjectsFNC', 'grant', '', ''),
(35, 'scr_ModAdmin_Profile_hide_gridRoleWithProfile', 'scr', 'ModAdmin.Profile.gridRoleWithProfile', 'revoke', '', ''),
(36, 'scr_ModAdmin_Profile_hide_tabPageProfileAuthObjectsFNC', 'scr', 'ModAdmin.Profile.tabPageProfileAuthObjectsFNC', 'grant', '', ''),
(37, 'scr_ModAdmin_Profile_hide_storeProfile', 'scr', 'ModAdmin.Profile.buttonStoreProfile', 'revoke', '', ''),
(38, 'scr_ModAdmin_Profile_1', 'scr', 'ModAdmin.Profile.Name.edit', 'revoke', '', ''),
(39, 'scr_ModAdmin_AppUser_MiscInfo', 'scr', 'ModAdmin.appUser.MiscInfo', 'grant', '', ''),
(40, 'scr_ModAdmin_AppUser_Create', 'scr', 'ModAdmin.appUser.Create', 'grant', '', ''),
(41, 'scr_ModAdmin_AppUser_Access', 'scr', 'ModAdmin.appUser.Access', 'grant', '', ''),
(42, 'dbt_AuthObject_update', 'dbt', 'appSys.AuthObject.update', 'grant', '', ''),
(43, 'dbt_AuthObject_update_AuthObjectType', 'dbt', 'appSys.AuthObject.update.AuthObjectType', 'grant', '123', ''),
(44, 'dbt_AppTrans_avoidFY', 'dbv', 'AppTrans.AttrValue', 'revokevalue', 'fuck', ''),
(45, 'dbt_AuthObject_avoidFY', 'dbv', 'appSys.AuthObject.AttrValue', 'revokevalue', 'fuck', ''),
(46, 'dbt_AppUser_*', 'dbt', 'appSys.AppUser.*', 'grant', '', ''),
(47, 'dbv_AppUser_avoidFY', 'dbv', 'AppUser.Street', 'revokevalue', '', ''),
(48, 'dbv_AppUser_Number_avoid1', 'dbv', 'AppUser.Number', 'revokevalue', '', ''),
(49, 'dbt_AppTrans_read', 'dbt', 'appSys.AppTrans.read', 'grant', '', ''),
(50, 'dbt_ApplicationSystem_*', 'dbt', 'ApplicationSystem.*', 'grant', '', ''),
(51, 'dbt_Application_*', 'dbt', 'Application.*', 'grant', '', ''),
(52, 'dbt_Client_*', 'dbt', 'Client.*', 'grant', '', ''),
(53, 'dbt_ClientApplication_*', 'dbt', 'ClientApplication.*', 'grant', '', ''),
(54, 'dbt_v_tables_*', 'dbt', 'v_tables.*', 'grant', '', ''),
(55, 'dbt_ApplicationVersion_*', 'dbt', 'ApplicationVersion.*', 'grant', '', ''),
(56, 'dbt_SysConfigObj_*', 'dbt', 'SysConfigObj.*', 'grant', '', ''),
(57, 'dbt_SysSession_*', 'dbt', 'SysSession.*', 'grant', '', ''),
(58, 'dbt_SysTrans_*', 'dbt', 'sys.SysTrans.*', 'grant', '', ''),
(59, 'dbt_SysUser_*', 'dbt', 'SysUser.*', 'grant', '', ''),
(60, 'dbt_applicationperclient_*', 'dbt', 'applicationperclient.*', 'grant', '', ''),
(61, 'dbt_applicationsystemperclient_*', 'dbt', 'applicationsystemperclient.*', 'grant', '', ''),
(62, 'dbt_AppConfigObj_*', 'dbt', 'AppConfigObj.*', 'grant', '', ''),
(63, 'dbt_AppTrans_*', 'dbt', 'AppTrans.*', 'grant', '', ''),
(64, 'dbt_AppUserRole_*', 'dbt', 'appSys.AppUserRole.*', 'grant', '', ''),
(65, 'dbt_RoleProfile_*', 'dbt', 'appSys.RoleProfile.*', 'grant', '', ''),
(66, 'dbt_v_AppUserAuthObjectSurvey_*', 'dbt', 'v_AppUserAuthObjectSurvey.*', 'grant', '', ''),
(67, 'dbt_v_AppUserRoleProfileAuthObjectSurvey_*', 'dbt', 'v_AppUserRoleProfileAuthObjectSurvey.*', 'grant', '', ''),
(68, 'dbt_v_AppUserRoleSurvey_*', 'dbt', 'v_AppUserRoleSurvey.*', 'grant', '', ''),
(69, 'dbt_v_AppUserWithRoleSurvey_*', 'dbt', 'v_AppUserWithRoleSurvey.*', 'grant', '', ''),
(70, 'dbt_v_ProfileAuthObjectSurvey_*', 'dbt', 'v_ProfileAuthObjectSurvey.*', 'grant', '', ''),
(71, 'dbt_v_ProfileWithAuthObjectSurvey_*', 'dbt', 'ProfileWithAuthObjectSurvey.*', 'grant', '', ''),
(72, 'dbt_v_RoleProfileSurvey_*', 'dbt', 'v_RoleProfileSurvey.*', 'grant', '', ''),
(73, 'dbt_v_RoleWithProfileSurvey_*', 'dbt', 'v_RoleWithProfileSurvey.*', 'grant', '', ''),
(74, 'dbt_v_tableswithoutAuthObject_*', 'dbt', 'v_tableswithoutAuthObject.*', 'grant', '', ''),
(75, 'dbt_SysTrans_read', 'dbt', 'sys.SysTrans.read', 'grant', '', ''),
(76, 'scr_ModAdmin_AppTrans', 'scr', 'ModAdmin.AppTrans', 'grant', '', ''),
(77, 'scr_ModAdmin_AppTrans_*', 'scr', 'ModAdmin.AppTrans.*', 'grant', '', ''),
(78, 'dbt_CashSaleItem_*', 'dbt', 'CashSaleItem.*', 'grant', '', ''),
(79, 'dbt_Address_*', 'dbt', 'def.Address.*', 'grant', '', ''),
(80, 'dbt_AddressContactSurvey_read', 'dbt', 'def.AddressContactSurvey.read', 'grant', '', ''),
(81, 'scr_editAddressContact', 'scr', 'ModBase.mainAddress.editAddressContact', 'grant', '', ''),
(82, 'dbt_AddressContact_*', 'dbt', 'def.AddressContact.*', 'grant', '', ''),
(83, 'dbt_Proprietor_*', 'dbt', 'def.Proprietor.*', 'grant', '', ''),
(84, 'dbt_ProprietorContactSurvey_read', 'dbt', 'def.v_AddressContactSurvey.read', 'grant', '', ''),
(85, 'dbt_ProprietorContact_*', 'dbt', 'def.ProprietorContact.*', 'grant', '', ''),
(86, 'dbt_ArtQPC_*', 'dbt', 'def.ArtQPC.*', 'grant', '', ''),
(87, 'dbt_ArtStruct_*', 'dbt', 'def.ArtStruct.*', 'grant', '', ''),
(88, 'dbt_Article_*', 'dbt', 'def.Article.*', 'grant', '', ''),
(89, 'dbt_ArticleAvgNeed_*', 'dbt', 'def.ArticleAvgNeed.*', 'grant', '', ''),
(90, 'dbt_ArticleComponent_*', 'dbt', 'def.ArticleComponent.*', 'grant', '', ''),
(91, 'dbt_ArticleGroup_*', 'dbt', 'def.ArticleGroup.*', 'grant', '', ''),
(92, 'dbt_ArticleGroupItem_*', 'dbt', 'def.ArticleGroupItem.*', 'grant', '', ''),
(93, 'dbt_ArticleImage_*', 'dbt', 'def.ArticleImage.*', 'grant', '', ''),
(94, 'dbt_ArticleProposal_*', 'dbt', 'def.ArticleProposal.*', 'grant', '', ''),
(95, 'dbt_ArticlePurchasePrice_*', 'dbt', 'def.ArticlePurchasePrice.*', 'grant', '', ''),
(96, 'dbt_ArticlePurchasePriceRel_*', 'dbt', 'def.ArticlePurchasePriceRel.*', 'grant', '', ''),
(97, 'dbt_ArticleSalesPrice_*', 'dbt', 'def.ArticleSalesPrice.*', 'grant', '', ''),
(98, 'dbt_ArticleSalesPriceCache_*', 'dbt', 'def.ArticleSalesPriceCache.*', 'grant', '', ''),
(99, 'dbt_ArticleStock_*', 'dbt', 'def.ArticleStock.*', 'grant', '', ''),
(100, 'dbt_ArticleStockCorrection_*', 'dbt', 'ArticleStockCorrection.*', 'grant', '', ''),
(101, 'dbt_ArticleStockCorrectionItem_*', 'dbt', 'ArticleStockCorrectionItem.*', 'grant', '', ''),
(102, 'dbt_ArticleStockPredict_*', 'dbt', 'ArticleStockPredict.*', 'grant', '', ''),
(103, 'dbt_ArticleText_*', 'dbt', 'def.ArticleText.*', 'grant', '', ''),
(104, 'dbt_ArticleTurnover_*', 'dbt', 'ArticleTurnover.*', 'grant', '', ''),
(105, 'dbt_Assembly_*', 'dbt', 'Assembly.*', 'grant', '', ''),
(106, 'dbt_AssemblyItem_*', 'dbt', 'AssemblyItem.*', 'grant', '', ''),
(107, 'dbt_Attribute_*', 'dbt', 'def.Attribute.*', 'grant', '', ''),
(108, 'dbt_AttributeTemplate_*', 'dbt', 'def.AttributeTemplate.*', 'grant', '', ''),
(109, 'dbt_AttributeTemplateItem_*', 'dbt', 'def.AttributeTemplateItem.*', 'grant', '', ''),
(110, 'dbt_Authority_*', 'dbt', 'Authority.*', 'grant', '', ''),
(111, 'dbt_BankAccount_*', 'dbt', 'BankAccount.*', 'grant', '', ''),
(112, 'dbt_BankAccountTransaction_*', 'dbt', 'BankAccountTransaction.*', 'grant', '', ''),
(113, 'dbt_Carrier_*', 'dbt', 'Carrier.*', 'grant', '', ''),
(114, 'dbt_CarrierOption_*', 'dbt', 'CarrierOption.*', 'grant', '', ''),
(115, 'dbt_CashInvoice_*', 'dbt', 'CashInvoice.*', 'grant', '', ''),
(116, 'dbt_CashInvoiceItem_*', 'dbt', 'CashInvoiceItem.*', 'grant', '', ''),
(117, 'dbt_CatalogGroup_*', 'dbt', 'CatalogGroup.*', 'grant', '', ''),
(118, 'dbt_CatalogGroupItem_*', 'dbt', 'CatalogGroupItem.*', 'grant', '', ''),
(119, 'dbt_ChemicalInfo_*', 'dbt', 'ChemicalInfo.*', 'grant', '', ''),
(120, 'dbt_ClientContact_*', 'dbt', 'ClientContact.*', 'grant', '', ''),
(121, 'dbt_ClientLog_*', 'dbt', 'def.ClientLog.*', 'grant', '', ''),
(122, 'dbt_Colli_*', 'dbt', 'Colli.*', 'grant', '', ''),
(123, 'dbt_ColliItem_*', 'dbt', 'ColliItem.*', 'grant', '', ''),
(124, 'dbt_ColliItem_DPD_*', 'dbt', 'ColliItem_DPD.*', 'grant', '', ''),
(125, 'dbt_CuCred_*', 'dbt', 'CuCred.*', 'grant', '', ''),
(126, 'dbt_CuCredItem_*', 'dbt', 'CuCredItem.*', 'grant', '', ''),
(127, 'dbt_CuRmnd_*', 'dbt', 'CuRmnd.*', 'grant', '', ''),
(128, 'dbt_CuTest_*', 'dbt', 'CuTest.*', 'grant', '', ''),
(129, 'dbt_CuTestItem_*', 'dbt', 'CuTestItem.*', 'grant', '', ''),
(130, 'dbt_Currency_*', 'dbt', 'def.Currency.*', 'grant', '', ''),
(131, 'dbt_ProprietorCart_*', 'dbt', 'def.ProprietorCart.*', 'grant', '', ''),
(132, 'dbt_ProprietorCartItem_*', 'dbt', 'def.ProprietorCartItem.*', 'grant', '', ''),
(133, 'dbt_ProprietorCommission_*', 'dbt', 'def.ProprietorCommission.*', 'grant', '', ''),
(134, 'dbt_ProprietorCommissionItem_*', 'dbt', 'def.ProprietorCommissionItem.*', 'grant', '', ''),
(135, 'dbt_ProprietorDelivery_*', 'dbt', 'def.ProprietorDelivery.*', 'grant', '', ''),
(136, 'dbt_ProprietorDeliveryItem_*', 'dbt', 'def.ProprietorDeliveryItem.*', 'grant', '', ''),
(137, 'dbt_ProprietorDeliveryPackage_*', 'dbt', 'def.ProprietorDeliveryPackage.*', 'grant', '', ''),
(138, 'dbt_ProprietorInvoice_*', 'dbt', 'def.ProprietorInvoice.*', 'grant', '', ''),
(139, 'dbt_ProprietorInvoiceItem_*', 'dbt', 'def.ProprietorInvoiceItem.*', 'grant', '', ''),
(140, 'dbt_ProprietorOffer_*', 'dbt', 'def.ProprietorOffer.*', 'grant', '', ''),
(141, 'dbt_ProprietorOfferItem_*', 'dbt', 'def.ProprietorOfferItem.*', 'grant', '', ''),
(142, 'dbt_ProprietorOrder_*', 'dbt', 'def.ProprietorOrder.*', 'grant', '', ''),
(143, 'dbt_ProprietorOrderItem_*', 'dbt', 'def.ProprietorOrderItem.*', 'grant', '', ''),
(144, 'dbt_ProprietorRFQ_*', 'dbt', 'def.ProprietorRFQ.*', 'grant', '', ''),
(145, 'dbt_ProprietorRFQItem_*', 'dbt', 'def.ProprietorRFQItem.*', 'grant', '', ''),
(146, 'dbt_ProprietorTempOrder_*', 'dbt', 'def.ProprietorTempOrder.*', 'grant', '', ''),
(147, 'dbt_ProprietorTempOrderItem_*', 'dbt', 'def.ProprietorTempOrderItem.*', 'grant', '', ''),
(148, 'dbt_Document_*', 'dbt', 'Document.*', 'grant', '', ''),
(149, 'dbt_EKDaten_*', 'dbt', 'EKDaten.*', 'grant', '', ''),
(150, 'dbt_EKPreis_*', 'dbt', 'EKPreis.*', 'grant', '', ''),
(151, 'dbt_Ebay_Item_*', 'dbt', 'Ebay_Item.*', 'grant', '', ''),
(152, 'dbt_Ebay_Order_*', 'dbt', 'Ebay_Order.*', 'grant', '', ''),
(153, 'dbt_Ebay_ShippingAddress_*', 'dbt', 'Ebay_ShippingAddress.*', 'grant', '', ''),
(154, 'dbt_Ebay_Transaction_*', 'dbt', 'Ebay_Transaction.*', 'grant', '', ''),
(155, 'dbt_FaAccount_*', 'dbt', 'FaAccount.*', 'grant', '', ''),
(156, 'dbt_FaJournal_*', 'dbt', 'FaJournal.*', 'grant', '', ''),
(157, 'dbt_FaJournalLine_*', 'dbt', 'FaJournalLine.*', 'grant', '', ''),
(158, 'dbt_FaJournalLineItem_*', 'dbt', 'FaJournalLineItem.*', 'grant', '', ''),
(159, 'dbt_FaPeriod_*', 'dbt', 'FaPeriod.*', 'grant', '', ''),
(160, 'dbt_FiscalYear_*', 'dbt', 'FiscalYear.*', 'grant', '', ''),
(161, 'dbt_Group_*', 'dbt', 'Group.*', 'grant', '', ''),
(162, 'dbt_HistVKPreis_*', 'dbt', 'HistVKPreis.*', 'grant', '', ''),
(163, 'dbt_IS_Branch_*', 'dbt', 'IS_Branch.*', 'grant', '', ''),
(164, 'dbt_IS_Brand_*', 'dbt', 'IS_Brand.*', 'grant', '', ''),
(165, 'dbt_IS_ItemFacts_*', 'dbt', 'IS_ItemFacts.*', 'grant', '', ''),
(166, 'dbt_IS_ModSize_*', 'dbt', 'IS_ModSize.*', 'grant', '', ''),
(167, 'dbt_IS_Model_*', 'dbt', 'IS_Model.*', 'grant', '', ''),
(168, 'dbt_IS_ModelFacts_*', 'dbt', 'IS_ModelFacts.*', 'grant', '', ''),
(169, 'dbt_IS_SizeRange_*', 'dbt', 'IS_SizeRange.*', 'grant', '', ''),
(170, 'dbt_IS_Sizes_*', 'dbt', 'IS_Sizes.*', 'grant', '', ''),
(171, 'dbt_Inventory_*', 'dbt', 'Inventory.*', 'grant', '', ''),
(172, 'dbt_InventoryItem_*', 'dbt', 'InventoryItem.*', 'grant', '', ''),
(173, 'dbt_Jobs_*', 'dbt', 'Jobs.*', 'grant', '', ''),
(174, 'dbt_JournalTmpl_*', 'dbt', 'JournalTmpl.*', 'grant', '', ''),
(175, 'dbt_JournalTmplItem_*', 'dbt', 'JournalTmplItem.*', 'grant', '', ''),
(176, 'dbt_Letter_*', 'dbt', 'Letter.*', 'grant', '', ''),
(177, 'dbt_LiefListe_*', 'dbt', 'LiefListe.*', 'grant', '', ''),
(178, 'dbt_Mailing_*', 'dbt', 'Mailing.*', 'grant', '', ''),
(179, 'dbt_Market_*', 'dbt', 'Market.*', 'grant', '', ''),
(180, 'dbt_MarketCond_*', 'dbt', 'MarketCond.*', 'grant', '', ''),
(181, 'dbt_Menu_*', 'dbt', 'Menu.*', 'grant', '', ''),
(182, 'dbt_MenuItem_*', 'dbt', 'MenuItem.*', 'grant', '', ''),
(183, 'dbt_Merchant_*', 'dbt', 'Merchant.*', 'grant', '', ''),
(184, 'dbt_OrderXML_*', 'dbt', 'OrderXML.*', 'grant', '', ''),
(185, 'dbt_PSuOrdr_*', 'dbt', 'PSuOrdr.*', 'grant', '', ''),
(186, 'dbt_PSuOrdrItem_*', 'dbt', 'PSuOrdrItem.*', 'grant', '', ''),
(187, 'dbt_ProductGroup_*', 'dbt', 'def.ProductGroup.*', 'grant', '', ''),
(188, 'dbt_ProductGroupItem_*', 'dbt', 'def.ProductGroupItem.*', 'grant', '', ''),
(189, 'dbt_Proj_*', 'dbt', 'Proj.*', 'grant', '', ''),
(190, 'dbt_ProjPosten_*', 'dbt', 'ProjPosten.*', 'grant', '', ''),
(191, 'dbt_Screens_*', 'dbt', 'Screens.*', 'grant', '', ''),
(192, 'dbt_SerNo_*', 'dbt', 'SerNo.*', 'grant', '', ''),
(193, 'dbt_ShopSession_*', 'dbt', 'def.ShopSession.*', 'grant', '', ''),
(194, 'dbt_Stock_*', 'dbt', 'Stock.*', 'grant', '', ''),
(195, 'dbt_StockLocation_*', 'dbt', 'StockLocation.*', 'grant', '', ''),
(196, 'dbt_SunriseSunset_*', 'dbt', 'SunriseSunset.*', 'grant', '', ''),
(197, 'dbt_Supplier_*', 'dbt', 'Supplier.*', 'grant', '', ''),
(198, 'dbt_SupplierContact_*', 'dbt', 'SupplierContact.*', 'grant', '', ''),
(199, 'dbt_SupplierDelivery_*', 'dbt', 'SupplierDelivery.*', 'grant', '', ''),
(200, 'dbt_SupplierDeliveryItem_*', 'dbt', 'SupplierDeliveryItem.*', 'grant', '', ''),
(201, 'dbt_SupplierDiscount_*', 'dbt', 'SupplierDiscount.*', 'grant', '', ''),
(202, 'dbt_SupplierInvoice_*', 'dbt', 'SupplierInvoice.*', 'grant', '', ''),
(203, 'dbt_SupplierInvoiceItem_*', 'dbt', 'SupplierInvoiceItem.*', 'grant', '', ''),
(204, 'dbt_SupplierOrder_*', 'dbt', 'SupplierOrder.*', 'grant', '', ''),
(205, 'dbt_SupplierOrderItem_*', 'dbt', 'SupplierOrderItem.*', 'grant', '', ''),
(206, 'dbt_SysDataItemType_*', 'dbt', 'SysDataItemType.*', 'grant', '', ''),
(207, 'dbt_SysTexte_*', 'dbt', 'SysTexte.*', 'grant', '', ''),
(208, 'dbt_TLabel_*', 'dbt', 'TLabel.*', 'grant', '', ''),
(209, 'dbt_Task_*', 'dbt', 'Task.*', 'grant', '', ''),
(210, 'dbt_Tax_*', 'dbt', 'Tax.*', 'grant', '', ''),
(211, 'dbt_Texte_*', 'dbt', 'Texte.*', 'grant', '', ''),
(212, 'dbt_TmpLabels_*', 'dbt', 'TmpLabels.*', 'grant', '', ''),
(213, 'dbt_Trans_*', 'dbt', 'Trans.*', 'grant', '', ''),
(214, 'dbt_TravelExpense_*', 'dbt', 'TravelExpense.*', 'grant', '', ''),
(215, 'dbt_TravelExpenseItem_*', 'dbt', 'TravelExpenseItem.*', 'grant', '', ''),
(216, 'dbt_TravelExpenseType_*', 'dbt', 'TravelExpenseType.*', 'grant', '', ''),
(217, 'dbt_User_*', 'dbt', 'User.*', 'grant', '', ''),
(218, 'dbt_VKDaten_*', 'dbt', 'VKDaten.*', 'grant', '', ''),
(219, 'dbt_WebPage_*', 'dbt', 'def.WebPage.*', 'grant', '', ''),
(220, 'dbt_optiongroup_*', 'dbt', 'optiongroup.*', 'grant', '', ''),
(221, 'dbt_productgroupitemlist_*', 'dbt', 'def.ProductGroupItemList.*', 'grant', '', ''),
(222, 'dbt_suppliercontactsurvey_*', 'dbt', 'suppliercontactsurvey.*', 'grant', '', ''),
(223, 'dbt_v_addressaddresscontactsurvey_*', 'dbt', 'v_addressaddresscontactsurvey.*', 'grant', '', ''),
(224, 'dbt_v_addresscontactsurvey_*', 'dbt', 'v_addresscontactsurvey.*', 'grant', '', ''),
(225, 'dbt_v_articlepurchasepricerelsurvey_*', 'dbt', 'v_ArticlePurchasePriceRelSurvey.*', 'grant', '', ''),
(226, 'dbt_v_articlepurchasepricesurvey_*', 'dbt', 'v_ArticlePurchasePriceSurvey.*', 'grant', '', ''),
(227, 'dbt_v_articlesalespricecache_1_*', 'dbt', 'v_ArticleSalesPriceCache_1.*', 'grant', '', ''),
(228, 'dbt_v_articlesalespricecacheforshop_*', 'dbt', 'v_ArticleSalesPriceCacheForShop.*', 'grant', '', ''),
(229, 'dbt_v_articlesurvey_*', 'dbt', 'v_ArticleSurvey.*', 'grant', '', ''),
(230, 'dbt_v_customercartitemlist_*', 'dbt', 'v_customercartitemlist.*', 'grant', '', ''),
(231, 'dbt_v_customercartsurvey_*', 'dbt', 'v_customercartsurvey.*', 'grant', '', ''),
(232, 'dbt_v_customercommissionitemlist_*', 'dbt', 'v_customercommissionitemlist.*', 'grant', '', ''),
(233, 'dbt_v_customercommissionsurvey_*', 'dbt', 'v_customercommissionsurvey.*', 'grant', '', ''),
(234, 'dbt_v_customercontactsurvey_*', 'dbt', 'v_ProprietorContactSurvey.*', 'grant', '', ''),
(235, 'dbt_v_customercustomercontactsurvey_*', 'dbt', 'v_customercustomercontactsurvey.*', 'grant', '', ''),
(236, 'dbt_v_customerdeliveryitemlist_*', 'dbt', 'v_customerdeliveryitemlist.*', 'grant', '', ''),
(237, 'dbt_v_customerdeliverysurvey_*', 'dbt', 'v_customerdeliverysurvey.*', 'grant', '', ''),
(238, 'dbt_v_customerinvoiceitemlist_*', 'dbt', 'v_customerinvoiceitemlist.*', 'grant', '', ''),
(239, 'dbt_v_customerinvoicesurvey_*', 'dbt', 'v_customerinvoicesurvey.*', 'grant', '', ''),
(240, 'dbt_v_customerofferitemlist_*', 'dbt', 'v_customerofferitemlist.*', 'grant', '', ''),
(241, 'dbt_v_customeroffersurvey_*', 'dbt', 'v_customeroffersurvey.*', 'grant', '', ''),
(242, 'dbt_v_customerorderitemconsolidatedeliveries_*', 'dbt', 'v_customerorderitemconsolidatedeliveries.*', 'grant', '', ''),
(243, 'dbt_v_customerorderitemconsolidateinvoices_*', 'dbt', 'v_customerorderitemconsolidateinvoices.*', 'grant', '', ''),
(244, 'dbt_v_customerorderitemconsolidateorder_*', 'dbt', 'v_customerorderitemconsolidateorder.*', 'grant', '', ''),
(245, 'dbt_v_customerorderitemlist_*', 'dbt', 'v_customerorderitemlist.*', 'grant', '', ''),
(246, 'dbt_v_customerordersurvey_*', 'dbt', 'v_customerordersurvey.*', 'grant', '', ''),
(247, 'dbt_v_customerrfqitemlist_*', 'dbt', 'v_customerrfqitemlist.*', 'grant', '', ''),
(248, 'dbt_v_customerrfqsurvey_*', 'dbt', 'v_customerrfqsurvey.*', 'grant', '', ''),
(249, 'dbt_v_customertemporderitemlist_*', 'dbt', 'v_customertemporderitemlist.*', 'grant', '', ''),
(250, 'dbt_v_customertempordersurvey_*', 'dbt', 'v_customertempordersurvey.*', 'grant', '', ''),
(251, 'dbt_v_suppliercontactsurvey_*', 'dbt', 'v_SupplierContactSurvey.*', 'grant', '', ''),
(252, 'dbt_v_suppliersuppliercontactsurvey_*', 'dbt', 'v_suppliersuppliercontactsurvey.*', 'grant', '', ''),
(253, 'dbt_v_taxsurvey_*', 'dbt', 'v_taxsurvey.*', 'grant', '', ''),
(254, 'dbt_AppUserRoleProfileAuthObject_*', 'dbt', 'appSys.AppUserRoleProfileAuthObject.*', 'grant', '', ''),
(255, 'dbt_v_supplierdiscountsurvey_*', 'dbt', 'v_supplierdiscountsurvey.*', 'grant', '', ''),
(256, 'dbt_SysConfigObj_read', 'dbt', 'SysConfigObj.read', 'grant', '', ''),
(257, 'dbt_Proprietor_read', 'dbt', 'def.Proprietor.read', 'grant', '', ''),
(258, 'dbt_ProfileWithAuthObject_*', 'dbt', 'appSys.ProfileWithAuthObject.*', 'grant', '', ''),
(259, 'dbt_AppUserWithRole_*', 'dbt', 'appSys.AppUserWithRole.*', 'grant', '', ''),
(260, 'dbt_RoleWithProfile_*', 'dbt', 'appSys.RoleWithProfile.*', 'grant', '', ''),
(263, 'dbt_ProductGroup_read', 'dbt', 'def.ProductGroup.read', 'grant', '', ''),
(264, 'dbt_ArticleGroup_read', 'dbt', 'def.ArticleGroup.read', 'grant', '', ''),
(266, 'dbt_ProductGroupItem_read', 'dbt', 'def.ProductGroupItem.read', 'grant', '', ''),
(267, 'dbt_ArticleGroupItem_read', 'dbt', 'def.ArticleGroupItem.read', 'grant', '', ''),
(268, 'dbt_Article_read', 'dbt', 'def.Article.read', 'grant', '', ''),
(269, 'dbt_ui_AppTrans_read', 'dbt', 'ui.AppTrans.read', 'grant', '', ''),
(270, 'dbt_ui_AppTrans_write\r\n', 'dbt', 'ui.AppTrans.write', 'grant', '', ''),
(271, 'scr_ModPurchase', 'scr', 'ModPurchase', 'grant', '', ''),
(272, 'scr_ModPurchase_*', 'scr', 'ModPurchase.*', 'grant', '', ''),
(273, 'scr_ModSales', 'scr', 'ModSales', 'grant', '', ''),
(274, 'scr_ModSales_*', 'scr', 'ModSales.*', 'grant', '', ''),
(275, 'scr_ModSystem', 'scr', 'ModSystem\r\n', 'grant', '', ''),
(276, 'scr_ModSystem_*', 'scr', 'ModSystem.*', 'grant', '', ''),
(277, 'dbt_ui_AppTrans_store', 'dbt', 'ui.AppTrans.store', 'grant', '', ''),
(278, 'scr_ModMisc', 'scr', 'ModMisc', 'grant', '', ''),
(279, 'scr_ModMisc_*', 'scr', 'ModMisc.*', 'grant', '', ''),
(280, 'dbt_AppUser_read', 'dbt', 'appSys.AppUser.read', 'grant', '', ''),
(281, 'dbt_ArticleSalesPriceCache_read', 'dbt', 'def.ArticleSalesPriceCache.read', 'grant', '', ''),
(284, 'dbt_Tax_read', 'dbt', 'def.Tax.read', 'grant', '', ''),
(285, 'scr_ModUser', 'scr', 'ModUser', 'grant', '', ''),
(286, 'scr_ModUser_*', 'scr', 'ModUser.*', 'grant', '', ''),
(287, 'dbt_Property_*', 'dbt', 'def.Property.*', 'grant', '', ''),
(288, 'dbt_Property_read', 'dbt', 'def.Property.read', 'grant', '', ''),
(289, 'dbt_Property_write', 'dbt', 'def.Property.write', 'grant', '', ''),
(290, 'scr_ModOperations', 'scr', 'ModOperations', 'grant', '', ''),
(291, 'scr_ModStatements', 'scr', 'ModStatements', 'grant', '', ''),
(292, 'scr_ModOperations_*', 'scr', 'ModOperations.*', 'grant', '', ''),
(293, 'scr_ModStatements_*', 'scr', 'ModStatements.*', 'grant', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `Profile`
--

DROP TABLE IF EXISTS `Profile`;
CREATE TABLE IF NOT EXISTS `Profile` (
  `Id` int(11) NOT NULL,
  `ProfileId` varchar(32) NOT NULL,
  `Name` varchar(64) NOT NULL,
  `Description` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Profile`
--

INSERT INTO `Profile` (`Id`, `ProfileId`, `Name`, `Description`) VALUES
(1, 'sysOp', 'system operator', ''),
(2, 'sysAdmin', 'system operator', ''),
(3, 'admin', 'system operator', ''),
(4, '_basic', 'system operator', ''),
(5, '_basicERM', '_basicERM', ''),
(6, 'buyer', 'buyer', ''),
(7, 'seller', 'seller', ''),
(8, 'logistic', 'logistic', ''),
(9, 'erpMaster', 'erpMaster', 'can do everything'),
(10, 'shop', 'Profile: Shop User', ''),
(11, '_RoleAdmin', 'Profile: Role admin', ''),
(12, '_ProfileAdmin', 'Profile: Profile admin', ''),
(13, '__basic', 'basic Profile', 'every user shall have access to this Profile');

-- --------------------------------------------------------

--
-- Table structure for table `ProfileAuthObject`
--

DROP TABLE IF EXISTS `ProfileAuthObject`;
CREATE TABLE IF NOT EXISTS `ProfileAuthObject` (
  `Id` int(11) NOT NULL,
  `ProfileId` varchar(32) NOT NULL,
  `AuthObjectId` varchar(128) NOT NULL,
  `Active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=294 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ProfileAuthObject`
--

INSERT INTO `ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`) VALUES
(1, 'admin', 'fnc_AppUser_create', 1),
(2, 'admin', 'fnc_AppUser_update', 1),
(3, 'admin', 'fnc_Role_create', 1),
(4, 'admin', 'fnc_Role_update', 1),
(6, 'admin', 'dbt_AppUser_*', 1),
(7, 'admin', 'scr_ModAdmin_Client', 1),
(8, 'admin', 'dbt_AppUserRole_*', 1),
(9, 'admin', 'scr_ModAdmin', 1),
(10, 'admin', 'scr_ModAdmin_AppUser', 1),
(11, 'admin', 'scr_ModAdmin_Role', 1),
(12, '_basicERM', 'scr_ModBase', 1),
(13, '_basicERM', 'scr_ModBase_*', 1),
(14, 'admin', 'scr_ModAdmin_Profile', 1),
(15, 'admin', 'scr_ModAdmin_AuthObject', 1),
(16, 'admin', 'dbt_Role_*', 1),
(17, '__basic', 'scr_ModUser', 1),
(18, '__basic', 'scr_ModUser_*', 1),
(19, 'admin', 'dbt_AuthObject_*', 1),
(20, 'admin', 'dbt_ProfileAuthObject_*', 1),
(21, 'admin', 'fnc_Profile_*', 1),
(22, 'admin', 'dbt_Profile_*', 1),
(23, 'admin', 'fnc_AuthObject_*', 1),
(24, 'sysOp', 'scr_ModOperator_*', 1),
(25, 'sysOp', 'scr_ModOperator', 1),
(26, 'admin', 'scr_ModAdmin_AuthObject_gridAuthObjectOV', 1),
(27, 'admin', 'scr_ModAdmin_*', 1),
(28, 'admin', 'scr_ModAdmin_AppUser_*', 1),
(29, 'admin', 'scr_ModAdmin_Client_*', 1),
(30, 'admin', 'scr_ModAdmin_Role_*', 1),
(31, 'admin', 'scr_ModAdmin_Profile_hide_gridProfileAuthObjectsFNC', 1),
(32, 'admin', 'scr_ModAdmin_Profile_hide_gridRoleWithProfile', 1),
(33, 'admin', 'scr_ModAdmin_Profile_hide_tabPageProfileAuthObjectsFNC', 1),
(34, 'admin', 'scr_ModAdmin_Profile_hide_storeProfile', 1),
(35, 'admin', 'scr_ModAdmin_Profile_1', 1),
(36, 'admin', 'scr_ModAdmin_AppUser_MiscInfo', 1),
(37, 'admin', 'scr_ModAdmin_AppUser_Create', 1),
(38, 'admin', 'scr_ModAdmin_AppUser_Access', 1),
(39, 'admin', 'dbt_AuthObject_update_AuthObjectType', 1),
(40, 'admin', 'dbt_AppUser_update_*', 1),
(41, 'admin', 'dbv_AppUser_avoidFY', 1),
(42, 'admin', 'dbv_AppUser_Number_avoid1', 1),
(43, 'admin', 'dbt_*', 1),
(45, '__basic', 'dbt_AppTrans_read', 1),
(46, '__basic', 'dbt_AppTrans_store', 1),
(47, 'sysOp', 'dbt_ApplicationSystem_*', 1),
(48, 'sysOp', 'dbt_Application_*', 1),
(49, 'sysOp', 'dbt_Client_*', 1),
(50, 'sysOp', 'dbt_ClientApplication_*', 1),
(51, 'admin', 'dbt_AppConfigObj_*', 1),
(53, 'admin', 'dbt_RoleProfile_*', 1),
(54, 'admin', 'dbt_v_AppUserAuthObjectSurvey_*', 1),
(55, 'admin', 'dbt_v_AppUserRoleProfileAuthObjectSurvey_*', 1),
(56, 'admin', 'dbt_v_AppUserRoleSurvey_*', 1),
(57, 'admin', 'dbt_v_appuserwithRolesurvey_*', 1),
(58, 'admin', 'dbt_v_ProfileAuthObjectsurvey_*', 1),
(59, 'admin', 'dbt_v_ProfilewithAuthObjectsurvey_*', 1),
(60, 'admin', 'dbt_v_RoleProfilesurvey_*', 1),
(61, 'admin', 'dbt_v_RolewithProfilesurvey_*', 1),
(62, 'admin', 'dbt_v_tables_*', 1),
(63, 'admin', 'dbt_v_tableswithoutAuthObject_*', 1),
(64, '__basic', 'dbt_SysTrans_read', 1),
(65, 'admin', 'ModAdmin_AppTrans', 1),
(66, 'admin', 'scr_ModAdmin_AppTrans', 1),
(67, 'admin', 'scr_ModAdmin_AppTrans_*', 1),
(68, '_basicERM', 'dbt_Address_*', 1),
(69, '_basicERM', 'dbt_AddressContactSurvey_read', 1),
(70, '_basicERM', 'scr_editAddressContact', 1),
(71, '_basicERM', 'dbt_AddressContact_*', 1),
(72, '_basicERM', 'dbt_Proprietor_*', 1),
(73, '_basicERM', 'dbt_ProprietorContactSurvey_read', 1),
(75, '_basicERM', 'dbt_ProprietorContact_*', 1),
(76, 'buyer', 'dbt_Supplier_*', 1),
(77, 'buyer', 'dbt_SupplierContact_*', 0),
(78, 'buyer', 'dbt_SupplierOrder_*', 1),
(79, 'buyer', 'dbt_SupplierOrderItem_*', 1),
(80, 'buyer', 'dbt_v_suppliercontactsurvey_*', 1),
(81, 'buyer', 'dbt_SupplierDiscount_*', 1),
(82, 'sysAdmin', 'dbt_AppUserRoleProfileAuthObject_*', 1),
(83, 'erpMaster', 'dbt_Article_*', 1),
(84, 'erpMaster', 'dbt_AppUser_store', 1),
(85, 'erpMaster', 'dbt_Address_*', 0),
(86, 'erpMaster', 'dbt_AddressContact_*', 0),
(87, 'erpMaster', 'dbt_ArtQPC_*', 0),
(88, 'erpMaster', 'dbt_ArtStruct_*', 0),
(89, 'erpMaster', 'dbt_ArticleAvgNeed_*', 1),
(90, 'erpMaster', 'dbt_ArticleComponent_*', 1),
(91, 'erpMaster', 'dbt_ArticleGroup_*', 1),
(92, 'erpMaster', 'dbt_ArticleGroupItem_*', 1),
(93, 'erpMaster', 'dbt_ArticleImage_*', 1),
(94, 'erpMaster', 'dbt_ArticleProposal_*', 1),
(95, 'erpMaster', 'dbt_ArticlePurchasePrice_*', 1),
(96, 'erpMaster', 'dbt_ArticlePurchasePriceRel_*', 1),
(97, 'erpMaster', 'dbt_ArticleSalesPrice_*', 1),
(98, 'erpMaster', 'dbt_ArticleSalesPriceCache_*', 1),
(99, 'erpMaster', 'dbt_ArticleStock_*', 1),
(100, 'erpMaster', 'dbt_ArticleStockCorrection_*', 1),
(101, 'erpMaster', 'dbt_ArticleStockCorrectionItem_*', 1),
(102, 'erpMaster', 'dbt_ArticleStockPredict_*', 1),
(103, 'erpMaster', 'dbt_ArticleText_*', 1),
(104, 'erpMaster', 'dbt_ArticleTurnover_*', 1),
(105, 'erpMaster', 'dbt_Assembly_*', 1),
(106, 'erpMaster', 'dbt_AssemblyItem_*', 1),
(107, 'erpMaster', 'dbt_Attribute_*', 1),
(108, 'erpMaster', 'dbt_AttributeTemplate_*', 1),
(109, 'erpMaster', 'dbt_AttributeTemplateItem_*', 1),
(110, 'erpMaster', 'dbt_Authority_*', 1),
(111, 'erpMaster', 'dbt_BankAccount_*', 1),
(112, 'erpMaster', 'dbt_BankAccountTransaction_*', 1),
(113, 'erpMaster', 'dbt_Carrier_*', 1),
(114, 'erpMaster', 'dbt_CarrierOption_*', 1),
(115, 'erpMaster', 'dbt_CashInvoice_*', 1),
(116, 'erpMaster', 'dbt_CashInvoiceItem_*', 1),
(117, 'erpMaster', 'dbt_CatalogGroup_*', 1),
(118, 'erpMaster', 'dbt_CatalogGroupItem_*', 1),
(119, 'erpMaster', 'dbt_ChemicalInfo_*', 1),
(120, 'erpMaster', 'dbt_Client_*', 1),
(121, 'erpMaster', 'dbt_ClientContact_*', 1),
(122, 'erpMaster', 'dbt_ClientLog_*', 1),
(123, 'erpMaster', 'dbt_Colli_*', 1),
(124, 'erpMaster', 'dbt_ColliItem_*', 1),
(125, 'erpMaster', 'dbt_ColliItem_DPD_*', 1),
(126, 'erpMaster', 'dbt_CuCred_*', 1),
(127, 'erpMaster', 'dbt_CuCredItem_*', 1),
(128, 'erpMaster', 'dbt_CuRmnd_*', 1),
(129, 'erpMaster', 'dbt_CuTest_*', 1),
(130, 'erpMaster', 'dbt_CuTestItem_*', 1),
(131, 'erpMaster', 'dbt_Currency_*', 1),
(132, 'erpMaster', 'dbt_Proprietor_*', 1),
(133, 'erpMaster', 'dbt_ProprietorCart_*', 1),
(134, 'erpMaster', 'dbt_ProprietorCartItem_*', 1),
(135, 'erpMaster', 'dbt_ProprietorCommission_*', 1),
(136, 'erpMaster', 'dbt_ProprietorCommissionItem_*', 1),
(137, 'erpMaster', 'dbt_ProprietorContact_*', 1),
(138, 'erpMaster', 'dbt_ProprietorDelivery_*', 1),
(139, 'erpMaster', 'dbt_ProprietorDeliveryItem_*', 1),
(140, 'erpMaster', 'dbt_ProprietorDeliveryPackage_*', 1),
(141, 'erpMaster', 'dbt_ProprietorInvoice_*', 1),
(142, 'erpMaster', 'dbt_ProprietorInvoiceItem_*', 1),
(143, 'erpMaster', 'dbt_ProprietorOffer_*', 1),
(144, 'erpMaster', 'dbt_ProprietorOfferItem_*', 1),
(145, 'erpMaster', 'dbt_ProprietorOrder_*', 1),
(146, 'erpMaster', 'dbt_ProprietorOrderItem_*', 1),
(147, 'erpMaster', 'dbt_ProprietorRFQ_*', 1),
(148, 'erpMaster', 'dbt_ProprietorRFQItem_*', 1),
(149, 'erpMaster', 'dbt_ProprietorTempOrder_*', 1),
(150, 'erpMaster', 'dbt_ProprietorTempOrderItem_*', 1),
(151, 'erpMaster', 'dbt_Document_*', 1),
(152, 'erpMaster', 'dbt_EKDaten_*', 1),
(153, 'erpMaster', 'dbt_EKPreis_*', 1),
(154, 'erpMaster', 'dbt_Ebay_Item_*', 1),
(155, 'erpMaster', 'dbt_Ebay_Order_*', 1),
(156, 'erpMaster', 'dbt_Ebay_ShippingAddress_*', 1),
(157, 'erpMaster', 'dbt_Ebay_Transaction_*', 1),
(158, 'erpMaster', 'dbt_FaAccount_*', 1),
(159, 'erpMaster', 'dbt_FaJournal_*', 1),
(160, 'erpMaster', 'dbt_FaJournalLine_*', 1),
(161, 'erpMaster', 'dbt_FaJournalLineItem_*', 1),
(162, 'erpMaster', 'dbt_FaPeriod_*', 1),
(163, 'erpMaster', 'dbt_FiscalYear_*', 1),
(164, 'erpMaster', 'dbt_Group_*', 1),
(165, 'erpMaster', 'dbt_HistVKPreis_*', 1),
(166, 'erpMaster', 'dbt_IS_Branch_*', 1),
(167, 'erpMaster', 'dbt_IS_Brand_*', 1),
(168, 'erpMaster', 'dbt_IS_ItemFacts_*', 1),
(169, 'erpMaster', 'dbt_IS_ModSize_*', 1),
(170, 'erpMaster', 'dbt_IS_Model_*', 1),
(171, 'erpMaster', 'dbt_IS_ModelFacts_*', 1),
(172, 'erpMaster', 'dbt_IS_SizeRange_*', 1),
(173, 'erpMaster', 'dbt_IS_Sizes_*', 1),
(174, 'erpMaster', 'dbt_Inventory_*', 1),
(175, 'erpMaster', 'dbt_InventoryItem_*', 1),
(176, 'erpMaster', 'dbt_Jobs_*', 1),
(177, 'erpMaster', 'dbt_JournalTmpl_*', 1),
(178, 'erpMaster', 'dbt_JournalTmplItem_*', 1),
(179, 'erpMaster', 'dbt_Letter_*', 1),
(180, 'erpMaster', 'dbt_LiefListe_*', 1),
(181, 'erpMaster', 'dbt_Mailing_*', 1),
(182, 'erpMaster', 'dbt_Market_*', 1),
(183, 'erpMaster', 'dbt_MarketCond_*', 1),
(184, 'erpMaster', 'dbt_Menu_*', 1),
(185, 'erpMaster', 'dbt_MenuItem_*', 1),
(186, 'erpMaster', 'dbt_Merchant_*', 1),
(187, 'erpMaster', 'dbt_OrderXML_*', 1),
(188, 'erpMaster', 'dbt_PSuOrdr_*', 1),
(189, 'erpMaster', 'dbt_PSuOrdrItem_*', 1),
(190, 'erpMaster', 'dbt_ProductGroup_*', 1),
(191, 'erpMaster', 'dbt_ProductGroupItem_*', 1),
(192, 'erpMaster', 'dbt_Proj_*', 1),
(193, 'erpMaster', 'dbt_ProjPosten_*', 1),
(194, 'erpMaster', 'dbt_Screens_*', 1),
(195, 'erpMaster', 'dbt_SerNo_*', 1),
(196, 'erpMaster', 'dbt_ShopSession_*', 1),
(197, 'erpMaster', 'dbt_Stock_*', 1),
(198, 'erpMaster', 'dbt_StockLocation_*', 1),
(199, 'erpMaster', 'dbt_SunriseSunset_*', 1),
(200, 'erpMaster', 'dbt_Supplier_*', 1),
(201, 'erpMaster', 'dbt_SupplierContact_*', 1),
(202, 'erpMaster', 'dbt_SupplierDelivery_*', 1),
(203, 'erpMaster', 'dbt_SupplierDeliveryItem_*', 1),
(204, 'erpMaster', 'dbt_SupplierDiscount_*', 1),
(205, 'erpMaster', 'dbt_SupplierInvoice_*', 1),
(206, 'erpMaster', 'dbt_SupplierInvoiceItem_*', 1),
(207, 'erpMaster', 'dbt_SupplierOrder_*', 1),
(208, 'erpMaster', 'dbt_SupplierOrderItem_*', 1),
(209, 'erpMaster', 'dbt_SysDataItemType_*', 1),
(210, 'erpMaster', 'dbt_SysTexte_*', 1),
(211, 'erpMaster', 'dbt_TLabel_*', 1),
(212, 'erpMaster', 'dbt_Task_*', 1),
(213, 'erpMaster', 'dbt_Tax_*', 1),
(214, 'erpMaster', 'dbt_Texte_*', 1),
(215, 'erpMaster', 'dbt_TmpLabels_*', 1),
(216, 'erpMaster', 'dbt_Trans_*', 1),
(217, 'erpMaster', 'dbt_TravelExpense_*', 1),
(218, 'erpMaster', 'dbt_TravelExpenseItem_*', 1),
(219, 'erpMaster', 'dbt_TravelExpenseType_*', 1),
(220, 'erpMaster', 'dbt_User_*', 1),
(221, 'erpMaster', 'dbt_VKDaten_*', 1),
(222, 'erpMaster', 'dbt_WebPage_*', 1),
(223, 'erpMaster', 'dbt_optiongroup_*', 1),
(224, 'erpMaster', 'dbt_ProductGroupItemList_*', 1),
(225, 'erpMaster', 'dbt_suppliercontactsurvey_*', 1),
(226, 'erpMaster', 'dbt_v_addressaddresscontactsurvey_*', 1),
(227, 'erpMaster', 'dbt_v_addresscontactsurvey_*', 1),
(228, 'erpMaster', 'dbt_v_articlepurchasepricerelsurvey_*', 1),
(229, 'erpMaster', 'dbt_v_articlepurchasepricesurvey_*', 1),
(230, 'erpMaster', 'dbt_v_articlesalespricecache_1_*', 1),
(231, 'erpMaster', 'dbt_v_articlesalespricecacheforshop_*', 1),
(232, 'erpMaster', 'dbt_v_articlesurvey_*', 1),
(233, 'erpMaster', 'dbt_v_customercartitemlist_*', 1),
(234, 'erpMaster', 'dbt_v_customercartsurvey_*', 1),
(235, 'erpMaster', 'dbt_v_customercommissionitemlist_*', 1),
(236, 'erpMaster', 'dbt_v_customercommissionsurvey_*', 1),
(237, 'erpMaster', 'dbt_v_customercontactsurvey_*', 1),
(238, 'erpMaster', 'dbt_v_customercustomercontactsurvey_*', 1),
(239, 'erpMaster', 'dbt_v_customerdeliveryitemlist_*', 1),
(240, 'erpMaster', 'dbt_v_customerdeliverysurvey_*', 1),
(241, 'erpMaster', 'dbt_v_customerinvoiceitemlist_*', 1),
(242, 'erpMaster', 'dbt_v_customerinvoicesurvey_*', 1),
(243, 'erpMaster', 'dbt_v_customerofferitemlist_*', 1),
(244, 'erpMaster', 'dbt_v_customeroffersurvey_*', 1),
(245, 'erpMaster', 'dbt_v_customerorderitemconsolidatedeliveries_*', 1),
(246, 'erpMaster', 'dbt_v_customerorderitemconsolidateinvoices_*', 1),
(247, 'erpMaster', 'dbt_v_customerorderitemconsolidateorder_*', 1),
(248, 'erpMaster', 'dbt_v_customerorderitemlist_*', 1),
(249, 'erpMaster', 'dbt_v_customerordersurvey_*', 1),
(250, 'erpMaster', 'dbt_v_customerrfqitemlist_*', 1),
(251, 'erpMaster', 'dbt_v_customerrfqsurvey_*', 1),
(252, 'erpMaster', 'dbt_v_customertemporderitemlist_*', 1),
(253, 'erpMaster', 'dbt_v_customertempordersurvey_*', 1),
(254, 'erpMaster', 'dbt_v_suppliercontactsurvey_*', 1),
(255, 'erpMaster', 'dbt_v_supplierdiscountsurvey_*', 1),
(256, 'erpMaster', 'dbt_v_suppliersuppliercontactsurvey_*', 1),
(257, 'erpMaster', 'dbt_v_tables_*', 1),
(258, 'erpMaster', 'dbt_v_taxsurvey_*', 1),
(259, '__basic', 'dbt_SysConfigObj_read', 1),
(260, 'erpMaster', 'dbt_Proprietor_read', 1),
(261, '_RoleAdmin', 'dbt_AppUserWithRole_*', 1),
(262, '_ProfileAdmin', 'dbt_RoleWithProfile_*', 1),
(263, '_ProfileAdmin', 'dbt_ProfileWithAuthObject_*', 1),
(264, 'shop', 'dbt_ClientLog_*', 1),
(265, 'shop', 'dbt_WebPage_*', 1),
(266, 'shop', 'dbt_ProductGroup_read', 1),
(267, 'shop', 'dbt_ArticleGroup_read', 1),
(268, 'shop', 'dbt_ShopSession_*', 1),
(269, 'shop', 'dbt_ArticleGroupItem_read', 1),
(270, 'shop', 'dbt_ProductGroupItem_read', 1),
(271, 'shop', 'dbt_Proprietor_*', 1),
(272, 'shop', 'dbt_Article_read', 1),
(273, '__basic', 'dbt_ui_AppTrans_read', 1),
(274, '__basic', 'dbt_ui_AppTrans_store', 1),
(275, '_basicERM', 'scr_ModPurchase', 1),
(276, '_basicERM', 'scr_ModPurchase_*', 1),
(277, '_basicERM', 'scr_ModSales', 1),
(278, '_basicERM', 'scr_ModSales_*', 1),
(279, '_basicERM', 'scr_ModMisc', 1),
(280, '_basicERM', 'scr_ModMisc_*', 1),
(281, '_basicERM', 'scr_ModMisc', 1),
(282, 'erpMaster', 'dbt_AddressContactSurvey_read', 1),
(283, 'shop', 'dbt_ClientLog_store', 1),
(284, '__basic', 'dbt_AppTrans_read_UI\r\n', 1),
(285, 'shop', 'dbt_ProprietorCart_*', 1),
(286, 'shop', 'dbt_ArticleSalesPriceCache_read', 1),
(287, 'shop', 'dbt_ProprietorCartItem_*', 1),
(288, 'shop', 'dbt_Tax_read', 1),
(289, 'shop', 'dbt_ProprietorContact_*', 1),
(290, '_basic', 'scr_ModUser', 1),
(291, 'erpMaster', 'dbt_Property_*', 1),
(292, '_basicERM', 'scr_ModOperations', 1),
(293, '_basicERM', 'scr_ModStatements', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Role`
--

DROP TABLE IF EXISTS `Role`;
CREATE TABLE IF NOT EXISTS `Role` (
  `Id` int(11) NOT NULL,
  `RoleId` varchar(32) NOT NULL,
  `Name` varchar(64) NOT NULL,
  `Description` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Role`
--

INSERT INTO `Role` (`Id`, `RoleId`, `Name`, `Description`) VALUES
(1, 'sysOp', 'Role: Administrator', 'This is the basic Role which all administrators should have.'),
(2, 'sysAdmin', 'Role: Administrator', 'This Role shall be provided only to those people who can translate the labels of user input elements into their native language.'),
(3, 'admin', 'Role: Tester', 'sfdsafdfsd f sds'),
(4, '__basic', '__basic', 'This Role shall be provided only to those people who can translate the labels of user input elements into their native language.'),
(5, '_basicERM', '_basicERM', 'Can read/write:\nAddresses\nCustomers\nSuppliers'),
(6, 'buyer', 'buyer', 'This Role shall be provided only to those people who can translate the labels of user input elements into their native language.'),
(7, 'seller', 'seller', 'This Role shall be provided only to those people who can translate the labels of user input elements into their native language.'),
(8, 'logistic', 'logistic', 'This Role shall be provided only to those people who can translate the labels of user input elements into their native language.'),
(9, 'erpMaster', 'Role: erp Master User', 'Can do everything'),
(10, 'shop', 'Role: shop User', 'Can do everything'),
(11, '_RoleAdmin', 'Role: _RoleAdmin', 'Can administer Roles.'),
(12, '_ProfileAdmin', 'Role: _ProfileAdmin', 'Can administer Profiles.'),
(13, '_userAdmin', 'Role: _userAdmin', 'Can administer Profiles.');

-- --------------------------------------------------------

--
-- Table structure for table `RoleProfile`
--

DROP TABLE IF EXISTS `RoleProfile`;
CREATE TABLE IF NOT EXISTS `RoleProfile` (
  `Id` int(10) NOT NULL,
  `RoleId` varchar(32) NOT NULL,
  `ProfileId` varchar(32) NOT NULL,
  `Active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `RoleProfile`
--

INSERT INTO `RoleProfile` (`Id`, `RoleId`, `ProfileId`, `Active`) VALUES
(3, 'admin', 'admin', 1),
(4, 'sysOp', 'sysOp', 1),
(5, 'sysAdmin', 'sysAdmin', 1),
(6, 'sysAdmin', 'admin', 1),
(7, '__basic', '__basic', 1),
(8, '_basicERM', '_basicERM', 1),
(9, 'buyer', 'buyer', 1),
(10, 'erpMaster', 'erpMaster', 1),
(11, 'shop', 'shop', 1),
(12, '_RoleAdmin', '_RoleAdmin', 1),
(13, '_ProfileAdmin', '_ProfileAdmin', 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_AppUserAuthObjectSurvey`
--
DROP VIEW IF EXISTS `v_AppUserAuthObjectSurvey`;
CREATE TABLE IF NOT EXISTS `v_AppUserAuthObjectSurvey` (
`UserId` varchar(16)
,`RPId` int(10)
,`RoleId` varchar(32)
,`PAOId` int(11)
,`ProfileId` varchar(32)
,`Id` int(11)
,`AuthObjectId` varchar(128)
,`AuthObjectType` varchar(8)
,`ObjectName` varchar(128)
,`ObjectAttribute` varchar(128)
,`AttrValue` varchar(64)
,`Description` varchar(256)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_AppUserRoleProfileAuthObjectSurvey`
--
DROP VIEW IF EXISTS `v_AppUserRoleProfileAuthObjectSurvey`;
CREATE TABLE IF NOT EXISTS `v_AppUserRoleProfileAuthObjectSurvey` (
`Id` int(11)
,`AUR_RoleId` varchar(32)
,`RP_ProfileId` varchar(32)
,`UserId` varchar(32)
,`AuthObjectId` varchar(128)
,`AuthObjectType` varchar(8)
,`ObjectName` varchar(128)
,`ObjectAttribute` varchar(128)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_AppUserRoleSurvey`
--
DROP VIEW IF EXISTS `v_AppUserRoleSurvey`;
CREATE TABLE IF NOT EXISTS `v_AppUserRoleSurvey` (
`Id` int(10)
,`UserId` varchar(32)
,`RoleId` varchar(32)
,`Name` varchar(64)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_AppUserWithRoleSurvey`
--
DROP VIEW IF EXISTS `v_AppUserWithRoleSurvey`;
CREATE TABLE IF NOT EXISTS `v_AppUserWithRoleSurvey` (
`Id` int(10)
,`RoleId` varchar(32)
,`UserId` varchar(32)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_ProfileAuthObjectSurvey`
--
DROP VIEW IF EXISTS `v_ProfileAuthObjectSurvey`;
CREATE TABLE IF NOT EXISTS `v_ProfileAuthObjectSurvey` (
`Id` int(11)
,`ProfileId` varchar(32)
,`AuthObjectId` varchar(128)
,`AuthObjectType` varchar(8)
,`ObjectName` varchar(128)
,`ObjectAttribute` varchar(128)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_ProfileWithAuthObjectSurvey`
--
DROP VIEW IF EXISTS `v_ProfileWithAuthObjectSurvey`;
CREATE TABLE IF NOT EXISTS `v_ProfileWithAuthObjectSurvey` (
`Id` int(11)
,`AuthObjectId` varchar(128)
,`ProfileId` varchar(32)
,`Name` varchar(64)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_RoleProfileSurvey`
--
DROP VIEW IF EXISTS `v_RoleProfileSurvey`;
CREATE TABLE IF NOT EXISTS `v_RoleProfileSurvey` (
`Id` int(10)
,`RoleId` varchar(32)
,`ProfileId` varchar(32)
,`Name` varchar(64)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_RoleWithProfileSurvey`
--
DROP VIEW IF EXISTS `v_RoleWithProfileSurvey`;
CREATE TABLE IF NOT EXISTS `v_RoleWithProfileSurvey` (
`Id` int(11)
,`ProfileId` varchar(32)
,`RoleId` varchar(32)
,`Name` varchar(64)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_Tables`
--
DROP VIEW IF EXISTS `v_Tables`;
CREATE TABLE IF NOT EXISTS `v_Tables` (
`TableName` varchar(64)
,`DataBaseName` varchar(64)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_TablesWithoutAuthObject`
--
DROP VIEW IF EXISTS `v_TablesWithoutAuthObject`;
CREATE TABLE IF NOT EXISTS `v_TablesWithoutAuthObject` (
`TABLE_NAME` varchar(64)
);

-- --------------------------------------------------------

--
-- Structure for view `v_AppUserAuthObjectSurvey`
--
DROP TABLE IF EXISTS `v_AppUserAuthObjectSurvey`;

CREATE VIEW `v_AppUserAuthObjectSurvey` AS select `AU`.`UserId` AS `UserId`,`RP`.`Id` AS `RPId`,`RP`.`RoleId` AS `RoleId`,`PAO`.`Id` AS `PAOId`,`PAO`.`ProfileId` AS `ProfileId`,`AO`.`Id` AS `Id`,`AO`.`AuthObjectId` AS `AuthObjectId`,`AO`.`AuthObjectType` AS `AuthObjectType`,`AO`.`ObjectName` AS `ObjectName`,`AO`.`ObjectAttribute` AS `ObjectAttribute`,`AO`.`AttrValue` AS `AttrValue`,`AO`.`Description` AS `Description` from ((((`AppUser` `AU` left join `AppUserRole` `AUR` on((`AUR`.`UserId` = `AU`.`UserId`))) left join `RoleProfile` `RP` on((`RP`.`RoleId` = `AUR`.`RoleId`))) left join `ProfileAuthObject` `PAO` on((`PAO`.`ProfileId` = `RP`.`RoleId`))) left join `AuthObject` `AO` on((`AO`.`AuthObjectId` = `PAO`.`AuthObjectId`))) order by `AO`.`AuthObjectId`;

-- --------------------------------------------------------

--
-- Structure for view `v_AppUserRoleProfileAuthObjectSurvey`
--
DROP TABLE IF EXISTS `v_AppUserRoleProfileAuthObjectSurvey`;

CREATE VIEW `v_AppUserRoleProfileAuthObjectSurvey` AS select `AO`.`Id` AS `Id`,`AUR`.`RoleId` AS `AUR_RoleId`,`RP`.`ProfileId` AS `RP_ProfileId`,`AUR`.`UserId` AS `UserId`,`AO`.`AuthObjectId` AS `AuthObjectId`,`AO`.`AuthObjectType` AS `AuthObjectType`,`AO`.`ObjectName` AS `ObjectName`,`AO`.`ObjectAttribute` AS `ObjectAttribute` from (((`AppUserRole` `AUR` left join `RoleProfile` `RP` on((`RP`.`RoleId` = `AUR`.`RoleId`))) left join `ProfileAuthObject` `PAO` on((`PAO`.`ProfileId` = `RP`.`ProfileId`))) left join `AuthObject` `AO` on((`AO`.`AuthObjectId` = `PAO`.`AuthObjectId`))) order by `AO`.`AuthObjectType`,`AO`.`ObjectName`;

-- --------------------------------------------------------

--
-- Structure for view `v_AppUserRoleSurvey`
--
DROP TABLE IF EXISTS `v_AppUserRoleSurvey`;

CREATE VIEW `v_AppUserRoleSurvey` AS select `AUR`.`Id` AS `Id`,`AUR`.`UserId` AS `UserId`,`AUR`.`RoleId` AS `RoleId`,`R`.`Name` AS `Name` from (`AppUserRole` `AUR` left join `Role` `R` on((`R`.`RoleId` = `AUR`.`RoleId`))) order by `AUR`.`RoleId`;

-- --------------------------------------------------------

--
-- Structure for view `v_AppUserWithRoleSurvey`
--
DROP TABLE IF EXISTS `v_AppUserWithRoleSurvey`;

CREATE VIEW `v_AppUserWithRoleSurvey` AS select `AUR`.`Id` AS `Id`,`R`.`RoleId` AS `RoleId`,`AUR`.`UserId` AS `UserId` from (`AppUserRole` `AUR` left join `Role` `R` on((`R`.`RoleId` = `AUR`.`RoleId`))) order by `AUR`.`UserId`;

-- --------------------------------------------------------

--
-- Structure for view `v_ProfileAuthObjectSurvey`
--
DROP TABLE IF EXISTS `v_ProfileAuthObjectSurvey`;

CREATE VIEW `v_ProfileAuthObjectSurvey` AS select `PAO`.`Id` AS `Id`,`PAO`.`ProfileId` AS `ProfileId`,`PAO`.`AuthObjectId` AS `AuthObjectId`,`AO`.`AuthObjectType` AS `AuthObjectType`,`AO`.`ObjectName` AS `ObjectName`,`AO`.`ObjectAttribute` AS `ObjectAttribute` from (`ProfileAuthObject` `PAO` left join `AuthObject` `AO` on((`AO`.`AuthObjectId` = `PAO`.`AuthObjectId`))) order by `AO`.`ObjectName`;

-- --------------------------------------------------------

--
-- Structure for view `v_ProfileWithAuthObjectSurvey`
--
DROP TABLE IF EXISTS `v_ProfileWithAuthObjectSurvey`;

CREATE VIEW `v_ProfileWithAuthObjectSurvey` AS select `P`.`Id` AS `Id`,`PAO`.`AuthObjectId` AS `AuthObjectId`,`P`.`ProfileId` AS `ProfileId`,`P`.`Name` AS `Name` from (`ProfileAuthObject` `PAO` left join `Profile` `P` on((`P`.`ProfileId` = `PAO`.`ProfileId`))) order by `P`.`ProfileId`;

-- --------------------------------------------------------

--
-- Structure for view `v_RoleProfileSurvey`
--
DROP TABLE IF EXISTS `v_RoleProfileSurvey`;

CREATE VIEW `v_RoleProfileSurvey` AS select `RP`.`Id` AS `Id`,`RP`.`RoleId` AS `RoleId`,`RP`.`ProfileId` AS `ProfileId`,`P`.`Name` AS `Name` from (`RoleProfile` `RP` left join `Profile` `P` on((`P`.`ProfileId` = `RP`.`ProfileId`))) order by `RP`.`RoleId`;

-- --------------------------------------------------------

--
-- Structure for view `v_RoleWithProfileSurvey`
--
DROP TABLE IF EXISTS `v_RoleWithProfileSurvey`;

CREATE VIEW `v_RoleWithProfileSurvey` AS select `R`.`Id` AS `Id`,`RP`.`ProfileId` AS `ProfileId`,`R`.`RoleId` AS `RoleId`,`R`.`Name` AS `Name` from (`RoleProfile` `RP` left join `Role` `R` on((`R`.`RoleId` = `RP`.`RoleId`))) order by `RP`.`RoleId`;

-- --------------------------------------------------------

--
-- Structure for view `v_Tables`
--
DROP TABLE IF EXISTS `v_Tables`;

CREATE VIEW `v_Tables` AS select `information_schema`.`tables`.`TABLE_NAME` AS `TableName`,`information_schema`.`tables`.`TABLE_SCHEMA` AS `DataBaseName` from `information_schema`.`tables`;

-- --------------------------------------------------------

--
-- Structure for view `v_TablesWithoutAuthObject`
--
DROP TABLE IF EXISTS `v_TablesWithoutAuthObject`;

CREATE VIEW `v_TablesWithoutAuthObject` AS select `information_schema`.`tables`.`TABLE_NAME` AS `TABLE_NAME` from `information_schema`.`tables` where ((`information_schema`.`tables`.`TABLE_SCHEMA` = 'mas_immo_1a2b3c4d_sys') and (not(exists(select 1 from `mas_immo_1a2b3c4d_sys`.`AuthObject` where (convert(`mas_immo_1a2b3c4d_sys`.`AuthObject`.`AuthObjectId` using utf8) = concat('dbt_',`information_schema`.`tables`.`TABLE_NAME`,'_*'))))));

--
-- Indexes for dumped tables
--

--
-- Indexes for table `AppConfigObj`
--
ALTER TABLE `AppConfigObj`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `AppOption`
--
ALTER TABLE `AppOption`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `AppTrans`
--
ALTER TABLE `AppTrans`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `AppUser`
--
ALTER TABLE `AppUser`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `UserId` (`UserId`);

--
-- Indexes for table `AppUserRole`
--
ALTER TABLE `AppUserRole`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `AuthObject`
--
ALTER TABLE `AuthObject`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `AuthObjectId` (`AuthObjectId`);

--
-- Indexes for table `Profile`
--
ALTER TABLE `Profile`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `ProfileId` (`ProfileId`);

--
-- Indexes for table `ProfileAuthObject`
--
ALTER TABLE `ProfileAuthObject`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `Role`
--
ALTER TABLE `Role`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `RoleId` (`RoleId`);

--
-- Indexes for table `RoleProfile`
--
ALTER TABLE `RoleProfile`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `AppConfigObj`
--
ALTER TABLE `AppConfigObj`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=120;
--
-- AUTO_INCREMENT for table `AppOption`
--
ALTER TABLE `AppOption`
  MODIFY `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=199;
--
-- AUTO_INCREMENT for table `AppTrans`
--
ALTER TABLE `AppTrans`
  MODIFY `Id` int(8) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `AppUser`
--
ALTER TABLE `AppUser`
  MODIFY `Id` int(8) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `AppUserRole`
--
ALTER TABLE `AppUserRole`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `AuthObject`
--
ALTER TABLE `AuthObject`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=294;
--
-- AUTO_INCREMENT for table `Profile`
--
ALTER TABLE `Profile`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `ProfileAuthObject`
--
ALTER TABLE `ProfileAuthObject`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=294;
--
-- AUTO_INCREMENT for table `Role`
--
ALTER TABLE `Role`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `RoleProfile`
--
ALTER TABLE `RoleProfile`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
