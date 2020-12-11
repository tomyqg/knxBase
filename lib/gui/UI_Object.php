<?php
/**
 * UI_Object - Application Object
 *
 * Base class for all objects which have
 *
 * @author [wimteccgen] Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcCoreSubSystem
 * @filesource
 */
/**
 * UI_Object - Application Object
 *
 * Automatisch generierter Code. �nderungen ausschliesslich
 * �ber die entsp. <Basisklasse>.xml Datei !!!
 *
 * @package wtcCoreSubSystem
 * @subpackage UI_Object
 */
abstract	class	UI_Object	extends	FDbObject	{
	/**
	 *
	 */
	public	static	$dbAlias	=	"sys" ;
	function	__construct( $_className, $_keyCol) {
		parent::__construct( $_className, $_keyCol, self::$dbAlias) ;
	}
	/**
	 * methods: add/upd/copy/del
	 */
	/**
	 * (non-PHPdoc)
	 * @see DbObject::addEmpty()
	 */
	function	addEmpty( $_key="", $_id=-1, $_val="") {
		$this->defaultDates() ;		// set all dates to TODAY
		$this->clearRem() ;			// clear all remarks
		$this->storeInDb() ;
		return $this->getXMLString() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see DbObject::add()
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		$this->defaultDates() ;		// set all dates to TODAY
		$this->clearRem() ;
		$this->getFromPost() ;
		$this->storeInDb() ;
		return $this->getXMLString() ;
	}
	/**
	 * upd
	 * update the current object in the database
	 * if the object has a LockState attribute and this is set to true, no update will be performed and an exception is thrown
	 * (non-PHPdoc)
	 * @see DbObject::upd()
	 */
	function	upd( $_key="", $_id=-1, $_val="", $reply=null) {
		$this->getFromPost() ;
		$this->updateInDb() ;
		$this->reload() ;		// reload; dependent objects might have changed, e.g. customer/supplier/-contacs
		return $this->getXMLComplete() ;
	}
	/**
	 * del
	 * deletes the current object from the database
	 * if the object has a LockState attribute and this is set to true, no update will be performed and an exception is thrown
	 * (non-PHPdoc)
	 * @see DbObject::upd()
	 */
	function	del( $_key="", $_id=-1, $_val="", $reply=null) {
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->className ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." = '".$myKey."' ") ;
		return $this->getXMLString() ;
	}
	/**
	 * methods: addDep/updDep/copyDep/delDep
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		$objName	=	$_val ;
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
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
	}
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		$objName	=	$_val ;
		try {
			$tmpObj	=	new $objName() ;
			$myKeyCol	=	$this->keyCol ;
			$myKey	=	$this->$myKeyCol ;
			if ( $tmpObj->setId( $_id)) {
				$tmpObj->getFromPostL() ;
				$tmpObj->updateInDb() ;
			} else {
				$e	=	new Exception( 'UI_Object::updDep[Id='.$_id.'] is INVALID !') ;
				error_log( $e) ;
				throw $e ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
	}
	function	copyDep( $_key="", $_id=-1, $_val="") {
	}
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		$objName	=	$_val ;
		try {
			$tmpObj	=	new $objName() ;
			$myKeyCol	=	$this->keyCol ;
			$myKey	=	$this->$myKeyCol ;
			if ( $tmpObj->setId( $_id)) {
				$tmpObj->removeFromDb() ;
			} else {
				$e	=	new Exception( "UI_Object.php::UI_Object::delDep[Id='$_id'] dependent is INVALID !") ;
				error_log( $e) ;
				throw $e ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
	}
	function	moveDepUp( $_key, $_id, $_val) {
		return $this->_moveDep( $_key, $_id, $_val, -15) ;
	}
	function	moveDepDown( $_key, $_id, $_val) {
		return $this->_moveDep( $_key, $_id, $_val, 15) ;
	}
	function	setSingle( $_key, $_id, $_val) {
		error_log( "$_val") ;
		$depObj	=	explode( ".", $_val) ;
		$objName	=	$depObj[0] ;
		$attrName	=	$depObj[1] ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			try {
				$tmpObj	=	new $objName() ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				if ( $tmpObj->setId( $_id)) {
					$tmpObj->getFromPostL() ;
					$tmpObj->updateInDb() ;
					$tmpObj->reload() ;
				} else {
					$e	=	new Exception( 'UI_Object.php::UI_Object::setSingle::item[Id='.$_id.'] is INVALID !') ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( 'UI_Object::decDepDown: the object is locked!') ;
			error_log( $e) ;
			throw $e ;
		}
		$ret	=	"<" . $this->className . ">\n" .
					$this->getDepAsXML( $_key, $_id, $objName) .
					"</" . $this->className . ">\n" ;
		return $ret ;
	}
	/**
	 * methods: retrieve object
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
	}
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		return parent::getXML() ;
	}
	function	getDepAsXML( $_key="", $_id=-1, $_val="") {
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$_val ;
		$tmpObj	=	new $objName() ;
		$tmpObj->setId( $_id) ;
		return $tmpObj->getXML() ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param string		operation to perform (f50 = first 50, p50, ...)
	 * @param string	dependent object
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val) {
		$objName	=	$_val ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$tmpObj	=	new $objName() ;
		$_POST['_step']	=	$_id ;
		if ( isset( $tmpObj->SeqNo))
			$orderBy	=	"ORDER BY SeqNo " ;
		else
			$orderBy	=	" " ;
		$ret	=	$tmpObj->tableFromDb( " ", " ", "C." . $myKeyCol . " = '" . $myKey . "' ", $orderBy) ;
		return $ret ;
	}
	function	_moveDep( $_key, $_id, $_val, $_step) {
		$objName	=	$_val ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			try {
				$tmpObj	=	new $objName() ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				if ( $tmpObj->setId( $_id)) {
					$query	=	"UPDATE ".$objName." SET SeqNo = SeqNo + " . $_step .
													" WHERE ".$myKeyCol." = '".$myKey."' AND SeqNo = " . $tmpObj->SeqNo ;
					error_log( $query) ;
					$myResult	=	FDb::query( $query) ;
					$this->_renumber( $objName) ;
					$tmpObj->reload() ;
				} else {
					$e	=	new Exception( "UI_Object.php::UI_Object::moveDepDown::Pos[Id='.$_id.'] is INVALID !") ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( "UI_Object.php::UI_Object::moveDepDown: Das Objekt ist schreibgeschuetzt !") ;
			error_log( $e) ;
			throw $e ;
		}
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
	}
	function	_stepDepSingle( $_key, $_id, $_val, $_step) {
		FDbg::dumpL( 0x01000000, "UI_Object.php::UI_Object::_stepDepSingle( '$_key', $_id, '$_val', $_step): begin") ;
		$depObj	=	explode( ".", $_val) ;
		$objName	=	$depObj[0] ;
		$attrName	=	$depObj[1] ;
		FDbg::dumpL( 0x02000000, "UI_Object.php::UI_Object::_stepDepSingle(...): objName = $objName, attrName = $attrName") ;
		if ( $this->LockState == 0) {
			try {
				$tmpObj	=	new $objName() ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				if ( $tmpObj->setId( $_id)) {
					$query	=	"UPDATE ".$objName." SET " . $attrName . " = ( " . $attrName . " + " . $_step . ") " .
									"WHERE ".$myKeyCol." = '".$myKey."' AND SeqNo = " . $tmpObj->SeqNo ; // . " AND SubSeqNo = '" . $tmpObj->SubSeqNo . "' " ;
					error_log( $query) ;
					$myResult	=	FDb::query( $query) ;
					$tmpObj->reload() ;
				} else {
					$e	=	new Exception( "UI_Object.php::UI_Object::_stepDepSingle(...): pos[Id=$_id] is invalid!") ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( 'UI_Object.php::UI_Object::_stepDepSingle(...): the object is locked!') ;
			error_log( $e) ;
			throw $e ;
		}
		$ret	=	"<" . $this->className . ">\n" .
					$this->getDepAsXML( $_key, $_id, $objName) .
					"</" . $this->className . ">\n" ;
		FDbg::dumpL( 0x01000000, "UI_Object.php::UI_Object::incDepSingle( '$_key', $_id, '$_val', $_step): end") ;
		return $ret ;
	}
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $_itemObjName
	 * @param unknown_type $_step
	 */
	function	_renumber( $_itemObjName, $_step=10) {
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$myObj	=	new $_itemObjName ;
		$lastSeqNo	=	-1 ;
		$newSeqNo	=	 0 ;
		if ( ! isset( $myObj->SubSeqNo)) {
			$myObj->SubSeqNo	=	"" ;
			$query	=	"SELECT Id, SeqNo FROM ".$_itemObjName." WHERE ".$myKeyCol." = '".$myKey."' ORDER BY SeqNo" ;
		} else {
			$query	=	"SELECT Id, SeqNo FROM ".$_itemObjName." WHERE ".$myKeyCol." = '".$myKey."' ORDER BY SeqNo, SubSeqNo" ;
		}
		$myResult	=	FDb::query( $query) ;
		try {
			while ( $myRow = FDb::getRow( $myResult)) {
				$myObj->assignFromRow( $myRow) ;
				if ( $myObj->SeqNo == $lastSeqNo) {
					$myObj->SeqNo	=	$newSeqNo ;
				} else {
					$lastSeqNo	=	$myObj->SeqNo ;
					$newSeqNo	+=	$_step ;
					$myObj->SeqNo	=	$newSeqNo ;
				}
				$myObj->updateColInDb( "SeqNo") ;
			}
		} catch( Exception $e) {
			throw $e ;
		}
	}
}

?>
