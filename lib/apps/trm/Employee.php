<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "MimeMail.php") ;
require_once( "XmlTools.php") ;
/**
 * Employee - Base Class
 *
 * @package Application
 * @subpackage Employee
 */
class	Employee	extends	AppObjectCore	{
	/**
	 *
	 */
	function	__construct( $_myEmployeeNo="") {
		parent::__construct( "Employee", "EmployeeNo") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myEmployeeNo) > 0) {
			try {
				$this->setEmployeeNo( $_myEmployeeNo) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
	}
	/**
	 *
	 */
	function	setEmployeeNo( $_myEmployeeNo) {
		$this->EmployeeNo	=	$_myEmployeeNo ;
		$this->reload() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myKey	=	$this->newKey( 6, "000000", "899999") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->EmployeeNo	=	$myKey ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( "Employee.php::Employee::add(): 'Employee' invalid after creation!") ;
			error_log( $e) ;
			throw $e ;
		}
		$this->getXMLString( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		$this->getXMLString( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Employee.php::Employee::del(...)") ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->className ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		$objName	=	$this->className . "Kontakt" ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "EmployeeContact") {
			$myEmployeeContact	=	new EmployeeContact() ;
			$myEmployeeContact->EmployeeNo	=	$this->EmployeeNo ;
			$myEmployeeContact->newEmployeeContact() ;
			$myEmployeeContact->getFromPostL() ;
			$myEmployeeContact->updateInDb() ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, $_val, $reply) ;
		} else if ( $objName == "LiefEmployee") {
			$this->_addDepEmployee( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefEmployee", $reply) ;
		} else if ( $objName == "RechEmployee") {
			$this->_addDepEmployee( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechEmployee", $reply) ;
		} else if ( $objName == "AddEmployee") {
			$this->_addDepEmployee( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddEmployee", $reply) ;
		}
		FDbg::end( 1, "Employee.php", "Employee", "addDep( '$_key', $_id, '$_val')") ;
		return $ret ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="") {
		$objName	=	$_val ;
		if ( $objName == "EmployeeContact") {
			$myEmployeeContact	=	new EmployeeContact() ;
			$myEmployeeContact->EmployeeNo	=	$this->EmployeeNo ;
			$myEmployeeContact->newEmployeeContact() ;
			$myEmployeeContact->getFromPostL() ;
			$myEmployeeContact->updateInDb() ;
			return $myEmployeeContact->EmployeeContactNo ;
		} else if ( $objName == "LiefEmployee") {
			return $this->_addDepEmployee( "L") ;
		} else if ( $objName == "RechEmployee") {
			return $this->_addDepEmployee( "R") ;
		} else if ( $objName == "AddEmployee") {
			return $this->_addDepEmployee( "A") ;
		}
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Employee.php", "Employee", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			return parent::updDep( $_key, $_id, $_val) ;
			break ;
		}
		FDbg::end( 1, "Employee.php", "Employee", "updDep( '$_key', $_id, '$_val')") ;
		return $this->getTableDepAsXML( $_key, $_id, $objName) ;
	}
	function	delDep( $_key, $_id, $_val) {
		$objName	=	$_val ;
		try {
			$tmpObj	=	new $objName() ;
			$myKeyCol	=	$this->keyCol ;
			$myKey	=	$this->$myKeyCol ;
			if ( $tmpObj->setId( $_id)) {
				$tmpObj->removeFromDb() ;
			} else {
				$e	=	new Exception( "Employee.php::Employee::delDep[Id='.$_id.'] dependent is INVALID !") ;
				error_log( $e) ;
				throw $e ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
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
	function	newKey( $_digits=6, $_nsStart="000000", $_nsEnd="999999", $_store=true) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_digits, '$_nsStart', '$_nsEnd')") ;
		$keyCol	=	$this->keyCol ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myQuery->addOrder( $this->keyCol . " DESC") ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		$myRow	=	FDb::queryRow( $myQuery, self::$db[$this->className]) ;
		$this->_assignFromRow( $myRow) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( $_digits, '$_nsStart', '$_nsEnd')", "Last Key := '".$this->$keyCol."'") ;
		$this->$keyCol	=	sprintf( "%06d", intval( substr( $this->$keyCol, 2)) + 1) ;
		/**
		 *
		 */
		if ( $_store) {
			$this->storeInDb() ;
			$this->reload() ;
		} else {
			$this->_valid	=	true ;
		}
		FDbg::end() ;
		return $this->$keyCol ;		// anmd return the newly assigned primary object key
	}
	/**
	 *
	 */
	function	newEmployee( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Employee.php::Employee::newEmployee( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  EmployeeNo >= '$_nsStart' AND EmployeeNo <= '$_nsEnd' " .
						"ORDER BY $this->keyCol DESC LIMIT 1 ), $_nsStart+1)  AS newKey" ;
		$myRow	=	FDb::queryRow( $myQuery) ;
		$keyCol	=	$this->keyCol ;
		$this->$keyCol	=	sprintf( "%06s", $myRow['newKey']) ;
		$this->Tax	=	1 ;
		$this->Remark	=	"" ;
		$this->storeInDb() ;
		$this->reload() ;
		return $this->_status ;
	}
 	/**
	 * Kommentar zu der Colli hinzufuegen
	 *
	 *	Dies Funktion fuegt einen Kommentar, $_rem, an die Colli an.
	 *	Datum/Uhrzeit sowie die Id des angemeldeten Benutzers, oder - im Falle eines unauthtntizierten Zugriffs - der Zusatz: "Hintergrunf Prozess"
	 *	werden automatisch in dem Kommentar vermerkt.
	 *
	 *	@param	char	$_rem
	 *	@return	void
	 */
	function	addRem( $_key="", $_id=-1, $_val="") {
		try {
			$this->_addRem( $_POST[ '_IRem']) ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "EmployeeContact") ; ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "LiefEmployee") ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "RechEmployee") ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "AddEmployee") ;
		return $ret ;
	}
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->className ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		default	:
			parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
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
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$myObj	=	new FDbObject( "EmployeeSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_employeeNoCrit	=	$sCrit ;
			$filter	=	"( C.Name like '%" . $sCrit . "%') " ;
			/**
			*
			*/
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addOrder( ["EmployeeNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Employee") ;
			break ;
		case	"EmployeeAbsence"	:
			$myObj	=	new FDbObject( $objName) ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( EmployeeNo = '" . $this->EmployeeNo . "') " ;
			$filter2	=	"" ;
			/**
			*
			*/
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( [ "Date DESC"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		return $reply ;
	}
	/**
	 * special functions for this class
	 */
	function	consolidateAbsence( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myAbsence	=	new EmployeeAbsence() ;
		$myAbsence->setIterCond( "EmployeeNo = '".$this->EmployeeNo."' AND AbsenceType='vac' AND Status='free'") ;
		$myAbsence->setIterOrder( "Date") ;
		$daysLeft	=	0 ;
		foreach ( $myAbsence as $absence) {
			switch ( $absence->Action) {
			case	"add"	:
				$daysLeft	+=	$absence->Days ;
				break ;
			case	"req"	:
				$daysLeft	-=	$absence->Days ;
				break ;
			}
		}
		$this->addCol( "DaysLeft", "int") ;
		$this->DaysLeft	=	$daysLeft ;
		$this->getXMLString( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	requestAbsence( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myAbsence	=	new EmployeeAbsence() ;
		$myAbsence->EmployeeNo	=	$this->EmployeeNo ;
		$myAbsence->Date	=	$this->today() ;
		$myAbsence->Action	=	"req" ;							// request absence
		$myAbsence->AbsenceType	=	$_POST[ "AbsenceType"] ;
		$myAbsence->DateFrom	=	$_POST[ "DateFrom"] ;
		$myAbsence->DateUntil	=	$_POST[ "DateUntil"] ;
		$myAbsence->Days	=	$_POST[ "NumberOfDays"] ;			// trust the data, will be checked later
		$myAbsence->Status	=	"prel" ;					// it's only pre-liminary for now
		$myAbsence->storeInDb() ;
		$this->getXMLString( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
}
?>
