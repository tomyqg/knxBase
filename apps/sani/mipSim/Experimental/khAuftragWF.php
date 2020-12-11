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
class   _AuftragWF   extends _AuftragWFAdapter    {

    /**
     * _Auftrag constructor.
     */
    public  function    __construct() {
        parent::__construct( "auftrag_wf", "aw_id", "kun") ;
        $this->setIdKey( "aw_id") ;
   }

    /**
     * @param   int $_id
     * @return  void
     */
    public  function    setId( $_id=-1) {
        parent::setId( $_id) ;
    }

    /**
     * @param string $_le
     * @return  void
     */
    public  function    __dump( $_le="\n") {
        error_log( parent::__dump()) ;
    }
}
