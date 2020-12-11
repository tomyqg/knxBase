<?php

/**
 * Class _FibuLog
 */
class   _FibuLog  extends FDbObject {

    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "Id"                    => "fl_id"
    ,   "Filiale"               => "fl_filiale"
    ,   "KontoNr1"              => "fl_ktonr"
    ,   "KontoArt1"             => "fl_ktoart"
    ,   "Datum"                 => "fl_datum"
    ,   "Bufeld"                => "fl_bufeld"
    ,   "KontoNr2"              => "fl_ggktonr"
    ,   "KontoArt2"             => "fl_ggktoart"
    ,   "Buchungstext1"         => "fl_buchtext"
    ,   "Buchungstext2"         => "fl_buchtext2"
    ,   "MwstSatz"              => "fl_ustsatz"
    ,   "BelegNr"               => "fl_belegnr"
    ,   "Betrag"                => "fl_betrag"
    ,   "Kostenstelle1"         => "fl_kost1"
    ,   "Kostenstelle2"         => "fl_kost2"
    ,   "ReferenzId"            => "fl_refid"
    ,   "ReferenzTyp"           => "fl_reftxpe"
    ,   "Exportiert"            => "fl_export"
    ,   "InfoAnlage"            => "anl_id"
    ,   "InfoAenderung"         => "aen_id"
    ) ;

    /**
     * _FibuLog constructor.
     */
    public function __construct() {
        parent::__construct( "fibu_log", "fl_id", "kun");
        $this->regTransTable( self::$nameTransTable) ;
        $this->setIdKey( "fl_id");
    }

    /**
     * @param   string $_key
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
