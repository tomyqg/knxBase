<?php

function	mBCS( $inStr) {
	$retStr	=	"" ;
	for ( $i=0 ; $i<strlen( $inStr) ; $i++) {
		if ( ord( $inStr[ $i]) >= 48 && ord( $inStr[ $i]) <= 57) {
			$retStr	.=	$inStr[ $i] ;
		} else {
		}
	}
	return $retStr ;
}

function	mPSS( $inStr) {
	$retStr	=	"" ;
	for ( $i=0 ; $i<strlen( $inStr) ; $i++) {
		if ( ord( $inStr[ $i]) >= 32 && ord( $inStr[ $i]) < 127) {
			switch ( ord( $inStr[ $i])) {
			case	ord( "(")	:
				$retStr	.=	"\(" ;
				break ;
			case	ord( ")")	:
				$retStr	.=	"\)" ;
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
			case	167	:			// 1/4
				break ;
			case	177	:			// 1/4
				break ;
			case	188	:			// 1/4
				break ;
			case	196	:
				break ;
			case	233	:
				break ;
			case	176	:			// grd Kringel
				break ;
			case	180	:			// '
				break ;
			case	214	:			// Oe
				$retStr	.=	"Oe" ;
				break ;
			case	216	:			// durchnschnitts symbol O/
				break ;
			case	220	:			// Ue
				$retStr	.=	"Ue" ;
				break ;
			case	223	:			// sz
				$retStr	.=	"ss" ;
				break ;
			case	224	:			// a`
				break ;
			case	225	:			// a'
				break ;
			case	228	:			// ae
				$retStr	.=	"ae" ;
				break ;
			case	246	:			// oe
				$retStr	.=	"oe" ;
				break ;
			case	252	:			// ue
				$retStr	.=	"ue" ;
				break ;
				$retStr	.=	$inStr[ $i] ;
				break ;
			case	256	:			// ????
				break ;
			case	150	:			// ????
				break ;
			case	129	:
				break ;
			default	:
				echo "[" . $inStr . "] \n" ;
				echo "ORD = " . ord( $inStr[ $i]) . " \n"  ;
				die() ;
				break ;
			}
		}
	}
//	echo "[" . $inStr . "] IIII \n[" . $retStr . "] \n" ;
	return $retStr ;
}

?>
