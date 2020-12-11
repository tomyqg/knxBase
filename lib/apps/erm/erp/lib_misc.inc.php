<?php
/**
 *
 */
function	makeSedSafe( $myStr) {
	$result	=	"" ;
	$i	=	0 ;
	$len	=	strlen( $myStr) ;
	while ( $i < $len) {
		switch ( $myStr[$i]) {
		case	"&"	:
			$result	.=	"\\\&" ;
			break ;
		case	"#"	:
			$result	.=	"\\\\#" ;
			break ;
		case	"/"	:
			$result	.=	"\\".$myStr[$i] ;
			break ;
		case	"{"	:
			$result	.=	"\\\\".$myStr[$i] ;
			break ;
		case	"}"	:
			$result	.=	"\\\\".$myStr[$i] ;
			break ;
		default	:
			$result	.=	$myStr[$i] ;
			break ;
		}
		$i++ ;
	}
	return $result ;
}
/**
 *
 */
function	makeFileName( $_baseName, $_prefix='') {
	$retStr	=	"" ;
	$inStr	=	$_baseName ;
	for ( $i=0 ; $i<strlen( $inStr) ; $i++) {
		if ( ord( $inStr[ $i]) >= 0 && ord( $inStr[ $i]) < 127) {
			switch ( ord( $inStr[ $i])) {
			case	ord( " ")	:
				$retStr	.=	"-" ;
				break ;
			case	ord( "*")	:
			case	ord( "%")	:
			case	ord( "&")	:
			case	ord( "#")	:
			case	ord( "{")	:
			case	ord( "}")	:
			case	ord( "[")	:
			case	ord( "}")	:
			case	ord( "!")	:
			case	ord( "@")	:
			case	ord( "|")	:
			case	ord( "$")	:
			case	ord( "^")	:
			case	ord( ".")	:
			case	ord( ",")	:
			case	ord( "<")	:
			case	ord( ">")	:
			case	ord( "/")	:
			case	ord( "?")	:
			case	ord( ";")	:
			case	ord( ":")	:
			case	ord( "'")	:
			case	ord( "\"")	:
			case	ord( "\\")	:
			case	ord( "`")	:
			case	ord( "~")	:
			case	ord( "+")	:
			case	ord( "=")	:
				break ;
			default	:
				$retStr	.=	$inStr[ $i] ;
				break ;
			}
		} else if (( ord( $inStr[ $i]) & 0xe0) == 0xc0) {
			$utfChar	=	ord( $inStr[ $i]) * 256 + ord( $inStr[ $i+1]) ;
			$i++ ;
			switch ( $utfChar) {
			case	0xc384	:			// Oe
				$retStr	.=	"Ae" ;
				break ;
			case	0xc396	:			// Oe
				$retStr	.=	"Oe" ;
				break ;
			case	0xc39c	:			// Oe
				$retStr	.=	"Ue" ;
				break ;
			case	0xc3a4	:			// Ae
				$retStr	.=	"ae" ;
				break ;
			case	0xc3b6	:			// Oe
				$retStr	.=	"oe" ;
				break ;
			case	0xc3bc	:			// Oe
				$retStr	.=	"ue" ;
				break ;
			case	0xc39f	:			// Oe
				$retStr	.=	"ss" ;
				break ;
			default	:
				break ;
			}
		} else {
		}
	}
	return $retStr ;
}
/**
 *
 */
function	convDate( $_date) {
	$parts	=	explode( "-", $_date) ;
	$myDate	=	$parts[2] . "." . $parts[1] . "." . $parts[0] ;
	return $myDate ;
}
/**
 *
 */
function	combinePDFs( $target, $pdfs) {
	FDbg::dumpL( 0x10000000, "lib_misc::combinePDFs(...)") ;
	FDbg::dumpL( 0x10000000, "lib_misc::combinePDFs(...): pdfCombineTool='%s'", $pdfCombineTool) ;
	$myConfig	=	EISSCoreObject::__getConfig() ;
	$pdfCombineTool	=	$myConfig->pdf->concatTool ;
	switch ( $pdfCombineTool) {
	case	"pdftk"	:
		$systemCmd	=	"pdftk " ;
		foreach ( $pdfs as $pdf) {
			$systemCmd	.=	$pdf . " " ;
		}
		$systemCmd	.=	"cat output " . $target ;
		error_log( "lib_misc::combinePDFs: systemCmd = '" . $systemCmd . "'" ) ;
		$result	=	system( $systemCmd) ;
		FDbg::dumpL( 0x10000000, "lib_misc::combinePDFs: result = %d", $result) ;
		break ;
	case	"gs"	:
		$systemCmd	=	"gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=" . $target . " " ;
		foreach ( $pdfs as $pdf) {
			$systemCmd	.=	$pdf . " " ;
		}
		FDbg::dumpL( 0x10000000, "lib_misc::combinePDFs: systemCmd = '%s'", $systemCmd) ;
		$result	=	system( $systemCmd) ;
		break ;
	default	:
		printf( "PANIC: no valid PDF combiner specified <br/>\n") ;
		break ;
	}
	FDbg::dumpL( 0x10000000, "lib_misc::combinePDFs(...) done") ;
}

/**
 *
 */
function	overlayPDF( $target, $pdf, $overlay) {
	FDbg::dumpL( 0x10000000, "lib_misc::overlayPDF(...)") ;
	FDbg::dumpL( 0x10000000, "lib_misc::overlayPDF(...): pdfCombineTool='%s'", $pdfCombineTool) ;
	$myConfig	=	EISSCoreObject::__getConfig() ;
	$pdfOverlayTool	=	$myConfig->pdf->overlayTool ;
	switch ( $pdfOverlayTool) {
	case	"pdftk"	:
		$systemCmd	=	"pdftk " ;
		$systemCmd	.=	$pdf . " " ;
		$systemCmd	.=	"background " . $overlay . " output " . $target ;
		FDbg::dumpL( 0x10000000, "lib_misc::overlayPDF: systemCmd = '%s'", $systemCmd) ;
		$result	=	system( $systemCmd) ;
		FDbg::dumpL( 0x10000000, "lib_misc::overlayPDF: result = %d", $result) ;
		break ;
	case	"gs"	:
		$systemCmd	=	"gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=" . $target . " " ;
		foreach ( $pdfs as $pdf) {
			$systemCmd	.=	$pdf . " " ;
		}
		error_log( "lib_misc::overlayPDF: systemCmd = '%s'", $systemCmd) ;
		$result	=	system( $systemCmd) ;
		break ;
	default	:
		printf( "PANIC: no valid PDF combiner specified <br/>\n") ;
		break ;
	}
	FDbg::dumpL( 0x10000000, "lib_misc::overlayPDF(...) done") ;
}

function	myFiletype( $_filename) {
	$parts	=	explode( ".", $_filename) ;
	foreach ( $parts as $value) {
		$myType	=	$value ;
	}
	return $myType ;
}

?>
