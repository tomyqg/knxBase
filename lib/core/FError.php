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
 * FError.php, wimtecc-Foundation Error-Manager
 *
 * This class implements a common error-manager.
 * @author Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package Application
 */

/**
 * FError implements a simple debugger for CLI or WebBased applications.
 *
 * @package Foundation
 * @subpackage ErrorManager
 */

class	FError {

	/**#@+
	 * Some private variables, don't have to be explained
	 */
	private	static	$errMod	=	"" ;
	private	static	$errNo	=	0 ;
	private	static	$errText	=	"" ;
	private	static	$inst	=	NULL;
	/**#@-*/

	//Konstruktor private, damit die Klasse nur aus sich selbst heraus instanziiert werden kann.
	private function __construct() {
		self::$errNo	=	0 ;
		self::$errText	=	"" ;
		self::$errMod	=	"" ;
	}

	/**
	 * Return the ONE AND ONLY instance of the debugger
	 *
	 * @return FError
	 */
	public	static	function get() {
		if (self::$inst === NULL) {
			self::$inst	=	new self ;
		}
		$buffer	=	sprintf( "FError: Mod='%s', ErrNo=%d, Error='%s'",
						self::$errMod,
						self::$errNo,
						self::$errText
					) ;
		return $buffer ;

	}

	/**
	 * Return the ONE AND ONLY instance of the debugger
	 *
	 * @return FError
	 */
	public	static	function clear() {
		if (self::$inst === NULL) {
			self::$inst	=	new self ;
		}
		self::$errNo	=	0 ;
		self::$errText	=	"" ;
		self::$errMod	=	"" ;
	}

	/**
	 * Set the debugger to Text-Mode (for CLI usage)
	 *
	 */
	public		function set( $_errMod, $_errNo, $_errText) {
		if (self::$inst === NULL) {
			self::$inst	=	new self ;
		}
		self::$errMod	=	$_errMod ;
		self::$errNo	=	$_errNo ;
		self::$errText	=	$_errText ;
	}

	// Klonen per 'clone()' von au�en verbieten.
	private function __clone() {}

}

?>
