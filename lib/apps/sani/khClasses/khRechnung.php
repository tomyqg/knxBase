<?php

/**
 * Class _Rechnung
 *
 * Anmerkung
 * Aufgrund des unterschiedlichen Hanldings der Mehrwertsteuer, sowohl Brutto als auch Netto Beträge koennen,
 * je nach LEGS Daten, als Kalkulationsbasis dienen, wird die Mehrwertsteuer explizit mitgefuehrt und
 * lediglich auf Positionsebene errechnet.
 *
 */
class   _Rechnung   extends FDbObject    {

    /**
     *  die folgenden Attribute existiern (noch) nicht in der Tabelle und muessen daher explizit erzeugt werden!
     */
    public  $gesamtEigenanteilBrutto    =   0.000 ;
    public  $Lock    =   false ;

    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "Id"                    => "re_id"
    ,   "RechnungNr"            => "re_rechnungsnr"
    ,   "Datum"                 => "re_datum"
    ,   "Verkaeufer"            => "re_verkaeufer"

        //  Summen der privat zu zahlenden Anteile

    ,   "GesamtNetto"           => "re_netto"                       // Gesamtsumme netto
    ,   "GesamtBrutto"          => "re_brutto"                      // Gesamtsumme der Privatzahlung = Zuzahlung + Eigenanteil + Zulage

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
        $this->fetchFromDbWhere( "AuftragNr = '$_key'") ;
        $this->_getFiliale() ;
    }

    /**
     *
     * @return  void
     */
    public function    vomAuftrag() {

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
        $this->GesamtAbgabeBrutto       =   0.000 ;     //
        $this->GesamtAbgabeNetto        =   0.000 ;     //
        $this->GesamtKTAnteilBrutto     =   0.000 ;     //
        $this->GesamtKTAnteilNetto      =   0.000 ;     //
        $this->GesamtPrivatBrutto       =   0.000 ;     // = Zuzahlung + Eigenanteil + Zulage
        $this->GesamtZuzahlungBrutto    =   0.000 ;
        $this->gesamtEigenanteilBrutto  =   0.000 ;
        $this->GesamtZulageBrutto       =   0.000 ;

        /**
         * @var  _AuftragPosition   $item
         */
        $myOrderItem  =   new _AuftragPosition() ;
        $myOrderItem->setIterCond( "ap_AuftragId = " . $this->Id) ;
        $myOrderItem->setIterOrder( array("ap_PosNo", "ap_UPosNo")) ;
        foreach( $myOrderItem as $idx => $item) {
            $item->consolidate( $this) ;
            $this->GesamtAbgabeBrutto       +=  $item->gesamtAbgabePreisBrutto ;
            $this->GesamtAbgabeNetto        +=  $item->gesamtAbgabePreisNetto ;
            $this->GesamtKTAnteilBrutto     +=  $item->gesamtKTAnteilBrutto ;
            $this->GesamtKTAnteilNetto      +=  $item->gesamtKTAnteilNetto ;
            $this->GesamtZuzahlungBrutto    +=  $item->GesamtZuzahlungBrutto ;
            $this->gesamtEigenanteilBrutto  +=  $item->gesamtEigenanteilBrutto ;
            $this->GesamtZulageBrutto       +=  $item->GesamtZulageBrutto ;
        }
        $this->GesamtPrivatBrutto   =   $this->GesamtZuzahlungBrutto + $this->gesamtEigenanteilBrutto + $this->GesamtZulageBrutto ;
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
            $myOrderItem->ap_AuftragId  =   $this->a_id ;
            $myOrderItem->ap_ArtId      =   $myArticle->mart_artnr ;
            $myOrderItem->ap_Bez1       =   $myArticle->mart_bezeichnung ;
            $myOrderItem->ap_Bez2       =   $myArticle->mart_bezeichnung2 ;
            $myOrderItem->ap_Bez3       =   $myArticle->mart_bezeichnung3 ;
            $myOrderItem->ap_Menge      =   $_qty ;
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

class   _RechnungPosition    extends _Position    {

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

        //  Abgabepreise + Mwst.

    ,   "BausatzZielpreis"      => "ap_gesamtpreis_bs_user"         // Zielpreisvorgabe für Bausatzkalkulation
    ,   "BausatzId"             => "ap_bausatz_id"                  // Id des Bausatzes, Verweis auf =>

    ,   "AbgabePreisNetto"      => "ap_VKPREISNETTO"                // Netto Verkaufspreis pro Stueck
    ,   "AbgabePreisMwst"       => "ap_VKMWST"                      // Mehrwertsteuer pro Stueck
    ,   "AbgabePreisBrutto"     => "ap_VKPREISBRUTTO"               // Brutto Verkaufspreis pro Stueck

        // Kostentraegeranteil

    ,   "KTAnteilNetto"         => "ap_PREISNETTO"                  // Netto-Kostentraegeranteil pro Stueck
    ,   "KTAnteilMwst"          => "ap_MWST"                        // Mehrwertsteuer-Kostentraegeranteil pro Stueck
    ,   "KTAnteilBrutto"        => "ap_PREISBRUTTO"                 // Brutto-Kostentraegeranteil pro Stueck

        //  Zuzahlung, Eigenanteil, Zulage

    ,   "ModusZuzahlung"        => "ap_Zuzahlungsmode"              // Zuzahlungsmodus, s=System, m=Manuelle Vorgabe
    ,   "ZuzahlungBrutto"       => "ap_AnteilKundeZuzahlungPos"
    ,   "GesamtZuzahlungBrutto" => "ap_AnteilKundeZuzahlungGUPos"   // Gesamtbetrag Qualitaetszuzahlung
    ,   "EigenanteilBrutto"     => "ap_FesterEigenanteilKunde"      // Fester Eigenanteil pro Stueck
        //    ,   "GesamtEigenanteil"     => "ap_FesterEigenanteilGUKunde"    // Gesamtbetrag Fester Eigenanteil pro Stueck
    ,   "ZulageBrutto"          => "ap_AnteilKundeWirtAufPos"       // Qualitaetszuzahlung pro Stueck
    ,   "GesamtZulageBrutto"    => "ap_AnteilKundeWirtAufGUPos"     // Gesamtbetrag Qualitaetszuzahlung

        //  Daten fuer die Preisberechnung

    ,   "BerechnungsModus"      => "ap_Berechnungsmode"             // 0= EK (Ref.-Preis: EKPreis), 1= KT (Ref.-Preis: PreisDB), 2= VK (Ref.-Preis: UVP)
    ,   "KorrekturProzent"      => "ap_ProzAufAbschlag"             // Prozentuale Korrektur des Abgabepreises
    ,   "KorrekturAbsolut"      => "ap_AbsAufAbschlag"              // Prozentuale Korrektur des Abgabepreises

        //  Daten fuer Buchhaltung etc.

    ,   "Warengruppe"           => "ap_WGR"                         // Aus der Warengruppe wird das ...
    ,   "Erloeskonto"           => "ap_erloeskto"                   // ... Erloeskonto ermittelt

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
        $this->_detMwst() ;

        /**
         *  einfache Werte berechnen
         */
        $this->_detGesamtEigenanteilBrutto() ;        // menge * eigenanteil
        $this->_getGesamtZulageBrutto( $_auftrag) ;             // menge * zulage

        /**
         *  2. Zulage eines moeglichen Bausatzes ermitteln
         */
        if ( $this->BausatzZielpreis != 0.00) {
            $this->gesamtKomponentenPreisBrutto =   0.00 ;
            $myOrderItem  =   new _AuftragPosition() ;
            $myOrderItem->setIterCond( array( "ap_AuftragId = " . $_auftrag->Id, "ap_PosNo = " . $this->PosNo)) ;
            $myOrderItem->setIterOrder( array( "ap_UPosNo")) ;
            foreach ( $myOrderItem as $item) {
                $this->gesamtKomponentenPreisBrutto +=  $item->Menge * $item->KTAnteilBrutto ;
            }
            $this->ZulageBrutto =   $this->BausatzZielpreis - $this->gesamtKomponentenPreisBrutto ;
        }

        /**
         *  3. Zuzahlung der Hauptpositionen ermitteln
         *
         *  WENN ich eine Unterposition bin
         *  SONST WENN ich Unterpositionen habe
         *  SONST
         *      Zuzahlungen bestimmen
         */
        if ( $this->UPosNo != 0) {
            $this->ZuzahlungBrutto    =   0.000 ;
        } else {
            $this->gesamtKTAnteilBrutto  =  $this->KTAnteilBrutto ;
            $myOrderItem  =   new _AuftragPosition() ;
            $myOrderItem->setIterCond( array( "ap_AuftragId = " . $_auftrag->Id, "ap_PosNo = " . $this->PosNo, "ap_UPosNo > 0")) ;
            $myOrderItem->setIterOrder( array( "ap_PosNo", "ap_UPosNo")) ;
            foreach ( $myOrderItem as $item) {
                $item->consolidate( $_auftrag) ;
                $this->gesamtKTAnteilBrutto  +=  $item->Menge * $item->KTAnteilBrutto ;
            }
            $this->_detZuzahlung( $_auftrag, $this->gesamtKTAnteilBrutto) ;

        }

        /**
         * 4. Gesamtbetraege bestimmen
         */
        $this->gesamtAbgabePreisNetto   =   $this->Menge * $this->AbgabePreisNetto ;
        $this->gesamtAbgabePreisBrutto  =   $this->Menge * $this->AbgabePreisBrutto ;
        $this->gesamtKTAnteilNetto      =   $this->Menge * $this->KTAnteilNetto ;
        $this->gesamtKTAnteilBrutto     =   $this->Menge * $this->KTAnteilBrutto ;
        $this->GesamtZuzahlungBrutto    =   $this->Menge * $this->ZuzahlungBrutto ;
        $this->gesamtEigenanteilBrutto  =   $this->Menge * $this->EigenanteilBrutto ;
        $this->GesamtZulageBrutto       =   $this->Menge * $this->ZulageBrutto ;

        $this->updateInDb() ;
    }

}
