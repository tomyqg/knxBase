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
 * FException
 *
 * Revision history
 *
 * Date		Rev.	Who	What
 * ----------------------------------------------------------------------------
 * 200x-xx-xx	PA1	???	inception;
 * 2015-12-17	PA2	khw	added severity level and die() on level < 10;
 * 2016-06-01	PA3	khw	changed default severity (if parameter missing)
 *				to SEV_11 to NOT cause die() on default;
 *
 * To-Do
 *
 * Date			Who		What
 * ----------------------------------------------------------------------------
 */
class	FException	extends	Exception	{
	const	SEV_00	=	0 ;		// Severity Level 0-9 will die after issueing to error_log
	const	SEV_01	=	1 ;
	const	SEV_10	=	10 ;
	const	SEV_11	=	11 ;		// Severity Level 1
	protected	$file ;
	protected	$module ;
	protected	$method ;
	protected	$message ;
	/**
	 *
	 */
	public	function	__construct( $_file, $_module, $_method, $_mesg, $_code=0, $_ec1=-1, $_ec2=-1, $_sev=FException::SEV_11) {
		parent::__construct( $_mesg, $_code) ;
		$this->file	=	$_file ;
		$this->module	=	$_module ;
		$this->method	=	$_method ;
		$this->code	=	$_code ;
		$this->ec1	=	$_ec1 ;
		$this->ec2	=	$_ec2 ;
		$this->message	=	$_file . "::" . $_module . "::" . $_method .": " . $_mesg ;
		error_log( ">>>>> " . $this->message) ;
		if ( $_sev < FException::SEV_10) {
			error_log( ">>>>> will die() due to severity level [_sev=$_sev]") ;
			die() ;
		}
	}
	/**
	 *
	 */
	public	function	__toString() {
		return $this->message ;
	}
}
?>
