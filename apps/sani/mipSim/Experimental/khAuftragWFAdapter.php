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
class   _AuftragWFAdapter   extends FDbObject    {

    /**
     *  die folgenden Attribute existiern (noch) nicht in der Tabelle und muessen daher explizit erzeugt werden!
     */

    /**
     * @var array   translation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "Id"                    => "aw_id"
    ,   "AuftragId"             => "aw_aufid"
    ,   "WFTyp"                 => "aw_typ"
    ,   "RefObjektId"           => "aw_lid"
    ,   "Datei"                 => "aw_file"
    ,   "Datum"                 => "aw_date"
    ,   "Parameter1"            => "aw_param"
    ,   "Parameter2"            => "aw_param2"
    ,   "UserId"                => "aw_user"                           //  IP Adresse der letzten Bearbeitung
    ,   "LUserId"               => "aw_luser"
    ,   "Geloscht"              => "aw_deleted"
    ,   "Storno"                => "aw_storno"
    ,   "Gutschrift"            => "aw_gutschrift"
    ,   "DatumZeit"             => "aw_datetime"
    ,   "LDatumZeit"            => "aw_ldatetime"
    ) ;

    /**
     *
     */
    protected   function _postInstantiate() {
        error_log( __CLASS__."::".__METHOD__) ;
        $this->regTransTable( self::$nameTransTable) ;
    }

    /**
     *
     */
    protected   function _postLoad() {
        error_log( __CLASS__."::".__METHOD) ;
    }
}
