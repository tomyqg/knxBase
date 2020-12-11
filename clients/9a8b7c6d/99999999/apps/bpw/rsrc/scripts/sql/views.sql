#
#	views.sql
#	=========
#
#	Path:	lib/sys/SQL/
#
#	Product id.:
#	Version:
#
#	Revision history
#
#	Date			Rev.	Who		what
#	----------------------------------------------------------------------------
#	2015-03-23				khw		inception;
#
#	ToDo
#
#	Date			what
#	----------------------------------------------------------------------------
#
#	@package		??
#	@subpackage	System
#	@author		khwelter
#

#
#
#

DROP VIEW IF EXISTS v_AssessmentSurvey ;
CREATE VIEW v_AssessmentSurvey AS
	SELECT C.Id, C.AssessmentId, C.ProtocolNo
			FROM Assessment AS C
	;

DROP VIEW IF EXISTS v_BrakeSurvey ;
CREATE VIEW v_BrakeSurvey AS
	SELECT B.Id AS Id, B.BrakeId AS BrakeId, B.Description AS Description, B.ManufacturerId AS ManufacturerId, B.BrakeTypeId AS B_BrakeTypeId, B.Current AS Current,
				BT.BrakeTypeId AS BT_BrakeTypeId, BT.Description AS BT_Description,
				M.Description AS Manufacturer,
				A.ProtocolNo AS ProtocolNo, A.Current AS A_Current,
				BP.Description AS BP_Description,
				( SELECT COUNT(*) FROM t_bremse AS cB WHERE cB.hersteller_id = B.ManufacturerId) AS subItems
			FROM Brake AS B
		LEFT JOIN BrakeType AS BT on BT.BrakeTypeId = B.BrakeTypeId
		LEFT JOIN Manufacturer M on M.ManufacturerId = B.ManufacturerId
		LEFT JOIN Assessment A on A.BrakeId = B.BrakeId
		LEFT JOIN BrakePad AS BP on BP.AssessmentId = A.AssessmentId
	;
DROP VIEW IF EXISTS v_BrakeSurvey_0 ;
CREATE VIEW v_BrakeSurvey_0 AS
	SELECT B.Id AS Id, B.BrakeId AS BrakeId, B.Description AS Description, B.ManufacturerId AS ManufacturerId, B.BrakeTypeId AS B_BrakeTypeId, B.Current AS Current,
				BT.BrakeTypeId AS BT_BrakeTypeId, BT.Description AS BT_Description,
				M.Description AS Manufacturer,
				A.ProtocolNo AS ProtocolNo, A.Current AS A_Current,
				BP.Description AS BP_Description,
				( SELECT COUNT(*) FROM v_BrakeSurvey AS cB
						WHERE cB.ManufacturerId = B.ManufacturerId AND cB.BT_Description = BT_Description) AS subItems
			FROM Brake AS B
		LEFT JOIN BrakeType AS BT on BT.BrakeTypeId = B.BrakeTypeId
		LEFT JOIN Manufacturer M on M.ManufacturerId = B.ManufacturerId
		LEFT JOIN Assessment A on A.BrakeId = B.BrakeId
		LEFT JOIN BrakePad AS BP on BP.AssessmentId = A.AssessmentId
		GROUP BY BT_Description
	;

DROP VIEW IF EXISTS v_CalculationSurvey ;
CREATE VIEW v_CalculationSurvey AS
	SELECT C.Id, C.CalculationId, C.Number, C.TrailerTypeId, C.TrailerManufacturer, TT.Options, TD.Value2 AS MaxTotalWeight
			FROM Calculation AS C
		LEFT JOIN TrailerType AS TT ON TT.TrailerTypeId = C.TrailerTypeId
		LEFT JOIN TrailerData AS TD ON TD.CalculationId = C.CalculationId AND TD.AxleNo = -2
	;

DROP VIEW IF EXISTS V_CylinderSurvey ;
CREATE VIEW v_CylinderSurvey AS
	SELECT C.Id, C.CylinderId, C.ManufacturerId, C.OrderNo, C.Description, C.LastUpdate, M.Description AS M_Description
			FROM Cylinder AS C
		LEFT JOIN Manufacturer AS M ON M.ManufacturerId = C.ManufacturerId
	;

DROP VIEW IF EXISTS v_TyreSurvey ;
CREATE VIEW v_TyreSurvey AS
	SELECT C.Id, C.TyreId, C.Description, C.RDyn, C.RStat
		FROM Tyre AS C
	;

DROP VIEW IF EXISTS v_ValveSequenceSurvey ;
CREATE VIEW v_ValveSequenceSurvey AS
	SELECT VS.*, V.ValveFamilyId, VF.Description AS VF_Description, M.Description AS M_Description, V.Description AS Valve_Description
			FROM ValveSequence AS VS
		LEFT JOIN Valve AS V ON V.ValveId = VS.ValveId
		LEFT JOIN ValveFamily AS VF ON VF.ValveFamilyId = V.ValveFamilyId
		LEFT JOIN Manufacturer AS M on M.ManufacturerId = V.ManufacturerId

DROP VIEW IF EXISTS v_ValveSurvey ;
CREATE VIEW v_ValveSurvey AS
	SELECT C.*
		FROM Valve AS C
	;
DROP VIEW IF EXISTS v_ValveParameterSurvey ;
CREATE VIEW v_ValveParameterSurvey AS
	SELECT C.*
		FROM ValveParameter AS C
	;

#
# create the views which - kind of - are the new alias names for the new version of brake calculator,
# which mostly will be held in english
#

DROP VIEW if exists AxleUnit ;
CREATE VIEW AxleUnit as
	select	ag_key AS Id,
		ag_id AS AxleUnitId,
		fahrzeugtyp_id AS TrailerTypeId,
		ag_bezeichnung AS Description,
		berechtigung_id AS RightsId,
		ag_aktuell AS Current,
		ag_wortindex AS WordIndex
		FROM t_aggregat ;
DROP VIEW if exists Brake ;
CREATE VIEW Brake as
	select br_key AS Id,
		br_id AS BrakeId,
		hersteller_id AS ManufacturerId,
		br_berechtigung AS Authorization,
		br_bezeichnung AS Description,
		bremstyp_id AS BrakeTypeId,
		br_zylinderanzahl AS CylinderCount,
		br_moegl_zylinderanzahl AS CylinderCountOption,
		br_zylinderart AS CylinderType,
		br_feststellbremse AS ParkingBrake,
		br_trommelradius AS DrumRadius,
		br_spreizhebel AS StretchingLever,
		br_eta AS eta,
		br_ae AS ae,
		br_he AS he,
		br_ee AS ee,
		br_ig AS ig,
		br_sortierung AS Sequence,
		br_letzteaenderung AS LastUpdate,
		br_nachfolger AS Successor,
		br_aktuell AS Current,
		br_lokal AS Local,
		br_moeglichezylindertypen AS CylinderTypesOption,
		br_c0 AS c0,
		br_f0 AS f0,
		br_letzteaenderungticks AS LastUpdateTicks,
		br_sp AS sp,
		br_fzeta AS fzeta,
		br_saveid AS Checksum
	FROM t_bremse ;
DROP VIEW if exists BrakeType ;
CREATE VIEW BrakeType as
	select bt_key AS Id,
		bt_id AS BrakeTypeId,
		bt_bezeichnung AS Description,
		bt_wortindex AS WordIndex,
		bt_saveid AS Checksum
	FROM t_bremstyp ;
DROP VIEW if exists LeverLength;
CREATE VIEW LeverLength as
	select h_key AS Id,
		h_id AS LeverLengthId,
		bremse_id AS BrakeId,
		h_wert AS Value_h,
		h_zylinderanzahl AS CylinderCount,
		h_user AS UserId,
		hl_saveid AS Checksum
	FROM t_hebellaenge ;
DROP VIEW if exists Manufacturer;
CREATE VIEW Manufacturer as
	select h_key AS Id,
		h_id AS ManufacturerId,
		h_bezeichnung AS Description,
		h_ventilhersteller AS MakesValves,
		h_bremsanlagenhersteller AS MakesBrakes,
		h_zylinderhersteller AS MakesCylinders,
		h_achshersteller AS MakesAxles,
		h_saveid AS Checksum
	FROM t_hersteller;

DROP VIEW if exists Cylinder;
CREATE VIEW Cylinder AS
	SELECT z_key AS Id,
		z_id AS CylinderId,
		hersteller_id AS ManufacturerId,
		z_berechtigung AS Authorization,
		zp_id AS zp_id,
		z_bestellnummer AS OrderNo,
		z_bezeichnung AS Description,
		z_kurzhubzylinder AS ShortStrokeCylinder,
		z_bremstyp AS BrakeType,
		z_rueckstellfeder AS ResetSpring,
		z_feststellbremse ParkingBrake,
		z_maxhub AS StrokeMax,
		z_typ AS typ,
		z_thaw AS thaw,
		z_tha0 AS tha0,
		z_spw AS spw,
		z_sp0 AS sp0,
		z_membranzylinder AS MembraneCylinder,
		z_fs_typ AS fs_typ,
		z_fs_tfzw AS fs_tfzw,
		z_fs_tfz0 AS fs_tfz0,
		z_gutachtenkorrektur AS AssessmentCorrection,
		z_sortierung AS Sequence,
		z_nachfolger AS Successor,
		z_letzteaenderung AS LastUpdate,
		z_user AS User,
		z_lokal AS Local,
		z_speichertyp AS ReservoirType,
		z_aktuell AS Current,
		z_oeffnungsdruck AS OpeningPressure,
		z_ecegutachten AS Assessment_ece,
		z_druckbereichmin AS PressureMin,
		z_druckbereichmax AS PressureMax,
		z_letzteaenderungticks AS LastUpdateTicks,
		z_saveid AS Checksum
	FROM t_zylinder ;

DROP VIEW if exists BrakePad ;
CREATE VIEW BrakePad as
	SELECT bb_key AS Id,
		bb_id AS BrakePadId,
		gutachten_id AS AssessmentId,
		bb_bezeichnung AS Description,
		bb_datum AS AssessmentDate,
		bb_ausstelldatum AS DateOfIssue,
		bb_eg AS eg,
		bb_ece AS ece,
		bb_datumticks AS DateTicks,
		bb_ausstelldatumticks AS DateOfIssueTicks,
		bb_saveid AS Checksum
	FROM t_bremsbelag ;

DROP VIEW if exists BrakePadValue ;
CREATE VIEW BrakePadValue as
	select bw_key AS Id,
		bw_id AS BrakePadValueId,
		bremsbelag_id AS BrakePadId,
		bb_abbremsung AS Decelaration,
		bb_anlegemoment AS Torque,
		bb_betaetigungskraft AS ActuationForce,
		bb_bremskraft AS BrakingForce,
		bb_bremskraftstrich AS BrakingForceNd,
		bb_hebellaenge AS LeverLength,
		bb_hub AS Hub,
		bb_nockenmoment AS CamTorque,
		bb_prueftyp AS CheckingType,
		bb_rueckstellkraft AS ResetForce,
		bb_zylindertyp AS CylinderTyp,
		bb_zylinderdruck AS CylinderPressure,
		bbw_saveid AS Checksum
	FROM t_bremsbelag_werte ;

DROP VIEW if exists Application ;
CREATE VIEW Application AS
	SELECT a_key AS Id,
		a_id AS ApplicationId,
		sprache_id AS LanguageId,
		a_berechnungstyp AS CalculationType,
		a_anhangstyp AS AppendixType,
		a_freigabekonzept AS ReleaseConcept,
		a_identifikation AS Identification,
		a_standort AS Location,
		a_adresse AS Address,
		a_firmenkuerzel AS LocationCode,
		a_vertragspartner AS Partner,
		a_ecefreigabe AS ReleaseECE,
		a_egfreigabe AS ReleaseEG,
		a_stvzofreigabe AS ReleaseSTVZO,
		a_updateintervallzentral AS UpdateIntervalCentral,
		a_updateintervalllokal AS UpdateIntervalLocal,
		a_updateintervallzentralwarning AS UpdateIntervalCentralWarn,
		a_updateintervalllokalwarning AS UpdateIntervalLocalWarn,
		a_updatequelle AS UpdateSource,
		a_ftpuser AS FTPUser,
		a_ftppwd AS FTPPassword,
		a_ftpcheckupdate AS FTPCheckUpdate,
		a_standortberechtigung AS Authority,
		a_bestempffreigabe AS PlacementProposal,
		a_saveid AS Checksum
	FROM t_applikation ;

DROP VIEW if exists Language ;
CREATE VIEW Language as
	SELECT s_key AS Id,
		s_id AS LanguageId,
		s_name AS Name,
		s_nummer AS Number,
		s_wortindex AS WordIndex
	FROM t_sprache ;

DROP VIEW if exists AppUser ;
CREATE VIEW AppUser as
	SELECT u_key AS Id,
		u_id AS UserId,
		u_vorname AS FirstName,
		u_nachname AS LastName,
		u_erstpruefer AS CheckFirst,
		u_zweitpruefer AS CheckSecond,
		u_unterschrift AS Signature,
		u_berechtigung AS Authority,
		u_passwort AS Password,
		u_identifikation AS Identification,
		u_filterreifen AS FilterTyre,
		u_filtergutachten AS FilterAssessment,
		u_filterbremsanlagen AS FilterBrakeSystem,
		u_berechnungsgrundlage AS CalculationBase,
		u_kostenstelle AS CostCenter,
		u_kommission AS Commission,
		u_ventildatenblatt AS ValveDataSheet,
		u_feststellbremse AS ParkingBrake,
		u_bremskraftverteilung AS BrakeForceDistribution,
		u_ungueltig AS Invalid,
		u_referenzwertberechnung AS ReferenceValueCalculation,
		u_zusatzberechnung AS AdditionalCalculation,
		u_ebsdatenblatt AS EBSDataSheet,
		u_spracheprogramm AS GUILanguageId,
		u_spracheausdruck AS PrintoutLanguageId,
		u_typII AS TypeII,
		u_unterschriftlink AS SignatureLink,
		u_unterschriftersteller AS SignatureCreated,
		u_unterschriftgenehmigt AS SignatureReeleased,
#		u_unterschriftdatei AS SignatureFile,
		u_filterzylinder AS FilterCylinder,
		u_showkundenhinweis AS DisplayCustomerNote,
		applikation_id AS ApplicationId,
		u_saveid AS Checksum
	FROM t_user ;

DROP VIEW if exists CylinderType ;
CREATE VIEW CylinderType as
	SELECT zt_key AS Id,
		zt_id AS CylinderTypeId,
		zt_wert AS Value,
		zt_typ AS TypeText,
		zt_wortindex AS WordIndex
	FROM t_zylindertypen ;

DROP VIEW if exists ValveFamily ;
CREATE VIEW ValveFamily as
	SELECT 	vfm_key AS Id,
		vfm_id AS ValveFamilyId,
		vfm_bezeichnung AS Description,
		woerter_id AS WordIndex,
		vfm_saveid AS Checksum
	FROM t_ventilfamilie ;

DROP VIEW if exists Authority ;
CREATE VIEW Authority as
	SELECT 	be_key AS Id,
		be_id AS AuthorityId,
		be_bezeichnung AS Description,
		be_saveid AS Checksum
	FROM t_berechtigung ;

DROP VIEW if exists Language ;
CREATE VIEW Language as
	SELECT 	s_key AS Id,
		s_id AS LanguageId,
		s_name AS Name,
		s_wortindex AS WordIndex
	FROM t_sprache ;

DROP VIEW if exists Assessment ;
CREATE VIEW Assessment as
	SELECT  g_key AS Id,
		g_id AS AssessmentId,
		bremse_id AS BrakeId,
		reifen_id AS TyreId,
		g_berechtigung AS Rights,
		g_protokollnr AS ProtocolNo,
		g_index AS AssessmentIndex,
		g_achstyp AS AxleType,
		g_bremsfaktor AS BrakeFactor,
		g_bremsfaktorfeststellbremse AS ParkingBrakeFactor,
		g_zulAchslast AS AllowedAxleLoad,
		g_pruefAchslast AS TestAxleLoad,
		g_nockenmoment AS CamTorque,
		g_betaetigungskraft AS OperatingForce,
		g_sortierung AS Sorting,
		g_nachfolger AS Successor,
		g_letzteaenderung AS LastChange,
		g_pruefRdyn AS TestRDyn,
		g_minReifen AS MinTyre,
		g_pruefRstat AS TestRStat,
		g_typ AS Type,
		g_gmalr AS Gmalr,
		g_nutzung AS 'Usage',
		g_aktuell AS Current,
		g_lokal AS Local,
		g_zusaetzlichestvzobedingung AS AdditionalSTVZO,
		g_maxReifen AS MaxTyre,
		g_bremsbelagstvzo AS BrakeLiningSTVZO,
		g_pruefdatumstvzo AS TestDateSTVZO,
		g_pruefdatumstvzoticks AS TestDateSTVZOTicks,
		g_letzteaenderungticks AS LastUpdateeTicks,
		g_saveid AS Checksum
	FROM t_gutachten ;

DROP VIEW if exists Tyre ;
CREATE VIEW Tyre as
	SELECT r_key AS Id,
 		r_id AS TyreId,
		r_bezeichnung AS Description,
		r_radius AS Radius,
		r_rdyn AS RDyn,
		r_stat AS RStat,
		r_nutzung AS nutzung,
		r_nachfolger AS Successor,
		r_letzteaenderung AS LastUpdate,
		r_berechtigung AS Authority,
		r_standort AS Location,
		r_user AS UserId,
		r_lokal AS Local,
		r_aktuell AS Current,
		r_letzteaenderungticks AS LastUpdateTicks
	FROM t_reifen ;
DROP VIEW if exists Calculation;
CREATE VIEW Calculation as
	SELECT b_key AS Id,
		b_id AS CalculationId,
		fahrzeugtyp_id AS TrailerTypeId,
		b_vierbremsen AS FourBrakes,
		b_freigabestatus AS StatusRelease,
		b_freigabekonzept AS ReleaseConcept,
		b_erstpruefer AS CheckerFirst,
		b_zweitpruefer AS CheckerSecond,
		b_nummer AS Number,
		b_version AS Version,
		aggregat1_id AS AxleUnit1Id,
		b_liftachsen AS LiftAxles,
		bremse1_id AS Brake1Id,
		bremse2_id AS Brake2Id,
		b_br_achsen1 AS BrakeAxle1,
		b_br_achsen2 AS BrakeAxle2,
		reifen1_id AS Tyre1Id,
		reifen2_id AS Tyre2Id,
		reifen3_id AS Tyre3Id,
		reifen4_id AS Tyre4Id,
		b_r1_rdyn AS Tyre1Rdyn,
		b_r1_rstat AS Tyre1Rstat,
		b_r2_rdyn AS Tyre2Rdyn,
		b_r2_rstat AS Tyre2Rstat,
		b_r3_rdyn AS Tyre3Rdyn,
		b_r3_rstat AS Tyre3Rstat,
		b_r4_rdyn AS Tyre4Rdyn,
		b_r4_rstat AS Tyre4Rstat,
		b_ebsleerband AS EBSBandEmpty,
		b_bvdiagram AS Diagram_bv,
		b_feststellbb AS ParkingBrake,
		b_refwertb AS refwertb,
		b_zusatzberechnung AS AdditionalCalculation,
		b_ebsdatenblatt AS DatasheetEBS,
		b_ungueltig AS Invalid,
		b_ausgedruckt AS DatePrinted,
		b_kommission AS Commission,
		b_bemerkung AS Remark,
		b_achstyp AS AxleType,
		b_sprache AS Language,
		b_datum AS Date,
		b_hersteller AS Manufacturer,
		b_ersteller AS Creator,
		b_revision AS Revision,
		b_zwischensicherung AS IntermediateSave,
		b_jahr AS Year,
		bremsanlage_id AS BrakeSystemId,
		gutachtenb1_id AS Assessment1Id,
		gutachtenb2_id AS Assessment2Id,
		b_bremsanlagemanuell AS BrakeSystemManual,
		b_anhaengertyp AS TrailerType,
		aggregat2_id AS AxleUnit2Id,
		b_berechnungsgrundlage AS BaseOfCalculation,
		b_geschwindigkeitsbereich AS SpeedRange,
		b_anzahlalb AS CountALB,
		b_laufachsen AS CountRunningAxles,
		b_liftachsabstand LiftAxlesDistance,
		b_fahrzeughersteller TrailerManufacturer,
		b_kostenstelle AS Kostenstelle,
		b_typIIIpruefung AS CheckTypeIII,
		b_ventildatenblatt AS DatasheetValves,
		b_fahrzugtypausdruck AS PrintoutTrailerType,
		b_balgparameterausgewaehlt AS balgparameterausgewaehlt,
		b_balgtypvorne AS balgtypvorne,
		b_balgtyphinten AS balgtyphinten,
		b_balgL1vorne AS balgL1vorne,
		b_balgL1hinten AS balgL1hinten,
		b_balgL2vorne AS balgL2vorne,
		b_balgL2hinten AS balgL2hinten,
		b_unterschriftdateiersteller AS SignatureFileCreator,
		b_unterschriftdateigenehmigt AS SignatureFileChecker,
		b_unterschriftpfadersteller AS SignaturePathCreator,
		b_unterschriftpfadgenehmigt AS SignaturePathChecker,
		b_r1_bezeichnung AS DescriptionR1,
		b_r2_bezeichnung AS DescriptionR2,
		b_r3_bezeichnung AS DescriptionR3,
		b_r4_bezeichnung AS DescriptionR4,
		spracheausdruck_id AS PrintoutLanguage,
		b_datumticks AS DateTicks,
		b_bremsbelagskombination AS BrakeLiningCombination,
		b_stvzooptionenbits AS OptionBitsSTVZO
	 FROM t_berechnung ;
DROP VIEW if exists TrailerData;
CREATE VIEW TrailerData as
	SELECT fd_key AS Id,
		fd_id AS TrailerDataId,
		berechnung_id AS CalculationId,
		fd_achse AS AxleNo,
		fd_w1 AS Value1,
		fd_w2 AS Value2,
		fd_standort AS Location,
		fd_user AS UserId,
		fd_lokal AS Local
	FROM t_fahrzeugdaten ;
DROP VIEW if exists TrailerType ;
CREATE VIEW TrailerType as
	SELECT ft_key AS Id,
		ft_id AS TrailerTypeId,
		ft_typ AS TrailerTypeNo,
		ft_bezeichnung AS Description,
		ft_wortindex AS WordIndex,
		ft_optionen AS Options,
		ft_vorderachsen AS AxlesFront,
		ft_hinterachsen AS AxlesRear,
		ft_aktuell AS Current,
		ft_art AS TrailerSubTypeNo,
		ft_user AS UserId,
		ft_lokal AS Local,
		ft_l1 AS L1,
		ft_l2 AS L2,
		ft_achsabstand AS AxleDistance,
		ft_sort AS OrderPos
	FROM t_fahrzeugtyp ;
DROP VIEW if exists Valve ;
CREATE VIEW Valve as
	SELECT v_key AS Id,
		v_id AS ValveId,
		hersteller_id AS ManufacturerId,
		v_bezeichnung AS Description,
		v_ventiltyp AS ValveType,
		v_ventilfamilie AS ValveFamilyId,
		v_maxEingangsdruck AS PressInputMax,
		v_einstellbar AS Adjustable,
		v_abs AS Absolute,
		v_nachfolger AS ReplacedByValveId,
		v_letzteaenderung AS LastChange,
		v_ebs AS EBS,
		v_einstellwertMin AS TuningValueMin,
		v_einstellwertMax AS TuningValueMax,
		v_einstellwertDefault AS TuningValueDefault,
		v_albregelverhaeltnis AS ALBProportion,
		v_albeinstellwertBeladen AS ALBTuningValueLoaded,
		v_aktuell AS Current,
		v_lokal AS Local,
		v_berechtigung AS Rights,
		v_anpasspendeformelid AS Formula1,
		v_automatischeinstellen AS AutomaticTuning,
		v_albtyp AS ALBType,
		v_letzteaenderungticks AS LastChangeTicks,
		v_saveid AS Checksum
	FROM t_ventil ;
DROP VIEW if exists ValveParameter ;
CREATE VIEW ValveParameter as
	SELECT vp_key AS Id,
		vp_id AS ValveParameterId,
		ventil_id AS ValveId,
		formel_id AS FormulaId,
		vp_einstellpunkt AS TuningPoint,
		vp_defaultwert AS DefaultValue,
		vp_x AS X,
		vp_y AS Y,
		vp_user AS UserId,
		vp_lokal AS Local
	FROM t_ventilparameter ;
DROP VIEW if exists Attribute ;
CREATE VIEW Attribute AS
	SELECT at_key AS Id,
		at_id AS AttributeId,
		at_bezeichnung AS Description,
		at_wert AS Value,
		at_beschreibung AS Explanation,
		at_getter AS Getter,
		at_Standort AS Location,
		at_User AS User,
		at_Lokal AS Local
	FROM t_attribute ;
DROP VIEW if exists Rights ;
CREATE VIEW Rights as
	SELECT be_key AS Id,
		be_id AS RightsId,
		be_status AS Status,
		be_bezeichnung AS Description,
		be_saveid AS Checksum
	FROM t_berechtigung ;

DROP VIEW if exists Configuration ;
CREATE VIEW Configuration as
	SELECT bs_key AS Id,
		bs_id AS ConfigurationId,
		berechnung_id AS CalculationId,
		bs_achse AS Axle,
		bs_anzahl AS Count,
		bs_hebellaenge AS LeverLength,
		zylinder_id AS CylinderId
	FROM t_bestueckung ;

DROP VIEW if exists BrakeSystem ;
CREATE VIEW BrakeSystem AS
	SELECT ba_key AS Id,
		ba_id AS BrakeSystemId,
		hersteller_id AS ManufacturerId,
		ba_bezeichnung AS Description,
		ba_beschreibung AS Explanation,
		ba_abs AS ABS,
		ba_ebs AS EBS,
		ba_berechtigung AS Rights,
		ba_typ AS TrailerTypeNo,
		ba_art AS TrailerSubTypeNo,
		ba_sortierung AS Sorting,
		ba_letzteaenderung AS LastUpdate,
		ba_nachfolger AS Successor,
		ba_ventilfolgebezeichnung AS ValveSequenceDescription,
		ba_achsenanzahl AS AxleCount,
		ba_standort AS Location,
		ba_lokal AS Local,
		ba_user AS UserId,
		ba_nutzung AS TUsage,
		ba_manuell AS Manual,
		ba_aktuell AS Current,
		ba_intern AS Internal,
		ba_benutzeranlage AS UserConfiguration,
		ba_anzahlalbregler AS ALBCount,
		ba_albtyp AS ALBType,
		ba_beschreibung_wortindex AS WordIndexDescription,
		ba_letzteaenderungticks AS LastUpdateTicks,
		ba_saveid AS Checksum
	FROM t_bremsanlage ;

DROP VIEW if exists ValveSequence ;
CREATE VIEW ValveSequence as
	SELECT vf_key AS Id,
		vf_id AS ValveSequenceId,
		bremsanlage_id AS BrakeSystemId,
		ventil_id AS ValveId,
		vf_bezeichnung AS Description,
		vf_achsnummer AS AxleNo,
		vf_ventilnummer AS ValveNo,
		vf_verbunden_up AS ConnectedUp,
		vf_verbunden_down AS ConnectedDown,
		vf_user AS User,
		vf_lokal AS Local,
		vf_saveid AS Checksum
	FROM t_ventilfolge ;

DROP VIEW if exists Customer ;
CREATE VIEW Customer as
	SELECT k_key AS Id,
		k_id AS CustomerId,
		k_bezeichnung AS Name,
		k_kostenstelle AS CostCenter,
		k_lokal AS Local
	FROM t_kunden ;

#DROP VIEW if exists AirSuspension ;
#CREATE VIEW AirSuspension as
#	SELECT l_key AS Id,
#		l_id AS AirSuspensionId,
#		l_hersteller AS ManufacturerName,
#		l_anzeiegename AS DisplayName,
#		l_ FROM t_luftfederparameter
#

DROP VIEW if exists Updates ;
CREATE VIEW Updates as
	SELECT up_key AS Id,
		up_id AS UpdateId,
		up_datenbank AS UpDatabase,
		up_sicherung AS UpBackup,
		up_datum AS UpdDate,
		up_datenbankversion AS DatabaseVersion,
		up_rechnername AS PCName,
		up_datenversion AS DataVersion,
		up_aenderungsindex AS ChangeIndex,
		up_sqlstring AS SQLCommand,
		up_datumticks AS DateTicks
	FROM t_updates ;
