<?php

/**
 * Class _KassenBelegPosition
 *
 * Zusätzliche Information zu Preisen:
 *
 * Menge                    =   Anzahl der Artikel
 *
 * UVP                      =   unverbindliche Preisempfehlung des Herstellers
 * AbgabePreis              =   Preis pro Stueck vor Abzug aller Rabatte zu dem das Sanitätshaus den Artikel an den Endverbraucher abgibt
 * KorrekturProzent         =   prozentuale Korrektur des Abgabepreises (kann sein + oder -)
 * KorrekturAbsolut         =   absolute Korrektur des Abgabepreises (kann sein + oder -)
 * EndPreis                 =   Preis pro Stueck nach Abzug aller Rabatte zu dem das Sanitaetshaus den Artikel an den Endverbraucher abgibt
 *                                  =>  EndPreis    =   Abgabepreis + (Abgabepreis * KorrekturProzent) + KorrekturAbsolut
 * GesamtPreis              =   Menge * EndPreis
 *                                  =>  GesamtPreis =   Menge * EndPreis
 *                                  =>  GesamtPreis =   GesamtKostentraegeranteil + GesamtEigenanteil + GesamtQualitaetszulage
 * GesamtPreisPatient       =   Gesamtpreis abzueglich des Kostentraegeranteils zuzueglich der Zuzahlung
 *                                  =>  GesamtPreisPatient  =   Gesamtpreis - GesamtKostentraegeranteil + Zuzahlung
 * KostentraegerAnteil      =   Betrag pro Stück den der Kostentraeger von den Kosten uebernimmt
 * GesamtKostentraegerAnteil=   Betrag auf die gesamte Abgabemenge den der Kostentraeger von den Kosten uebernimmt
 * Zuzahlung                =   Zuzahlung auf die gesamte Abgabemenge (immer brutto)
 * Eigenanteil              =   Eigenanteil pro Stueck (immer brutto)
 * GesamtEigenanteil        =   Eigenanteil auf die gesamte Abgabemenge (immer brutto)
 * Qualitätszulage          =   Qualitätszulage pro Stueck (immer bruttto)
 * GesamtQualitaetszulage   =   Qualitaetszulage auf die gesamte Abgabemenge (immer brutto)
 */
class   _KassenBelegPositionAdapter   extends FDbObject  {

    public $GesamtZuzahlungBrutto   =   0.00 ;
    public $GesamtEigenanteilBrutto =   0.00 ;
    public $GesamtZulageBrutto      =   0.00 ;
    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "Id"                    => "kp_id"                          //
    ,   "BelegId"               => "kp_kid"                         // => kk_id => _KassenBeleg::Id
    ,   "PosNr"                 => "kp_posnr"                       // Laufenden Positionsnummer
    ,   "ArtikelNr"             => "kp_artid"
    ,   "EAN"                   => "kp_ean"
    ,   "HMV"                   => "kp_hmv"
    ,   "Bezeichnung1"          => "kp_txt1"
    ,   "Bezeichnung2"          => "kp_txt2"
    ,   "Bezeichnung3"          => "kp_txt3"
    ,   "Einheit"               => "kp_einheit"

    //

    ,   "Menge"                 => "kp_menge"                       // Verkaufete Menge des Artikels
    ,   "SatzMwst"              => "kp_mwst_satz"                   // Mehrwertsteuersatz, ie. 0,07 oder 0,19
    ,   "SatzMwstId"            => "kp_mwst_id"                     // Id des Mehrwertsteuersatzes
    ,   "GesamtBetragMwst"      => "kp_mwst_betrag"                 // Gesamtbetrag des Mehrwertsteuer
    ,   "KorrekturAbsolut"      => "kp_rabatt_a"
    ,   "KorrekturProzent"      => "kp_rabatt_p"

    //  Abgabepreise + Mwst.

    ,   "AbgabePreisNetto"      => "kp_vk_netto"
    ,   "AbgabePreisBrutto"     => "kp_vk_brutto"

    //  Kostentraegeranteil

    ,   "KTAnteilNetto"         => "kp_kk_netto"
    ,   "KTAnteilBrutto"        => "kp_kk_brutto"

    //  Zuzahlung, Eigenanteil, Zulage

    ,   "ZuzahlungBrutto"       => "kp_zuz_betrag"
    ,   "EigenanteilBrutto"     => "kp_eig_betrag"
    ,   "ZulageBrutto"          => "kp_qzu_betrag"                  //

    //  Daten fuer Buchhaltung etc.

    ,   "Warengruppe"           => "kp_wgr"
    ,   "ErloesKonto"           => "kp_erloeskonto"

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
        error_log( __CLASS__."::".__METHOD__) ;
    }
}
