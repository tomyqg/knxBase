<?php
/**
 * Copyright (c) 2015-2018 wimtecc, Karl-Heinz Welter
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 * AppObject - Application Object
 *
 * Base class for all objects which have
 *
 * @author [wimteccgen] Karl-Heinz Welter <khwelter@icloud.com>
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
abstract	class	AppObject	extends	AppObjectCore	{
	protected	$ProjectNo ;
	protected	$lastUpdateList	=	"" ;
	
	/**
	 * Creates an object of
	 * @param $_className
	 * @param $_keyCol
	 * @param string $_db
	 * @param string $_tableName
	 */
	function	__construct( $_className, $_keyCol, $_db="def", $_tableName="") {
		parent::__construct( $_className, $_keyCol, $_db, $_tableName) ;
	}
	
	/**
	 * @param array|string $_key
	 * @return bool
	 * @throws FException
	 */
	function	setKey( $_key) {
		parent::setKey( $_key) ;
		if ( $this->isValid()) {
			if ( isset( SysSession::$data)) {
				$keyName	=	$this->getClassName() . "LastKey" ;
				SysSession::$data[ $keyName]	=	$_key ;
			}
		}
		return $this->isValid() ;
	}

	/**
	 *
	 */
	function	getLastAsXML( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		if ( isset( SysSession::$data)) {
			$keyName	=	$this->className . "LastKey" ;
			if ( isset( SysSession::$data[ $keyName])) {
				parent::setKey( SysSession::$data[ $keyName]) ;
			}
		}
		$this->getXMLString( $_key, $_id, $_val, $_reply) ;
		return $_reply ;
	}

	/**
	 * (non-PHPdoc)
	 * @see FDbObject::add()
	 */
	function	create( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$this->newKey( 6) ;
		$this->defaultDates() ;		// set all dates to TODAY
		$this->clearRem() ;
		/**
		 * if this db-table has a RevNo entry, set its value to the first possible
		 * revision no.
		 */
		if ( isset( $this->RevNo)) {
			$myRevNo	=	new RevNo( "") ;
			$this->RevNo	=	$myRevNo->step() ;
		}
		$this->updateInDb() ;
	}

	/**
	 * (non-PHPdoc)
	 * @see FDbObject::add()
	 */
	function	add( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		if ( $_val != "") {
			$this->_addDep( $_key, $_id, $_val, $_reply) ;
		} else {
			$this->_add( $_key, $_id, $_val, $_reply) ;
		}
		return $_reply ;
	}

	/**
	 * update the current object in the database
	 * if the object has a LockState attribute and this is set to true, no update will be performed and an exception is thrown
	 * (non-PHPdoc)
	 * @see FDbObject::upd()
	 */
	function	upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			if ( $_val != "") {
				$this->_updDep( $_key, $_id, $_val, $_reply) ;
			} else {
				$this->_upd( $_key, $_id, $_val, $_reply) ;
			}
		} else {
			$e	=	new Exception( "AppObject.php::AppObject::upd: The object [" . $_key . "] is locked!") ;
			error_log( $e) ;
			throw $e ;
		}
		return $_reply ;
	}
	/**
	 * del
	 * deletes the current object from the database
	 * if the object has a LockState attribute and this is set to true, no update will be performed and an exception is thrown
	 * (non-PHPdoc)
	 * @see FDbObject::upd()
	 */
	function	del( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			if ( $_val != "") {
				$this->_delDep( $_key, $_id, $_val, $_reply) ;
			} else {
				$this->_del( $_key, $_id, $_val, $_reply) ;
			}
		} else {
			$e	=	new Exception( "AppObject.php::AppObject::del: The object [" . $this->$keyCol . "] is locked!") ;
			error_log( $e) ;
			throw $e ;
		}
		return $this->getXMLString( $_key, $_id, $_val, $_reply) ;
	}
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::add()
	 */
	function	_add( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$this->defaultDates() ;
		$this->clearRem() ;
		if ( isset( $this->RevNo)) {
			$myRevNo	=	new RevNo( "") ;
			$this->RevNo	=	$myRevNo->step() ;
		}
		$this->getFromPostL() ;
//		$this->ProjectNo	=	$this->newKey() ;
		$this->storeInDb() ;
		$this->getXMLString( $_key, $_id, $_val, $_reply) ;
		return $_reply ;
	}
	/**
	 * methods: addDep/updDep/copyDep/delDep
	 */
	function	_addDep( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
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
			$e	=	new Exception( 'AppObject.php::AppObject::addDep: the object is locked!') ;
			error_log( $e) ;
			throw $e ;
		}
		$this->getList( $_key, $_id, $objName, $_reply) ;
		return $_reply ;
	}

	/**
	 * update the current object in the database
	 * if the object has a LockState attribute and this is set to true, no update will be performed and an exception is thrown
	 * (non-PHPdoc)
	 * @see FDbObject::upd()
	 */
	function	_upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		if ( $_reply->replyingClass === __class__)
			$this->lastUpdateList	=	$this->getFromPostL() ;
		$this->updateInDb() ;
		$this->getXMLString( $_key, $_id, $_val, $_reply) ;
		return $_reply ;
	}

	/**
	 * updDep
	 *
	 * update an object which depends on 'this' object.
	 * when the $_val operator is empty we assume that the method is called from a survey of objects.
	 *
	 * @param string $_key	specifies the key of the owning object
	 * @param int $_id		specifies the id of the owning object
	 * @param mixed $_val	specifies the class and optionall the attribute in the form <class>[.[<attribute>]]
	 * @param $_reply		object where to store the result of the operation
	 * @throws Exception
	 * @return  Reply
	 */
	function	_updDep( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			try {
				$tmpObj	=	new $objName() ;
				$tmpObj->setId( $_id) ;
				if ( $tmpObj->setId( $_id)) {
					$tmpObj->getFromPostL() ;
					$tmpObj->updateInDb() ;
				} else {
					$e	=	new Exception( "AppObject.php::AppObject::delDep[Id='$_id'] dependent is INVALID !") ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( 'AppObject.php::AppObject::addDep: the object is locked!') ;
			error_log( $e) ;
			throw $e ;
		}
		$this->getList( $_key, $_id, $objName, $_reply) ;
		return $_reply ;
	}
	/**
	 * del
	 * deletes the current object from the database
	 * if the object has a LockState attribute and this is set to true, no update will be performed and an exception is thrown
	 * (non-PHPdoc)
	 * @see FDbObject::upd()
	 */
	function	_del( $_key="", $_id=-1, $_val="", $_reply=null) {
		$this->removeFromDb() ;
		return $this->getXMLString( $_key, $_id, $_val, $_reply) ;
	}
	/**
	 *
	 * @param string	$_key
	 * @param int	$_id
	 * @param mixed	$_val
	 * @throws Exception
	 * @return  Reply
	 */
	function	_delDep( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		try {
			$tmpObj	=	new $objName() ;
			$myKeyCol	=	$this->keyCol ;
			$myKey	=	$this->$myKeyCol ;
			if ( $tmpObj->setId( $_id)) {
				$tmpObj->removeFromDb() ;
			} else {
				$e	=	new Exception( "AppObject.php::AppObject::delDep[Id='$_id'] dependent is INVALID !") ;
				error_log( $e) ;
				throw $e ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		$this->getList( $_key, $_id, $objName, $_reply) ;
		return $_reply ;
	}
	/**
	 *
	 */
	function	moveDepUp( $_key, $_id, $_val) {
		$this->_moveDep( $_key, $_id, $_val, -15) ;
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
	}

	/**
	 *
	 */
	function	moveDepDown( $_key, $_id, $_val) {
		$this->_moveDep( $_key, $_id, $_val, 15) ;
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
	}

	/**
	 *
	 */
	function	decDep( $_key, $_id, $_val) {
		return $this->_stepDep( $_key, $_id, $_val, -1) ;
	}

	/**
	 *
	 */
	function	incDep( $_key, $_id, $_val) {
		return $this->_stepDep( $_key, $_id, $_val, 1) ;
	}

	/**
	 *
	 */
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
					if ( isset( $tmpObj->ItemNo)) {
						$query	=	"UPDATE ".$objName." SET ItemNo = ItemNo + " . $_step .
														" WHERE ".$myKeyCol." = '".$myKey."' AND ItemNo = " . $tmpObj->ItemNo ;
						error_log( $query) ;
						$myResult	=	FDb::query( $query) ;
						$this->_renumber( $objName) ;
						$tmpObj->reload() ;
					}
				} else {
					$e	=	new Exception( "AppObjectERM.php::AppObject::moveDepDown::Pos[Id='.$_id.'] is INVALID !") ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( "AppObjectERM.php::AppObject::moveDepDown: Das Objekt ist schreibgeschuetzt !") ;
			error_log( $e) ;
			throw $e ;
		}
	}

	/**
	 *
	 * Enter description here ...
	 * @param string	$_key
	 * @param int		$_id
	 * @param string	$_val
	 * @param int		$_step
	 * @throws Exception
	 * @return  Reply
	 */
	function	_stepDep( $_key, $_id, $_val, $_step) {
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
					$query	=	"UPDATE ".$objName." SET " . $attrName . " = ( " . $attrName . " + " . $_step . ") " .
									"WHERE ".$myKeyCol." = '".$myKey."' " ;
					if ( isset( $tmpObj->ItemNo))
						$query	.=	"AND ItemNo = " . $tmpObj->ItemNo ; // . " AND SubItemNo = '" . $tmpObj->SubItemNo . "' " ;
					error_log( $query) ;
					$myResult	=	FDb::query( $query) ;
					$tmpObj->reload() ;
					if ( isset( $tmpObj) && isset( $tmpObj->Preis) && isset( $tmpObj->Menge))
						$tmpObj->GesamtPreis	=	$tmpObj->Menge * $tmpObj->Preis ;
					$tmpObj->updateInDb() ;
				} else {
					$e	=	new Exception( "AppObjectERM.php::AppObject::_stepDep(...): pos[Id=$_id] is invalid!") ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( 'AppObjectERM.php::AppObject::_stepDep(...): the object is locked!') ;
			error_log( $e) ;
			throw $e ;
		}
		$ret	=	"<" . $this->className . ">\n" .
					$this->getDepAsXML( $_key, $_id, $objName) .
					"</" . $this->className . ">\n" ;
		return $ret ;
	}

	/**
	 *
	 * Enter description here ...
	 * @param string $_itemObjName
	 * @param integer $_step
	 * @throws  FException
	 */
	function	_renumber( $_itemObjName, $_step=10) {
		$myKeyCol	=	$this->keyCol ;
		/**
		 *
		 */
		$myItems	=	new $_itemObjName() ;
		$myItems->clearIterCond() ;
		$myItems->setIterCond( "$myKeyCol = '".$this->$myKeyCol."' ") ;
		$myItems->setIterOrder( [ "ItemNo", "SubItemNo"]) ;
		$lastItemNo =   -1 ;
		$lastNewItemNo	=	-1 ;
		$newItemNo  =   $_step ;
		try {
			foreach ( $myItems as $actItem) {
				if ( $actItem->ItemNo == $lastItemNo) {
					$actItem->ItemNo	=	$newItemNo ;
				} else {
					$lastItemNo	=	$actItem->ItemNo ;
					$newItemNo	+=	$_step ;
					$actItem->ItemNo	=	$newItemNo ;
				}
				$actItem->updateColInDb( "ItemNo") ;
			}
		} catch( FException $e) {
			throw $e ;
		}
	}

	/**
	 *
	 * Enter description here ...
	 * @param string $_itemObjName
	 * @param integer $_step
	 * @throws  Exception
	 */
	function	_renumberItem( $_itemObjName, $_step=10) {
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$myObj	=	new $_itemObjName ;
		$lastItemNo	=	-1 ;
		$newItemNo	=	 0 ;
		if ( ! isset( $myObj->SubItemNo)) {
			$myObj->SubItemNo	=	"" ;
			$query	=	"SELECT Id, ItemNo FROM ".$_itemObjName." WHERE ".$myKeyCol." = '".$myKey."' ORDER BY ItemNo" ;
		} else {
			$query	=	"SELECT Id, ItemNo FROM ".$_itemObjName." WHERE ".$myKeyCol." = '".$myKey."' ORDER BY ItemNo, SubItemNo" ;
		}
		$myResult	=	FDb::query( $query) ;
		try {
			while ( $myRow = FDb::getRow( $myResult)) {
				$myObj->assignFromRow( $myRow) ;
				if ( $myObj->ItemNo == $lastItemNo) {
					$myObj->ItemNo	=	$newItemNo ;
				} else {
					$lastItemNo	=	$myObj->ItemNo ;
					$newItemNo	+=	$_step ;
					$myObj->ItemNo	=	$newItemNo ;
				}
				$myObj->updateColInDb( "ItemNo") ;
			}
		} catch( Exception $e) {
			throw $e ;
		}
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
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @param null $_reply
	 * @return null|Reply
	 * @throws  Exception
	 */
	function	getInsertStatement( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$stmt	=	parent::storeInDb( false) ;
		$_reply->message	=	$stmt ;
		return $_reply ;
	}

	/**
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @param null $_reply
	 * @return null|Reply
	 * @throws  Exception
	 */
	function	getUpdateStatement( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$stmt	=	parent::updateInDb( false) ;
		$_reply->message	=	$stmt ;
		return $_reply ;
	}

	/**
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @param null $_reply
	 * @return null|Reply
	 * @throws  Exception
	 */
	function	setSingle( $_key="", $_id=-1, $_val="", $_reply=null) {
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
					$e	=	new Exception( 'AppObject.php::AppObject::setSingle::item[Id='.$_id.'] is INVALID !') ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( 'AppObject::decDepDown: the object is locked!') ;
			error_log( $e) ;
			throw $e ;
		}
		$ret	=	"<" . $this->className . ">\n" .
					$this->getDepAsXML( $_key, $_id, $objName) .
					"</" . $this->className . ">\n" ;
		return $ret ;
	}
	
	/**
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @param null $_reply
	 * @return null|Reply
	 */
	function	getImage( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$_reply->replyMediaType	=	Reply::mediaImgPng ;
		/**
		 *
		 */
		if ( strpos( $this->ImageReference, ".jpg") >= 0)
			$im			=	imagecreatefromjpeg( "../../Images/thumbs/" . $this->ImageReference) ;
		else if ( strpos( $this->ImageReference, ".png") >= 0)
			$im			=	imagecreatefrompng( "../../Images/thumbs/" . $this->ImageReference) ;
		$_reply->gdImage	=	$im ;
		return $_reply ;
	}
	
	/**
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 * @return string
	 */
	function	getDocListAsXML( $_key, $_id, $_val) {
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$fullPath	=	$this->path->Archive . $this->className . "/" ;
		$ret	=	DocList::getXMLComplete( $fullPath, $myKey, $this->url->Archive."$this->className/") ;
		return $ret ;
	}
	
	/**
	 * _assignAdmDomainId()
	 * @param string $_dbt
	 * @param string $_attr
	 * @param string $_cond
	 * @return    string    unique Id
	 * @throws Exception
	 */
	function	_assignUserDomainId( $_dbt="", $_attr="", $_cond="") {
		if ( $_dbt == "") {
			$_dbt	=	$this->className ;
			$_attr	=	$this->keyCol ;
			$_cond	=	"1 = 1 " ;
		}
		$query	=	"SELECT $_attr FROM $_dbt "
				.	"WHERE $_cond AND $_attr like '$this->UserDomain@$this->UserId/%' "
				.	"ORDER BY $_attr DESC LIMIT 1" ;
		error_log( $query) ;
		$row	=	FDb::queryRow( $query) ;
		$parts	=	explode( "/", $row[$_attr]) ;
		error_log( $row[$_attr]) ;
		if ( isset( $parts[1]))
			$this->$_attr	=	$this->UserDomain . "@" . $this->UserId . "/" . sprintf( "%08d", $parts[1] + 1) ;
		else
			$this->$_attr	=	$this->UserDomain . "@" . $this->UserId . "/" . sprintf( "%08d", 1) ;
		return $this->$_attr ;
	}
	
	/**
	 * _assignAdmDomainId()
	 * assigns and retrieves the next unique LeverLengthId for this LeverLength in the Admin domain
	 * @param string $_dbt
	 * @param string $_attr
	 * @param string $_cond
	 * @return    string    unique Id
	 * @throws Exception
	 */
	function	_assignAdmDomainId( $_dbt="", $_attr="", $_cond="") {
		if ( $_dbt == "") {
			$_dbt	=	$this->className ;
			$_attr	=	$this->keyCol ;
			$_cond	=	"1 = 1 " ;
		}
		$query	=	"SELECT $_attr FROM $_dbt "
				.	"WHERE $_cond AND $_attr like '$this->AdmDomain/%' "
				.	"ORDER BY $_attr DESC LIMIT 1" ;
		$row	=	FDb::queryRow( $query) ;
		$parts	=	explode( "/", $row[$_attr]) ;
		error_log( $row[$_attr]) ;
		if ( isset( $parts[1]))
			$this->$_attr	=	$this->AdmDomain . "/" . sprintf( "%08d", $parts[1] + 1) ;
		else
			$this->$_attr	=	$this->AdmDomain . "/" . sprintf( "%08d", 1) ;
		return $this->$_attr ;
	}
	
	/**
	 * methods: abstract catcher
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @return mixed
	 * @throws	FException
	 */
	function	approve( $_key="", $_id=-1, $_val="") {
		if ( isset( $this->RevNo)) {
			$myRev	=	new RevNo( $this->RevNo) ;
			$this->RevNo	=	$myRev->release() ;
			$this->updateColInDb( "RevNo") ;
		}
		return $this->getXMLComplete( $_key, $_id, $_val) ;
	}
	
	/**
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @return mixed
	 * @throws	FException
	 */
	function	newRev( $_key="", $_id=-1, $_val="") {
		if ( isset( $this->RevNo)) {
			$myRev	=	new RevNo( $this->RevNo) ;
			$this->RevNo	=	$myRev->step() ;
			$this->updateColInDb( "RevNo") ;
		}
		return $this->getXMLComplete( $_key, $_id, $_val) ;
	}
	
	/**
	 *
	 */
	function	getNextItemNo() {
	}
}

?>
