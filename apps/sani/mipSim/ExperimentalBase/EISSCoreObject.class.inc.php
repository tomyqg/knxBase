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
 * EISSCoreObject - Central Core object for EISS
 *
 *  Revision history
 *
 * Date		Rev.	Who	what
 * ------------------------------------------------------------
 * 2013-05-18	PA1	khw	added to rev. control
 * 2016-06-02	PA2	khw	a lot of stuff in between;
 *
 * @author	Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package wapCore
 * @subpackage Core
 * @filesource	mas_r1/lib/core
 */
/**
 * FDbObject
 *
 * Summary before class line ...
 *
 * @author	Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @subpackage Core
 */
class	EISSCoreObject	{
    static	$globals	=	array() ;
    private	static	$sysUser	=	null ;
    private	static	$appUser	=	null ;
    private	static	$_appUser	=	null ;
    private	static	$sysConfig	=	null ;
    private	static	$appConfig	=	null ;
    private	$valid	=	false ;
    private	$status	=	0 ;
    private	$type	=	"undefined" ;
    public	$date	=	"" ;
    public	static	$err	=	array() ;
    public	$StatusInfo	=	"" ;
    /**
     * __constructor( $_type)
     *
     * Instantiate an object and initialize some basic attributes.
     *
     * @param	string		type		A type-name for the object
     */
    function	__construct( $_type="void") {
       $this->type	=	$_type ;
        $this->date	=	self::today() ;
    }
    static	function	remapGET() {
        foreach ( $_GET as $key => $value) {
            if ( ! isset( $_POST[ $key])) {
                $_POST[ $key]	=	$value ;
            } else {
                error_log( "EISSCoreObject.php::EISSCoreObject::remapGET(): can't remap $key from _GET as it exists already in _POST") ;
                die() ;
            }
        }
    }
    /**
     *	__get()
     *
     * Magic retrieval of so far undefined value.
     *
     * This method will only be called upon access of a non-existing attribute of $this
     * Object.
     *
     * First, it is checked if EISSCoreObject has a valid link to a sysConfig-object.
     * If so we will check if this sysConfig-object has an attribute with the given name.
     * If this is the case, the value of this sysConfig-object attribute will be returned.
     *
     * Second, it is checked if EISSCoreObject has a valid link to an appConfig-object.
     * If so we will check if this sysConfig-object has an attribute with the given name.
     * If this is the case, the value of this appConfig-object attribute will be returned.
     * Third, it is checked whether this EISSCoreObject has a valid link to a sysUser-object.
     * If so we will check if this (system)user-object has an attribute with the given name
     * and return this attributes value.
     * If, after all, still nothing was found we will throw en exception.
     *
     * @param string	$_name	name of the undefined attribute
     */
    function	__get( $_name) {
        /**
         * check the system configuration
         */
        if ( self::$sysConfig != null) {
            if ( isset( self::$sysConfig->$_name)) {
               $retVal	=	self::$sysConfig->$_name ;
            }
        }
        /**
         * check the application configuration
         */
        if ( self::$appConfig != null) {
            if ( isset( self::$appConfig->$_name)) {
                $retVal	=	self::$appConfig->$_name ;
            }
        }
        /**
         * check the system user
         */
        if ( self::$sysUser != null) {
            if ( isset( self::$sysUser->$_name)) {
                $retVal	=	self::$sysUser->$_name ;
            }
        }
        /**
         *
         */
        if ( !isset( $retVal)) {
            error_log( print_r( debug_backtrace(), true)) ;
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_name')",
                "variable '$_name' not defined/set!") ;
        }
        return $retVal ;
    }

    /**
     * __setSysConfig
     *
     * @param Config $_config
     */
    static	function	__setSysConfig( $_config) {
        self::$sysConfig	=	$_config ;
    }

    /**
     * __setAppConfig
     *
     * @param Config $_config
     */
    static	function	__setAppConfig( $_config) {
        self::$appConfig	=	$_config ;
    }

    /**
     * __getSysConfig
     *
     * returns a reference to the configuration
     *
     * @return Config reference to the Config object
     */
    static	function	__getSysConfig() {
        return self::$sysConfig ;
    }

    /**
     * __getAppConfig
     *
     * returns a reference to the configuration
     *
     * @return Config reference to the Config object
     */
    static	function	__getAppConfig() {
        return self::$appConfig ;
    }

    /**
     * getType - return object type nae as string
     *
     * @return string type name of the object
     */
    function	getType() {
        return $this->type ;
    }

    /**
     * getStatus - return object status
     *
     * @return int status of the object
     */
    function	getStatus() {
        return $this->status() ;
    }

    /**
     * isValid
     * return validity of object
     * @return bool validity of the object, false=invalid, true=valid
     */
    function	isValid() {
        return $this->valid ;
    }

    function	__toSring() {
        $this->dump( "this.", true, false) ;
    }

    function	dump( $_lead="this.", $_desc=false, $_config=false) {
        $buffer	=	"" ;
//		error_log( "$_lead" . "EISSCoreObject.php::dump( '$_lead', $_desc, $_config):") ;
        $buffer	.=	$_lead . "type := " . $this->type . "\n" ;
        $myData	=	get_object_vars( $this) ;
        foreach ( $myData as $key => $val) {
            if ( is_array( $val)) {
                $buffer	.=	" OBJECT => " . $key . " => array \n" ;
                foreach ( $val as $name => $value) {
                    error_log( "$key[$name] :=> '$value'") ;
                }
            } else if ( is_object( $val)) {
                $buffer	.=	" OBJECT => " . $key . " => object of type " . $val->type . "\n" ;
                if ( $_desc && $key != "sqlResult") {
                    $buffer	.=	$val->dump( "\t".$_lead."$key.", $_desc) . "\n" ;
                }
            } else {
                $buffer	.=	$_lead . "$key := '$val'" . " := " . $this->$val . "\n" ;
            }
        }
        return $buffer ;
    }

    /**
     * dumpGET
     * write all GET variables in the current http-request to the tracing subsystem
     * @return void
     */
    static	function	dumpGET() {
        $myBuffer	=	"" ;
        foreach ( $_GET AS $index => $value) {
            $myBuffer	.=	"_GET['$index'] => '$value'\n" ;
        }
    }

    /**
     * getGETAsXML
     * return an XML structure with all GET variables in the current http-request
     * @return string
     */
    static	function	getGETAsXML() {
        $ret	=	"<GET>\n" ;
        foreach ( $_GET AS $index => $value) {
            $ret	.=	"<$index><![CDATA[$value]]></$index>\n" ;
        }
        $ret	.=	"</GET>\n" ;
        return $ret ;
    }

    /**
     * addGET
     *
     * add all GET variables in the current http-request as attributes to $this object.
     *
     * @return void
     */
    function	addGET() {
        foreach ( $_GET AS $index => $value) {
            if ( !isset( $this->$index)) {
                $this->$index	=	$value ;
            } else {
                error_log( "EISSCoreObject.php::EISSCoreObject::addGET(): GET Variable '$index' already defined as attribute!") ;
            }
        }
    }

    /**
     * dumpGET
     * write all GET variables in the current http-request to the tracing subsystem
     * @return void
     */
    static	function	dumpPOST() {
        $myBuffer	=	"" ;
        foreach ( $_POST AS $index => $value) {
            $myBuffer	.=	"_POST['$index'] => '$value'\n" ;
        }
    }

    /**
     * dumpPOST
     * return an XML structure with all POST variables in the current http-request
     * @return string
     */
    static	function	getPOSTAsXML() {
        $ret	=	"<POST>\n" ;
        foreach ( $_POST AS $index => $value) {
            $ret	.=	"<$index><![CDATA[$value]]></$index>\n" ;
        }
        $ret	.=	"</POST>\n" ;
        return $ret ;
    }

    /**
     * addPOST
     * add all POST variables in the current http-request as attributes to $this object.
     * @return void
     */
    function	addPOST() {
        foreach ( $_POST AS $index => $value) {
            if ( !isset( $this->$index)) {
                $this->$index	=	$value ;
            } else {
            }
        }
    }

    /**
     * dumpFILE
     * write all FILE-name variables in the current http-request to the tracing subsystem
     * @return void
     */
    static	function	dumpFILE() {
        error_log( "EISSCoreObject::dumpFILE():") ;
        foreach ( $_FILES AS $index => $value) {
        }
    }

    /**
     * set the named attribute '$_paraName' of $this object to '$_paraValue'
     * @param string $_paraName
     * @param mixed $_paraValue
     */
    function	setValue( $_paraName, $_paraValue) {
        $this->$_paraName	=	$_paraValue ;
    }

    /**
     * return the named attribute '$_paraName' of $this object
     * if the attribute is not defined a boolean false is returned
     * @param $_paraName
     * @return mixed value of the propert or false in case the attribute is not defined
     */
    function	getValue( $_paraName) {
        if ( isset( $this->$_paraName))
            return $this->$_paraName ;
        else
            return	false ;
    }

    /**
     *
     * @param $_paraName
     */
    function	getValueIf( $_paraName) {
        if ( isset( $this->$_paraName))
            return $this->$_paraName ;
        else
            return "" ;
    }

    /**
     * checks if the given <$_paraName> is a defined attribute of the object
     *
     * @param string	$_paraName
     * return bool
     */
    function	isValue( $_paraName) {
        if ( isset( $this->$_paraName))
            return true ;
        else
            return false ;
    }

    /**
     * Copies all attributes, except for attributes starting with '_' and the attribute 'Id' which are common to
     * to <$this> object and the argument passed as <$_dest> from <$_srv> object to <this> object
     *
     * @param object	$_dest
     */
    function	copyFrom( $_src) {
        foreach ( $this as $key => $val) {
            if ( $key != "Id" && substr( $key, 0, 1) != "_") {
                if ( isset( $_src->$key) )
                    $this->$key	=	$_src->$key ;
            }
        }
    }

    /**
     * Copies all attributes, except for attributes starting with '_' and the attribute 'Id' which are common to
     * to <$this> object and the argument passed as <$_dest> from <$this> object to <$_dest> object
     *
     * @param object	$_dest
     * return void
     */
    function	copyTo( $_dest) {
        foreach ( $this as $key => $val) {
            if ( $key != "Id" && substr( $key, 0, 1) != "_") {
                if ( isset( $_dest->$key))
                    $_dest->$key	=	$this->$key ;
            }
        }
    }

    /**
     * Copies all attributes, except for attributes starting with '_' and the attribute 'Id' which are common to
     * to <$this> object and the argument passed as <$_dest> from <$this> object to <$_dest> object
     *
     * @param object	$_dest
     * return void
     */
    function	_getAsXML( $_objName="") {
        if ( $_objName == "")
            $_objName	=	$this->type ;
        $buffer	=	"<" . $_objName . ">\n" ;
        foreach ( $this as $colName => $val) {
            if ( is_object( $val)) {
                $buffer	.=	$val->_getAsXml() ;
            } else {
                $buffer	.=	"<" . $colName . " title=\"" . FTr::tr( $colName) . "\"><![CDATA[" . $val . "]]></" . $colName . ">\n" ;
            }
        }
        $buffer	.=	"</" . $_objName . ">\n" ;
        return( $buffer) ;
    }

    /**
     * catches all calls to un-defined static methods of classes derived from EISSCoreObject, performs debugging
     * creates an exception
     *
     * @param   string	    $_fName name of the method which was called
     * @param   array		$_args	list of the arguments
     * @throws  FException  in every case
     * return void
     */
    function	__call( $_fName, $_args) {
        throw new FException( __FILE__, __CLASS__, __METHOD__."( '$_fName', <ARRAY>')", "EISSCoreObject.php::[$this->type]:__call( $_fName, ...) not implemented") ;
    }

    /**
     *
     * @param unknown $_user
     */
    static	function	__setSysUser( $_user) {
        self::$sysUser	=	$_user ;
   }

    /**
     * returns the confi
     */
    static	function	__getSysUser() {
        return self::$sysUser ;
    }

    /**
     *
     * @param unknown $_user
     */
    static	function	__setAppUser( $_user) {
        self::$appUser	=	$_user ;
    }

    /**
     * returns the confi
     */
    static	function	__getAppUser() {
        return self::$appUser ;
    }

    static	function	__pushAppUser() {
        self::$_appUser	=	self::$appUser ;
        self::$appUser	=	null ;
    }

    static	function	__popAppUser() {
        self::$appUser	=	self::$_appUser ;
        self::$_appUser	=	null ;
    }

    /**
     *
     * @param number $_corr
     * @return string
     */
    static	function	today( $_corr=0) {
        return(	date("Y-m-d", time() + $_corr)) ;
    }

    /**
     *
     * @param string $_buffer
     * @return string
     */
    function	interpret( $_buffer, $_htmlMode=false, $_obj=null) {
        $varName    =   "" ;

        /**
         *
         */
        $in	=	$_buffer ;
        $len	=	strlen( $in) ;
        $inVar	=	false ;
        $out	=	"" ;
        for ( $i=0 ; $i < $len ; $i++) {
            $c	=	$in[$i] ;
            if ( ! $inVar ) {
                if ( $c == "$") {
                    $inVar	=	true ;
                    $varName	=	"" ;
                } else {
                    $out	.=	$c ;
                }
            } else {
                if ( ctype_alnum( $c) || $c == "-" || $c == "_") {
                    $varName	.=	$c ;
                } else if ( $c == ".") {
                    $varName	.=	"." ;
                } else if ( $c == "$") {		// end of variable statement
//					error_log( $varName) ;
//					error_log( "Page.php::Page:interpret(...): Variable: $varName") ;
                    /**
                     *	break up the variable name into parts
                     *	valid trailers are:
                     *		trans.*
                     *		options.<OptionName>.*
                     *		select.<Optionname>.*
                     *		post.<NameOfPostVariable>
                     *		self.<NameOfStaticVariableOfSELF>
                     *		err.{attributeNameOfThisObject}
                     *		*.
                     */
                    $v	=	explode( ".", $varName) ;
                    $o	=	$this ;
                    if ( $v[0] == "trans") {
                        $o	=	FTr::tr( $v[1]) ;
                    } else if ( $v[0] == "options") {
                        if ( isset( $v[2]) && isset( $v[3])) {
                            $val	=	$this->$v[2]->$v[3] ;
                        } else {
                            $val	=	"" ;
                        }
                        $o	=	Option::getOptions( "Options", "Key", "Value", "OptionName = '".$v[1]."'", "", $val) ;
                    } else if ( $v[0] == "select") {
                        if ( isset( $v[2]) && isset( $v[3])) {
                            $val	=	$this->$v[2]->$v[3] ;
                        } else {
                            $val	=	"" ;
                        }
                        $o	=	Option::getSelect( "Options", "Key", "Value", "OptionName = '".$v[1]."'", "", $v[2]) ;
                    } else if ( $v[0] == "post") {
                        if ( isset( $_POST[ $v[1]]))
                            $o	=	$_POST[ $v[1]] ;
                        else
                            $o	=	"" ;
                    } else if ( $v[0] == "self") {
                        if ( isset( self::$$v[1]))
                            $o	=	self::$$v[1] ;
                        else if ( isset( self::$globals[$v[1]]))
                            $o	=	self::$globals[$v[1]] ;
//
// enable the following three lines to access sysUser settings through $self.<setting>$
//
//						else if ( isset( self::$sysUser))
//							if ( isset( self::$sysUser->$v[1]))
//								$o	=	self::$sysUser->$v[1] ;
                        else
                            $o	=	"" ;
                    } else if ( $v[0] == "err") {
                        $index	=	intval( $v[1]) ;
                        if ( isset( self::$err[ $index]))
                            $o	=	"r18" ;
                        else if ( isset( self::$err[ $v[1]]))
                            $o	=	"r18" ;
                        else
                            $o	=	"" ;
                    } else {
                        $buf	=	"" ;
                        foreach ( $v as $name) {
                            if ( $name == "toHTML") {
                                $o	=	htmlentities( $o, ENT_COMPAT, "UTF-8") ;
                            } else if ( isset( $o->$name) || is_object( $o->$name)) {
                                $o	=	$o->$name ;
                            } else {
                                $o	=	"undefined" ;
                            }
                            $buf	.=	"     " ;
                        }
                    }
                    if ( $_htmlMode) {
                        $out	.=	htmlentities( $o, ENT_COMPAT, "UTF-8") ;
                    } else {
                        $out	.=	$o ;
                    }
                    $varName	=	"" ;
                    $inVar	=	false ;
                }
            }
        }
//		error_log( $out) ;
        return $out ;
    }
}
?>