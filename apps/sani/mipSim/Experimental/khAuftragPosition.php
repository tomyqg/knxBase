<?php

/**
 * Class _AuftragPosition
 */
class   _AuftragPosition    extends __AuftragPositionAdapter    {

    const	ModusZuzahlungManuell   =   "m" ;
    const	ModusZuzahlungSystem    =   "" ;
    const	ModusZuzahlungSystemS   =   "s" ;

    // Gesamtbetrag des Abgabepreises für diese Position (Menge * Preis)

    public  $gesamtAbgabePreisNetto         =   0.00 ;
    public  $gesamtAbgabePreisMwst          =   0.00 ;
    public  $gesamtAbgabePreisBrutto        =   0.00 ;

    // Gesamtbetrag des Kostenträgeranteils (Menge * KT-Anteil)

    public  $gesamtKTAnteilNetto         =   0.00 ;
    public  $gesamtKTAnteilMwst          =   0.00 ;
    public  $gesamtKTAnteilBrutto        =   0.00 ;

    public  $gesamtKomponentenPreisNetto    =   0.00 ;
    public  $gesamtKomponentenPreisMwst     =   0.00 ;
    public  $gesamtKomponentenPreisBrutto   =   0.00 ;

    public  $gesamtEigenanteilNetto   =   0.00 ;
    public  $gesamtEigenanteilMwst    =   0.00 ;
    public  $GesamtEigenanteilBrutto  =   0.00 ;

    /**
     * _AuftragPosition constructor.
     */
    public  function    __construct() {
        parent::__construct( "auf_pos", "ap_id", "kun") ;
        $this->regTransTable( self::$nameTransTable) ;
        $this->setIdKey( "ap_id") ;
    }

    /**
     * deepConsolidate
     *
     * Konsolidiert die Auftragsposition auf Basis der fundamentalsten Informationen:
     *
     *      ArtikelNr, HMVNr, LKZ, Leistungserbringer, Kostenträger
     *
     * Alle Benutzermodifizierten Daten gehen dabei verloren. Das Vorgehen entspricht vom
     * Grundsatz dem Einfügen einer neuen Auftragsposition.
     *
     * @param $_auftrag _Auftrag
     */
    public function deepConsolidate( $_auftrag) {
        $this->consolidate( $_auftrag) ;
    }
    /**
     * Methode consolidate
     *
     * Auftragsposition konsolidieren.
     *
     * Reihenfolge der Operationen:
     *
     *  1. Mehrwertsteuer-Beträge der Einzelpositionen ermitteln
     *  2. Zulage der Bausaetze ermitteln
     *  3. Zuzahlung der Hauptpositionen ermitteln
     *
     * @param $_auftrag
     * @return  void
     */
    public  function    consolidate( $_auftrag) {
        /**
         *  determine total 'Brutto' for this item
         */

        $this->gesamtAbgabePreisNetto     =   0.00 ;
        $this->gesamtAbgabePreisMwst      =   0.00 ;
        $this->gesamtAbgabePreisBrutto    =   0.00 ;
        $this->gesamtEigenanteilNetto     =   0.00 ;
        $this->gesamtEigenanteilMwst      =   0.00 ;
        $this->GesamtEigenanteilBrutto    =   0.00 ;

        /**
         *  1. Mehrwertsteuer-Beträge ermitteln
         */
        $this->_detAbgabePreis() ;
        $this->_detMwst() ;

        /**
         *  einfache Werte berechnen
         */
        $this->_detGesamtEigenanteilBrutto() ;        // menge * eigenanteil
        $this->_detGesamtZulageBrutto( $_auftrag) ;             // menge * zulage

        /**
         *  3. Zuzahlung der Hauptpositionen ermitteln
         *
         *  WENN ich eine Unterposition bin
         *  SONST_WENN ich Unterpositionen habe
         *  SONST
         *      Zuzahlungen bestimmen
         */
        if ( $this->uPosNo != 0) {
           $this->zuzahlungBrutto    =   0.00 ;
        } else {
            $this->gesamtKTAnteilBrutto  =  $this->kTAnteilBrutto ;
            $myOrderItem  =   new _AuftragPosition() ;
            $myOrderItem->setIterCond( array( "auftragId = " . $_auftrag->id, "posNo = " . $this->posNo, "uPosNo > 0")) ;
            $myOrderItem->setIterOrder( array( "posNo", "uPosNo")) ;
            foreach ( $myOrderItem as $item) {
                $item->consolidate( $_auftrag) ;
                $this->gesamtKTAnteilBrutto  +=  $item->kTAnteilBrutto ;
            }
            $this->_detZuzahlung( $_auftrag, $this->gesamtKTAnteilBrutto) ;

        }

        /**
         * 4. Gesamtbetraege bestimmen
         */
        $this->gesamtAbgabePreisNetto   =   $this->menge * $this->abgabePreisNetto ;
        $this->gesamtAbgabePreisBrutto  =   $this->menge * $this->abgabePreisBrutto ;
        $this->gesamtKTAnteilNetto      =   $this->menge * $this->kTAnteilNetto ;
        $this->gesamtKTAnteilBrutto     =   $this->menge * $this->kTAnteilBrutto ;
//        $this->gesamtZuzahlungBrutto    =   $this->mengeZuzahlung * $this->zuzahlungBrutto ;
        $this->gesamtZulageBrutto       =   $this->menge * $this->zulageBrutto ;

        $this->updateInDb() ;
    }

    /**
     * @param $_auftrag
     * @return  void
     */
    private function    _detZuzahlung( $_auftrag, $_value=0.00) {
        $zuzahlung  =   0.00 ;
        if ( $_value == 0.00) {
            $_value =   $this->kTAnteilBrutto ;
        }
        switch ( $this->modusZuzahlung) {
        case    _AuftragPosition::ModusZuzahlungSystem  :
        case    _AuftragPosition::ModusZuzahlungSystemS :
            if ( $_auftrag->zuzahlungsBefreiung == 1) {
                $this->zuzahlungBrutto   =   0.0 ;
            } else {
                switch ( $this->ap_ZUZAHLUNGSKEY) {
                case    1   :                                   // 10%, min. 5,-, max. 10,-
                    $this->zuzahlungBrutto    =   round(( $_value * 0.1), 2) ;
                    if ( $this->zuzahlungBrutto < 5.00) {
                        $this->zuzahlungBrutto   =   5.00 ;
                    } else if ( $this->zuzahlungBrutto > 10.00) {
                        $this->zuzahlungBrutto   =   10.00 ;
                    }
                    break ;
                case    2   :
                    $this->zuzahlungBrutto    =   $_value * 0.1 ;
                    if ( $this->zuzahlungBrutto > 10.00) {            // 10%, min 0,-, max. 10,-
                        $this->zuzahlungBrutto   =   10.00 ;
                    }
                    break ;
                }
            }
            $this->modusZuzahlung   =   _AuftragPosition::ModusZuzahlungSystemS ;
            break ;
        default :
            break ;
        }
        $zuzahlungBrutto  =   $this->zuzahlungBrutto ;

        $this->gesamtZuzahlungBrutto    =   ( $this->menge / $this->mengeWirtEinheit) * $this->zuzahlungBrutto ;

        return $zuzahlungBrutto ;
    }

    /**
     * @return  float
     */
    protected   function    _detGesamtEigenanteilBrutto() {
        $eigenanteil    =   $this->menge * $this->EigenanteilBrutto ;
        $this->GesamtEigenanteilBrutto    =   $eigenanteil ;

        return $eigenanteil ;
    }

    /**
     * @return  float
     */
    protected   function    _detGesamtZulageBrutto( $_auftrag) {

        /**
         *  2. Zulage eines moeglichen Bausatzes ermitteln
         */
        if ( $this->bausatzZielpreis != 0.00) {
            $this->gesamtKTAnteilBrutto =   0.00 ;
            $myOrderItem  =   new _AuftragPosition() ;
            $myOrderItem->setIterCond( array( "auftragId = " . $_auftrag->id, "posNo = " . $this->posNo)) ;
            $myOrderItem->setIterOrder( array( "uPosNo")) ;
            foreach ( $myOrderItem as $item) {
                $this->gesamtKTAnteilBrutto +=  $item->menge * $item->kTAnteilBrutto ;
            }
            $this->zulageBrutto =   $this->bausatzZielpreis - $this->gesamtKTAnteilBrutto ;
        }

        $zulage =   $this->menge * $this->zulageBrutto ;
        $this->gesamtZulageBrutto    =   $zulage ;

        return $zulage ;
    }
}
