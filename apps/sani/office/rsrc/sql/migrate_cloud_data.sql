/**
 *
 */
INSERT INTO `mas_sani_cloud`.`KostentraegerGruppe` ( KTGruppeNr, Name1)
	SELECT kg_grpnr, kg_grpname
		FROM `mas_sani_cloud_orig`.`kassengruppen_zuordnung`
		GROUP BY `kg_grpnr`
	;

/**
 *
 */
INSERT INTO `mas_sani_cloud`.`KostentraegerGruppeKostentraeger` ( KTGruppeNr, IKNr)
	SELECT kg_grpnr, kg_ik
		FROM `mas_sani_cloud_orig`.`kassengruppen_zuordnung`
	;

/**
 *
 */

/**
 *
 */

/**
 *
 */

/**
 *
 */

/**
 *
 */

/**
 *
 */
INSERT INTO `mas_sani_cloud`.`Leistungserbringer` ( _Id, IKNr, Name1, Name2, Name3, Strasse, Hausnummer, PLZ, Ort, Telefon, Fax)
	SELECT id, ik_nummern, name, '', '', '', '', '', '', '', ''
		FROM `mas_sani_cloud_orig`.`mandant`
	;

/**
 *
 */
INSERT INTO `mas_sani_cloud`.`Artikel` ( ArtikelNr, Bezeichnung1, Bezeichnung2, Bezeichnung3, HMVNr)
	SELECT art_artnr, art_bezeichnung, art_bezeichnung2, art_bezeichnung3,
				CONCAT( SUBSTR( art_HMV, 1, 2), ".", SUBSTR( art_HMV, 3, 2), ".", SUBSTR( art_HMV, 5, 2), ".", SUBSTR( art_HMV, 7, 4))
		FROM `mas_sani_cloud_orig`.`artikelliste_komplett`
	;

/**
 *
 */
INSERT INTO `mas_sani_cloud`.`LEGSVertrag` ( LEGS, _odId, TarifbereichText, Kassenart, DatumGueltigAb, DatumGueltigBis, DatumLetzterUpdate)
	SELECT prl_legs, prl_preisid, prl_tarifbereich_text, prl_kassenart
			, concat( substr( prl_gueltig_ab, 7, 4), "-", substr( prl_gueltig_ab, 4, 2), "-",substr( prl_gueltig_ab, 1, 2))
			, concat( substr( prl_gueltig_bis, 7, 4), "-", substr( prl_gueltig_bis, 4, 2), "-",substr( prl_gueltig_bis, 1, 2))
			, concat( substr( prl_updatum, 7, 4), "-", substr( prl_updatum, 4, 2), "-",substr( prl_updatum, 1, 2))
		FROM `mas_sani_cloud_orig`.`preislink_new`
        WHERE prl_legs NOT like '1_00008'
		GROUP BY prl_legs
	;

UPDATE `mas_sani_cloud`.`LEGSVertrag` AS LV
	SET `Beschreibung` = ( SELECT `ver_bezeichnung` FROM `mas_sani_1a2b3c4d_orig`.`vertraege` AS V WHERE V.ver_legs = LV.LEGS LIMIT 1)
	;

/**
 *
 */

/**
 *
 */
DROP TABLE IF EXISTS `mas_sani_cloud`.`VertragPosition`
	;

CREATE TABLE IF NOT EXISTS `mas_sani_cloud`.`VertragPosition` (
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

ALTER TABLE `mas_sani_cloud`.`VertragPosition`
	MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT
	;

INSERT INTO `mas_sani_cloud`.`VertragPosition` ( HMVNr, Bezeichnung1, Bezeichnung2, LKZ, LEGS, Preis)
	SELECT CONCAT( SUBSTR( pl_hmv, 1, 2), ".", SUBSTR( pl_hmv, 3, 2), ".", SUBSTR( pl_hmv, 5, 2), ".", SUBSTR( pl_hmv, 7, 4)), pl_beschreibung1, pl_beschreibung2, pl_leistungskz, pl_legs, pl_preis_primaerkassen
		FROM `mas_sani_cloud_orig`.`preislisten_new`
		WHERE pl_legs <> "" AND HMVNr like '1_00008'
	;

INSERT INTO `mas_sani_cloud`.`VertragPosition` ( HMVNr, Bezeichnung1, Bezeichnung2, LKZ, LEGS, Preis)
	SELECT CONCAT( SUBSTR( pl_hmv, 1, 2), ".", SUBSTR( pl_hmv, 3, 2), ".", SUBSTR( pl_hmv, 5, 2), ".", SUBSTR( pl_hmv, 7, 4)), pl_beschreibung1, pl_beschreibung2, pl_leistungskz, pl_legs_ersatz, pl_preis_ersatzkassen
		FROM `mas_sani_cloud_orig`.`preislisten_new`
		WHERE pl_legs_ersatz <> "" AND HMVNr like '1_00008'
	;

INSERT INTO `mas_sani_cloud`.`VertragPosition` ( HMVNr, Bezeichnung1, Bezeichnung2, LKZ, LEGS, Preis)
	SELECT CONCAT( SUBSTR( pl_hmv, 1, 2), ".", SUBSTR( pl_hmv, 3, 2), ".", SUBSTR( pl_hmv, 5, 2), ".", SUBSTR( pl_hmv, 7, 4)), pl_beschreibung1, pl_beschreibung2, pl_leistungskz, '#######', pl_preis_privat
		FROM `mas_sani_cloud_orig`.`preislisten_new`
		WHERE pl_legs = '' AND pl_legs_ersatz = '' AND HMVNr like '1_00008'
	;

/**
 *
 */
