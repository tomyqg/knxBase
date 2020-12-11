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
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class	Role	extends AppObject	{
	function	__construct( $_name="default") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		parent::__construct( "Role", "RoleId", "appSys") ;
		if ( strlen( $_name) > 0) {
			try {
				$this->setKey( $_name) ;
			} catch ( Exception $e) {
			}
		}
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
			if ( ! $appUser->isGranted( "fnc", "Role.add")) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)", "insufficient rights to create a new role") ;
			}
		}
		try {
			$this->getFromPostL() ;
			$this->RoleId	=	$_POST['RoleId'] ;
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
			if ( ! $appUser->isGranted( "fnc", "Role.upd")) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)", "insufficient rights to update a role") ;
			}
		}
		parent::upd( $_key, $_id, $_val) ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param string	$_key
	 * @param int	$_id
	 * @param mixed	$_val
	 */
	function	del( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$appUser	=	$this->__getAppUser() ;
		if ( $appUser) {
			if ( ! $appUser->isGranted( "fnc", "Role.del")) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)", "insufficient rights to delete a 'Role' object") ;
			}
		}
		parent::del( $_key, $_id, $_val) ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	function	copy( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <_reply>") ;
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
		$this->getList( $_key, $_id, $objName, $reply) ;
		FDbg::end( 1, "Role.php", "Role", "addDep( '$_key', $_id, '$_val')") ;
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Role.php", "Role", "updDep( '$_key', $_id, '$_val')",
						"object is not locked") ;
		try {
			$tmpObj	=	new $objName() ;
			$myKeyCol	=	$this->keyCol ;
			$myKey	=	$this->$myKeyCol ;
			if ( $tmpObj->setId( $_id)) {
				FDbg::trace( 2, "Role.php", "Role", "updDep( '$_key', $_id, '$_val')",
						"object is valid") ;
				$tmpObj->getFromPostL() ;
				$tmpObj->updateInDb() ;
			} else {
				$e	=	new Exception( 'Role::updDep[Id='.$_id.'] is INVALID !') ;
				error_log( $e) ;
				throw $e ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		$this->getDepAsXML( $_key, $_id, $objName, $reply) ;
		FDbg::end( 1, "Role.php", "Role", "updDep( '$_key', $_id, '$_val')") ;
		return $reply ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	copyDep( $_key="", $_id=-1, $_val="", $reply=null) {
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
		$this->getList( $_key, $_id, $objName, $reply) ;
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
			if ( isset( $_POST['StartRow'])) {
				$this->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"RoleId like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery	=	$this->getQueryObj( "Select") ;
			$myQuery->addOrder( ["RoleId"]) ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$this->tableFromQuery( $myQuery) ;
			break ;
		case	"RoleProfile"	:
			$myObj	=	new FDbObject( "RoleProfile", "Id", "appSys", "v_RoleProfileSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["RoleId = '".$this->RoleId."'"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "RoleProfile") ;
			break ;
		case	"AppUserWithRole"	:
			$myObj	=	new FDbObject( "AppUserWithRole", "Id", "appSys", "v_AppUserWithRoleSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["RoleId = '".$this->RoleId."'"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
}

?>
