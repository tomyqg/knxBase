<?php

require_once( "global.inc.php") ;
require_once( "option.inc.php") ;
require_once( "iPrintable.inc.php") ;
require_once( "iListDoc.inc.php") ;
require_once( "Receiver.php" );
require_once( "Brief.php" );
require_once( "Mailing.php" );

class	BriefDoc	extends Brief	implements iListDoc, iPrintable {

	public	$myReceiver ;
	private	$myType ;
	private	$myMailing ;

	const	NORMAL	= 0 ;
	const	MAILING	= 1 ;

	private	static	$rBriefDocType	=	array (
						BriefDoc::NORMAL		=> "Normal",
						BriefDoc::MAILING		=> "Mailing"
					) ;

	/**
	 *
	 */
	function	__construct( $_briefNr, $_type=BriefDoc::NORMAL) {

		$this->BriefNr	=	$_briefNr ;
		$this->fetchFromDb() ;
		$this->myType	=	$_type ;

		$this->myReceiver	=	new Receiver() ;
		switch ( $this->AdrTyp) {
		case	1	:
			$this->myReceiver->setFromAdr( $this->AdrNr, $this->AdrKontaktNr) ;
			break ;
		case	2	:
			$this->myReceiver->setFromKunde( $this->AdrNr, $this->AdrKontaktNr) ;
			break ;
		case	3	:
			$this->myReceiver->setFromLief( $this->AdrNr, $this->AdrKontaktNr) ;
			break ;
		}

		$this->setTexte() ;

	}

	function	setRcvrFromKunde( $_kundeNr, $_kundeKontaktNr) {
		$this->myReceiver->setFromKunde( $_kundeNr, $_kundeKontaktNr) ;
		$this->AdrNr	=	$_kundeNr ;
		$this->AdrKontaktNr	=	$_kundeKontaktNr ;
	}

	function	archive() {

		global	$archivPath ;

		// create the bill-of-delivery-original (Lieferschein-Original)

		$pdfTargetName	=	$archivPath . "Briefe/" . $this->BriefNr . ".pdf" ;
		$pdfName	=	$this->getPDF( 0, "A4", true) ;
		$systemCmd	=	"mv " . $pdfName . " " . $pdfTargetName . " " ;
printf( "%s <br/>", $systemCmd) ;
		system( $systemCmd) ;

	}

	function	printIt( $_prn) {

		global	$archivPath ;

		if ( strcmp( $_prn, "-") != 0) {
			$systemCmd	=	"lpr -P " . $_prn . " " . $archivPath . "Briefe/" . $this->CuDlvrNo . ".pdf " ;
			system( $systemCmd) ;
		}
	}

	function	getPDF( $_cnt, $_size, $_finalMode) {

		global	$texPath ;

		//
		// prepare file names for the different temporary files
		//
		//
		// sequence of file translations:
		//

		$tex_name	=	$this->getTeX( $_cnt, $_size, $_finalMode) ;
		$dvi_name	=	str_replace( ".tex", ".dvi", $tex_name) ;
		$aux_name	=	str_replace( ".tex", ".aux", $tex_name) ;
		$log_name	=	str_replace( ".tex", ".log", $tex_name) ;
		$pdf_name	=	str_replace( ".tex", ".pdf", $tex_name) ;

		// now run the pdf-latex compiler

		$command	=	"cd /tmp; latex " . $tex_name . " >/dev/null ; dvipdf " . $dvi_name . " >/dev/null" ;
		system( $command) ;

		// remove all temporary files

//		system( "rm " . $tex_name) ;
		system( "rm " . $dvi_name) ;
		system( "rm " . $log_name) ;
		system( "rm " . $aux_name) ;
	//	system( "rm " . $pdf_name) ;

		return $pdf_name ;

	}

	function	getTeX( $_cnt, $_size, $_finalMode) {

		require_once( "textools.inc.php" );

		global	$texPath ;

		//

		$signature	=	"" ;

		$prefix	=	"" ;
		$postfix	=	"" ;

		//
		// prepare file names for the different temporary files
		//
		//
		// sequence of file translations:
		//

		if ( $this->myType == BriefDoc::NORMAL) {
			$tex_Iname	=	$texPath . "briefBasisV3.tex" ;
			$texIFile	=	file_get_contents( $tex_Iname) ;
		} else {
			$this->myMailing	=	new Mailing( $this->MailingNr) ;
			$texIFile	=	$this->myMailing->TeXText ;
		}

		$f_name	=	tempnam( "/tmp", "letter-") ;
		$tex_name	=	$f_name . ".tex" ;
		//

		if ( $texIFile) {
			$myReplTable	=	$this->getReplTable() ;
			$myReplTable["SIGNATURE"]	=	"Karl-Heinz Welter" ;
			$myReplTableIn	=	array_keys( $myReplTable) ;
			$myReplTableOut	=	array_values( $myReplTable) ;
			$texOFile	=	str_replace( $myReplTableIn, $myReplTableOut, $texIFile) ;
			$texIFile	=	file_put_contents( $tex_name, $texOFile) ;
		} else {
			echo "Problem ..." ;
			die() ;
		}

		// remove all temporary files

//		system( "rm " . $tex_name) ;

		return $tex_name ;

	}

	function	getReplTable() {

		global	$modusSkonto ;

		$replTable["DRAFTCOPYNAME"]	=	"ENTWURF\n" ;
		$replTable["DRAFTMODE"]		=	"final" ;
		$replTable["NAME"]		=	"khw" ;
		$replTable["SIGNATURE"]		=	"$ $" ;
		$replTable["DATE"]		=	sprintf( "Unsere Ref. Nr. %s / %s", $this->BriefNr, convDate( $this->Datum)) ;
		$replTable["DOCTYPE"]		=	"Bestellung\n" ;
		$replTable["SUBJECT"]		=	$this->Block1 ;
		switch ( $this->myType)	{
		case	BriefDoc::NORMAL	:
			$replTable["TOPTABLE"]		=	
								sprintf( "\\vspace{2cm}\n") .
								sprintf( "\begin{tabular}{lr}\n") . 
								sprintf( "Datum: & %s\\\\\n", convDate( $this->Datum)) . 
//								sprintf( "Adr-Nr.: & %s/%03d\\\\\n", $this->AdrNr, $this->AdrKontaktNr) . 
								sprintf( "\\\\[2mm]\n") . 
								sprintf( "Ref.: & %s\\\\\n", $this->RefNr) . 
								sprintf( "vom: & %s\\\\\n", convDate( $this->RefDatum)) . 
								sprintf( "\end{tabular}\n") ;
			break ;
		case	BriefDoc::MAILING	:
			$replTable["TOPTABLE"]		=	
								sprintf( "\flushright{\scalebox{0.70}[0.70]{\includegraphics{/srv/www/vhosts/modis-gmbh.eu/bilder/tex-pics/".$this->myMailing->BildRef."}}}\\\\\n") .
								sprintf( $this->myMailing->BildText . "\\\\\n") ;
			break ;
		}
		$replTable["ADRESSE"]		=	$this->myReceiver->getAdrAsTeX() ;
		$replTable["OPENING"]		=	sprintf( "%s", $this->myReceiver->getGreetingAsTex()) ;
		$replTable["PREFIX"]		=	$this->xmlToTex( $this->Block3) ;
		$replTable["EINLEITUNG"]	=	"$ $" ;
		$replTable["LOOPLISTE"]		=	"" ;
		$replTable["ADDITION"]		=	mTS( $this->Block4) ;
		$replTable["ADRCODE"]		=	"" ;
		$replTable["AUSLEITUNG"]	=	mTS( $this->Block5) . "$ $\\\\[5mm]\n" ;
		$replTable["BLOCKPS"]	=	mTS( $this->BlockPS) . "$ $\\\\[5mm]\n" ;
		$replTable["ADRID"]	=	mTS( $this->myReceiver->AdrNr . "/" . $this->myReceiver->AdrKontaktNr . "/" . $this->myReceiver->myAdrTyp) ;
		$replTable["PICTUREREF"]	=	"/srv/www/vhosts/modis-gmbh.eu/bilder/tex-pics/PSC/bio_main_41524.eps" ;
		$replTable["SCHULE1"]	=	mTS( $this->myReceiver->FirmaName1) ;
		$replTable["SCHULE2"]	=	mTS( $this->myReceiver->FirmaName2) ;
		$replTable["KONTAKT"]	=	mTS( $this->myReceiver->getNameAsTeX()) ;

		return $replTable ;

	}

	private	function	setTexte() {
		switch ( $this->myReceiver->lC) {
		case	"de"	:
			$this->Closing	=	"Mit freundlichen Gr..." ;
			break ;
		case	"en"	:
			$this->Closing	=	"Kind regards" ;
			break ;
		default	:
			$this->Closing	=	"Mit freundlichen Gr..." ;
			break ;
		}
	}

	/**
	 *	Funktion:	xmlToTex( $_text)
	 */
	function	xmlToTex( $_text) {

		global	$webPrefix ;

		FDbg::dumpL( 0x01000000, "BriefDoc::xmlToTex: ") ;

		$buffer	=	"" ;

		if ( strlen( $_text) == 0) {
			return $buffer ;
		}
		
		$xml	=	new XMLReader() ;
		$xml->XML( iconv( "ISO-8859-1", "UTF-8", $_text)) ;
		while ( $xml->read()) {
			switch ( $xml->nodeType) {
			case	1	:			// start element
				FDbg::dumpL( 0x08000000, "BriefDoc::xmlToTex: found start of '%s'", $xml->name) ;
				if ( strcmp( $xml->name, "div") == 0) {
					$buffer	.=	"{\n" ;
				} else if ( strcmp( $xml->name, "ul") == 0) {
					$buffer	.=	"\\begin{itemize}\n" ;
				} else if ( strcmp( $xml->name, "li") == 0) {
					$buffer	.=	"\\item " ;
				} else if ( strcmp( $xml->name, "b") == 0) {
//					$buffer	.=	"\\emph{" ;
					$buffer	.=	"{\\sf\\bfseries " ;
				} else if ( strcmp( $xml->name, "a") == 0) {
					$attr	=	$xml->getAttribute( "href") ;
					if ( strncmp( $attr, "http:", 5) == 0) {
						$buffer	.=	mTS( "(siehe: " . $attr . ") ") ;
					} else {
						$buffer	.=	mTS( "(siehe: " . $webPrefix . $attr . ") ") ;
					}
				} else if ( strcmp( $xml->name, "br") == 0) {
					$buffer	.=	"\\\\" ;
				} else if ( strcmp( $xml->name, "posber") == 0) {
					FDbg::dumpL( 0x10000000, "BriefDoc::xmlToTex: found <posber/>") ;
					$buffer	.=	$this->myReceiver->getPosBer() ;
				}
				break ;
			case	3	:			// text node
				if ( mb_check_encoding( $xml->value, "ISO-8859-1")) {
					$buffer	.=	mTS( iconv( "UTF-8", "ISO-8859-1//TRANSLIT", $xml->value)) ;
				} else {
					printf( "<<<<<<<%s>>>>>>>>>",  $xml->value) ;
					die() ;
				}
				
				break ;
			case	14	:			// whitespace node
				$buffer	.=	mTS( iconv( "UTF-8", "ISO-8859-1", $xml->value)) ;
				break ;
			case	15	:			// end element
				FDbg::dumpL( 0x08000000, "BriefDoc::xmlToTex: found end of '%s'", $xml->name) ;
				if ( strcmp( $xml->name, "div") == 0) {
					$buffer	.=	"}\n" ;
				} else if ( strcmp( $xml->name, "ul") == 0) {
					$buffer	.=	"\\end{itemize}\n" ;
				} else if ( strcmp( $xml->name, "li") == 0) {
					$buffer	.=	"" ;
				} else if ( strcmp( $xml->name, "p") == 0) {
					$buffer	.=	"\\\\" ;
				} else if ( strcmp( $xml->name, "b") == 0) {
					$buffer	.=	"}" ;
				} else if ( strcmp( $xml->name, "a") == 0) {
				} else if ( strcmp( $xml->name, "br") == 0) {
					$buffer	.=	"\\vspace{5mm}" ;
				} else if ( strcmp( $xml->name, "skip") == 0) {
					$buffer	.=	"\\par\n" ;
				} else if ( strcmp( $xml->name, "posber") == 0) {
					FDbg::dumpL( 0x10000000, "BriefDoc::xmlToTex: found <posber/>") ;
					$buffer	.=	$this->myReceiver->getPosBer() ;
				}
				break ;
			case	16	:			// end element
				break ;
			}
		}
		return $buffer ;
	}

}

?>
