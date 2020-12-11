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
abstract	class	AppObjectImmo	extends	AppObject	{
	static	$const ;
	/**
	 *
	 */
	function	__construct( $_className, $_keyCol, $_db="def", $_tableName="") {
		parent::__construct( $_className, $_keyCol, $_db, $_tableName) ;
		if ( self::$const == null) {
//			$myOption	=	new Option() ;
//			self::$const	=	$myOption->getArray( "AppOption", "Symbol", "Key", "Class='".$this->className."'") ;
//			foreach ( self::$const as $key => $val) {
//				error_log( " Key...: " . $key . " --> " . $val) ;
//			}
//			error_log( "................: " . self::$const[ "conf"]) ;
		}
	}
	/**
	 * methods: add/upd/copy/del
	 */
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::addEmpty()
	 */
	function	addEmpty( $_key="", $_id=-1, $_val="", $reply = NULL) {
		$this->newKey( 6) ;
		$this->defaultDates() ;		// set all dates to TODAY
		$this->clearRem() ;			// clear all remarks
		$this->updateInDb() ;
		return $this->getXMLString() ;
	}
	/**
	 * update the current object in the database
	 * if the object has a LockState attribute and this is set to true, no update will be performed and an exception is thrown
	 * (non-PHPdoc)
	 * @see FDbObject::upd()
	 */
	function	upd( $_key="", $_id=-1, $_val="", $reply=null) {
		$action	=	false ;
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		if ( ! isset( $this->LockState)) {
			$action	=	true ;
		} else if ( $this->LockState == 0) {
			$action	=	true ;
		}
		if ( $action) {
			$this->getFromPostL() ;
			$this->updateInDb() ;
			$this->reload() ;		// reload; dependent objects might have changed, e.g. customer/supplier/-contacs
		} else {
			$e	=	new Exception( "AppObjectImmo.php::AppObject::upd: The object [" . $this->$keyCol . "] is locked!") ;
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
	function	del( $_key="", $_id=-1, $_val="", $reply = NULL) {
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			$myKeyCol	=	$this->keyCol ;
			$myKey	=	$this->$myKeyCol ;
			$objName	=	$this->className ;
		} else {
			$e	=	new Exception( "AppObjectImmo.php::AppObject::del: The object [" . $this->$keyCol . "] is locked!") ;
			error_log( $e) ;
			throw $e ;
		}
		return $this->getXMLString( $_key, $_id, $_val, $reply) ;
	}
	/**
	 * methods: addDep/updDep/copyDep/delDep
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "AppObjectImmo.php", "AppObjectImmo", "addDep( '$_key', $_id, '$_val')") ;
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
			$e	=	new Exception( 'AppObjectImmo.php::AppObject::addDep: the object is locked!') ;
			error_log( $e) ;
			throw $e ;
		}
		$this->getList( $_key, $_id, $objName, $reply) ;
		FDbg::end( 1, "AppObjectImmo.php", "AppObjectImmo", "addDep( '$_key', $_id, '$_val')") ;
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
		FDbg::begin( 1, "AppObjectImmo.php", "AppObjectImmo", "updDep( '$_key', $_id, '$_val')") ;
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
			FDbg::trace( 2, "AppObjectImmo.php", "AppObjectImmo", "updDep( '$_key', $_id, '$_val')",
							"object is not locked") ;
			try {
				$tmpObj	=	new $objName() ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				if ( $tmpObj->setId( $_id)) {
					FDbg::trace( 2, "AppObjectImmo.php", "AppObjectImmo", "updDep( '$_key', $_id, '$_val')",
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
		FDbg::begin( 1, "AppObjectImmo.php", "AppObjectImmo", "delDep( '$_key', $_id, '$_val')") ;
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
					$e	=	new Exception( "AppObjectImmo.php::AppObject::delDep[Id='$_id'] dependent is INVALID !") ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( "AppObjectImmo.php::AppObject::delDep(...): the object is locked!") ;
			error_log( $e) ;
			throw $e ;
		}
		$this->getList( $_key, $_id, $objName, $reply) ;
		FDbg::end( 1, "AppObjectImmo.php", "AppObjectImmo", "delDep( '$_key', $_id, '$_val')") ;
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
	function	addRem( $_key="", $_id=-1, $_val="") {
		$this->_addRem( $_val) ;
		return $this->getAttrXMLF( "Rem1") ;
	}
	function	updAnschreiben( $_key, $_id, $_val) {
		FDbg::dumpL( 0x01000000, "AppObjectImmo.php::AppObject::updAnschreiben( '$_key', $_id, '$_val'): begin") ;
		try {
			$this->Anschreiben	=	$_POST['_IAnschreiben'.$this->className] ;
			$this->updateColInDb( "Anschreiben") ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
		FDbg::dumpL( 0x01000000, "AppObjectImmo.php::AppObject::updAnschreiben( '$_key', $_id, '$_val'): end") ;
	}
	function	moveDepUp( $_key, $_id, $_val) {
		$this->_moveDep( $_key, $_id, $_val, -15) ;
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
			}
	function	moveDepDown( $_key, $_id, $_val) {
		$this->_moveDep( $_key, $_id, $_val, 15) ;
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
	}
	function	decDep( $_key, $_id, $_val) {
		FDbg::dumpL( 0x01000000, "AppObjectImmo.php::AppObject::incDep( '$_key', $_id, '$_val'): begin/end") ;
		return $this->_stepDep( $_key, $_id, $_val, -1) ;
	}
	function	incDep( $_key, $_id, $_val) {
		FDbg::dumpL( 0x01000000, "AppObjectImmo.php::AppObject::incDep( '$_key', $_id, '$_val'): begin/end") ;
		return $this->_stepDep( $_key, $_id, $_val, 1) ;
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
					$e	=	new Exception( 'AppObjectImmo.php::AppObject::setSingle::item[Id='.$_id.'] is INVALID !') ;
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
	 * methods: retrieve object
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
	}
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
	function	getDepAsJSON( $_key="", $_id=-1, $_val="") {
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
		return $tmpObj->getJSONComplete() ;
	}
	/**
	 *
	 * @param string	$_key
	 * @param string	operation to perform (f50 = first 50, p50, ...)
	 * @param string	dependent object
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val) {
		$objName	=	$_val ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$tmpObj	=	new $objName() ;
		$_POST['_step']	=	$_id ;
		if ( isset( $tmpObj->ItemNo))
			$orderBy	=	"ORDER BY ItemNo " ;
		else
			$orderBy	=	" " ;
		$ret	=	$tmpObj->tableFromDb( " ", " ", "C." . $myKeyCol . " = '" . $myKey . "' ", $orderBy) ;
		return $ret ;
	}
	function	getDocListAsXML( $_key, $_id, $_val) {
		FDbg::dumpL( 0x01000000, "AppObjectImmo.php::AppObject::getDocListAsXML( '$_key', $_id, '$_val'): begin") ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$fullPath	=	$this->path->Archive . $this->className . "/" ;
		$ret	=	DocList::getXMLComplete( $fullPath, $myKey, $this->url->Archive."$this->className/") ;
		FDbg::dumpL( 0x01000000, "AppObjectImmo.php::AppObject::getDocListAsXML( '$_key', $_id, '$_val'): end") ;
		return $ret ;
	}
	/**
	 * methods: internal
	 */
	function	_newFrom( $_srcObjClass, $_srcObjKey, $_trgObjKey="", $_where="", $_nsStart="000000", $_nsEnd="899999") {
		FDbg::begin( 1, "AppObjectImmo.php", "AppObjectImmo", "_newFrom(...)") ;
		/**
		 * create the (provisionary) PKdKomm and KdKomm for each distinct supplier
		 */
		try {
			$srcObj	=	new $_srcObjClass( $_srcObjKey) ;
			if ( $srcObj->isValid()) {
				$trgKeyCol	=	$this->keyCol ;
				if ( $_trgObjKey == "") {
					$this->newKey( 6, $_nsStart, $_nsEnd) ;		// get a new key
					$myTrgObjKey	=	$this->$trgKeyCol ;		// remember the key
				} else {
					$this->$trgKeyCol	=	$_trgObjKey ;
					$this->storeInDb() ;
					$myTrgObjKey	=	$this->$trgKeyCol ;		// remember the key
				}
				$this->copyFrom( $srcObj) ;			// copy all common attributes from <SrcObject> to <this> object
				$this->$trgKeyCol	=	$myTrgObjKey ;
				$this->Datum	=	$this->today() ;		// update the date to today
				$this->Status	=	0 ;
				$this->Rem	=	"" ;
				$this->Rem1	=	"" ;
				$this->_addRem( "from " . $_srcObjClass . ", key no. " . $_srcObjKey . "\n") ;
				$this->updateInDb() ;				// update data in db
				$this->reload() ;					// reload, thereby refresh supplier data (esp. language!)
				try {
					$this->_setTexte() ;				//
					$this->_setAnschreiben();
				} catch ( Exception $e) {
					$e	=	new exception( "AppObjectImmo.php::AppObject::_newFrom(...): error during _setTexte/_setAnschreiben") ;
					error_log( $e) ;
				}
				FDbg::trace( 1, FDbg::mdTrcInfo1, "AppObjectImmo.php", "AppObjectImmo", "_newFrom(...)", "preparing source item for copy") ;
				$srcKeyCol      =       $srcObj->keyCol ;
				$srcItemObjClass        =       $_srcObjClass . "Item" ;
				$srcItemObj     =       new $srcItemObjClass() ;
				$srcItemObj->$srcKeyCol =       $srcObj->$srcKeyCol ;
				$srcItemObj->setIterCond( "$srcKeyCol = '$_srcObjKey' " . $_where . " ") ;
				$srcItemObj->setIterOrder( "ItemNo, SubItemNo ") ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "AppObjectImmo.php", "AppObjectImmo", "_newFrom(...)", "preparing target item for copy") ;
				$trgItemObjClass        =       $this->className . "Item" ;
				$trgItemObj     =       new $trgItemObjClass() ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "AppObjectImmo.php", "AppObjectImmo", "_newFrom(...)", "starting to copy items") ;
				foreach( $srcItemObj as $ndx => $obj) {
					$trgItemObj->copyFrom( $obj) ;
					$trgItemObj->$trgKeyCol =       $this->$trgKeyCol ;
					$trgItemObj->storeInDb() ;
				}
			} else {
				FDbg::abort() ;
				throw new FException( __FILE__, __CLASS__, __METHOD__."( $_srcObjClass, $_srcObjKey, $_trgObjKey, $_where, $_nsStart, $_nsEnd): exception: ", $e) ;
			}
		} catch ( Exception $e) {
			FDbg::abort() ;
			throw new FException( __FILE__, __CLASS__, __METHOD__."( ...): failure: ", $e) ;
		}
		FDbg::end() ;
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
					if ( isset( $tmpObj->ItemNo)) {
						$query	=	"UPDATE ".$objName." SET ItemNo = ItemNo + " . $_step .
														" WHERE ".$myKeyCol." = '".$myKey."' AND ItemNo = " . $tmpObj->ItemNo ;
						error_log( $query) ;
						$myResult	=	FDb::query( $query) ;
						$this->_renumber( $objName) ;
						$tmpObj->reload() ;
					}
				} else {
					$e	=	new Exception( "AppObjectImmo.php::AppObject::moveDepDown::Pos[Id='.$_id.'] is INVALID !") ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( "AppObjectImmo.php::AppObject::moveDepDown: Das Objekt ist schreibgeschuetzt !") ;
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
	 */
	function	_stepDep( $_key, $_id, $_val, $_step) {
		FDbg::dumpL( 0x01000000, "AppObjectImmo.php::AppObject::_stepDep( '$_key', $_id, '$_val', $_step): begin") ;
		$depObj	=	explode( ".", $_val) ;
		$objName	=	$depObj[0] ;
		$attrName	=	$depObj[1] ;
		FDbg::dumpL( 0x02000000, "AppObjectImmo.php::AppObject::_stepDep(...): objName = $objName, attrName = $attrName") ;
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
					$e	=	new Exception( "AppObjectImmo.php::AppObject::_stepDep(...): pos[Id=$_id] is invalid!") ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( 'AppObjectImmo.php::AppObject::_stepDep(...): the object is locked!') ;
			error_log( $e) ;
			throw $e ;
		}
		$ret	=	"<" . $this->className . ">\n" .
					$this->getDepAsXML( $_key, $_id, $objName) .
					"</" . $this->className . ">\n" ;
		FDbg::dumpL( 0x01000000, "AppObjectImmo.php::AppObject::incDepSingle( '$_key', $_id, '$_val', $_step): end") ;
		return $ret ;
	}
	/**
	 *
	 * Enter description here ...
	 * @param string $_itemObjName
	 * @param integer $_step
	 */
	function	_renumber( $_itemObjName, $_step=10) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_itemObjName', $_step)") ;
		$myKeyCol	=	$this->keyCol ;
		/**
		 *
		 */
		$myItems	=	new $_itemObjName() ;
		$myItems->clearIterCond() ;
		$myItems->setIterCond( "$myKeyCol = '".$this->$myKeyCol."' ") ;
		$myItems->setIterOrder( [ "ItemNo", "SubItemNo"]) ;
		$lastNewItemNo	=	-1 ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "starting to iterate ...") ;
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
		FDbg::end() ;
	}
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $_itemObjName
	 * @param unknown_type $_step
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
	 * _assignAdmDomainId()
	 * @param	string	name of the database table
	 * @param	string	name of the attribute
	 * @param	string	condition w/o where, e.g. BrakeId = '12345'
	 * @return	string	unique Id
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
	 * @param	string	name of the database table
	 * @param	string	name of the attribute
	 * @param	string	condition w/o where, e.g. BrakeId = '12345'
	 * @return	string	unique Id
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
	function	buche() {
		error_log( "AppDepObject.php::AppDepObject::buche(): needs to be defined in derived class!") ;
	}
	function	unbuche() {
		error_log( "AppDepObject.php::AppDepObject::unbuche(): needs to be defined in derived class!") ;
	}
}

?>
