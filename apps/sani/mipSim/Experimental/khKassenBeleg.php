<?php

/**
 * KassenbuchKopf
 */
class   _KassenBeleg   extends _KassenBelegAdapter  {

    private static  $lf  =   "\n" ;
    private static  $trenner = "------------------------------------------";

    /**
     * _KassenbuchKopf constructor.
     * @param string $_belegNr
     */
    public  function __construct( $_belegNr="") {
        parent::__construct( "kassenbuch_k", "kk_id", "kun");
        $this->setIdKey( "kk_id");
        if ( $_belegNr != "") {
            $this->getByBelegNr( $_belegNr) ;
        }
    }

    /**
     * @param   int $_id
     * @return  void
     */
    public  function    setId( $_id=-1) {
        parent::setId( $_id) ;
        if ( $this->isValid()) {
            $this->_getFiliale() ;
        }
    }

    /**
     * @param string $_key
     * @return  void
     */
    public  function    setKey( $_key) {
        $this->fetchFromDbWhere( "BelegNr = '$_key'") ;
    }

    /**
     * @param $_belegNr
     * @return bool
     */
    public  function    getByBelegNr( $_belegNr) {
        if ( $this->fetchFromDbWhere( array( "BelegNr = $_belegNr"))) {
        }
        return $this->_valid ;
    }

    /**
     * @param $_base
     * @return bool
     */
    public  function    create( $_base) {
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
        return $this->_valid ;
    }

    /**
     * @param _Auftrag  $_auftrag
     */
    private function    _presetFromAuftrag( $_auftrag) {
        $this->kundeNr  =   $_auftrag->kundeNr ;
        $this->datum    =   self::today() ;
    }

    /**
     * @param _Rechnung $_rechnung
     */
    private function    _presetFromRechnung( $_rechnung) {
    }

    /**
     * @return  _KassenBeleg
     */
    public  function    cancel() {
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
            $myStornoBeleg->refBelegNr  =   $this->belegNr ;
            $myStornoBeleg->vorgangArt  =   10 ;                // 10= Storno
            $myStornoBeleg->vorgangId   =   $this->belegNr ;    // 10= Storno
            $myStornoBeleg->buchungstext    =   "Storno zu Beleg Nr. " . $this->belegNr ;
            $myStornoBeleg->storeInDb() ;

            /**
             * update the original entry
             */
            $this->stornoReferenzBelegNr    =   $myStornoBeleg->belegNr ;
            $this->storno  =   "J" ;
            $this->updateColInDb( "stornoReferenzBelegNr") ;
            $this->updateColInDb( "storno") ;

            /**
             * copy all entries, cancel and write to db
             */
            $myKassenbuchPosition   =   new _KassenBelegPosition() ;
            $myKassenbuchPosition->setIterCond( array( "belegId = {$this->id}")) ;
            $myKassenbuchPosition->setIterOrder( array( "posNr")) ;
            $myStornoEntry  =   new _KassenBelegPosition() ;
            foreach ( $myKassenbuchPosition as $kp) {
                $myStornoEntry->copyFrom( $kp) ;
                $myStornoEntry->belegId    =   $myStornoBeleg->id ;
                $myStornoEntry->cancel() ;
                $myStornoEntry->storeInDb() ;
            }

            /**
             * create workflow entry
             */
            $myWorkflowStep =   new _WorkflowStep() ;
            $myWorkflowStep->typ    =   "BON" ;
            $myWorkflowStep->refNr  =   $myStornoBeleg->belegNr ;
            $myWorkflowStep->userId =   $this->userId ;
            $myWorkflowStep->auftragId    =   $this->auftragId ;
            $myWorkflowStep->StoreInDb() ;

        } else {

        }
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
        $myFibuLogEntry->BelegNr  =   $this->belegNr ;
        $myFibuLogEntry->Exportiert =   "nein" ;                // not yet exported to Datev
        foreach ( $myKassenbuchPosition as $kp) {
            $myFibuLogEntry->Filiale    =   $this->Filiale ;
            $myFibuLogEntry->Buchungstext1   =   "Kassenvorgang: Beleg #{$this->belegNr} Kasse {$this->IdentId}" ;
            $myFibuLogEntry->storeInDb() ;
        }
    }

    /**
     * @return mixed
     */
    public  function    getNextBelegId() {
        $myKassenbuchKopf    =   new _KassenBeleg() ;
        $myKassenbuchKopf->first( array( "filiale = ".$this->kk_filiale), array( "belegNr DESC")) ;
        $this->belegNr =   $myKassenbuchKopf->belegNr + 1 ;
        return $this->belegNr ;
    }

    /**
     * @param bool $barverkauf
     * @param bool $storno
     * @param string $verwendungszweck
     * @return mixed
     */
    public  function    generateBon( $barverkauf = false, $storno = false, $verwendungszweck = "") {
        /**
         * Stelle den Bezug zum Auftrag her
         * => dazu müssen wir erst über die AuftragWF Tabelle den Eintrag für DIESEN Storno-Bon suchen, dort
         * findet sich ein Verweis auf die Auftragsnummer
         */
        $myAuftragWF    =   new _WorkflowStep() ;
        $myAuftragWF->fetchFromDbWhere( array( "aw_typ = 'BON'", "aw_lid = '{$this->belegNr}'")) ;

        /**
         *  WENN kein Workflow Eintrag zu dieser BelegNr existiert
         *      ->  nix zu tun
         */
        if ( !$myAuftragWF->isValid()) {
            return "noprint" ;
        }

        $myAuftrag  =   new _Auftrag() ;
        $myAuftrag->fetchFromDbWhere( array( "id = " . $myAuftragWF->auftragId . "")) ;

        /**
         *  WENN Auftrag nicht geladen werden konnte
         *      -> nix zu tun
         */
        if ( !$myAuftrag->isValid()) {
            return "noprint" ;
        }

        /**
         *
         */
        if ($_SESSION["ladenkasse"]["lk_uebergabe_an_externes_system_aktiv"] == "1") {
//            return self::generateBonExport();               // ohne Auftrag wird die Methode sich selber eine Auftrag zusammensuchen (=> Cache!)
            //            return self::generateBonExport( $_belegId);
        }

        /**
         *  Hol den Kopf-Block für den Bon
         */
        $data   =   self::getBonHeader( $storno);

        $data.= "Beleg-Nr: " . $this->belegNr . self::$lf;

        if ( $barverkauf) {
            $belegNr    =   $this->belegNr ;
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

        $gesamtZuzahlung = $this->gesamtZuzahlungBrutto ;
        $gesamtPreis = $this->gesamtBetrag ;
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
        if ( $this->zulageGesamt != 0.00) {
            $data.= str_pad(substr( $qualitxt . ":", 0, 30), 33, ' ') . str_pad( PreisLib::toUI( $this->zulageGesamt->kk_qzu_betrag), 8, ' ', STR_PAD_LEFT) . self::$lf;
        }
        $data.= str_pad("Privat gesamt inkl. " . substr( $qualitxt, 0, 12) . ":", 33, ' ', STR_PAD_RIGHT) . str_pad( PreisLib::toUI( $gesPreis), 8, ' ', STR_PAD_LEFT) . self::$lf;
        $data.= "Gesetzliche Zuzahlung:           " . str_pad( PreisLib::toUI($gesamtZuzahlung), 8, ' ', STR_PAD_LEFT) . self::$lf;
        if ( $this->gesamtMwst1 > 0.00) {
            $data.= str_pad("Enthaltene Mwst (19%):", 33, ' ', STR_PAD_RIGHT) . str_pad(PreisLib::toUI( $this->gesamtMwst1), 8, ' ', STR_PAD_LEFT) . self::$lf;
        }

        if ( $this->gesamtMwst2 > 0.00) {
            $data.= str_pad("Enthaltene Mwst (7%):", 33, ' ', STR_PAD_RIGHT) . str_pad(PreisLib::toUI( $this->gesamtMwst2), 8, ' ', STR_PAD_LEFT) . self::$lf;
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
        return self::saveFiledata( $belegNr, __PRINTMODE_BON, "<text>".$data."</text>");
    }

    /**
     * @param   bool        $_storno
     * @return  string
     */
    function getBonHeader( $_storno=false) {
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
        return $data;
    }

    /**
     * @param $bezeichnung
     * @param $hmv
     * @param int $laenge
     * @return string
     */
    private static function printBezWithHmv($bezeichnung, $hmv, $laenge = 16) {
        $data = "";
        if (trim(substr($bezeichnung, $laenge)) !=  "") {
            $line = substr($bezeichnung, $laenge, $laenge);
            $data.= trim($line) . self::$lf;
        }
        if($hmv != "") {
            $line = "HMV: " . $hmv;
            $data.= $line . self::$lf;
        }
        return $data;
    }

    /**
     * @param $bezeichnung
     * @param $art
     * @return string
     */
    private static function printBezWithArt($bezeichnung, $art) {
        $data = "";
        if (trim(substr($bezeichnung, 16)) !=  "") {
            $line = substr($bezeichnung, 16, 16);
            $data.= trim($line) . self::$lf;
        }
        if($art != "") {
            $line = "ArtNr: " . $art;
            $data.= $line . self::$lf;
        }
        return $data;
    }

    static function saveFiledata( $belegNr, $type, $data, $filename = "", $dir = "", $escape = true) {
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
        return $filename;
    }

    /**
     * invertAmounts
     * @return  void
     */
    private function    _invertAmounts() {
        $this->gesamtBetrag *=   -1.000 ;
        $this->gesamtNetto1 *=   -1.000 ;
        $this->gesamtNetto2 *=   -1.000 ;
        $this->gesamtNetto3 *=   -1.000 ;
        $this->gesamtNetto4 *=   -1.000 ;
        $this->gesamtMwst1 *=   -1.000 ;
        $this->gesamtMwst2 *=   -1.000 ;
        $this->gesamtMwst3 *=   -1.000 ;
        $this->gesamtMwst4 *=   -1.000 ;
        $this->gesamtZuzahlungNetto     *=   -1.000 ;
        $this->gesamtZuzahlungMwst1     *=   -1.000 ;
        $this->gesamtZuzahlungMwst2     *=   -1.000 ;
        $this->gesamtZuzahlungBrutto    *=   -1.000 ;
        $this->gesamtEigenanteilNetto   *=   -1.000 ;
        $this->gesamtEigenanteilMwst1   *=   -1.000 ;
        $this->gesamtEigenanteilMwst2   *=   -1.000 ;
        $this->gesamtEigenanteilBrutto  *=   -1.000 ;
        $this->gesamtZulageNetto    *=   -1.000 ;
        $this->gesamtZulageMwst1    *=   -1.000 ;
        $this->gesamtZulageMwst2    *=   -1.000 ;
        $this->gesamtZulageBrutto   *=   -1.000 ;
        $this->betragGegeben *=   -1.000 ;
        $this->betragRueckgabe *=   -1.000 ;
        $this->betragAnzahlung *=   -1.000 ;
        $this->betragRest *=   -1.000 ;
    }

    /**
     * @param string $_le
     * @return  void
     */
    public  function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }
}
