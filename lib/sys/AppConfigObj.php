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
 * ConfigObj - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BConfigObj which should
 * not be modified.
 *
 * @package Application
 * @subpackage ConfigObj
 */
class	AppConfigObj	extends	SysConfigObj	{
	/*
	 * __construct
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_db="appSys") {
		FDbg::begin( 101, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbObject::__construct( "AppConfigObj", "Id", $_db) ;
		FDbg::end() ;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 * @return null|Reply
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
			case	""	:
 				$_POST['_step']	=	$_val ;
				$filter	=	"1 = 1 " ;
				$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
				$myObj->addCol( "Id", "int") ;
				$myObj->addCol( "Class", "var") ;
				$myObj->addCol( "Section", "var") ;
				$myObj->addCol( "Parameter", "var") ;
				$myObj->addCol( "Value", "var") ;
				$reply->replyData	=	$myObj->tableFromDb( ",CONCAT( SUBSTRING( C.Value, 1, 20), \" ...\") AS Value ",
										" ",
										$filter,
										"ORDER BY C.Class ASC, C.Section ASC, C.Parameter ASC ",
										"AppConfigObj",
										"AppConfigObj",
										"C.Id, C.Class, C.Section, C.Parameter") ;
				break ;
		}
//		error_log( $ret) ;
		return $reply ;
	}
}
