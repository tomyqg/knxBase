<?php
class   _WorkflowStep  extends FDbObject {

    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable   =   array(
        "Id" => "aw_id"
    ,   "AuftragId"       => "aw_aufid"
    ,   "Typ"             => "aw_typ"
    ,   "RefNr"           => "aw_lid"
    ,   "Datei"           => "aw_file"
    ,   "Datum"           => "aw_date"
    ,   "Parameter1"      => "aw_param"
    ,   "Parameter2"      => "aw_param2"
    ,   "UserId"          => "aw_user"
    ,   "UserIdLast"      => "aw_luser"
    ,   "Deleted"         => "aw_deleted"
    ,   "Storno"          => "aw_storno"                // Bedeutung dieses Eintrages unklar
    ,   "DatumAnlage"     => "aw_datetime"
    ,   "DatumZugriff"    => "aw_ldatetime"
    ) ;

    /**
     * _AuftragWF constructor.
     */
    public function __construct() {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
        parent::__construct("auftrag_wf", "aw_id", "kun");
        $this->regTransTable( self::$nameTransTable) ;
        $this->setIdKey( "aw_id");
        $this->Datum  =   $this->today() ;
        $this->Datei  =   "Es wurde noch keine Datei erzeugt!" ;
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

