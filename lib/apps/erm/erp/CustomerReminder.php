<?php

/**
 * CustomerReminder.php Definition der Basis Klasses f�r Kunden Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CustomerReminder - Basis Klasse f�r Kunden Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage CustomerReminder
 */
class	CustomerReminder	extends	AppObject	{

	public	$myCuInvc ;

	const	NEU			=	  0 ;
	const	UPDATE		=	 30 ;
	const	ONGOING		=	 50 ;
	const	REMINDED	=	 70 ;
	const	WAITINV		=	 80 ;
	const	CLOSED		=	 90 ;
	const	ONHOLD		=	980 ;
	const	CANCELLED	=	990 ;
	private	static	$rStatus	=	array (
						-1				=> "ALL",
						CustomerReminder::NEU		=> "Neu",
						CustomerReminder::ONGOING	=> "Gedruckt (Verschickt)",
						CustomerReminder::CLOSED	=> "Abgeschlossen (vollst. geliefert)"
	) ;

	const	INVRMDL0	= 0 ;
	const	INVRMDL1	= 1 ;
	const	INVRMDL2	= 2 ;
	const	INVRMDL3	= 3 ;
	private	static	$rStufe	=	array (
						CustomerReminder::INVRMDL0 => "Erinnerung",
						CustomerReminder::INVRMDL1 => "Stufe 1 (Rechtsbelehrung)",
						CustomerReminder::INVRMDL2 => "Stufe 2 (Zinsen)",
						CustomerReminder::INVRMDL3 => "Stufe 3 (Rechtsanwalt)",
						CustomerReminder::CLOSED => "Abgeschlossen",
						CustomerReminder::CANCELLED => "Storniert"
	) ;
		
	/**
	 *
	 */
	function	__construct( $_myCustomerReminderNr='') {
		FDbg::dumpL( 0x01000000, "CustomerReminder::__construct( '%s')", $_myCustomerReminderNr) ;
		parent::__construct( "CustomerReminder", "CustomerReminderNr") ;
		$this->myKunde	=	NULL ;
		$this->myKundeKontakt	=	NULL ;
		if ( strlen( $_myCustomerReminderNr) > 0) {
			$this->setCustomerReminderNr( $_myCustomerReminderNr) ;
		} else {
			FDbg::dumpL( 0x01000000, "CustomerReminder::__construct(...): CustomerReminderNr not specified") ;
		}
		FDbg::dumpL( 0x01000000, "CustomerReminder::__construct(...) done") ;
	}

	/**
	 *
	 */
	function	setCustomerReminderNr ( $_myCustomerReminderNr) {
		FDbg::dumpL( 0x01000000, "CustomerReminder::setCustomerReminderNr( '%s')", $_myCustomerReminderNr) ;
		$this->CustomerReminderNr	=	$_myCustomerReminderNr ;
		if ( strlen( $_myCustomerReminderNr) > 0) {
			$this->reload() ;
		}
		FDbg::dumpL( 0x01000000, "CustomerReminder::setCustomerReminderNr(...) done") ;
	}

	/**
	 *
	 */
	function	reload() {
		FDbg::dumpL( 0x01000000, "CustomerReminder::reload()") ;
		$this->fetchFromDb() ;
		if ( $this->_valid == 1) {
			FDbg::dumpL( 0x01000000, "CustomerReminder::reload(): CustomerReminder is valid !") ;
			/**
			 *
			 */
			try {
				$this->myKunde	=	new Kunde( $this->KundeNr) ;
				$this->myKundeKontakt	=	new KundeKontakt( $this->KundeNr, $this->KundeKontaktNr) ;
				$this->myCuInvc	=	new CuInvc( $this->CuInvcNo) ;
			} catch ( Exception $e) {
				FDbg::dumpL( 0x01000000, "CustomerReminder::reload(): exception='%s'", $e->getMessage()) ;
			}
		}
		FDbg::dumpL( 0x01000000, "CustomerReminder::reload() done") ;
	}

	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PCuOrdr)
	 *
	 * @return void
	 */
	function	newFromCuInvc( $_key="", $_id="", $_cuInvcNo="") {
		FDbg::dumpL( 0x01000000, "CustomerReminder::newFromCuInvc(...)") ;
		$query	=	sprintf( "CustomerReminder_newFromCuInvc( @status, '%s', @newCustomerReminderNr) ; ", $_cuInvcNo) ;
		try {
			$row	=	FDb::callProc( $query, "@newCustomerReminderNr") ;
			$this->setCustomerReminderNr( $row['@newCustomerReminderNr']) ;
			$this->_addRem( "erstellt aus Rechnung Nr. " . $_cuInvcNo) ;
			$this->setTexte() ;
		} catch( Exception $e) {
			FDbg::dumpF( "CustomerReminder::newFromSuOrdr(...): exception='%s'", $e->getMessage()) ;
		}
		return $this->getXMLComplete() ;
	}

	/**
	 * Setzt den Prefix sowie den Postfix der Kundenbestellung auf die Default Werte.
	 * Die Default Werte werden bestimmt durch die f�r den Kunden abgespeicherten Wert
	 * f�r Sprache (z.B. de_de, en_us).
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: CustomerReminder_setTexte( @status, <CustomerReminderNr>).
	 *
	 * @return void
	 */
	function	setTexte( $_key="", $_id="", $_val=0) {
		error_log( "CustomerReminder::setTexte(...)") ;
		try {
			$myTexte	=	new SysTexte( "CustomerReminderPrefix") ;
			$this->Prefix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Prefix") ;
			$myTexte	=	new SysTexte( "CustomerReminderPostfix") ;
			$this->Postfix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Postfix") ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}

	function	setAnschreiben( $_key="", $_id="", $_val=0) {
		try {
			$myTexte	=	new SysTexte( "CustomerReminderEMail") ;
			$this->Anschreiben	=	$myTexte->Volltext ;
			$this->updateColInDb( "Anschreiben") ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getRStatus() {
		return  self::$rStatus ;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getRStufe() {
		return  self::$rStufe ;
	}

	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getKundeAsXML() ;
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
		$filename	=	$this->path->Archive . "CustomerReminder/" . $this->CustomerReminderNr . ".pdf" ;
		$filenameC	=	$this->path->Archive . "CustomerReminder/" . $this->CustomerReminderNr . "c.pdf" ;
		if ( file_exists( $filenameC)) { 
			$ret	.=	 $this->url->Archive . "CustomerReminder/" . $this->CustomerReminderNr . "c.pdf" ;
		} else if ( file_exists( $filename)) { 
			$ret	.=	 $this->url->Archive . "CustomerReminder/" . $this->CustomerReminderNr . ".pdf" ;
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
}
?>
