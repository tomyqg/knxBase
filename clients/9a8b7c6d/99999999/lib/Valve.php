<?php
/**
 * Valve.php - Class definition
 *  Domain:
 *  	- administrative
 * 	Valve references:
 * 		- n/a
 *  Valve is referenced by:
 *  	- TrailerType
 *  	- Valve (twice!)
 *
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
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
class	Valve	extends	BCObject	{
	private	static	$families	=	array() ;
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
	 * (non-PHPdoc)
	 * @see AppObject::getXMLComplete()
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "Valve") ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch ( $_val) {
			default	:
				if ( $_id >= 0) {
					return parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
				} else {
					$newItem	=	new $_val ;
					$newItem->ValveId	=	$this->ValveId ;
					return $newItem->getAsXML() ;
				}
				break ;
		}
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::addDep()
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Valve.php", "Valve", "addDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch( $_val) {
			default	:
				parent::addDep( $_key, $_id, $_val, $reply) ;
				break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Valve.php", "Valve", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Valve.php", "Valve", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			parent::updDep( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::delDep()
	 */
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Valve.php", "Valve", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch( $_val) {
			default	:
				parent::delDep( $_key, $_id, $_val, $reply) ;
				break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$filter	=	"" ;
			$_searchCrit	=	"" ;
			$_idCrit	=	"" ;
			$_descriptionCrit	=	"" ;
			if ( isset( $_POST['_SSearch']))
				$_searchCrit	=	$_POST['_SSearch'] ;
			$filter	.=	"(" ;
			$filter	.=	"( ValveId like '%" . $_searchCrit . "%') " ;
			$filter	.=	")" ;
			/**
			 *
			 */
			$myObj	=	new FDbObject( "Valve", "Id", "def", "v_ValveSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [$filter]) ;
			$myQuery->addOrder( ["ValveId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"ValveParameter"	:
			$myObj	=	new FDbObject( "ValveParameter", "Id", "def", "v_ValveParameterSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["ValveId = '".$this->ValveId."'"]) ;
			$myQuery->addOrder( ["ToX"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	getImage( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Valve.php", "Valve", "getList( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyMediaType	=	Reply::mediaImgPng ;
		/**
		 *
		 */
		$objName	=	"Valve_Type" . strval( $this->ValveType) ;
		if ( isset( self::$families[$objName])) {
			$myValve	=	new $objName() ;
			$myValve->ValveId	=	$this->ValveId ;
			$myValve->getImage( $_key, $_id, $_val, $reply) ;
		} else {
			$im			=	imagecreate( 300, 200);
			$bgColor	=	imagecolorallocate( $im, 255, 255, 255) ;
			$gridColor	=	imagecolorallocate ( $im, 0, 0, 0) ;
			imagestring( $im, 5, 0, 0, "$objName is not yet implemented", $gridColor);
			imagestring( $im, 5, 0, 15, $this->ValveId . "/" . time(), $gridColor);
			// wir erstellen ein Wasserzeichen mit GD
			$stamp = imagecreatetruecolor(100, 70);
			imagefilledrectangle($stamp, 0, 0, 99, 69, 0x0000FF);
			imagefilledrectangle($stamp, 9, 9, 90, 60, 0xFFFFFF);
			imagestring($stamp, 5, 20, 20, 'Hellmig EDV', 0x0000FF);
			imagestring($stamp, 3, 20, 40, '(c) 2014', 0x0000FF);

			// R�nder setzen, Dimensionen ermitteln
			$marge_right = 10;
			$marge_bottom = 10;
			$sx = imagesx($stamp);
			$sy = imagesy($stamp);

			// Wasserzeichen mit einer Transparenz von 50% �ber das Foto legen
			imagecopymerge($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp), 50);
			$reply->gdImage	=	$im ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	calcGraph( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Valve.php", "Valve", "getList( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * register
	 * this function must be called by each plug-in module
	 * @param unknown_type $_name
	 */
	static	function	register( $_name) {
		self::$families[$_name]	=	$_name ;
	}
}
/**
 * load the valve family modules
 * @var unknown_type
 */
$myAppConfig	=	EISSCoreObject::__getAppConfig() ;
$fullPath	=	$myAppConfig->path->modules ;
FDbg::trace( 2, "Valve.php", "Valve", "*", "module path := '$fullPath'") ;
$myDir	=	opendir( $fullPath) ;
if ( $myDir) {
	while (( $file = readdir( $myDir)) !== false) {
		if ( strncmp( $file, "Valve_Type", 9) == 0 && strpos( $file, ".php") > 0 && strpos( $file, "dbInit") === false) {
			FDbg::trace( 2, "Valve.php", "Valve", "*", "adding file := '$file'") ;
			include( $fullPath . $file) ;
		}
	}
	closedir( $myDir);
}
?>
