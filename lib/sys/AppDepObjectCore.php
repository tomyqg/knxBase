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
 * AppDepObject - Basis Klasse
 *
 * @author Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package wtcAppERPBaseObjects
 * @filesource
 */
/**
 * AppDepObject - Basic class for dependent (secondary) objects
 *
 * @package wtcAppERPBaseObjects
 * @subpackage AppObjects
 */
class	AppDepObjectCore	extends	FDbObject	{
	private	$baseQuery ;
	/**
	 *
	 */
	function	__construct( $_className, $_keyCol) {
		parent::__construct( $_className, $_keyCol) ;
	}
	/**
	 * methods: add/upd/copy/del
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		$e	=	new Exception( "AppObject.php::AppObject::add( '$_key', $_id, '$_val'): must be defined in derived class (".$this->className.")!") ;
		error_log( $e->getMessage()) ;
		throw $e ;
	}
	function	upd( $_key="", $_id=-1, $_val="") {
		$e	=	new Exception( "AppObject.php::AppObject::upd( '$_key', $_id, '$_val'): must be defined in derived class (".$this->className.")!") ;
		error_log( $e->getMessage()) ;
		throw $e ;
	}
	function	del( $_key="", $_id=-1, $_val="") {
		$e	=	new Exception( "AppObject.php::AppObject::del( '$_key', $_id, '$_val'): must be defined in derived class (".$this->className.")!") ;
		error_log( $e->getMessage()) ;
		throw $e ;
	}
}

?>
