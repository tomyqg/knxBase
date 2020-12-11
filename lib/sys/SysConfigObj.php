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
 * in-code documentation through DocSections and tags.
 * @author Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package Application
 */
/**
 * requires the WTF Debugger and the Base Class
 */
/**
 * SysConfigObj - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BSysConfigObj which should
 * not be modified.
 *
 * @package Application
 * @subpackage SysConfigObj
 */
class	SysConfigObj	extends	FDbObject	{
	/*
	 * __construct
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_db="sys") {
		parent::__construct( "SysConfigObj", "Id", $_db) ;
	}

	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @throws	Exception
	 * @return	Reply
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		$this->getFromPostL() ;
		$this->Parameter	=	$_POST['_IParameter'] ;
		$this->storeInDb() ;
		$ret	=	$this->getXMLString() ;
		return $ret ;
	}

	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @throws	Exception
	 * @return	Reply
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
		$this->getFromPostL() ;
		$this->updateInDb() ;
		return $this->getXMLString() ;
	}

	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @throws	Exception
	 * @return	Reply
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		$e	=	new Exception( "ConfObj.php::ConfObj::del(...): this type of object can not be deleted!") ;
		error_log( $e) ;
		throw $e ;
	}
	
	/**
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @param Reply
	 * @return null|Reply
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->className ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
		return $reply ;
	}
	
	/**
	 *
	 */
	protected	function	_postInstantiate() {
	}
	
	/**
	 *
	 */
	protected	function	_postLoad() {
	}
}

?>
