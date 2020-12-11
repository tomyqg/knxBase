<?php

class   _Auftrag   extends FDbObject    {

    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "Id"                    => "a_id"
    ,   "AuftragNr"             => "a_AuftragId"
    ,   "AuftragArt"            => "a_AuftragArt"
    ,   "Datum"                 => "a_Datum"
    ,   "UserId"                => "a_UserId"
    ,   "Verkaeufer"            => "a_Verkaeufer"
    ,   "Sachbearbeiter"        => "a_Sachbearbeiter"
    ,   "IP"                    => "a_IP"                           //  IP Adresse der letzten Bearbeitung
    ,   "Fa"                    => "a_Fa"
    ,   "Mandant"               => "a_Mandant"
    ,   "Filiale"               => "a_Filiale"
    ,   "GeliefertDurch"        => "a_GeliefertDurch"
    ,   "KasseId"               => "a_Kasse"

    //  Kundenbezogene Daten

    ,   "ZuzahlungsBefreiung"   => "a_Befreit"
    ,   "ZuzahlungsBefreiungVon"=> "a_DatumBefreitVon"
    ,   "ZuzahlungsBefreiungBis"=> "a_DatumBefreitBis"

    //  Mwst. bezogene Daten

    ,   "BelegNr"               => "a_BelegeNr"                     //  Belegnr. des opt. zugehoerigen Kassenbelegs
    ,   "SatzMwst1"             => "a_MwstSatz1"                    // Mehrwertsteuersatz der Mehrwertsteuer 1, ie. 0,07 oder 0,19
    ,   "SatzMwst2"             => "a_MwstSatz2"                    // Mehrwertsteuersatz der Mehrwertsteuer 2, ie. 0,07 oder 0,19
    ,   "SatzMwst3"             => "a_MwstSatz3"                    // Mehrwertsteuersatz der Mehrwertsteuer 3, ie. 0,07 oder 0,19
    ,   "SatzMwst4"             => "a_MwstSatz4"                    // Mehrwertsteuersatz der Mehrwertsteuer 4, ie. 0,07 oder 0,19

    //  Summen der Abgabebetraege

    ,   "GesamtAbgabeNetto"    => "a_GesamtvkpreisNetto"           // Gesamtsumme der Gesamtnettobetraege ueber alle Auftragspositionen
    ,   "GesamtAbgabeNetto1"   => "a_GesamtvkpreisNetto1"          // Gesamtsumme der Gesamtnettobetraege für Mehrwertsteuer 1, typ. 0,19
    ,   "GesamtAbgabeNetto2"   => "a_GesamtvkpreisNetto2"          // Gesamtsumme der Gesamtnettobetraege für Mehrwertsteuer 2, typ. 0,07
    ,   "GesamtAbgabeNetto3"   => "a_GesamtvkpreisNetto3"          // Gesamtsumme der Gesamtnettobetraege für Mehrwertsteuer 3
    ,   "GesamtAbgabeNetto4"   => "a_GesamtvkpreisNetto4"          // Gesamtsumme der Gesamtnettobetraege für Mehrwertsteuer 4
    ,   "GesamtAbgabeBrutto"   => "a_GesamtvkpreisBrutto"          // Gesamtsumme der Gesamtbruttobetraege ueber alle Auftragspositionen
    ,   "GesamtAbgabeMwst1"    => "a_GesamtvkpreisMwSt1"           // Gesamtbetrag Mehrwertsteuer 1 auf Gesamtnetto 1
    ,   "GesamtAbgabeMwst2"    => "a_GesamtvkpreisMwSt2"           // Gesamtbetrag Mehrwertsteuer 1 auf Gesamtnetto 2
    ,   "GesamtAbgabeMwst3"    => "a_GesamtvkpreisMwSt3"           // Gesamtbetrag Mehrwertsteuer 1 auf Gesamtnetto 3
    ,   "GesamtAbgabeMwst4"    => "a_GesamtvkpreisMwSt4"           // Gesamtbetrag Mehrwertsteuer 1 auf Gesamtnetto 4

    //  Summen der Kostentraegeranteile

    ,   "GesamtPreisNetto"      => "a_GesamtPreisNetto"             // Gesamtsumme des Netto-Kostentraegeranteils
    ,   "GesamtPreisNetto1"     => "a_GesamtPreisNetto1"            // Gesamtsumme des Netto-Kostentraegeranteils fuer Mehrwertsteuer 1
    ,   "GesamtPreisNetto2"     => "a_GesamtPreisNetto2"            // Gesamtsumme des Netto-Kostentraegeranteils fuer Mehrwertsteuer 2
    ,   "GesamtPreisNetto3"     => "a_GesamtPreisNetto3"            // Gesamtsumme des Netto-Kostentraegeranteils fuer Mehrwertsteuer 3
    ,   "GesamtPreisNetto4"     => "a_GesamtPreisNetto4"            // Gesamtsumme des Netto-Kostentraegeranteils fuer Mehrwertsteuer 4
    ,   "GesamtPreisBrutto"     => "a_GesamtPreisBrutto"            // Gesamtsumme des Brutto-Kostentraegeranteils
    ,   "GesamtPreisMwst1"      => "a_GesamtpreisMwSt1"             // Gesamtbetrag der Mwst. 1
    ,   "GesamtPreisMwst2"      => "a_GesamtpreisMwSt2"             // Gesamtbetrag der Mwst. 2
    ,   "GesamtPreisMwst3"      => "a_GesamtpreisMwSt3"             // Gesamtbetrag der Mwst. 3
    ,   "GesamtPreisMwst4"      => "a_GesamtpreisMwSt4"             // Gesamtbetrag der Mwst. 4

    //  Summen der privat zu zahlenden Anteile

    ,   "GesamtPrivat"          => "a_GesamtpreisPrivat"            // Gesamtsumme der Privatzahlung = Zuzahlung + Eigenanteil + Zulage
    ,   "GesamtPrivatNetto"     => "a_GesamtpreisPrivatNetto"       // Gesamtsumme der Netto-Privatzahlung = Zuzahlung + Eigenanteil + Zulage
    ,   "GesamtPrivatNetto1"    => "a_GesamtpreisPrivatNetto1"      // Gesamtsumme der Netto-Privatzahlung für Mwst. 1 = Zuzahlung + Eigenanteil + Zulage
    ,   "GesamtPrivatNetto2"    => "a_GesamtpreisPrivatNetto2"      // Gesamtsumme der Netto-Privatzahlung für Mwst. 2 = Zuzahlung + Eigenanteil + Zulage
    ,   "GesamtPrivatNetto3"    => "a_GesamtpreisPrivatNetto3"      // Gesamtsumme der Netto-Privatzahlung für Mwst. 3 = Zuzahlung + Eigenanteil + Zulage
    ,   "GesamtPrivatNetto4"    => "a_GesamtpreisPrivatNetto4"      // Gesamtsumme der Netto-Privatzahlung für Mwst. 4 = Zuzahlung + Eigenanteil + Zulage
    ,   "GesamtPrivatMwst1"     => "a_GesamtpreisPrivatMwSt1"       // Gesamtsumme der Mwst. 1
    ,   "GesamtPrivatMwst2"     => "a_GesamtpreisPrivatMwSt2"       // Gesamtsumme der Mwst. 2
    ,   "GesamtPrivatMwst3"     => "a_GesamtpreisPrivatMwSt3"       // Gesamtsumme der Mwst. 3
    ,   "GesamtPrivatMwst4"     => "a_GesamtpreisPrivatMwSt4"       // Gesamtsumme der Mwst. 4

    //

    ,   "GesamtZuzahlung"       => "a_GesamtZuzahlung"              // Gesamtbetrag der Zuzahlungen
    ,   "GesamtZuzahlungNetto"  => "a_GesamtZuzahlungNetto"         // Gesamtbetrag der Zuzahlungen netto
    ,   "GesamtZuzahlungNetto1" => "a_GesamtZuzahlungNetto1"        // Gesamtbetrag der Zuzahlungen netto mit Mwst. 1
    ,   "GesamtZuzahlungNetto2" => "a_GesamtZuzahlungNetto2"        // Gesamtbetrag der Zuzahlungen netto mit Mwst. 2
    ,   "GesamtZuzahlungNetto3" => "a_GesamtZuzahlungNetto3"        // Gesamtbetrag der Zuzahlungen netto mit Mwst. 3
    ,   "GesamtZuzahlungNetto4" => "a_GesamtZuzahlungNetto4"        // Gesamtbetrag der Zuzahlungen netto mit Mwst. 4
    ,   "GesamtZuzahlungMwSt1"  => "a_GesamtZuzahlungMwSt1"         // Gesamtbetrag der Mwst. auf Zuzahlungen netto mit Mwst. 1
    ,   "GesamtZuzahlungMwSt2"  => "a_GesamtZuzahlungMwSt2"         // Gesamtbetrag der Mwst. auf Zuzahlungen netto mit Mwst. 2
    ,   "GesamtZuzahlungMwSt3"  => "a_GesamtZuzahlungMwSt3"         // Gesamtbetrag der Mwst. auf Zuzahlungen netto mit Mwst. 3
    ,   "GesamtZuzahlungMwSt4"  => "a_GesamtZuzahlungMwSt4"         // Gesamtbetrag der Mwst. auf Zuzahlungen netto mit Mwst. 4

    //

    ,   "GesamtZulage"          => "a_GesamtWirAuf"                 // Gesamtbetrag der Zulagen
    ,   "GesamtZulageNetto"     => "a_GesamtWirAufNetto"            // Gesamtbetrag der Zulagen netto
    ,   "GesamtZulageNetto1"    => "a_GesamtWirAufNetto1"           // Gesamtbetrag der Zulagen netto mit Mwst. 1
    ,   "GesamtZulageNetto2"    => "a_GesamtWirAufNetto2"           // Gesamtbetrag der Zulagen netto mit Mwst. 2
    ,   "GesamtZulageNetto3"    => "a_GesamtWirAufNetto3"           // Gesamtbetrag der Zulagen netto mit Mwst. 3
    ,   "GesamtZulageNetto4"    => "a_GesamtWirAufNetto4"           // Gesamtbetrag der Zulagen netto mit Mwst. 4
    ,   "GesamtZulageMwSt1"     => "a_GesamtWirAufMwSt1"            // Gesamtbetrag der Mwst. auf Zulagen netto mit Mwst. 1
    ,   "GesamtZulageMwSt2"     => "a_GesamtWirAufMwSt2"            // Gesamtbetrag der Mwst. auf Zulagen netto mit Mwst. 2
    ,   "GesamtZulageMwSt3"     => "a_GesamtWirAufMwSt3"            // Gesamtbetrag der Mwst. auf Zulagen netto mit Mwst. 3
    ,   "GesamtZulageMwSt4"     => "a_GesamtWirAufMwSt4"            // Gesamtbetrag der Mwst. auf Zulagen netto mit Mwst. 4

    //  Konsolidierung der Zahlungsdaten

    ,   "BetragGezahlt"         => "a_GezahltBetrag"                //
    ,   "BetragGezahltPrivat"   => "a_GezahltBetragEA"              //
    ,   "BetragGezahltKT"       => "a_GezahltBetragKK"              //
    ,   "BetragGezahltAZ"       => "a_GezahltBetragAZ"              //
    ) ;

    private $myFiliale  =   null ;

    /**
     * _Auftrag constructor.
     */
    public  function    __construct() {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( void)") ;#
        parent::__construct( "auftrag", "a_id", "kun") ;
        $this->regTransTable( self::$nameTransTable) ;
        $this->setIdKey( "a_id") ;
        $this->_getFiliale() ;
        FDbg::end() ;
    }

    /**
     * @param   int $_id
     * @return  void
     */
    public  function    setId( $_id=-1) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( {$_id})") ;#
        parent::setId( $_id) ;
        if ( $this->isValid()) {
            $this->_getFiliale() ;
        }
        FDbg::end() ;
    }

    /**
     * @param string $_key
     * @return  void
     */
    public  function    setKey( $_key) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( {$_key})") ;#
        $this->fetchFromDbWhere( "a_AuftragId = '$_key'") ;
        $this->_getFiliale() ;
        FDbg::end() ;
    }

    /**
     *
     * @return  void
     */
    private function    _getFiliale() {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( void)") ;#
        if ( $this->isValid()) {
            $this->myFiliale  =   new _Filiale( $this->a_Filiale) ;
            if ( $this->myFiliale->isValid()) {
                //                $this->myFiliale->__dump() ;
            }
        }
        FDbg::end() ;
    }

    /**
     *
     * @return  void
     */
    public function    consolidate() {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( void)") ;

        /**
         *
         */
        $this->__dump() ;
        $myOrderItem  =   new _AuftragPosition() ;
        $myOrderItem->setIterCond( "ap_AuftragId = " . $this->Id) ;
        $myOrderItem->setIterOrder( array("ap_PosNo", "ap_UPosNo")) ;
        $auftragGesamtBrutto    =   0.0 ;
        $auftragGesamtNetto     =   0.0 ;
        $this->GesamtPrivat     =   0.000 ;
        $this->GesamtZuzahlung  =   0.000 ;
        $this->GesamtZulage     =   0.000 ;
        foreach( $myOrderItem as $idx => $item) {
            $item->consolidate( $this) ;
            $this->GesamtZuzahlung  +=  $item->Zuzahlung ;
            $this->GesamtZuzahlung  +=  $item->Eigenanteil ;
            $this->GesamtZulage     +=  $item->GesamtZulage ;
        }
        $this->GesamtPrivat =   $this->GesamtZuzahlung + $this->GesamtZulage ;

        $posNo  =   10 ;
        foreach( $myOrderItem as $idx => $item) {
            $item->PosNo     =   $posNo ;
            $item->ap_UPosNo    =   null ;
            $posNo  +=  10 ;
            $item->updateColInDb( "ap_PosNo,ap_UPosNo") ;
        }
        $this->updateInDb() ;
        FDbg::end() ;
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
        FDbg::trace( 1, FDbg::mdTrcInfo1, basename(__FILE__), __CLASS__, __METHOD__ . "( ...)", "trying to find local article") ;
        $myArticle  =   new _LocalArticle( $_articleRef) ;
        if ( ! $myArticle->isValid()) {
            FDbg::trace( 1, FDbg::mdTrcInfo1, basename(__FILE__), __CLASS__, __METHOD__ . "( ...)", "trying to find cloud article") ;
            $myArticle  =   new _CloudArticle( $_articleRef) ;
        }
        if ( $myArticle->isValid()) {
            $myArticle->__dump() ;
            $myOrderItem    =   new _AuftragPosition() ;
            $myOrderItem->ap_AuftragId  =   $this->a_id ;
            $myOrderItem->ap_ArtId      =   $myArticle->mart_artnr ;
            $myOrderItem->ap_Bez1       =   $myArticle->mart_bezeichnung ;
            $myOrderItem->ap_Bez2       =   $myArticle->mart_bezeichnung2 ;
            $myOrderItem->ap_Bez3       =   $myArticle->mart_bezeichnung3 ;
            $myOrderItem->ap_Menge      =   $_qty ;
            $myOrderItem->storeInDb() ;
        } else {
            FDbg::trace( 1, FDbg::mdTrcInfo1, basename(__FILE__), __CLASS__, __METHOD__ . "( ...)", "no article found") ;
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
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( void)") ;
        $this->ZuzahlungsBefreiung  =   $_befreiung ;
        $this->consolidate() ;
        $this->updateInDb() ;
        FDbg::end() ;
    }

    /**
     * @param string $_le
     * @return  void
     */
    public  function    __dump( $_le="\n") {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( void)") ;
        error_log( parent::__dump()) ;
        $myOrderItem  =   new _AuftragPosition() ;
        $myOrderItem->setIterCond( "ap_AuftragId = " . $this->a_id) ;
        foreach( $myOrderItem as $idx => $item) {
            error_log( $item->__dump()) ;
        }
        FDbg::end() ;
    }
}

class   _AuftragPosition   extends FDbObject    {

    const	ModusZuzahlungManuell    =	"m" ;
    const	ModusZuzahlungSystem    =	"s" ;

    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "Id"                    => "ap_id"
    ,   "AuftragId"             => "ap_AuftragId"                   // Verweis auf auftrag::Id

    //  Beschreibung der Auftragsposition

    ,   "PosNo"                 => "ap_PosNo"
    ,   "UPosNo"                => "ap_UPosNo"
    ,   "ArtikelNr"             => "ap_ArtId"
    ,   "HMV"                   => "ap_HMV"
    ,   "HMV2"                  => "ap_HMV2"
    ,   "EAN"                   => "ap_EAN"
    ,   "PZN"                   => "ap_PZN"
    ,   "Bezeichnung1"          => "ap_Bez1"
    ,   "Bezeichnung2"          => "ap_Bez2"
    ,   "Bezeichnung3"          => "ap_Bez3"
    ,   "LEGS"                  => "ap_LEGS"
    ,   ""

    //  Basis Preise

    ,   "EKPreis"               => "ap_PREISEKNETTO"                // Einkaufspreis des Artikels   ( => Berechnungsmode : 0)
    ,   "PreisDB"               => "ap_PREISDB"                     // Kostentraegeranteil          ( => Berechnungsmode : 1)
    ,   "UVP"                   => "ap_UVP"                         // UVP des Hersteller           ( => Berechnungsmode : 2)
    ,   "SatzMwstId"            => "ap_MWSTKEY"                     // Id des Mehrwertsteuersatzes

    //

    ,   "Menge"                 => "ap_Menge"                       // Verkaufte Menge

    // Kostentraegeranteil

    ,   "PreisNetto"            => "ap_PREISNETTO"                  // Netto-Kostentraegeranteil pro Stueck
    ,   "PreisBrutto"           => "ap_PREISBRUTTO"                 // Brutto-Kostentraegeranteil pro Stueck
    ,   "Mwst"                  => "ap_MWST"                        // Mehrwertsteuer-Kostentraegeranteil pro Stueck

    //  Abgabepreise + Mwst.

    ,   "AbgabePreisNetto"      => "ap_VKPREISNETTO"                // Netto Verkaufspreis pro Stueck
    ,   "AbgabePreisBrutto"     => "ap_VKPREISBRUTTO"               // Brutto Verkaufspreis pro Stueck
    ,   "AbgabePreisMwst"       => "ap_VKMWST"                      // Mehrwertsteuer pro Stueck
    ,   "BausatzZielpreis"      => "ap_gesamtpreis_bs_user"         // Zielpreisvorgabe für Bausatzkalkulation
    ,   "BausatzId"             => "ap_bausatz_id"                  // Id des Bausatzes, Verweis auf =>

    //  Zuzahlung, Eigenanteil, Zulage

    ,   "ModusZuzahlung"        => "ap_Zuzahlungsmode"              // Zuzahlungsmodus, s=System, m=Manuelle Vorgabe
    ,   "Zuzahlung"             => "ap_AnteilKundeZuzahlungPos"
    ,   "Eigenanteil"           => "ap_FesterEigenanteilKunde"      // Fester Eigenanteil pro Stueck
//    ,   "GesamtEigenanteil"     => "ap_FesterEigenanteilGUKunde"    // Gesamtbetrag Fester Eigenanteil pro Stueck
    ,   "Zulage"                => "ap_AnteilKundeWirtAufPos"       // Qualitaetszuzahlung pro Stueck
    ,   "GesamtZulage"          => "ap_AnteilKundeWirtAufGUPos"     // Gesamtbetrag Qualitaetszuzahlung

    //  Daten fuer die Preisberechnung

    ,   "BerechnungsModus"      => "ap_Berechnungsmode"             // 0= EK (Ref.-Preis: EKPreis), 1= KT (Ref.-Preis: PreisDB), 2= VK (Ref.-Preis: UVP)
    ,   "KorrekturProzent"      => "ap_ProzAufAbschlag"             // Prozentuale Korrektur des Abgabepreises
    ,   "KorrekturAbsolut"      => "ap_AbsAufAbschlag"              // Prozentuale Korrektur des Abgabepreises

    //  Daten fuer Buchhaltung etc.

    ,   "Erloeskonto"           => "ap_erloeskto"
    ,   "Warengruppe"           => "ap_WGR"

    ) ;

    /**
     * _AuftragPosition constructor.
     */
    public  function    __construct() {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( void)") ;
        parent::__construct( "auf_pos", "ap_id", "kun") ;
        $this->regTransTable( self::$nameTransTable) ;
        $this->setIdKey( "ap_id") ;
        FDbg::end() ;
    }

    /**
     * @param $_auftrag
     * @return  void
     */
    public  function    consolidate( $_auftrag) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( void)") ;

        /**
         *  determine total 'Brutto' for this item
         */

        /**
         *  determine 'Zuzahlung' for this item
         */
        $this->_detZuzahlung( $_auftrag) ;
        $this->updateColInDb( "ap_AnteilKundeZuzahlungPos") ;
        FDbg::end() ;
    }

    /**
     * @param $_auftrag
     * @return  void
     */
    private function    _detZuzahlung( $_auftrag) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( <auftrag>)") ;
        if ( $this->ModusZuzahlung == _AuftragPosition::ModusZuzahlungSystem) {
            if ( $_auftrag->ZuzahlungsBefreiung == 1) {
                $this->Zuzahlung   =   0.0 ;
            } else {
                switch ( $this->ap_ZUZAHLUNGSKEY) {
                case    1   :
                    $this->Zuzahlung    =   $this->Menge
                        *   $this->PreisBrutto
                        *   0.1 ;
                    if ( $this->Zuzahlung < 5.00) {
                        $this->Zuzahlung   =   5.00 ;
                    } else if ( $this->Zuzahlung > 10.00) {
                        $this->Zuzahlung   =   10.00 ;
                    }
                break ;
                case    2   :
                break ;
                }
            }
        } else {
            $this->ModusZuzahlung   =   _AuftragPosition::ModusZuzahlungManuell ;
        }
        FDbg::end() ;
    }
}
