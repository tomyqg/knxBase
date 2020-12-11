/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`KostentraegerGruppe`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`KostentraegerGruppe` (
		`Id` int(11) NOT NULL,
		`KTGruppeNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`Name1` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Name2` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Name3` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`KostentraegerGruppe`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_KostentraegerGruppeSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_KostentraegerGruppeSurvey` AS
	SELECT KT.Id, KT.KTGruppeNr, KT.Name1 AS Name
		FROM `mas_sani_cloud`.`KostentraegerGruppe` AS KT
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`KostentraegerGruppeKostentraeger`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`KostentraegerGruppeKostentraeger` (
		`Id` int(11) NOT NULL,
		`KTGruppeNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`PosNr` int NOT NULL DEFAULT -1,
		`IKNr` varchar(16) COLLATE latin1_general_ci DEFAULT '',
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`KostentraegerGruppeKostentraeger`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_KostentraegerGruppeKostentraegerSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_KostentraegerGruppeKostentraegerSurvey` AS
	SELECT KTGK.Id, KTGK.KTGruppeNr, KTGK.IKNr
		FROM `mas_sani_cloud`.`KostentraegerGruppeKostentraeger` AS KTGK
		ORDER BY KTGK.KTGruppeNr, KTGK.IKNr
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`Kostentraeger`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`Kostentraeger` (
		`Id` int(11) NOT NULL,
		`_Id` int(11) NOT NULL,
		`IKNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`Name1` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Name2` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Name3` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Strasse` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Hausnummer` varchar(8) COLLATE latin1_general_ci DEFAULT '',
		`PLZ` varchar(8) COLLATE latin1_general_ci DEFAULT '',
		`Ort` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Telefon` varchar(64) COLLATE latin1_general_ci NULL,
		`Fax` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '12345',
		`KostentraegerArt` varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT 'kk',
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`Kostentraeger`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_KostentraegerSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_KostentraegerSurvey` AS
	SELECT KT.Id, KT.IKNr, CONCAT( KT.Name1, " ", KT.Name2) AS Name, KT.PLZ
		FROM `mas_sani_cloud`.`Kostentraeger` AS KT
	;

INSERT INTO `mas_sani_cloud`.`Kostentraeger` ( _Id, IKNr, Name1, Name2, Name3, Strasse, Hausnummer, PLZ, Ort, Telefon, Fax)
	SELECT kk_id, kk_ik_nummer, kk_name, '', '', '', '', '', '', '', ''
		FROM `mas_sani_cloud_orig`.`krankenkassen`
	;

UPDATE `mas_sani_cloud`.`Kostentraeger` AS KT
	SET KT.Name2 = ( SELECT name2 FROM `mas_sani_cloud_orig`.`krankenkassen_routing_anschrift` AS KRA WHERE KRA.iknr = KT.IKNr LIMIT 1)
 	;

UPDATE `mas_sani_cloud`.`Kostentraeger` AS KT
	SET KT.Name3 = ( SELECT name3 FROM `mas_sani_cloud_orig`.`krankenkassen_routing_anschrift` AS KRA WHERE KRA.iknr = KT.IKNr LIMIT 1)
 	;

UPDATE `mas_sani_cloud`.`Kostentraeger` AS KT
	SET KT.Strasse = ( SELECT strasse FROM `mas_sani_cloud_orig`.`krankenkassen_routing_anschrift` AS KRA WHERE KRA.iknr = KT.IKNr LIMIT 1)
 	;

UPDATE `mas_sani_cloud`.`Kostentraeger` AS KT
	SET KT.PLZ = ( SELECT plz FROM `mas_sani_cloud_orig`.`krankenkassen_routing_anschrift` AS KRA WHERE KRA.iknr = KT.IKNr LIMIT 1)
 	;

UPDATE `mas_sani_cloud`.`Kostentraeger` AS KT
	SET KT.Ort = ( SELECT ort FROM `mas_sani_cloud_orig`.`krankenkassen_routing_anschrift` AS KRA WHERE KRA.iknr = KT.IKNr LIMIT 1)
 	;
/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`KostentraegerAnschrift`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`KostentraegerAnschrift` (
		`Id` int(11) NOT NULL,
		`IKNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`Typ`int(3) NOT NULL DEFAULT '0',
		`Strasse` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Hausnummer` varchar(8) COLLATE latin1_general_ci DEFAULT '',
		`PLZ` varchar(8) COLLATE latin1_general_ci DEFAULT '',
		`Ort` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Telefon` varchar(64) COLLATE latin1_general_ci NULL,
		`Fax` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '12345',
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
				PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`KostentraegerAnschrift`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_KostentraegerAnschriftSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_KostentraegerAnschriftSurvey` AS
	SELECT IA.Id, IA.IKNr, IA.Strasse, IA.PLZ, IA.Ort, IAAA.Value AS AnschriftArt
		FROM `mas_sani_cloud`.`KostentraegerAnschrift` AS IA
		LEFT JOIN `mas_sani_cloud`.`AppOption` AS IAAA ON IAAA.Key = IA.Typ AND IAAA.OptionName = "KTAnschriftArt"
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`KostentraegerKontakt`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`KostentraegerKontakt` (
		`Id` int(11) NOT NULL,
		`IKNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`Name` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Vorname` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Telefon` varchar(64) COLLATE latin1_general_ci NULL,
		`Mobil` varchar(64) COLLATE latin1_general_ci NULL,
		`Fax` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '12345',
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
				PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`KostentraegerKontakt`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_KostentraegerKontaktSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_KostentraegerKontaktSurvey` AS
	SELECT I.Id, I.IKNr, I.Name, I.Vorname
		FROM `mas_sani_cloud`.`KostentraegerKontakt` AS I
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`KostentraegerVerweis`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`KostentraegerVerweis` (
		`Id` int(11) NOT NULL,
		`IKNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`Typ` int(3) COLLATE latin1_general_ci DEFAULT 0,
		`IKNrVerweis` varchar(16) COLLATE latin1_general_ci DEFAULT '',
		`Bundesland` int(3) COLLATE latin1_general_ci DEFAULT 0,
		`Bezirk` int(3) COLLATE latin1_general_ci DEFAULT 0,
		`Leistungsart` int(3) COLLATE latin1_general_ci DEFAULT 0,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
				PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`KostentraegerVerweis`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_KostentraegerVerweisSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_KostentraegerVerweisSurvey` AS
	SELECT IV.Id, IV.IKNr, IV.Typ, IV.IKNrVerweis, IV.Bundesland, IV.Bezirk, IAAA.Value AS VerweisArt
		FROM `mas_sani_cloud`.`KostentraegerVerweis` AS IV
		LEFT JOIN `mas_sani_cloud`.`AppOption` AS IAAA ON IAAA.Key = IV.Typ AND IAAA.OptionName = "KTVerweisArt"
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`KostentraegerGKVList`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`KostentraegerGKVList` (
		`Id` int(11) NOT NULL,
		`Filename` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`DatumGeladen` date NULL,
		`DatumAusgewertet` date NULL,
		`Quelle` varchar(255) COLLATE latin1_general_ci DEFAULT '',
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`KostentraegerGKVList`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_KostentraegerGKVListSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_KostentraegerGKVListSurvey` AS
	SELECT IGL.Id, IGL.Filename, IGL.DatumGeladen, IGL.DatumAusgewertet
		FROM `mas_sani_cloud`.`KostentraegerGKVList` AS IGL
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`Leistungserbringer`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`Leistungserbringer` (
		`Id` int(11) NOT NULL,
		`_Id` int(11) NOT NULL,
		`IKNr` varchar(250) COLLATE latin1_general_ci NOT NULL,
		`Name1` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Name2` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Name3` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Strasse` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Hausnummer` varchar(8) COLLATE latin1_general_ci DEFAULT '',
		`PLZ` varchar(8) COLLATE latin1_general_ci DEFAULT '',
		`Ort` varchar(64) COLLATE latin1_general_ci DEFAULT '',
		`Telefon` varchar(64) COLLATE latin1_general_ci NULL,
		`Fax` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '12345',
		`KostentraegerArt` varchar(8) COLLATE latin1_general_ci NOT NULL DEFAULT 'kk',
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`Leistungserbringer`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_LeistungserbringerSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_LeistungserbringerSurvey` AS
	SELECT LE.Id, LE.IKNr, CONCAT( LE.Name1, " ", LE.Name2) AS Name, LE.PLZ
		FROM `mas_sani_cloud`.`Leistungserbringer` AS LE
	;

/**
 *
 */
/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`Arzt`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`Arzt` (
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

ALTER TABLE `mas_sani_cloud`.`Arzt`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_ArztSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_ArztSurvey` AS
	SELECT A.Id, A.ERPNr, A.ArztNr, CONCAT( A.Name1, ", ", A.Vorname) AS Name, A.PLZ
		FROM `mas_sani_cloud`.`Arzt` AS A
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`HMV_PG`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`HMV_PG` (
		`Id` int(11) NOT NULL,
		`ProduktGruppe` varchar(255) COLLATE latin1_general_ci NOT NULL,
		`Bezeichnung` text COLLATE latin1_general_ci NOT NULL,
		`Definition` text COLLATE latin1_general_ci NOT NULL,
		`Indikation` text COLLATE latin1_general_ci NOT NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`HMV_PG`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

INSERT INTO `mas_sani_cloud`.`HMV_PG` ( ProduktGruppe, Bezeichnung, Definition, Indikation)
	SELECT gruppe, bezeichnung, definition, indikation
		FROM `mas_sani_cloud_orig`.`hmv_01_produktgruppe`
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_HMV_PGSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_HMV_PGSurvey` AS
	SELECT PG.Id, PG.ProduktGruppe, PG.Bezeichnung
		FROM `mas_sani_cloud`.`HMV_PG` AS PG
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`HMV_AO`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`HMV_AO` (
		`Id` int(11) NOT NULL,
		`Anwendungsort` varchar(255) COLLATE latin1_general_ci NOT NULL,
		`Bezeichnung` text COLLATE latin1_general_ci NOT NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`HMV_AO`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

INSERT INTO `mas_sani_cloud`.`HMV_AO` ( Anwendungsort, Bezeichnung)
	SELECT ort, bezeichnung
		FROM `mas_sani_cloud_orig`.`hmv_02_anwendungsort`
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_HMV_AOSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_HMV_AOSurvey` AS
	SELECT AO.Id, AO.Anwendungsort, AO.Bezeichnung
		FROM `mas_sani_cloud`.`HMV_AO` AS AO
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`HMV_UG`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`HMV_UG` (
		`Id` int(11) NOT NULL,
		`HMVNr` varchar(255) COLLATE latin1_general_ci NOT NULL,
		`Bezeichnung` text COLLATE latin1_general_ci NOT NULL,
		`Medizin` text COLLATE latin1_general_ci NOT NULL,
		`Technik` text COLLATE latin1_general_ci NOT NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`HMV_UG`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

INSERT INTO `mas_sani_cloud`.`HMV_UG` ( HMVNr, Bezeichnung, Medizin, Technik)
	SELECT CONCAT( gruppe, ort, unter), bezeichnung, medizin, technik
		FROM `mas_sani_cloud_orig`.`hmv_03_untergruppe`
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_HMV_UGSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_HMV_UGSurvey` AS
	SELECT UG.Id, UG.HMVNr, UG.Bezeichnung
		FROM `mas_sani_cloud`.`HMV_UG` AS UG
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`HMV_PA`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`HMV_PA` (
		`Id` int(11) NOT NULL,
		`HMVNr` varchar(255) COLLATE latin1_general_ci NOT NULL,
		`Bezeichnung` text COLLATE latin1_general_ci NOT NULL,
		`Beschreibung` text COLLATE latin1_general_ci NOT NULL,
		`Indikation` text COLLATE latin1_general_ci NOT NULL,
		`Anmerkung` text COLLATE latin1_general_ci NOT NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`HMV_PA`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

INSERT INTO `mas_sani_cloud`.`HMV_PA` ( HMVNr, Bezeichnung, Beschreibung, Indikation, Anmerkung)
	SELECT CONCAT( gruppe, ort, unter, art), bezeichnung, beschreibung, indikation, anmerkung
		FROM `mas_sani_cloud_orig`.`hmv_04_produktart`
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_HMV_PASurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_HMV_PASurvey` AS
	SELECT PA.Id, PA.HMVNr, PA.Bezeichnung
		FROM `mas_sani_cloud`.`HMV_PA` AS PA
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`HMV_EP`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`HMV_EP` (
		`Id` int(11) NOT NULL,
		`HMVNr` varchar(255) COLLATE latin1_general_ci NOT NULL,
		`Bezeichnung` text COLLATE latin1_general_ci NOT NULL,
		`Hersteller` text COLLATE latin1_general_ci NOT NULL,
		`DatumAnlage` date NULL,
		`DatumAenderung` date NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`HMV_EP`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

INSERT INTO `mas_sani_cloud`.`HMV_EP` ( HMVNr, Bezeichnung, Hersteller, DatumAnlage, DatumAenderung)
	SELECT CONCAT( h_gruppe, h_ort, h_untergruppe, h_art, h_produkt), h_bezeichnung, h_hersteller, h_aufnahmedatum, h_aenderungsdatum
		FROM `mas_sani_cloud_orig`.`hmv_05_einzelprodukt`
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_HMV_EPSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_HMV_EPSurvey` AS
	SELECT EP.Id, EP.HMVNr, EP.Bezeichnung, EP.Hersteller
		FROM `mas_sani_cloud`.`HMV_EP` AS EP
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`LEGSVertrag`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`LEGSVertrag` (
		`Id` int(11) NOT NULL,
		`ERPNo` varchar(15),
		`LEGS` varchar(16) COLLATE latin1_general_ci DEFAULT '#####',
		`Beschreibung` varchar( 255),
		`_odId` bigint(20),
		`TarifbereichText` varchar(64) COLLATE latin1_general_ci NOT NULL,
		`Kassenart` varchar(8) COLLATE latin1_general_ci NOT NULL,
		`DatumGueltigAb` date NULL,
		`DatumGueltigBis` date NULL,
		`DatumLetzterUpdate` date NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`LEGSVertrag`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

INSERT INTO `mas_sani_cloud`.`LEGSVertrag` ( LEGS, _odId, TarifbereichText, Kassenart, DatumGueltigAb, DatumGueltigBis, DatumLetzterUpdate)
	SELECT prl_legs, prl_preisid, prl_tarifbereich_text, prl_kassenart
			, concat( substr( prl_gueltig_ab, 7, 4), "-", substr( prl_gueltig_ab, 4, 2), "-",substr( prl_gueltig_ab, 1, 2))
			, concat( substr( prl_gueltig_bis, 7, 4), "-", substr( prl_gueltig_bis, 4, 2), "-",substr( prl_gueltig_bis, 1, 2))
			, concat( substr( prl_updatum, 7, 4), "-", substr( prl_updatum, 4, 2), "-",substr( prl_updatum, 1, 2))
		FROM `mas_sani_cloud_orig`.`preislink_new`
        WHERE prl_legs like '1_00008'
		GROUP BY prl_legs
	;

UPDATE `mas_sani_cloud`.`LEGSVertrag` AS LV
	SET `Beschreibung` = ( SELECT `ver_bezeichnung` FROM `mas_sani_cloud_orig`.`vertraege` AS V WHERE V.ver_legs = LV.LEGS LIMIT 1)
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_LEGSVertragSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_LEGSVertragSurvey` AS
	SELECT LV.Id, LV.LEGS, LV.Beschreibung, LV.TarifbereichText, LV.Kassenart
		FROM `mas_sani_cloud`.`LEGSVertrag` AS LV
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`LeistungserbringerLEGS`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`LeistungserbringerLEGS` (
		`Id` int(11) NOT NULL,
		`LEIKNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`LEGS` varchar(16) COLLATE latin1_general_ci DEFAULT '#####',
		`DatumGueltigAb` date NULL,
		`DatumGueltigBis` date NULL,
		`DatumLetzterUpdate` date NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`LeistungserbringerLEGS`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

INSERT INTO `mas_sani_cloud`.`LeistungserbringerLEGS` ( _odId, LEIKNr, DatumGueltigAb, DatumLetzterUpdate)
	SELECT ip_preisid, ip_ik, ip_gueltigab, ip_updatum
		FROM `mas_sani_cloud_orig`.`ik_preisliste_new`
	;

UPDATE `mas_sani_cloud`.`LeistungserbringerLEGS` AS LL
   SET LEGS = ( SELECT LEGS FROM `mas_sani_cloud`.`LEGSVertrag` AS LV WHERE LV._odId = LL._odId)
   ;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_LeistungserbringerLEGSSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_LeistungserbringerLEGSSurvey` AS
	SELECT LL.Id, LL.LEGS, LL.LEIKNr
		FROM `mas_sani_cloud`.`LeistungserbringerLEGS` AS LL
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`LEGSPosition`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`LEGSPosition` (
		`Id` int(11) NOT NULL,
		`LEGS` varchar(16) COLLATE latin1_general_ci DEFAULT '#####',
		`HMVNr` varchar(16) COLLATE latin1_general_ci NOT NULL,
		`LKZ` varchar(16) COLLATE latin1_general_ci DEFAULT '##',
		`Bezeichnung1` varchar(255) COLLATE latin1_general_ci NOT NULL,
		`Bezeichnung2` varchar(255) COLLATE latin1_general_ci NOT NULL,
		`Preis` float(10,2) NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`LEGSPosition`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

INSERT INTO `mas_sani_cloud`.`LEGSPosition` ( HMVNr, Bezeichnung1, Bezeichnung2, LKZ, LEGS, Preis)
	SELECT pl_hmv, pl_beschreibung1, pl_beschreibung2, pl_leistungskz, pl_legs, pl_preis_primaerkassen
		FROM `mas_sani_cloud_orig`.`preislisten_new`
		WHERE pl_legs <> "" AND pl_legs like '1_00008'
	;

INSERT INTO `mas_sani_cloud`.`LEGSPosition` ( HMVNr, Bezeichnung1, Bezeichnung2, LKZ, LEGS, Preis)
	SELECT pl_hmv, pl_beschreibung1, pl_beschreibung2, pl_leistungskz, pl_legs_ersatz, pl_preis_ersatzkassen
		FROM `mas_sani_cloud_orig`.`preislisten_new`
		WHERE pl_legs_ersatz <> "" AND pl_legs like '1_00008'
	;

INSERT INTO `mas_sani_cloud`.`LEGSPosition` ( HMVNr, Bezeichnung1, Bezeichnung2, LKZ, LEGS, Preis)
	SELECT pl_hmv, pl_beschreibung1, pl_beschreibung2, pl_leistungskz, '#######', pl_preis_privat
		FROM `mas_sani_cloud_orig`.`preislisten_new`
		WHERE pl_legs = '' AND pl_legs_ersatz = '' AND pl_legs like '1_00008'
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_LEGSPositionSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_LEGSPositionSurvey` AS
	SELECT VP.Id, VP.HMVNr, VP.Bezeichnung1, VP.LKZ, VP.LEGS, VP.Preis
		FROM `mas_sani_cloud`.`LEGSPosition` AS VP
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_VertragLESurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_VertragLESurvey` AS
	SELECT LEL.Id, LEL.LEGS, LEL.LEIKNr, LE.Name1
		FROM `mas_sani_cloud`.`LeistungserbringerLEGS` AS LEL
		LEFT JOIN `mas_sani_cloud`.`Leistungserbringer` AS LE ON LE.IKNr = LEL.LEIKNr
	;

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`BundeslandTarifbereich`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`BundeslandTarifbereich` (
		`Id` int(11) NOT NULL,
		`_odId` bigint(20),
		`Bundesland` varchar(64) COLLATE latin1_general_ci NULL,
		`Tarifbereich` varchar(64) COLLATE latin1_general_ci NULL,
		`Kuerzel` varchar(8) NULL,
		`TarifbereichNr` int(2) NULL,
		`Remark` text,
		`LockState` int(11) DEFAULT 0,
			PRIMARY KEY (`Id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
	;

ALTER TABLE `mas_sani_cloud`.`BundeslandTarifbereich`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

INSERT INTO `mas_sani_cloud`.`BundeslandTarifbereich` ( _odId, Bundesland, Tarifbereich, Kuerzel, TarifbereichNr)
	SELECT bl_id, bl_name, bl_tarifbereich_name, bl_kuerzel, bl_tarifbereich
		FROM `mas_sani_cloud_orig`.`bundesland`
	;

DROP VIEW IF EXISTS `mas_sani_cloud`.`v_BundeslandTarifbereichSurvey` ;
CREATE VIEW `mas_sani_cloud`.`v_BundeslandTarifbereichSurvey` AS
	SELECT BLTB.Id, BLTB.Bundesland, BLTB.Tarifbereich
		FROM `mas_sani_cloud`.`BundeslandTarifbereich` AS BLTB
	;
