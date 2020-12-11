<?php
/**
 * AppObject - Application Object for ERM ( Enterprise Resource Management )
 *
 * Base class for all objects which have
 *
 * @author [wimteccgen] Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcAppERPBaseObjects
 * @filesource
 */
/**
 * AppObject - Application Object
 *
 * @package wtcAppERPBaseObjects
 * @subpackage AppObjects
 */
abstract	class	AppObjectBC	extends	AppObject	{
	static	$const ;
	/**
	 *
	 */
	function	__construct( $_className, $_keyCol, $_db="def", $_tableName="") {
		parent::__construct( $_className, $_keyCol, $_db, $_tableName) ;
		if ( self::$const == null) {
			$myOption	=	new Option() ;
			self::$const	=	$myOption->getArray( "AppOption", "Symbol", "Key", "Class='".$this->className."'") ;
		}
	}
	/**
	 * methods: add/upd/copy/del
	 */
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::addEmpty()
	 */
	function	addEmpty( $_key="", $_id=-1, $_val="") {
		$this->newKey( 6) ;
		$this->defaultDates() ;		// set all dates to TODAY
		$this->clearRem() ;			// clear all remarks
		$this->updateInDb() ;
		return $this->getXMLString() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::add()
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <_reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$this->getFromPostL() ;
		$this->BatchNo	=	$_POST["BatchNo"] ;
		$this->defaultDates() ;		// set all dates to TODAY
		$this->storeInDb() ;
		FDbg::end() ;
		return $this->getXMLString( $_key, $_id, $_val, $reply) ;
	}
	/**
	 * update the current object in the database
	 * if the object has a LockState attribute and this is set to true, no update will be performed and an exception is thrown
	 * (non-PHPdoc)
	 * @see FDbObject::upd()
	 */
	function	upd( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			$this->getFromPostL() ;
			$this->updateInDb() ;
			$this->reload() ;		// reload; dependent objects might have changed, e.g. customer/supplier/-contacs
		} else {
			$e	=	new Exception( "AppObjectBC.php::AppObject::upd: The object [" . $this->$keyCol . "] is locked!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLString( $_key, $_id, $_val, $reply) ;
	}
	/**
	 * del
	 * deletes the current object from the database
	 * if the object has a LockState attribute and this is set to true, no update will be performed and an exception is thrown
	 * (non-PHPdoc)
	 * @see FDbObject::upd()
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			$myKeyCol	=	$this->keyCol ;
			$myKey	=	$this->$myKeyCol ;
			$objName	=	$this->className ;
		} else {
			$e	=	new Exception( "AppObjectBC.php::AppObject::del: The object [" . $this->$keyCol . "] is locked!") ;
			error_log( $e) ;
			throw $e ;
		}
		return $this->getXMLString( $_key, $_id, $_val, $reply) ;
	}
	/**
	 * methods: addDep/updDep/copyDep/delDep
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "AppObjectBC.php", "AppObjectBC", "addDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			try {
				$tmpObj	=	new $objName() ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				$tmpObj->getFromPostL() ;
				$tmpObj->$myKeyCol	=	$this->$myKeyCol ;
				$tmpObj->storeInDb() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( 'AppObjectBC.php::AppObject::addDep: the object is locked!') ;
			error_log( $e) ;
			throw $e ;
		}
		$this->getList( $_key, $_id, $objName, $reply) ;
		FDbg::end( 1, "AppObjectBC.php", "AppObjectBC", "addDep( '$_key', $_id, '$_val')") ;
		return $reply ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @throws Exception
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "AppObjectBC.php", "AppObjectBC", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			FDbg::trace( 2, "AppObjectBC.php", "AppObjectBC", "updDep( '$_key', $_id, '$_val')",
							"object is not locked") ;
			try {
				$tmpObj	=	new $objName() ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				if ( $tmpObj->setId( $_id)) {
					FDbg::trace( 2, "AppObjectBC.php", "AppObjectBC", "updDep( '$_key', $_id, '$_val')",
							"object is valid") ;
					$tmpObj->getFromPostL() ;
					$tmpObj->updateInDb() ;
				} else {
					$e	=	new Exception( 'AppObject::updDep[Id='.$_id.'] is INVALID !') ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( 'AppObject::updDep: object is write-protected (locked)!') ;
			error_log( $e) ;
			throw $e ;
		}
		$this->getList( $_key, $_id, $objName, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * @param string	$_key
	 * @param int	$_id
	 * @param mixed	$_val
	 * @throws Exception
	 */
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "AppObjectBC.php", "AppObjectBC", "delDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			try {
				$tmpObj	=	new $objName() ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				if ( $tmpObj->setId( $_id)) {
					$tmpObj->removeFromDb() ;
				} else {
					$e	=	new Exception( "AppObjectBC.php::AppObject::delDep[Id='$_id'] dependent is INVALID !") ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( "AppObjectBC.php::AppObject::delDep(...): the object is locked!") ;
			error_log( $e) ;
			throw $e ;
		}
		$this->getList( $_key, $_id, $objName, $reply) ;
		FDbg::end( 1, "AppObjectBC.php", "AppObjectBC", "delDep( '$_key', $_id, '$_val')") ;
		return $reply ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	copyDep( $_key="", $_id=-1, $_val="") {
	}
	/**
	 * methods: business logic
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyData	=	parent::getXML() ;
		return $reply ;
	}
	/**
	 * returns the dependent object of class $_val given by the id $_id
	 * for this base object. If the id is -1 the dependent objects ItemNo (or: PosNr)
	 * will be preset through a call to _getNextItem of the dependent object class.
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$_val ;
		$tmpObj	=	new $objName() ;
		if ( $_id == -1) {
			$tmpObj->$myKeyCol	=	$this->$myKeyCol ;
			$tmpObj->getNextItemNo() ;
		} else {
			$tmpObj->setId( $_id) ;
		}
		$reply->replyData	=	$tmpObj->getXML() ;
		return $reply ;
	}
}

?>
