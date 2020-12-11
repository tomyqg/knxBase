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
 * SysSession.php
 * ===========
 *
 * Path:	lib/sys/
 *
 * Product id.:
 * Version:
 *
 * Revision history
 *
 * Date		Rev.	Who	what
 * ----------------------------------------------------------------------------
 * 2013-10-21	PA1	khw	created this class;
 * 2013-10-22			verify on provided SessionId that data has not
 * 				been tempered with, through a checksum;
 * 2016-07-31	PA2	khw	added support for "automatic" referesh task;
 *				added support for session storage;
 *				added history comment;
 * ToDo
 *
 * Date		what
 * ----------------------------------------------------------------------------
 *
 * @package	wap
 * @subpackage	database
 * @author	khwelter
 *
 */
const debugSysSessionObject	= false ;
class	SysSession	extends	FDbObject	{
	/**
	 * constants defining the validity of THIS session;
	 * only SysSession::VALIDAPP means the user is fully authorized!
	 */
	const	INVALID		= 0 ;	// session is invalid => present login screen
	const	INVAULOFF	= 1 ;	// session id invalid due to automatic logoff due to idle time
	const	VALID02		= 2 ;	// unused
	const	VALIDLTD	= 3 ;	// session is valid only for login screen or limited query of system data
					// no authorized user;
					// $this->SysUser contains no valid reference
					// $this->AppUser contains no valid reference
	const	VALID04		= 4 ;	// unused
	const	VALIDLOGIN	= 5 ;	// session is valid only for login screen or limited query if system or
					// application data, no authorized user;
					// $this->SysUser contains __appuser__ reference
					// $this->AppUser contains no valid reference
	const	VALID06		= 6 ;	// unused
	const	VALIDJOBS	= 7 ;	// session is valid only for execution of background jobs
	const	VALID08		= 8 ;	// unused
	const	VALIDAPP	= 9 ;	// session is valid for application level functions:
					// user is properly authorized with ClientId, ApplicationSystemId, ApplicationId;
					// $this->SysUser contains a valid reference
					// $this->AppUser contains a valid reference
	/**
	 * these are THIS' static attributes
	 */
		static	$data	=	array() ;
	/**
	 * these are THIS' public variables
	 */
//		public	$SessionId	=	"" ;
//		public	$IPAddress	=	"" ;
//		public	$Checksum	=	"" ;
//		public	$LastAccess	=	"" ;
	public		$SysUser	=	"" ;
	public		$AppUser	=	"" ;
//	public		$Validity	=	SysSession::INVALID ;
	/**
	 * __construct()
	 *
	 * instantiates THIS instance, presets attributes and determines the session status
	 * based on GET/POST variables;
	 * any user needs to use (and consider) the status of THIS newly created object by
	 * comparing *variable->Validity against the values as defined
	 *
	 * @return SysSession	a reference to the oibject just created
	 */
	public	function	__construct() {
		FDbg::begin( 11, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		parent::__construct( "SysSession", "SessionId", "sys") ;
		$this->Validity	=	SysSession::INVALID ;
		$this->SysUser	=	null ;					// no valid system user
		$this->AppUser	=	null ;				// no valid application user
		$this->getStatus() ;
		FDbg::end( "Validity := " . $this->Validity) ;
	}
	/**
	 *
	 */
	public	function	setKey( $_key) {
		FDbg::begin( 11, basename( __FILE__), __CLASS__, __METHOD__."( '$_key')") ;
		parent::setKey( $_key) ;
		if ( $this->_valid) {
			if ( $this->Checksum != $this->getChecksum())
				$this->_valid	=	false ;
		}
		FDbg::end( "Validity := " . $this->Validity) ;
	}
	/**
	 * getStatus()
	 *
	 * tries to determine the status of a session.
	 *
	 * 	IF a session identifier has been supplied we try to find this session in the database.
	 * 		IF the session is valid
	 * 			everything is fine
	 * 		ELSE
	 * 			devalidate the session
	 * 		END
	 *	ELSEIF a user id has been supplied (trying to login)
	 *		try to authorize the user against the requested ApplicationSystem/Application
	 *		IF user authorized
	 *			populate the session
	 *		ELSE
	 *			devalidate the session
	 *		ENDIF
	 *	ELSEIF a default user has been defined by means of appConfig
	 *		populate the session
	 *	ELSE
	 *		devalidate the session
	 *	ENDIF
	 *	write session to database
	 */
	public	function	getStatus() {
		FDbg::begin( 11, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$mySysConfig	=	EISSCoreObject::__getSysConfig() ;
		$this->_valid	=	false ;
		if ( isset( $_REQUEST[ 'sessionId'])) {
			/**
			 * give limited session Validity to login requests
			 */
			FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "trying available sessionId '".$_REQUEST[ 'sessionId']."' ") ;
			$myDT	=	new DateTime() ;
			$this->setKey( $_REQUEST[ 'sessionId']) ;
			if ( $_REQUEST[ 'sessionId'] == "login") {
				FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "available session indicates enquiry!") ;
				$this->Validity	=	SysSession::VALIDLTD ;
			} else if ( $this->_valid && ! isset( $_REQUEST[ "logoff"]) && ( $myDT->getTimestamp() - $this->LastAccess) <= $mySysConfig->session->timeout) {
				FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "available session is ok for ".( $myDT->getTimestamp() - $this->LastAccess)." seconds") ;
				$this->SysUser	=	new SysUser() ;
				$this->SysUser->setKey( $this->SysUserId) ;
				$this->ClientId	=	$this->ClientId ;
				$this->ApplicationSystemId	=	$this->ApplicationSystemId ;
				$this->ApplicationId	=	$this->ApplicationId ;
				$autoCall	=	(( isset( $_REQUEST[ "IsAutoCall"]) ? $_REQUEST[ "IsAutoCall"] : "false") == "true") ;
				if ( ! $autoCall) {
					$this->LastAccess	=	$myDT->getTimestamp() ;
					$this->updateColInDb( "LastAccess") ;
				}
				$this->restore() ;
			} else if ( $this->_valid && ! isset( $_REQUEST[ "logoff"]) && ( $myDT->getTimestamp() - $this->LastAccess) > $mySysConfig->session->timeout) {
				FDbg::trace( 0, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "available session is ok but has expired!") ;
				$this->SysUser	=	new SysUser() ;
				$this->SysUser->setKey( $this->SysUserId) ;
				$this->ClientId	=	$this->ClientId ;
				$this->ApplicationSystemId	=	$this->ApplicationSystemId ;
				$this->ApplicationId	=	$this->ApplicationId ;
				$this->LastAccess	=	$myDT->getTimestamp() ;
				$this->Validity	=	SysSession::INVAULOFF ;
				$this->killSysSession() ;
			} else {
				$this->Validity	=	SysSession::INVALID ;
				$this->killSysSession() ;
			}
		} else if ( isset( $_REQUEST['UserId'])) {
			FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "trying login") ;
			try {
				$this->SysUser	=	new SysUser() ;
				/**
				 * verify password
				 */
				if ( $this->SysUser->identify( $_REQUEST['Password'], $_REQUEST['UserId'])) {
					FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "password ok!") ;
					/**
					 * verify ClientApplication is allowed for this user
					 */
					$myClientApplication	=	new ClientApplication() ;
 					$myClientApplication->setComplexKey( array( $_REQUEST['ClientId'], $_REQUEST['UserId'], $_REQUEST['ApplicationSystemId'], $_REQUEST['ApplicationId'])) ;
					if ( $myClientApplication->_valid) {
						FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Applicationsystem/Application available to this user!") ;
						$this->ClientId	=	$_REQUEST['ClientId'] ;
						$this->ApplicationSystemId	=	$_REQUEST['ApplicationSystemId'] ;
						$this->ApplicationId	=	$_REQUEST['ApplicationId'] ;
						$this->SysUserId	=	$_REQUEST['UserId'] ;
						$this->AppUserId	=	$_REQUEST['UserId'] ;
						$this->Status	=	1 ;
						$this->Validity	=	SysSession::VALIDAPP ;
						$this->createSysSession( "12345") ;
					} else {
						FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Applicationsystem/Application un-available to this user!") ;
						$this->Validity	=	SysSession::INVALID ;
						$this->SysUser	=	null ;
					}
				} else {
					FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "SysUser not identified, trying AppUser!") ;
					/**
					 *	check if the application maintains an own user database
					 */
					$myClientApplication	=	new ClientApplication() ;
 					$myClientApplication->setComplexKey( array( $_REQUEST['ClientId'], "__appuser__", $_REQUEST['ApplicationSystemId'], $_REQUEST['ApplicationId'])) ;
					if ( $myClientApplication->_valid) {
						FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Checking AppUser!") ;
						$this->ClientId	=	$_REQUEST['ClientId'] ;
						$this->ApplicationSystemId	=	$_REQUEST['ApplicationSystemId'] ;
						$this->ApplicationId	=	$_REQUEST['ApplicationId'] ;
						$this->Validity	=	SysSession::VALIDLOGIN ;
						Application::init( $this) ;
						$this->AppUser	=	new AppUser() ;
						FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "this application allows for local users!") ;
						if ( $this->AppUser->identify( $_REQUEST['Password'], $_REQUEST['UserId'])) {
							FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Applicationsystem/Application available to this user!") ;
							$this->ClientId	=	$_REQUEST['ClientId'] ;
							$this->ApplicationSystemId	=	$_REQUEST['ApplicationSystemId'] ;
							$this->ApplicationId	=	$_REQUEST['ApplicationId'] ;
							$this->SysUserId	=	"__appuser__" ;
							$this->AppUserId	=	$this->AppUser->UserId ;
							$this->Status	=	1 ;
							$this->Validity	=	SysSession::VALIDAPP ;
							$this->createSysSession( "12345") ;
						} else {
							FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "invalid user or wrong password!") ;
							$this->Validity	=	SysSession::INVALID ;
							$this->SysUser	=	null ;
						}
					} else {
						FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "this application does NOT allow for local users!") ;
					}
				}
			} catch ( Exception $e) {
				error_log( "problem with login credentials!") ;
				die() ;
			}
//		} else if ( isset( $this->defaultSysUser)) {
//			FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "trying defaultSysUser") ;
//			$this->Validity	=	SysSession::VALIDLTD ;
		} else if ( isset( $_REQUEST[ "logoff"])) {
			FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "logging off") ;
			$this->Validity	=	SysSession::VALIDLTD ;
		} else {
			FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "call with invalid context") ;
		}
		if ( $this->Validity != SysSession::VALIDAPP) {
			FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "SessionId is INVALID!!!") ;
		}
		FDbg::trace( 13, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "available session is ok!") ;
		FDbg::end( "Validity := " . $this->Validity) ;
	}
	/**
	 * createSysSession
	 *
	 * presets THIS session with data and writes THIS session to the database
	 *
	 * @param	string	$_key
	 * @return	void
	 */
	private	function	createSysSession( $_key) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key')") ;
		$myDT	=	new DateTime() ;
		$this->SessionId	=	MD5( $_key . ">" . $_SERVER['REMOTE_ADDR'] . ":" . date("Y-m-d-H-i-s") ) ;
		$this->IPAddress	=	 $_SERVER['REMOTE_ADDR'] ;
		$this->Checksum	=	$this->getChecksum() ;
		$this->LastAccess	=	$myDT->getTimestamp() ;
		$this->storeInDb() ;
		FDbg::end() ;
	}
	/**
	 * killSysSession()
	 *
	 * devalidate THIS session and update THIS session record in the database
	 *
	 * @return	void
	 */
	private	function	killSysSession() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$this->LastAccess	=	-1 ;
		$this->updateInDb() ;
		FDbg::end( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
	}
	/**
	 * getChecksum()
	 *
	 * calculate the MD5 checksum over own attributes: SessionId+ClientId+ApplicationSystemId+ApplicationId+SysUserId
	 *
	 * @return	string	MD5 checksum
	 */
	private	function	getChecksum() {
		return md5( $this->SessionId . $this->ClientId . $this->ApplicationSystemId . $this->ApplicationId . $this->SysUserId) ;
	}
	/**
	 * save
	 *
	 * save the data-array as a temporary file for THIS sessions user id
 	 *
	 * @return	void
	 */
	public	function	save() {
		if ( isset( $this->SysUser)) {
			if ( debugSysSessionObject)		error_log( "--------------------------SAVE-----------------------------------") ;
			if ( debugSysSessionObject)		error_log( "this->SysUser .... : " . print_r( $this->SysUser, true)) ;
			$tmpfname	=	sys_get_temp_dir() . "mas_" . $this->SysUser->UserId ;
			if ( debugSysSessionObject)		error_log( "tmpfname ......... : " . $tmpfname) ;
			$handle	=	fopen($tmpfname, "w", 0644);
			fwrite($handle, serialize( self::$data));
			fclose($handle);
			if ( debugSysSessionObject)		error_log( print_r( self::$data, true)) ;
			if ( debugSysSessionObject)		error_log( "--------------------------SAVE-----------------------------------") ;
		} else {
			if ( debugSysSessionObject)		error_log( "--------------------------SAVE-NO-TARGET-------------------------") ;
		}
	}
	/**
	 * restore
	 *
	 * restore the data-array from a temporary file for THIS sessions user id
 	 *
	 * @return	void
	 */
	public	function	restore() {
		if ( debugSysSessionObject)		error_log( "--------------------------RESTORE-----------------------------------") ;
		$tmpfname	=	sys_get_temp_dir() . "/mas_" . $this->SysUser->UserId ;
		if ( file_exists ( $tmpfname)) {
			$handle = fopen($tmpfname, "r", 0644);
			self::$data	=	unserialize( fread($handle, 65536));
			fclose($handle);
			if ( debugSysSessionObject)		error_log( print_r( self::$data, true)) ;
		}
		if ( debugSysSessionObject)		error_log( "--------------------------RESTORE-----------------------------------") ;
	}
	
	/**
	 *
	 */
	protected	function	_postInstantiate() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}
	
	/**
	 *
	 */
	protected	function	_postLoad() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}
}
?>
