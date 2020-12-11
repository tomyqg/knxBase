<?php
/**
 * Copyright (c) 2015, 2016 wimtecc, Karl-Heinz Welter
 *y
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
class	AuthObject	extends	AppObjectCore	{
	public	$roleId	=	"*" ;
	public	$profileId	=	"*" ;
	public	$LockState	=	false ;
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "AuthObject", "AuthObjectId", "appSys") ;
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
			if ( ! $appUser->isGranted( "fnc", "AuthObject.add")) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)", "insufficient rights to create a new authorization object") ;
			}
		}
		try {
			$this->getFromPostL() ;
			$this->AuthObjectId	=	$_POST['AuthObjectId'] ;
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
			if ( ! $appUser->isGranted( "fnc", "AuthObject.upd")) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)", "insufficient rights to update an authorization object") ;
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
			if ( ! $appUser->isGranted( "fnc", "AuthObject.del")) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)", "insufficient rights to update an 'AuthObject' object") ;
			}
		}
		parent::del( $_key, $_id, $_val) ;
		FDbg::end() ;
		return $this->getXMLString() ;
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
			$filter1	=	"AuthObjectId like '" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery	=	$this->getQueryObj( "Select") ;
			$myQuery->addOrder( ["AuthObjectId"]) ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$this->tableFromQuery( $myQuery) ;
			break ;
		case	"ProfileWithAuthObject"	:
			$myObj	=	new FDbObject( "ProfileWithAuthObject", "Id", "appSys", "v_ProfileWithAuthObjectSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["AuthObjectId = '".$this->AuthObjectId."'"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
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
	function	createDefaultDBT( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$authObject	=	new AuthObject() ;
		$myTables	=	new FDbObject( "v_Tables", "", "def") ;
		$myTables->setIterCond( "DataBaseName = '".FDb::getDbName( $_POST[ "DatabaseAlias"])."'") ;
		foreach ( $myTables as $obj) {
			error_log( $obj->TableName) ;
			$authObject->setKey( $_POST["Prefix"] . $obj->TableName . "_*") ;
			if ( $authObject->isValid()) {
				error_log( "................. '" . $authObject->AuthObjectId . "' already exists ... no need to create!") ;
			} else {
				if ( intval( $_POST["ExecuteI"]) == 1) {
					error_log( "................. '" . $authObject->AuthObjectId . "' DOES NOT YET EXISTS exist ... WILL CREATE!") ;
					$authObject->AuthObjectType	=	"dbt" ;
					$authObject->ObjectAttribute	=	"grant" ;
					$authObject->ObjectName	=	$obj->TableName . ".*" ;
					$authObject->storeInDb() ;
				}
			}
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	assignToProfile( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$profileAuthObject	=	new ProfileAuthObject() ;
		$myTables	=	new FDbObject( "v_Tables", "", "def") ;
		$myTables->setIterCond( "DataBaseName = '".FDb::getDbName( $_POST[ "DatabaseAlias"])."'") ;
		foreach ( $myTables as $obj) {
			$myAuthObjectId	=	$_POST["Prefix"] . $obj->TableName . "_*" ;
			error_log( $obj->TableName) ;
			$profileAuthObject->fetchFromDbWhere( [ "ProfileId = '".$_POST[ "ProfileId"]."'", "AuthObjectId = '".$myAuthObjectId."'"]) ;
			if ( $profileAuthObject->isValid()) {
				error_log( "................. ProfileAuthObject already exists ... no need to create!") ;
			} else {
				error_log( "................. '" . $_POST[ "ProfileId"] . ":" . $myAuthObjectId . "' DOES NOT YET EXISTS exist") ;
				if ( intval( $_POST["ExecuteII"]) == 1) {
					error_log( "................. ... WILL CREATE!") ;
					$profileAuthObject->ProfileId	=	$_POST[ "ProfileId"] ;
					$profileAuthObject->AuthObjectId	=	$myAuthObjectId ;
					$profileAuthObject->storeInDb() ;
				}
			}
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	isGranted( $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_val')") ;
		/**
		 *
		 */
		switch ( $this->AuthObjectType) {
		case "scr"	:
			$grant	=	$this->ObjectAttribute == "grant" ? true : false ;
			break;
		case "fnc"	:
			$grant	=	$this->ObjectAttribute == "grant" ? true : false ;
			break;
		case "dbt"	:
			$grant	=	$this->ObjectAttribute == "grant" ? true : false ;
			break;
		case "dbv"	:
			$grant	=	$this->ObjectAttribute == "grant" ? true : false ;
			break;
		}
		FDbg::end( $grant) ;
		return $grant ;
	}
	function	isValueGranted( $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_val')") ;
		/**
		 *
		 */
		switch ( $this->AuthObjectType) {
		case "scr"	:
		case "fnc"	:
		case "dbt"	:
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)", "CODING ERROR: ") ;
			break;
		case "dbv"	:
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_val')",
							"_val = '$_val', regex = '".$this->AttrValue."'") ;
			$res	=	preg_match( "/".$this->AttrValue."/", $_val) ;
			if ( $res === false) {
				FDbg::abort( $res) ;
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)", "invalid regular expression '".$this->AttrValue."'!") ;
			} else if ( $res == 0 && $this->ObjectAttribute == "revokevalue") {
				$grant	=	true ;
			} else if ( $res > 0 && $this->ObjectAttribute == "grantvalue") {
				$grant	=	true ;
			} else {
				$grant	=	false ;
			}
			break;
		}
		FDbg::end( $res) ;
		return $grant ;
	}
	/**
	 *
	 */
	function	__toString() {
		$buffer	=	"AuthObject:"."\n" ;
		$buffer	.=	"\tId..............: ".$this->Id."\n" ;
		$buffer	.=	"\tAuthObjectId....: ".$this->AuthObjectId."\n" ;
		$buffer	.=	"\tAuthObjectType..: ".$this->AuthObjectType."\n" ;
		$buffer	.=	"\tObjectName......: ".$this->ObjectName."\n" ;
		$buffer	.=	"\tObjectAttribute.: ".$this->ObjectAttribute."\n" ;
		return $buffer ;
	}
	/**
	 *
	 */
	function	toStringR1( $_le="\n") {
		$buffer	=	"AuthObject:".$_le ;
		$buffer	.=	"\tSource..........: ".$this->roleId.".".$this->profileId.$_le ;
		$buffer	.=	"\tId..............: ".$this->Id.$_le ;
		$buffer	.=	"\tAuthObjectId....: ".$this->AuthObjectId.$_le ;
		$buffer	.=	"\tAuthObjectType..: ".$this->AuthObjectType.$_le ;
		$buffer	.=	"\tObjectName......: ".$this->ObjectName.$_le ;
		$buffer	.=	"\tObjectAttribute.: ".$this->ObjectAttribute.$_le ;
		return $buffer ;
	}
	/**
	 *
	 */
	function	toStringR2() {
		$buffer	=	"" ;
		$buffer	.=	$this->roleId.".".$this->profileId ;
		$buffer	.=	";".$this->AuthObjectType ;
		$buffer	.=	";".$this->AuthObjectId ;
		$buffer	.=	";".$this->ObjectName ;
		$buffer	.=	";".$this->ObjectAttribute ;
		$buffer	.=	";".$this->AttrValue ;
		return $buffer ;
	}

	protected	function	_postInstantiate() {
	}

	protected	function	_postLoad() {
	}

}
?>
