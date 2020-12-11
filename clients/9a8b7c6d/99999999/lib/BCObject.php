<?php
/**
 * BCObject - Brake Calculator Object
 *
 * Base class for all Brake Calculator objects.
 * This class extends the application object, which is supposed to remain generic for all openWAP applications, like openEISS, BrakeCalculator.
 *
 * @author	Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Platform
 * @filesource
 */
/**
 * BCObject - Brake Calculator Object
 *
 * @package BrakeCalc
 * @subpackage CoreClasses
 */
abstract	class	BCObject	extends	AppObjectCore	{
	/**
	 *
	 */
	function	__construct( $_className, $_keyCol, $_db="def") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_cölassName', '$_keyCol', '$_db')") ;
		parent::__construct( $_className, $_keyCol, $_db) ;
		FDbg::end() ;
	}
	/**
	 * methods: addDep/updDep/copyDep/delDep
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
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
			$e	=	new Exception( 'AppObject.php::AppObject::addDep: the object is locked!') ;
			error_log( $e) ;
			throw $e ;
		}
		$this->getList( $_key, $_id, $objName, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @throws Exception
	 */
	function	updDep( $_key, $_id, $_val, $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		/**
		 *
		 */
		if ( $objName === "")
			$objName	=	$this->className ;
		/**
		 *
		 */
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		/**
		 *
		 */
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		/**
		 *
		 */
		if ( $this->LockState == 0) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
							"object is not locked") ;
			try {
				$tmpObj	=	new $objName() ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				if ( $tmpObj->setId( $_id)) {
					FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)", "object is not-locked!") ;
			try {
				FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)", "trying to instantiate") ;
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
		} else {
			$e	=	new Exception( "AppObject.php::AppObject::delDep(...): the object is locked!") ;
			error_log( $e) ;
			throw $e ;
		}
		$this->getList( $_key, $_id, $objName, $reply) ;
		FDbg::end() ;
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
	 * returns the dependent object of class $_val given by the id $_id
	 * for this base object. If the id is -1 the dependent objects ItemNo (or: PosNr)
	 * will be preset through a call to _getNextItem of the dependent object class.
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
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
		FDbg::end() ;
		$reply->replyData	=	$tmpObj->getXML() ;
		return $reply ;
	}
	/**
	 * newKey( $_digits, $_nsStart, $_nsEnd, $_store)
	 *
	 * Get a new key for the object and stores the object as an empty object in the database.
	 * The object is then reloaded.
	 * @param int $_digits	number of digits for the key
	 * @param string $_nsStart	beginning of the number range within which to fetch the new key
	 * @param string $_nsEnd	end of the number range within which to fetch the new key
	 * @return void
	 */
	function	newKey() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_digits, '$_nsStart', '$_nsEnd')") ;
		$keyCol	=	$this->keyCol ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myQuery->addWhere( $this->keyCol . " LIKE 'A.%'") ;
		$myQuery->addOrder( $this->keyCol . " DESC") ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		$myRow	=	FDb::queryRow( $myQuery, self::$db[$this->className]) ;
		$this->_assignFromRow( $myRow) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( $_digits, '$_nsStart', '$_nsEnd')", "Last Key := '".$this->$keyCol."'") ;
		$this->$keyCol	=	"A." . sprintf( "%06d", intval( substr( $this->$keyCol, 2)) + 1) ;
		FDbg::end() ;
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
		$myUserIdA	=	explode( "/", $this->UserId) ;	$myUserId	=	$myUserIdA[0] ;
		$query	=	"SELECT $_attr FROM $_dbt "
				.	"WHERE $_cond AND $_attr like '$this->UserDomain@$myUserId/%' "
				.	"ORDER BY $_attr DESC LIMIT 1" ;
		error_log( $query) ;
		$row	=	FDb::queryRow( $query) ;
		$parts	=	explode( "/", $row[$_attr]) ;
		error_log( $row[$_attr]) ;
		if ( isset( $parts[1]))
			$this->$_attr	=	$this->UserDomain . "@" . $myUserId . "/" . sprintf( "%08d", $parts[1] + 1) ;
		else
			$this->$_attr	=	$this->UserDomain . "@" . $myUserId . "/" . sprintf( "%08d", 1) ;
		return $this->$_attr ;
	}
	/**
	 * _assignAdmDomainId()
	 * assigns and retrieves the next unique Id for this LeverLength in the Admin domain
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
		$myUserIdA	=	explode( "/", $this->UserId) ;
		$myUserId	=	$myUserIdA[0] ;
		$query	=	"SELECT $_attr FROM $_dbt "
				.	"WHERE $_cond AND $_attr like '$this->AdmDomain@$myUserId/%' "
				.	"ORDER BY $_attr DESC LIMIT 1" ;
		$row	=	FDb::queryRow( $query) ;
		$parts	=	explode( "/", $row[$_attr]) ;
		error_log( $row[$_attr]) ;
		if ( isset( $parts[1]))
			$this->$_attr	=	$this->AdmDomain . "@" . $myUserId . "/" . sprintf( "%08d", $parts[1] + 1) ;
		else
			$this->$_attr	=	$this->AdmDomain . "@" . $myUserId . "/" . sprintf( "%08d", 1) ;
		return $this->$_attr ;
	}
}
?>
