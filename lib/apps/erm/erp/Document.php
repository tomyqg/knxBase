<?php

/**
 * Document.php - Basis Anwendungsklasse fuer Kundenbestellung (Document)
 *
 *	Definiert die Klassen:
 *		Document
 *		DocumentPosten
 *
 * Einige Parameter haben eine extrem wichtige Bedeutung fuer das Gesamtsystem, d.h. von der
 * Bestellung ueber die Kommissionierung und den Lieferschein bis hin zur Rechnung.
 *
 * Attribut:	PosType
 *
 * Dieses Attribut beschreibt wie sich eine Position in der Bestellung in den verschiedenen Phasen
 * verhaelt.
 * Bei der Erzeugung von Kommisison, Lieferung und Rechnung werden grundsaetzlich alle Positionen
 * uebernommen deren Menge in dem entsprechenden Papier > 0 ist (Kommission: Menge noch zu liefern; Lieferschein: jetzt
 * geliefert; Rechnung: berechnete Menge). Die EN
 * Eine "NORMALe" Position wird im Lager reserviert (falls der Artikel an sich reserviert werden muss), wird
 * kommissioniert, geliefert und ebenfalls berechnet.
 * Eine "LieFeRuNG" Position wird im Lager reserviert (s.o.). Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp gelistet. Auf der Rechnung wird dieser Positionstyp NICHT gelistet.
 * Eine "ReCHNunG" Position wird im Lager NICHT reservert. Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp nicht gelistet. Auf der Rechnung wird dieser Typ gelistet.
 * Eine "KOMPonenten" Position wird im Lager reserviert, auf dem 
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * Document - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BDocument which should
 * not be modified.
 *
 * @package Application
 * @subpackage Document
 */
class	Document	extends	FDbObject	{

	const	PREL	=	"PREL" ;
	const	TAPP	=	"TAPP" ;
	const	APPR	=	"APPR" ;
	const	FREE	=	"FREE" ;
	const	REPL	=	"REPL" ;
	private	static	$rStatus	=	array (
						Document::PREL	=> "Preliminary",
						Document::TAPP	=> "tech. Approved",
						Document::APPR	=> "Approved",
						Document::FREE	=> "Free",
						Document::REPL	=> "Replaced"
					) ;

	const	DT_IT		=	"1" ;			// Interne Templates
	const	DT_ITMPLMS	=	"1010" ;		// Interne Templates Microsoft
	const	DT_ITMPLXLS	=	"10101" ;		// Interne Templates Microsoft Excel
	const	DT_ITMPLDOC	=	"10102" ;		// Interne Templates Microsoft Word
	const	DT_ITMPLPPT	=	"10103" ;		// Interne Templates Microsoft PowerPoint
	const	DT_ITMPLOO	=	"1020" ;		// Interne Templates Microsoft
	const	DT_ITMPLOOXLS	=	"10201" ;		// Interne Templates Microsoft Excel
	const	DT_ITMPLOODOC	=	"10202" ;		// Interne Templates Microsoft Word
	const	DT_ITMPLOOPPT	=	"10203" ;		// Interne Templates Microsoft PowerPoint
	const	DT_ID		=	"2" ;			// Interne Dokumente
	const	DT_IDLOGO	=	"20001" ;		// Interne Dokumente, Logos
	const	DT_IDLBL	=	"20101" ;		// Interne Dokumente, Aufkleber
	const	DT_IDBSC	=	"20201" ;		// Interne Dokumente, Visitenkarten
	const	DT_IDSTS	=	"20301" ;		// Interne Dokumente, StockScan
	const	DT_ISW		=	"3" ;			// Interne Software
	const	DT_ISWTS	=	"30001" ;		// Interne Software, Testspezifikation
	const	DT_ISWTI	=	"30002" ;		// Interne Software, Testinstruktion
	const	DT_ISWTSI	=	"30011" ;		// Interne Software, Testspezifikation / -instruktion
	const	DT_EG		=	"5" ;			// Experimentieranleitung
	const	DT_EGCH		=	"50" ;			// Experimentieranleitung Chemie
	const	DT_EGCHLSV	=	"50010" ;		// Experimentieranleitung kombiniert Lehrer/Schueler
	const	DT_EGCHSV	=	"50011" ;		// Experimentieranleitung Schueler
	const	DT_EGCHLV	=	"50012" ;		// Experimentieranleitung Lehrer
	const	DT_EGPH		=	"51" ;			// Experimentieranleitung Physik
	const	DT_EGBI		=	"52" ;			// Experimentieranleitung Biologie
	const	DT_EGDS		=	"53" ;			// Experimentieranleitung DataStudio Arbeitsmappe
	const	DT_EGDSAM	=	"53010" ;		// Experimentieranleitung DataStudio Arbeitsmappe
	const	DT_UG		=	"6" ;			// Benutzeranleitung
	const	DT_UGGK		=	"60010" ;		// Benutzeranleitung, Geraetekarte
	const	DT_UGPI		=	"60020" ;		// Benutzeranleitung, Beipackzettel
	const	DT_TN		=	"601" ;			// Tech Note
	const	DT_AN		=	"602" ;			// Application Note
	const	DT_PI		=	"7" ;			// Produkt Information
	const	DT_PICO		=	"70" ;			// Produkt Information, Commercials (Werbung)
	const	DT_PICOFL	=	"700" ;			// Produkt Information, Commercials (Werbung), Flyer
	const	DT_PICOFLS	=	"70001" ;		// Produkt Information, Commercials (Werbung), Flyer, Sonderangebot
	const	DT_PICOFLN	=	"70002" ;		// Produkt Information, Commercials (Werbung), Flyer, Normal
	const	DT_PRCL		=	"71" ;			// Produkt Information, Commercials (Werbung), Pricelist
	const	DT_PRCLRGNTS=	"71001" ;		// Product Information, Commercisl, Pricelist Reagents
	const	DT_PRCLLAB	=	"71101" ;		// Product Information, Commercisl, Pricelist Laboratory general
	const	DT_FORM		=	"79999" ;		// Form for customer use 
	const	DT_PIPIC	=	"79" ;			// Produkt Information, Bilder
	const	DT_PIPIC001	=	"79001" ;		// Produkt Information, Bilder, 001
	public	static	$rDocType	=	array (
//						Document::DT_IT			=> "Interne Templates",
						Document::DT_ITMPLMS	=> "Interne Templates, Microsoft",
						Document::DT_ITMPLXLS	=> "Interne Templates, Microsoft Excel",
						Document::DT_ITMPLDOC	=> "Interne Templates, Microsoft Word",
						Document::DT_ITMPLPPT	=> "Interne Templates, Microsoft PowerPoint",
						Document::DT_ITMPLOO	=> "Interne Templates, OpenOffice",
						Document::DT_ITMPLOOXLS	=> "Interne Templates, OpenOffice Calc",
						Document::DT_ITMPLOODOC	=> "Interne Templates, OpenOffice Writer",
						Document::DT_ITMPLOOPPT	=> "Interne Templates, OpenOffice PowerPoint",
//						Document::DT_ID			=> "Interne Dokumente",
						Document::DT_IDLOGO		=> "Interne Dokumente, Logo Designs",
						Document::DT_IDLBL		=> "Interne Dokumente, Aufkleber",
						Document::DT_IDBSC		=> "Interne Dokumente, Visitenkarten",
						Document::DT_IDSTS		=> "Interne Dokumente, StockScan",
						//						Document::DT_EG			=> "Versuchsanleitungen",
						Document::DT_EGCH		=> "Versuchsanleitungen Chemie",
						Document::DT_EGCHLSV	=> "Versuchsanleitung Chemie Lehrer/SchŸler",
						Document::DT_EGCHSV		=> "Versuchsanleitung Chemie SchŸler",
						Document::DT_EGCHLV		=> "Versuchsanleitung Chemie Lehrer",
						Document::DT_EGPH		=> "Versuchsanleitungen Physik",
						Document::DT_EGBI		=> "Versuchsanleitungen Biologie",
						Document::DT_UG			=> "Benutzeranleitung",
						Document::DT_UGGK		=> "Benutzeranleitung Geraetelkarte",
						Document::DT_UGPI		=> "Benutzeranleitung Beipackzettel",
						Document::DT_TN			=> "Technote",
						Document::DT_AN			=> "Application Note",
						Document::DT_UG			=> "Benutzeranleitung",
						Document::DT_EG			=> "Versuchsanleitung",
						Document::DT_EGDSAM		=> "DataStudio Arbeitsmappe",
						Document::DT_PRCL		=> "Pricelist",
						Document::DT_PRCLRGNTS	=> "Pricelist Reagents",
						Document::DT_PRCLLAB	=> "Pricelist Laboratory",
						Document::DT_FORM		=> "User form",
						Document::DT_PIPIC001	=> "Bild 001"
					) ;

	const	RT_ARTICLE	=	"Article" ;		// article, ref.no. := article no.
	const	RT_SUPP		=	"Supp" ;		// supplier, ref.no. := supplier no.
	const	RT_CUST		=	"Cust" ;		// customer, ref.no. := customer no.
	const	RT_TEMPL	=	"Tmpl" ;		// template, ref.no. := short description
	const	RT_PRCL		=	"Prcl" ;		// pricelist, ref.no. := short description
	const	RT_FORM		=	"Form" ;		// form, ref. no := short description
	const	RT_STOCK	=	"Stock" ;		// form, ref. no := short description
	const	RT_MISC		=	"Misc" ;		// form, ref. no := short description
	private	static	$rRefType	=	array (
						Document::RT_ARTICLE		=> "Artikel",
						Document::RT_SUPP			=> "Lieferant",
						Document::RT_CUST			=> "Kunde",
						Document::RT_PRCL			=> "Pricelist",
						Document::RT_FORM			=> "Form",
						Document::RT_TEMPL			=> "Template",
						Document::RT_STOCK			=> "Stock",
						Document::RT_MISC			=> "Miscellaneous",
						) ;
	const	FT_PDF			=	"pdf" ;		// Benutzeranleitung, Geraetekarte
	const	FT_XLS			=	"xls" ;		// Benutzeranleitung, Geraetekarte
	const	FT_ODT			=	"odt" ;		// Benutzeranleitung, Geraetekarte
	const	FT_TXT			=	"txt" ;
	private	static	$rFiletype	=	array (
						Document::FT_PDF		=> "PDF",
						Document::FT_XLS		=> "Excel",
						Document::FT_ODT		=> "OpenOffice",
						Document::FT_TXT		=> "Text"
					) ;
						/**
	 * Constructor
	 *
	 * The constructor can be passed a OrderNr (DocumentNr), in which case it will automatically
	 * (try to) load the respective Customer Order via the base class from the Database
	 * If the order data was loaded the customer data, and customer contact data, will also be loaded
	 * from the database.
	 *
	 * @param string $_myDocumentNr
	 * @return void
	 */
	function	__construct( $_myDocumentNr='') {
		parent::__construct( "Document", "Id") ;
		if ( strlen( $_myDocumentNr) > 0) {
			$this->setDocumentNr( $_myDocumentNr) ;
		} else {
		}
	}

	/**
	 * set the Order Number (DocumentNr)
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
	function	setDocumentNr( $_myDocumentNr) {
		$this->DocumentNr	=	$_myDocumentNr ;
		if ( strlen( $_myDocumentNr) > 0) {
			$this->reload() ;
		}
	}

	/**
	 * Gets a new item-nr for the next item to add to the temporary order
	 *
	 * @return void
	 */
	function	newVersion() {

		$query	=	sprintf( "SELECT Version FROM Document WHERE DocType='%s' AND ProdCode='%s' ORDER BY Version DESC LIMIT 0, 1 ", $this->DocType, $this->ProdCode) ;
		$sqlResult	=	FDb::query( $query) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->Version	=	$row[0] + 1 ;
		}
		return $this->_status ;
	}

	function	add( $_key="", $_id="", $_val="") {
		FDbg::dumpL( 0x01000000, "AppObject::upd(...)") ;
		$this->getFromPostL() ;
		$this->storeInDb() ;
		return $this->getAsXML() ;
	}

	function	upd( $_key="", $_id="", $_val="") {
		FDbg::dumpL( 0x01000000, "AppObject::upd(...)") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		return $this->getAsXML() ;
	}

	/**
	 *
	 */
	function	getRStatus() {		return self::$rStatus ;			}
	function	getRDocType() {		return self::$rDocType ;		}
	function	getRRefType() {		return self::$rRefType ;		}
	function	getRFileType() {	return self::$rFiletype ;		}
	function	getDocType() {		return self::$rDocType[$this->DocType] ;		}
	function	getFiletype() {		return self::$rFiletype[$this->Filetype] ;		}
	/**
	 * 
	 */
	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getAsXML() ;
		return $ret ;
	}
	/**
	 * 
	 */
	function	getAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	uploadOld( $_key="", $_id="", $_val="") {
		global	$documentsPath ;
		global	$authUser ;
		$ret	=	"" ;
		error_log( "Uploading document") ;
		/**
		 * determine the new version nr.
		 * @var unknown_type
		 */
		if ( $this->DocRev[0] == "P") {
			$revLetter	=	$this->DocRev[1] ;
			$revNr	=	intval( substr( $this->DocRev,2)) ;
			error_log( "Preliminary " . $revLetter . " " . $revNr) ;
			$this->DocRev	=	sprintf( "P%s%02d", $revLetter, $revNr+1) ;
			$this->DocDate	=	$this->today() ;
			$this->updateInDb() ;
		}
		$filename	=	$_FILES['_IFilename']['name'] ;
		$fullPathname	=	$documentsPath ;
		$fullFilename	=	$fullPathname . $this->DocType . "_" . $this->RefNr . "_" . $this->DocRev . "_" . $authUser . "." . $this->Filetype ;
		if (move_uploaded_file($_FILES['_IFilename']['tmp_name'], $fullFilename)) {
//			$myDocument	=	new Document() ;
//			if ( $myDocument->setId( $_POST['_HId'])) {
//				$myDocument->Filename	=	$filename ;
//				$myDocument->updateInDb() ;
//			} else {
//				echo "File-Id not valid <br/>" ;
//			}
		} else {
			error_log( "Possible file upload attack!") ;
		}
		$ret	.=	$this->getAsXML() ;
		return $ret ;
	}
	function	upload( $_key="", $_id="", $_val="") {
		$filename	=	$_FILES['_IFilename']['name'] ;
		$filetype	=	$_FILES['_IFilename']['name'] ;
		echo "Upload: Dokument<br/>" ;
		echo "Filename: [" . $filename . "]<br/>" ;
		/**
		 * @var unknown_type
		 */
		$this->Filename	=	$this->RefType . "_"
						.	$this->RefNr . "_"
						.	$this->DocType . "_"
						.	"_"							// should be the doc type as abbreviation
						.	"_"							// should be the doc name
						.	$this->ValidFrom . "_"
						.	$this->ValidTo . "_"
						.	$this->DocRev . "_"
						.	$this->Status
						.	"." . $this->Filetype ;
		$this->updateColInDb( "Filename") ;
		/**
		 * 
		 * @var unknown_type
		 */
		$fullFilename	=	$this->path->Documents . $this->Filename ;
		echo "Full Filename: [" . $fullFilename . "]<br/>" ;
		/**
		 * 
		 */
		if (move_uploaded_file($_FILES['_IFilename']['tmp_name'], $fullFilename)) {
			echo "File is valid, and was successfully uploaded.<br/>";
		} else {
			echo "Possible file upload attack!<br/>" ;
		}
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getTableDocumentsAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		$ret	.=	"<DocList>\n" ;
		$ret	.=	"<URLPath>".$this->url->Documents."</URLPath>\n" ;
		$myDocument	=	new Document() ;
		$ret	.=	$myDocument->tableFromDb( "", "", "C.RefType like '%' AND C.RefNr like '%' ", "", "Doc") ;
		$ret	.=	"</DocList>\n" ;
		return $ret ;
	}
	function	getF50( $_key, $_id, $_val) {
		$fullPath	=	$this->path->Documents ;	//. $this->LiefNr . "/" ;
		$myDir	=	opendir( $fullPath) ;
		if ( $myDir) {
			$myFiles	=	array() ;
			while (($file = readdir( $myDir)) !== false) {
//				if ( strncmp( $file, $this->LiefNr, 6) == 0) {
					$myFiles[]	=	$file ;
//				}
			}
		}
		closedir( $myDir);
		reset( $myFiles) ;
		asort( $myFiles) ;
		$ret	=	"<DocList>\n" ;
		$ret	.=	"<URLPath>".$this->url->Documents."</URLPath>\n" ;
		foreach ( $myFiles as $file) {
			$ret	.=	"<Doc>\n" ;
			if ( filetype( $this->path->Documents . $file) == "file") {
				$ret	.=	"<RefType><![CDATA[FILESYSTEM]]></RefType>\n" ;
				$ret	.=	"<RefNr><![CDATA[FILESYSTEM]]></RefNr>\n" ;
				$ret	.=	"<Filename><![CDATA[$file]]></Filename>\n" ;
				$ret	.=	"<Filetype><![CDATA[" . myFiletype( $file) . "]]></Filetype>\n" ;
				$ret	.=	"<Filesize><![CDATA[" . filesize( $fullPath . $file) . "]]></Filesize>\n" ;
				$ret	.=	"<FileURL><![CDATA[" . $this->url->Documents . $file . "]]></FileURL>\n" ;
			}
			$ret	.=	"</Doc>\n" ;
		}
		$ret	.=	"</DocList>" ;
		return $ret ;
	}

}

?>
