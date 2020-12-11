/**
 *	20170303	khw	Erste Unbennung kunden -> Kunde
 */

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`Firma`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`Firma` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`Name1` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Name2` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Name3` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Strasse` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Hausnummer` varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`PLZ` varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Ort` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Telefon` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Fax` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`IKNr` varchar(9) COLLATE latin1_general_ci NOT NULL,
  		`DatumAnlage` date NULL,
		`DatumAenderung` date NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`Firma`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_FirmaSurvey` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_FirmaSurvey` AS
	SELECT F.Id, F.ERPNr, CONCAT( F.Name1, " ", F.Name2) AS Name, F.PLZ, F.Ort
		FROM `mas_sani_1a2b3c4d`.`Firma` AS F
	;

INSERT INTO `Firma` (`Id`, `ERPNr`, `Name1`, `Name2`, `Name3`, `Strasse`, `Hausnummer`, `PLZ`, `Ort`, `Telefon`, `Fax`, `IKNr`, `DatumAnlage`, `DatumAenderung`, `Remark`, `LockState`) VALUES
(1, '999999000001', 'Sanitätshaus', 'Karl-Heinz Welter', '', 'Musterstrasse', '4712', '12345', 'Musterstadt', '01234 / 123456-0', '01234 / 123456-99', '330330330', '2017-11-02', '2017-11-02', NULL, 0);

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`Filiale`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`Filiale` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`FirmaERPNr` varchar(16) COLLATE latin1_general_ci,
		`Name1` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Name2` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Name3` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Strasse` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Hausnummer` varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`PLZ` varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Ort` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Telefon` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Fax` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`IKNr` varchar(9) COLLATE latin1_general_ci NOT NULL,
  		`DatumAnlage` date NULL,
		`DatumAenderung` date NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`Filiale`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_FilialeSurvey` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_FilialeSurvey` AS
	SELECT F.Id, F.ERPNr, F.FirmaERPNr, CONCAT( F.Name1, " ", F.Name2) AS Name, F.PLZ, F.Ort
		FROM `mas_sani_1a2b3c4d`.`Filiale` AS F
	;

INSERT INTO `Filiale` (`Id`, `ERPNr`, `FirmaERPNr`, `Name1`, `Name2`, `Name3`, `Strasse`, `Hausnummer`, `PLZ`, `Ort`, `Telefon`, `Fax`, `IKNr`, `DatumAnlage`, `DatumAenderung`, `Remark`, `LockState`) VALUES
(1, '999999010001', '999999000001', 'Sanitätshaus', 'Karl-Heinz Welter', '', 'Musterstrasse', '4712', '12345', 'Musterstadt', '01234 / 123456-0', '01234 / 123456-99', '330330330', '2017-11-02', '2017-11-02', NULL, 0);

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`Kasse`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`Kasse` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`FilialeERPNr` varchar(16) COLLATE latin1_general_ci,
		`Bezeichnung1` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Bezeichnung2` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  		`DatumAnlage` date NULL,
		`DatumAenderung` date NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`Kasse`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_KasseSurvey` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_KasseSurvey` AS
	SELECT K.Id, K.ERPNr, K.FilialeERPNr, K.Bezeichnung1 AS Bezeichnung
		FROM `mas_sani_1a2b3c4d`.`Kasse` AS K
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`Artikel`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`Artikel` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`ArtikelNr` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Bezeichnung1` varchar(255) COLLATE latin1_general_ci NOT NULL,
		`Bezeichnung2` varchar(255) COLLATE latin1_general_ci NOT NULL,
		`Bezeichnung3` varchar(255) COLLATE latin1_general_ci NOT NULL,
		`HMVNr` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`MwstTyp` int(1),
		`MwstSatz` decimal( 3,1),
		`Source` int(1),
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`Artikel`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_ArtikelSurvey` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_ArtikelSurvey` AS
	SELECT A.Id, A.ERPNr, A.ArtikelNr, CONCAT( A.Bezeichnung1, " ", A.Bezeichnung2) AS Bezeichnung, A.Bezeichnung1, A.Bezeichnung2, A.HMVNr
		FROM `mas_sani_1a2b3c4d`.`Artikel` AS A
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`Arzt`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`Arzt` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`ArztNr` varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Vorname` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Name1` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Name2` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Strasse` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Hausnummer` varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`PLZ` varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Ort` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Telefon` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Fax` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`BetriebsstaetteNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`IKNr` varchar(9) COLLATE latin1_general_ci NOT NULL,
  		`DatumAnlage` date NULL,
		`DatumAenderung` date NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`Arzt`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_ArztSurvey` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_ArztSurvey` AS
	SELECT A.Id, A.ERPNr, A.ArztNr, CONCAT( A.Name1, ", ", A.Vorname) AS Name, A.PLZ
		FROM `mas_sani_1a2b3c4d`.`Arzt` AS A
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`Kunde`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`Kunde` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`KundeNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`Vorname` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Name1` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Name2` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Strasse` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Hausnummer` varchar(8) COLLATE latin1_general_ci NOT NULL,
		`PLZ` varchar(8) COLLATE latin1_general_ci NOT NULL,
		`Ort` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Land` varchar(3) COLLATE latin1_general_ci NOT NULL,
		`Telefon` varchar(64) COLLATE latin1_general_ci NULL,
		`Fax` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '12345',
		`AdressTyp` int(1) NOT NULL DEFAULT '1',
		`AdressArt` int(1) NOT NULL DEFAULT '1',
		`Vers1IKNr` varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Vers1KVNr` varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Vers2IKNr` varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Vers2KVNr` varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Vers3IKNr` varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Vers3KVNr` varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Arzt1Nr`  varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Arzt2Nr`  varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Arzt3Nr`  varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
		`Pflegegrad` int(1),
		`DatumGeburt` date NULL,
		`DatumVerstorben` date NULL,
  		`DatumAnlage` date NULL,
		`DatumAenderung` date NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`Kunde`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_KundeSurvey` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_KundeSurvey` AS
	SELECT K.Id, K.ERPNr, K.KundeNr, CONCAT( K.Name1, ", ", K.Vorname) AS Name, K.PLZ
		FROM `mas_sani_1a2b3c4d`.`Kunde` AS K
	;

/**
 *
 */

DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`KundeAdresse`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`KundeAdresse` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`KundeNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`KundeAdresseNr` varchar(8) COLLATE latin1_general_ci NOT NULL,
		`Vorname` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Name1` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Name2` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Strasse` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Hausnummer` varchar(8) COLLATE latin1_general_ci NOT NULL,
		`PLZ` varchar(8) COLLATE latin1_general_ci NOT NULL,
		`Ort` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Telefon` varchar(64) COLLATE latin1_general_ci NULL,
		`Fax` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '12345',
		`AdressTyp` int(1) NOT NULL DEFAULT '1',
		`AdressArt` int(1) NOT NULL DEFAULT '1',
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
  		`DatumAnlage` date NULL,
		`DatumAenderung` date NULL,
  		`DatumGueltigVon` date NULL,
		`DatumGueltigBis` date NULL,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`KundeAdresse`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_KundeAdresseSurvey` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_KundeAdresseSurvey` AS
	SELECT KA.Id, KA.ERPNr, KA.KundeNr, KA.KundeAdresseNr, CONCAT( KA.Name1, ", ", KA.Vorname) AS Name, KA.PLZ, KA.Ort, KA.AdressTyp, KA.AdressArt
		FROM `mas_sani_1a2b3c4d`.`KundeAdresse` AS KA
	;

/**
 *
 */

DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`KundeBefreiung`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`KundeBefreiung` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
  		`DatumBefreiungVon` date NULL,
  		`DatumBefreiungBis` date NULL,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`KundeBefreiung`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_KundeBefreiungSurvey` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_KundeBefreiungSurvey` AS
	SELECT KB.Id, KB.ERPNr, KB.DatumBefreiungVon, KB.DatumBefreiungBis
		FROM `mas_sani_1a2b3c4d`.`Kunde` AS K
		LEFT JOIN `mas_sani_1a2b3c4d`.`KundeBefreiung` AS KB on KB.ERPNr = K.ERPNr
	;

/**
 *
 */

DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`KundeNotiz`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`KundeNotiz` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`KundeNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`KundeNotizNr` varchar(8) COLLATE latin1_general_ci NOT NULL,
		`_KundeId`int(11),
		`NotizTyp` int(2),
		`NotizTypText` varchar(32),
		`Hinweis` int(1),
		`Notiz` text COLLATE latin1_general_ci,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`KundeNotiz`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_KundeNotizSurvey` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_KundeNotizSurvey` AS
	SELECT KN.Id, KN.ERPNr, KN.KundeNr, KN.KundeNotizNr, SUBSTRING( KN.Notiz, 1, 32) AS Notiz
		FROM `mas_sani_1a2b3c4d`.`Kunde` AS K
		LEFT JOIN `mas_sani_1a2b3c4d`.`KundeNotiz` AS KN on KN.ERPNr = K.ERPNr
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`KundeAuftrag`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`KundeAuftrag` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`AuftragNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`Datum` date,
		`KundeNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`_KundeId`int(11),
		`Filiale` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`IKNr_LE` varchar(16) COLLATE latin1_general_ci DEFAULT '',
		`IKNr_KK` varchar(16) COLLATE latin1_general_ci DEFAULT '',
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`KundeAuftrag`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_KundeAuftragSurvey` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_KundeAuftragSurvey` AS
	SELECT KA.Id, KA.ERPNr, KA.AuftragNr, KA.Filiale, KA.IKNr_KK
		FROM `mas_sani_1a2b3c4d`.`KundeAuftrag` AS KA
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`KundeAuftragPosition`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`KundeAuftragPosition` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`AuftragNr` varchar(16) COLLATE latin1_general_ci NULL,
		`PosNr` int(11),
		`UPosNr` int(11),
		`ArtikelNr` varchar(32) COLLATE latin1_general_ci NOT NULL,
		`HMVNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`LKZ` varchar(2) COLLATE latin1_general_ci DEFAULT '00',
		`PZN` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`Bez1` varchar(255) COLLATE latin1_general_ci,
		`Bez2` varchar(255) COLLATE latin1_general_ci,
		`Bez3` varchar(255) COLLATE latin1_general_ci,
		`Menge` int(11) DEFAULT '0',
		`MengenEinheit` int(11) DEFAULT '0',
		`MwstSatz` int(1) DEFAULT '0',
		`PreisEK` dec(11,2) DEFAULT '0.00'					COMMENT 'Einkaufspreis',
		`PreisDb` dec(11,2) DEFAULT '0.00'					COMMENT '',
		`PreisUVP` dec(11,2) DEFAULT '0.00'					COMMENT 'Preis Hersteller UVP',
		`AbschlagProz` dec(11,2) DEFAULT '0.00'				COMMENT 'Abschlag prozentual für Kalkulation',
		`AbschlagAbs` dec(11,2) DEFAULT '0.00'				COMMENT 'Abschlag absolut für Kalkulation',
		`AbgabePreisNetto` dec(11,2) DEFAULT '0.00'			COMMENT 'Abgabepreis netto',
		`AbgabePreisBrutto` dec(11,2) DEFAULT '0.00'		COMMENT 'Abgabepreis brutto',
		`AbgabePreisMwst` dec(11,2) DEFAULT '0.00'			COMMENT 'Abgabepreis Mwst.',
		`KTAnteilNetto` dec(11,2) DEFAULT '0.00'			COMMENT 'KT Anteil netto',
		`KTAnteilBrutto` dec(11,2) DEFAULT '0.00'			COMMENT 'KT Anteil brutto',
		`Zuzahlung` dec(11,2) DEFAULT '0.00',
		`Eigenanteil` dec(11,2) DEFAULT '0.00',
		`Zulage` dec(11,2) DEFAULT '0.00',
		`GesamtAbgabePreisNetto` dec(11,2) DEFAULT '0.00',
		`GesamtAbgabePreisBrutto` dec(11,2) DEFAULT '0.00',
		`GesamtZulage` dec(11,2) DEFAULT '0.00',
		`GesamtEigenanteil` dec(11,2) DEFAULT '0.00',
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`KundeAuftragPosition`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_KundeAuftragPositionList` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_KundeAuftragPositionList` AS
	SELECT KAP.Id, KAP.ERPNr, KAP.AuftragNr, KAP.PosNr, KAP.UPosNr,
			KAP.ArtikelNr, KAP.HMVNr, KAP.LKZ, KAP.PZN, KAP.Bez1,
			KAP.Menge, KAP.AbgabePreisBrutto, KAP.KTAnteilBrutto,
			KAP.Zuzahlung, KAP.Eigenanteil, KAP.Zulage
		FROM `mas_sani_1a2b3c4d`.`KundeAuftragPosition` AS KAP
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`KundeAuftragObjekt`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`KundeAuftragObjekt` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`AuftragNr` varchar(16) COLLATE latin1_general_ci NULL,
		`_AuftragId` int(11),
		`Datum` date,
		`ObjektTyp` varchar(8) COLLATE latin1_general_ci NOT NULL,
		`ObjektId` int(11),
		`Datei` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Deleted` int(1),
		`Storno` int(1),
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`KundeAuftragObjekt`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_KundeAuftragObjektList` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_KundeAuftragObjektList` AS
	SELECT KAO.Id, KAO.AuftragNr, KAO.Datum, KAO.ObjektTyp, KAO.Datei
		FROM `mas_sani_1a2b3c4d`.`KundeAuftragObjekt` AS KAO
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`KundeRechnung`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`KundeRechnung` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`RechnungNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`Datum` date,
		`AuftragNr` varchar(16) COLLATE latin1_general_ci,
		`KundeNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`_KundeId`int(11),
		`Filiale` varchar(16) COLLATE latin1_general_ci,
		`IKNr_LE` varchar(16) COLLATE latin1_general_ci NULL DEFAULT '',
		`IKNr_KK` varchar(16) COLLATE latin1_general_ci NULL DEFAULT '',
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`KundeRechnung`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_KundeRechnungSurvey` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_KundeRechnungSurvey` AS
	SELECT CI.Id, CI.RechnungNr, CI.AuftragNr, CI.KundeNr
		FROM `mas_sani_1a2b3c4d`.`KundeRechnung` AS CI
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`KundeRechnungPosition`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`KundeRechnungPosition` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`RechnungNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`_RechnungId` int(11),
		`PosNr` int(11),
		`UPosNr` int(11),
		`ArtikelNr` varchar(32) COLLATE latin1_general_ci NOT NULL,
		`HMVNr` varchar(16) COLLATE latin1_general_ci,
		`PZN` varchar(16) COLLATE latin1_general_ci,
		`Bez1` varchar(255) COLLATE latin1_general_ci,
		`Bez2` varchar(255) COLLATE latin1_general_ci,
		`Bez3` varchar(255) COLLATE latin1_general_ci,
		`Menge` int(11) DEFAULT '0',
		`MengenEinheit` int(11) DEFAULT '0',
		`MwstSatz` int(1) DEFAULT '0',
		`PreisUVP` dec(11,2) DEFAULT '0.00',
		`PreisEK` dec(11,2) DEFAULT '0.00',
		`PreisAbgabe` dec(11,2) DEFAULT '0.00',
		`Erstattung` dec(11,2) DEFAULT '0.00',
		`Zuzahlung` dec(11,2) DEFAULT '0.00',
		`Zulage` dec(11,2) DEFAULT '0.00',
		`Eigenanteil` dec(11,2) DEFAULT '0.00',
		`GesamtPreisAbgabe` dec(11,2) DEFAULT '0.00',
		`GesamtZulage` dec(11,2) DEFAULT '0.00',
		`GesamtEigenanteil` dec(11,2) DEFAULT '0.00',
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`KundeRechnungPosition`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;


DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_KundeRechnungPositionList` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_KundeRechnungPositionList` AS
	SELECT CII.Id, CII.RechnungNr, CII.PosNr, CII.ArtikelNr, CII.Bez1, CII.Menge, CII.PreisAbgabe, CII.Erstattung, CII.Zuzahlung, CII.Zulage
		FROM `mas_sani_1a2b3c4d`.`KundeRechnungPosition` AS CII
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`KundeBeleg`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`KundeBeleg` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`BelegNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`_BelegId` int(11),
		`Datum` date,
		`AuftragNr` varchar(16) COLLATE latin1_general_ci,
		`KundeNr` varchar(16) COLLATE latin1_general_ci,
		`_KundeId`int(11),
		`Filiale` varchar(16) COLLATE latin1_general_ci,
		`IKNr_LE` varchar(16) COLLATE latin1_general_ci,
		`IKNr_KK` varchar(16) COLLATE latin1_general_ci,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`KundeBeleg`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_KundeBelegSurvey` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_KundeBelegSurvey` AS
	SELECT CR.Id, CR.BelegNr, CR.AuftragNr, CR.KundeNr
		FROM `mas_sani_1a2b3c4d`.`KundeBeleg` AS CR
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_1a2b3c4d`.`KundeBelegPosition`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_1a2b3c4d`.`KundeBelegPosition` (
		`Id` int(11) NOT NULL,
		`ERPNr` varchar(16) COLLATE latin1_general_ci,
		`BelegNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`_BelegId` int(11),
		`PosNr` int(11),
		`UPosNr` int(11),
		`ArtikelNr` varchar(32) COLLATE latin1_general_ci NOT NULL,
		`HMVNr` varchar(16) COLLATE latin1_general_ci,
		`PZN` varchar(16) COLLATE latin1_general_ci,
		`Bez1` varchar(255) COLLATE latin1_general_ci,
		`Bez2` varchar(255) COLLATE latin1_general_ci,
		`Bez3` varchar(255) COLLATE latin1_general_ci,
		`Menge` int(11) DEFAULT '0',
		`MengenEinheit` int(1) DEFAULT '0',
		`MwstSatz` int(1) DEFAULT '0',
		`PreisEKNetto` dec(11,2) DEFAULT '0.00',
		`PreisUVPNetto` dec(11,2) DEFAULT '0.00',
		`RabattProzent` dec(11,2) DEFAULT '0.00',
		`RabattNetto` dec(4,2) DEFAULT '0.00',
		`PreisAbgabeNetto` dec(11,2) DEFAULT '0.00',
		`ErstattungNetto` dec(11,2) DEFAULT '0.00',
		`Zuzahlung` dec(11,2) DEFAULT '0.00',
		`Zulage` dec(11,2) DEFAULT '0.00',
		`Eigenanteil` dec(11,2) DEFAULT '0.00',
		`GesamtPreisAbgabeNetto` dec(11,2) DEFAULT '0.00',
		`GesamtZulage` dec(11,2) DEFAULT '0.00',
		`GesamtEigenanteil` dec(11,2) DEFAULT '0.00',
		`WarenGruppe` varchar(32) DEFAULT '',
		`ErloesKonto` varchar(16) DEFAULT '',
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_1a2b3c4d`.`KundeBelegPosition`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_1a2b3c4d`.`v_KundeBelegPositionList` ;
CREATE VIEW `mas_sani_1a2b3c4d`.`v_KundeBelegPositionList` AS
	SELECT CRI.Id, CRI.BelegNr, CRI.PosNr, CRI.ArtikelNr, CRI.Bez1, CRI.Menge, CRI.PreisAbgabeNetto, CRI.ErstattungNetto, CRI.Zuzahlung
		FROM `mas_sani_1a2b3c4d`.`KundeBelegPosition` AS CRI
	;
