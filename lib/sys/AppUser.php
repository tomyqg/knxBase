<?php
/**
 * Copyright (c) 2015, 2016 wimtecc, Karl-Heinz Welter
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
 * AppUser.php - Class definition
 *  Domain:
 *  	- administrative
 * 	AppUser references:
 * 		- n/a
 *  AppUser is referenced by:
 *
 *
 * @author	Karl-Heinz Welter <khwelter@icloud.com>
 * @version	0.1
 * @package	AppUserCalc
 */
/**
 * AppUser - AppUser-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package	AppUserCalc
 * @subpackage	Classes
 */
class	AppUser	extends	AppObject	{
	public		$scr	=	array() ;
	public		$fnc	=	array() ;
	public		$dbt	=	array() ;
	public		$dbv	=	array() ;

	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param	string	$_artikelNr
	 * @return void
	 */
	function	__construct() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."", "__construct()") ;
		parent::__construct( "AppUser", "UserId", "appSys") ;
		FDbg::end() ;
	}
	
	/**
	 *
	 * @param string	$_key
	 * @param int	$_id
	 * @param mixed	$_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$appUser	=	$this->__getAppUser() ;
		if ( $appUser) {
			if ( ! $appUser->isGranted( "fnc", "AppUser.add")) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)", "insufficient rights to create a new application user", 0, -1) ;
			}
		}
		try {
			$this->getFromPostL() ;
			$this->UserId	=	$_POST['UserId'] ;
			$this->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param string	$_key
	 * @param int	$_id
	 * @param mixed	$_val
	 */
	function	upd( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$appUser	=	$this->__getAppUser() ;
		if ( $appUser) {
			if ( ! $appUser->isGranted( "fnc", "AppUser.upd")) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)", "insufficient rights to update an application user", 0, -2) ;
			}
		}
		parent::upd( $_key, $_id, $_val) ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getXMLComplete()
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "AppUser{AppObjectCore}") ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "AppUser.php", "AppUser{AppObjectCore}", "getDepAsXML( '$_key', $_id, '$_val')") ;
		switch ( $_val) {
			default	:
				if ( $_id >= 0) {
					return parent::getDepAsXML( $_key, $_id, $_val) ;
				} else {
					$newItem	=	new $_val ;
					$newItem->UserId	=	$this->UserId ;
					return $newItem->getAsXML() ;
				}
				break ;
		}
		FDbg::end() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::addDep()
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		case "AppUserRole"	:
			$myAppUserRole	=	new AppUserRole() ;
			$myAppUserRole->UserId	=	$this->UserId ;
			$myAppUserRole->add() ;
//			$this->getList( $_key, $_id, $_val, $reply) ;
			break;
		default	:
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch( $_val) {
			default	:
				parent::delDep( $_key, $_id, $_val) ;
				break ;
		}
//		$this->getList( $_key, $_id, $_val, $reply) ;
		return ( $reply) ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "AppUser.php", "AppUser{AppObjectCore}", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			if ( isset( $_POST['StartRow'])) {
				$this->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"UserId like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery	=	$this->getQueryObj( "Select") ;
			$myQuery->addOrder( ["UserId"]) ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$this->tableFromQuery( $myQuery) ;
			break ;
		case	"AppUserRole"	:
			$myObj	=	new FDbObject( "AppUserRole", "Id", "appSys", "v_AppUserRoleSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ "UserId = '".$this->UserId."'", "RoleId LIKE '%".$sCrit."%'"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "AppUserRole") ;
			break ;
		case	"AppUserRoleProfileAuthObject"	:
			$myObj	=	new FDbObject( "AppUserRoleProfileAuthObject", "Id", "appSys", "v_AppUserRoleProfileAuthObjectSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"UserId like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( ["UserId = '".$this->UserId."'"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	identify( $_password , $_userId) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '*****', '$_userId')") ;
		$this->_valid	=	false ;
		if ( $_userId != "") {
			$this->setKey( $_userId) ;
		}
		$this->fetchFromDb() ;
		if ( !$this->_valid) {
//			$this->MailId	=	$this->UserId ;
//			$this->fetchFromDbByEmail() ;
		}
		if ( $this->_valid) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '*****', '$_userId')", "checking validity period") ;
			if ( $this->ValidFrom <= $this->today() && $this->today() <= $this->ValidTo) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '*****', '$_userId')", "checking password") ;
				if ( $this->MD5Password == md5( $_password)) {
					$this->_valid	=	true ;
				} else if ( $this->Password != "*" && $this->Password == $_password) {
					$this->_valid	=	true ;
				} else {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__." '*****', '$_userId')", "no password match") ;
					$this->_valid	=	false ;
				}
			} else {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '*****', '$_userId')", "userId expired") ;
				$this->_valid	=	false ;
				$this->_status	=	4712 ;
			}
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '*****', '$_userId')", "userId not in database") ;
			$this->_valid	=	false ;
		}
		if ( $this->_valid == false) {
			$this->StatusInfo	=	"INVALID" ;
		} else {
			$this->StatusInfo	=	"token" ;
		}
		FDbg::end( $this->_valid) ;
		return $this->_valid ;
	}
	/**
	 *
	 */
	function	buildAuthTree() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( void)") ;
		/**
		 *
		 */
		$myAppUserRole	=	new AppUserRole() ;
		$myRoleProfile	=	new RoleProfile() ;
		$myProfileAuthObject	=	new ProfileAuthObject() ;
		/**
		 *
		 */
		EISSCoreObject::__pushAppUser() ;		// temporarily dereference the AppUser to "disable" authorization
		/**
		 *
		 */
		$myAppUserRole->clearIterCond() ;
		$myAppUserRole->setIterCond( [ "UserId = '".$this->UserId."' ", "Active = 1"]) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "starting to iterate ...") ;
		try {
			foreach ( $myAppUserRole as $myRole) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "assigned role := '".$myRole->RoleId."' ") ;
				$myRoleProfile->clearIterCond() ;
				$myRoleProfile->setIterCond( [ "RoleId = '".$myRole->RoleId."' ", "Active = 1"]) ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "starting to iterate ...") ;
				foreach ( $myRoleProfile as $myProfile) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "assigned profile := '".$myProfile->ProfileId."' ") ;
					$myProfileAuthObject->clearIterCond() ;
					$myProfileAuthObject->setIterCond( [ "ProfileId = '".$myProfile->ProfileId."' ", "Active = 1"]) ;
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "starting to iterate ...") ;
					foreach ( $myProfileAuthObject as $myAuthObject) {
						$authObject	=	new AuthObject() ;
						$authObject->setKey( $myAuthObject->AuthObjectId) ;
						$authObject->profileId	=	$myProfile->ProfileId ;
						$authObject->roleId		=	$myRole->RoleId ;
						FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "assigned authObject := '".$authObject->ObjectName."' ") ;
						switch ( $authObject->AuthObjectType) {
						case "scr"	:
							$this->scr[$authObject->ObjectName]	=	$authObject ;
							break ;
						case "dbt"	:
							$this->dbt[$authObject->ObjectName]	=	$authObject ;
							break ;
						case "dbv"	:
							$this->dbv[$authObject->ObjectName]	=	$authObject ;
							break ;
						case "fnc"	:
							$this->fnc[$authObject->ObjectName]	=	$authObject ;
							break ;
						}
					}
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "done to iterate ...") ;
				}
			}
		} catch ( Exception $e) {
			error_log( "..........................".$e->getMessage()) ;
		}
		/**
		 *
		 */
		EISSCoreObject::__popAppUser() ;		//  re-reference the AppUser to "disale" authorization
		/**
		 *
		 */
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	isGranted( $_authObjectType, $_authObjectName, $_val="", $_explicit=false) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_authObjectType', '$_authObjectName', '$_val')") ;
		/**
		 *
		 */
		switch ( $_authObjectType) {
		case "scr"	:
			if ( isset( $this->scr[ $_authObjectName]))
				$grant	=	 $this->scr[ $_authObjectName]->isGranted( $_val) ;
			break;
		case "fnc"	:
			if ( isset( $this->fnc[ $_authObjectName]))
				$grant	=	 $this->fnc[ $_authObjectName]->isGranted( $_val) ;
			break ;
		case "dbt"	:
			if ( isset( $this->dbt[ $_authObjectName]))
				$grant	=	 $this->dbt[ $_authObjectName]->isGranted( $_val) ;
			break ;
		case "dbv"	:
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)", "CODING FAULT: isGranted with wrong types!") ;
			break ;
		default	:
			$grant	=	false ;
			break ;
		}
		/**
		 * if we have no statement on this authorization object as of yet, see if we can obtain a statement
		 * by climbing up the tree with wildcars in the last position
		 */
		if ( ! isset( $grant) && ! $_explicit) {
			$levels	=	explode( ".", $_authObjectName) ;
			if ( $levels[ count( $levels) - 1] == "*") {
				unset( $levels[ count( $levels) - 1]) ;
			}
			while ( ! isset( $grant) && count( $levels) > 1) {
				unset( $levels[ count( $levels) - 1]) ;
				$grant	=	$this->isGranted( $_authObjectType, implode( ".", $levels) . ".*", $_val, $_explicit) ;
				$levels	=	explode( $_authObjectName, ".") ;
			}
		}
		/**
		 *
		 */
		if ( ! isset( $grant))
			$grant	=	false ;
		$grant	=	true ;
		return $grant ;
	}
	/**
	 *
	 */
	function	isValueGranted( $_authObjectType, $_authObjectName, $_val="", $_explicit=false) {
		/**
		 *
		 */
		switch ( $_authObjectType) {
		case "scr"	:
		case "fnc"	:
		case "dbt"	:
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)", "CODING FAULT: isValueGranted with wrong types!") ;
			break ;
		case "dbv"	:
			if ( isset( $this->dbv[ $_authObjectName])) {
				$grant	=	 $this->dbv[ $_authObjectName]->isValueGranted( $_val) ;
			} else{
				$grant	=	true ;
			}
			break ;
		default	:
			$grant	=	false ;
			break ;
		}
		/**
		 *
		 */
		if ( ! isset( $grant))
			$grant	=	false ;
		$grant	=	true ;
		return $grant ;
	}
	function	toString( $_prefix, $_block, $_le="\n") {
		$buffer	=	"" ;
		foreach ( $_block as $authObject) {
			$buffer	.=	$authObject->toStringR2() . "<br/>" ;
		}
		return $buffer ;
	}

	/**
	 *
	 */
	function	showAuthTree() {
		foreach ( $this->dbt as $name => $data) {
			error_log( "right name '" . $name . "'") ;
		}
	}
	/**
	 *
	 */
	protected	function	_postInstantiate() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}

	/**
	 *
	 */
	protected	function	_postLoad() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}
}
?>
