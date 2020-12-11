<?php

class   _SKRKonto    extends FDbObject   {

    /**
     * @var array   translation/mapping table for translating self-explaining database attributes to real world shit
     */
    static  $nameTransTable   =   array(
        "Id"                    => "fs_id"
    ,   "FaId"                  => "fs_fa_id"
    ,   "KontoNr"               => "fs_ktonr"
    ,   "Bezeichnung1"          => "fs_bez1"
    ,   "Info"                  => "fs_info"
    ,   "i18n"                  => "fs_i18n"
    ,   "KontoNrSKR03"          => "fs_skr03_kontonr"
    ,   "KontoNrSKR04"          => "fs_skr04_kontonr"
    ,   "Steuerschluessel"      => "fs_steuerschluessel"
    ,   "Kostenstelle"          => "fs_kostenstelle"
    ,   "KontoArt"              => "fs_ktoart"
    ,   "AnzeigeArt"            => "fs_anzeigeart"                // Bedeutung dieses Eintrages unklar
    ) ;

    /**
     * _SKRKonto    constructor
     * @param       string  $_ktonr
     */
    public function __construct( $_ktonr="") {
        parent::__construct( "fibu_skr", "fs_ktonr", "kun");
        $this->regTransTable( self::$nameTransTable) ;
        if ( $_ktonr != "") {
            $this->setKey( $_ktonr) ;
        }
    }
}
