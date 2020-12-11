<?php
class   _WorkflowStep  extends FDbObject {

    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable   =   array(
        "id" => "aw_id"
    ,   "auftragId"       => "aw_aufid"
    ,   "typ"             => "aw_typ"
    ,   "refNr"           => "aw_lid"
    ,   "datei"           => "aw_file"
    ,   "datum"           => "aw_date"
    ,   "parameter1"      => "aw_param"
    ,   "parameter2"      => "aw_param2"
    ,   "userId"          => "aw_user"
    ,   "userIdLast"      => "aw_luser"
    ,   "deleted"         => "aw_deleted"
    ,   "storno"          => "aw_storno"                // Bedeutung dieses Eintrages unklar
    ,   "datumAnlage"     => "aw_datetime"
    ,   "datumZugriff"    => "aw_ldatetime"
    ) ;

    /**
     * _AuftragWF constructor.
     */
    public function __construct() {
        parent::__construct("auftrag_wf", "aw_id", "kun");
        $this->regTransTable( self::$nameTransTable) ;
        $this->setIdKey( "aw_id");
        $this->datum  =   $this->today() ;
        $this->datei  =   "Es wurde noch keine Datei erzeugt!" ;
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

