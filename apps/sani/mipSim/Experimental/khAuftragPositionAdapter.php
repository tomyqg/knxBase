<?php

/**
 * Class _Position
 *
 * Basis Klasse für:
 * - Austragsposition
 * - Rechnungsposition
 * - Lieferscheinposition
 * - RMA Position
 *
 * Diese Klasse sollte nicht eigenständig instanziiert werden.
 *
 */
class   _Position           extends FDbObject    {

    const BerechnungsModusEK    =   0 ;
    const BerechnungsModusKK    =   1 ;
    const BerechnungsModusUVPVK =   2 ;
    /**
     * @return  float
     */
    public function _detMwst() {
        $myMwst =   new _Mwst() ;
        if ( $myMwst->setId( $this->satzMwstId)) {
            switch ( $myMwst->exklusiv) {
                case    _Mwst::inMwst   :                   //  Mwst. ist im Betrag enthalten, d.h. wir gehen vom Brutto aus
                    $this->abgabePreisMwst      =   round( ( $this->abgabePreisBrutto / ( 1.00 + $myMwst->satz / 100.00) * ( $myMwst->satz / 100.00)), 2) ;
                    $this->abgabePreisNetto     =   round( ( $this->abgabePreisBrutto - $this->abgabePreisMwst), 2) ;
                    $this->kTAnteilMwst         =   round( ( $this->kTAnteilBrutto / ( 1.00 + $myMwst->satz / 100.00) * ( $myMwst->satz / 100.00)), 2) ;
                    $this->kTAnteilNetto        =   round( ( $this->kTAnteilBrutto - $this->kTAnteilMwst), 2) ;
                    break ;
                case    _Mwst::exMwst   :
                    $this->abgabePreisMwst      =   round( ( ( $this->abgabePreisNetto * $myMwst->satz) / 100.00), 2) ;
                    $this->abgabePreisBrutto    =   round( ( $this->abgabePreisNetto + $this->abgabePreisMwst), 2) ;
                    $this->kTAnteilMwst         =   round( ( ( $this->kTAnteilNetto * $myMwst->satz) / 100.00), 2) ;
                    $this->kTAnteilBrutto       =   round( ( $this->kTAnteilNetto + $this->kTAnteilMwst), 2) ;
                    break ;
            }
        }
    }

    /**
     * ermittelt den Abgabepreis, basierend auf der vom Benutzer eingestellten Art der Berechnung, i.e. {KK-Preis; EK-Preis; (UVP)VK-Preis
     * Berechunngsarten:
     *  KT-Preis
     *      AbgabePreis =   KT-Anteil +
     *  EK-Preis
     *      AbgabePreis =   EK-Preis +/- Auf/Ab-Schläge + Arbeitszeit
     *      KT-Anteil   =   Abgabepreis - Zulage
     *  UVP/VK-Preis
     *      AbgabePreis =   UVP/VK-Preis +/- Auf/Abschläge + Arbeitszeit
     *      KT-Anteil   =   Abgabepreis - Zulage
     */
    public function _detAbgabePreis() {
        $myMwst =   new _Mwst() ;
        if ( $myMwst->setId( $this->satzMwstId)) {
            switch ( $this->berechnungsModus){
                case    _Position::BerechnungsModusEK   :       // Wichtig: unser EK ist immer ein netto-Preis
                    switch($myMwst->exklusiv){
                        case    _Mwst::inMwst   :
                            $eKMwst                     =   round(( $this->eKPreis * ( $myMwst->satz / 100.00)), 2) ;
                            $eKBruttoPreis              =   round(( $this->eKPreis + $eKMwst), 2) ;
                            $this->abgabePreisBrutto    =   round(( $eKBruttoPreis * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                            $this->kTAnteilBrutto       =   round(( $eKBruttoPreis * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                            break ;
                        case    _Mwst::exMwst   :
                            $this->abgabePreisNetto     =   round(( $this->eKPreis * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                            $this->kTAnteilNetto        =   round(( $this->eKPreis * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                            break ;
                    }
                    break;
                case    _Position::BerechnungsModusKK   :
                    $this->abgabePreisNetto     =   $this->kTAnteilNetto ;
                    $this->abgabePreisBrutto    =   $this->kTAnteilBrutto ;
                    break;
                case    _Position::BerechnungsModusUVPVK   :
                    switch($myMwst->exklusiv){
                        case    _Mwst::inMwst   :
                            $this->abgabePreisBrutto    =   round(( $this->uVP * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                            $this->kTAnteilBrutto       =   round(( $this->uVP * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                            break ;
                        case    _Mwst::exMwst   :
                            $uVPMwst                    =   round(( $this->uVP / ( 1.00 + $myMwst->satz / 100.00) * ( $myMwst->satz / 100.00)), 2) ;
                            $uVPNetto                   =   round(( $this->uVP - $uVPMwst), 2) ;
                            $this->abgabePreisNetto     =   round(( $uVPNetto * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                            $this->kTAnteilNetto        =   $this->abgabePreisNetto ;
                            break ;
                    }
                    break;
            }
        }
    }
}
/**
 * Class __AuftragPositionAdapter
 *
 * Sorgt dafür, dass sich eine Auftragsposition so darstellt wir man es erwartet.
 * Zusätzlich zu den in der Datenbank vorhandenen Attribute werden weitere, virtuelle Attribute definiert.
 * Im zukünftigen Design sollten diese virtuellen Attribute, diese werden via _postLoad nach dem Laden des
 * Datensatzes aus der Tabelle "konstruiert", nicht mehr notwendig sein, sodass der *Adapter kommentarlos wegfallen kann.
 */
class   __AuftragPositionAdapter   extends _Position {
    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "id"                    => "ap_id"
    ,   "auftragId"             => "ap_AuftragId"                   // Verweis auf auftrag::Id

        //  Beschreibung der Auftragsposition

    ,   "posNo"                 => "ap_PosNo"
    ,   "uPosNo"                => "ap_UPosNo"
    ,   "artikelNr"             => "ap_ArtId"
    ,   "hMV"                   => "ap_HMV"
    ,   "hMV2"                  => "ap_HMV2"
    ,   "eAN"                   => "ap_EAN"
    ,   "pZN"                   => "ap_PZN"
    ,   "bezeichnung1"          => "ap_Bez1"
    ,   "bezeichnung2"          => "ap_Bez2"
    ,   "bezeichnung3"          => "ap_Bez3"
    ,   "lEGS"                  => "ap_LEGS"

        //  Basis Preise

    ,   "eKPreis"               => "ap_PREISEKNETTO"                // Einkaufspreis des Artikels   ( => Berechnungsmode : 0)
    ,   "preisDB"               => "ap_PREISDB"                     // Kostentraegeranteil          ( => Berechnungsmode : 1)
    ,   "uVP"                   => "ap_UVP"                         // UVP des Hersteller           ( => Berechnungsmode : 2)
    ,   "satzMwstId"            => "ap_MWSTKEY"                     // Id des Mehrwertsteuersatzes

        //

    ,   "menge"                 => "ap_Menge"                       // Verkaufte Menge
    ,   "mengeZuzahlung"        => "ap_ZuzahlungWirtEinheit"        // Anzahl der Einheiten auf der Zuzahlung basiert
    ,   "mengeWirtEinheit"      => "ap_AnteilKundeWirtEinheit"      // ...

        //  Abgabepreise + Mwst.

    ,   "bausatzZielpreis"      => "ap_gesamtpreis_bs_user"         // Zielpreisvorgabe für Bausatzkalkulation
    ,   "bausatzId"             => "ap_bausatz_id"                  // Id des Bausatzes, Verweis auf =>

    ,   "abgabePreisNetto"      => "ap_VKPREISNETTO"                // Netto Verkaufspreis pro Stueck
    ,   "abgabePreisMwst"       => "ap_VKMWST"                      // Mehrwertsteuer pro Stueck
    ,   "abgabePreisBrutto"     => "ap_VKPREISBRUTTO"               // Brutto Verkaufspreis pro Stueck

        // Kostentraegeranteil

    ,   "kTAnteilNetto"         => "ap_PREISNETTO"                  // Netto-Kostentraegeranteil pro Stueck
    ,   "kTAnteilMwst"          => "ap_MWST"                        // Mehrwertsteuer-Kostentraegeranteil pro Stueck
    ,   "kTAnteilBrutto"        => "ap_PREISBRUTTO"                 // Brutto-Kostentraegeranteil pro Stueck

        //  Zuzahlung, Eigenanteil, Zulage

    ,   "modusZuzahlung"        => "ap_Zuzahlungsmode"              // Zuzahlungsmodus, s=System, m=Manuelle Vorgabe
    ,   "zuzahlungBrutto"       => "ap_AnteilKundeZuzahlungPos"
    ,   "gesamtZuzahlungBrutto" => "ap_AnteilKundeZuzahlungGUPos"   // Gesamtbetrag Zuzahlung
    ,   "EigenanteilBrutto"     => "ap_FesterEigenanteilKunde"      // Fester Eigenanteil pro Stueck
//    ,   "GesamtEigenanteilBrutto"     => "ap_FesterEigenanteilGUKunde"    // Gesamtbetrag Fester Eigenanteil pro Stueck
    ,   "zulageBrutto"          => "ap_AnteilKundeWirtAufPos"       // Qualitaetszulage pro Stueck
    ,   "gesamtZulageBrutto"    => "ap_AnteilKundeWirtAufGUPos"     // Gesamtbetrag Qualitaetszulage

        //  Daten fuer die Preisberechnung

    ,   "berechnungsModus"      => "ap_Berechnungsmode"             // 0= EK (Ref.-Preis: EKPreis), 1= KK (Ref.-Preis: PreisDB), 2= VK (Ref.-Preis: UVP)
    ,   "korrekturProzent"      => "ap_Prozaufabschlag"             // Prozentuale Korrektur des Abgabepreises
    ,   "korrekturAbsolut"      => "ap_AbsAufabschlag"              // Prozentuale Korrektur des Abgabepreises

        //  Daten fuer Buchhaltung etc.

    ,   "warengruppe"           => "ap_WGR"                         // Aus der Warengruppe wird das ...
    ,   "erloeskonto"           => "ap_erloeskto"                   // ... Erloeskonto ermittelt

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
        error_log( __CLASS__."::".__METHOD__) ;
        $this->_detGesamtEigenanteilBrutto() ;
    }
}
