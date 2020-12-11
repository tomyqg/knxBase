<?php

//
// Funktion:	mTS( str)
//
// Diese Funktion übersetzt die Zeichen in einer Zeichenkette von ISO-8859-1 nach TeX
//

function	mTS( $_inStr) {
	$inStr	=	iconv( 'UTF-8', 'windows-1252', $_inStr) ;
	$retStr	=	"" ;
	for ( $i=0 ; $i<strlen( $inStr) ; $i++) {
		if ( ord( $inStr[ $i]) >= 0 && ord( $inStr[ $i]) < 127) {
			switch ( ord( $inStr[ $i])) {
			case	ord( "^")	:
				$retStr	.=	"\^ " ;
				break ;
			case	ord( "_")	:
				$retStr	.=	"\\_" ;
				break ;
			case	ord( ">")	:
				$retStr	.=	"$>$" ;
				break ;
			case	ord( "<")	:
				$retStr	.=	"$<$" ;
				break ;
			case	ord( "*")	:
				$retStr	.=	"*" ;
				break ;
			case	ord( "%")	:
				$retStr	.=	"\%" ;
				break ;
			case	ord( "&")	:
				$retStr	.=	"\&" ;
				break ;
			case	ord( "#")	:
				$retStr	.=	"\#" ;
				break ;
			case	ord( "{")	:
				$retStr	.=	"\{" ;
				break ;
			case	ord( "}")	:
				$retStr	.=	"\}" ;
				break ;
			case	ord( "\"")	:
//				$retStr	.=	"\\dq " ;
				break ;
			default	:
				$retStr	.=	$inStr[ $i] ;
				break ;
			}
		} else {
			switch ( ord( $inStr[ $i])) {
			case	ord("_")	:
				echo "DEBUG: Exception ... [" . $inStr . "] " . ord( $inStr[ $i]) . " \n" ;
				break ;
			case	132	:			// 1/4
				break ;
			case	133	:			// 1/4
				break ;
			case	139	:			// 1/4
				$retStr	.=	"<" ;
				break ;
			case	146	:			// 1/4
				$retStr	.=	"\'" ;
				break ;
			case	147	:			// 1/4
				break ;
			case	148	:			// 1/4
				$retStr	.=	"\"" ;
				break ;
			case	153	:			// 1/4
				$retStr	.=	"\trademark" ;
				break ;
			case	155	:			// 1/4
				$retStr	.=	">" ;
				break ;
			case	160	:			// 1/4
				$retStr	.=	"\trademark" ;
				break ;
			case	167	:			// 1/4
				$retStr	.=	"\S" ;
				break ;
			case	174	:			// 1/4
				$retStr	.=	"\copyright" ;
				break ;
			case	177	:			// 1/4
				$retStr	.=	"+/-" ;
				break ;
			case	178	:			// ^2
				$retStr	.=	"$^2$" ;
				break ;
			case	179	:			// ^3
				$retStr	.=	"$^3$" ;
				break ;
			case	181	:			// 1/4
				$retStr	.=	"$\mu$" ;
				break ;
			case	186	:			// 1/4
				$retStr	.=	"$^\circ$" ;
				break ;
			case	188	:			// 1/4
				$retStr	.=	"1/4" ;
				break ;
			case	196	:
				$retStr	.=	"\"{A}" ;
				break ;
			case	233	:
				$retStr	.=	"\`{e}" ;
				break ;
			case	174	:
				$retStr	.=	"\copyright" ;
				break ;
			case	176	:			// grd Kringel
			case	180	:			// '
			case	214	:			// Oe
			case	216	:			// durchnschnitts symbol O/
			case	220	:			// Ue
			case	223	:			// sz
			case	224	:			// a`
			case	225	:			// a'
			case	228	:			// ae
			case	246	:			// oe
			case	248	:			// oe
			case	252	:			// ue
				$retStr	.=	$inStr[ $i] ;
				break ;
			case	256	:			// ????
			case	150	:			// ????
				break ;
			case	129	:
			default	:
				echo "[" . $inStr . "] \n" ;
				echo "OOOORD = " . ord( $inStr[ $i]) . "'" . $inStr[$i] . "' \n"  ;
//				die() ;
				break ;
			}
		}
	}
//	echo "[" . $inStr . "] IIII \n[" . $retStr . "] \n" ;
	return $retStr ;
}

/**
 *	Funktion:	xmlToTex( $_text)
 */
function	xmlToTex( $_text) {

	global	$webPrefix ;

	$buffer	=	"" ;

	if ( strlen( $_text) == 0) {
		return $buffer ;
	}
	
	$xml	=	new XMLReader() ;
	$xml->XML( iconv( 'UTF-8', 'windows-1252', $_text)) ;
	while ( $xml->read()) {
		switch ( $xml->nodeType) {
		case	1	:			// start element
			if ( strcmp( $xml->name, "div") == 0) {
				$buffer	.=	"{\n" ;
			} else if ( strcmp( $xml->name, "ul") == 0) {
				$buffer	.=	"\\begin{itemize}\n" ;
			} else if ( strcmp( $xml->name, "li") == 0) {
				$buffer	.=	"\\item " ;
			} else if ( strcmp( $xml->name, "b") == 0) {
//				$buffer	.=	"\\emph{" ;
				$buffer	.=	"{\\sf\\bfseries " ;
			} else if ( strcmp( $xml->name, "br") == 0) {
				$buffer	.=	"\\\\" ;
			} else if ( strcmp( $xml->name, "a") == 0) {
				$attr	=	$xml->getAttribute( "href") ;
				if ( strncmp( $attr, "http:", 5) == 0) {
					$buffer	.=	mTS( "(siehe: " . $attr . ") ") ;
				} else {
					$buffer	.=	mTS( "(siehe: " . $webPrefix . $attr . ") ") ;
				}
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
			}
		case	16	:			// end element
			break ;
		}
//echo $buffer . "<br />" ;
	}
	return $buffer ;
}

function	xmlToPlain( $_text) {

	global	$webPrefix ;

	$buffer	=	"" ;

	if ( strlen( $_text) == 0) {
		return $buffer ;
	}
	
	$xml	=	new XMLReader() ;
	$xml->XML( iconv( 'UTF-8', 'windows-1252', $_text)) ;
	$inA	=	false ;
	$justStarted	=	false ;
	while ( $xml->read()) {
		switch ( $xml->nodeType) {
		case	1	:			// start element
			if ( strcmp( $xml->name, "div") == 0) {
				$justStarted	=	true ;
			} else if ( strcmp( $xml->name, "ul") == 0) {
			} else if ( strcmp( $xml->name, "li") == 0) {
				$buffer	.=	"- " ;
			} else if ( strcmp( $xml->name, "b") == 0) {
			} else if ( strcmp( $xml->name, "br") == 0) {
				$buffer	.=	"\n" ;
			} else if ( strcmp( $xml->name, "a") == 0) {
				$attr	=	$xml->getAttribute( "href") ;
				if ( strncmp( $attr, "http:", 5) == 0) {
					$buffer	.=	mTS( $attr . " ") ;
				} else {
					$buffer	.=	mTS( $webPrefix . $attr . " ") ;
				}
				$inA	=	true ;
			}
			break ;
		case	3	:			// text node
			if ( ! $inA ) {					// wenn wir in einem A Tag sind KEINE Text ausgeben
				if ( $justStarted) {
					$buffer	.=	ltrim( $xml->value) ;
				} else {
					$buffer	.=	trim( $xml->value, "\n") ;
				}
			}
			$justStarted	=	false ;
			break ;
		case	14	:			// whitespace node
			if ( ! $inA ) {
				if ( $justStarted) {
					$buffer	.=	ltrim( $xml->value) ;
				} else {
					$buffer	.=	trim( $xml->value, "\n") ;
				}
			}
			$justStarted	=	false ;
			break ;
		case	15	:			// end element
			if ( strcmp( $xml->name, "div") == 0) {
			} else if ( strcmp( $xml->name, "ul") == 0) {
				$buffer	.=	"" ;
			} else if ( strcmp( $xml->name, "li") == 0) {
				$buffer	.=	"\n" ;
			} else if ( strcmp( $xml->name, "p") == 0) {
				$buffer	.=	"\n" ;
			} else if ( strcmp( $xml->name, "b") == 0) {
			} else if ( strcmp( $xml->name, "br") == 0) {
				$buffer	.=	"\n" ;
			} else if ( strcmp( $xml->name, "a") == 0) {
				$inA	=	false ;
			}
		case	16	:			// end element
			break ;
		}
	}
	return $buffer ;
}

?>
