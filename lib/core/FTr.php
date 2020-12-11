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
 * FTr.php - SysTranslator
 *
 * Fundamental class for text translation.
 *
 * @author Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package Platform
 * @filesource
 * @todo Document the file header after completion of the implementation.
 */
/**
 * FTr implements a simple translator
 *
 * @package Platform
 * @subpackage SysTranslator
 */
class	FTr {
	/**
	 *
	 */
	private	static	$inst	=	null ;
	private	static	$locale	=	"" ;
	private	static	$actSysTrans	=	null ;
	private	static	$actAppTrans	=	null ;
	/**
	 * __construct
	 *
	 * Instantiates 'the' object for the given translator. There can be only a single instance for
	 * the translator.
	 */
	private function __construct( $_locale='en') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_locale')") ;
		FDbg::end() ;
	}
	/**
	 * tr
	 *
	 * SysTranslates the given string
	 *
	 * @return FDb
	 */
	public	static	function init( $_locale='en') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_locale')") ;
		self::$locale	=	$_locale ;
		self::$actSysTrans	=	new SysTrans() ;
		if ( !isset( self::$inst)) {
			self::$inst	=	new self( $_locale) ;
		}
		FDbg::end() ;
	}
	/**
	 *
	 */
	public	static	function	enableAppTrans() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		self::$actAppTrans	=	new AppTrans() ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	public	static	function	getLocale() {
		return self::$locale ;
	}
	/**
	 *
	 */
	public	static	function	getId( $_text) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		try {
			if ( self::$actAppTrans != null) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "App: Trying to translate '$_text' to default '" . self::$locale . "' ") ;
				self::$actAppTrans->setKeys( "trans", md5( $_text), self::$locale) ;
			} else {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Sys: Trying to translate '$_text' to default '" . self::$locale . "' ") ;
				self::$actSysTrans->setKeys( "trans", md5( $_text), self::$locale) ;
			}
		} catch ( Exception $e) {
			try {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Sys: Trying to translate '$_text' to default '" . self::$locale . "' ") ;
				self::$actSysTrans->setKeys( "trans", md5( $_text), self::$locale) ;
			} catch ( Exception $e) {
			}
		}
		FDbg::end() ;
		return self::$actAppTrans->Id ;
	}
	/**
	 * tr
	 *
	 * Translate a given $_text into the target language $_lang
	 *
	 * @param string	$_text		vollstaendiger Aufruf, ohne 'call', fuer die Stored Procedure
	 * @param string	$_helpText	Name der Ergenisvariable die dne Status der Stored Procedure beinhaltet
	 * @return string
	 */
	static	function	tr( $_text, $_para=null, $_lang='', $_helpText='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_text', <_para>, '$_lang', '$_helpText')") ;
		$translate	=	false ;
		$res	=	"" ;
		try {
			if ( self::$actAppTrans != null) {
				if ( $_lang == '') {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "App01: Trying to translate '$_text' to default '" . self::$locale . "' ") ;
					self::$actAppTrans->setKeys( "trans", md5( $_text), self::$locale) ;
				} else {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "App02: Trying to translate '$_text' to requested '$_lang' ") ;
					self::$actAppTrans->setKeys( "trans", md5( $_text), $_lang) ;
				}
				$res	=	self::$actAppTrans->Fulltext2 ;
			} else {
				if ( $_lang == '') {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Sys03: Trying to translate '$_text' to default '" . self::$locale . "' ") ;
					self::$actSysTrans->setKeys( "trans", md5( $_text), self::$locale) ;
				} else {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Sys04: Trying to translate '$_text' to requested '$_lang' ") ;
					self::$actSysTrans->setKeys( "trans", md5( $_text), $_lang) ;
				}
				$res	=	self::$actSysTrans->Fulltext2 ;
			}
		} catch ( Exception $e) {
			try {
				if ( $_lang == '') {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Sys05: Trying to translate '$_text' to default '" . self::$locale . "' ") ;
					self::$actSysTrans->setKeys( "trans", md5( $_text), self::$locale) ;
				} else {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Sys06: Trying to translate '$_text' to requested '$_lang' ") ;
					self::$actSysTrans->setKeys( "trans", md5( $_text), $_lang) ;
				}
				$res	=	self::$actSysTrans->Fulltext2 ;
			} catch ( Exception $e) {
				$translate	=	true ;
			}
		}
		if ( $translate && $res == "") {
			if ( self::$actAppTrans != null) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "AppTranslation not found for '$_text' in '$_lang'; will add to dbTable 'AppTrans' ") ;
				$res	=	$_text ;
				self::$actAppTrans->Fulltext	=	$_text ;
				self::$actAppTrans->Fulltext2	=	$_text ;
				self::$actAppTrans->storeInDb() ;
				if ( self::$actAppTrans->Language != "en") {
					self::$actAppTrans->Language	=	"en" ;
					try {
						self::$actAppTrans->reloadByKeys() ;
					} catch ( Exception $e) {
						self::$actAppTrans->storeInDb() ;
					}
				}
			} else {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "SysTranslation not found for '$_text' in '$_lang'; will add to dbTable 'SysTrans' ") ;
				$res	=	$_text ;
				self::$actSysTrans->Fulltext	=	$_text ;
				self::$actSysTrans->Fulltext2	=	$_text ;
				self::$actSysTrans->storeInDb() ;
				if ( self::$actSysTrans->Language != "en") {
//					self::$actSysTrans->Language	=	"en" ;
					self::$actSysTrans->storeInDb() ;
				}
			}
		}
		/**
		 * perform replacement of placeholder by values
		 */
		if ( $_para !== null) {
			if ( is_array( $_para)) {
				foreach ( $_para as $i => $v) {
					$pair	=	explode( ":", $v) ;
//					if ( ! isset( $pair[1]))
//						echo $_text ;
					$par	=	sprintf( $pair[0], $pair[1]) ;
					$newRes	=	str_replace( "#".($i+1), $par, $res) ;
					$res	=	$newRes ;
				}
			} else {
				$pair	=	explode( ":", $_para) ;
				$par	=	sprintf( $pair[0], $pair[1]) ;
//				if ( ! isset( $pair[1]))
//					echo $_text ;
				$newRes	=	str_replace( "#1", $par, $res) ;
				$res	=	$newRes ;
			}
		} else {
		}
		FDbg::end( $res) ;
		return $res ;
	}

	/**
	 * __clone
	 *
	 * Dummy method to prohibit cloning !
	 */
	private function __clone() {}

}

?>
