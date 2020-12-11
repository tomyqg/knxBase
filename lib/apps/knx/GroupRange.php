<?php
/**
 * GroupAddressRange
 *
 * GroupAddressRange is a more theoretical class which is needed for import of ETS4/ETS5 generated grouop data.
 * Ths class as such serves really no purpose - to my understanding - but is needed in this software architecture
 * to facilitate the import from an ETS .xml file.
 *
 * @author		Karl-Heinz Welter <khwelter@icloud.com>
 * @version		0.1
 * @package		GroupAddress
 * @subpackage	Client
 * @filesource
 */
/**
 * Client - Base Class
 *
 */
class	GroupRange	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myId="") {
		parent::__construct( "GroupAddress", "Id", "def") ;
		$this->Name			=	"" ;
		$this->Description	=	"" ;
		$this->RangeStart	=	-1 ;
		$this->RangeEnd		=	-1 ;
		$this->Unfiltered	=	0 ;
	}
	function	storeInDb( $_exec=true) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $this->Unfiltered == "true")
			$this->Unfiltered	=	1 ;
		if ( $this->RangeStart >= 0) {
			if ( $this->RangeStart == 1)
				$this->RangeStart	=	0 ;
			$size	=	intval( $this->RangeEnd) - intval( $this->RangeStart)	+ 1 ;
			$top	=	intval( floor( $this->RangeStart / 2048)) ;
			$middle	=	intval( floor( $this->RangeStart - $top * 2048) / 256) ;
			if ( $size == 256) {
				$this->Address	=	sprintf( "%d/%d", $top, $middle) ;
			} else if ( $size == 2048) {
				$this->Address	=	sprintf( "%d", $top) ;
			}
		}
		$parts	=	explode( "/", $this->Address) ;
		if ( count( $parts) == 1) {
			$this->TopGroup	=	intval( $parts[0]) ;
			$this->MiddleGroup	=	null ;
			$this->Object	=	null ;
			$this->GroupAddressDec	=	$this->TopGroup * 2048 ;
		} else if ( count( $parts) == 2) {
			$this->TopGroup	=	intval( $parts[0]) ;
			$this->MiddleGroup	=	intval( $parts[1]) ;
			$this->Object	=	null ;
			$this->GroupAddressDec	=	$this->TopGroup * 2048
									+	$this->MiddleGroup * 256 ;
		} else {
			$this->TopGroup	=	intval( $parts[0]) ;
			$this->MiddleGroup	=	intval( $parts[1]) ;
			$this->Object	=	intval( $parts[2]) ;
			$this->GroupAddressDec	=	$this->TopGroup * 2048
									+	$this->MiddleGroup * 256
									+	$this->Object ;
		}
		parent::storeInDb( $_exec) ;
		FDbg::end() ;
	}
}
?>
