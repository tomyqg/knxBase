<?php

/**
 * option.php Definition of general options on application level
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wwsbe-app
 */
/**
 * Opt - Base class to deal with general options
 *
 * not be modified.
 *
 * @package wwsbe-app
 * @subpackage options
 */
class	OptionGroup extends FDbObject	{
	public	static	$myOption ;
	/**
	 *
	 */
	function	__construct( $_myId=-1) {
		parent::__construct( "OptionGroup", "OptionGroupName") ;
		if ( $_myId >= 0) {
			$this->setId( $_myId) ;
		}
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "OptionGroup.php", "OptionGroup", "add( '$_key', $_id, '$_val')") ;
		FDbg::end( 1, "OptionGroup.php", "OptionGroup", "add( '$_key', $_id, '$_val')") ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "OptionGroup.php", "OptionGroup", "upd( '$_key', $_id, '$_val')") ;
		FDbg::end( 1, "OptionGroup.php", "OptionGroup", "upd( '$_key', $_id, '$_val')") ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "OptionGroup.php::OptionGroup::del(...)") ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
				$_POST['_step']	=	$_id ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$filter	=	"( C.OptionGroupName like '%".$_POST["Search"]."%' ) " ;
			$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
			$myObj->addCol( "OptionGroupName", "var") ;
			$reply->replyData	=	$myObj->tableFromDb( " ",
									" ",
									$filter,
									"ORDER BY C.OptionGroupName ASC ",
									"OptionGroup",
									"OptionGroup",
									"C.OptionGroupName ") ;
			break ;
		case	"Option"	:
			$filter	=	"( C.OptionName = '".$this->OptionGroupName."' ) " ;
				$tmpObj	=	new $objName() ;
				$tmpObj->setId( $_id) ;
				$reply->replyData	=	$tmpObj->tableFromDb( "", "", "C.OptionName = '".$this->OptionGroupName."' ", "ORDER BY OptionName, `Key`, `Value` ") ;
				break ;
		}
		return $reply ;
	}
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @param string $reply
	 * @return Ambigous <string, Reply>
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "OptionGroup.php", "OptionGroup", "getDepAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"Option"	:
			$myOption	=	new Option() ;
			if ( $_id == -1) {
				$myOption->OptionName	=	$this->OptionGroupName ;
			} else {
				$myOption->setId( $_id) ;
			}
			$reply	=	$myOption->getAsXML( $_key, $_id, $_val, $_reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "OptionGroup.php", "OptionGroup", "addDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "Option") {
			$myOption	=	new Option() ;
			$myOption->OptionName	=	$this->OptionGroupName ;
			$myOption->getFromPostL() ;
			$myOption->storeInDb() ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "OptionGroup.php", "OptionGroup", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "OptionGroup.php", "OptionGroup", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			$myOption	=	new Option() ;
			$myOption->setId( $_id) ;
			$myOption->getFromPostL() ;
			$myOption->updateInDb() ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::delDep()
	 */
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "OptionGroup.php", "OptionGroup", "delDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch( $_val) {
		default	:
			$myOption	=	new Option() ;
			$myOption->Id	=	$_id ;
			$myOption->removeFromDb() ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
}

?>
