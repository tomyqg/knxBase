<?php
/**
 * ValveFam6.php
 * =============
 * 
 *  Valve of Family type 6 (deutsch: Knickventil).
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package ValveCalc
 */
/**
 * Valve - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package ValveCalc
 * @subpackage Classes
 */
class	Valve_Type3	extends	Valve	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "Valve", "ValveId") ;
	}
	/**
	 * 
	 */
	function	getImage( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Calculation.php", "Calculation", "getList( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyMediaType	=	Reply::mediaImgPng ;
//		$reply->replyMediaType	=	Reply::mediaImgJpg ;
		/**
		 * draw the grid
		 */
		$im			=	imagecreate( 300, 200);
		$bgColor	=	imagecolorallocate( $im, 255, 255, 255) ;
		$gridColor	=	imagecolorallocate ( $im, 0, 0, 0) ;
		/**
		 * vertical lines
		 */
		for ( $i=0 ; $i<=10 ; $i++) {
			imageline( $im, 25+$i*25, 25, 25+$i*25, 175, $gridColor) ;
			imagestring( $im, 5, 20+$i*25, 180, $i, $gridColor);
		}
		for ( $i=0 ; $i<=10 ; $i++) {
			imageline( $im, 25, 25+$i*15, 275, 25+$i*15, $gridColor) ;
			imagestring( $im, 5, 0, 165-$i*15, $i, $gridColor);
		}
		imagestring( $im, 5, 0, 0, "Valve_XType3", $gridColor);
//		imagestring( $im, 5, 0, 15, $this->ValveId . "/" . time(), $gridColor);
		// wir erstellen ein Wasserzeichen mit GD
		$stamp = imagecreatetruecolor(100, 70);
		imagefilledrectangle($stamp, 0, 0, 99, 69, 0x0000FF);
		imagefilledrectangle($stamp, 9, 9, 90, 60, 0xFFFFFF);
		imagestring($stamp, 5, 20, 20, 'Hellmig EDV', 0x0000FF);
		imagestring($stamp, 3, 20, 40, '(c) 2014', 0x0000FF);
		
		// RŠnder setzen, Dimensionen ermitteln
		$marge_right = 10;
		$marge_bottom = 10;
		$sx = imagesx($stamp);
		$sy = imagesy($stamp);
		/**
		 * get the table with the adjustment points
		 */
		$myPoints	=	new ValveParameter() ;
		$myPoints->setIterCond( "ValveId = '" . $this->ValveId . "' ") ;
		$myPoints->setIterOrder( "ORDER BY TuningPoint ") ;
		$il0	=	0 ;
		foreach ( $myPoints as $key => $obj) {
			if ( $il0 > 0) {
				$dX	=	$lastX - $obj->X ;
				$dY	=	$lastY - $obj->Y ;
				FDbg::trace( 2, "Valve_Type3.php", "Valve_Type3", "drawing ...", "\n"
					.	"lastX := $lastX, \n"
					.	"lastY := $lastY, \n"
					.	"    X := " . $obj->X . " \n"
					.	"    Y := " . $obj->Y . "") ;
				imageline( $im, 25 + $lastX * 25, 175 - $lastY * 15, 25 + $obj->X * 25, 175 - $obj->Y * 15, $gridColor) ;
			}
			$lastX	=	$obj->X ;
			$lastY	=	$obj->Y ;
			$il0++ ;
		}
		/**
		 *  Wasserzeichen mit einer Transparenz von 50% Ÿber das Foto legen
		 */
		imagecopymerge($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp), 50);
		$reply->gdImage	=	$im ;
		FDbg::end() ;
		return $reply ;
	}
}
Valve::register( "Valve_Type3") ;
?>
