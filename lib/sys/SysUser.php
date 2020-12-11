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
 * BDocument.php Base class for PDF-format printed matters
 *
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package Application
 */
/**
 * requires the WTF Debugger and the Base Class
 */
/**
 * SysUser - SysUser-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BSysUser which should
 * not be modified.
 *
 * @package Application
 * @subpackage SysUser
 */
class	SysUser	extends	FDbObject	{
	/*
	 * The constructor can be passed an ArticleNr (UserId), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_userId='') {
		parent::__construct( "SysUser", "UserId", "sys") ;
		$this->setKey( $_userId) ;
	}
	/**
	 * set the Order Number (UserId)
	 *
	 * Sets the order number for this object and tries to load the order from the database.
	 * If the order could successfully be loaded from the database the respective customer data
	 * as well as customer contact data is retrieved as well.
	 * If the order has a separate Invoicing address, identified through a populated field, this
	 * data is read as well.
	 * If the order has a separate Delivery address, identified through a populated field, this
	 * data is read as well.
	 *
	 * @param	string	$_myUserId
	 * @return void
	 */
	function	setUserId( $_userId) {
		$this->setKey( $_userId) ;
		return $this->isValid() ;
	}
	/**
	 *
	 * @param string	$_key
	 * @param int	$_id
	 * @param mixed	$_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		try {
			$this->getFromPostL() ;
			$this->UserId	=	$_POST['_IUserId'] ;
			$this->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="", $reply=null) {
		global	$mySysUser ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	login( $_key, $_id, $_val, $reply=null) {
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param string $_password
	 * @param string $_userId
	 */
	function	identify( $_password , $_userId) {
		$this->invalidate() ;
		$this->setKey( $_userId) ;
		$this->fetchFromDb() ;
		if ( !$this->isValid()) {
//			$this->MailId	=	$this->UserId ;
//			$this->fetchFromDbByEmail() ;
		}
		if ( $this->isValid()) {
			error_log( "User is valid ...") ;
			if ( $this->ValidFrom <= $this->today() && $this->today() <= $this->ValidTo) {
				error_log( "timeframe is valid ...") ;
				if ( $this->MD5Password == md5( $_password)) {
					$this->_valid	=	true ;
				} else if ( $this->Password != "*" && $this->Password == $_password) {
					$this->_valid	=	true ;
				} else {
					$this->invalidate() ;
				}
			} else {
				$this->invalidate() ;
				$this->_status	=	4712 ;
			}
		} else {
			$this->invalidate() ;
		}
		if ( $this->isValid() == false) {
			$this->StatusInfo	=	"INVALID" ;
		} else {
			$this->StatusInfo	=	"token" ;
		}
		return $this->isValid() ;
	}
	/**
	 *
	 * @param string	$password
	 */
	function	identifySysUser( $_key, $_id, $_val) {
		$this->identify( $_val, $this->UserId) ;
		return "" ;
	}
	/**
	 *
	 */
	function	fetchFromDbByEmail() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( void)") ;
		$this->_status	=	0 ;
		$this->invalidate() ;
		$query	=	"SELECT * " ;
		$query	.=	"FROM SysUser " ;
		if ( $this->MailId != "") {
			$query	.=	"WHERE MailId='" . $this->MailId . "' " ;
		}
		$sqlResult	=	FDb::query( $query) ;
		if ( !$sqlResult) {
			$this->_status	=	-1 ;
		} else {
			$numrows	=	FDb::rowCount() ;
			if ( $numrows == 1) {
				$this->assignFromRow() ;
				$this->invalidate() ;
			} else {
				$this->_status	=	-2 ;
			}
		}
		FDbg::end() ;
		return $this->_status ;
	}
	/**
	 *
	 */
	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		return $ret ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
				$_POST['_step']	=	$_val ;
			/**
			 *
			 */
			$myObj	=	new SysUser() ;				// no specific object we need here
 			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addOrder( ["ClientId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"ClientApplication"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	new FSqlSelect( "ClientApplication") ;
			$myQuery->addWhere( ["ClientId = '".$this->ClientId."'"]) ;
			$myQuery->addOrder( ["ApplicationSystemId", "ApplicationId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "ClientApplication") ;
			break ;
		}
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
