<?php
/**
 * 
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;

//session_start() ;

	$fullFilename	=	$myConfig->path->Archive . "Lief/" . showManageVar( "_IFilename", "INVALID") ;
	$maxCount		=	intval( showManageVar( "_IMaxCount", 50)) ;
	$startLine		=	intval( showManageVar( "_IStartLine", 5)) ;
	$endLine		=	intval( showManageVar( "_IEndLine", 5)) ;
	$myArtNrCol		=	intval( showManageVar( "_IArtNrCol", 0)) ;
	$defCurr		=	showManageVar( "_IDefCurr", "EUR") ;
	$defMenge		=	intval( showManageVar( "_IDefMenge", 1)) ;
	$defHKRabKlasse	=	showManageVar( "_IDefHKRabKlasse", "") ;
	$defMengeProVPE	=	showManageVar( "_IDefMengeProVPE", 1) ;
	$defMengeFuerPreis	=	showManageVar( "_IDefMengeFuerPreis", 1) ;
	$nrFormat		=	showManageVar( "_INrFormat", 0) ;
	$gueltigVon		=	showManageVar( "_IGueltigVon", "2010-01-01") ;
	$gueltigBis		=	showManageVar( "_IGueltigBis", "2010-12-31") ;
	$genEKPreisR	=	intval( showManageVar( "_IGenEKPreisR", 0)) ;

	$parts	=	explode( "_", $_POST["_IFilename"]) ;
	$myLiefNr	=	$parts[0] ;

?>
<html dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" media="screen" href="/styles/v1<?php echo $_SERVER['PHP_AUTH_USER'] ; ?>.css" title="Version 1 <?php echo $_SERVER['PHP_AUTH_USER'] ; ?>" />
<link rel="alternate stylesheet" type="text/css" media="screen" href="/styles/v1.css" title="Version 1" />
<link rel="alternate stylesheet" type="text/css" media="screen" href="/styles/v1alt.css" title="Version 1 Alt" />
</head>
<body>
<fieldset>
	<legend>Ausgew&auml;hlte Preisliste iterativ auswerten</legend>
	<form action="/ModBase/Lief/doEval.php" method="post" name="showLiefFormat" enctype="multipart/form-data">
		<div id="content">
			<div id="maindata">
				<table><?php
					rowDisplay( "Lieferant Nr.:", "_ILiefNr", 10, 10, $myLiefNr, "") ;
					rowDisplay( "Dateiname:", "_IFilename", 10, 10, $_SESSION['_IFilename'], "") ;
					rowOption( "Preis Kalkulation:", "_IGenEKPreisR", Opt::getRFlagNeinJa(), $genEKPreisR, "") ;
					rowEdit( "Gueltig von:", "_IGueltigVon", 10, 10, $_SESSION['_IGueltigVon'], "") ;
					rowEdit( "Gueltig bis:", "_IGueltigBis", 10, 10, $_SESSION['_IGueltigBis'], "") ;
					rowEdit( "Artikel Nr. Spalte:", "_IArtNrCol", 10, 10, $myArtNrCol, "") ;
					rowEdit( "Max. Anzahl Zeilen f&uuml;r Anzeige:", "_IMaxCount", 10, 10, $_SESSION['_IMaxCount'], "") ;
					rowEdit( "Default Menge:", "_IDefMenge", 10, 10, $_SESSION['_IDefMenge'], "") ;
					rowEdit( "Default HK-Rab. Klasse:", "_IDefHKRabKlasse", 10, 10, $_SESSION['_IDefHKRabKlasse'], "") ;
					rowEdit( "Default Menge pro VPE:", "_IDefMengeProVPE", 10, 10, $_SESSION['_IDefMengeProVPE'], "") ;
					rowEdit( "Default Menge fuer Preis:", "_IDefMengeFuerPreis", 10, 10, $_SESSION['_IDefMengeFuerPreis'], "") ;
					rowOption( "Default W&auml;hrung:", "_IDefCurr", Opt::getRCurrCodes(), $_SESSION['_IDefCurr'], "") ;
					rowEditDbl( "Zeilen:", "_IStartLine", 5, 5, $_SESSION['_IStartLine'], "_IEndLine", 6, 10, $_SESSION['_IEndLine'], "") ;
			?>
			<tr>
				<td>Aktionen:</td>
				<td><input type="submit" name="actionRefresh" value="Iterieren" tabindex="14" border="0" />
				    <input type="submit" name="actionEvaluate" value="Auswerten" tabindex="14" border="0" /></td>
			</tr>
		</table>
	<table border="1">
<?php
	$firstLine	=	TRUE ;
	$myCSVFile	=	fopen( $fullFilename, "r") ;

	/**
	 *
	 */
	$lineCnt	=	1 ;
	$blockCnt	=	1 ;
	while ( ( $myFields = fgetcsv( $myCSVFile)) !== FALSE && $lineCnt <= $maxCount) {
		if ( $firstLine) {
			printf( "<tr>\n") ;
			printf( "<td>Line:</td>\n") ;
			$colCnt	=	0 ;
			foreach ( $myFields AS $value) {

				/**
				 * 
				 */
				$colName	=	sprintf( "_IColName_%s_%03d", $myLiefNr, $colCnt) ;
				$colSplit	=	sprintf( "_IColSplit_%s_%03d", $myLiefNr, $colCnt) ;
				$colSplitChars	=	sprintf( "_IColSplitChars_%s_%03d", $myLiefNr, $colCnt) ;
				$colFieldAction	=	sprintf( "_IColFieldAction_%s_%03d", $myLiefNr, $colCnt) ;
				if ( isset( $_POST[ $colName])) {
					$_SESSION[ $colName]	=	$_POST[ $colName] ;
					if ( isset( $_POST[ $colSplit]))
						$_SESSION[ $colSplit]	=	$_POST[ $colSplit] ;
					if ( isset( $_POST[ $colSplitChars]))
						$_SESSION[ $colSplitChars]	=	$_POST[ $colSplitChars] ;
					if ( isset( $_POST[ $colFieldAction]))
						$_SESSION[ $colFieldAction]	=	$_POST[ $colFieldAction] ;
				} else if ( isset( $_SESSION[ $colName])) {
				} else {
					$_SESSION[ $colName]	=	Opt::Evaluate ;
					$_SESSION[ $colSplit]	=	Opt::NoSplit ;
					$_SESSION[ $colSplitChars]	=	"/" ;
					$_SESSION[ $colSplitChars]	=	Opt::ActionNone ;
				}

				/**
				 * 
				 */
				if ( ! isset( $_SESSION[ $colSplit])) {
					$_SESSION[ $colSplit]	=	Opt::NoSplit ;
				}
				if ( ! isset( $_SESSION[ $colSplitChars])) {
					$_SESSION[ $colSplitChars]	=	"/" ;
				}
				if ( ! isset( $_SESSION[ $colFieldAction])) {
					$_SESSION[ $colFieldAction]	=	Opt::ActionNone ;
				}

				/**
				 * 
				 */
				if ( intval( $_SESSION[ $colName]) === Opt::Ignore) {
					printf( "<td>%s</td>\n", Opt::optionRet( Opt::getRFieldEval(), 0, $colName)) ;
				} else {
					printf( "<td>") ;
					printf( "%s<br/>%s<br/>",
									Opt::optionRet( Opt::getRFieldNames(), intval( $_SESSION[ $colName]), $colName),
									Opt::optionRet( Opt::getRSplitMode(), intval( $_SESSION[ $colSplit]), $colSplit)) ;
					if ( intval( $_SESSION[ $colSplit]) === Opt::Split) {
						printf( "<input name=\"%s\" value=\"%s\" /><br/>", $colSplitChars, $_SESSION[ $colSplitChars]) ;
						printf( "%s",
										Opt::optionRet( Opt::getRFieldAction(), intval( $_SESSION[ $colFieldAction]), $colFieldAction)) ;
					}
					printf( "</td>") ;
				}
				$colCnt++ ;
			}
			reset( $myFields) ;
			printf( "</tr>\n") ;
			$firstLine	=	FALSE ;
		}

		/**
		 *
		 */
		$blockCnt++ ;
		if ( $blockCnt == 10) {
			$blockCnt	=	0 ;
			printf( "<tr>\n") ;
			printf( "<td>Line:</td>\n") ;
			$colCnt	=	0 ;
			foreach ( $myFields AS $value) {

				/**
				 * 
				 */
				$colName	=	sprintf( "_IColName_%s_%03d", $myLiefNr, $colCnt) ;

				/**
				 * 
				 */
				if ( intval( $_SESSION[ $colName]) === Opt::Ignore) {
					printf( "<td>") ;
					$texte	=	Opt::getRFieldEval() ;
					printf( "%s<br/>", $texte[ intval( $_SESSION[ $colName])]) ;
					printf( "</td>") ;
				} else {
					printf( "<td>") ;
					$texte	=	Opt::getRFieldNames() ;
					printf( "%s<br/>", $texte[ intval( $_SESSION[ $colName])]) ;
					printf( "</td>") ;
				}
				$colCnt++ ;
			}
			reset( $myFields) ;
			printf( "</tr>\n") ;
			$firstLine	=	FALSE ;
		}
		
		if ( $lineCnt >= intval( $_SESSION['_IStartLine'])) {
			printf( "<tr style=\"background-color: #ffbbbb; font-size: 11pt;\">\n") ;
		} else {
			printf( "<tr style=\"background-color: #bbbbbb; font-size: 11pt;\">\n") ;
		}
		printf( "<td>%d</td>\n", $lineCnt) ;
		$colCnt	=	0 ;
		foreach ( $myFields AS $value) {
			$colName	=	sprintf( "_IColName_%s_%03d", $myLiefNr, $colCnt) ;
			$colSplit	=	sprintf( "_IColSplit_%s_%03d", $myLiefNr, $colCnt) ;
			$colSplitChars	=	sprintf( "_IColSplitChars_%s_%03d", $myLiefNr, $colCnt) ;
			$colFieldAction	=	sprintf( "_IColFieldAction_%s_%03d", $myLiefNr, $colCnt) ;
			if ( intval( $_SESSION[ $colSplit]) == Opt::Split && $lineCnt >= intval( $_SESSION['_IStartLine'])) {
				$parts	=	explode( $_SESSION[ $colSplitChars], $value) ;
				if ( ! isset( $parts[1]))
					$parts[1]	=	"1" ;
				switch ( intval( $_SESSION[ $colFieldAction])) {
				case	Opt::ActionMul:
					$value	=	sprintf( "%d", getInt( $parts[0]) * getInt( $parts[1])) ;
					break ;
				case	Opt::ActionLeft:
					$value	=	$parts[0] ;
					break ;
				case	Opt::ActionRight:
					$value	=	$parts[1] ;
					break ;
				default	:
					break ;
				}
			}
			switch ( intval( $_SESSION[ $colName])) {
			case	Opt::MengeFuerPreis	:
			case	Opt::MengeProVPE	:
			case	Opt::Menge1	:
			case	Opt::Menge2	:
			case	Opt::Menge3	:
			case	Opt::Menge4	:
				printf( "<td>I:%10d</td>\n", getInt( $value)) ;
				break ;
			case	Opt::Preis1	:
			case	Opt::Preis2	:
			case	Opt::Preis3	:
			case	Opt::Preis4	:
			case	Opt::LiefVKPreis1	:
			case	Opt::LiefVKPreis2	:
			case	Opt::LiefVKPreis3	:
			case	Opt::LiefVKPreis4	:
				printf( "<td>F:%10.2f", getFloat( $value, ",")) ;
				break ;
			default	:
				if ( $colCnt == $myArtNrCol && $lineCnt >= intval( $startLine)) {
					printf( "<td style=\"background-color: #ff6666; font-size: 12pt;\">%s</td>\n", $value) ;
				} else {
					printf( "<td>%s</td>\n", $value) ;
				}
				break ;
			}
			$colCnt++ ;
		}
		reset( $myFields) ;
		printf( "</tr>\n") ;
		$lineCnt++ ;
	}
	fclose( $myCSVFile) ;

function	showManageVar( $_varname, $_def) {
	if ( isset( $_POST[ $_varname])) {
		$_SESSION[ $_varname]	=	$_POST[ $_varname] ;
	} else if ( isset( $_SESSION[ $_varname])) {
	} else {
		$_SESSION[ $_varname]	=	$_def ;
	}
	return $_SESSION[ $_varname] ;
}
?>
	</table>
	</form>
</fieldset>
</body>
</html>
