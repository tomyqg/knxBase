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
 * Include Dateien
 */
class Jobs extends	FDbObject	{

	private	static	$rSchedule	=	array (
						"hourly"	=> "hourly",
						"daily"		=> "daily",
						"weekly"	=> "weekly",
						"weeklyMon"	=> "weeklyMon",
						"weeklyTue"	=> "weeklyTue",
						"weeklyWed"	=> "weeklyWed",
						"weeklyThu"	=> "weeklyThu",
						"weeklyFri"	=> "weeklyFri",
						"weeklySat"	=> "weeklySat",
						"weeklySun"	=> "weeklySun",
						"monthly"	=> "monthly",
						"interval"	=> "interval"
						) ;
	const	WAITING	=	0 ;
	const	PAUSED	=	5 ;
	const	RUNNING	=	9 ;
	private	static	$rStatus	=	array (
						Jobs::WAITING	=> "waiting",
						Jobs::PAUSED	=> "paused",
						Jobs::RUNNING	=> "running",
					) ;


	function	__construct() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."", "__construct()") ;
		parent::__construct( "Jobs", "Id", "appSys") ;
		FDbg::end() ;
	}
	function	add( $_key, $_id, $_val) {
		$this->getFromPostL() ;
		$this->storeInDb() ;
		return $this->getXMLString() ;
	}
	function	upd( $_key, $_id, $_val) {
		$this->getFromPostL() ;
		$this->updateInDb() ;
		return $this->getXMLString() ;
	}
	function	del( $_key, $_id, $_val) {
		$this->removeFromDb() ;
		return $this->getXMLString() ;
	}
	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		return $ret ;
	}
	function	getXMLString() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}

	function	getRSchedule() {	return self::$rSchedule ;		}
	function	getRStatus() {		return self::$rStatus ;			}

}

?>
