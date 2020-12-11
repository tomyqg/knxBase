<?php

//
// Function:	makeHalfPhonetic(...)
//

class	Phonetics	{
	static	function	makeHalfPhonetic( $inStr) {
		FDbg::begin( 1, "Phonetics.php", "Phonetics", "makeHalfPhonetic( '$inStr')") ;
	
		$imStr	=	"" ;
		for ( $i=0 ; $i<strlen( $inStr) ; $i++) {
	
			$mychar	=	$inStr[ $i] ;
	
			if ( $mychar >= "A" && $mychar <= "Z") {
				$imStr	.=	chr( ord("a") + ord( $mychar) - ord( "A")) ;
			} else if ( $mychar >= "a" && $mychar <= "z") {
				$imStr	.=	$mychar ;
			} else if ( $mychar >= "0" && $mychar <= "9") {
				$imStr	.=	$mychar ;
			} else if ( $mychar == " ") {
				$imStr	.=	"%" ;
			}
		
		}
		FDbg::end( 1, "Phonetics.php", "Phonetics", "makeHalfPhonetic( '$inStr')", $imStr) ;
		return $imStr ;
	}
	
	//
	// Function:	makePhonetic(...)
	//
	
	static	function	makePhonetic( $inStr) {
		FDbg::begin( 1, "Phonetics.php", "Phonetics", "makePhonetic( '$inStr')") ;
			
		$imStr	=	"" ;
		for ( $i=0 ; $i<strlen( $inStr) ; $i++) {
	
			$mychar	=	$inStr[ $i] ;
	
			if ( $mychar >= "A" && $mychar <= "Z") {
				$imStr	.=	chr( ord("a") + ord( $mychar) - ord( "A")) ;
			} else if ( $mychar >= "a" && $mychar <= "z") {
				$imStr	.=	$mychar ;
			} else if ( $mychar >= "0" && $mychar <= "9") {
				$imStr	.=	$mychar ;
			} else if ( $mychar == " ") {
				$imStr	.=	"%" ;
			} else {
				switch ( ord( $mychar)) {
				case	196	:			// Ae
					$imStr	.=	"a" ;
					break ;
				case	214	:			// Oe
					$imStr	.=	"o" ;
					break ;
				case	220	:			// Ue
					$imStr	.=	"u" ;
					break ;
				case	223	:			// sz
					$imStr	.=	"s" ;
					break ;
				case	228	:			// ae
					$imStr	.=	"a" ;
					break ;
				case	246	:			// oe
					$imStr	.=	"o" ;
					break ;
				case	252	:			// ue
					$imStr	.=	"u" ;
					break ;
				}
			}
		
		}
	
		$imStr	.=	" " ;
		$imStr	.=	" " ;
		$retStr	=	"" ;
		for ( $i=0 ; $i<strlen( $imStr)-2 ; $i++) {
	
			$mychar	=	$imStr[ $i] ;
	
			if ( ord( $imStr[ $i]) > ord("9") && $imStr[ $i] == $imStr[ $i+1] && $imStr[ $i+1] == $imStr[ $i+2]) {
	
				$retStr	.=	$imStr[ $i] ;
				$i++ ;
				$i++ ;
			} else if ( ord( $imStr[ $i]) > ord("9") && $imStr[ $i] == $imStr[ $i+1]) {
				$retStr	.=	$imStr[ $i] ;
				$i++ ;
			} else if ( $imStr[ $i] == "a" && $imStr[ $i+1] == "e") {
				$retStr	.=	"a" ;
				$i++ ;
			} else if ( $imStr[ $i] == "a" && $imStr[ $i+1] == "i") {
				$retStr	.=	"ei" ;
				$i++ ;
			} else if ( $imStr[ $i] == "a" && $imStr[ $i+1] == "y") {
				$retStr	.=	"ei" ;
				$i++ ;
			} else if ( $imStr[ $i] == "c" && $imStr[ $i+1] == "k") {
				$retStr	.=	"k" ;
				$i++ ;
			} else if ( $imStr[ $i] == "o" && $imStr[ $i+1] == "e") {
				$retStr	.=	"o" ;
				$i++ ;
			} else if ( $imStr[ $i] == "u" && $imStr[ $i+1] == "e") {
				$retStr	.=	"u" ;
				$i++ ;
			} else if ( $imStr[ $i] == "p" && $imStr[ $i+1] == "h") {
				$retStr	.=	"f" ;
				$i++ ;
			} else if ( $imStr[ $i] == "t" && $imStr[ $i+1] == "h") {
				$retStr	.=	"t" ;
				$i++ ;
			} else if ( $imStr[ $i] == "t" && $imStr[ $i+1] == "z") {
				$retStr	.=	"z" ;
				$i++ ;
			} else if ( $imStr[ $i] == "i" && $imStr[ $i+1] == "h") {
				$retStr	.=	"i" ;
				$i++ ;
			} else if ( $imStr[ $i] == "i" && $imStr[ $i+1] == "e") {
				$retStr	.=	"i" ;
				$i++ ;
			} else if ( $imStr[ $i] == "s" && $imStr[ $i+1] == "s") {
				$retStr	.=	"s" ;
				$i++ ;
			} else {
				$retStr	.=	$mychar ;
			}
		}
		FDbg::end( 1, "Phonetics.php", "Phonetics", "makePhonetic( '$inStr')", $retStr) ;
		return $retStr ;
	}
	
	//
	// Function:	makePhoneticForDb(...)
	//
	
	static	function	makePhoneticForDb( $inStr) {
		FDbg::begin( 1, "Phonetics.php", "Phonetics", "makePhoneticForDb( '$inStr')") ;
		$imStr	=	"" ;
		for ( $i=0 ; $i<strlen( $inStr) ; $i++) {
	
			$mychar	=	$inStr[ $i] ;
	
			if ( $mychar >= "A" && $mychar <= "Z") {
				$imStr	.=	chr( ord("a") + ord( $mychar) - ord( "A")) ;
			} else if ( $mychar >= "a" && $mychar <= "z") {
				$imStr	.=	$mychar ;
			} else if ( $mychar >= "0" && $mychar <= "9") {
				$imStr	.=	$mychar ;
			} else if ( $mychar == " ") {
			} else {
				switch ( ord( $mychar)) {
				case	196	:			// Ae
					$imStr	.=	"a" ;
					break ;
				case	214	:			// Oe
					$imStr	.=	"o" ;
					break ;
				case	220	:			// Ue
					$imStr	.=	"u" ;
					break ;
				case	223	:			// sz
					$imStr	.=	"s" ;
					break ;
				case	228	:			// ae
					$imStr	.=	"a" ;
					break ;
				case	246	:			// oe
					$imStr	.=	"o" ;
					break ;
				case	252	:			// ue
					$imStr	.=	"u" ;
					break ;
				}
			}
		
		}
	
		$imStr	.=	" " ;
		$imStr	.=	" " ;
		$retStr	=	"" ;
		for ( $i=0 ; $i<strlen( $imStr)-2 ; $i++) {
	
			$mychar	=	$imStr[ $i] ;
	
			if ( ord( $imStr[ $i]) > ord("9") && $imStr[ $i] == $imStr[ $i+1] && $imStr[ $i+1] == $imStr[ $i+2]) {
	
				$retStr	.=	$imStr[ $i] ;
				$i++ ;
				$i++ ;
			} else if ( ord( $imStr[ $i]) > ord("9") && $imStr[ $i] == $imStr[ $i+1]) {
				$retStr	.=	$imStr[ $i] ;
				$i++ ;
			} else if ( $imStr[ $i] == "a" && $imStr[ $i+1] == "e") {
				$retStr	.=	"a" ;
				$i++ ;
			} else if ( $imStr[ $i] == "a" && $imStr[ $i+1] == "i") {
				$retStr	.=	"ei" ;
				$i++ ;
			} else if ( $imStr[ $i] == "a" && $imStr[ $i+1] == "y") {
				$retStr	.=	"ei" ;
				$i++ ;
			} else if ( $imStr[ $i] == "c" && $imStr[ $i+1] == "k") {
				$retStr	.=	"k" ;
				$i++ ;
			} else if ( $imStr[ $i] == "o" && $imStr[ $i+1] == "e") {
				$retStr	.=	"o" ;
				$i++ ;
			} else if ( $imStr[ $i] == "u" && $imStr[ $i+1] == "e") {
				$retStr	.=	"u" ;
				$i++ ;
			} else if ( $imStr[ $i] == "p" && $imStr[ $i+1] == "h") {
				$retStr	.=	"f" ;
				$i++ ;
			} else if ( $imStr[ $i] == "t" && $imStr[ $i+1] == "h") {
				$retStr	.=	"t" ;
				$i++ ;
			} else if ( $imStr[ $i] == "t" && $imStr[ $i+1] == "z") {
				$retStr	.=	"z" ;
				$i++ ;
			} else if ( $imStr[ $i] == "i" && $imStr[ $i+1] == "h") {
				$retStr	.=	"i" ;
				$i++ ;
			} else if ( $imStr[ $i] == "i" && $imStr[ $i+1] == "e") {
				$retStr	.=	"i" ;
				$i++ ;
			} else if ( $imStr[ $i] == "s" && $imStr[ $i+1] == "s") {
				$retStr	.=	"s" ;
				$i++ ;
			} else {
				$retStr	.=	$mychar ;
			}
		}
		FDbg::end( 1, "Phonetics.php", "Phonetics", "makePhoneticForDb( '$inStr')", $retStr) ;
		return $retStr ;
	}
}

?>
