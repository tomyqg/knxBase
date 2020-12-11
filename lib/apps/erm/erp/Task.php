<?php

/**
 * BDocument.php Base class for PDF-format printed matters
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package WTA
 */

/**
 * requires the WTF Debugger and the Base Class
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/DbObject.php") ;
require_once( "base/AppObject.php") ;
require_once( "base/AppDepObject.php") ;
require_once( "MimeMail.php" );
require_once( "XmlTools.php" );
/**
 * Task - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BTask which should
 * not be modified.
 *
 * @package WTA
 * @subpackage Article
 */

class	Task	extends	AppObject	{

	const	STATNEW	= 0 ;
	const	STATONG	=  50 ;
	const	STATWTG	=  55 ;
	const	STATCLS	=  90 ;
	private	static	$rTaskStat	=	array (
						Task::STATNEW => "New",
						Task::STATONG => "Ongoing",
						Task::STATWTG => "Waiting",
						Task::STATCLS => "Closed"
					) ;
	const	FINSTATCLN	= 0 ;
	const	FINSTATNEW	= 10 ;
	const	FINSTATFWD	= 20 ;
	const	FINSTATFIN	= 30 ;
	const	FINSTATCAN	= 40 ;
	private	static	$rTaskFinStat	=	array (
						Task::FINSTATCLN => "",
						Task::FINSTATNEW => "New",
						Task::FINSTATFWD => "Forwarded",
						Task::FINSTATFIN => "Finished",
						Task::FINSTATCAN => "Cancelled"
					) ;
	const	PRIO_A	=  0 ;
	const	PRIO_B	= 10 ;
	const	PRIO_C	= 20 ;
	private	static	$rTaskPrio	=	array (
						Task::PRIO_A => "A",
						Task::PRIO_B => "B",
						Task::PRIO_C => "C"
					) ;
					/*
	 * The constructor can be passed an ArticleNr (TaskNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_taskNr="") {
		AppObject::__construct( "Task", "TaskNr") ;
		if ( strlen( $_taskNr) > 0) {
			$this->setTaskNr( $_taskNr) ;
		} else {
		}
	}
	function	setTaskNr( $_myTaskNr) {
		FDbg::get()->dump( "Task.php::Task::setTaskNr('%s')", $_myTaskNr) ;
		$this->TaskNr	=	$_myTaskNr ;
		if ( strlen( $_myTaskNr) > 0) {
			$this->reload() ;
		}
		FDbg::get()->dump( "Task.php::Task::setTaskNr( '%s') is done", $_myTaskNr) ;
	}
	function	add( $_key="", $_id=-1, $_val="") {
		$this->newKey( 6) ;
		$this->Priority	=	Task::PRIO_B ;
		$this->DateReg	=	$this->today() ;
		$this->updateInDb() ;
		return $this->getXMLComplete() ;
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
	function	getRTaskStat() {
		return self::$rTaskStat ;
	}
	function	getRTaskFinStat() {
		return self::$rTaskFinStat ;
	}
	function	getRTaskPrio() {
		return self::$rTaskPrio ;
	}
}

?>
