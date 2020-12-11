<?php

class   _Krankenkasse   extends FDbObject {
    static  $nameTransTable     =   array(
                                "Name"                  => "kl_name"
    ,                           "IKNr"                  => "kl_kvkik"
    ) ;
    public function __construct( $_key="") {
        parent::__construct("kassenliste_new", "kl_kvkik", "od");
        $this->regTransTable( self::$nameTransTable) ;
        $this->setIdKey("IKNr");
        if ( $_key != "") {
            $this->setKey( $_key) ;
        }
    }

    public function setKey($_key) {
        $this->fetchFromDbWhere("IKNr = '$_key'");
    }
}

class   _KrankenkasseGruppen   extends FDbObject {
    public function __construct( $_key="") {
        parent::__construct("kassengruppen_zuordnung", "kg_ik", "od");
        $this->setIdKey("kg_ik");
        if ( $_key != "") {
            $this->setKey( $_key) ;
        }
    }

    public function setKey( $_key) {
        $this->fetchFromDbWhere( "kg_ik = '$_key'");
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
        $this->fetchFromDbWhere("kk_ik_nummer = '$_key'");
    }

    public  function    showAll( $_leIK, $_ktIK) {
        $myVertrag  =   new _Vertrag() ;
        $myVertragR =   new _VertragR() ;
        $myVertragR->setIterCond( "ip_ik = '$_leIK'") ;
        foreach ( $myVertragR as $idx => $actVertragR) {
            $actVertragR->__dump() ;
            $myVertrag->showAll( $actVertragR->ip_preisid, $_ktIK) ;
        }
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
        $this->fetchFromDbWhere("kk_ik_nummer = '$_key'");
    }

    public  function    showAll( $_vertragId, $_ktIK) {
        $myVertragLEGSData  =   new _VertragLEGSData() ;
        $myVertrag  =   new _Vertrag() ;
        $myVertrag->setIterCond( array( "pk_preisid = '$_vertragId'",
            "( pk_kassenik_angeschlossen='$_ktIK' OR pk_ekkart_angeschlossen = 'TK')",
            "pk_tarifbereich_angeschlossen = 20")) ;
        foreach ( $myVertrag as $idx => $actVertrag) {
            $actVertrag->__dump() ;
            $myVertragLEGSData->showAll( $_vertragId) ;
        }
    }
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}
class   _VertragLEGSData   extends FDbObject {
    public function __construct() {
        parent::__construct("preislink_new", "prl_preisid", "od");
        $this->setIdKey("prl_preisid");
    }

    public function setKey( $_key) {
    }

    public function getByContractNo( $_contractNo) {
        $this->fetchFromDbWhere( array( "prl_preisid = $_contractNo")) ;
        return $this->_valid ;
    }

    public  function    showAll( $_vertragId) {
        $myVertragLEGSData  =   new _VertragLEGSData() ;
        $myVertragLEGSData->setIterCond( array( "prl_preisid = '$_vertragId'")) ;
        foreach ( $myVertragLEGSData as $idx => $actVertragLEGSData) {
            $actVertragLEGSData->__dump() ;
        }
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
    }

    /**
     * @param $_leIK    string  IK Nummer Leistungserbringer
     * @param $_ktIK    string  IK Nummer Kostenträger
     * @param $_hmvNo   string  HMV Nummer
     * @param $_lkz     string  LKZ (Leistungskennziffer)
     * @return  _VertragHMVData
     */
    public function    getHMVData( $_leIK, $_ktIK, $_hmvNo, $_lkz) {
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
                        } else if ( $currVertrag->pk_tarifbereich_angeschlossen == "17") {
                        } else {
                            $currVertrag    =   null ;
                        }
                    }

                    if ( $currVertrag !== null) {
                        $krankenkassenCount++ ;
                        $myVertragLEGSData->first( "prl_preisid = '".$currVertrag->pk_preisid."'", "prl_prioritaet ASC") ;
                        if ( $myVertragLEGSData->isValid()) {
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
            }
        } else {
        }
        return $resVertragHMVData ;
    }

    /**
     * @param string $_le
     * @return  void
     */
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}
class   _Filiale   extends FDbObject {
    static  $nameTransTable     =   array(
        "Name1"                 => "f_name1"
    ) ;

    /**
     * _Filiale constructor.
     * @param string $_key
     */
    public function __construct( $_key="") {
        parent::__construct("firma", "f_id", "kun");
        $this->regTransTable( self::$nameTransTable) ;
        if ( $_key != "") {
            $this->setKey( $_key) ;
        }
    }

    /**
     * @param string $_key
     * @return  void
     */
    public function setKey( $_key) {
        $this->fetchFromDbWhere( "f_id = '$_key'");
    }

    /**
     * @param string $_key
     * @return  void
     */
    public function getByLEIKNr( $_leIKNr) {
        $this->first( "f_ik = '{$_leIKNr}'");
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
        parent::__construct("artikelliste_mandant", "mart_artnr", "kun");
        if ( $_key != "") {
            $this->setKey( $_key) ;
        }
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
        parent::__construct("artikelliste_komplett", "art_artnr", "od");
        if ( $_key != "") {
            $this->setKey( $_key) ;
        }
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
        parent::__construct( "mitarbeiter", "ma_id", "kun");
        $this->setIdKey("ma_id");
    }

    /**
     * @param string $_key
     * @return  void
     */
    public function setKey( $_key) {
    }

    /**
     * @param string $_le
     * @return  void
     */
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}
