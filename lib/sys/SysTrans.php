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
 * SysTrans.php - Class for dealing with translations of arbitrary text from teh system database
 *
 * 'SysTrans' is
 *
 * @author Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package Application
 * @subpackage Trans
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
class	SysTrans	extends	Trans	{
	private	static	$dbAlias	=	"sys" ;
	/*
	 * The constructor can be passed an ArticleNr (SysTransNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_name='', $_refNr='', $_sprache='en_US') {
		parent::__construct( self::$dbAlias, "SysTrans", $_name, $_refNr, $_sprache) ;
		if ( strlen( $_name) > 0) {
			try {
				$this->setKeys( $_name, $_refNr, $_sprache) ;
			} catch ( Exception $e) {
			}
		}
	}
	/**
	 *
	 */
	static	function	__setDbAlias( $_dbAlias) {
		self::$dbAlias	=	$_dbAlias ;
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
