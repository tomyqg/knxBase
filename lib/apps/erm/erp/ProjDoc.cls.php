<?php

require_once( "global.inc.php") ;
require_once( "option.inc.php") ;
require_once( "iPrintable.inc.php") ;
require_once( "iListDoc.inc.php") ;
require_once( "Receiver.php" );
require_once( "Proj.php" );
require_once( "CuOffrDoc.cls.php") ;

class	ProjDoc	extends Proj	implements iListDoc, iPrintable {

	public	$myReceiver ;
	public	$taxes = array() ;
	public	$net = array() ;
	public	$totalNet ;
	public	$totalGross ;

	function	__construct( $_projNr) {

		$this->taxes['A']	=	19.0 ;
		$this->taxes['B']	=	 7.0 ;
		$this->taxes['C']	=	 0.0 ;
		$this->net['A']	=	 0.0 ;
		$this->net['B']	=	 0.0 ;
		$this->net['C']	=	 0.0 ;

		$this->ProjNr	=	$_projNr ;
		$this->fetchFromDb() ;

		$this->myReceiver	=	new Receiver() ;
		$this->myReceiver->setFromKunde( $this->KundeNr, $this->KundeKontaktNr) ;

		$this->setTexte() ;

	}

	function	archive() {

		global	$archivPath ;

		// create the bill-of-delivery-original (Lieferschein-Original)

		$pdfTargetName	=	$archivPath . "Angebote/P" . $this->ProjNr . ".pdf" ;
		$pdfName	=	$this->getPDF( 0, "A4", true) ;
		$systemCmd	=	"mv " . $pdfName . " " . $pdfTargetName . " " ;
printf( "%s <br/>", $systemCmd) ;
		system( $systemCmd) ;

	}

	function	printIt( $_prn) {

		global	$archivPath ;

		if ( strcmp( $_prn, "-") != 0) {
			$systemCmd	=	"lpr -P " . $_prn . " " . $archivPath . "Angebote/" . $this->ProjNr . ".pdf " ;
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

		$f_name	=	tempnam( "/tmp", "letter-") ;
		$tex_name	=	$f_name . ".tex" ;
		$tex_Iname	=	$texPath . "briefBasisV3.tex" ;

		//

		$texIFile	=	file_get_contents( $tex_Iname) ;
		if ( $texIFile) {
			$myReplTable	=	$this->getReplTable() ;
			$myReplTableIn	=	array_keys( $myReplTable) ;
			$myReplTableOut	=	array_values( $myReplTable) ;
			$myReplTable	=	$this->getReplTable() ;
			$texOFile	=	str_replace( $myReplTableIn, $myReplTableOut, $texIFile) ;
			$texIFile	=	file_put_contents( $tex_name, $texOFile) ;
		} else {
			echo "Problem ..." ;
		}

		// remove all temporary files

//		system( "rm " . $tex_name) ;

		return $tex_name ;

	}

	function	getReplTable() {

		global	$modusSkonto ;

		$replTableOut = array() ;
		
		$replTableIn["DRAFTCOPYNAME"]	=	"ENTWURF\n" ;
		$replTableIn["DRAFTMODE"]	=	"final" ;
		$replTableIn["NAME"]		=	"khw" ;
		$replTableIn["SIGNATURE"]	=	"$ $" ;
		$replTableIn["DATE"]		=	sprintf( "%s: %s / %s", $this->BestellungNrText, $this->ProjNr, convDate( $this->Datum)) ;
		$replTableIn["DOCTYPE"]		=	"Angebot / Aufstellung\n" ;
		$replTableIn["SUBJECT"]		=	sprintf( "%s", $this->BestellungText) ;
		$replTableIn["TOPTABLE"]	=	
							sprintf( "\begin{tabular}{lr}\n") . 
							sprintf( "Projekt: & %s\\\\\n", $this->ProjNr) . 
							sprintf( "Datum: & %s\\\\\n", convDate( $this->Datum)) . 
							sprintf( "Kunden-Nr.: & %s/%03d\\\\\n", $this->KundeNr, $this->KundeKontaktNr) . 
							sprintf( "\\\\[2mm]\n") . 
							sprintf( "Ihre Ausschreibung: & %s\\\\\n", $this->KdRefNr) . 
							sprintf( "vom: & %s\\\\\n", convDate( $this->KdRefDatum)) . 
							sprintf( "\end{tabular}\n") ;
		$replTableIn["ADRESSE"]		=	$this->myReceiver->getAdrAsTeX() ;
		$replTableIn["OPENING"]		=	sprintf( "%s", $this->myReceiver->getGreetingAsTex()) ;
		$replTableIn["PREFIX"]		=	xmlToTex( $this->Prefix) ;
		$replTableIn["EINLEITUNG"]	=	"" ;
		$replTableIn["LOOPLISTE"]	=	$this->writeProjPos( $this) ;
		$replTableIn["ADDITION"]	=	// xmlToTex( "<div>Zahlungsbedingungen: " . $modusSkonto[ $this->ModusSkonto] . "</div>") .
							xmlToTex( $this->Postfix) ; ;
		$replTableIn["ADRCODE"]		=	"" ;
		$replTableIn["AUSLEITUNG"]	=	$this->Closing ;

		return $replTableIn ;

	}

	//
	// writeProjPos
	//
	// Gibt die Liste aller Artikel in diesem Projekt aus
	//

	function	writeProjPos( $_proj) {

		require_once( "Artikel.php") ;
		require_once( "ProjPos.php") ;


		$result	=	false ;

		$buf	=	"" ;

		$myProjPos	=	new ProjPos() ;

		$this->totalNet	=	0.0 ;

		$query	=	"select * " ;
		$query	.=	"from ProjPos " ;
		$query	.=	"where ProjNr = '" . $_proj->ProjNr . "' " ;
		$query	.=	"order by PosNr " ;

		$sqlResult =       FDb::query( $query) ;

		if ( $sqlResult) {
			$numrows        =       FDb::numrows( $_db) ;
			if ( $numrows > 0) {
				while ($row = mysql_fetch_assoc( $sqlResult)) {
					$myCuOffr	=	new CuOffrDoc( $_db, $row['CuOffrNo']) ;
//					$myCuOffr->renumber( $_db, 1) ;
					if ( strlen( $row['Prefix']) > 0) {
						$buf	.=	 xmlToTex( $row['Prefix']) ;
						$buf	.=	 "\\\\" ;
					}
					$buf	.=	 "Angebot Nr. " . $row[ 'CuOffrNo'] . "\\\\" ;
					$buf	.=	 $myCuOffr->writePosTableProj( $_db) ;

					foreach ( $myCuOffr->net as $_mwstSatz => $netto) {
						$this->net[$_mwstSatz]	+=	$netto ;
					}
					reset( $this->net) ;

					if ( strlen( $row['Postfix']) > 0) {
						$buf	.=	 xmlToTex( $row['Postfix']) ;
						$buf	.=	 "\\\\" ;
					}
				}
				$buf	.=	$this->beginTable( $_db) ;
				$buf	.=	$this->showNetto( $_db) ;
				$buf	.=	$this->showTaxes( $_db) ;
				$buf	.=	$this->showGross( $_db) ;
				$buf	.=	$this->endTable( $_db) ;
			} else {
				$buf	.=	"KEINE PROJEKT POSITIONEN" ;
				$buf	.=	$query ;
			}
		} else {
			$buf	.=	"FEHLER BEI DATENBANKZUGRIFF" ;
			$buf	.=	$query ;
		}
	
		return $buf ;

	}

	function	beginTable( $_db) {

		$buf	=	"" ;

		// start of table and table heading

		$buf	.=	 "Mehrwertsteuerbetr\"{a}ge und Gesamtbrutto \\\\[-0.0ex]\n" ;
		$buf	.=	 "\begin{longtable}{lr}\n" ;
		switch ( $this->myReceiver->lC) {
		case	"de"	:
			$buf	.=	 " & \\\\[-0.0ex]\n" ;
			break ;
		case	"en"	:
			$buf	.=	 " & \\\\[-0.0ex]\n" ;
			break ;
		default	:
			$buf	.=	 " & \\\\[-0.0ex]\n" ;
			break ;
		}

		return $buf ;

	}

	function	endTable( $_db) {

		$buf	=	"" ;

		$buf	.=	 "\end{longtable}\n" ;

		return $buf ;

	}

	function	showNetto( $_db) {

		// write shipping and handling (S&H) cost

		$this->totalNet	=	0.0 ;
		foreach ( $this->net as $_mwstSatz => $netto) {
			$this->totalNet	+=	$netto ;
		}
		reset( $this->net) ;

		$buf	=	"" ;
		$buf	.=	sprintf( " %s, %s & %s \\\\[0.3ex]\n",
					$this->NettoText,
					$this->myReceiver->Currency,
					number_format( $this->totalNet, 2, ",", "")) ;
		$buf	.=	" \\\\[-1.5ex] \n" ;

		return $buf ;

	}

	function	showGross( $_db) {

		// write shipping and handling (S&H) cost

		$this->totalGross	=	0.0 ;
		foreach ( $this->net as $_mwstSatz => $netto) {
			$this->totalGross	+=	$netto + round( $netto * $this->taxes[ $_mwstSatz] / 100.0, 2) ;
		}
		reset( $this->net) ;

		$buf	=	"" ;
		$buf	.=	sprintf( " Gesamt & %s \\\\[0.3ex]", number_format( $this->totalGross, 2, ",", ".")) ;

		return $buf ;

	}

	function	showTaxes( $_db) {

		// write shipping and handling (S&H) cost

		$buf	=	"" ;
		if ( $this->myReceiver->Tax == 1) {
			foreach ( $this->net as $_mwstSatz => $netto) {
				$myMwst	=	round( $this->net[ $_mwstSatz] * $this->taxes[ $_mwstSatz] / 100.0, 2) ;
				if ( $myMwst > 0.0) {
					$buf	.=	 sprintf( " Mehrwertsteuer (%s), %s\\%% auf EUR %s & %s \\\\[0.3ex]\n",
								$_mwstSatz,
								number_format( $_mwstSatz, 0, ",", ""),
								number_format( $this->net[ $_mwstSatz], 2, ",", ""),
								number_format( $myMwst, 2, ",", "")
						) ;
				}
			}
			reset( $this->taxes) ;
		}

		return $buf ;

	}

	function	writeRabattKd( $_netto, $_rabattSatz, & $_rabatt) {

		$buf	=	"" ;

		//

		$_rabatt	=	0.0 ;
		$myRabattSatz	=	0.0 ;

		//

		if ( $_rabattSatz > 1) {
			$myRabattSatz	=	$_rabattSatz ;
		} else {
			$myRabattSatz	=	$_rabattSatz * 100.0 ;
		}
		$_rabatt	=	$_netto * $myRabattSatz / 100.0 ;

		//

		if ( $myRabattSatz > 0.1) {
			$myRabatt	=	$_rabatt * $_netto / 100 ;
			$buf	.=	sprintf( " &  & Rabatt %s\\%% (auf: %s) & & & -%s \\\\[0.3ex]",
					number_format( $myRabattSatz, 1, ",", ""),
					number_format( $_netto, 2, ",", ""),
						number_format( $_rabatt, 2, ",", "")
				) ;
		}

		//

		return $buf ;
	}

	function	setTexte() {
		switch ( $this->myReceiver->lC) {
		case	"de"	:
			$this->NettoText	=	"Nettobetrag" ;
			$this->BestellungText	=	"Projekt" ;
			$this->BestellungNrText	=	"Projekt Nr." ;
			$this->DatumText	=	"Datum" ;
			$this->KdNrText	=	"Kunden Nr." ;
			$this->LfNrText	=	"Lieferant Nr." ;
			$this->Closing	=	"" ;
			break ;
		case	"en"	:
			$this->NettoText	=	"Net amount" ;
			$this->BestellungText	=	"Project" ;
			$this->BestellungNrText	=	"Project Nr." ;
			$this->DatumText	=	"Date" ;
			$this->KdNrText	=	"Customer Nr." ;
			$this->LfNrText	=	"Supplier Nr." ;
			$this->Closing	=	"" ;
			break ;
		default	:
			$this->NettoText	=	"Nettobetrag" ;
			$this->BestellungText	=	"Projekt" ;
			$this->BestellungNrText	=	"Projekt Nr." ;
			$this->DatumText	=	"Datum" ;
			$this->KdNrText	=	"Kunden Nr." ;
			$this->LfNrText	=	"Lieferant Nr." ;
			$this->Closing	=	"" ;
			break ;
		}
	}

}

?>
