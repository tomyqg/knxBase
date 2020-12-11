<?php
/**
 * Copyright (c) 2015-2020 Karl-Heinz Welter
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
const debugSysSessionObject	= true ;
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
	const	VALIDFRM	= 8 ;	// session is valid for selection of any application which this
								// user is associated with
	const	VALIDAPP	= 9 ;	// session is valid for application level functions:
								// user is properly authorized with ClientId, ApplicationSystemId, ApplicationId;
								// $this->SysUser contains a valid reference
								// $this->AppUser contains a valid reference
	/**
	 * these are THIS' static attributes
	 */
	static	$data	=	array() ;
	const	LOG		=	false ;
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
	public	function	__construct( $_sessionId = "") {
		error_log( "SysSession::__construct() ...") ;
		parent::__construct( "SysSession", "SessionId", "sys") ;
		$this->Validity	=	SysSession::INVALID ;
		$this->SysUser	=	null ;					// no valid system user
		$this->AppUser	=	null ;				// no valid application user
		if ( $_sessionId != "") {
			$this->setKey( $_sessionId) ;
		} else {
			$this->createSysSession( "testkey") ;
		}
	}
	/**
	 *
	 */
	public	function	setKey( $_key) {
		parent::setKey( $_key) ;
		if ( $this->isValid()) {
			error_log( __CLASS__ . "::" . __METHOD__ . ": is valid ") ;
			if ( $this->Checksum != $this->getChecksum()) {
				error_log( __CLASS__ . "::" . __METHOD__ . ": invalidating") ;
				$this->invalidate() ;
			}
		}
		return $this->isValid() ;
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
	 * 			invalidate the session
	 * 		END
	 *	ELSEIF a user id has been supplied (trying to login)
	 *		try to authorize the user against the requested ApplicationSystem/Application
	 *		IF user authorized
	 *			populate the session
	 *		ELSE
	 *			invalidate the session
	 *		ENDIF
	 *	ELSEIF a default user has been defined by means of appConfig
	 *		populate the session
	 *	ELSE
	 *		invalidate the session
	 *	ENDIF
	 *	write session to database
	 */
	public	function	getStatus() {
		error_log( "SysSession::getStatus() ...") ;
		$mySysConfig	=	EISSCoreObject::__getSysConfig() ;
		$this->invalidate() ;
		if ( isset( $_REQUEST[ 'sessionId'])) {
			error_log( "sessionId =  := " . $_REQUEST[ 'sessionId'] . "/" . $this->Validity) ;
			/**
			 * give limited session Validity to login requests
			 */
			$myDT	=	new DateTime() ;
			$this->setKey( $_REQUEST[ 'sessionId']) ;
			if ( $_REQUEST[ 'sessionId'] == "login") {
				$this->Validity = SysSession::VALIDLTD;
			} else if ( $this->Validity == SysSession::VALIDFRM) {
				if ( isset( $_REQUEST['_key0'])) {
					error_log( "received missing data ...") ;
					$this->ClientId	=	$_REQUEST['_key0'] ;
					$this->UserId	=	$_REQUEST['_key1'] ;
					$this->ApplicationSystemId	=	$_REQUEST['_key2'] ;
					$this->ApplicationId	=	$_REQUEST['_key3'] ;
					$this->Validity	=	SysSession::VALIDAPP ;
					$this->updateInDb() ;
				}
			} else if ( $this->isValid() && ! isset( $_REQUEST[ "logoff"]) && ( $myDT->getTimestamp() - $this->LastAccess) <= $mySysConfig->session->timeout) {
				$this->SysUser	=	new SysUser() ;
				$this->SysUser->setKey( $this->SysUserId) ;
//				$this->ClientId	=	$this->ClientId ;
//				$this->ApplicationSystemId	=	$this->ApplicationSystemId ;
//				$this->ApplicationId	=	$this->ApplicationId ;
				$autoCall	=	(( isset( $_REQUEST[ "IsAutoCall"]) ? $_REQUEST[ "IsAutoCall"] : "false") == "true") ;
				if ( ! $autoCall) {
					$this->LastAccess	=	$myDT->getTimestamp() ;
					$this->updateColInDb( "LastAccess") ;
				}
				$this->restore() ;
			} else if ( $this->isValid() && ! isset( $_REQUEST[ "logoff"]) && ( $myDT->getTimestamp() - $this->LastAccess) > $mySysConfig->session->timeout) {
				error_log( $this->Client . "::" . $this->ApplicationSystemId . "::" . $this->ApplicationId) ;
				$this->SysUser	=	new SysUser() ;
				$this->SysUser->setKey( $this->SysUserId) ;
//				$this->ClientId	=	$this->ClientId ;
//				$this->ApplicationSystemId	=	$this->ApplicationSystemId ;
//				$this->ApplicationId	=	$this->ApplicationId ;
				$this->LastAccess	=	$myDT->getTimestamp() ;
				$this->Validity	=	SysSession::INVAULOFF ;
				$this->killSysSession() ;
			} else {
				$this->Validity	=	SysSession::INVALID ;
				$this->killSysSession() ;
			}
		} else if ( isset( $_REQUEST['UserId'])) {
			error_log( "no sessionID, UserId = " . $_REQUEST['UserId'] . "::" . $_REQUEST['Password']) ;
			try {
				$this->SysUser	=	new SysUser() ;
				/**
				 * verify password
				 */
				if ( $this->SysUser->identify( $_REQUEST['Password'], $_REQUEST['UserId'])) {
					$this->Validity	=	SysSession::VALIDAPP ;
					$this->SysUserId	=	$_REQUEST['UserId'] ;
					$this->AppUserId	=	$_REQUEST['UserId'] ;
					$this->ApplicationSystemId	=	$_REQUEST['ApplicationSystemId'] ;
					$this->ApplicationId	=	$_REQUEST['ApplicationId'] ;
					$this->createSysSession( "12345") ;
					error_log( "User was identified and session created ...") ;
				} else {
					$this->Validity	=	SysSession::INVALID ;
				}
			} catch ( Exception $e) {
				error_log( "problem with login credentials!") ;
				die() ;
			}
		} else if ( isset( $_REQUEST[ "logoff"])) {
			error_log("logging off") ;
			$this->Validity	=	SysSession::VALIDLTD ;
		} else {
			error_log("call with invalid context") ;
		}
	}
	
	/**
	 * createSysSession
	 *
	 * presets THIS session with data and writes THIS session to the database
	 *
	 * @param	string	$_key
	 * @return	void
	 */
	public	function	createSysSession( $_key) {
		$myDT	=	new DateTime() ;
		$this->SessionId	=	MD5( $_key . ">" . $_SERVER['REMOTE_ADDR'] . ":" . date("Y-m-d-H-i-s") ) ;
		$this->IPAddress	=	 $_SERVER['REMOTE_ADDR'] ;
		$this->Checksum	=	$this->getChecksum() ;
		$this->LastAccess	=	$myDT->getTimestamp() ;
		$this->storeInDb() ;
	}
	
	/**
	 * killSysSession()
	 *
	 * devalidate THIS session and update THIS session record in the database
	 *
	 * @return	void
	 */
	private	function	killSysSession() {
		$this->LastAccess	=	-1 ;
		$this->updateInDb() ;
	}
	/**
	 * updateChecksum()
	 *
	 * calculate the MD5 checksum over own attributes: SessionId+ClientId+ApplicationSystemId+ApplicationId+SysUserId
	 *
	 * @return	string	MD5 checksum
	 */
	public	function	updateChecksum() {
		if ( self::LOG) {
			error_log( __FILE__ . "::" . __METHOD__ . ": SessionId....... : " . $this->SessionId) ;
			error_log( __FILE__ . "::" . __METHOD__ . ": SessionId....... : " . $this->ClientId) ;
			error_log( __FILE__ . "::" . __METHOD__ . ": SessionId....... : " . $this->ApplicationSystemId) ;
			error_log( __FILE__ . "::" . __METHOD__ . ": SessionId....... : " . $this->ApplicationId) ;
			error_log( __FILE__ . "::" . __METHOD__ . ": SessionId....... : " . $this->SysUserId) ;
		}
		$this->Checksum = $this->getChecksum() ;
		return $this->isValid() ;
	}
	/**
	 * getChecksum()
	 *
	 * calculate the MD5 checksum over own attributes: SessionId+ClientId+ApplicationSystemId+ApplicationId+SysUserId
	 *
	 * @return	string	MD5 checksum
	 */
	private	function	getChecksum() {
		if ( self::LOG) {
			error_log( __FILE__ . "::" . __METHOD__ . ": SessionId....... : " . $this->SessionId) ;
			error_log( __FILE__ . "::" . __METHOD__ . ": SessionId....... : " . $this->ClientId) ;
			error_log( __FILE__ . "::" . __METHOD__ . ": SessionId....... : " . $this->ApplicationSystemId) ;
			error_log( __FILE__ . "::" . __METHOD__ . ": SessionId....... : " . $this->ApplicationId) ;
			error_log( __FILE__ . "::" . __METHOD__ . ": SessionId....... : " . $this->SysUserId) ;
		}
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
			$tmpfname	=	sys_get_temp_dir() . "mas_" . $this->SysUser->UserId ;
			$handle	=	fopen($tmpfname, "w", 0644);
			fwrite($handle, serialize( self::$data));
			fclose($handle);
		} else {
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
		$tmpfname	=	sys_get_temp_dir() . "/mas_" . $this->SysUser->UserId ;
		if ( file_exists ( $tmpfname)) {
			$handle = fopen($tmpfname, "r", 0644);
			self::$data	=	unserialize( fread($handle, 65536));
			fclose($handle);
		}
	}
	
	/**
	 *
	 */
	protected	function	_postInstantiate() {
	}
	
	/**
	 *
	 */
	protected	function	_postLoad() {
	}
}
?>
