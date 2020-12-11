<?php

/**
 * KassenbuchKopf
 */
class   _KassenBelegAdapter extends FDbObject {

    public $GesamtNetto         =   0.00 ;
    public $GesamtMwst          =   0.00 ;
    public $GesamtBrutto        =   0.00 ;
    public $GesamtAbgabeNetto   =   0.00 ;
    public $GesamtAbgabeMwst    =   0.00 ;
    public $GesamtKTAnteilNetto     =   0.00 ;
    public $GesamtKTAnteilMwst      =   0.00 ;
    public $GesamtKTAnteilBrutto    =   0.00 ;
    public $GesamtZuzahlungNetto   =   0.00 ;
    public $GesamtZuzahlungMwst    =   0.00 ;
//    public $GesamtZuzahlungBrutto  =   0.00 ;
    public $GesamtEigenanteilNetto   =   0.00 ;
    public $GesamtEigenanteilMwst    =   0.00 ;
//    public $GesamtEigenanteilBrutto  =   0.00 ;
    public $GesamtZulageNetto   =   0.00 ;
    public $GesamtZulageMwst    =   0.00 ;
//    public $GesamtZulageBrutto  =   0.00 ;
    public $GesamtPatientNetto  =   0.00 ;
    public $GesamtPatientMwst   =   0.00 ;
    public $GesamtPatientMwst1  =   0.00 ;
    public $GesamtPatientMwst2  =   0.00 ;
    public $GesamtPatientBrutto =   0.00 ;

    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "Id"                    => "kk_id"
    ,   "Filiale"               => "kk_filiale"
    ,   "IdentId"               => "kk_identid"
    ,   "SelIdent"              => "kk_selident"
    ,   "IdIdent"               => "kk_Idident"
    ,   "KassiererId"           => "kk_kassiererid"
    ,   "UserId"                => "kk_userid"
    ,   "BelegNr"               => "kk_belegid"
    ,   "KundeNr"               => "kk_kunid"
    ,   "Datum"                 => "kk_datum"
    ,   "RefBelegNr"            => "kk_vbeleg"
    ,   "GesamtBetrag"          => "kk_kasse_betrag"                // Gesamtbetrag des Belegs
    ,   "GesamtNetto1"          => "kk_kassebe_netto1"              // Gesamtbetrag Netto Mwstsatz 1
    ,   "GesamtNetto2"          => "kk_kassebe_netto2"              // Gesamtbetrag Netto Mwstsatz 2
    ,   "GesamtNetto3"          => "kk_kassebe_netto3"
    ,   "GesamtNetto4"          => "kk_kassebe_netto4"
    ,   "GesamtMwst1"           => "kk_kassebe_mwst1"               // Gesamtbetrag Mehrwertsteuer 1
    ,   "GesamtMwst2"           => "kk_kassebe_mwst2"               // GEsamtbetrag Mehrwertsteuer 2
    ,   "GesamtMwst3"           => "kk_kassebe_mwst3"
    ,   "GesamtMwst4"           => "kk_kassebe_mwst4"
    ,   "Waehrung"              => "kk_kassen_waehrung"
    ,   "VorgangArt"            => "kk_vorgangsart"
    ,   "VorgangId"             => "kk_vorgangsid"
    ,   "GesamtAbgabeBrutto"    => "kk_gesamtbetrag"                // Gesamtbetrag des Auftrags = Kostentraeger + Zuzahlung + Eigenanteil + Zulage
    ,   "GesamtAbgabeMwst1"     => "kk_gber_mwst1"                  // Gesamtbetrag Mehrwertsteuer 1 des Auftrags (wie oben)
    ,   "GesamtAbgabeMwst2"     => "kk_gber_mwst2"                  // Gesamtbetrag Mehrwertsteuer 2 des Auftrags (wie oben)
    ,   "GesamtAbgabeMwst3"     => "kk_gber_mwst3"
    ,   "GesamtZuzahlungBrutto" => "kk_zuz_betrag"                  // Gesamtbetrag der Zuzahlung
    ,   "GesamtZuzahlungMwst1"  => "kk_zuz_mwst1"                   // Gesamtbetrag Mehrwertsteuer 1 der Zuzahlung
    ,   "GesamtZuzahlungMwst2"  => "kk_zuz_mwst2"                   // Gesamtbetrag Mehrwertsteuer 2 der Zuzahlung
    ,   "GesamtEigenanteilBrutto" => "kk_eig_betrag"                // Gesamtbetrag des Eigenanteils
    ,   "GesamtEigenanteilMwst1"  => "kk_eig_mwst1"                 // Gesamtbetrag Mehrwertsteuer 1 des Eigenanteils
    ,   "GesamtEigenanteilMwst2"  => "kk_eig_mwst2"                 // Gesamtbetrag Mehrwertsteuer 2 des Eigenanteils
    ,   "GesamtZulageBrutto"    => "kk_qzu_betrag"                  // Gesamtbetrag der Qualitaetszulage
    ,   "GesamtZulageMwst1"     => "kk_qzu_mwst1"                   // Gesamtbetrag Mehrwertsteuer 1 der Qualitaetszulage
    ,   "GesamtZulageMwst2"     => "kk_qzu_mwst2"                   // Gesamtbetrag Mehrwertsteuer 2 der Qualitaetszulage
    ,   "SatzMwst1"             => "kk_satz_mwst1"                  // Mehrwertsteuersatz der Mehrwertsteuer 1, ie. 0,07 oder 0,19
    ,   "SatzMwst2"             => "kk_satz_mwst2"                  // dto. der Mehrwertsteuer 2
    ,   "Storno"                => "kk_storno"                      // Stornobeleg "J"a oder ""
    ,   "StornoGrund"           => "kk_stornogrund"
    ,   "StornoReferenzBelegNr" => "kk_storno_kbid"                 // Belegnr. des stornierten Beleges
    ,   "BetragGegeben"         => "kk_gegeben"                     // Betrag der vom Kunden gegeben wurde
    ,   "BetragRueckgabe"       => "kk_rest"                        // Betrag der an den Kunden zurueck gegeben wurde
    ,   "BetragAnzahlung"       => "kk_anzahlung"                   //
    ,   "BetragRest"            => "kk_restsumme"
    ,   "Buchungstext"          => "kk_text"
    ,   "DateiName"             => "kk_file"
    ,   "ExportKZ"              => "kk_exportkz"
    ,   "Kostenstelle1"         => "kk_kostst1"
    ,   "Kostenstelle2"         => "kk_kostst2"
    ,   "AuftragId"             => "kk_aufid"
    ,   "RechnungNr"            => "kk_rechnr"
    ) ;

    /**
     *
     */
    protected   function _postInstantiate() {
        error_log( __CLASS__."::".__METHOD__) ;
        $this->regTransTable( self::$nameTransTable) ;
    }

    /**
     *
     */
    protected   function _postLoad() {
        error_log( __CLASS__."::".__METHOD) ;
        $this->GesamtZuzahlungMwst  =   $this->GesamtZuzahlungMwst1 + $this->GesamtZuzahlungMwst2 ;
        $this->GesamtPatientBrutto  =   $this->GesamtZuzahlungBrutto + $this->GesamtEigenanteilBrutto + $this->GesamtZulageBrutto ;
        $this->GesamtPatientMwst1   =   $this->GesamtZuzahlungMwst1 + $this->GesamtEigenanteilMwst1 + $this->GesamtZulageMwst1 ;
        $this->GesamtPatientMwst2   =   $this->GesamtZuzahlungMwst2 + $this->GesamtEigenanteilMwst2 + $this->GesamtZulageMwst2 ;

        $this->GesamtNetto  =   $this->GesamtNetto1 + $this->GesamtNetto2 ;
        $this->GesamtMwst   =   $this->GesamtMwst1 + $this->GesamtMwst2 ;
        $this->GesamtBrutto =   $this->GesamtBetrag ;
        $this->GesamtAbgabeMwst     =   $this->GesamtAbgabeMwst1 + $this->GesamtAbgabeMwst2 ;
        $this->GesamtAbgabeNetto    =      $this->GesamtAbgabeBrutto - $this->GesamtAbgabeMwst ;
        $this->GesamtZuzahlungMwst      =   $this->GesamtZuzahlungMwst1 + $this->GesamtZuzahlungMwst2 ;
        $this->GesamtZuzahlungNetto     =      $this->GesamtZuzahlungBrutto - $this->GesamtZuzahlungMwst ;
        $this->GesamtEigenanteilMwst    =   $this->GesamtEigenanteilMwst1 + $this->GesamtEigenanteilMwst2 ;
        $this->GesamtEigenanteilNetto   =      $this->GesamtEigenanteilBrutto - $this->GesamtEigenanteilMwst ;
        $this->GesamtZulageNetto    =      $this->GesamtZulageBrutto - $this->GesamtZulageMwst ;
        $this->GesamtZulageMwst     =   $this->GesamtZulageMwst1 + $this->GesamtZulageMwst2 ;
        $this->GesamtKTAnteilNetto  =   $this->GesamtAbgabeNetto - $this->GesamtZuzahlungNetto - $this->GesamtEigenanteilNetto - $this->GesamtZulageNetto ;
        $this->GesamtKTAnteilMwst   =   $this->GesamtAbgabeMwst - $this->GesamtZuzahlungMwst - $this->GesamtEigenanteilMwst - $this->GesamtZulageMwst ;
        $this->GesamtKTAnteilBrutto =   $this->GesamtKTAnteilNetto + $this->GesamtKTAnteilMwst ;
        $this->GesamtPatientNetto   =   $this->GesamtZuzahlungNetto + $this->GesamtEigenanteilNetto + $this->GesamtZulageNetto ;
        $this->GesamtPatientMwst    =   $this->GesamtZuzahlungMwst + $this->GesamtEigenanteilMwst + $this->GesamtZulageMwst ;
        $this->GesamtPatientBrutto  =   $this->GesamtPatientNetto + $this->GesamtPatientMwst ;
    }
}
