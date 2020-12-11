<?php

/**
 * Class _Auftrag
 *
 * Anmerkung
 * Aufgrund des unterschiedlichen Handlings der Mehrwertsteuer, sowohl Brutto als auch Netto Beträge koennen,
 * je nach LEGS Daten, als Kalkulationsbasis dienen, wird die Mehrwertsteuer explizit mitgefuehrt und
 * lediglich auf Positionsebene errechnet.
 *
 */
class   _Auftrag   extends FDbObject    {

    /**
     *  die folgenden Attribute existiern (noch) nicht in der Tabelle und muessen daher explizit erzeugt werden!
     */
    public  $gesamtEigenanteilBrutto    =   0.00 ;
    public  $lock    =   false ;

    /**
     * @var array   translation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "id"                    => "a_id"
    ,   "auftragNr"             => "a_AuftragId"
    ,   "auftragArt"            => "a_AuftragArt"
    ,   "datum"                 => "a_Datum"
    ,   "userId"                => "a_UserId"
    ,   "verkaeufer"            => "a_Verkaeufer"
    ,   "sachbearbeiter"        => "a_Sachbearbeiter"
    ,   "iP"                    => "a_IP"                           //  IP Adresse der letzten Bearbeitung
    ,   "fa"                    => "a_Fa"
    ,   "mandant"               => "a_Mandant"
    ,   "filiale"               => "a_Filiale"
    ,   "geliefertDurch"        => "a_GeliefertDurch"
    ,   "kasseId"               => "a_Kasse"

    //  Kundenbezogene Daten

    ,   "zuzahlungsBefreiung"   => "a_Befreit"
    ,   "zuzahlungsBefreiungVon"=> "a_DatumBefreitVon"
    ,   "zuzahlungsBefreiungBis"=> "a_DatumBefreitBis"

    //  Mwst. bezogene Daten

    ,   "belegNr"               => "a_BelegNr"                      //  Belegnr. des opt. zugehoerigen Kassenbelegs

    //  Summen der Abgabebetraege

    ,   "gesamtAbgabeNetto"    => "a_GesamtvkpreisNetto"            // Gesamtsumme der Gesamtnettobetraege ueber alle Auftragspositionen
    ,   "gesamtAbgabeBrutto"   => "a_GesamtvkpreisBrutto"           // Gesamtsumme der Gesamtbruttobetraege ueber alle Auftragspositionen

    //  Summen der Kostentraegeranteile

    ,   "gesamtKTAnteilNetto"      => "a_GesamtpreisNetto"          // Gesamtsumme des Netto-Kostentraegeranteils
    ,   "gesamtKTAnteilBrutto"     => "a_GesamtpreisBrutto"         // Gesamtsumme des Brutto-Kostentraegeranteils

    //

    ,   "gesamtZuzahlungNetto"  => "a_GesamtZuzahlungNetto"         // Gesamtbetrag der Zuzahlungen netto
    ,   "gesamtZuzahlungBrutto" => "a_GesamtZuzahlung"              // Gesamtbetrag der Zuzahlungen

    //

    ,   "gesamtZulageNetto"     => "a_GesamtWirAufNetto"            // Gesamtbetrag der Zulagen netto
    ,   "gesamtZulageBrutto"    => "a_GesamtWirAuf"                 // Gesamtbetrag der Zulagen

        //  Summen der privat zu zahlenden Anteile

    ,   "gesamtPatientNetto"     => "a_GesamtpreisPrivatNetto"       // Gesamtsumme der Netto-Privatzahlung = Zuzahlung + Eigenanteil + Zulage
    ,   "gesamtPatientBrutto"    => "a_GesamtpreisPrivat"            // Gesamtsumme der Privatzahlung = Zuzahlung + Eigenanteil + Zulage

        //  Konsolidierung der Zahlungsdaten

    ,   "betragGezahlt"         => "a_GezahltBetrag"                //
    ,   "betragGezahltPrivat"   => "a_GezahltBetragEA"              //
    ,   "betragGezahltKT"       => "a_GezahltBetragKK"              //
    ,   "betragGezahltAZ"       => "a_GezahltBetragAZ"              //
    ) ;

    private $myFiliale  =   null ;

    /**
     * _Auftrag constructor.
     */
    public  function    __construct() {
        parent::__construct( "auftrag", "a_id", "kun") ;
        $this->regTransTable( self::$nameTransTable) ;
        $this->setIdKey( "a_id") ;
        $this->_getFiliale() ;
    }

    /**
     * @param   int $_id
     * @return  void
     */
    public  function    setId( $_id=-1) {
        parent::setId( $_id) ;
        if ( $this->isValid()) {
            $this->_getFiliale() ;
        }
    }

    /**
     * @param string $_key
     * @return  void
     */
    public  function    setKey( $_key) {
        $this->fetchFromDbWhere( "auftragNr = '$_key'") ;
        $this->_getFiliale() ;
    }

    /**
     *
     * @return  void
     */
    private function    _getFiliale() {
       if ( $this->isValid()) {
            $this->myFiliale  =   new _Filiale( $this->a_Filiale) ;
            if ( $this->myFiliale->isValid()) {
                //                $this->myFiliale->__dump() ;
            }
        }
    }

    /**
     *
     */
    public function    deepConsolidate() {

        /**
         * @var  _AuftragPosition   $item
         */
        $myOrderItem  =   new _AuftragPosition() ;
        $myOrderItem->setIterCond( "auftragId = " . $this->id) ;
        $myOrderItem->setIterOrder( array("posNo", "uPosNo")) ;
        foreach( $myOrderItem as $idx => $item){
            $item->deepConsolidate($this);
        }

        /**
         *
         */
        $this->consolidate() ;
    }
    /**
     *
     * @return  void
     */
    public function    consolidate() {
        /**
         *
         */
        $this->__dump() ;
        $this->gesamtAbgabeBrutto       =   0.000 ;     //
        $this->gesamtAbgabeNetto        =   0.000 ;     //
        $this->gesamtKTAnteilBrutto     =   0.000 ;     //
        $this->gesamtKTAnteilNetto      =   0.000 ;     //
        $this->gesamtZuzahlungBrutto    =   0.000 ;
        $this->gesamtEigenanteilBrutto  =   0.000 ;
        $this->gesamtZulageBrutto       =   0.000 ;
        $this->gesamtPatientBrutto      =   0.000 ;     // = Zuzahlung + Eigenanteil + Zulage

        /**
         * @var  _AuftragPosition   $item
         */
        $myOrderItem  =   new _AuftragPosition() ;
        $myOrderItem->setIterCond( "auftragId = " . $this->id) ;
        $myOrderItem->setIterOrder( array("posNo", "uPosNo")) ;
        foreach( $myOrderItem as $idx => $item) {
            $item->consolidate( $this) ;
            $this->gesamtAbgabeBrutto       +=  $item->gesamtAbgabePreisBrutto ;
            $this->gesamtAbgabeNetto        +=  $item->gesamtAbgabePreisNetto ;
            $this->gesamtKTAnteilBrutto     +=  $item->gesamtKTAnteilBrutto ;
            $this->gesamtKTAnteilNetto      +=  $item->gesamtKTAnteilNetto ;
            $this->gesamtZuzahlungBrutto    +=  $item->gesamtZuzahlungBrutto ;
            $this->gesamtEigenanteilBrutto  +=  $item->gesamtEigenanteilBrutto ;
            $this->gesamtZulageBrutto       +=  $item->gesamtZulageBrutto ;
        }
        $this->gesamtPatientBrutto   =   $this->gesamtZuzahlungBrutto + $this->gesamtEigenanteilBrutto + $this->gesamtZulageBrutto ;
        $this->__dump() ;

//        $posNo  =   10 ;
//        foreach( $myOrderItem as $idx => $item) {
//            $item->PosNo     =   $posNo ;
//            $item->ap_UPosNo    =   null ;
//            $posNo  +=  10 ;
//            $item->updateColInDb( "ap_PosNo,ap_UPosNo") ;
//        }
        $this->updateInDb() ;
    }
    /**
     * PARKING RANGE
     *
    $this->addItem( "0803021000") ;                     // add a well known item ...
     *
     * PARKING RANGE
     */

    /**
     * @param $_articleRef
     * @param int $_qty
     * @return  void
     */
    public  function    addArticle( $_articleRef, $_qty=1) {
        /**
         * Step 1:  add the article from the article database
         */
        $myArticle  =   new _LocalArticle( $_articleRef) ;
        if ( ! $myArticle->isValid()) {
            $myArticle  =   new _CloudArticle( $_articleRef) ;
        }
        if ( $myArticle->isValid()) {
            $myArticle->__dump() ;
            $myOrderItem    =   new _AuftragPosition() ;
            $myOrderItem->auftragId  =   $this->a_id ;
            $myOrderItem->artId      =   $myArticle->mart_artnr ;
            $myOrderItem->bezeichnung1  =   $myArticle->mart_bezeichnung ;
            $myOrderItem->bezeichnung2  =   $myArticle->mart_bezeichnung2 ;
            $myOrderItem->bezeichnung3  =   $myArticle->mart_bezeichnung3 ;
            $myOrderItem->ap_menge  =   $_qty ;
            $myOrderItem->storeInDb() ;
        } else {
        }
        /**
         * Step 2:  fill up the item with the data from KK Vertrag
         */
        $myHMVVertragData   =   new _VertragHMVData() ;
        $myHMVVertragData->getHMVData( $this->myFiliale->f_ik, $this->a_IKNummer, $myArticle->mart_hmv, "00") ;

    }

    /**
     * @param $_befreiung
     * @return  void
     */
    public function setBefreiung( $_befreiung) {
        $this->ZuzahlungsBefreiung  =   $_befreiung ;
        $this->consolidate() ;
        $this->updateInDb() ;
    }

    /**
     * @param string $_le
     * @return  void
     */
    public  function    __dump( $_le="\n") {
        error_log( parent::__dump()) ;
        $myOrderItem  =   new _AuftragPosition() ;
        $myOrderItem->setIterCond( "ap_AuftragId = " . $this->a_id) ;
        foreach( $myOrderItem as $idx => $item) {
            error_log( $item->__dump()) ;
        }
    }
}

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
     *      KT-Anteil   =   ->Abgabepreis - Zulage
     */
    public function _detAbgabePreis() {
        $myMwst =   new _Mwst() ;
        if ( $myMwst->setId( $this->satzMwstId)) {
            switch ( $this->berechnungsModus){
            case    _Position::BerechnungsModusEK   :       // Wichtig: unser EK ist immer ein netto-Preis
                switch($myMwst->exklusiv){
                case    _Mwst::inMwst   :
                    $eKMwst                     =   round( ( $this->eKPreis * ( $myMwst->satz / 100.00)), 2) ;
                    $eKBruttoPreis              =   round( ( $this->eKPreis + $eKMwst), 2) ;
                    $this->abgabePreisBrutto    =   round( ( $eKBruttoPreis * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                    $this->kTAnteilBrutto       =   round( ( $eKBruttoPreis * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                break ;
                case    _Mwst::exMwst   :
                    $this->abgabePreisNetto =   round( ( $this->eKPreis * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                    $this->kTAnteilNetto    =   round( ( $this->eKPreis * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
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
                    $this->abgabePreisBrutto    =   round( ( $this->uVP * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                    $this->kTAnteilBrutto       =   round( ( $this->uVP * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                break ;
                case    _Mwst::exMwst   :
                    $uVPMwst                    =   round( ( $this->uVP / ( 1.00 + $myMwst->satz / 100.00) * ( $myMwst->satz / 100.00)), 2) ;
                    $uVPNetto                   =   round( ( $this->uVP - $uVPMwst), 2) ;
                    $this->abgabePreisNetto     =   round( ( $uVPNetto * ( 1.00 + ( $this->korrekturProzent / 100.00))) + $this->korrekturAbsolut, 2) ;
                    $this->kTAnteilNetto        =   $this->abgabePreisNetto ;
                break ;
                }
            break;
            }
        }
    }
}
class   _AuftragPosition    extends _Position    {

    const	ModusZuzahlungManuell   =   "m" ;
    const	ModusZuzahlungSystem    =   "" ;
    const	ModusZuzahlungSystemS   =   "s" ;

    // Gesamtbetrag des Abgabepreises für diese Position (Menge * Preis)

    public  $gesamtAbgabePreisNetto         =   0.00 ;
    public  $gesamtAbgabePreisMwst          =   0.00 ;
    public  $gesamtAbgabePreisBrutto        =   0.00 ;

    // Gesamtbetrag des Kostenträgeranteils (Menge * KT-Anteil)

    public  $gesamtKTAnteilNetto         =   0.00 ;
    public  $gesamtKTAnteilMwst          =   0.00 ;
    public  $gesamtKTAnteilBrutto        =   0.00 ;

    public  $gesamtKomponentenPreisNetto    =   0.00 ;
    public  $gesamtKomponentenPreisMwst     =   0.00 ;
    public  $gesamtKomponentenPreisBrutto   =   0.00 ;

    public  $gesamtEigenanteilNetto   =   0.00 ;
    public  $gesamtEigenanteilMwst    =   0.00 ;
    public  $gesamtEigenanteilBrutto  =   0.00 ;

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
    ,   "gesamtZuzahlungBrutto" => "ap_AnteilKundeZuzahlungGUPos"   // Gesamtbetrag Qualitaetszuzahlung
    ,   "eigenanteilBrutto"     => "ap_FesterEigenanteilKunde"      // Fester Eigenanteil pro Stueck
//    ,   "GesamtEigenanteil"     => "ap_FesterEigenanteilGUKunde"    // Gesamtbetrag Fester Eigenanteil pro Stueck
    ,   "zulageBrutto"          => "ap_AnteilKundeWirtAufPos"       // Qualitaetszuzahlung pro Stueck
    ,   "gesamtZulageBrutto"    => "ap_AnteilKundeWirtAufGUPos"     // Gesamtbetrag Qualitaetszuzahlung

    //  Daten fuer die Preisberechnung

    ,   "berechnungsModus"      => "ap_Berechnungsmode"             // 0= EK (Ref.-Preis: EKPreis), 1= KK (Ref.-Preis: PreisDB), 2= VK (Ref.-Preis: UVP)
    ,   "korrekturProzent"      => "ap_Prozaufabschlag"             // Prozentuale Korrektur des Abgabepreises
    ,   "korrekturAbsolut"      => "ap_AbsAufabschlag"              // Prozentuale Korrektur des Abgabepreises

    //  Daten fuer Buchhaltung etc.

    ,   "warengruppe"           => "ap_WGR"                         // Aus der Warengruppe wird das ...
    ,   "erloeskonto"           => "ap_erloeskto"                   // ... Erloeskonto ermittelt

    ) ;

    /**
     * _AuftragPosition constructor.
     */
    public  function    __construct() {
        parent::__construct( "auf_pos", "ap_id", "kun") ;
        $this->regTransTable( self::$nameTransTable) ;
        $this->setIdKey( "ap_id") ;
    }

    /**
     * deepConsolidate
     *
     * Konsolidiert die Auftragsposition auf Basis der fundamentalsten Informationen:
     *
     *      ArtikelNr, HMVNr, LKZ, Leistungserbringer, Kostenträger
     *
     * Alle Benutzermodifizierten Daten gehen dabei verloren. Das Vorgehen entspricht vom
     * Grundsatz dem Einfügen einer neuen Auftragsposition.
     *
     * @param $_auftrag _Auftrag
     */
    public function deepConsolidate( $_auftrag) {
        $this->consolidate( $_auftrag) ;
    }
    /**
     * Methode consolidate
     *
     * Auftragsposition konsolidieren.
     *
     * Reihenfolge der Operationen:
     *
     *  1. Mehrwertsteuer-Beträge der Einzelpositionen ermitteln
     *  2. Zulage der Bausaetze ermitteln
     *  3. Zuzahlung der Hauptpositionen ermitteln
     *
     * @param $_auftrag
     * @return  void
     */
    public  function    consolidate( $_auftrag) {
        /**
         *  determine total 'Brutto' for this item
         */

        $this->gesamtAbgabePreisNetto     =   0.00 ;
        $this->gesamtAbgabePreisMwst      =   0.00 ;
        $this->gesamtAbgabePreisBrutto    =   0.00 ;
        $this->gesamtEigenanteilNetto     =   0.00 ;
        $this->gesamtEigenanteilMwst      =   0.00 ;
        $this->gesamtEigenanteilBrutto    =   0.00 ;

        /**
         *  1. Mehrwertsteuer-Beträge ermitteln
         */
        $this->_detAbgabePreis() ;
        $this->_detMwst() ;

        /**
         *  einfache Werte berechnen
         */
        $this->_detGesamtEigenanteilBrutto() ;        // menge * eigenanteil
        $this->_getGesamtZulageBrutto( $_auftrag) ;             // menge * zulage

        /**
         *  2. Zulage eines moeglichen Bausatzes ermitteln
         */
        if ( $this->bausatzZielpreis != 0.00) {
            $this->gesamtKomponentenPreisBrutto =   0.00 ;
            $myOrderItem  =   new _AuftragPosition() ;
            $myOrderItem->setIterCond( array( "auftragId = " . $_auftrag->id, "posNo = " . $this->posNo)) ;
            $myOrderItem->setIterOrder( array( "uPosNo")) ;
            foreach ( $myOrderItem as $item) {
                $this->gesamtKomponentenPreisBrutto +=  $item->menge * $item->kTAnteilBrutto ;
            }
            $this->zulageBrutto =   $this->bausatzZielpreis - $this->gesamtKomponentenPreisBrutto ;
        }

        /**
         *  3. Zuzahlung der Hauptpositionen ermitteln
         *
         *  WENN ich eine Unterposition bin
         *  SONST_WENN ich Unterpositionen habe
         *  SONST
         *      Zuzahlungen bestimmen
         */
        if ( $this->uPosNo != 0) {
           $this->zuzahlungBrutto    =   0.00 ;
        } else {
            $this->gesamtKTAnteilBrutto  =  $this->kTAnteilBrutto ;
            $myOrderItem  =   new _AuftragPosition() ;
            $myOrderItem->setIterCond( array( "auftragId = " . $_auftrag->id, "posNo = " . $this->posNo, "uPosNo > 0")) ;
            $myOrderItem->setIterOrder( array( "posNo", "uPosNo")) ;
            foreach ( $myOrderItem as $item) {
                $item->consolidate( $_auftrag) ;
                $this->gesamtKTAnteilBrutto  +=  $item->kTAnteilBrutto ;
            }
            $this->_detZuzahlung( $_auftrag, $this->gesamtKTAnteilBrutto) ;

        }

        /**
         * 4. Gesamtbetraege bestimmen
         */
        $this->gesamtAbgabePreisNetto   =   $this->menge * $this->abgabePreisNetto ;
        $this->gesamtAbgabePreisBrutto  =   $this->menge * $this->abgabePreisBrutto ;
        $this->gesamtKTAnteilNetto      =   $this->menge * $this->kTAnteilNetto ;
        $this->gesamtKTAnteilBrutto     =   $this->menge * $this->kTAnteilBrutto ;
//        $this->gesamtZuzahlungBrutto    =   $this->mengeZuzahlung * $this->zuzahlungBrutto ;
        $this->gesamtEigenanteilBrutto  =   $this->menge * $this->eigenanteilBrutto ;
        $this->gesamtZulageBrutto       =   $this->menge * $this->zulageBrutto ;

        $this->updateInDb() ;
    }

    /**
     * @param $_auftrag
     * @return  void
     */
    private function    _detZuzahlung( $_auftrag, $_value=0.00) {
        $zuzahlung  =   0.00 ;
        if ( $_value == 0.00) {
            $_value =   $this->kTAnteilBrutto ;
        }
        switch ( $this->modusZuzahlung) {
        case    _AuftragPosition::ModusZuzahlungSystem  :
        case    _AuftragPosition::ModusZuzahlungSystemS :
            if ( $_auftrag->zuzahlungsBefreiung == 1) {
                $this->zuzahlungBrutto   =   0.0 ;
            } else {
                switch ( $this->ap_ZUZAHLUNGSKEY) {
                case    1   :                                   // 10%, min. 5,-, max. 10,-
                    $this->zuzahlungBrutto    =   round(( $_value * 0.1), 2) ;
                    if ( $this->zuzahlungBrutto < 5.00) {
                        $this->zuzahlungBrutto   =   5.00 ;
                    } else if ( $this->zuzahlungBrutto > 10.00) {
                        $this->zuzahlungBrutto   =   10.00 ;
                    }
                    break ;
                case    2   :
                    $this->zuzahlungBrutto    =   $_value * 0.1 ;
                    if ( $this->zuzahlungBrutto > 10.00) {            // 10%, min 0,-, max. 10,-
                        $this->zuzahlungBrutto   =   10.00 ;
                    }
                    break ;
                }
            }
            $this->modusZuzahlung   =   _AuftragPosition::ModusZuzahlungSystemS ;
            break ;
        default :
            break ;
        }
        $zuzahlungBrutto  =   $this->zuzahlungBrutto ;

        $this->gesamtZuzahlungBrutto    =   ( $this->menge / $this->mengeWirtEinheit) * $this->zuzahlungBrutto ;

        return $zuzahlungBrutto ;
    }

    /**
     * @return  float
     */
    private function    _detGesamtEigenanteilBrutto() {
        $eigenanteil    =   $this->menge * $this->eigenanteilBrutto ;
        $this->gesamtEigenanteilBrutto    =   $eigenanteil ;
        return $eigenanteil ;
    }

    /**
     * @return  float
     */
    private function    _getGesamtZulageBrutto() {
        $zulage =   $this->menge * $this->zulageBrutto ;
        $this->gesamtZulageBrutto    =   $zulage ;
        return $zulage ;
    }

    /**
     *
     */
    protected   function _postLoad() {
        error_log( "AuftragPosition::_postLoad()") ;
    }
}
