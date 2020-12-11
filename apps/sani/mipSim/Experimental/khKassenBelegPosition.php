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
class   _KassenBelegPosition   extends _KassenBelegPositionAdapter {

    /**
     * _KassenbuchPosition constructor.
     */
    public function __construct() {
        parent::__construct( "kassenbuch_p", "kp_id", "kun");
        $this->setIdKey( "kp_id");
    }

    /**
     * @param string $_key
     * @return  void
     */
    public function setKey( $_key) {
    }

    /**
     * @return  void
     */
    public function cancel() {
        $this->_invert() ;
    }

    /**
     * invertAmounts
     * @return  void
     */
    public function    _invert() {
        $this->menge *=   -1.000 ;
    }

    /**
     * @param string $_le
     * @return  void
     */
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}
