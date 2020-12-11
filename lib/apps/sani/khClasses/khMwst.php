<?php

/**
 * Class _FibuLog
 */
class   _Mwst  extends FDbObject {

    const   inMwst  =   0 ;
    const   exMwst  =   1 ;

    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "id"                    => "amm_id"
    ,   "bezeichung"            => "amm_bezeichnung"
    ,   "satz"                  => "amm_satz"
    ,   "exklusiv"              => "amm_exklusiv"
    ) ;

    /**
     * _FibuLog constructor.
     */
    public function __construct() {
        parent::__construct( "auf_pos_mm_mwst", "amm_id", "kun");
        $this->regTransTable( self::$nameTransTable) ;
        $this->setIdKey( "amm_id");
    }

    /**
     * @param   string $_key
     * @return  void
     */
    public function setKey( $_key) {
    }

}
