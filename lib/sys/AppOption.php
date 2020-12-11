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
 * SysTrans - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BSysTrans which should
 * not be modified.
 *
 * @package Application
 * @subpackage SysTrans
 */
class	AppOption	extends	Option	{
	private	static	$dbAlias	=	"appSys" ;
	/*
	 * __construct
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		parent::__construct( self::$dbAlias, "AppOption") ;
		FDbg::end() ;
	}
	static	function	__setDbAlias( $_dbAlias) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_dbAlias')") ;
		self::$dbAlias	=	$_dbAlias ;
		FDbg::end() ;
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
