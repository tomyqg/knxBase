<?php

class   _Krankenkasse   extends FDbObject {
    public function __construct() {
        parent::__construct("krankenkassen", "kk_id", "kun");
        $this->setIdKey("kk_id");
    }

    public function setKey($_key) {
        $this->fetchFromDbWhere("kk_ik_nummer = '$_key'");
    }
}

class   _KassengruppenZuordnung   extends FDbObject {
    public function __construct( $_key="") {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '$_key')") ;
        parent::__construct("kassengruppen_zuordnung", "kg_ik", "od");
        FDbg::end() ;
    }

    public function setKey( $_key) {
        $this->fetchFromDbWhere( "kg_ik = '$_key'");
    }
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}

class   _VertragR   extends FDbObject {
    public function __construct() {
        parent::__construct("ik_preisliste_new", "ip_ik", "od");
        $this->setIdKey("kk_id");
    }

    public function setKey( $_key) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '$_key')") ;
        $this->fetchFromDbWhere("kk_ik_nummer = '$_key'");
        FDbg::end() ;
    }

    public  function    showAll( $_leIK, $_ktIK) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( void)") ;
        $myVertrag  =   new _Vertrag() ;
        $myVertragR =   new _VertragR() ;
        $myVertragR->setIterCond( "ip_ik = '$_leIK'") ;
        foreach ( $myVertragR as $idx => $actVertragR) {
            $actVertragR->__dump() ;
            $myVertrag->showAll( $actVertragR->ip_preisid, $_ktIK) ;
        }
        FDbg::end() ;
    }
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}
class   _Vertrag   extends FDbObject {
    public function __construct() {
        parent::__construct("preislink_kassenarten_new", "pk_preisid", "od");
        $this->setIdKey("pk_preisid");
    }

    public function setKey($_key) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '$_key')") ;
        $this->fetchFromDbWhere("kk_ik_nummer = '$_key'");
        FDbg::end() ;
    }

    public  function    showAll( $_vertragId, $_ktIK) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( void)") ;
        $myVertragLEGSData  =   new _VertragLEGSData() ;
        $myVertrag  =   new _Vertrag() ;
        $myVertrag->setIterCond( array( "pk_preisid = '$_vertragId'",
            "( pk_kassenik_angeschlossen='$_ktIK' OR pk_ekkart_angeschlossen = 'TK')",
            "pk_tarifbereich_angeschlossen = 20")) ;
        foreach ( $myVertrag as $idx => $actVertrag) {
            $actVertrag->__dump() ;
            $myVertragLEGSData->showAll( $_vertragId) ;
        }
        FDbg::end() ;
    }
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}
class   _VertragLEGSData   extends FDbObject {
    public function __construct() {
        parent::__construct("preislink_new", "prl_preisid", "od");
        $this->setIdKey("pk_preisid");
    }

    public function setKey( $_key) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '$_key')") ;
        FDbg::end() ;
    }

    public function getByContractNo( $_contractNo) {
        $this->fetchFromDbWhere( array( "prl_preisid = $_contractNo")) ;
        return $this->_valid ;
    }

    public  function    showAll( $_vertragId) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( void)") ;
        $myVertragLEGSData  =   new _VertragLEGSData() ;
        $myVertragLEGSData->setIterCond( array( "prl_preisid = '$_vertragId'")) ;
        foreach ( $myVertragLEGSData as $idx => $actVertragLEGSData) {
            $actVertragLEGSData->__dump() ;
        }
        FDbg::end() ;
    }
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}
class   _VertragHMVData   extends FDbObject {
    public function __construct() {
        parent::__construct("preislisten_new", "prl_preisid", "od");
        $this->setIdKey("pk_preisid");
    }

    /**
     * @param string $_key
     * @return  void
     */
    public function setKey($_key) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '$_key')") ;
        FDbg::end() ;
    }

    /**
     * @param $_leIK    string  IK Nummer Leistungserbringer
     * @param $_ktIK    string  IK Nummer Kostenträger
     * @param $_hmvNo   string  HMV Nummer
     * @param $_lkz     string  LKZ (Leistungskennziffer)
     * @return  _VertragHMVData
     */
    public function    getHMVData( $_leIK, $_ktIK, $_hmvNo, $_lkz) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "(  '$_leIK', '$_ktIK', '$_hmvNo', '$_lkz')") ;
        $resVertragHMVData  =   null ;
        /**
         *  find 'Kostentraeger' data
         *  IF data not found
         *      bail out
         */
        $myKostenträger =   new _Krankenkasse() ;
        $myKostenträger->setKey( $_ktIK) ;
        $myKassengruppenZuordnung =   new _KassengruppenZuordnung() ;
        $myKassengruppenZuordnung->setKey( $_ktIK) ;
        $myKassengruppenZuordnung->__dump() ;
        $myVertragHMVData   =   new _VertragHMVData() ;
        $myVertragLEGSData  =   new _VertragLEGSData() ;        // preislink_new
        /**
         *  Kostenträger ist gültig
         */
        if ( $myKostenträger->isValid() && $myKassengruppenZuordnung->isValid()) {
            $primaerKasse   =   "EKK" ;
            $ersatzKasse    =   "TK" ;
            $tarifBereich   =   "5" ;
            $myVertragR  =   new _VertragR() ;                      // ik_preisliste_new
            $myVertrag  =   new _Vertrag() ;                        // preislink_kassenarten_new
            $myVertragR->setIterCond( "ip_ik = '$_leIK'") ;
            /**
             *  für alle Vertrag des Leistungserbringers
             */
            $contractCount  =   0 ;
            $priceCount =   0 ;
            foreach ( $myVertragR as $actVertragR) {                // ip_*
                FDbg::trace( 11, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "working on " . $actVertragR->ip_ik . " => " . $actVertragR->ip_preisid) ;
                $contractCount++ ;
                $myVertrag->clearIterCond() ;
                $myVertrag->setIterCond( "pk_preisid = '".$actVertragR->ip_preisid."'") ;
                $krankenkassenCount =   0 ;
                foreach ( $myVertrag as $actVertrag) {              // pk_*
                    $currVertrag    =   $actVertrag ;
                    $connectedVia   =   "" ;
                    if ( $actVertrag->pk_kassenart_angeschlossen == $primaerKasse && $actVertrag->pk_ekkart_angeschlossen == "") {
                        $connectedVia = "Kassenart";
                    } else if ( $actVertrag->pk_kassenart_angeschlossen == $primaerKasse && $actVertrag->pk_ekkart_angeschlossen == $ersatzKasse) {
                        $connectedVia   =   "Kassenart" ;
                    } else if ( $actVertrag->pk_kassenik_angeschlossen == $_ktIK) {
                        $connectedVia   =   "IK-Nummer" ;
//                    } else if ( $actVertrag->pk_kassenart_angeschlossen == "RVO") {
//                        $connectedVia   =   "RVO" ;
                    } else {
                        $currVertrag    =   null ;
                    }

                    if ( $currVertrag && $myKassengruppenZuordnung->kg_region_ignorieren == 0) {
                        if ( $currVertrag->pk_tarifbereich_angeschlossen == $tarifBereich) {
                            FDbg::trace( 31, FDbg::mdTrcInfo1, basename(__FILE__), __CLASS__, __METHOD__ . "( ...)", "angeschlossen ueber Tarifbereich := " . $currVertrag->pk_tarifbereich_angeschlossen);
                        } else if ( $currVertrag->pk_tarifbereich_angeschlossen == "17") {
                            FDbg::trace( 31, FDbg::mdTrcInfo1, basename(__FILE__), __CLASS__, __METHOD__ . "( ...)", "angeschlossen ueber Tarifbereich := " . $currVertrag->pk_tarifbereich_angeschlossen);
                        } else {
                            $currVertrag    =   null ;
                        }
                    }

                    if ( $currVertrag !== null) {
                        FDbg::trace( 21, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Kasse angeschlossen via " . $connectedVia) ;
                        $krankenkassenCount++ ;
                        $myVertragLEGSData->first( "prl_preisid = '".$currVertrag->pk_preisid."'", "prl_prioritaet ASC") ;
                        if ( $myVertragLEGSData->isValid()) {
                            FDbg::trace( 21, FDbg::mdTrcInfo1, basename(__FILE__), __CLASS__, __METHOD__ . "( ...)", "LEGS Data found, LEGS := " . $myVertragLEGSData->prl_legs);
                            FDbg::trace(  1, FDbg::mdTrcInfo1, basename(__FILE__), __CLASS__, __METHOD__ . "( ...)", "Prioritaet := " . $myVertragLEGSData->prl_prioritaet);
                            $this->first( array( "( pl_legs = '".$myVertragLEGSData->prl_legs."' OR pl_legs_ersatz = '".$myVertragLEGSData->prl_legs."' OR pl_legs_privat = '".$myVertragLEGSData->prl_legs."')"
                                ,   "( pl_hmv = '".$_hmvNo."' OR pl_hmv = '".substr( $_hmvNo, 0, 7)."' OR pl_hmv = '".substr( $_hmvNo, 0, 6)."')"
                                ,   "(pl_leistungskz = '".$_lkz."')")
                                , array( "LENGTH( pl_hmv) DESC")) ;
                            if ( $this->isValid()) {
                                $priceCount++ ;
                                $myVertragLEGSData->__dump() ;
                                $this->__dump() ;
                            }
                        }
                    }
                }
                FDbg::trace( 11, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Number of krankenkassen / tarifbereich checked := " . $krankenkassenCount) ;
            }
        } else {
            FDbg::trace( 11, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "INVALID KOSTENTRÄGER ...") ;
        }
        FDbg::trace( 11, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Number of contracts checked := " . $contractCount) ;
        FDbg::trace( 11, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Number of prices found      := " . $priceCount) ;

        FDbg::end() ;
        return $resVertragHMVData ;
    }

    /**
     * @param string $_le
     * @return  void
     */
    public function    __dump( $_le="\n") {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "(  <string>)") ;
        error_log( parent::__dump( $_le)) ;
        FDbg::end() ;
    }
}
class   _Filiale   extends FDbObject {
    public function __construct( $_key="") {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '$_key')") ;
        parent::__construct("firma", "f_id", "kun");
        if ( $_key != "") {
            $this->setKey( $_key) ;
        }
        FDbg::end() ;
    }

    /**
     * @param string $_key
     * @return  void
     */
    public function setKey( $_key) {
        $this->fetchFromDbWhere( "f_id = '$_key'");
    }

    /**
     * @param string $_le
     * @return  void
     */
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}
class   _LocalArticle   extends FDbObject {
    public function __construct( $_key="") {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '$_key')") ;
        parent::__construct("artikelliste_mandant", "mart_artnr", "kun");
        if ( $_key != "") {
            $this->setKey( $_key) ;
        }
        FDbg::end() ;
    }

    /**
     * @param string $_key
     * @return  void
     */
    public function setKey( $_key) {
        $this->fetchFromDbWhere( "mart_artnr = '$_key'") ;
    }

    /**
     * @param string $_le
     * @return  void
     */
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}
class   _CloudArticle   extends FDbObject {
    public function __construct( $_key="") {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '$_key')") ;
        parent::__construct("artikelliste_komplett", "art_artnr", "od");
        if ( $_key != "") {
            $this->setKey( $_key) ;
        }
        FDbg::end() ;
    }

    public function setKey( $_key) {
        $this->fetchFromDbWhere( "art_artnr = '$_key'") ;
    }
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}

class   _Mitarbeiter  extends FDbObject {

    /**
     * _Mitarbeiter constructor.
     */
    public function __construct() {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
        parent::__construct( "mitarbeiter", "ma_id", "kun");
        $this->setIdKey("ma_id");
        FDbg::end() ;
    }

    /**
     * @param string $_key
     * @return  void
     */
    public function setKey( $_key) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '$_key')") ;
        FDbg::end() ;
    }

    /**
     * @param string $_le
     * @return  void
     */
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}

require_once( "khWarengruppe.php") ;    // warengruppen:
require_once( "khSKRKonto.php") ;       // fibu_cfg:        Kontenrahmen Konten, nur eine Referenz zur Umsetzung
require_once( "khKassenBeleg.php") ;
require_once( "khFibuLog.php") ;
require_once( "khAuftrag.php") ;        // auftrag:         Auftragsdaten
require_once( "khWorkflowStep.php") ;   // auftrag_wf:
