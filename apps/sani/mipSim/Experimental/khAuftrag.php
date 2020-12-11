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
class   _Auftrag   extends _AuftragAdapter    {

    private $myFiliale  =   null ;

    /**
     * _Auftrag constructor.
     */
    public  function    __construct() {
        parent::__construct( "auftrag", "a_id", "kun") ;
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
        $this->GesamtEigenanteilBrutto  =   0.000 ;
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
            $this->GesamtEigenanteilBrutto  +=  $item->GesamtEigenanteilBrutto ;
            $this->gesamtZulageBrutto       +=  $item->gesamtZulageBrutto ;
        }
        $this->gesamtPatientBrutto   =   $this->gesamtZuzahlungBrutto + $this->GesamtEigenanteilBrutto + $this->gesamtZulageBrutto ;
        $this->__dump() ;

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
