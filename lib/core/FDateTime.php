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
 * FDateTime
 *
 * Revision history
 *
 * Date			Rev.	Who		What
 * ----------------------------------------------------------------------------
 * 200x-xx-xx	PA1		???		inception;
 * 2015-12-17	PA2		khw		added header(s);
 *
 * To-Do
 *
 * Date			Who		What
 * ----------------------------------------------------------------------------
 */
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class FDateTime	{
	/**
	 * convert the given mySQL DateTime string to a unix timestamp
	 * @param	string	$mySqlDateTime	mySQL DateTime string
	 * @return	int		unix timestamp
	 */
	static	function	mySqlDateTimeToTime( $mySqlDateTime) {
		return strtotime( $mySqlDateTime) ;
	}
	/**
	 * convert the given mySQL DateTime string to a c# timestamp
	 * @param	string	$mySqlDateTime	mySQL DateTime string
	 * @return	int		c# timestamp
	 */
	static	function	mySqlDateTimeToTicks( $mySqlDateTime) {
		return $this->timeToTicks( strtotime( $mySqlDateTime)) ;
	}
	/**
	 * convert the given unix timestamp to a mySQL DateTime string
	 * @param	int		$time	unix timestamp
	 * @return	string	mySQL DateTime string
	 */
	static	function	timeToMySqlDateTime( $time) {
		return date("Y-m-d H:m:s", $time);
	}
	/**
	 * convert the given unix timestamp to a mySQL Date string
	 * @param	int		$time	unix timestamp
	 * @return	string	mySQL Date string
	 */
	static	function	timeToMySqlDate( $time) {
		return date("Y-m-d", $time);
	}
	/**
	 * convert the given unix timestamp to a mySQL Time string
	 * @param	int		$time	unix timestamp
	 * @return	string	mySQL Time string
	 */
	static	function	timeToMySqlTime( $time) {
		return date("H:m:s", $time);
	}
	/**
	 * convert the given c# timestamp to a unix timestamp
	 * @param	int		$ticks	c# timestamp
	 * @return	int		unix timestamp
	 */
	static	function	ticksToTime( $ticks) {
		return (($ticks - 621355968000000000) / 10000000) ;
	}
	/**
	 * convert the given unix timestamp to a c# timestamp
	 * @param	int		$time	unix timestamp
	 * @return	int		c# timestamp
	 */
	static	function	timeToTicks( $time) {
		return number_format(($time * 10000000) + 621355968000000000 , 0, '.', '') ;
	}
	/**
	 *
	 */
	static	function	getDayOfTheYear( $_time=null) {
		return idate( "z", ( $_time === null ? time() : $_time)) ;
	}
	/**
	 *
	 */
	static	function	getYear( $_time=null) {
		return idate( "Y", ( $_time === null ? time() : $_time)) ;
	}
}
?>
