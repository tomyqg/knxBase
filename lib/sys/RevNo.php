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
class	RevNo	{
	private	$revNo ;
	/**
	 *
	 * @param unknown_type $_revNo
	 */
	function	__construct( $_revNo="PA1") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_revNo)") ;
		$this->revNo	=	$_revNo ;
		FDbg::end() ;
	}
	/**
	 * steps a revision number to the next in sequence
	 */
	function	step() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <VOID>)") ;
		/**
		 * check if we are in a preliminary number or in a full release
		 */
		$matches	=	array() ;
		$myRevNo	=	$this->revNo ;
		/**
		 * if we are at full letter revision 'Z' throw an error. no revision beyond 'Z'
		 */
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()",
			"RevNo := '".$this->revNo."'") ;
		if ( $myRevNo == "") {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()",
				"path 1 ... ") ;
			$myNewRevNo	=	"PA1" ;
		} else if ( $myRevNo[0] == "Z") {
			$e	=	new Exception( __FILE__."::".__CLASS__."::".__METHOD__."(): can't step beyond 'Z'!") ;
			error_log( $e) ;
			throw $e ;
		} else if ( $myRevNo[0] == "P") {
			error_log( "Number is preliminary") ;
			preg_match( "/(P)([A-Z])([0-9].*)/", $myRevNo, $matches) ;
// 			foreach ( $matches as $i => $v) {
// 				error_log( " $i := $v") ;
// 			}
			$myNewRevNo	=	$matches[1] . $matches[2] . (intval( $matches[3]) + 1) ;
		} else {
			preg_match( "/([A-Z])/", $myRevNo, $matches) ;
// 			foreach ( $matches as $i => $v) {
// 				error_log( " $i := $v") ;
// 			}
			/**
			 * skip the letter 'P', might be confusing with the prefix 'P' for preliminary version
			 */
			if ( $matches[1] == "O")				// skip the "O", could be mistaken for 0
				$matches[1]	=	chr( ord( $matches[1])+1) ;
			if ( $matches[1] == "P")				// skip the "P", could be mistaken for Preliminary
				$matches[1]	=	chr( ord( $matches[1])+1) ;
			$myNewRevNo	=	"P" . chr( ord( $matches[1])+1) . "1" ;
		}
//		error_log( $myNewRevNo) ;
		$this->revNo	=	$myNewRevNo ;
		FDbg::end() ;
		return $myNewRevNo ;
	}
	/**
	 *
	 */
	function	release() {
		$matches	=	array() ;
		$myRevNo	=	$this->revNo ;
		preg_match( "/(P)([A-Z])([0-9].*)/", $myRevNo, $matches) ;
// 		foreach ( $matches as $i => $v) {
// 			error_log( " $i := $v") ;
// 		}
		$myNewRevNo	=	$matches[2] ;
//		error_log( $myNewRevNo) ;
		$this->revNo	=	$myNewRevNo ;
		return $myNewRevNo ;
	}
	/**
	 * check if the current revNo is valid
	 */
	function	check() {
		$res	=	false ;
	}
	/**
	 *
	 */
	function	getRevNo() {
		return $this->revNo ;
	}
	
	function __toString(){
		return $this->revNo ;
	}
}
?>
