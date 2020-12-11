<?php

/**
 * KdGuts.php Definition der Basis Klasses f�r Kunden Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/AppObjectCR.php") ;
require_once( "base/AppDepObject.php") ;
require_once( "XmlTools.php" );
/**
 * Kduts - Basis Klasse f�r Kunden Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage KdGuts
 */
class	KdGuts	extends	AppObjectCR	{

	private	$myCuInvc ;

	const	NEU			=	  0 ;
	const	ONGOING		=	 50 ;
	const	REMINDED	=	 70 ;
	const	CLOSED		=	 90 ;
	const	CANCELLED	=	990 ;
	private	static	$rStatus	=	array (
						-1				=> "ALL",
						KdGuts::NEU		=> "Neu",
						KdGuts::ONGOING	=> "Verschickt",
						KdGuts::REMINDED	=> "Angemahnt",
						KdGuts::CLOSED	=> "Abgeschlossen (Bezahlt)",
						KdGuts::CANCELLED	=> "Storniert"
					) ;
	function	getRStatus() {
		return  self::$rStatus ;
	}

	/**
	 *
	 */
	function	__construct( $_myKdGutsNr="") {
		AppObjectCR::__construct( "KdGuts", "KdGutsNr") ;
		$this->myCuInvc	=	new CuInvc() ;
		if ( strlen( $_myKdGutsNr) > 0) {
			$this->setKdGutsNr( $_myKdGutsNr) ;
		} else {
			FDbg::dump( "CuDlvr::__construct(...): CuDlvrNo not specified") ;
		}
		FDbg::dump( "KdGuts::__construct(...) done", $_myKdGutsNr) ;
	}

	/**
	 *
	 */
	function	setKdGutsNr( $_myKdGutsNr) {
		FDbg::dump( "KdGuts::setKdGutsNr( '%s')", $_myKdGutsNr) ;
		$this->KdGutsNr	=	$_myKdGutsNr ;
		if ( strlen( $_myKdGutsNr) > 0) {
			$this->reload() ;
		}
		FDbg::dump( "KdGuts::setKdGutsNr(...) done") ;
	}
	/**
	 *
	 */
	function	getCuInvc() {
		return $this->myCuInvc ;
	}
	/**
	 * Fuegt eine Rechnungsposition "Versandkosten" zu der Rechnung hinzu
	 * hinzu.
	 * 
	 * @return void
	 */
	private	function	_addSpezial( $_posType, $_artikelNr) {
		try {
			$newKdGutsPosten	=	new KdGutsPosten( $this->KdGutsNr) ;
			$newKdGutsPosten->getNextPosNr( $_posType) ;
			$newKdGutsPosten->PosType	=	$_posType ;
			$newKdGutsPosten->ArtikelNr	=	$_artikelNr ;
			$newKdGutsPosten->Menge	=	1 ;
			$newKdGutsPosten->BerechneteMenge	=	1 ;
			$newKdGutsPosten->Preis	=	0.0 ;
			$newKdGutsPosten->RefPreis	=	0.0 ;
			$newKdGutsPosten->MengeProVPE	=	1 ;
			$newKdGutsPosten->GesamtPreis	=	0.0 ;
			$newKdGutsPosten->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}

		return $newKdGutsPosten ;
	}
	/**
	 * updAddText
	 * @return KdGutsPos
	 */
	function	updAddText( $_id, $_text) {
		FDbg::get()->dump( "KdGuts::updAddText(%d, '%s')", $_id, $_text) ;
		try {
			$this->tmpKdGutsPos	=	new KdGutsPosten() ;
			$this->tmpKdGutsPos->Id	=	$_id ;
			$this->tmpKdGutsPos->fetchFromDbById() ;
			if ( $this->tmpKdGutsPos->_valid == 1) {
				FDbg::get()->dump( "KdGuts::updAddText: refers to PosNr=%d", $this->tmpKdGutsPos->PosNr) ;
				$this->tmpKdGutsPos->AddText	=	$_text ;
				$this->tmpKdGutsPos->updateInDb() ;
			} else {
				throw new Exception( 'KdGuts::updAddText: KdGutsPosten[Id='.$_id.'] is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::get()->dump( "KdGuts::updAddText(%s), done", $_id) ;
		return $this->tmpKdGutsPos ;
	}

	/**
	 *
	 * @return KdGutsPos
	 */
	function	getFirstPos() {
		$this->tmpKdGutsPos	=	new KdGutsPosten() ;
		$this->tmpKdGutsPos->KdGutsNr	=	$this->KdGutsNr ;
		$this->tmpKdGutsPos->firstFromDb() ;
		return $this->tmpKdGutsPos ;
	}

	/**
	 *
	 * @return KdGutsPos
	 */
	function	getNextPos() {
		$this->tmpKdGutsPos->nextFromDb() ;
		return $this->tmpKdGutsPos ;
	}
	/**
	 * Setzt den Prefix sowie den Postfix der Kundenbestellung auf die Default Werte.
	 * Die Default Werte werden bestimmt durch die f�r den Kunden abgespeicherten Wert
	 * f�r Sprache (z.B. de_de, en_us).
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: KdGuts_setTexte( @status, <KdGutsNr>).
	 *
	 * @return void
	 */
	function	setTexte( $_key, $_id, $_val) {
		try {
			$myTexte	=	new SysTexte( "KdGutsPrefix", $this->KundeNr, $this->myKunde->Sprache) ;
			$this->Prefix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Prefix") ;
			$myTexte	=	new SysTexte( "KdGutsPostfix", $this->KundeNr, $this->myKunde->Sprache) ;
			$this->Postfix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Postfix") ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	setAnschreiben( $_key, $_id, $_val) {
		try {
			$myTexte	=	new SysTexte( "KdGutsEMail", $this->KundeNr, $this->myKunde->Sprache) ;
			$this->Anschreiben	=	$myTexte->Volltext ;
			$this->updateColInDb( "Anschreiben") ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	sendByEMail( $_key, $_id, $_val) {
		try {
			$this->upd( '', '', '') ;
			$newMail	=	new mimeMail( $this->eMail->Sales,
								$_POST['_IeMail'],
								$this->eMail->Sales,
								FTr::tr( "Cretdit note No. #1, dated #2", array( "%s:".$this->KdGutsNr, "%s:".convDate( $this->Datum))),
								"Bcc: ".$this->eMail->Archive."\n") ;
			$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
			$myDisclaimerText	=	new SysTexte( "DisclaimerText") ;
								
			$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer", "#KdGutsDatum") ;
			$myReplTableOut	=	array( $this->myKundeKontakt->getAnrede(), $this->Datum, $this->eMail->Greeting, $myDisclaimerHTML->Volltext, $this->Datum) ;
			$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;

			$myText	=	new mimeData( "multipart/alternative") ;
			$myText->addData( "text/plain", xmlToPlain( "<div>".$myMail."</div>")) ;
			$myText->addData( "text/html", "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><head></head><body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n".$myMail."</body></html>", "", true) ;
	
			$myBody	=	new mimeData( "multipart/mixed") ;
			$myBody->addData( "multipart/mixed", $myText->getAll()) ;
			$myBody->addData( "application/pdf", $this->path->Archive."KdGuts/".$this->KdGutsNr.".pdf", $this->KdGutsNr.".pdf", true) ;

			$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
			$mailSendResult	=	$newMail->send() ;

			$this->DocVerschVia	=	Opt::DOCVIAEMAIL ;
			$this->_addRem( FTr::tr( "send by E-Mail to: #1 (Bcc: #2)", array( "%s:".$_POST['_IeMail'],"%s:".$this->eMail->Archive)), $this->sysUser->UserId) ;
		} catch ( Exeption $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}
	/**
	 * 
	 */
	function	getAnschAsHTML() {
		$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
		$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer") ;
		$myReplTableOut	=	array( $this->myKundeKontakt->getAnrede(), $this->Datum, $this->eMail->Greeting, $myDisclaimerHTML->Volltext) ;
		$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;
		return $myMail ;
	}					
	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getKundeAsXML() ;
		$ret	.=	$this->getTablePostenAsXML() ;
		return $ret ;
	}

	function	getXMLString() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		$ret	.=	$this->getXMLDocInfo() ;
		return $ret ;
	}

	function	getXMLDocInfo() {
		$ret	=	"<Document><![CDATA[" ;
		$filename	=	$this->path->Archive . "KdGuts/" . $this->KdGutsNr . ".pdf" ;
		if ( file_exists( $filename)) { 
			$ret	.=	 $this->url->Archive . "/KdGuts/" . $this->KdGutsNr . ".pdf" ;
		}
		$ret	.=	"]]></Document>" ;
		return $ret ;
	}

	function	getKundeAsXML() {
		$ret	=	"" ;

		$ret	.=	'<KundeAdr>' ;
		$ret	.=	$this->myKunde->getXMLF() ;
		$ret	.=	$this->myKundeKontakt->getXMLF() ;
		$ret	.=	'</KundeAdr>' ;
		return $ret ;
	}

	function	getDocListAsXML( $_key, $_id, $_val) {
		$fullPath	=	$this->path->Archive . "KdGuts/" ;
		$myDir	=	opendir( $fullPath) ;
		$myFiles	=	array() ;
		while (($file = readdir( $myDir)) !== false) {
			if ( strncmp( $file, $this->KdGutsNr, 6) == 0) {
				$myFiles[]	=	$file ;
			}
		}
		closedir( $myDir);
		reset( $myFiles) ;
		asort( $myFiles) ;
		$ret	=	"<DocList>\n" ;
		$ret	.=	"<URLPath>$this->url->Archive</URLPath>\n" ;
		foreach ( $myFiles as $file) {
			$ret	.=	"<Doc>\n" ;
			if ( filetype( $fullPath . $file) == "file") {
				$ret	.=	"<Filename>$file</Filename>\n" ;
				$ret	.=	"<Filetype>" . myFiletype( $file) . "</Filetype>\n" ;
				$ret	.=	"<Filesize>" . filesize( $fullPath . $file) . "</Filesize>\n" ;
				$ret	.=	"<FileURL>" . $this->url->Archive . "KdGuts/" . $file . "</FileURL>\n" ;
			}
			$ret	.=	"</Doc>\n" ;
		}
		$ret	.=	"</DocList>" ;
		return $ret ;
	}
}

/**
 * KdGutsPosten - Basis Klasse f�r Kunden Lieferung
 *
 * not be modified.
 *
 * @package Base
 * @subpackage KdGuts
 */
class	KdGutsPosten	extends	AppDepObject	{

	public	$myArtikel ;
	public	$myCond ;

	/**
	 *
	 */
	function	__construct( $_myKdGutsNr="") {
		AppDepObject::__construct( "KdGutsPosten", "Id") ;
		$this->KdGutsNr	=	$_myKdGutsNr ;
		$this->myArtikel	=	new Artikel() ;
	}

	/**
	 * Zugriff auf Artikel
	 *
	 * @return [Artikel]
	 */
	function	getArtikel() {
		return $this->myArtikel ;
	}

	/**
	 *
	 * @return void
	 */
	function	getNextPosNr( $_posType=0) {
		if ( $_posType <= CuOrdrItem::_LASTNORM) {
			$query	=	sprintf( "SELECT PosNr FROM KdGutsPosten WHERE KdGutsNr='%s' AND PosType <= %d ORDER BY PosNr DESC LIMIT 0, 1 ", $this->KdGutsNr, CuOrdrItem::_LASTNORM) ;
		} else {
			$query	=	sprintf( "SELECT PosNr FROM KdGutsPosten WHERE KdGutsNr='%s' AND PosType=%d ORDER BY PosNr DESC LIMIT 0, 1 ", $this->KdGutsNr, $_posType) ;
		}
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			if ( $row === FALSE) {
				$this->PosNr	=	sprintf( "%02d0010", $_posType) ;
			} else {
				$this->PosNr	=	$row[0] + 10 ;
			}
		}
		return $this->_status ;
	}
		
}

?>
