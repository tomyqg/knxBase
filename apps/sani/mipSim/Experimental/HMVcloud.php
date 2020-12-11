<?php

/**
 * Class HMV Einzelprodukt (cloud::hmv_05_einzelprodukt)
 */
class   _HMV_EP  extends FDbObject {

    /**
     * @var array   translation/mapping table to obtain self-explaining attributes instead of database real world shit
     */
    static  $nameTransTable     =   array(
        "HMVNr"                 => "h_hilfsmittelnr"            // 10-stellige Hilsmittelnr. ohne Punkte
    ,   "Bezeichnung"           => "h_bezeichnung"              // Bezeichnung laut GKV
    ,   "Hersteller"            => "h_hersteller"               // Hersteller Name im Klartext
    ,   "NichtBesetzt"          => "h_kznichtbesetzt"           // 1 wenn nicht in Verwendung
    ,   "PseudoHMV"             => "h_kzpseudo"
    ) ;

    /**
     * HMV Einzelprodukt constructor
     */
    public function __construct( $_key="") {
        parent::__construct( "hmv_05_einzelprodukt", "h_hilfsmittelnr", "od");
        $this->setIdKey( "HMVNr");
        if ( $_key != "") {
            $this->setKey( $_key) ;
        }
    }

    /**
     * @param   string $_key
     * @return  void
     */
    public function setKey( $_key) {
        $this->fetchFromDbWhere( "HMVNr = '$_key'") ;
    }

    /**
     *  Methode wird ausgeführt während die Klasse zum ersten Mal instanziiert wird
     */
    protected   function _postInstantiate() {
        error_log( __CLASS__ . "::_postInstatiate()") ;
        $this->regTransTable( self::$nameTransTable) ;
    }

    /**
     *  Methode wird ausgeführt wenn ein Objekt erfolgreich aus der Db geladen wurde
     */
    protected   function _postLoad() {
        error_log( __CLASS__ . "::_postLoad()") ;
    }
}

/**
 * Class HMV Produktart (cloud::hmv_04_produktart)
 */
class   _HMV_PA  extends FDbObject {

    /**
     * @var array   translation/mapping table to obtain self-explaining attributes instead of database real world shit
     */
    static  $nameTransTable     =   array(
        "Id"                    =>  "04_id"
    ,   "ProduktGruppe"         =>  "gruppe"                    // 10-stellige Hilsmittelnr. ohne Punkte
    ,   "AnwendungsOrt"         =>  "ort"                       // Bezeichnung laut GKV
    ,   "UnterGruppe"           =>  "untergruppe"               // Hersteller Name im Klartext
    ,   "ProduktArt"            =>  "art"                       // 1 wenn nicht in Verwendung
    ,   "Bezeichnung"           =>  "bezeichnung"
    ,   "Beschreibung"          =>  "beschreibung"
    ,   "Indikation"            =>  "indikation"
    ,   "Anmerkung"             =>  "anmerkung"
    ) ;

    /**
     * HMV Einzelprodukt constructor
     */
    public function __construct( $_key="") {
        parent::__construct( "hmv_04_produktart", array( "gruppe", "ort", "unter", "art"), "od");
        $this->setIdKey( "Id");
        if ( $_key != "") {
            $this->setKey( $_key) ;
        }
    }

    /**
     * @param   string $_key
     * @return  void
     */
    public function setKey( $_key) {
        parent::setKey( $_key) ;
    }

    /**
     *  Methode wird ausgeführt während die Klasse zum ersten Mal instanziiert wird
     */
    protected   function _postInstantiate() {
        error_log( __CLASS__ . "::_postInstatiate()") ;
        $this->regTransTable( self::$nameTransTable) ;
    }

    /**
     *  Methode wird ausgeführt wenn ein Objekt erfolgreich aus der Db geladen wurde
     */
    protected   function _postLoad() {
        error_log( __CLASS__ . "::_postLoad()") ;
    }
}
