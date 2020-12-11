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
class   _AuftragAdapter   extends FDbObject    {

    /**
     *  die folgenden Attribute existiern (noch) nicht in der Tabelle und muessen daher explizit erzeugt werden!
     */
    public  $GesamtEigenanteilBrutto    =   0.00 ;
    public  $lock    =   false ;

    /**
     * @var array   translation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "Id"                    => "a_id"
    ,   "AuftragNr"             => "a_AuftragId"
    ,   "AuftragArt"            => "a_AuftragArt"
    ,   "Datum"                 => "a_Datum"
    ,   "UserId"                => "a_UserId"
    ,   "Verkaeufer"            => "a_Verkaeufer"
    ,   "Sachbearbeiter"        => "a_Sachbearbeiter"
    ,   "IP"                    => "a_IP"                           //  IP Adresse der letzten Bearbeitung
    ,   "Fa"                    => "a_Fa"
    ,   "Mandant"               => "a_Mandant"
    ,   "Filiale"               => "a_Filiale"
    ,   "GeliefertDurch"        => "a_GeliefertDurch"
    ,   "KasseId"               => "a_Kasse"

    //  Kundenbezogene Daten

    ,   "ZuzahlungsBefreiung"   => "a_Befreit"
    ,   "ZuzahlungsBefreiungVon"=> "a_DatumBefreitVon"
    ,   "ZuzahlungsBefreiungBis"=> "a_DatumBefreitBis"

    //  Mwst. bezogene Daten

    ,   "BelegNr"               => "a_BelegNr"                      //  Belegnr. des opt. zugehoerigen Kassenbelegs

    //  Summen der Abgabebetraege

    ,   "GesamtAbgabeNetto"    => "a_GesamtvkpreisNetto"            // Gesamtsumme der Gesamtnettobetraege ueber alle Auftragspositionen
    ,   "GesamtAbgabeBrutto"   => "a_GesamtvkpreisBrutto"           // Gesamtsumme der Gesamtbruttobetraege ueber alle Auftragspositionen

    //  Summen der Kostentraegeranteile

    ,   "GesamtKTAnteilNetto"      => "a_GesamtpreisNetto"          // Gesamtsumme des Netto-Kostentraegeranteils
    ,   "GesamtKTAnteilBrutto"     => "a_GesamtpreisBrutto"         // Gesamtsumme des Brutto-Kostentraegeranteils

    //

    ,   "GesamtZuzahlungNetto"  => "a_GesamtZuzahlungNetto"         // Gesamtbetrag der Zuzahlungen netto
    ,   "GesamtZuzahlungBrutto" => "a_GesamtZuzahlung"              // Gesamtbetrag der Zuzahlungen

    //

    ,   "GesamtZulageNetto"     => "a_GesamtWirAufNetto"            // Gesamtbetrag der Zulagen netto
    ,   "GesamtZulageBrutto"    => "a_GesamtWirAuf"                 // Gesamtbetrag der Zulagen

        //  Summen der privat zu zahlenden Anteile

    ,   "GesamtPatientNetto"     => "a_GesamtpreisPrivatNetto"      // Gesamtsumme der Netto-Privatzahlung = Zuzahlung + Eigenanteil + Zulage
    ,   "GesamtPatientBrutto"    => "a_GesamtpreisPrivat"           // Gesamtsumme der Privatzahlung = Zuzahlung + Eigenanteil + Zulage

        //  Konsolidierung der Zahlungsdaten

    ,   "BetragGezahlt"         => "a_GezahltBetrag"                //
    ,   "BetragGezahltPrivat"   => "a_GezahltBetragEA"              //
    ,   "BetragGezahltKT"       => "a_GezahltBetragKK"              //
    ,   "BetragGezahltAZ"       => "a_GezahltBetragAZ"              //
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
