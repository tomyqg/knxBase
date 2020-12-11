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
 * @author		Karl-Heinz Welter <khwelter@icloud.com>
 * @version		0.1
 * @package		Application
 * @subpackage	Client
 * @filesource
 */
/**
 * Client - Base Class
 *
 */
class	Application	extends	AppObjectCore	{
	/**
	 * @var		$initialized		Speficies if static object has already been initialized
	 */
	private	static	$initialized	=	false ;
	/**
	 * @var		$runLevel			Run-Level of THIS application
	 */
	private	static	$runLevel		=	0 ;
	/**
	 * @var		$includePath		Include path of THIS application
	 */
	private	static	$includePath	=	"" ;
	/**
	 * __construct( $_myApplicationId)
	 *
	 * Create an instance of this class. If the $_applicationId is given an attempt is made to read
	 * the data from the sys-database.
	 *
	 * @param string $_myApplicationId	Id of application to load from database
	 * @throws Exception
	 */
	function	__construct( $_myApplicationId="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myApplicationId')") ;
		parent::__construct( "Application", array( "ApplicationId"), "sys") ;
		if ( ! self::$initialized) {
			if ( strlen( $_myApplicationId) > 0) {
				try {
					$this->setKey( $_myApplicationId) ;
					if ( $this->_valid) {
						self::$initialized	=	true ;
					}
				} catch ( Exception $e) {
					throw $e ;
				}
			} else {
			}
		} else {

		}
		FDbg::end( self::$initialized) ;
	}
	/**
	 * getList( $_key, $_id, $_val, $reply)
	 *
	 * Get list of objects.
	 *
	 * @param	string		$_key			Key of the application to work with
	 * @param	int			$_id			Id of the application to work with
	 * @param	mixed		$_val			Optional additional parameter
	 * @param	Reply		$reply			Reply where the result goes. If null
	 *										a new Reply object will be instantiated
	 * @return	Reply						Reply object containing the result
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
				$_POST['_step']	=	$_val ;
			/**
			 *
			 */
			$myObj	=	new Application() ;				// no specific object we need here
 			$myQuery	=	new FSqlSelect( "Application") ;
			$myQuery->addField( ["Id","ApplicationId","Description1"]) ;
			$myQuery->addOrder( ["ApplicationId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"APerClient"	:
			$_POST['_step']	=	$_val ;
			/**
			 *
			 */
			$where	=	array() ;
			if ( isset( $_POST['ClientId'])) {
				$where[]	=	"ClientId = \"" . $_POST['ClientId'] . "\"" ;
			}
			if ( isset( $_POST['ApplicationSystemId'])) {
				$where[]	=	"ApplicationSystemId = \"" . $_POST['ApplicationSystemId'] . "\"" ;
			}
			$myObj	=	new FDbObject( "ApplicationPerClient", "", "sys") ;		// no specific object we need here
 			$myQuery	=	$myObj->getQueryObj( "Select") ;
 			$myQuery->addOrder( ["ClientId"]) ;
			$myQuery->addWhere( $where) ;
			$reply->replyData	=	str_replace( "ApplicationPerClient", "Application", $myObj->tableFromQuery( $myQuery)) ;
			break ;
		}
//		error_log( $ret) ;
		FDbg::end() ;
		return $reply ;
	}
/**
 * Special Code:
 * =============
 */
	/**
	 * tr
	 *
	 * SysTranslates the given string
	 *
	 * @param	SysSession		$_session			Session to initialize this aplication from.
	 * @return FDb
	 */
	public	static	function init( $_session) {
		global	$includeBase ;
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <SysSession>)") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "Application.inc.php", "Application", "init", "new run-level := " . $_session->Validity) ;
		self::$runLevel	=	$_session->Validity ;
		switch ( $_session->Validity) {
			case	SysSession::INVALID	:
				break ;
			case	SysSession::VALIDLTD	:
				break ;
			case	SysSession::VALIDLOGIN	:
				/**
				 * re-establish the include path, now including the
				 * 	-	client-specific and
				 * 	-	application-system-specific
				 * pathes.
				 */
				$pathI	=	""
						.	$includeBase . "/clients/".$_session->ClientId ."/lib/".$_session->ApplicationSystemId."/".$_session->ApplicationId. PATH_SEPARATOR
						.	$includeBase . "/clients/".$_session->ClientId ."/lib/".$_session->ApplicationSystemId . PATH_SEPARATOR
						.	$includeBase . "/clients/".$_session->ClientId ."/lib" . PATH_SEPARATOR
						.	$includeBase . "/lib/apps/".$_session->ApplicationSystemId."/".$_session->ApplicationId. PATH_SEPARATOR
						.	$includeBase . "/lib/apps/".$_session->ApplicationSystemId . PATH_SEPARATOR
						.	$includeBase . "/lib/apps"
								;
				/**
				 * prepend this path so it gets priority over the already defined include pathes
				 */
				$includePath	=	$pathI . PATH_SEPARATOR . get_include_path() ;
				FDbg::trace( 0, FDbg::mdTrcInfo1, "config_app.inc.php", "*", "main", "includePath := '$includePath'") ;
				if ( ! set_include_path( $includePath)) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, "config_app.inc.php", "*", "main", "failed to set include path! FATAL!");
					die() ;
				}
				$mySysConfig	=	EISSCoreObject::__getSysConfig() ;
				$mySysConfig->addFromSysDb( "def", $_session->ApplicationSystemId, $_session->ApplicationId, $_session->ClientId) ;
				if ( isset( $mySysConfig->def)) {
					FDb::registerDb( $mySysConfig->def->dbHost, $mySysConfig->def->dbUser, $mySysConfig->def->dbPassword, $mySysConfig->def->dbName, $mySysConfig->def->dbAlias) ;
				}
				if ( isset( $mySysConfig->appSys)) {
					FDb::registerDb( $mySysConfig->appSys->dbHost, $mySysConfig->appSys->dbUser, $mySysConfig->appSys->dbPassword, $mySysConfig->appSys->dbName, $mySysConfig->appSys->dbAlias, $mySysConfig->appSys->dbDriver, $mySysConfig->appSys->dbPrefix) ;
				}
				break ;
			case	SysSession::VALIDAPP	:
				break ;
		}
		FDbg::end( 0) ;
	}
	/**
	 * powerOn()
	 *
	 * Perform activities to make this applicatrion run.
	 *
	 * @param	string		$_currIncludePath		Include path to remember when Application can not power up
	 * @return 	FDb
	 */
	static	function	powerOn( $_currIncludePath) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <SysSession>)") ;
		error_log( "Application::powerOn()") ;
		self::$runLevel	=	0 ;
		self::$includePath	=	$_currIncludePath ;
		FDbg::end() ;
	}
}
?>
