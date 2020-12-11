<?php

function	mES( $inStr) {
	$retStr	=	"" ;
	for ( $i=0 ; $i<strlen( $inStr) ; $i++) {
		if ( ord( $inStr[ $i]) >= 0 && ord( $inStr[ $i]) < 127) {
			switch ( ord( $inStr[ $i])) {
			case	ord( "\"")	:
				$retStr	.=	"\\\"" ;
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
			case	147	:			// 1/4
			case	148	:			// 1/4
				$retStr	.=	"*" ;
				break ;
			case	167	:			// 1/4
				$retStr	.=	"*" ;
				break ;
			case	174	:			// 1/4
				$retStr	.=	"*" ;
				break ;
			case	177	:			// 1/4
				$retStr	.=	"+/-" ;
				break ;
			case	188	:			// 1/4
				$retStr	.=	"1/4" ;
				break ;
			case	196	:
				$retStr	.=	"Ae" ;
				break ;
			case	233	:
				$retStr	.=	"" ;
				break ;
			case	176	:			// grd Kringel
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
			case	224	:			// a`
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
			case	256	:			// ????
			case	150	:			// ????
				break ;
			case	129	:
			default	:
				echo "[" . $inStr . "] \n" ;
				echo "OOOORD = " . ord( $inStr[ $i]) . " \n"  ;
				die() ;
				break ;
			}
		}
	}
//	echo "[" . $inStr . "] IIII \n[" . $retStr . "] \n" ;
	return $retStr ;
}

?>
