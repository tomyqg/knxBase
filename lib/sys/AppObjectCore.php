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
 * AppObjectCore - Application Object
 *
 * This class is derived from FDbObject.
 * AppObjectCore adds functionality for Locking and handling of remarks, both of which are
 * supported by methods in FDbObject.
 *
 * Base class for all objects which have
 *
 * @author [wimteccgen] Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package wtcAppERPBaseObjects
 * @filesource
 */
/**
 * AppObjectCore - Application Object
 *
 * @package wtcAppERPBaseObjects
 * @subpackage AppObjectCores
 */
abstract	class	AppObjectCore	extends	FDbObject	{
	private	$LockState	=	false ;
	
	/**
	 * AppObjectCore constructor.
	 * @param string $_className    Name of table in database
	 * @param string $_keyCol       Name of attribute in table which is primary index
	 * @param string $_db           Alias of database connection to be used
	 * @param string $_tableName    Name of table in database
	 */
	function	__construct( $_className, $_keyCol, $_db="def", $_tableName="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_className', <_keyCol>, '$_db', '$_tableName')") ;
		parent::__construct( $_className, $_keyCol, $_db, $_tableName) ;
		FDbg::end() ;
	}
	
	/**
	 * methods: add/upd/copy/del
	 */
	
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::add()
	 */
	function	add( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$this->defaultDates() ;										// set all dates to TODAY
		$this->clearRem() ;											// clear all remarks
		$this->getFromPostL() ;										// fetch POSTed atributes
		$this->storeInDb() ;										// and store in database
		$this->reload() ;											// reload
		parent::getXMLString( $_key, $_id, $_val, $_reply) ;
		FDbg::end() ;
		return $_reply ;
	}
	/**
	 * update the current object in the database
	 * if the object has a LockState attribute and this is set to true, no update will be performed and an exception is thrown
	 * (non-PHPdoc)
	 * @see FDbObject::upd()
	 */
	function	upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			if ( $_reply->replyingClass === __class__)
				$this->getFromPostL() ;
			$this->updateInDb() ;
			$this->reload() ;		// reload; dependent objects might have changed, e.g. customer/supplier/-contacs
			$this->getXMLString( $_key, $_id, $_val, $_reply) ;
		} else {
			$keyCol	=	$this->keyCol ;
			$e	=	new Exception( "AppObjectCore.php::AppObjectCore::upd: The object [" . $this->$keyCol . "] is locked!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end() ;
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			$this->removeFromDb() ;
			$_reply	=	$this->getNextAsXML( $_key, $_id, $_val) ;
		} else {
			$keyCol	=	$this->keyCol ;
			$e	=	new Exception( "AppObjectCore.php::AppObjectCore::del: The object [" . $this->$keyCol . "] is locked!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end() ;
		return $_reply ;
	}
	/**
	 * methods: business logic
	 */
	function	lock( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$this->_lock( 1) ;
		$this->reload() ;
		parent::getXMLString( $_key, $_id, $_val, $_reply) ;
		FDbg::end() ;
		return $_reply ;
	}
	function	unlock( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$this->_lock( 0) ;
		$this->reload() ;
		parent::getXMLString( $_key, $_id, $_val, $_reply) ;
		FDbg::end() ;
		return $_reply ;
	}
	/**
	 * methods: business logic
	 */
	function	addRem( $_key="", $_id=-1, $_val="", $_reply=null) {
		$this->_addRem( $_val) ;
		return $this->getAttrXMLF( "Rem1") ;
	}
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::getXMLString()
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		if ( $_val != "") {
			$this->getDepAsXML( $_key, $_id, $_val, $_reply) ;
		} else {
			parent::getXMLString( $_key, $_id, $_val, $_reply) ;
		}
		FDbg::end() ;
		return $_reply ;
	}
	/**
	 * returns the dependent object of class $_val given by the id $_id
	 * for this base object. If the id is -1 the dependent objects ItemNo (or: PosNr)
	 * will be preset through a call to _getNextItem of the dependent object class.
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @return   Reply
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		$tmpObj	=	new $objName() ;
		if ( $_id == -1) {
		} else {
			$tmpObj->setId( $_id) ;
		}
		FDbg::end() ;
		$_reply->replyData	=	$tmpObj->getXML() ;
		return $_reply ;
	}
	/**
	 *
	 */
	function	getDepAsJSON( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		$tmpObj	=	new $objName() ;
		if ( $_id == -1) {
		} else {
			$tmpObj->setId( $_id) ;
		}
		$_reply->replyData	=	$tmpObj->getJSONComplete() ;
		return $_reply ;
	}

	/**
	 *
	 * Enter description here ...
	 * @var string  $_key
	 * @var int     $_id
	 * @var mixed   $_val
	 * @var Reply      $_reply
	 * @return Reply
	 */
	function	getList( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$_POST['_step']	=	$_val ;
			$myObj	=	new FDbObject( $this->className) ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( $_POST['StartRow'], $_POST['RowCount'], $_POST['step']) ;
			}
			$myQuery	=	new FSqlSelect( $this->className) ;
			$myQuery->addOrder( ["Id"]) ;
			$_reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		default	:
			error_log( "PANIC: ---> ".__CLASS__.".php(".__LINE__.")::".__CLASS__."::query( ...)::NOTE: may not be called for dependent objects!") ;
			die() ;
			break ;
		}
		FDbg::end() ;
		return $_reply ;
	}
}

?>
