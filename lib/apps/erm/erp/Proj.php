<?php

/**
 * Proj.php Anwendungsklasses f�r Angebote
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * Proj - Anwendungsklasse f�r: Angebot
 *
 * This class acts as an interface towards the automatically generated BProj which should
 * not be modified.
 *
 * @package Application
 * @subpackage Proj
 */
class	Proj	extends	AppObjectCR	{

	private	$tmpProjPos ;

	const	NEU			=	  0 ;
	const	UPDATE		=	 30 ;
	const	ONGOING		=	 50 ;
	const	REMINDED	=	 70 ;
	const	WAITINV		=	 80 ;
	const	CLOSED		=	 90 ;
	const	ONHOLD		=	980 ;
	const	CANCELLED	=	0x01000000 ;
	private	static	$rStatus	=	array (
						Proj::NEU => "Neu",
						Proj::UPDATE => "In Bearbeitung (auch: Revision)",
						Proj::ONGOING => "Verschickt",
						Proj::CLOSED => "Abgelaufen/Beendet",
						Proj::CANCELLED => "Storniert"
					) ;

	const	PROJLEV0	=	 10 ;
	const	PROJLEV1	=	 20 ;
	const	PROJLEV2	=	 30 ;
	private	static	$rProjLevel	=	array (
						Proj::PROJLEV0 => "Ausschreibung",
						Proj::PROJLEV1 => "Los einer Ausschreibung",			//	+
						Proj::PROJLEV2 => "Teil-Los einer Ausschreibung") ;		//	|
	function	getRProjLevel() {
		return  self::$rProjLevel ;
	}

	const	PROJPCM0	=	 10 ;		// PROJPCM = PROJect Posten Calculation Mode
	const	PROJPCM1	=	 20 ;
	const	PROJPCM2	=	 30 ;
	private	static	$rProjPCM	=	array (
						Proj::PROJPCM0 => "Kalkulation wie im Angebot",
						Proj::PROJPCM1 => "Nach alter Formel, V1",			//	+
						Proj::PROJPCM2 => "Nach neuer Formel, V2"
					) ;		//	|
	function	getRProjPCM() {
		return  self::$rProjPCM ;
	}

	const	PROJCM0	=	 10 ;		// PROJCM = PROJect Calculation Mode
	const	PROJCM1	=	 20 ;
	const	PROJCM2	=	 30 ;
	private	static	$rProjCM	=	array (
						Proj::PROJCM0 => "Kalkulation pro Angebot",
						Proj::PROJCM1 => "Summenkalkulation, V2"			//	+
					) ;		//	|
	function	getRProjCM() {
		return  self::$rProjCM ;
	}

	/**
	 * Konstructor f�r Klasse: Proj (Angebot)
	 *
	 * Der Konstruktor kann mit mit oder ohne eine Angebotsnummer aufgerufen werden.
	 * Wenn der Konstruktor mit einer Angebotsnummer aufgerufen wird, wird versucht
	 *
	 * @param string $_myProjNr
	 * @return void
	 */
	function	__construct( $_myProjNr='') {
		FDbg::get()->dumpL( 0x01000000, "Proj::__construct( '%s ')", $_myProjNr) ;
		AppObjectCR::__construct( "Proj", "ProjNr") ;
		if ( strlen( $_myProjNr) > 0) {
			$this->setProjNr( $_myProjNr) ;
		} else {
			FDbg::get()->dumpL( 0x01000000, "Proj::__construct(...): ProjNr not specified !") ;
		}
		FDbg::get()->dumpL( 0x01000000, "Proj::__construct(...) done") ;
	}

	/**
	 * set the Order Number (ProjNr)
	 *
	 * Sets the order number for this object and tries to load the order from the database.
	 * If the order could successfully be loaded from the database the respective customer data
	 * as well as customer contact data is retrieved as well.
	 * If the order has a separate Invoicing address, identified through a populated field, this
	 * data is read as well.
	 * If the order has a separate Delivery address, identified through a populated field, this
	 * data is read as well.
	 *
	 * @return void
	 */
	function	setProjNr( $_myProjNr) {
		FDbg::get()->dumpL( 0x01000000, "Proj::setProjNr('%s')", $_myProjNr) ;
		$this->ProjNr	=	$_myProjNr ;
		if ( strlen( $_myProjNr) > 0) {
			$this->reload() ;
		}
		FDbg::get()->dumpL( 0x01000000, "Proj::setProjNr('%s') is done", $_myProjNr) ;
	}

	/**
	 * Create a new temporary order with the next available temp-order-nr and store
	 * the order in the database.
	 *
	 * @return void
	 */
	function	newProj() {
		FDbg::dumpL( 0x01000000, "Proj.php::newProj(...)") ;
		try {
			/**
			 * create the (provisionary) PCuOrdr and CuComm for each distinct supplier
			 */
			$newProj	=	new Proj() ;
			$newProj->newProj() ;					// get a new offer no.
			$newProjNr	=	$newProj->ProjNr ;	// remember offer no.
			$newProj->copyFrom( $this) ;			// copy this offer data to the new one
			$newProj->ProjNr	=	$newProjNr ;	// restore offer no.
			$newProj->Datum	=	$this->today() ;		// update the date to today
			$newProj->Status	=	Proj::NEU ;
			if ( isset( $_SESSION['UserId'])) {
				$myText	=	date( "Ymd/Hi") . ": " . $_SESSION['UserId'] . ": Kopie von $this->ProjNr\n" ;
			} else {
				$myText	=	date( "Ymd/Hi") . ": " . "Hintergrund Prozess" . ": Kopie von $this->ProjNr\n" ;
			}
			$myText	.=	$newProj->Rem1 ;
			$newProj->Rem1	=	$myText ;
			$newProj->updateInDb() ;				// update data in db
			$this->setProjNr( $newProjNr) ;
		} catch( Exception $e) {
			FDbg::dumpF( "Proj.php::Proj::newProj(...): exception='%s'", $e->getMessage()) ;
		}
		FDbg::dump( "Proj.php::newProj(...): end") ;
	}

	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (TProj)
	 *
	 * @return void
	 */
	function	newFromProj( $_myProjNr) {
		FDbg::get()->dumpL( 0x01000000, "Proj::newFromProj(...)") ;
		$query	=	sprintf( "Proj_newFromProj( @status, '%s', @newProjNr) ; ", $_myProjNr) ;
		try {
			$row	=	FDb::callProc( $query, '@newProjNr') ;
			$this->setProjNr( $row['@newProjNr']) ;
			$this->addRem( "Erzeugt aus Angebot Nr. " . $_myCuRFQNo . " ") ;
			$myProj	=	new Proj( $_myProjNr) ;
			$myProj->addRem( "Angebot " . $this->ProjNr . " erstellt") ;
		} catch( Exception $e) {
			FDbg::get()->dumpF( "Proj::newFromProj(...): exception='%s'", $e->getMessage()) ;
		}
	}

	/**
	 * F�hrt eine Neuberechnung aller abh�ngigen Werte der Kundenbestellung durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: Proj_restate( @status, <ProjNr>).
	 *
	 * @return void
	 */
	function	restate() {
		FDbg::get()->dumpL( 0x01000000, "Proj::restate()") ;
		$query	=	sprintf( "Proj_restate( @status, '%s') ; ", $this->ProjNr) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
		} catch( Exception $e) {
			FDbg::get()->dumpF( "Proj::restate(...): exception='%s'", $e->getMessage()) ;
		}
	}

	/**
	 * F�hrt eine Neuberechnung aller abh�ngigen Werte der Kundenbestellung durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: Proj_restate( @status, <ProjNr>).
	 *
	 * @return void
	 */
	function	resetPreise() {
		FDbg::get()->dumpL( 0x01000000, "Proj::resetPreise()") ;
		try {
			$myVKPreis	=	new VKPreis() ;
			$myProjPos	=	new ProjPos() ;
			$myVKPreis	=	new VKPreis() ;
			for ( $actProjPos = $this->getFirstPos() ;
					$actProjPos->_valid == 1 ;
					$actProjPos = $this->getNextPos()) {
				if ( strlen( $actProjPos->SubPosNr) == 0) {
					$myVKPreis->CuOffrNo	=	$actProjPos->CuOffrNo ;
					$myVKPreis->firstFromView( "VKPreisCache") ;
					$actProjPos->RefPreis	=	$myVKPreis->Preis ;
					$actProjPos->updateInDb() ;
				}
			}
		} catch( Exception $e) {
			FDbg::get()->dumpF( "Proj::resetPreise(...): exception='%s'", $e->getMessage()) ;
		}
	}

	/**
	 * F�hrt eine Neuberechnung aller abh�ngigen Werte der Kundenbestellung durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: Proj_restate( @status, <ProjNr>).
	 *
	 * @return void
	 */
	function	recalc() {
		FDbg::get()->dumpL( 0x01000000, "Proj::recalc()") ;
		$query	=	sprintf( "Proj_recalc( @status, '%s') ; ", $this->ProjNr) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
		} catch( Exception $e) {
			FDbg::get()->dumpF( "Proj::recalc(...): exception='%s'", $e->getMessage()) ;
		}
	}

	/**
	 * Setzt den Prefix sowie den Postfix der Kundenbestellung auf die Default Werte.
	 * Die Default Werte werden bestimmt durch die f�r den Kunden abgespeicherten Wert
	 * f�r Sprache (z.B. de_de, en_us).
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: CuOrdr_setTexte( @status, <CuOrdrNo>).
	 *
	 * @return void
	 */
	function	setTexte( $_key, $_id, $_val) {
		try {
			$myTexte	=	new SysTexte( "ProjPrefix") ;
			$this->Prefix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Prefix") ;
			$myTexte	=	new SysTexte( "ProjPostfix") ;
			$this->Postfix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Postfix") ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}

	function	setAnschreiben( $_key, $_id, $_val) {
		try {
			$myTexte	=	new SysTexte( "ProjEMail") ;
			$this->Anschreiben	=	$myTexte->Volltext ;
			$this->updateColInDb( "Anschreiben") ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}

	/**
	 * Setzt den Rabatt-Modus (DiscountMode) f�r die Bestellung auf den Rabatt Modus 1 (alte Variante)
	 * und f�hrt eine Neuberechnung der Einzelpreise, der Rabatte sowie der Gesamtpreise durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: Proj_setDM10( @status, <ProjNr>).
	 *
	 * @return void
	 */
	function	setDM10() {
		FDbg::get()->dumpL( 0x01000000, "Proj::setDM10()") ;
		$query	=	sprintf( "Proj_setDM10( @status, '%s') ; ", $this->ProjNr) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
		} catch( Exception $e) {
			FDbg::get()->dumpF( "Proj::setDM10(...): exception='%s'", $e->getMessage()) ;
		}
	}

	/**
	 * Setzt den Rabatt-Modus (DiscountMode) f�r die Bestellung auf den Rabatt Modus 2 (neue Variante)
	 * und f�hrt eine Neuberechnung der Einzelpreise, der Rabatte sowie der Gesamtpreise durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: Proj_setDM10( @status, <ProjNr>).
	 *
	 * @return void
	 */
	function	setDM20() {
		FDbg::get()->dumpL( 0x01000000, "Proj::setDM20()") ;
		$query	=	sprintf( "Proj_setDM20( @status, '%s') ; ", $this->ProjNr) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
		} catch( Exception $e) {
			FDbg::get()->dumpF( "Proj::setDM20(...): exception='%s'", $e->getMessage()) ;
		}
	}

	/**
	 * F�gt einen CuOffr, beschrieben durch die CuOffrNo, in der angegebenen Menge zu der Bestellung
	 * hinzu.
	 *
	 * @return void
	 */
	function	addPos( $_key="", $_id=-1, $_val="") {
		FDbg::dump( "Proj.php::Proj::( $_key)") ;
		try {
			$myCuOffr	=	new CuOffr() ;
			$myCuOffr->setId( $_id) ;
			if ( $myCuOffr->_valid) {
				$newProjPos	=	new ProjPosten( $this->ProjNr) ;
				$newProjPos->getNextPosNr() ;
				$newProjPos->CuOffrNo	=	$myCuOffr->CuOffrNo ;
				$newProjPos->storeInDb() ;
			} else {
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		$ret	=	$this->getTablePostenAsXML() ;

		FDbg::dumpL( 0x01000000, "ProjPos::addPos() donbe") ;
		return $ret ;
	}

	/**
	 * updAddText
	 * @return ProjPos
	 */
	function	updAddText( $_id, $_text) {
		FDbg::get()->dumpL( 0x01000000, "Proj::updAddText(%d, '%s')", $_id, $_text) ;
		try {
			$this->tmpProjPos	=	new ProjPos() ;
			$this->tmpProjPos->Id	=	$_id ;
			$this->tmpProjPos->fetchFromDbById() ;
			if ( $this->tmpProjPos->_valid == 1) {
				FDbg::get()->dumpL( 0x01000000, "Proj::updAddText: refers to PosNr=%d", $this->tmpProjPos->PosNr) ;
				$this->tmpProjPos->AddText	=	$_text ;
				$this->tmpProjPos->updateInDb() ;
			} else {
				throw new Exception( 'Proj::updAddText: ProjPos[Id='.$_id.'] is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::get()->dumpL( 0x01000000, "Proj::updAddText(%s), done", $_id) ;
		return $this->tmpProjPos ;
	}

	/**
	 * Kommentar zu der Colli hinzufuegen
	 *
	 *	Dies Funktion fuegt einen Kommentar, $_rem, an die Colli an.
	 *	Datum/Uhrzeit sowie die Id des angemeldeten Benutzers, oder - im Falle eines unauthtntizierten Zugriffs - der Zusatz: "Hintergrunf Prozess"
	 *	werden automatisch in dem Kommentar vermerkt.
	 *
	 *	@param	char	$_rem
	 *	@return	void
	 */
	function	addRem( $_rem) {
		FDbg::get()->dumpL( 0x01000000, "Proj::addRem(...)") ;
		try {
			if ( isset( $_SESSION['UserId'])) {
				$myText	=	date( "Ymd/Hi") . ": " . $_SESSION['UserId'] . ": " . $_rem . "\n" ;
			} else {
				$myText	=	date( "Ymd/Hi") . ": " . "Hintgergrund Prozess" . ": " . $_rem . "\n" ;
			}
			$myText	.=	$this->Rem1 ;
			$this->Rem1	=	$myText ;
			$this->updateInDb() ;
		} catch( Exception $e) {
			throw $e ;
		}
	}

	/**
	 *
	 */
	function	getRStatus() {
		return self::$rStatus ;
	}

	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getKundeAsXML() ;
		$ret	.=	$this->getTablePostenAsXML() ;
		$ret	.=	"<Document>" ;
		$filename	=	$this->path->Archive . "Proj/" . $this->ProjNr . ".pdf" ;
		if ( file_exists( $filename)) {
			$ret	.=	 $this->url->Archive . "Proj/" . $this->ProjNr . ".pdf" ;
		}
		$ret	.=	"</Document>" ;
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
		$filename	=	$this->path->Archive . "Proj/" . $this->ProjNr . ".pdf" ;
		if ( file_exists( $filename)) {
			$ret	.=	 $this->url->Archive . "/Proj/" . $this->ProjNr . ".pdf" ;
		}
		$ret	.=	"]]></Document>" ;
		return $ret ;
	}

	function	getProjPostenAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myProjPosten	=	new ProjPosten() ;
		$myProjPosten->setId( $_id) ;
		$ret	.=	$myProjPosten->getXMLF() ;
		return $ret ;
	}

	function	getTablePostenAsXML( $_key="", $_id=-1, $_val="") {
		$tmpObj	=	new ProjPosten( $this->ProjNr) ;
		$tmpObj->addCol( "GesamtPreis", "var") ;
		$ret	=	$tmpObj->tableFromDb( ",KdA.GesamtPreis ",
								"LEFT JOIN CuOffr AS KdA ON KdA.CuOffrNo = C.CuOffrNo",
								"C.ProjNr = '" . $this->ProjNr . "' ",
								"ORDER BY C.PosNr, C.SubPosNr ") ;
//		$ret	=	$tmpObj->tableFromDb( " ",
//								" ",
//								"C.ProjNr = '" . $this->ProjNr . "' ",
//								"ORDER BY C.PosNr, C.SubPosNr ") ;
		return $ret ;
	}

	function	getDocListAsXML( $_key, $_id, $_val) {
		$fullPath	=	$this->path->Archive . "Proj/" ;
		$myDir	=	opendir( $fullPath) ;
		$myFiles	=	array() ;
		while (($file = readdir( $myDir)) !== false) {
			if ( strncmp( $file, $this->ProjNr, 6) == 0) {
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
				$ret	.=	"<FileURL>" . $this->url->Archive . "Proj/" . $file . "</FileURL>\n" ;
			}
			$ret	.=	"</Doc>\n" ;
		}
		$ret	.=	"</DocList>" ;
		return $ret ;
	}

}

/**
 * ProjPos - Anwendungsklasse fuer: Angebot
 *
 * This class acts as an interface towards the automatically generated BProj which should
 * not be modified.
 *
 * @package Base
 * @subpackage Proj
 */
class	ProjPosten	extends	FDbObject	{

	function	__construct( $_myProjNr='') {
		parent::__construct( "ProjPosten", "Id") ;
		$this->ProjNr	=	$_myProjNr ;
		FDbg::get()->dumpL( 0x01000000, "ProjPosten::__constructor done") ;
	}

	/**
	 *
	 * @return void
	 */
	function	getNextPosNr() {
		FDbg::get()->dumpL( 0x01000000, "ProjPosten::getNextPosNr()") ;
		$query	=	sprintf( "SELECT PosNr FROM ProjPosten WHERE ProjNr='%s' ORDER BY PosNr DESC LIMIT 0, 1 ", $this->ProjNr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$row    =       mysql_fetch_array( $sqlResult) ;
			$this->PosNr	=	$row[0] + 1 ;
		}
		FDbg::get()->dumpL( 0x01000000, "ProjPosten::getNextPosNr() done") ;
		return $this->_status ;
	}

}

?>
