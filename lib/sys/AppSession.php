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
 * AppSession.php
 * ===========
 *
 * Path:	lib/sys/
 *
 * Product id.:
 * Version:
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2013-10-21	PA1		khw		created this class;
 * 2013-10-22					verify on provided SessionId that data has not
 * 								been tempered with, through a checksum;
 *
 * ToDo
 *
 * Date			what
 * ----------------------------------------------------------------------------
 *
 * @package		masLibraryGlobal
 * @subpackage	System
 * @author		khwelter
 *
 */
class	AppSession	extends	AppObjectCore	{
	/**
	 *
	 */
	function	__construct() {
		FDbg::begin( 101, "AppSession.php", "AppSession", "__construct()") ;
		parent::__construct( "AppSession", "SessionId", "appSys") ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setKey( $_key) {
		parent::setKey( $_key) ;
		if ( $this->_valid) {
//			if ( $this->Checksum != $this->getChecksum())
//				$this->_valid	=	false ;
		} else {
			$this->storeInDb() ;
		}
	}
	/**
	 *
	 */
	function	getChecksum() {
		return md5( $this->SessionId . $this->ClientId . $this->ApplicationSystemId . $this->ApplicationId . $this->SysUserId) ;
	}
}
