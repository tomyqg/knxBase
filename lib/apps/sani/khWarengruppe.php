<?php

class   _Warengruppe    extends FDbObject   {

    /**
    * @var array   translation/mapping table for translating self-explaining database attributes to real world shit
    */
    static  $nameTransTable     =   array(
        "Id"                    => "wg_id"
    ,   "WarengruppeNr"         => "wg_nr"
    ,   "LoeschKennzeichen"     => "wg_loeschkz"
    ,   "Name"                  => "wg_name"
    ,   "ErlNul"                => "wg_erlnul"
    ,   "ErlErm"                => "wg_erlerm"
    ,   "ErlVol"                => "wg_erlvol"
    ,   "EinNul"                => "wg_einnul"
    ,   "EinErm"                => "wg_einerm"
    ,   "EinVol"                => "wg_einvol"
    ,   "ErkNak"                => "wg_erlnak"
    ,   "ErlNlk"                => "wg_erlnlk"                // Bedeutung dieses Eintrages unklar
    ,   "ErlNip"                => "wg_erlnip"
    ,   "ErlNap"                => "wg_erlnap"
    ,   "ErlNlp"                => "wg_erlnlp"
    ,   "ErlErp"                => "wg_erlerp"
    ,   "ErlVop"                => "wg_erlvop"
    ,   "ErlVolAlt"             => "wg_erlvol_alt"
    ,   "ErlVopAlt"             => "wg_erlvop_alt"
    ,   "EigeneWGR"             => "wg_eigen_wgr"
    ,   "WGEnde"                => "wg_ende"
    ,   "HMVBereich"            => "wg_hmvbereich"
    ) ;

    /**
    * _Warengruppe constructor.
    * @param string $_wgr
    */
    public function __construct( $_wgr="") {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '{$_wgr}')") ;
        parent::__construct( "Warengruppen", "wg_nr", "kun");
        $this->regTransTable( self::$nameTransTable) ;
        if ( $_wgr != "") {
        $this->setKey( $_wgr) ;
    }
    FDbg::end() ;
    }
}
