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
 * @author	Karl-Heinz Welter <khw@wimtecc.com>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * Client - Base Class
 *
 * @package Application
 * @subpackage Client
 */
class	Client	extends	AppObjectCore	{
	/**
	 *
	 */
	function	__construct( $_myClientId="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myClientId')") ;
		parent::__construct( "Client", "ClientId", "sys") ;
		if ( strlen( $_myClientId) > 0) {
			try {
				$this->setClientId( $_myClientId) ;
			} catch ( Exception $e) {
				FDbg::abort() ;
				throw $e ;
			}
		} else {
		}
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setClientId( $_myClientId) {
		error_log( "setting ClientId to " . $_myClientId) ;
		$this->ClientId	=	$_myClientId ;
		$this->reload() ;
		error_log( $this->Name1) ;
		return $this->_valid ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->ClientId	=	$myKey ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			$e	=	new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$this->getClassName()', '$_key'): n'Client' invalid after creation!", $e) ;
			FDbg::abort() ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->className ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		$objName	=	$this->className . "Kontakt" ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		return $this->getXMLString() ;
	}
	/**
	 * addDep( ...)
	 *
	 * add a dependent object to a 'Client' object.
	 * Creating a 'ClientApplication' requries special care as we need to create some basic 'SysConfigObj's as well and store
	 * these in the database.

	 * @param string	$_key
	 * @param int		$_id
	 * @param mixed		$_val
	 * @param Reply
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"ClientApplication"	:
			$tmpObj	=	new $objName() ;
			$myKeyCol	=	$this->keyCol ;
			$myKey	=	$this->$myKeyCol ;
			$tmpObj->getFromPostL() ;
			$tmpObj->$myKeyCol	=	$this->$myKeyCol ;
			$tmpObj->storeInDb() ;
			$mySysConfigObj	=	new SysConfigObj() ;
			$mySysConfigObj->ApplicationSystemId	=	$tmpObj->ApplicationSystemId ;
			$mySysConfigObj->ApplicationId	=	$tmpObj->ApplicationId ;
			$mySysConfigObj->ClientId	=	$tmpObj->ClientId ;
			$mySysConfigObj->Class	=	"def" ;
			$mySysConfigObj->Parameter	=	"dbAlias" ;
			$mySysConfigObj->Value	=	"" ;
			$mySysConfigObj->storeInDb() ;
			$mySysConfigObj->Parameter	=	"dbHost" ;
			$mySysConfigObj->Value	=	"" ;
			$mySysConfigObj->storeInDb() ;
			$mySysConfigObj->Parameter	=	"dbName" ;
			$mySysConfigObj->Value	=	"" ;
			$mySysConfigObj->storeInDb() ;
			$mySysConfigObj->Parameter	=	"dbPassword" ;
			$mySysConfigObj->Value	=	"" ;
			$mySysConfigObj->storeInDb() ;
			$mySysConfigObj->Parameter	=	"dbUser" ;
			$mySysConfigObj->Value	=	"" ;
			$mySysConfigObj->storeInDb() ;
			$mySysConfigObj->Parameter	=	"driver" ;
			$mySysConfigObj->Value	=	"" ;
			$mySysConfigObj->storeInDb() ;
			$this->getList( $_key, $_id, $objName, $reply) ;
			break ;
		default	:
			parent::addDep( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * buche
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
		FDbg::trace( 2, "Client.php", "Client", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		case ""	:
			$objName	=	"Client" ;
			$tmpObj	=	new $objName() ;
			$myKeyCol	=	$this->keyCol ;
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
			$this->getList( $_key, $_id, $_val, $reply) ;
			break ;
		default	:
			parent::updDep( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	function	delDep( $_key, $_id, $_val, $reply=null) {
		FDbg::begin( 1, "Client.php", "Client", "delDep( '$_key', $_id, '$_val', <reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		try {
			switch ( $objName) {
			default	:
				parent::delDep( $_key, $_id, $_val, $reply) ;
				break ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $reply ;
	}
   /**
     *
     */
    function    fetchForNew() {
        $this->ClientName1   =   $_POST['_IClientName1'] ;
        $this->ClientName2   =   $_POST['_IClientName2'] ;
        $this->ZIP  =   $_POST['_IZIP'] ;
        $this->City  =   $_POST['_ICity'] ;
        $this->Street  =   $_POST['_IStreet'] ;
        $this->Number   =   $_POST['_INumber'] ;
        $this->Country =   $_POST['_ICountry'] ;
        $this->Phone  =   $_POST['_IPhone'] ;
        $this->Fax  =   $_POST['_IFax'] ;
        $this->Cellphone    =   $_POST['_ICellphone'] ;
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
	function	addRem( $_key="", $_id=-1, $_val="", $_reply=null) {
		try {
			$this->_addRem( $_POST[ '_IRem']) ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Client.php", "Client", "getAsXML( '$_key', $_id, '$_val', <Reply>)") ;
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
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		global	$mySysSession ;
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			if ( isset( $_POST['StartRow'])) {
				$this->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$this->getQueryObj( "Select") ;
			$myQuery->addField( ["Id","ClientId","Name1","Language","Server"]) ;
			$myQuery->addOrder( ["ClientId"]) ;
			if ( $mySysSession->Validity <= SysSession::VALIDLOGIN) {
				$myServerName	=	explode( ".", $_SERVER['SERVER_NAME']) ;
				$myQuery->addWhere( "Server LIKE '%,".$myServerName[0].",%'") ;
			}
			$reply->replyData	=	$this->tableFromQuery( $myQuery) ;
			break ;
		case	"ClientContact"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["ClientId = '".$this->ClientId."'"]) ;
			$myQuery->addOrder( ["ClientContactNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"ClientApplication"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["ClientId = '".$this->ClientId."'"]) ;
			$myQuery->addOrder( ["ApplicationSystemId", "ApplicationId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "ClientApplication") ;
			break ;
		case	"SysConfigObj"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$cond	=	"ClientId = '" . $this->ClientId . "' "
					;
			$order	=	"LENGTH( ClientId) ASC, Class, Block, Parameter " ;
			$myQuery->addWhere( [ $cond, "ApplicationSystemId like '%".$sCrit."%'"]) ;
			$myQuery->addOrder( [ "ApplicationSystemId", "ApplicationId", "Class", "Block", "PArameter"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "SysConfigObj") ;
			break ;
		}
//		error_log( $ret) ;
		FDbg::end() ;
		return $reply ;
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
