/**
 *	20170303	khw	Erste Unbennung kunden -> Kunde
 */

/**
 *
 */
INSERT INTO `mas_sani_1a2b3c4d`.`Artikel` ( ArtikelNr, Bezeichnung1, Bezeichnung2, Bezeichnung3, HMVNr)
	SELECT mart_artnr, mart_bezeichnung, mart_bezeichnung2, mart_bezeichnung3, mart_HMV
		FROM `mas_sani_1a2b3c4d_orig`.`artikelliste_mandant`
	;

/**
 *
 */
INSERT INTO `mas_sani_1a2b3c4d`.`Kunde` ( KundeNr, _KundeId, Vorname, Name1, Name2, Strasse, Hausnummer, PLZ, Ort, Vers1IKNr, Vers1KVNr, Vers2IKNr, Vers3IKNr)
	SELECT kun_kunnr
			,	kun_id
			,	kun_vorname
			,	kun_name
			,	kun_name2
			,	kun_strasse
			,	kun_strasse_zusatz
			,	kun_plz
			,	kun_ort
			,	kun_krankenkassen_nummer
			,	kun_krankenversichertennummer
			,	kun_krankenkassen_nummer2
			,	kun_krankenkassen_nummer3
		FROM `mas_sani_1a2b3c4d_orig`.`kunden`
	;

/**
 *
 */

/**
 *
 */
INSERT INTO `mas_sani_1a2b3c4d`.`CustomerNote` ( KundeNr, _KundeId, Hinweis, NotizTypText, Notiz)
	SELECT "LEER"
		,	KN.kn_kunid
		,	KN.kn_whinweis
		,	KN.kn_type
		,	KN.kn_text
		FROM `mas_sani_1a2b3c4d_orig`.`kunden_notes` AS KN
	;

UPDATE `mas_sani_1a2b3c4d`.`CustomerNote` AS CN
	SET CN.KundeNr = ( SELECT kun_kunnr FROM `mas_sani_1a2b3c4d_orig`.`kunden` AS K
				WHERE K.kun_id = CN._KundeId
			)
	;

/**
 *
 */
INSERT INTO `mas_sani_1a2b3c4d`.`KundeAuftrag` ( AuftragNr, Datum, Filiale, KundeNr, _KundeId, IKNr_KK)
	SELECT a_AuftragId, a_Datum, a_Filiale, a_KunNr, a_KunId, SUBSTRING( a_IKNummer, 1, 9)
		FROM `mas_sani_1a2b3c4d_orig`.`auftrag`
	;

/**
 *
 */
INSERT INTO `mas_sani_1a2b3c4d`.`KundeAuftragPosition` (
				AuftragNr, _AuftragId, PosNr, UPosNr,
				ArtikelNr, HMVNr, PZN,
				Menge,
				MwstSatz,
				PreisEK, PreisDb, PreisUVP, AbschlagProz, AbschlagAbs,
				KTAnteilNetto, KTAnteilBrutto,
				AbgabePreisNetto, AbgabePreisBrutto, AbgabePreisMwst,
				Zuzahlung, Eigenanteil, Zulage
				)
	SELECT "LEER", ap_AuftragId, ap_PosNo, ap_UPosNo,
				ap_ArtId, ap_HMV, ap_PZN,
				ap_Menge,
				ap_MWSTKEY,
				ap_PREISEKNETTO, ap_PREISDB, ap_UVP, ap_ProzAufAbschlag, ap_AbsAufAbschlag,
				ap_PREISNETTO, ap_PREISBRUTTO,
				ap_VKPREISNETTO, ap_VKPREISBRUTTO, ap_VKMWST,
				ap_AnteilKundeZuzahlungPos, ap_FesterEigenanteilKunde, ap_AnteilKundeWirtAufPos
		FROM `mas_sani_1a2b3c4d_orig`.`auf_pos`
	;

UPDATE `mas_sani_1a2b3c4d`.`KundeAuftragPosition` AS KAP
	SET KAP.AuftragNr = ( SELECT a_AuftragId FROM `mas_sani_1a2b3c4d_orig`.`auftrag` AS A
				WHERE A.a_id = KAP._AuftragId
			)
	;

/**
 *
 */
INSERT INTO `mas_sani_1a2b3c4d`.`KundeAuftragObjekt` ( AuftragNr, _AuftragId, Datum, ObjektTyp, ObjektId, Datei, Deleted, Storno)
	SELECT "LEER", aw_aufid, aw_date, aw_typ, aw_lid, aw_file, aw_deleted, aw_storno
		FROM `mas_sani_1a2b3c4d_orig`.`auftrag_wf`
	;

UPDATE `mas_sani_1a2b3c4d`.`KundeAuftragObjekt` AS KAO
	SET KAO.AuftragNr = ( SELECT a_AuftragId FROM `mas_sani_1a2b3c4d_orig`.`auftrag` AS A
				WHERE A.a_id = KAO._AuftragId
			)
	;

/**
 *
 */
INSERT INTO `mas_sani_1a2b3c4d`.`CustomerInvoice` ( RechnungNr, AuftragNr, KundeNr, _KundeId, IKNr_KK)
	SELECT re_rechnungsnr, re_auftragsnummer, "LEER", re_kunid, re_iknummer
		FROM `mas_sani_1a2b3c4d_orig`.`rechnung`
	;

/**
 *
 */
INSERT INTO `mas_sani_1a2b3c4d`.`CustomerInvoiceItem` ( RechnungNr, PosNr, ArtikelNr, Bez1, Menge, PreisAbgabe)
	SELECT rp_re_id, rp_PosNr, rp_ArtikelNr, rp_bezeichnung, rp_Menge, rp_Preis
		FROM `mas_sani_1a2b3c4d_orig`.`rechnung_pos`
		WHERE  rp_PosNr < 1000
	;

UPDATE `mas_sani_1a2b3c4d`.`CustomerInvoiceItem` AS CII
	SET Zuzahlung = ( SELECT rp_Preis
				FROM `mas_sani_1a2b3c4d_orig`.`rechnung_pos` AS O_RP
				WHERE O_RP.rp_re_id = CII.RechnungNr
 				AND O_RP.rp_PosNr - 1000 = CII.PosNr
			) * 1.0
	;

UPDATE `mas_sani_1a2b3c4d`.`CustomerInvoiceItem` AS CII
	SET Zulage = ( SELECT rp_Preis
				FROM `mas_sani_1a2b3c4d_orig`.`rechnung_pos` AS O_RP
				WHERE O_RP.rp_re_id = CII.RechnungNr
 				AND O_RP.rp_PosNr - 2000 = CII.PosNr
			) * 1.0
	;

UPDATE `mas_sani_1a2b3c4d`.`CustomerInvoiceItem` AS CII
	SET CII.Zuzahlung = CII.PreisAbgabe
		,	CII.PreisAbgabe = 0
		,	CII.Bez1 = SUBSTRING( CII.Bez1, 27)
	WHERE CII.Bez1 LIKE 'Zuzahlung/Eigenanteil für %'
	;

/**
 *
 */
INSERT INTO `mas_sani_1a2b3c4d`.`KundeBeleg` ( BelegNr, _BelegId, AuftragNr, KundeNr, _KundeId)
	SELECT kk_belegid, kk_id, "LEER", "LEER", kk_kunid
		FROM `mas_sani_1a2b3c4d_orig`.`kassenbuch_k`
	;

/**
 *
 */
UPDATE `mas_sani_1a2b3c4d_orig`.`kassenbuch_p`
	SET kp_einheit = '1'
	WHERE kp_einheit = ''
	;
INSERT INTO `mas_sani_1a2b3c4d`.`KundeBelegPosition` ( BelegNr, _BelegId, PosNr, ArtikelNr, Bez1, Bez2, Bez3, Menge, MengenEinheit, MwstSatz, RabattProzent, RabattNetto, PreisAbgabeNetto, ErstattungNetto, Zuzahlung, Eigenanteil, Zulage, WarenGruppe, ErloesKonto)
	SELECT "LEER", kp_kid, kp_posnr, kp_artid, kp_txt1, kp_txt2, kp_txt3, kp_menge, kp_einheit, kp_mwst_id, kp_rabatt_p, kp_rabatt_a, kp_vk_netto, kp_kk_netto, kp_zuz_betrag, kp_eig_betrag, kp_qzu_betrag, kp_wgr, kp_erloeskonto
		FROM `mas_sani_1a2b3c4d_orig`.`kassenbuch_p`
	;

/**
 *
 */
UPDATE `mas_sani_1a2b3c4d`.`KundeBelegPosition` AS CRI
	SET CRI.BelegNr = ( SELECT BelegNr FROM `mas_sani_1a2b3c4d`.`KundeBeleg` AS CR
				WHERE CR._BelegId = CRI._BelegId
			)	,
	GesamtPreisAbgabeNetto = PreisAbgabeNetto * Menge
	;
