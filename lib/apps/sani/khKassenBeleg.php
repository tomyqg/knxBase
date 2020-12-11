<?php

/**
 * KassenbuchKopf
 */
class   _KassenBeleg   extends FDbObject {

    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "Id" => "kk_id"
    ,   "Filiale"               => "kk_filiale"
    ,   "IdentId"               => "kk_identid"
    ,   "SelIdent"              => "kk_selident"
    ,   "IdIdent"               => "kk_Idident"
    ,   "KassiererId"           => "kk_kassiererid"
    ,   "UserId"                => "kk_userid"
    ,   "BelegNr"               => "kk_belegid"
    ,   "KundeNr"               => "kk_kunid"
    ,   "Datum"                 => "kk_datum"
    ,   "RefBelegNr"            => "kk_vbeleg"
    ,   "GesamtBetrag"          => "kk_kasse_betrag"                // Gesamtbetrag des Belegs
    ,   "GesamtNetto1"          => "kk_kassebe_netto1"              // Gesamtbetrag Netto Mwstsatz 1
    ,   "GesamtNetto2"          => "kk_kassebe_netto2"              // Gesamtbetrag Netto Mwstsatz 2
    ,   "GesamtNetto3"          => "kk_kassebe_netto3"
    ,   "GesamtNetto4"          => "kk_kassebe_netto4"
    ,   "GesamtMwst1"           => "kk_kassebe_mwst1"               // Gesamtbetrag Mehrwertsteuer 1
    ,   "GesamtMwst2"           => "kk_kassebe_mwst2"               // GEsamtbetrag Mehrwertsteuer 2
    ,   "GesamtMwst3"           => "kk_kassebe_mwst3"
    ,   "GesamtMwst4"           => "kk_kassebe_mwst4"
    ,   "Waehrung"              => "kk_kassen_waehrung"
    ,   "VorgangArt"            => "kk_vorgangsart"
    ,   "VorgangId"             => "kk_vorgangsid"
    ,   "AuftragGesamtBetrag"   => "kk_gesamtbetrag"                // Gesamtbetrag des Auftrags = Kostentraeger + Zuzahlung + Eigenanteil + Zulage
    ,   "AuftragGesamtMwst1"    => "kk_gber_mwst1"                  // Gesamtbetrag Mehrwertsteuer 1 des Auftrags (wie oben)
    ,   "AuftragGesamtMwst2"    => "kk_gber_mwst2"                  // Gesamtbetrag Mehrwertsteuer 2 des Auftrags (wie oben)
    ,   "AuftragGesamtMwst3"    => "kk_gber_mwst3"
    ,   "ZuzahlungGesamt"       => "kk_zuz_betrag"                  // Gesamtbetrag der Zuzahlung
    ,   "ZuzahlungGesamtMwst1"  => "kk_zuz_mwst1"                   // Gesamtbetrag Mehrwertsteuer 1 der Zuzahlung
    ,   "ZuzahlungGesamtMwst2"  => "kk_zuz_mwst2"                   // Gesamtbetrag Mehrwertsteuer 2 der Zuzahlung
    ,   "EigenanteilGesamt"     => "kk_eig_betrag"                  // Gesamtbetrag des Eigenanteils
    ,   "EigenanteilGesamtMwst1" => "kk_eig_mwst1"                  // Gesamtbetrag Mehrwertsteuer 1 des Eigenanteils
    ,   "EigenanteilGesamtMwst2" => "kk_eig_mwst2"                  // Gesamtbetrag Mehrwertsteuer 2 des Eigenanteils
    ,   "ZulageGesamt"          => "kk_qzu_betrag"                  // Gesamtbetrag der Qualitaetszulage
    ,   "ZulageGesamtMwst1"     => "kk_qzu_mwst1"                   // Gesamtbetrag Mehrwertsteuer 1 der Qualitaetszulage
    ,   "ZulageGesamtMwst2"     => "kk_qzu_mwst2"                   // Gesamtbetrag Mehrwertsteuer 2 der Qualitaetszulage
    ,   "SatzMwst1"             => "kk_satz_mwst1"                  // Mehrwertsteuersatz der Mehrwertsteuer 1, ie. 0,07 oder 0,19
    ,   "SatzMwst2"             => "kk_satz_mwst2"                  // dto. der Mehrwertsteuer 2
    ,   "Storno"                => "kk_storno"                      // Stornobeleg "J"a oder ""
    ,   "StornoGrund"           => "kk_stornogrund"
    ,   "StornoReferenzBelegNr" => "kk_storno_kbid"                 // Belegnr. des stornierten Beleges
    ,   "BetragGegeben"         => "kk_gegeben"                     // Betrag der vom Kunden gegeben wurde
    ,   "BetragRueckgabe"       => "kk_rest"                        // Betrag der an den Kunden zurueck gegeben wurde
    ,   "BetragAnzahlung"       => "kk_anzahlung"                   //
    ,   "BetragRest"            => "kk_restsumme"
    ,   "Buchungstext"          => "kk_text"
    ,   "DateiName"             => "kk_file"
    ,   "ExportKZ"              => "kk_exportkz"
    ,   "Kostenstelle1"         => "kk_kostst1"
    ,   "Kostenstelle2"         => "kk_kostst2"
    ,   "AuftragId"             => "kk_aufid"
    ,   "RechnungNr"            => "kk_rechnr"
    ) ;
    private static  $lf  =   "\n" ;
    private static  $trenner = "------------------------------------------";

    /**
     * _KassenbuchKopf constructor.
     * @param string $_belegNr
     */
    public  function __construct( $_belegNr="") {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
        parent::__construct( "kassenbuch_k", "kk_id", "kun");
        $this->regTransTable( self::$nameTransTable) ;
        $this->setIdKey( "kk_id");
        if ( $_belegNr != "") {
            $this->getByBelegNr( $_belegNr) ;
        }
        FDbg::end() ;
    }

    /**
     * @param string $_key
     * @return  void
     */
    public  function    setKey( $_key) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '$_key')") ;
        FDbg::end() ;
    }

    /**
     * @param $_belegNr
     * @return bool
     */
    public  function    getByBelegNr( $_belegNr) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '$_belegNr')") ;
        $this->fetchFromDbWhere( array( "kk_belegid = $_belegNr")) ;
        FDbg::end() ;
        return $this->_valid ;
    }

    /**
     * @param $_base
     * @return bool
     */
    public  function    create( $_base) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '<object>')") ;
        switch ( get_class( $_base)) {
        case    "_Auftrag"  :
            $this->_presetFromAuftrag() ;       // erforderliche Daten aus dem Auftrag in den Beleg kopieren
            $this->getNextBelegId() ;           // BelegNr. holen
            $this->storeInDb() ;                // und ab in die Datenbank
            break ;
        case    "_Rechnung" :
            $this->_presetFromRechnung() ;
            break ;
        }
        FDbg::end() ;
        return $this->_valid ;
    }

    /**
     * @param _Auftrag  $_auftrag
     */
    private function    _presetFromAuftrag( $_auftrag) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '<object>')") ;
        $this->KundeNr  =   $_auftrag->KundeNr ;
        $this->Datum    =   self::today() ;
        FDbg::end() ;
    }

    /**
     * @param _Rechnung $_rechnung
     */
    private function    _presetFromRechnung( $_rechnung) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '<object>')") ;
        FDbg::end() ;
    }

    /**
     * @param $_belegId
     * @return  _KassenBeleg
     */
    public  function    cancel( $_belegId) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "( '$_belegId')") ;

        /**
         *
         */
        if ( $this->isValid()) {

            /**
             * create the cancellation entry
             */
            $myStornoBeleg   =   new _KassenBeleg() ;
            $myStornoBeleg->copyFrom( $this) ;
            $myStornoBeleg->getNextBelegId() ;
            $myStornoBeleg->_invertAmounts() ;
            $myStornoBeleg->RefBelegNr    =   $this->BelegNr ;
            $myStornoBeleg->VorgangArt  =   10 ;                // 10= Storno
            $myStornoBeleg->VorgangId   =   $this->BelegNr ;   // 10= Storno
            $myStornoBeleg->Buchungstext    =   "Storno zu Beleg Nr. " . $this->BelegNr ;
            $myStornoBeleg->storeInDb() ;

            /**
             * update the original entry
             */
            $this->StornoReferenzBelegNr    =   $myStornoBeleg->BelegNr ;
            $this->Storno  =   "J" ;
            $this->updateColInDb( "kk_storno_kbid,kk_storno") ;

            /**
             * copy all entries, cancel and write to db
             */
            $myKassenbuchPosition   =   new _KassenBelegPosition() ;
            $myKassenbuchPosition->setIterCond( array( "kp_kid = {$this->kk_id}")) ;
            $myKassenbuchPosition->setIterOrder( array( "kp_posnr")) ;
            $myStornoEntry  =   new _KassenBelegPosition() ;
            foreach ( $myKassenbuchPosition as $kp) {
                $myStornoEntry->copyFrom( $kp) ;
                $myStornoEntry->BelegId    =   $myStornoBeleg->kk_id ;
                $myStornoEntry->cancel() ;
                $myStornoEntry->storeInDb() ;
            }

            /**
             * create workflow entry
             */
            $myWorkflowStep =   new _WorkflowStep() ;
            $myWorkflowStep->Typ    =   "BON" ;
            $myWorkflowStep->RefNr  =   $myStornoBeleg->BelegNr ;
            $myWorkflowStep->UserId =   $this->UserId ;
            $myWorkflowStep->AuftragId    =   $this->AuftragId ;
            $myWorkflowStep->StoreInDb() ;

        } else {

        }
        FDbg::end() ;
        return $myStornoBeleg ;
    }

    /**
     * inserts needed stuff for datev export to required fibu_log table
     * @return  void
     */
    public function prepareDatevExport() {

        /**
         * copy all entries, cancel and write to db
         */
        $myKassenbuchPosition   =   new _KassenBelegPosition() ;
        $myKassenbuchPosition->setIterCond( array( "kp_kid = {$this->Id}")) ;
        $myKassenbuchPosition->setIterOrder( array( "kp_posnr")) ;
        $myFibuLogEntry =   new _FibuLog() ;
        $myFibuLogEntry->Datum  =   $this->Datum ;
        $myFibuLogEntry->BelegNr  =   $this->BelegNr ;
        $myFibuLogEntry->Exportiert =   "nein" ;                // not yet exported to Datev
        foreach ( $myKassenbuchPosition as $kp) {
            $myFibuLogEntry->Filiale    =   $this->Filiale ;
            $myFibuLogEntry->Buchungstext1   =   "Kassenvorgang: Beleg #{$this->BelegNr} Kasse {$this->IdentId}" ;
            $myFibuLogEntry->storeInDb() ;
        }
    }

    /**
     * @return mixed
     */
    public  function    getNextBelegId() {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
        $myKassenbuchKopf    =   new _KassenBeleg() ;
        $myKassenbuchKopf->first( array( "kk_filiale = ".$this->kk_filiale), array( "kk_belegid DESC")) ;
        $this->kk_belegid =   $myKassenbuchKopf->kk_belegid + 1 ;
        FDbg::end() ;
        return $this->kk_belegid ;
    }

    /**
     * @param bool $barverkauf
     * @param bool $storno
     * @param string $verwendungszweck
     * @return mixed
     */
    public  function    generateBon( $barverkauf = false, $storno = false, $verwendungszweck = "") {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( barverkauf=".($barverkauf?"true":"false").", storno=".($storno?"true":"false").", '$verwendungszweck')") ;

        /**
         * Stelle den Bezug zum Auftrag her
         * => dazu müssen wir erst über die AuftragWF Tabelle den Eintrag für DIESEN Storno-Bon suchen, dort
         * findet sich ein Verweis auf die Auftragsnummer
         */
        $myAuftragWF    =   new _WorkflowStep() ;
        $myAuftragWF->fetchFromDbWhere( array( "aw_typ = 'BON'", "aw_lid = '{$this->BelegNr}'")) ;

        /**
         *  WENN kein Workflow Eintrag zu dieser BelegNr existiert
         *      ->  nix zu tun
         */
        if ( !$myAuftragWF->isValid()) {
            FDbg::end( "line := " . __LINE__) ;
            return "noprint" ;
        }

        $myAuftrag  =   new _Auftrag() ;
        $myAuftrag->fetchFromDbWhere( array( "a_id = " . $myAuftragWF->AuftragId . "")) ;

        /**
         *  WENN Auftrag nicht geladen werden konnte
         *      -> nix zu tun
         */
        if ( !$myAuftrag->isValid()) {
            FDbg::end( "line := " . __LINE__) ;
            return "noprint" ;
        }

        /**
         *
         */
        if ($_SESSION["ladenkasse"]["lk_uebergabe_an_externes_system_aktiv"] == "1") {
            FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "übergabe an externes system aktiv") ;
            FDbg::end( "line := " . __LINE__) ;
//            return self::generateBonExport();               // ohne Auftrag wird die Methode sich selber eine Auftrag zusammensuchen (=> Cache!)
            //            return self::generateBonExport( $_belegId);
        }

        /**
         *  Hol den Kopf-Block für den Bon
         */
        $data   =   self::getBonHeader( $storno);

        $data.= "Beleg-Nr: " . $this->BelegNr . self::$lf;

        if ( $barverkauf) {
            $belegNr    =   $this->BelegNr ;
            $verkaeufer = $_SESSION['verkaeufer'];
        } else {
            $belegNr = $myAuftrag->a_AuftragId ;
            $verkaeufer = $myAuftrag->a_Verkaeufer;
            $data.= "Auftrag-Nr: " . $belegNr . self::$lf;
        }

        $kassierer = $this->KassiererId ;

        if ($kassierer == "0") {
            $kassierer = $myAuftrag->a_UserId ;
        }
        $this->__dump() ;
        $myAuftragWF->__dump() ;
        $myAuftrag->__dump() ;
        if ($kassierer != $verkaeufer) {
            $myKassierer  =   new _Mitarbeiter() ;
            $myKassierer->setId( $kassierer) ;
            if ( $myKassierer->isValid()) {
                $kassiererVor = '';
                if ( $myKassierer->ma_vorname != "") {
                    $kassiererVor = substr( $myKassierer->ma_vorname, 0, 1) . ". ";
                }
                $kassiererName = $kassiererVor . $myKassierer->ma_name;
            }

            if ( empty($kassiererName)) {
                $kassiererSessVor = '';
                if ( $_SESSION["mitarbeiter"]["ma_vorname"]) {
                    $kassiererSessVor = substr($_SESSION["mitarbeiter"]["ma_vorname"], 0, 1) . ". ";
                }
                $kassiererName = $kassiererSessVor . $_SESSION["mitarbeiter"]["ma_name"];
            }

            $data.= "Kassierer: " . $kassiererName . self::$lf;

            $myVerkaeufer  =   new _Mitarbeiter() ;
            $myVerkaeufer->setId( $verkaeufer) ;
            $rt = '';

            if ( $myVerkaeufer->isValid()) {
                if ( $myVerkaeufer->ma_vorname != "") {
                    $rt = substr($myVerkaeufer->ma_vorname, 0, 1) . ". " . $myVerkaeufer->ma_name;
                } else {
                    $rt = $myVerkaeufer->ma_name;
                }
            }
            $myVerkaeufer->__dump() ;
            $myKassierer->__dump() ;
        } else {
            $rt = substr( $_SESSION["mitarbeiter"]["ma_vorname"], 0, 1) . ". " . $_SESSION["mitarbeiter"]["ma_name"];
        }

        if ( $storno) {
            $stornoDurchSessVor = '';
            if ($_SESSION["mitarbeiter"]["ma_vorname"]) {
                $stornoDurchSessVor = substr($_SESSION["mitarbeiter"]["ma_vorname"], 0, 1) . ". ";
            }
            $stornoDurch = $stornoDurchSessVor . $_SESSION["mitarbeiter"]["ma_name"];
            $data.= "Storno durch: " . $stornoDurch . self::$lf;
        }

        $data.= $_SESSION["ladenkasse"]["lk_firma_verkaeufer_begriff"] . ": " . $rt . self::$lf;

        if ( $myAuftrag->a_KunVorname != "" || $myAuftrag->a_KunNachname != "") {
            $data.= "Kunde: " . trim( $myAuftrag->a_KunVorname . " " . $myAuftrag->a_KunNachname) . self::$lf;
            $data.= "       " . $myAuftrag->a_KunStrasse . self::$lf;
            $data.= "       " . $myAuftrag->a_KunPLZ . " " . $myAuftrag->a_KunOrt . self::$lf;
        }

        if (!$barverkauf && $verwendungszweck != '') {
            $data.= self::$lf . "Verwendungszweck: " . self::$lf . $verwendungszweck . self::$lf;
        }

        $data.= self::$lf . self::$lf . "Bezeichnung      Menge    Privat   Zuzahl" . self::$lf . self::$trenner . self::$lf;

        $count = 0;

        $gesamtZuzahlung = $this->ZuzahlungGesamt ;
        $gesamtPreis = $this->GesamtBetrag ;
        $gesPreis = bcsub( $gesamtPreis, $gesamtZuzahlung, __CUR_DEC);

        $myKassenbuchPosition   =   new _KassenBelegPosition() ;
        $myKassenbuchPosition->setIterCond( "kp_kid = " . $this->Id . " ") ;

        /**
         *  Für alle Einträge die zu diesem Bon gehoeren
         */
        foreach ( $myKassenbuchPosition as $kP) {
            $count++;

            /**
             *  Versuche zu bestimmen ob das eine Privatposition ist:
             *  WENN die Zuzahlung 0 ist (Zuzahlung != 0 => Kostentraegerposition)
             */
            $privatPos  =   false ;
            if ( $kP->kp_zuz_betrag == 0.0) {
                $diff   =   abs( ( $kP->kp_menge * $kP->kp_vk_brutto) + ( $kP->kp_p_brutto * 1.0)) ;
                if (  $diff < 0.03) {
                    $privatPos  =   true ;
                }
            }
            /**
             *  WENN Privatposition
             */
            if ( $privatPos) {
                $line   =   str_pad( substr( $kP->kp_txt1, 0, 16), 18, ' ')
                    .   str_pad( PreisLib::MengeToUI( $kP->kp_menge), 6, ' ')
                    .   str_pad( str_pad( PreisLib::toUI( $kP->kp_vk_brutto), 7, ' ', STR_PAD_LEFT), 10, ' ')
                    .   str_pad( str_pad( "--", 7, ' ', STR_PAD_LEFT), 10, ' ');
            } else {
                $line   =   str_pad( substr( $kP->kp_txt1, 0, 16), 18, ' ')
                    .   str_pad( PreisLib::MengeToUI( $kP->kp_menge), 6, ' ')
                    .   str_pad( str_pad( PreisLib::toUI( $kP->kp_qzu_betrag), 7, ' ', STR_PAD_LEFT), 10, ' ')
                    .   str_pad( str_pad( PreisLib::toUI( $kP->kp_zuz_betrag), 7, ' ', STR_PAD_LEFT), 10, ' ');
            }
            $data   .=  trim($line) . self::$lf;
            $data   .=  self::printBezWithHmv( $kP->kp_txt1, $kP->kp_hmv);
        }

        $data.= self::$lf;
        $data.= self::$trenner . self::$lf;
        if (!$storno) {
            $_SESSION["gesamtbetrag"]   =   bcsub( $myAuftrag->a_GesamtPreisPrivat, $myAuftrag->a_GesamtZuzahlung, __CUR_DEC);
            $_SESSION["zuzahlung"]      =   $myAuftrag->a_GesamtZuzahlung;
            $_SESSION["zahlbetrag"]     =   $myAuftrag->a_GesamtPreisPrivat;
        }

        $qualitxt = $_SESSION["ladenkasse"]["lk_firma_qualizulage_begriff"];
        if (!$qualitxt) {
            $qualitxt = "Qualitätszulage";
        }
        if ( $this->ZulageGesamt != 0.00) {
            $data.= str_pad(substr( $qualitxt . ":", 0, 30), 33, ' ') . str_pad( PreisLib::toUI( $this->ZulageGesamt->kk_qzu_betrag), 8, ' ', STR_PAD_LEFT) . self::$lf;
        }
        $data.= str_pad("Privat gesamt inkl. " . substr( $qualitxt, 0, 12) . ":", 33, ' ', STR_PAD_RIGHT) . str_pad( PreisLib::toUI( $gesPreis), 8, ' ', STR_PAD_LEFT) . self::$lf;
        $data.= "Gesetzliche Zuzahlung:           " . str_pad( PreisLib::toUI($gesamtZuzahlung), 8, ' ', STR_PAD_LEFT) . self::$lf;
        if ( $this->GesamtMwst1 > 0.00) {
            $data.= str_pad("Enthaltene Mwst (19%):", 33, ' ', STR_PAD_RIGHT) . str_pad(PreisLib::toUI( $this->GesamtMwst1), 8, ' ', STR_PAD_LEFT) . self::$lf;
        }

        if ( $this->GesamtMwst2 > 0.00) {
            $data.= str_pad("Enthaltene Mwst (7%):", 33, ' ', STR_PAD_RIGHT) . str_pad(PreisLib::toUI( $this->GesamtMwst2), 8, ' ', STR_PAD_LEFT) . self::$lf;
        }

        $data.= self::$lf;
        $data.= str_pad("Zahlbetrag:", 33, ' ', STR_PAD_RIGHT) . str_pad( PreisLib::toUI($gesamtPreis), 8, ' ', STR_PAD_LEFT) . self::$lf;

        if ( !empty( self::$given)) {
            if ( !empty( self::$teleCash)) {
                $data.= self::$lf;
                $data.= "Letzte Zahlung (EC/TeleCash):    " . str_pad( PreisLib::toUI(self::$given), 8, ' ', STR_PAD_LEFT) . self::$lf;
            } else {
                $data.= self::$lf;
                $data.= "Letzte Zahlung:                  " . str_pad( PreisLib::toUI(self::$given), 8, ' ', STR_PAD_LEFT) . self::$lf;
                $data.= "Rückgeld:                        " . str_pad( PreisLib::toUI(self::$drawback), 8, ' ', STR_PAD_LEFT) . self::$lf;
            }
            $belegNr = self::$receipt;
        } else {
            if (!$storno) {
                if ($_SESSION["vorgang_abschluss_zahlart"] == "telecash") {
                    $data.= "Gegeben EC/TeleCash:             " . str_pad( PreisLib::toUI($_SESSION['vorgang_abschluss_gegeben']), 8, ' ', STR_PAD_LEFT) . self::$lf;
                } else {
                    $data.= "Gegeben:                         " . str_pad( PreisLib::toUI($_SESSION['vorgang_abschluss_gegeben']), 8, ' ', STR_PAD_LEFT) . self::$lf;
                    $data.= "Rückgeld:                        " . str_pad( PreisLib::toUI($_SESSION['vorgang_abschluss_rueckgeld']), 8, ' ', STR_PAD_LEFT) . self::$lf;
                }
            }
        }

        $data.= self::$lf;
        $data.= self::$trenner . self::$lf;
        if (!$storno && isset($_SESSION["ladenkasse"]["lk_bon_zusatztext"])) {
            $data.= Printer::separateStringToMultiline($_SESSION["ladenkasse"]["lk_bon_zusatztext"], " ", 40) . self::$lf;
            $data.= self::$trenner . self::$lf;
        }

        if (!$storno && isset($_SESSION["ladenkasse"]["lk_bon_endtext"])) {
            $data.= self::$lf;
            $data.= Printer::separateStringToMultiline($_SESSION["ladenkasse"]["lk_bon_endtext"], " ", 40) . self::$lf;
        }

        if ($storno && $myAuftrag->a_KunVorname == "" && $myAuftrag->a_KunNachname == "") {
            $data.= self::$lf . self::$lf . "------------------------------------------" . self::$lf
                . "Vorname Name" . self::$lf . self::$lf
                . "------------------------------------------" . self::$lf
                . "Strasse Nr" . self::$lf . self::$lf
                . "------------------------------------------" . self::$lf
                . "PLZ Ort" . self::$lf;
        }
        if ($storno) {
            $data.= self::$lf . self::$lf . self::$lf . "------------------------------" . self::$lf
                . "Betrag erhalten" . self::$lf;
        }
        FDbg::traceData( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Printer output", $data) ;
        FDbg::end() ;
        return self::saveFiledata( $belegNr, __PRINTMODE_BON, "<text>".$data."</text>");
    }

    /**
     * @param   bool        $_storno
     * @return  string
     */
    function getBonHeader( $_storno=false) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
        $data = "";

        if ($_storno) {
            $data.= "*****           S T O R N O          *****" . self::$lf . self::$lf;
        }

        $data.= $_SESSION["ladenkasse"]["lk_firma_name"] . self::$lf;
        $data.= str_replace("§", "ss", $_SESSION["ladenkasse"]["lk_firma_strasse"]) . self::$lf;
        $data.= $_SESSION["ladenkasse"]["lk_firma_plz"] . " " . $_SESSION["ladenkasse"]["lk_firma_ort"] . self::$lf;
        if (trim($_SESSION["ladenkasse"]["lk_firma_telefon"])) {
            $data.= "Tel.: " . $_SESSION["ladenkasse"]["lk_firma_telefon"] . self::$lf;
        }
        if (trim($_SESSION["ladenkasse"]["lk_firma_telefax"])) {
            $data.= "Fax.: " . $_SESSION["ladenkasse"]["lk_firma_telefax"] . self::$lf;
        }
        $data.= self::$trenner . self::$lf;
        $data.= "Datum: " . date("d.m.Y") . " " . date("H:i") . " Uhr" . self::$lf;
        FDbg::end() ;
        return $data;
    }

    /**
     * @param $bezeichnung
     * @param $hmv
     * @param int $laenge
     * @return string
     */
    private static function printBezWithHmv($bezeichnung, $hmv, $laenge = 16) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
        $data = "";
        if (trim(substr($bezeichnung, $laenge)) !=  "") {
            $line = substr($bezeichnung, $laenge, $laenge);
            $data.= trim($line) . self::$lf;
        }
        if($hmv != "") {
            $line = "HMV: " . $hmv;
            $data.= $line . self::$lf;
        }
        FDbg::end() ;
        return $data;
    }

    /**
     * @param $bezeichnung
     * @param $art
     * @return string
     */
    private static function printBezWithArt($bezeichnung, $art) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
        $data = "";
        if (trim(substr($bezeichnung, 16)) !=  "") {
            $line = substr($bezeichnung, 16, 16);
            $data.= trim($line) . self::$lf;
        }
        if($art != "") {
            $line = "ArtNr: " . $art;
            $data.= $line . self::$lf;
        }
        FDbg::end() ;
        return $data;
    }

    static function saveFiledata( $belegNr, $type, $data, $filename = "", $dir = "", $escape = true) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
        $printer = $_SESSION['ladenkasse']['lk_bondrucker'];

        if ($escape) {
            $data = str_replace("&", "&amp;", $data);
        }

        switch ($type) {
        case __PRINTMODE_BON:
        case __PRINTMODE_BON_ENTNAHME:
            $__dir = __DIR_BONDRUCK;
            $xmltag = "bon";
        break;

        case __PRINTMODE_EXPORT:
            $__dir = __DIR_EXPORT;
        break;

        case __PRINTMODE_REZEPT:
            $__dir = __DIR_REZEPTDRUCK;
            $xmltag = "rezept";
            $printer = $_SESSION['ladenkasse']['lk_rezeptdrucker'];
            if ($filename == "") {
                $filename = $_SESSION['ladenkasse']['lk_id'] . "_re_" . $belegNr . "_" . time() . ".xml";
            }
        break;

        default:
            $__dir = __DIR_BONDRUCK;
            $xmltag = "bon";
        break;
        }

        if ($dir == "") {
            $dir = Mandant::getDir($__dir, "", "", true);
        }

        if ($filename == "") {
            $filename = $_SESSION['ladenkasse']['lk_id'] . "_" . $type . "_" . $belegNr . "_" . time() . ".xml";
        }

        if ($_SESSION["settings"]["print"]["generateXML"] == "1") {
            $usePos = 0;
            if ($_SESSION['ladenkasse']['lk_suffix'] == "\"1\"" || $_SESSION['ladenkasse']['lk_suffix'] == "1") {
                $usePos = 1;
            }
            if ($type != __PRINTMODE_EXPORT) {
                $data = "<" . $xmltag . " printerName=\"" . $printer . "\" posName=\"PosPrinter\" usePos=\"" . $usePos . "\">" . $data . "</" . $xmltag . ">";

                $msg["time"] = time();
                $msg["type"] = $type;
                $msg["lk_id"] = $_SESSION['ladenkasse']['lk_id'];
                $msg["id"] = $belegNr;
                $msg["filename"] = $filename;
                $msg["value"] = $dir . $filename;
                $msg["content"] = $data;
                $_SESSION["messages"][] = $msg;
            }
        }
        else {
            $filename = str_replace(".xml", ".txt", $filename);
        }

        //check/create folder
        if (!file_exists($dir)) {
            mkdir($dir, 0700, true);
        }

        //write file
        $fp = fopen($dir . $filename, 'w');
        fwrite($fp, $data);
        fclose($fp);
        FDbg::end() ;
        return $filename;
    }

    /**
     * invertAmounts
     * @return  void
     */
    private function    _invertAmounts() {
        $this->GesamtBetrag *=   -1.000 ;
        $this->GesamtNetto1 *=   -1.000 ;
        $this->GesamtNetto2 *=   -1.000 ;
        $this->GesamtNetto3 *=   -1.000 ;
        $this->GesamtNetto4 *=   -1.000 ;
        $this->GesamtMwst1 *=   -1.000 ;
        $this->GesamtMwst2 *=   -1.000 ;
        $this->GesamtMwst3 *=   -1.000 ;
        $this->GesamtMwst4 *=   -1.000 ;
        $this->AuftragGesamtBetrag *=   -1.000 ;
        $this->AuftragGesamtMwst1 *=   -1.000 ;
        $this->AuftragGesamtMwst2 *=   -1.000 ;
        $this->AuftragGesamtMwst3 *=   -1.000 ;
        $this->ZuzahlungGesamt *=   -1.000 ;
        $this->ZuzahlungGesamtMwst1 *=   -1.000 ;
        $this->ZuzahlungGesamtMwst2 *=   -1.000 ;
        $this->EigenanteilGesamt *=   -1.000 ;
        $this->EigenanteilGesamtMwst1 *=   -1.000 ;
        $this->EigenanteilGesamtMwst2 *=   -1.000 ;
        $this->ZulageGesamt *=   -1.000 ;
        $this->ZulageGesamtMwst1 *=   -1.000 ;
        $this->ZulageGesamtMwst2 *=   -1.000 ;
        $this->BetragGegeben *=   -1.000 ;
        $this->BetragRueckgabe *=   -1.000 ;
        $this->BetragAnzahlung *=   -1.000 ;
        $this->BetragRest *=   -1.000 ;
    }

    /**
     * @param string $_le
     * @return  void
     */
    public  function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}

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
class   _KassenBelegPosition   extends FDbObject {

    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */
    static  $nameTransTable     =   array(
        "Id"                    => "kp_id"                          //
    ,   "BelegId"               => "kp_kid"                         // => kk_id
    ,   "PosNr"                 => "kp_posnr"                       // Laufenden Positionsnummer
    ,   "ArtikelRef"            => "kp_artid"
    ,   "EAN"                   => "kp_ean"
    ,   "HMV"                   => "kp_hmv"
    ,   "Text1"                 => "kp_txt1"
    ,   "Text2"                 => "kp_txt2"
    ,   "Text3"                 => "kp_txt3"
    ,   "Einheit"               => "kp_einheit"

    //

    ,   "Menge"                 => "kp_menge"                       // Verkaufete Menge des Artikels
    ,   "SatzMwst"              => "kp_mwst_satz"                   // Mehrwertsteuersatz, ie. 0,07 oder 0,19
    ,   "SatzMwstId"            => "kp_mwst_id"                     // Id des Mehrwertsteuersatzes
    ,   "GesamtBetragMwst"      => "kp_mwst_betrag"                 // Gesamtbetrag des Mehrwertsteuer
    ,   "RabattAbsolut"         => "kp_rabatt_a"
    ,   "RabattProzent"         => "kp_rabatt_p"

    //  Abgabepreise + Mwst.

    ,   "AbgabePreisNetto"      => "kp_vk_netto"
    ,   "AbgabePreisBrutto"     => "kp_vk_brutto"

    //  Kostentraegeranteil

    ,   "KTAnteilNetto"         => "kp_kk_netto"
    ,   "KTAnteilBrutto"        => "kp_kk_brutto"

    //  Zuzahlung, Eigenanteil, Zulage

    ,   "Zuzahlung"             => "kp_zuz_betrag"
    ,   "Eigenanteil"           => "kp_eig_betrag"
    ,   "Zulage"                => "kp_qzu_betrag"                  //

    //  Daten fuer Buchhaltung etc.

    ,   "Warengruppe"           => "kp_wgr"
    ,   "ErloesKonto"           => "kp_erloeskonto"

    ) ;

    /**
     * _KassenbuchPosition constructor.
     */
    public function __construct() {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
        parent::__construct( "kassenbuch_p", "kp_id", "kun");
        $this->regTransTable( self::$nameTransTable) ;
        $this->setIdKey( "kp_id");
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
     * @return  void
     */
    public function cancel() {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__ . "()") ;
        $this->_invert() ;
        FDbg::end() ;
    }

    /**
     * invertAmounts
     * @return  void
     */
    public function    _invert() {
        $this->Menge *=   -1.000 ;
    }

    /**
     * @param string $_le
     * @return  void
     */
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}
