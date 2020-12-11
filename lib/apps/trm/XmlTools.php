<?php

//
// Funktion:	mTS( str)
//
// Diese Funktion übersetzt die Zeichen in einer Zeichenkette von ISO-8859-1 nach TeX
//

function	xmlToPlain( $_text) {
	$myConfig	=	EISSCoreObject::__getConfig() ;
	$buffer	=	"" ;

	if ( strlen( $_text) == 0) {
		return $buffer ;
	}
	
	$xml	=	new XMLReader() ;
	$xml->XML( iconv( "ISO-8859-1", "UTF-8", $_text)) ;
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
					$buffer	.=	mTS( $myConfig->url->fullShop . $attr . " ") ;
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
