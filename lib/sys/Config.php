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
 * requires mostly platform stuff
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * --------------------------------------------
 * 2013-05-18	PA1		khw		added to rev. control
 *
 * @author	Karl-Heinz Welter <khw@wimtecc.com>
 * @version 0.1
 * @package wtcCore
 * @filesource
 */
/**
 *
 * @author miskhwe
 *
 */
class	Config	extends	EISSCoreObject	{
	/**
	 * __construct
	 *
	 * Instantiate an object of the class and read the php-compliant configuration file $_filename.
	 *
	 * @param string	$_filename
	 */
	function	__construct( $_filename="") {
		parent::__construct( "Config") ;
		$this->_valid	=	false ;
		$this->addFromFile( $_filename) ;
	}
	/**
	 * addFromFile
	 *
	 * Read a php-compliant configuration file and assign all
	 * @param string $_filename
	 */
	function	addFromFile( $_filename="") {
		if ( $_filename != "") {
			error_log( "reading config file " . $_filename) ;
			$appConf	=	parse_ini_file( $_filename, true) ;
			if ( $appConf !== false) {
				foreach ( $appConf as $section => $values) {
					if ( ! isset( $this->$section)) {
						$this->$section	=	new EISSCoreObject() ;
					}
					foreach ( $values as $name => $val) {
						$this->$section->$name	=	$val ;
					}
				}
			} else {
				error_log( "can not read config file " . $_filename) ;
			}
		}
	}

	function _assignObject( $_data) {
		$section	=	$_data->Section ;
		$name	=	$_data->Parameter ;
		if ( $section != "" && $name != "") {
			$val	=	$_data->Value ;
			if ( ! isset( $this->$section)) {
				$this->$section	=	new EISSCoreObject() ;
			}
			if ( $val == "false" || $val == "no") {
				$this->$section->$name	=	false ;
			} else  if ( $val == "true" || $val == "yes") {
				$this->$section->$name	=	true ;
			} else {
				$this->$section->$name	=	$val ;
			}
		} else {
//			error_log( "Config.php::addFromSysDb(): invalid section and/or parameter name in config-db") ;
		}
	}


	function	addFromSysDb( $_class="", $_applicationSystemId="", $_applicationId="", $_clientId="") {
		/**
		 *
		 */
		$myDbConfig	=	new SysConfigObj() ;
		$myDbConfig->setIterCond( "Class = '".$_class."' ") ;
		if ( $_applicationSystemId != "") {
			$myDbConfig->setIterCond( "ApplicationSystemId = '".$_applicationSystemId."' ") ;
		}
		if ( $_applicationId != "") {
			$myDbConfig->setIterCond( "ApplicationId = '".$_applicationId."' ") ;
		}
		if ( $_clientId != "") {
			$myDbConfig->setIterCond( "ClientId = '".$_clientId."' ") ;
		}
		$myDbConfig->setIterOrder( [ "Section ASC", "Parameter ASC"]) ;
		foreach ( $myDbConfig as $idx => $data) {
			$this->_assignObject( $data) ;
		}
	}

	/**
	 *
	 * @param string $_class
	 * @param string $_applicationSystemId
	 * @param string $_applicationId
	 * @param string $_clientId
	 * @throws Exception
	 */
	function	addFromAppDb( $_class="", $_applicationSystemId="", $_applicationId="", $_clientId="") {
		/**
		 *
		 */
		$iterCond	=	"Class = '" . $_class . "' " ;
		if ( isset( $this->def->appConfigAlias)) {
			$myDbConfig	=	new AppConfigObj( $this->def->appConfigAlias) ;
		} else {
			$myDbConfig	=	new AppConfigObj() ;
		}
		$myDbConfig->setIterCond( "Class = '".$_class."' ") ;
		if ( $_applicationSystemId != "") {
			$myDbConfig->setIterCond( "ApplicationSystemId = '".$_applicationSystemId."' ") ;
		}
		if ( $_applicationId != "") {
			$myDbConfig->setIterCond( "ApplicationId = '".$_applicationId."' ") ;
		}
		if ( $_clientId != "") {
			$myDbConfig->setIterCond( "ClientId = '".$_clientId."' ") ;
		}
		$myDbConfig->setIterOrder( [ "Section ASC", "Parameter ASC"]) ;
		foreach ( $myDbConfig as $idx => $data) {
			$this->_assignObject( $data) ;
		}
	}
	/**
	 *
	 * @param $_name
	 * @throws Exception
	 * @return value
	 */
	function	__get( $_name) {
		$myParameter	=	new FDbObject( "SysConfigObj", "Parameter") ;
		$myParameter->Parameter	=	$_name ;
		$myParameter->fetchFromDb() ;
		if ( $myParameter->_valid) {
		} else {
			$e	=	new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_name')", "Exception: no parameter named '$_name' found in configuration table!") ;
			throw $e ;
		}
		return $myParameter->Value ;
	}
	/**
	 *
	 */
	function	dFump( $_lead="") {
		$myData	=	get_object_vars( $this) ;
		foreach ( $myData as $section => $values) {
			if ( is_array( $values)) {
				foreach ( $values as $name => $val) {
					error_log( "appConf[$section:$name] :=> '$val'") ;
				}
			} else if ( is_object( $values)) {
				$values->dump( "Config.", true, false) ;
			} else {
				error_log( " OBJECT => " . $section . " => " . $values) ;
			}
		}
	}
	/**
	 *
	 */
	function	write( $_filename="/tmp/test.ini") {
		$myFile	=	fopen( $_filename, "w") ;
		foreach ( $this as $section => $values) {
			fprintf( $myFile, "[$section]\n") ;
			foreach ( $values as $name => $val) {
				fprintf( $myFile, "$name = $val\n") ;
			}
		}
		fclose( $myFile) ;
	}
}
?>
