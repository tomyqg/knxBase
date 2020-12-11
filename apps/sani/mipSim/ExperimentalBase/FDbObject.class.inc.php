<?php
/**
 * Copyright (c) 2015, 2016, 2017 wimtecc, Karl-Heinz Welter
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
 * FDbObject	-	Base Database Object Abstraction Class
 *
 * Versatile class representing objects stored in databases.
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 200x-xx-xx	PA1		khw		added to rev. control
 * 2014-10-06	A		khw		frozen R1 base; start to replace the old
 * 								mysql API with the new mysqli API
 * 2015-01-26	PB1		khw		added special handling for null values;
 * 								see note 1);
 * 2015-04-02	PB2		khw		added further support for MSSQL database,
 * 								which remains in experimental status!;
 * 2015-04-13	PB3		khw		further comments;_
 * 2015-10-15	PB4		khw		export functions improved, uses DOMDocument;
 * 2016-01-19	PB5		khw		moved "'" bracketing values to the native
 *						        drivers (FSqlMSSQLQuery and FSqlMySQLQuery);
 * 2016-06-02	PB6		khw		cleaned up exception handling, now this module only
 *						        issues FExceptions
 * 2016-09-01   PB7     khw     removed assignFromRow in favour of object assignment;
 *                              general performance improvements;
 * 2016-09-13   PB8     khw     added assignFromObject; cleanup, removed various redundant
 *                              attributes; $this->attrs now holds the only reliable information
 *                              about the structure in the database
 *
 * Notes:
 * ======
 *
 * 1)	null values are supporteed now to some extend.
 * 	create and update operation:
 * 	IF an empty value is provided for an attribute which may be NULL
 * 		=> the NULL value is written to the database.
 * 	IF en empty value is provided for an attribute which may NOT be NULL
 * 		=> the default value from the database structure is used
 *
 *
 * @author	Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package wapCore
 * @filesource
 */
/**
 * FDbObject
 *
 * Summary before class line ...
 *
 * @author	Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @subpackage Database
 */
class FDbObject  extends	EISSCoreObject	implements Iterator {
    /**
     *
     * Enter description here ...
     */
    public	            $className ;
    public	            $tableName ;
    public	            $keyCol ;
    private             $id             =   -1 ;        // holds id from last setId method call
    public              $key ;                          // holds key from last setKey method call
    public	            $ownAlias ;
    /**
     *
     */
    private	    static	$attrsCache	    =	array() ;		// variable types of attributes
    private	    static	$namesCache	    =	array() ;	// names of attributes
    /**
     *
     */
    private             $attrs          =   array() ;       // holds the structure rows
    protected   static	$db		        =	array() ;
    private	    static	$idKey	        =	array() ;		// attribute which indicates auto-increment id; defaults to "Id"
    private     static  $nameTransTables =   array() ;
    private     static  $attrTransTables =   array() ;
    private         	$addAttrNames	=   array() ;
    private         	$addAttrTypes	=   array() ;
    private	            $addDataRem 	=	"" ;
    public	            $startRow	    =	0 ;					// default: start on 1st row
    public	            $rowCount	    =	20 ;				// default: pick 20 lines
    public	            $step		    =	"thisPage" ;		// default: refresh current page
    var	                $_status ;
    private             $_statusInfo    =   "" ;
    public              $_numrows   =   0 ;
    var	                $_valid	=	false ;
    private         	$_currRow ;
    private	            $_lastRow ;
    var	$_where ;
    var	$traceUpdate	=	false ;
    private $level      =   0 ;
    private $maxLevel   =   0 ;
    private $dataset    =   "" ;
    private $cacheName  =   "" ;
    private $obj ;
    public  $public     =   true ;
    /**
     * variables needed for the 'Iterator' portion
     */
    private	            $iterQuery	    =	null ;              // query object for the iterator
    private         	$iterJoin	    =	"" ;
    private	            $iterJoinCols	=	"" ;
    private             $iterCount      =   0 ;
    private             $sqlIterResult ;
    private             $dataArray ;
    /**
     * variables needed for interfacing the real Db
     */
    private $sqlResult ;

    /**
     * __construct( $_className, $_keyCol, $_db)
     *
     * Create an instance of the class and manifests its attributes
     * based on the table named $_className in the database specified
     * by $_db. The keycolumn for object access in the database table is
     * specified by $_keyCol.
     * if $_tableName is specified this name will be used as the table name in the database
     * instead of the provided $_className.
     * If no database is specified the database access will refer to the
     * default database 'def'.
     *
     * @param   string      $_className	    database table name
     * @param   string      $_keyCol		database table key column (defaults to "Id")
     * @param   string      $_db			database alias (default to "def")
     * @param   string      $_tableName 	name of table in database
     *                                      (defaults to $_classname if not specified)
     * @throws  FException
     * @return  FDbObject
     */
    function	__construct( $_className, $_keyCol="Id", $_db="def", $_tableName="") {
        parent::__construct( $_className) ;
        $this->dataset	=	$_className ;
        $this->level	=	0 ;
        $this->maxLevel	=	0 ;
        $this->ownAlias	=	$_db ;
        if ( $_className != "") {
            $this->className	=	$_className ;
            if ( $_tableName != "") {
                $this->tableName	=	$_tableName ;
            } else {
                $this->tableName	=	$this->className ;
            }
            $this->cacheName	=	$this->ownAlias . "::" . $this->tableName ;
            $this->addDataRem	=	"ADDITIONAL_DATA: " . $this->className ;
            $this->keyCol	=	$_keyCol ;
            $this->key      =   "" ;
            $this->_status	=	0 ;
            $this->_valid	=	false ;
            $this->_currRow	=	0 ;
            $this->_lastRow	=	0 ;
            /**
             *  IF this class is not yet in the cache
             */
            if ( ! isset( self::$attrsCache[$this->cacheName])) {
//                error_log( "FDbObject::__construct(): caching object description for class [$_className]") ;
                self::$db[$this->cacheName]	=	$_db ;
                self::$idKey[$this->cacheName]	=	"Id" ;
                $myQuery	=	$this->getQueryObj( "Structure") ;
                $result	=	FDb::query( $myQuery, $_db) ;
                while ($obj = FDb::getObject( $result, $_db)) {
                    /**
                     *  get the column-name
                     */
                    $attrName	=	$obj->Field ;
                    $attrTypeParts	=	explode( "(", $obj->Type) ;
                    $obj->attrType  =   $attrTypeParts[0] ;
                    $this->attrs[$attrName]  =   $obj ;
                }
                /**
                 *
                 */
                self::$attrsCache[ $this->cacheName]	=	&$this->attrs ;         // reference to this
                self::$nameTransTables[ $this->cacheName]   =   array() ;
                /**
                 * TODO:
                 * here we need to include the authorization stuff
                 * basically what we do here is to remove all attributes which the user is not even allowed to see
                 */
                $this->_postInstantiate() ;
            } else {
                error_log( "FDbObject::__construct(): using cached object description for class [$_className]") ;
                $this->attrs        =   &self::$attrsCache[$this->cacheName] ;
            }
            $this->clear() ;
        } else {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_className', '$_keyCol')",
                "no valid className provided!") ;
        }
        $this->iterQuery	=	$this->getQueryObj( "Select") ;
    }

    function    regTransTable  ( $_nameTransTable) {
        self::$nameTransTables[ $this->cacheName]   =   $_nameTransTable ;
        self::$attrTransTables[ $this->cacheName]   =   array_flip( $_nameTransTable) ;
    }

    /**
     * @param   string  $_vName     Name of the variable to be set
     * @param   mixed   $_vValue    Value to be set
     * @throws  FException
     */
    function    __set( $_vName, $_vValue) {
        $vName  =   "" ;
        if ( count( self::$nameTransTables[ $this->cacheName]) > 0) {
            if ( array_key_exists( $_vName, self::$nameTransTables[ $this->cacheName])) {
                $vName  =   self::$nameTransTables[ $this->cacheName][ $_vName] ;
            }
        }
        if ( $vName == "") {
            $vName  =   $_vName ;
        }
        if ( $this->attrs[ $vName]->Field == $vName) {
            if ( $this->public) {
                $this->$vName  =   $_vValue ;
            } else {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_vName', '$_vValue')",
                    "trying to set-access virtual-private attribute! Use ::set{$_vName} instead") ;
            }
        } else {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_vName', '$_vValue')",
                "variable '$_vName' does not exist in db-based object!") ;
        }
    }

    /**
     * @param   string  $_vName     Name of the variable to be set
     * @return  mixed
     * @throws  FException
     */
    function    __get( $_vName) {
        $vName  =   "" ;
        if ( count( self::$nameTransTables[ $this->cacheName]) > 0) {
            if ( array_key_exists( $_vName, self::$nameTransTables[ $this->cacheName])) {
                $vName  =   self::$nameTransTables[ $this->cacheName][ $_vName] ;
            }
        }
        if ( $vName == "") {
            $vName  =   $_vName ;
        }
        if ( $this->attrs[ $vName]->Field == $vName) {
            if ( $this->public) {
                $ret    =   $this->$vName ;
            } else {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_vName')",
                    "trying to get-access virtual-private attribute! Use ::get{$_vName} instead") ;
            }
        } else {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_vName\[$vName\]')",
                "variable does not exist in db-based object!") ;
        }
        return $ret ;
    }

    /**
     * @param   $_vName     Name of the variable to be set
     * @return  boolean
     */
    function    __isset( $_vName) {
        $res = false ;
        if ( $this->attrs[ $_vName]->Field == $_vName) {
            $res    =   true ;
        } else {
        }
        return $res ;
    }

    /**
     * @param   $_vName     Name of the variable to be set
     * @return  boolean
     * @throws  FException
     */
    function    _declare( $_vName) {
        $res = false ;
        if ( ! isset( $this->obj->$_vName)) {
            $this->attrs[ $_vName]->Field == $_vName ;
            $this->$_vName =   -1 ;
            $res    =   true ;
        } else {
        }
        return $res ;
    }

    /**
     * @param   string      $_fName     Name of the "not-existing" function which was called
     * @param   array       $_args      Arguments to the "not-existing" function
     * @return  mixed                   bool:true=  successfull call to set<varname>
     *                                  mixed=      value of variable for get<varname>
     * @throws  FException
     */
    function	__call( $_fName, $_args) {
        $ret    =   false ;
        switch ( substr( $_fName, 0, 3)) {
            case    "set"   :
                $varName    =   substr( $_fName, 3) ;
                if ( isset( $this->$varName)) {
                    $this->$varName =   $_args[0] ;
                    $ret    =   true ;
                } else {
                    throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_fName', <array>)",
                        "'setter' called on not-existing variable in db-based object!") ;
                }
                break ;
            case    "get"   :
                $varName    =   substr( $_fName, 3) ;
                if ( isset( $this->$varName)) {
                    $ret =   $this->$varName ;
                } else {
                    throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_fName', <array>)",
                        "'getter' called on not-existing variable in db-based object!") ;
                }
                break ;
            default :
                parent::__call( $_fName, $_args) ;
//                throw new FException( __FILE__, __CLASS__, __METHOD__."( '$_fName', <ARRAY>')", "EISSCoreObject.php::[$this->type]:__call( $_fName, ...) not implemented") ;
                break ;
        }
        return $ret ;
    }

    /**
     * setIdKey( $_idKey)
     *
     * Set the name of the table attribute which holds the unique auto-increment id
     * of this database table. The default is set to "Id" at instantiation time,
     * thus there should be no need to use this method.
     *
     * @param	string		$_idKey		name of the id attribute
     * @return	int
     */
    function	setIdKey( $_idKey="Id") {
        if ( array_key_exists( $_idKey, self::$nameTransTables[ $this->cacheName])) {
            self::$idKey[$this->cacheName]	=	self::$nameTransTables[ $this->cacheName][$_idKey] ;
        } else {
            self::$idKey[$this->cacheName]	=	$_idKey ;
        }
        return 0 ;
    }
    /**
     * setId( $_id)
     *
     * Set the Id of an object to $_id and try to retrieve the object from the db
     *
     * This method sets the Id of the object to $_id and, if the $_id is
     * equal to or larger than 0, tries to retrieve the object from the db.
     *
     * @param	int 		$_id		id of the object to retrieve
     * @return	bool					success of the method
     *									false - no success
     *									true - success
     */
    function	setId( $_id=-1) {
        if ( $_id > -1) {
            $this->id	=	$_id ;
            $this->fetchFromDbById( self::$db[$this->cacheName]) ;
        } else {
            $this->_valid	=	false ;
        }
        if ( $this->_valid) {
            $this->_postLoad() ;
        }
        return $this->_valid ;
    }
    /**
     * setKey( $_key)
     *
     * Set the Key of an object to $_key and try to retrieve the object from the db.
     * This method supports a key comprising multipl attributes.
     *
     * This method sets the Key of the object to $_key and, if the $_key is
     * longer than 0 characters, tries to retrieve the object from the db.
     *
     * @param	string	$_key			key of the object to retrieve
     * @return	bool					object validity after reload
     *									false - object(data) not valid
     *									true - object(data) is valid
     * @throws  FException
     */
    function	setKey( $_key) {
        $this->_valid	=	false ;     // this object ist NOT valid
        $this->key	=	$_key ;         // remember the key
        $this->reload() ;               // and fetch object from database
        return $this->_valid ;
    }
    /**
     * Get the key of the current object
     *
     * Returns the Key of the object.
     *
     * @return mixed				value of the key field
     */
    function	getKey() {
        $key	=	$this->key ;
        return $key ;
    }
    /**
     * reload()
     *
     * Reload the object from the db.
     * This reload is handled through fetchFromDb(), which retrieves the data either by
     * the key(s) or the id.
     *
     * @return	bool					object validity after reload
     *									false - object(data) not valid
     *									true - object(data) is valid
     */
    function	reload() {
        $res	=	$this->fetchFromDb() ;
        return $res ;
    }

    /**
     * getAsXML( $_key, $_id, $_val, $reply)
     *
     * Get the object as XML
     *
     * Returns the XML serialization of the object, without any LF ("\n") between the
     * XML attributes. This is nice to have the stuff more difficult to read.
     * The top level element of the XML tree is the class name. For special needs
     * this top level element can be replaced with $_className. This is useful for example
     * for a Customer object which might reference other Customer objects for delviery
     * or invoicing addresses.
     *
     * @param	string	$_key       alternative class name for the start element of the XML tree
     * @param   int     $_id        id of the object to retrieve
     * @param   mixed   $_val       optional value
     * @param   Reply   $reply      Reply object
     * @return	string				XML representation of the object
     */
    function	getAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
        if ( $reply == null)
            $reply	=	new Reply( __class__, $this->className) ;
        $reply->replyData	=	$this->getXMLF() ;
        return $reply ;
    }
    /**
     * getXMLF()
     *
     * @deprecated	deprecated
     * @param	string				$_className
     * @return	string
     */
    function	getXML( $_className = "") {
        return( $this->getXMLF( $_className)) ;
    }
    /**
     * getXMLF( $_className)
     *
     * Return a string with the XML representation of THIS object. If $_className is specified
     * this name will be used as the classname instead of the class given during instantiation.
     * This method uses the php::DOMDocument to construct the XML object.
     * @param	string 				$_className
     * @return	string				XML representation of the object
     */
    function	getXMLF( $_className = "") {
        $myXML	=	new DOMDocument() ;
        $myXML->xmlStandAlone	=	false ;		// avoid the <?xml version="1.0"> line
        $myXML->formatOutput	=	true ;		// make it readable
//		if ( $_className == "") {
//			$dataXML	=	$myXML->appendChild( $myXML->createElement( $this->className)) ;
//			$this->addDataRem	=	"ADDITIONAL_DATA: " . $this->className ;
//		} else {
//			$dataXML	=	$myXML->appendChild( $myXML->createElement( $_className)) ;
//			$this->addDataRem	=	"ADDITIONAL_DATA: " . $_className ;
//		}
//		$dataXML->setAttribute( "type", "class") ;
        $startNode	=	$this->_exportXML( $myXML, $myXML) ;
       return( $myXML->saveXML( $startNode)) ;
    }
    /**
     * getXMLF( $_className)
     *
     * Return a string with the XML representation of THIS object. If $_className is specified
     * this name will be used as the classname instead of the class given during instantiation.
     * This method uses the php::DOMDocument to construct the XML object.
     * @param	DOMDocument $_doc		DOMDocument, must exist
     * @param   DOMNode     $_node
     * @return	string				    XML representation of the object
     */
    function	_exportXML( $_doc, $_node) {
        $dataXML	=	$_node->appendChild( $_doc->createElement( $this->className)) ;
        $dataXML->setAttribute( "type", "class") ;
        $this->addDataRem	=	"ADDITIONAL_DATA: " . $this->className ;
        foreach ( $this->attrs as $colName => $attrInfo) {
            $dataNode	=	$dataXML->appendChild( $_doc->createElement( $colName)) ;
            $dataNode->setAttribute( "type", $attrInfo->attrType) ;
            $dataNode->setAttribute( "title", $colName) ;
            switch ( $attrInfo->attrType) {
                case	"money"	:							// groesster Schwachsinn der Welt von Microsoft!!!
                    $dataNode->appendChild( $_doc->createCDATASection( iconv( FDb::getCharset( self::$db[$this->cacheName]), "UTF-8", $this->$colName))) ;
                    break ;
                case	"enum"	:
                case	"char"	:
                case	"varchar"	:
                    $dataNode->appendChild( $_doc->createCDATASection( iconv( FDb::getCharset( self::$db[$this->cacheName]), "UTF-8", $this->$colName))) ;
                    break ;
                case	"blob"	:
                case	"tinyblob"	:
                case	"mediumblob"	:
                case	"longblob"	:
                    $dataNode->appendChild( $_doc->createCDATASection( "<<<NO *BLOB DATA PROVIDED >>>")) ;
                    break ;
                case	"uniqueidentifier"	:
                    $dataNode->appendChild( $_doc->createCDATASection( mssql_guid_string( $this->$colName))) ;
                    break ;
                default	:
                    $dataNode->appendChild( $_doc->createCDATASection( $this->$colName)) ;
                    break ;
            }
        }
        $dataXML->appendChild( $_doc->createComment( $this->addDataRem)) ;
//        foreach ( $this->addAttrNames as $colName) {
//            $dataNode	=	$dataXML->appendChild( $_doc->createElement( $colName)) ;
//            $dataNode->setAttribute( "type", $this->addAttrTypes[ $colName]) ;
//            $dataNode->setAttribute( "title", $colName) ;
//            $dataNode->appendChild( $_doc->createCDATASection( $this->$colName)) ;
//        }
//		$_node->appendChild( $myXML->createComment( $this->addDataRem)) ;
       return( $dataXML) ;
    }
    /**
     * getXMLF( $_className)
     *
     * Return a string with the XML representation of THIS object. If $_className is specified
     * this name will be used as the classname instead of the class given during instantiation.
     * This method uses the php::DOMDocument to construct the XML object.
     * @param	DOMDocument 		DOMDocument, must exist
     * @param
     * @return	string				XML representation of the object
     */
    function	_exportJSON( $_le="\n") {
        $subDataJSON	=	"" ;
        foreach ( $this->attrs as $colName => $attrInfo) {
            if ( $subDataJSON != "") {
                $subDataJSON	.=	"," . $_le ;
            }
            $subDataJSON	.=	"\t\"" . $colName . "\":\"" . iconv( FDb::getCharset( self::$db[$this->cacheName]), "UTF-8", $this->$colName) . "\"" ;
        }
//		foreach ( $this->addAttrNames as $colName) {
//			$dataNode	=	$dataXML->appendChild( $_doc->createElement( $colName)) ;
//			$dataNode->setAttribute( "type", $this->addAttrTypes[ $colName]) ;
//			$dataNode->setAttribute( "title", $colName) ;
//			$dataNode->appendChild( $_doc->createCDATASection( $this->$colName)) ;
//		}
        $subDataJSON	.=	$_le ;
		$dataJSON	=	"{\"".$this->className."\":{" . $subDataJSON . "}" . $_le ;
//        $dataJSON	=	"{" . $_le;
//        $dataJSON	.=	$subDataJSON ;
        $dataJSON	.=	"}" . $_le ;
//		$_node->appendChild( $myXML->createComment( $this->addDataRem)) ;
        return( $dataJSON) ;
    }
    /**
     * getXMLF( $_className)
     *
     * Return a string with the XML representation of THIS object. If $_className is specified
     * this name will be used as the classname instead of the class given during instantiation.
     * This method uses the php::DOMDocument to construct the XML object.
     * @param	DOMDocument 	$_doc	DOMDocument, must exist
     * @param   DOMNode         $_node
     * @return	string			XML representation of the object
     */
    function	_importXML( $_doc, $_node, $_attribs=false) {
        $myNodes	=	$_node->childNodes ;
//		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "sub-nodes available....: " . $myNodes->length) ;
        if ( $_attribs) {
            if ( $_node->attributes->length > 0) {
                for ( $i=0 ; $i<$_node->attributes->length ; $i++) {
                    $attr	=	$_node->attributes->item( $i)->nodeName ;
                    $this->$attr	=	$_node->attributes->item( $i)->nodeValue ;
                }
            }
        }
        foreach ( $myNodes as $node) {
            if ( $node->nodeType == 1) {
                $attr	=	$node->tagName ;
                if ( isset( $this->$attr)) {
                    $this->$attr	=	$node->firstChild->textContent ;
                } else {
                    $myObj	=	new $attr() ;
                    $myObj->_importXML( $_doc, $node, $_attribs) ;
                }
            } else {
//				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "--> " . $node->nodeType) ;
            }
        }
        $this->Imported	=	1 ;
        $this->storeInDb() ;
    }

    /**
     *
     */
    function	getAddDataRem() {
        return "<!--".$this->addDataRem."-->" ;
    }

    /**
     * getJSON( $_className)
     *
     * THIS METHOD is in status: EXPERIMENTAL!!!
     *
     * Return a string with the JSON representation of THIS object.
     * If $_className is specified
     * this name will be used as the classname instead of the class given during instantiation.
     *
     * @param	string 				$_className
     * @return	string				XML representation of the object
     */
    function	getJSON( $_className = "") {
        $buffer	=	"" ;
        foreach ( $this->attrs as $colName => $attrInfo) {
            if ( $buffer != "")
                $buffer	.=	",\n" ;
            $buffer	.=	"\t\"" . $colName . "\" : " . json_encode( $this->$colName) ;
        }
        $buffer	.=	"\n" ;
        return( $buffer) ;
    }

    /**
     * assignFromObj( $_row)
     *
     * Fetch the attributes from a MySQL result row
     *
     * This methods assigns all manifested attributes from the given MySQL row
     *
     * @param	object	$_obj			MySQL row result set
     * @return	array	$_row			MySQL row result set
     */
    function	assignFromObj( $_obj) {
        $this->_assignFromObj( $_obj) ;
    }

    /**
     * _assignFromObj( $_row)
     *
     * Fetch all defined attributes of THIS object from the associative array $_row.
     *
     * @param       object   $_obj                   MySQL row result set
     * @return      array   $_row                   MySQL row result set
     */
    function        _assignFromObj( $_obj) {
        foreach ( $_obj as $colName => $value) {
            if ( isset ( $this->$colName) && $value !== null) {
                $buffer	=	"<" . $this->className . ">\n" ;
                switch ( $this->attrs[ $colName]->attrType) {
                    case "smalldatetime"	:
                    case "datetime"	:
                        $this->$colName	=	FDb::getDateTime( self::$db[$this->cacheName], $value) ;
                        break ;
                    default	:
                        $this->$colName	=	$value ;
                        break ;
                }
            } else if ( $value === null) {
                $this->$colName	=	"NULL" ;
            }
        }
        reset( $_row) ;
        $this->_postLoad() ;
    }

    /**
     * storeInDb( $_exec)
     *
     * Store the object in the database. In case the execution flag is set to FALSE, the command will not be
     * executed but the database query will be returned as a string. This is primarily a diagnostic feature!
     *
     * @param	bool	$_exec					execute the query
     *											false - query will not be executed but query string will be returned
     *											to the calling function
     *											true - execute the query
     * @return	bool							object validity after storage
     *											false - object(data) not valid
     *											true - object(data) is valid
     * @throws  FException
     */
    function	storeInDb( $_exec=true) {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        $idKey	=	self::$idKey[$this->cacheName] ;
        /**
         *x
         */
        $appUser	=	$this->__getAppUser() ;
        if ( $appUser) {
            $authority	=	self::$db[$this->cacheName] . "." . $this->className.".store" ;
            if ( ! $appUser->isGranted( "dbt", $authority)) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                    "insufficient rights to store an object of type '".$this->className."'! Required: '".$authority."'") ;
            }
        }
        $myQuery	=	$this->getQueryObj( "Insert") ;
        foreach ( $this->attrs as $colName => $attrInfo) {
            if ( $colName != $idKey && $colName != "TimeStamp" && $attrInfo->attrType != "uniqueidentifier") {
                $myQuery->addField( $colName) ;
            }
        }
        reset( $this->attrs) ;
        foreach ( $this->attrs as $colName => $attrInfo) {
            if ( $appUser) {
                if ( ! $appUser->isValueGranted( "dbv", $this->className.".".$colName, FDb::escapeString( $this->$colName, self::$db[$this->cacheName]), false)) {
                    throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                        "value not allowed '".FDb::escapeString( $this->$colName, self::$db[$this->cacheName])."'!") ;
                }
            }
            if ( $colName != $idKey && $colName != "TimeStamp" && $attrInfo->attrType != "uniqueidentifier") {
                if ( ( $this->$colName === null || $this->$colName === "NULL") && $attrInfo->Null == "YES")
                    $myQuery->addValue( "null") ;
                else if ( $this->$colName === null)
                    $myQuery->addValue( "'".$attrInfo->Default. "'") ;
                else
                    $myQuery->addValue( "'" . FDb::escapeString( $this->$colName, self::$db[$this->cacheName]) . "'") ;
            }
        }
        reset( $this->attrs) ;
        if ( $_exec) {
            $this->sqlResult	=	FDb::query( $myQuery, self::$db[$this->cacheName]) ;
            if ( !$this->sqlResult) {
                $this->_status  =       -1 ;
            } else {
                $this->$idKey	=	FDb::insertId( self::$db[$this->cacheName]) ;
                $this->_valid	=	true ;
            }
        } else {
           return $myQuery->getQuery() ;
        }
//        if ( $this->traceUpdate) {
//            $myFile	=	fopen( $this->path->Archive . "XML/Up/" . "myUpd.sql", "a+") ;
//            fwrite( $myFile, $query . "\n") ;
//            fclose( $myFile) ;
//        }
        return $this->_valid ;
    }
    /**
     * fetchFromDb()
     *
     * Fetches the object from the database.
     *
     * This method fetches the object from the database by the key.
     *
     * @return	bool							object validity after storage
     *											false - object(data) not valid
     *											true - object(data) is valid
     * @throws  FException
     */
    function	fetchFromDb() {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        $idKey	=	self::$idKey[$this->cacheName] ;
        /**
         *
         */
        $appUser	=	$this->__getAppUser() ;
        if ( $appUser) {
            $authority	=	self::$db[$this->cacheName] . "." . $this->className.".read" ;
            if ( ! $appUser->isGranted( "dbt", $authority)) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                    "insufficient rights to read objects of type '".$this->className."'! Required: '".$authority."' for user '" . $appUser->UserId . "'") ;
            }
        }
        /**
         *
         */
        $keyString	=	"" ;
        $myQuery	=	$this->getQueryObj( "Select") ;
        $keyCol	=	$this->keyCol ;
        if ( is_array( $this->key)) {
            foreach ( $this->key as $i => $v) {
                $keyString	.=	"[$i] := '".$v."' " ;
                $myQuery->addWhere( "{$keyCol[$i]} = '" . $v . "' ") ;
            }
        } else if ( $this->key != "") {
            switch ( $this->attrs[ $keyCol]->attrType) {
                case	"smallint"	:
                    $myQuery->addWhere( "$keyCol = " . $this->key . " ") ;
                    break ;
                default	:
                    $myQuery->addWhere( "$keyCol = '" . $this->key . "' ") ;
                    break ;
            }
        } else {
            $myQuery->addWhere( $idKey . " = '" . $this->Id . "' ") ;
        }
        $this->sqlResult      =       FDb::query( $myQuery, self::$db[$this->cacheName]) ;
        if ( !$this->sqlResult) {
            $this->_status  =       -1 ;
        } else {
            $numrows        =       FDb::rowCount( self::$db[$this->cacheName]) ;
            if ( $numrows == 1) {
                $obj    =       FDb::getObject( $this->sqlResult, self::$db[$this->cacheName]) ;
                $this->assignFromObj( $obj) ;
                $this->_valid   =       true ;
            } else if ( $numrows > 1) {
                $this->_status  =       -3 ;
            } else {
                $this->_status  =       -2 ;
                $this->_statusInfo	=	"Object['".$this->className."'], " . $keyString ;
            }
        }
        return $this->_valid ;
    }
    /**
     * fetchFromDbById()
     *
     * This method fetches the object from the database by the Id.
     *
     * @param	string	$_db			database alias
     * @return	bool							object validity after storage
     *											false - object(data) not valid
     *											true - object(data) is valid
     * @throws  FException
     */
    function	fetchFromDbById() {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        /**
         *
         */
        $appUser	=	$this->__getAppUser() ;
        if ( $appUser) {
            $authority	=	self::$db[$this->cacheName] . "." . $this->className.".read" ;
            if ( ! $appUser->isGranted( "dbt", $authority)) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                    "insufficient rights to read objects of type '".$this->className."'! Required: '".$authority."'") ;
            }
        }
        /**
         *
         */
        $idKey	=	self::$idKey[ $this->cacheName] ;
        $myQuery	=	$this->getQueryObj( "Select") ;
        $myQuery->addWhere( $idKey."='" . $this->id . "' ") ;
        $this->sqlResult      =       FDb::query( $myQuery, self::$db[$this->cacheName]) ;
        if ( !$this->sqlResult) {
            $this->_status  =       -1 ;
        } else {
            $numrows        =       FDb::rowCount( self::$db[$this->cacheName]) ;
            if ( $numrows == 1) {
                $obj    =       FDb::getObject( $this->sqlResult, self::$db[$this->cacheName]) ;
                $this->assignFromObj( $obj) ;
                $this->_valid   =       true ;
            } else {
                $this->_status  =       -2 ;
            }
        }
        return $this->_valid ;
    }

    /**
     * fetchFromDbAsArray( $_where, $_iCol)
     *
     * Fetches the object from the database
     *
     * This method fetches the object from the database by the key.
     *
     * @param	string	$_where			database alias
     * @param	string	$_iCol			database alias
     * @return	bool					success of the method, false - no success, true - success
     */
    function	fetchFromDbAsArray( $_where, $_iCol="Parameter") {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        $query	=	"SELECT * FROM " . FDb::fTableName( $this->className) . " " ;
        $query	.=	$_where ;
        $this->sqlResult      =       FDb::query( $query, self::$db[$this->cacheName]) ;
        if ( !$this->sqlResult) {
            $this->_status  =       -1 ;
        } else {
            $numrows        =       FDb::rowCount( self::$db[$this->cacheName]) ;
            if ( $numrows >= 1) {
                $this->dataArray	=	array() ;
                while ( $numrows > 0) {
                    $this->dataArray[]	=	FDb::getObject( $this->sqlResult, self::$db[$this->cacheName]) ;
                }
                $this->_valid   =       true ;
            } else {
                $this->_status  =       -2 ;
            }
        }
        return $this->_valid ;
    }
    /**
     * fetchFromDbWhere( $_where, $_order)
     *
     * Fetches an object from the database
     *
     * This method fetches an object which matches the given condition $_condition from the db.
     * The condition must be given as a full WHERE statement, e.g. "WHERE CustNr = '123456' ".
     *
     * @param	string	$_where			condition for the object to be met including the "WHERE " statement
     * @return	bool					success of the method, false - no success, true - success
     * @throws  FException
     */
    function	fetchFromDbWhere( $_where) {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        $this->_numrows =   0 ;
        /**
         *
         */
        $appUser	=	$this->__getAppUser() ;
        if ( $appUser) {
            $authority	=	self::$db[$this->cacheName] . "." . $this->className.".read" ;
            if ( ! $appUser->isGranted( "dbt", $authority)) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                    "insufficient rights to read objects of type '".$this->className."'! Required: '".$authority."'") ;
            }
        }
        /**
         *
         */
        $keyString	=	"" ;
        $myQuery	=	$this->getQueryObj( "Select") ;
        $_where =   str_replace( array_keys( self::$nameTransTables[ $this->cacheName]), array_values( self::$nameTransTables[ $this->cacheName]), $_where) ;
        $myQuery->addWhere( $_where) ;
        $this->sqlResult      =       FDb::query( $myQuery, self::$db[$this->cacheName]) ;
//        error_log( $myQuery) ;
        if ( !$this->sqlResult) {
            $this->_status  =       -1 ;
        } else {
            $numrows        =       FDb::rowCount( self::$db[$this->cacheName]) ;
            if ( $numrows == 1) {
                $obj    =       FDb::getObject( $this->sqlResult, self::$db[$this->cacheName]) ;
                $this->assignFromObj( $obj) ;
                $this->_valid   =       true ;
            } else if ( $numrows > 1) {
                $this->_numrows     =   $numrows ;
                $this->_status  =       -3 ;
            } else {
                $this->_status  =       -2 ;
                $this->_statusInfo	=	"Object['".$this->className."'], " . $keyString ;
            }
        }
        return $this->_valid ;
    }
    /**
     * getCountWhere( $_where, $_order)
     *
     * Fetches an object from the database
     *
     * This method fetches an object which matches the given condition $_condition from the db.
     * The condition must be given as a full WHERE statement, e.g. "WHERE CustNr = '123456' ".
     *
     * @param	string	$_where		condition for the object to be met including the "WHERE " statement
     * @return	int					success of the method, false - no success, true - success
     * @throws FException
     */
    function	getCountWhere( $_where) {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        /**
         *
         */
        $appUser	=	$this->__getAppUser() ;
        if ( $appUser) {
            $authority	=	self::$db[$this->cacheName] . "." . $this->className.".read" ;
            if ( ! $appUser->isGranted( "dbt", $authority)) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                    "insufficient rights to read objects of type '".$this->className."'! Required: '".$authority."'") ;
            }
        }
        /**
         *
         */
        $myQuery	=	$this->getQueryObj( "Select") ;
        $_where =   str_replace( array_keys( self::$nameTransTables[ $this->cacheName]), array_values( self::$nameTransTables[ $this->cacheName]), $_where) ;
        $myQuery->addWhere( $_where) ;
        $matchingRows	=	FDb::getCount( $myQuery, self::$db[$this->cacheName]) ;
        return $matchingRows ;
    }
    /**
     * first( $_order)
     *
     * Fetches an object from the database
     *
     * This method fetches an object which matches the given condition $_condition from the db.
     * The condition must be given as a full WHERE statement, e.g. "WHERE CustNr = '123456' ".
     *
     * @param	mixed	$_order			condition for the object to be met including the "WHERE " statement
     * @return	bool					success of the method, false - no success, true - success
     */
    function	first( $_where="", $_order="") {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        $keyString	=	"" ;
        $_where =   str_replace( array_keys( self::$nameTransTables[ $this->cacheName]), array_values( self::$nameTransTables[ $this->cacheName]), $_where) ;
        $_order =   str_replace( array_keys( self::$nameTransTables[ $this->cacheName]), array_values( self::$nameTransTables[ $this->cacheName]), $_order) ;
        $myQuery	=	$this->getQueryObj( "Select") ;
        $myQuery->addWhere( $_where) ;
        $myQuery->addOrder( $_order) ;
        $myQuery->addLimit( new FSqlLimit( 0, 1)) ;
//        error_log( $myQuery->getQuery()) ;
        $this->sqlResult      =       FDb::query( $myQuery, self::$db[$this->cacheName]) ;
        if ( !$this->sqlResult) {
            $this->_status  =       -1 ;
        } else {
            $numrows        =       FDb::rowCount( self::$db[$this->cacheName]) ;
            if ( $numrows == 1) {
                $obj    =       FDb::getObject( $this->sqlResult, self::$db[$this->cacheName]) ;
                $this->assignFromObj( $obj) ;
                $this->_valid   =       true ;
            } else if ( $numrows > 1) {
                $this->_status  =       -3 ;
            } else {
                $this->_status  =       -2 ;
                $this->_statusInfo	=	"Object['".$this->className."'], " . $keyString ;
            }
        }
        return $this->_valid ;
    }
    /**
     * fetchFromXML( $_filemame)
     *
     * This methods populats the attributes from the given XML file.
     * The XML file needs to be in the exact format as the generated XML files
     * are. However, missing attributes in the XML file are *NOT* a reason for failure.
     * IMPORTANT: this method EXPECTS the XML input to be encoded in the UTF-8 charset.
     * If this is not the case than some iconv(...) needs to be added (as commented).
     *
     * @param	string	$_filename		name of the file
     * @return	bool					success of the method, false - no success, true - success
     * @throws  FException
     */
    function	fetchFromXML( $_filename) {
        $iFile	=	fopen( $_filename, "r") ;
        if ( $iFile) {
            $contents	=	fread( $iFile, filesize( $_filename));
            fclose( $iFile) ;
            $xml	=	new XMLReader() ;
            $xml->XML( $contents) ;
            $objectValid	=	false ;
            $buffer	=	"" ;
            $this->_fetchFromXML( $xml) ;
        } else {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_filename')",
                "object[".$this->cacheName."], can not open file '$_filename'") ;
        }
        $this->_valid	=	true ;
        return true ;
    }
    /**
     * _fetchFromXML( $_xml)
     *
     * This methods populats the attributes from the given XML object.
     * However, missing attributes in the XML file are *NOT* a reason for failure.
     * IMPORTANT: this method EXPECTS the XML input to be encoded in the UTF-8 charset.
     * If this is not the case than some iconv(...) needs to be added (as commented).
     *
     * @param	string	$_filename		name of the file
     * @return	bool					success of the method, false - no success, true - success
     */
    function	_fetchFromXML( $_xml) {
        $buffer	=	"" ;
        $this->_valid	=	false ;
        while ( $_xml->read() && ! $this->_valid) {
            switch ( $_xml->nodeType) {
                case	XmlReader::ELEMENT	:			// start element
                    if ( strcmp( $_xml->name, $this->className) == 0) {
                    }
                    break ;
                case	XmlReader::TEXT	:			// text node
                case	XmlReader::CDATA	:
                    $buffer	=	$_xml->value ;
                    break ;
                case	XmlReader::SIGNIFICANT_WHITESPACE	:			// whitespace node
                    break ;
                case	XmlReader::END_ELEMENT	:			// end element
                    if ( strcmp( $_xml->name, $this->className) == 0) {
                        $this->_valid	=	true ;
                    } else {
                        $colName	=	$_xml->name ;
                        if ( isset( $this->$colName)) {
                            $this->$colName	=	$buffer ;
                            $buffer	=	"" ;
                        }
                    }
                    break ;
                case	XmlReader::END_ENTITY	:			// end entity
                    break ;
            }
        }
        return $this->_valid ;
    }
    /**
     * existWhere( $_where)
     *
     * Checks how many objects with the given criteria exist in the database
     *
     * This method fetches an object which matches the given condition $_condition from the db.
     * The condition must be given as a full WHERE statement, e.g. "WHERE CustNr = '123456' ".
     *
     * @param	string	$_where			condition for the object to be met including the "WHERE " statement
     * @return	int					number of objects
     */
    function	existWhere( $_where) {
        $this->_status	=	1 ;
        $this->_valid	=	false ;
        $myQuery	=	new FSqlSelect( $this->className) ;
        $myQuery->addWhere( $_where) ;
        $matchingRows	=	FDb::getCount( $myQuery, self::$db[$this->cacheName]) ;
        $this->sqlResult	=	FDb::query( $myQuery, self::$db[$this->cacheName]) ;
        if ( ! $this->sqlResult) {
            $this->_status	=	-1 ;
            $matchingRows	=	-1 ;
        } else {
            $this->_valid   =       true ;
        }
        return $matchingRows ;
    }
    /**
     * addCol( $_attrName, $_attrType)?
     *
     * Add columns to the list of manifested attributes
     * This methods adds an attribute with the name $_attrName and the type $_attrType
     * to the list of manifested attributes for the object.
     * This method is useful ONLY in preparation for a subsequent tableFromDb(...) operation.
     * After columns have been added to the manifest all subsequent calls to fetchFromDb(), storeInDb()
     * and fetchFromDb() will fail in the database engine since non-existing columns would be queried.
     *
     * @param	string	$attrName       condition for the object to be met
     * @param	string	$attrType		database alias
     * @return	bool					success of the method, false - no success, true - success
     */
    function	addCol( $attrName, $attrType) {
        $this->addAttrNames[]	=	$attrName ;
        $this->addAttrTypes[ $attrName]	=	$attrType ;
        $this->$attrName	=	"" ;
    }
    /**
     *
     * @param int	$_startRow
     */
    function	setStartRow( $_startRow) {	$this->startRow	=	$_startRow ;	}
    function	setRowCount( $_rowCount) {	$this->rowCount	=	$_rowCount ;	}
    function	getStartRow() {		return $this->startRow ;	}
    function	getRowCount() {		return $this->rowCount ;	}
    function	setPage( $_startRow, $_rowCount, $_step) {
        $this->startRow	=	$_startRow ;
        $this->rowCount	=	$_rowCount ;
        $this->step		=	$_step ;
    }
    function	getCount( $_query) {
        return FDb::getCount( $_query, self::$db[$this->cacheName]) ;
    }
    function	getSum( $_query, $_sumcolumn) {
        return FDb::getSum( $_query, self::$db[$this->cacheName], $_sumcolumn) ;
    }

    /**
     * tableFromQuery( $_query, $_asXML, $_db)
     *
     * Return a string containing an XML formatted table with all records resulting from the given
     * $_query from the database alias $_db.
     * If $_className is specified
     * this name will be used as the classname instead of the class given during instantiation.
     * @param   FSqlQuery $_query
     * @param   string $_db
     * @throws  FException
     * @return  string
     */
    function	tableFromQuery( $_query, $_className="", $_db="def") {
        /**
         *
         */
        $appUser	=	$this->__getAppUser() ;
        if ( $appUser) {
            $authority	=	self::$db[$this->cacheName] . "." . $this->className.".read" ;
            if ( ! $appUser->isGranted( "dbt", $authority)) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                    "insufficient rights to read objects of type '".$this->className."'! Required: '".$authority."'") ;
            }
        }
        /**
         *
         */
        $this->pageCount	=	-1 ;
        $ret	=	"" ;
        /**
         *
         */
//		$this->className	=	$_query->table ;
        /**
         * figure out how many matching lines there are
         */
        $matchingRows	=	FDb::getCount( $_query, self::$db[$this->cacheName]) ;
        if ( ! isset( $this->step)) {
            $this->step	=	"thisPage" ;
            if ( isset( $_POST['step'])) {
                $this->step	=	$_POST['step'] ;
            }
        }
        /**
         *
         */
        switch ( $this->step) {
            case	"firstPage"	:
                $this->startRow	=	0 ;
                break ;
            case	"previousPage"	:
                if ( $this->startRow > 0) {
                    $this->startRow	-=	$this->rowCount ;
                } else {
                    $this->startRow	=	0 ;
                }
                break ;
            case	"oneBackward"	:
                if ( $this->startRow > 0) {
                    $this->startRow	-=	1 ;
                } else {
                    $this->startRow	=	0 ;
                }
                break ;
            case	"thisPage"	:
                break ;
            case	"oneForward"	:
                $this->startRow	+=	1 ;
                if ( ( $this->startRow + $this->rowCount) >= $matchingRows )
                    $this->startRow	=	$matchingRows - $this->rowCount ;
                break ;
            case	"nextPage"	:
                $this->startRow	+=	$this->rowCount ;
                if ( ( $this->startRow + $this->rowCount) >= $matchingRows)
                    $this->startRow	=	$matchingRows - $this->rowCount ;
                break ;
            case	"lastPage"	:
                $this->startRow	=	$matchingRows - $this->rowCount ;
                break ;
        }
        if ( $this->startRow < 0)
            $this->startRow	=	0 ;
        $ret	=	"" ;
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        if ( ! $_query->limit) {
            $_query->addLimit( new FSqlLimit( $this->startRow, $this->rowCount)) ;
        }
        try {
            $this->sqlResult      =       FDb::query( $_query, self::$db[$this->cacheName]) ;
            if ( !$this->sqlResult) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( <FSqlQuery>)",
                    "sqlResult error [".$_query."]") ;
            } else {
                //	IF no name five, take the name of 'this' class
                if ( $_className == "") {
                    $_className	=	$this->className ;
                }
                $ret	=	"<Table" . $_className . ">\n" ;
                $ret	.=	"<TableInfo>\n" ;
                $ret	.=	"<StartRow>".$this->startRow."</StartRow>\n" ;
                $ret	.=	"<RowCount>".$this->rowCount."</RowCount>\n" ;
                $ret	.=	"<PageCount>".$this->pageCount."</PageCount>\n" ;
                $ret	.=	"<TotalRows>".$matchingRows."</TotalRows>\n" ;
                $ret	.=	"<Dataset>".$this->dataset."</Dataset>\n" ;
                $ret	.=	"<Level>".$this->level."</Level>\n" ;
                $ret	.=	"<MaxLevel>".$this->maxLevel."</MaxLevel>\n" ;
                $ret	.=	"</TableInfo>\n" ;
                while ( $row = FDb::getRow( $this->sqlResult, self::$db[$this->cacheName])) {
                    $this->_assignFromObj( $row) ;
                    $ret	.=	$this->getXMLF( $_className) ;
                    $ret	.=	"\n" ;
                }
                $ret	.=	"</Table" . $_className . ">\n" ;
                $this->_status  =       0 ;
            }
        } catch ( Exception $e) {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_query', '$_className', '$_db')",
                $e) ;
        }
//        $this->jsonTableFromQuery( $_query, $_className, $_db) ;
        return $ret ;
    }
    /**
     * tableFromQuery( $_query, $_asXML, $_db)
     *
     * Return a string containing an XML formatted table with all records resulting from the given
     * $_query from the database alias $_db.
     * If $_className is specified
     * this name will be used as the classname instead of the class given during instantiation.
     * @param FSqlQuery $_query
     * @param string $_db
     * @throws FException
     * @return string
     */
    function	jsonTableFromQuery( $_query, $_className="", $_db="def") {
        /**
         *
         */
        $appUser	=	$this->__getAppUser() ;
        if ( $appUser) {
            $authority	=	self::$db[$this->cacheName] . "." . $this->className.".read" ;
            if ( ! $appUser->isGranted( "dbt", $authority)) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                    "insufficient rights to read objects of type '".$this->className."'! Required: '".$authority."'") ;
            }
        }
        /**
         *
         */
        $this->pageCount	=	-1 ;
        $ret	=	"" ;
        /**
         *
         */
//		$this->className	=	$_query->table ;
        /**
         * figure out how many matching lines there are
         */
        $matchingRows	=	FDb::getCount( $_query, self::$db[$this->cacheName]) ;
        if ( ! isset( $this->step)) {
            $this->step	=	"thisPage" ;
            if ( isset( $_POST['step'])) {
                $this->step	=	$_POST['step'] ;
            }
        }
        /**
         *
         */
        switch ( $this->step) {
            case	"firstPage"	:
                $this->startRow	=	0 ;
                break ;
            case	"previousPage"	:
                if ( $this->startRow > 0) {
                    $this->startRow	-=	$this->rowCount ;
                } else {
                    $this->startRow	=	0 ;
                }
                break ;
            case	"oneBackward"	:
                if ( $this->startRow > 0) {
                    $this->startRow	-=	1 ;
                } else {
                    $this->startRow	=	0 ;
                }
                break ;
            case	"thisPage"	:
                break ;
            case	"oneForward"	:
                $this->startRow	+=	1 ;
                if ( ( $this->startRow + $this->rowCount) >= $matchingRows )
                    $this->startRow	=	$matchingRows - $this->rowCount ;
                break ;
            case	"nextPage"	:
                $this->startRow	+=	$this->rowCount ;
                if ( ( $this->startRow + $this->rowCount) >= $matchingRows)
                    $this->startRow	=	$matchingRows - $this->rowCount ;
                break ;
            case	"lastPage"	:
                $this->startRow	=	$matchingRows - $this->rowCount ;
                break ;
        }
        if ( $this->startRow < 0)
            $this->startRow	=	0 ;
        $ret	=	"" ;
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        if ( ! $_query->limit) {
            $_query->addLimit( new FSqlLimit( $this->startRow, $this->rowCount)) ;
        }
        try {
            $this->sqlResult      =       FDb::query( $_query, self::$db[$this->cacheName]) ;
            if ( !$this->sqlResult) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( <FSqlQuery>)",
                    "sqlResult error [".$_query."]") ;
            } else {
                //	IF no name given, take the name of 'this' class
                if ( $_className == "") {
                    $_className	=	$this->className ;
                }
                $ret	=	"{\n" ;
                $ret	.=	"\t\"TableInfo\":{\n" ;
                $ret	.=	"\t\t\"StartRow\":\"".$this->startRow."\",\n" ;
                $ret	.=	"\t\t\"RowCount\":\"".$this->rowCount."\",\n" ;
                $ret	.=	"\t\t\"PageCount\":\"".$this->pageCount."\",\n" ;
                $ret	.=	"\t\t\"TotalRows\":\"".$matchingRows."\",\n" ;
                $ret	.=	"\t\t\"Dataset\":\"".$this->dataset."\",\n" ;
                $ret	.=	"\t\t\"Level\":\"".$this->level."\",\n" ;
                $ret	.=	"\t\t\"MaxLevel\":\"".$this->maxLevel."\"\n" ;
                $ret	.=	"\t},\n" ;
                $ret	.=	"\t\"".$this->className."s\":[\n" ;
                $subRet	=	"" ;
                while ( $row = FDb::getRow( $this->sqlResult, self::$db[$this->cacheName])) {
                    if ( $subRet != "") {
                        $subRet	.=	",\n" ;
                    }
                    $this->_assignFromObj( $row) ;
                    $subRet	.=	$this->_exportJSON( $_className) ;
                }
                $subRet	.=	"]\n" ;
                $ret	.=	$subRet ;
                $ret	.=	"}\n" ;
                $this->_status  =       0 ;
            }
        } catch ( Exception $e) {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_query', '$_className', '$_db')",
                $e) ;
        }
//        error_log( $ret) ;
        return $ret ;
    }

    /**
     * _firstFromDb( $_where, $_start)
     *
     * Get first object matching given criteria from db
     *
     * This methods fetches the first object matching the given condition
     * from the database db.
     *
     * @param	string	$_where			condition for the object to be met. Must *NOT* include the "WHERE "
     * @param	int		$_start			start position of the
     * @return	bool					success of the method, false - no success, true - success
     * @throws FException
     */
    function	_firstFromDb( $_where, $_start=0) {
        $keyCol	=	$this->keyCol ;
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        $this->_currRow	=	$_start ;
        $myQuery	=	$this->getQueryObj( "Select") ;
        $_where =   str_replace( array_keys( self::$nameTransTables[ $this->cacheName]), array_values( self::$nameTransTables[ $this->cacheName]), $_where) ;
        $myQuery->addWhere( $_where) ;
        $numrows	=	FDb::getCount( $myQuery, self::$db[$this->cacheName]) ;
        try {
            $this->sqlIterResult	=	FDb::query( $myQuery, self::$db[$this->cacheName]) ;
            if ( $numrows == 1) {
                $row    =	mysqli_fetch_assoc( $this->sqlIterResult) ;
                $this->_assignFromObj( $row) ;
                $this->_valid   =       true ;
                $this->_lastRow   =       0 ;
            } else if ( $numrows > 1) {
                $row    =       mysqli_fetch_assoc( $this->sqlIterResult) ;
                $this->_assignFromObj( $row) ;
                $this->_valid   =       true ;
                $this->_lastRow   =       $numrows - 1 ;
            } else {
                $this->_status   =       -5 ;
            }
        } catch ( Exception $e) {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_where', $_start)",
                $e) ;
        }
        return $this->_valid ;
    }
    /**
     * _nextFromDb()
     *
     * Get next object matching given criteria from db
     *
     * This methods fetches the first object matching the given condition
     * from the database db.
     *
     * @return	bool					success of the method, false - no success, true - success
     */
    function	_nextFromDb() {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        if ( $this->_currRow < $this->_lastRow) {
            $this->_currRow++ ;
            $row    =       mysqli_fetch_assoc( $this->sqlIterResult) ;
            $this->_assignFromObj( $row) ;
            $this->_valid	=	true ;
        } else {
            $this->_status	=	-3 ;
        }
        return $this->_valid ;
    }
    /**
     * updateInDb( $_exec)
     *
     * Update object in db
     *
     * Updates the given object in the db. The update is performed using the Id
     * as criterium. The Id and the Key of the object are *NOT* updated.
     *
     * @param	bool	$_exec			database alias
     * @return	bool					success of the method, false - no success, true - success
     * @throws  FException
     */
    function	updateInDb( $_exec=true) {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        /**
         * perform basic authorization for this dbt function
         */
        $appUser	=	$this->__getAppUser() ;
        if ( $appUser) {
            $authority	=	self::$db[$this->cacheName] . "." . $this->className.".update" ;
            if ( ! $appUser->isGranted( "dbt", $authority)) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                    "insufficient rights to update objects of type '".$this->className."'! Required: '".$authority."'") ;
            }
        } else {
        }
        /**
         *
         */
        $idKey	=	self::$idKey[$this->cacheName] ;
        $myQuery	=	$this->getQueryObj( "Update") ;
        $myQuery->addWhere( $idKey . "='" . FDb::escapeString( $this->$idKey, self::$db[$this->cacheName]) . "' " ) ;
        foreach ( $this->attrs as $colName => $attrInfo) {
            if ( $colName != $idKey && $colName != $this->keyCol && $attrInfo->attrType != "uniqueidentifier") {
                $authority	=	self::$db[$this->cacheName] . "." . $this->className . ".update." . $colName ;
                if ( $appUser) {
                    if ( ! $appUser->isGranted( "dbt", $authority, false)) {
                        throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                            "insufficient rights to update field '".$colName."'!") ;
                    }
                    if ( ! $appUser->isValueGranted( "dbv", $this->className.".".$colName, FDb::escapeString( $this->$colName, self::$db[$this->cacheName]), false)) {
                        throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                            "value not allowed '".FDb::escapeString( $this->$colName, self::$db[$this->cacheName])."'!") ;
                    }
                }
                if (( $this->$colName === "NULL" || $this->$colName === null) && $attrInfo->Null == "YES") {
                    $myQuery->addField( $colName) ;
                    $myQuery->addValue( "null") ;
                } else if ( $this->$colName === "NULL") {
                    $myQuery->addField( $colName) ;
                    /**
                     * special MS handling:
                     * UniqueIDentifier is handled as string but MUST be send to the server w/o '' !!!
                     */
                    if ( $this->attrs[ $colName]->attrType != "uniqueidentifier") {
                        $myQuery->addValue( "'" . $attrInfo->Default. "' ") ;
                    } else {
                        $myQuery->addValue( " " . $attrInfo->Default. "  ") ;
                    }
                } else {
                    $myQuery->addField( $colName) ;
                    $myQuery->addValue( "'" . FDb::escapeString( $this->$colName, self::$db[$this->cacheName]) . "' ") ;
                }
            }
        }
        $this->sqlResult      =       FDb::query( $myQuery, self::$db[$this->cacheName]) ;
        if ( !$this->sqlResult) {
            $this->_status  =       -1 ;
        } else {
            $this->_valid	=	true ;
        }
        /**
         *
         */
//        if ( $this->traceUpdate) {
//            $myFile	=	fopen( $this->path->Archive . "XML/Up/" . "myUpd.sql", "a+") ;
//            fwrite( $myFile, $_query . "\n") ;
//            fclose( $myFile) ;
//        }
        return $this->_valid ;
    }

    /**
     * updateColInDb( $_col)
     *
     * Update object in db
     *
     * Updates a specific attribute of the given object in the db.
     * The update is performed using the Id
     * as criterium. The Id and the Key of the object are *NOT* updated.
     *
     * @param	string	$_col			name of the column to update
     * @return	bool					success of the method, false - no success, true - success
     * @throws  FException
     */
    function	updateColInDb( $_col) {
        $idKey  =   "" ;
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        /**
         * perform basic authorization for this dbt function
         */
        $appUser	=	$this->__getAppUser() ;
        if ( $appUser) {
            $authority	=	self::$db[$this->cacheName] . "." . $this->className.".update" ;
            if ( ! $appUser->isGranted( "dbt", $authority)) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                    "insufficient rights to update objects of type '".$this->className."'! Required: '".$authority."'") ;
            }
        }
        /**
         *
         */
        $idKey	=	self::$idKey[$this->cacheName] ;
        $myQuery	=	$this->getQueryObj( "Update") ;
        $myQuery->addWhere( $idKey . "='" . FDb::escapeString( $this->$idKey, self::$db[$this->cacheName]) . "' " ) ;
        $cols	=	explode( ",", $_col) ;
        foreach ( $cols as $colName) {
            $colName    =   $this->_getDbAttr( $colName) ;
            if ( $colName != "Id" && $colName != $this->keyCol && $this->attrs[$colName]->Type != "uniqueidentifier") {
                if ( $this->$colName === null && $this->attrs[$colName]->Null == "YES") {
                    $myQuery->addField( $colName) ;
                    $myQuery->addValue( "null") ;
                } else if ( $this->$colName === null) {
                    $myQuery->addField( $colName) ;
                    $myQuery->addValue( "'" . $this->attrs[$colName]->Default. "' ") ;
                } else {
                    $myQuery->addField( $colName) ;
                    $myQuery->addValue( "'" . FDb::escapeString( $this->$colName, self::$db[$this->cacheName]) . "' ") ;
                }
            }
        }
        $this->sqlResult      =       FDb::query( $myQuery, self::$db[$this->cacheName]) ;
        if ( !$this->sqlResult) {
            $this->_status  =       -1 ;
        } else {
            $this->_valid	=	true ;
        }
        /**
         *
         */
//        if ( $this->traceUpdate) {//           $myFile	=	fopen( $this->path->Archive . "XML/Up/" . "myUpd.sql", "a+") ;
//            fwrite( $myFile, $query . "\n") ;
//            fclose( $myFile) ;
//        }
        return $this->_valid ;
    }
    /**
     *fullUpdateInDb()
     *
     * Full update of object in db
     *
     * Updates the given object in the db. The update is performed using the Id
     * as criterium. The Id and the Key of the object are also updated.
     * This makes the usage of this function VERY DANGEROUS, as it may render
     * references to key columns INVALID. USE WITH CAUTION!
     *
     * @param	string	$_col			database alias
     * @return	bool					success of the method, false - no success, true - success
     */
    function	updateAllInDb( $_col) {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        $idKey	=	self::$idKey[$this->cacheName] ;
        $myQuery	=	new FSqlUpdate( $this->className, $this) ;
        $myQuery->addWhere( $idKey . "='" . FDb::escapeString( $this->$idKey, self::$db[$this->cacheName]) . "' " ) ;
        $query	=	"UPDATE " . FDb::fTableName( $this->className) . " " ;
        $query	.=	"SET " ;
        $ndx	=	0 ;
        $cols	=	explode( ",", $_col) ;
        foreach ( $cols as $colName => $attrInfo) {
            if ( $this->$colName === null && $attrInfo->Null == "YES") {
                $myQuery->addField( $colName) ;
                $myQuery->addValue( "null") ;
            } else if ( $this->$colName === null) {
                $myQuery->addField( $colName) ;
                $myQuery->addValue( "'" . $attrInfo->Default. "' ") ;
            } else {
                $myQuery->addField( $colName) ;
                $myQuery->addValue( "'" . FDb::escapeString( $this->$colName, self::$db[$this->cacheName]) . "' ") ;
            }
        }
        $this->sqlResult      =       FDb::query( $myQuery, self::$db[$this->cacheName]) ;
        if ( !$this->sqlResult) {
            $this->_status  =       -1 ;
        } else {
            $this->_valid	=	true ;
        }
        /**
         *
         */
        if ( $this->traceUpdate) {
            $myFile	=	fopen( $this->path->Archive . "XML/Up/" . "myUpd.sql", "a+") ;
            fwrite( $myFile, $query . "\n") ;
            fclose( $myFile) ;
        }
        return $this->_valid ;
    }
    /**
     *fullUpdateInDb()
     *
     * Full update of object in db
     *
     * Updates the given object in the db. The update is performed using the Id
     * as criterium. The Id and the Key of the object are also updated.
     * This makes the usage of this function VERY DANGEROUS, as it may render
     * references to key columns INVALID. USE WITH CAUTION!
     *
     * @param	string	$_col			database alias
     * @param   string  $_where         where condition
     * @return	bool					success of the method, false - no success, true - success
     */
    function	updateColInDbWhere( $_col, $_where) {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        $idKey	=	self::$idKey[$this->cacheName] ;
        $myQuery	=	$this->getQueryObj( "Update") ;
        $cols	=	explode( ",", $_col) ;
        foreach ( $cols as $colName => $attrInfo) {
            if ( $this->$colName === null && $attrInfo->Null == "YES") {
                $myQuery->addField( $colName) ;
                $myQuery->addValue( "null") ;
            } else if ( $this->$colName === null) {
                $myQuery->addField( $colName) ;
                $myQuery->addValue( "'" . $attrInfo->Default. "' ") ;
            } else {
                $myQuery->addField( $colName) ;
                $myQuery->addValue( "'" . FDb::escapeString( $this->$colName, self::$db[$this->cacheName]) . "' ") ;
            }
        }
        $myQuery->addWhere( $_where ) ;
        $this->sqlResult      =       FDb::query( $myQuery, self::$db[$this->cacheName]) ;
        if ( !$this->sqlResult) {
            $this->_status  =       -1 ;
        } else {
            $this->_valid	=	true ;
        }
        /**
         *
         */
//        if ( $this->traceUpdate) {
//            $myFile	=	fopen( $this->path->Archive . "XML/Up/" . "myUpd.sql", "a+") ;
//            fwrite( $myFile, $query . "\n") ;
//            fclose( $myFile) ;
//        }
        return $this->_valid ;
    }
    /**
     * removeFromDb()
     *
     * Remove object from db
     *
     * Removed the given object from the db. The removal is performed using the Id
     * as criterium.
     *
     * @param	string	$_db			database alias
     * @return	bool					success of the method, false - no success, true - success
     */
    function	removeFromDb() {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        /**
         * perform basic authorization for this dbt function
         */
        $appUser	=	$this->__getAppUser() ;
        if ( $appUser) {
            $authority	=	self::$db[$this->cacheName] . "." . $this->className.".delete" ;
            if ( ! $appUser->isGranted( "dbt", $authority)) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                    "insufficient rights to delete objects of type '".$this->className."'! Required: '".$authority."'") ;
            }
        }
        /**
         *
         */
        $idKey	=	self::$idKey[$this->cacheName] ;
        $myQuery	=	$this->getQueryObj( "Delete") ;
        $myQuery->addWhere( $idKey . "='" . FDb::escapeString( $this->$idKey, self::$db[$this->cacheName]) . "' " ) ;
        $this->sqlResult	=	FDb::query( $myQuery, self::$db[$this->cacheName]) ;
        if ( !$this->sqlResult) {
            $this->_status	=	-1 ;
        } else {
            $this->_valid	=	false ;
        }
        return $this->_valid ;
    }
    /**
     * removeFromDbWhere()
     *
     * Remove object from db
     *
     * Removed the given object from the db. The removal is performed using the Id
     * as criterium.
     *
     * @param	string	$_where			database alias
     * @return	bool					success of the method, false - no success, true - success
     * @throws  FException
     */
    function	removeFromDbWhere( $_where) {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        /**
         * perform basic authorization for this dbt function
         */
        $appUser	=	$this->__getAppUser() ;
        if ( $appUser) {
            $authority	=	self::$db[$this->cacheName] . "." . $this->className.".delete" ;
            if ( ! $appUser->isGranted( "dbt", $authority)) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( ...)",
                    "insufficient rights to delete objects of type '".$this->className."'! Required: '".$authority."'") ;
            }
        }
        /**
         *
         */
        $myQuery	=	$this->getQueryObj( "Delete") ;
        $myQuery->addWhere( $_where ) ;
        $this->sqlResult	=	FDb::query( $myQuery, self::$db[$this->cacheName]) ;
        if ( !$this->sqlResult) {
            $this->_status	=	-1 ;
        } else {
            $this->_valid	=	false ;
        }
        return $this->_valid ;
    }
    /**
     * getFromJSON( $_json)
     *
     * Assign object attributes from POST variables
     *
     * Assigns all manifested attributes of the object from POST variables.
     * The POST variables need to follow the naming convention '_I'<attributeName>.
     * The Id ist *NOT* assigned.
     * @return	void
     */
    function	getFromJSON( $_json) {
        foreach ( $this->attrs as $colName => $attrInfo) {
            if ( $colName != "Id") {
                if ( isset( $_json->$colName)) {				// general input
                    $this->$colName	=	$_json->$colName ;
                }
            }
        }
    }
    /**
     * getFromPost()
     *
     * Assign object attributes from POST variables
     *
     * Assigns all manifested attributes of the object from POST variables.
     * The POST variables need to follow the naming convention '_I'<attributeName>.
     * The Id ist *NOT* assigned.
     * @return	void
     */
    function	getFromPost() {
        if ( isset( $_POST['json'])) {
            $this->getFromJSON( json_decode( $_POST['json'])) ;
        } else {
            foreach ( $this->attrs as $colName => $attrInfo) {
                /**
                 * we MAY NOT overwrite an 'Id' attribute ...
                 */
                if ( $colName != "Id") {
                    if ( isset( $_POST[ $colName])) {				// general input
                        $myValue	=	$_POST[ $colName] ;
                    } else if ( isset( $_POST[ $colName.$this->className])) {				// general input
                        $myValue	=	$_POST[ $colName.$this->className] ;
                    }
                    if ( isset( $myValue)) {
                        if ( $attrInfo->Null == "YES") {
                            if ( $myValue == "")
                                $myValue	=	null ;
                        }
                        $this->$colName	=	$myValue ;
                        unset( $myValue) ;
                    }
                }
            }
        }
    }
    /**
     * getFromPostL( $_noGet)
     *
     * Assign object attributes from POST variables
     *
     * Assigns all manifested attributes of the object from POST variables.
     * The POST variables need to follow the naming convention '_I'<attributeName>,
     * '_I<attributeName><className>', 'dI'<attributeName> or
     * 'dI<attributeName><className>'
     *
     * @return	void
     */
    function	getFromPostL( $_noGet="") {
        if ( isset( $_POST['json'])) {
            $this->getFromJSON( json_decode( $_POST['json'])) ;
        } else {
            foreach ( $this->attrs as $colName => $attrInfo) {
                if ( $colName != "Id" && $colName != $this->keyCol && strpos( $_noGet, ",$colName,") === false) {
                    if ( isset( $_POST[ $colName])) {				// general input
                        $myValue	=	$_POST[ $colName] ;
                    } else if ( isset( $_POST[ $colName.$this->className])) {				// general input
                        $myValue	=	$_POST[ $colName.$this->className] ;
                    }
                    if ( isset( $myValue)) {
                        if ( $myValue === "") {
                            if ( $attrInfo->Null === "YES")
                                $myValue	=	null ;
                            else
                                $myValue	=	$attrInfo->Default ;
                        }
                        $this->$colName	=	$myValue ;
                        unset( $myValue) ;
                    }
                }
            }
        }
    }
    /**
     * dup( $_master)
     *
     * @param $master
     */
    function	dup( $master) {
        foreach ( $this->attrs as $colName => $attrInfo) {
            $this->$colName	=	$master->$colName ;
        }
        reset( $this->attrs) ;
    }
    /**
     * newKey( $_digits, $_nsStart, $_nsEnd, $_store)
     *
     * Get a new key for the object and stores the object as an empty object in the database.
     * The object is then reloaded.
     * @param int $_digits	number of digits for the key
     * @param string $_nsStart	beginning of the number range within which to fetch the new key
     * @param string $_nsEnd	end of the number range within which to fetch the new key
     * @return void
     * @throws  FException
     */
    function	newKey( $_digits=6, $_nsStart="000000", $_nsEnd="999999", $_store=true) {
        if ( self::$idKey[$this->cacheName]	!=	"Id") {
            try {
                $myConfig	=	EISSCoreObject::__getAppConfig() ;
                $class	=	$this->className ;
                if ( $myConfig->$class->nsStart != "") {
                    $_nsStart	=	$myConfig->$class->nsStart ;
                    $_nsEnd	=	$myConfig->$class->nsEnd ;
                }
                $_digits	=	strlen( $_nsStart) ;
            } catch ( Exception $e) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( $_digits, '$_nsStart', '$_nsEnd', $_store)",
                    $e) ;
            }
            if ( strlen( $_nsStart) != $_digits) {
                error_log( "FDbObject.php::FDbObject::newKey( $_digits, '$_nsStart', '$_nsEnd'): $this->className invalid combination!") ;
            }
            $myQuery	=	"SELECT IFNULL(( SELECT ".$this->keyCol." + 1 FROM ".$this->className." " .
                "WHERE ".$this->keyCol." > '".$_nsStart."' AND ".$this->keyCol." <= '".$_nsEnd."' " .
                "ORDER BY ".$this->keyCol." DESC LIMIT 1 ), ".$_nsStart."+1)  AS newKey" ;
            $myRow	=	FDb::queryRow( $myQuery, self::$db[$this->cacheName]) ;
            $keyCol	=	$this->keyCol ;
            $this->$keyCol	=	sprintf( "%0".$_digits."s", $myRow['newKey']) ;
            if ( $_store) {
                $this->storeInDb() ;
                $this->reload() ;
            } else {
                $this->_valid	=	true ;
            }
            $res	=	$this->$keyCol ;
        } else {
            if ( $_store) {
                $this->storeInDb() ;
                $this->reload() ;
            } else {
                $this->_valid	=	true ;
            }
            $res	=	"void" ;
        }
        return $res ;
    }
    /**
     * newKeyKO( $_digits, $_nsStart, $_nsEnd)
     *
     * Get a new key for the object and returns this key.
     * @param int $_digits	number of digits for the key
     * @param string $_nsStart	beginning of the number range within which to fetch the new key
     * @param string $_nsEnd	end of the number range within which to fetch the new key
     * @return string
     */
    function	newKeyKO( $_digits=6, $_nsStart="000000", $_nsEnd="999999") {
        if ( strlen( $_nsStart) != $_digits) {
            error_log( "FDbObject.php::FDbObject::newKey( $_digits, '$_nsStart', '$_nsEnd'): $this->className invalid combination!") ;
        }
        $myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM '$this->className' " .
            "WHERE  $this->keyCol > $_nsStart AND $this->keyCol <= $_nsEnd " .
            "ORDER BY $this->keyCol DESC LIMIT 1 ), $_nsStart+1)  AS newKey" ;
        $myRow	=	FDb::queryRow( $myQuery, self::$db[$this->cacheName]) ;
        $newKey	=	sprintf( "%0".$_digits."s", $myRow['newKey']) ;
        return $newKey ;
    }
    /**
     * defaultDates()
     *
     * Set all attributes of type date to the current date
     * @return void
     */
    function	defaultDates() {
        foreach ( $this->attrs as $colName => $colInfo) {
            if ( $colName != "Id" && $colName != $this->keyCol) {
                if ( $colInfo->attrType == "date") {
                    $this->$colName	=	$this->today() ;
                }
            }
        }
    }
    /**
     * clearRem()
     *
     */
    function	clearRem() {
        $this->Remark	=	"" ;
    }

    /**
     * clear()
     *
     * Set all attributes of type date to the current date
     * @return void
     */
    function	clear() {
        foreach ( $this->attrs as $colName => $colInfo) {
            switch ( $colInfo->attrType)	{
                case	"date"	:
                case	"smalldatetime"	:
                case	"datetime"	:
                    $this->$colName	=	$this->today() ;
                    break ;
                case 	"bit"	:
                case	"smallint"	:
                case	"int"	:
                case	"tinyint"	:
                case	"bigint"	:
                    $this->$colName	=	0 ;
                    break ;
                case	"float"	:
                case	"double"	:
                    $this->$colName	=	0.0 ;
                    break ;
                case	"decimal"	:
                    $this->$colName	=	0.0 ;
                    break ;
                case	"smallmoney"	:
                case	"money"	:
                    $this->$colName	=	0.0 ;
                    break ;
                case	"enum"	:
                case	"char"	:
                case	"varchar"	:
                case	"mediumtext"	:
                case	"longtext"	:
                case	"text"	:
                    $this->$colName	=	"" ;
                    break ;
                case	"timestamp"	:
                    $this->$colName	=	date( "Y-m-d H:i:s") ;
                    break ;
                case	"uniqueidentifier"	:
                    $this->$colName	=	"" ;
                    break ;
                case	"blob"	:
                case	"tinyblob"	:
                case	"mediumblob"	:
                case	"longblob"	:
                    $this->$colName	=	"" ;
                    break ;
                default	:
                    error_log( "Unidentified datatype ..... " . $colInfo->attrType) ;
                    break ;
            }
        }
    }

    /**
     * clear()
     *
     * Set all attributes of type date to the current date
     * @return void
     */
    function	_initOnSetup() {
        foreach ( $this->attrs as $colName => $colInfo) {
            switch ( $colInfo->attrType)	{
                case	"date"	:
                case	"smalldatetime"	:
                case	"datetime"	:
                    $this->$colName	=	$this->today() ;
                    break ;
                case 	"bit"	:
                case	"smallint"	:
                case	"int"	:
                case	"tinyint"	:
                case	"bigint"	:
                    $this->$colName	=	0 ;
                    break ;
                case	"float"	:
                case	"double"	:
                    $this->$colName	=	0.0 ;
                    break ;
                case	"decimal"	:
                    $this->$colName	=	0.0 ;
                    break ;
                case	"smallmoney"	:
                case	"money"	:
                    $this->$colName	=	0.0 ;
                    break ;
                case	"enum"	:
                case	"char"	:
                case	"varchar"	:
                case	"mediumtext"	:
                case	"longtext"	:
                case	"text"	:
                    $this->$colName	=	"" ;
                    break ;
                case	"timestamp"	:
                    $this->$colName	=	date( "Y-m-d H:i:s") ;
                    break ;
                case	"uniqueidentifier"	:
                    $this->$colName	=	"" ;
                    break ;
                case	"blob"	:
                case	"tinyblob"	:
                case	"mediumblob"	:
                case	"longblob"	:
                    $this->$colName	=	"" ;
                    break ;
                default	:
                    error_log( "Unidentified datatype ..... " . $colInfo->attrType) ;
                    break ;
            }
        }
    }

    /**
     * _getPrevNext( $_query)
     */
    function	_getPrevNext( $_query) {
        $this->sqlResult      =       FDb::query( $_query, self::$db[$this->cacheName]) ;
        if ( ! $this->sqlResult) {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( <FSqlQuery>)",
                "failure trying to receive prev/next object! Invalid result!") ;
        } else {
            $numrows        =       FDb::rowCount( self::$db[$this->cacheName]) ;
            if ( $numrows == 1) {
                $obj    =       FDb::getObject( $this->sqlResult, self::$db[$this->cacheName]) ;
                $this->_assignFromObj( $obj) ;
            } else if ( $numrows == 0)  {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( <FSqlQuery>)",
                    "failure trying to receive prev/next object! rows = 0  >>> '$_query'") ;
            } else {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( <FSqlQuery>)",
                    "failure trying to receive prev/next object! rows > 1 >>> '$_query'") ;
            }
        }
//		$this->reload() ;
    }
    /**
     * getNextAsXML( $_query)
     */
    function	getNextAsXML( $_key="", $_id=-1, $_val="") {
        $reply	=	new Reply( __class__, $this->className) ;
        $keyCol	=	$this->keyCol ;
        try {
            $myQuery	=	$this->getQueryObj( "Select") ;
            $myQuery->addWhere( $this->keyCol." > '" . $this->$keyCol . "' ") ;
            $myQuery->addOrder( $this->keyCol . " ASC ") ;
            $myQuery->addLimit( new FSqlLimit( 0, 1)) ;
            $this->_getPrevNext( $myQuery) ;
            $reply->replyData	=	$this->getAsXML()->replyData ;
        } catch ( FException $e) {
            try {
                $myQuery	=	$this->getQueryObj( "Select") ;
                $myQuery->clearWhere() ;
                $myQuery->addOrder( $this->keyCol . " ASC ") ;
                $myQuery->addLimit( new FSqlLimit( 0, 1)) ;
                $this->_getPrevNext( $myQuery) ;
                $reply->replyData	=	$this->getAsXML()->replyData ;
            } catch ( Exception $e) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
                    $e) ;
            } catch ( FException $e) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
                    $e) ;
            }
        } catch ( Exception $e) {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
                $e) ;
        }
        return $reply ;
    }
    /**
     * getPrevAsXML( $_query)
     */
    function	getPrevAsXML( $_key="", $_id=-1, $_val="") {
        $reply	=	new Reply( __class__, $this->className) ;
        $keyCol	=	$this->keyCol ;
        try {
            $myQuery	=	$this->getQueryObj( "Select") ;
            $myQuery->addWhere( $this->keyCol." < '" . $this->$keyCol . "' ") ;
            $myQuery->addOrder( $this->keyCol . " DESC ") ;
            $myQuery->addLimit( new FSqlLimit( 0, 1)) ;
            $this->_getPrevNext( $myQuery) ;
            $reply->replyData	=	$this->getAsXML()->replyData ;
        } catch ( FException $e) {
            try {
                $myQuery	=	$this->getQueryObj( "Select") ;
                $myQuery->addOrder( $this->keyCol . " DESC ") ;
                $myQuery->addLimit( new FSqlLimit( 0, 1)) ;
                $this->_getPrevNext( $myQuery) ;
                $reply->replyData	=	$this->getAsXML()->replyData ;
            } catch ( Exception $e) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
                    $e) ;
            } catch ( FException $e) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
                    $e) ;
            }
        } catch ( Exception $e) {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
                $e) ;
        }
        return $reply ;
    }
    /**
     * getJSONNext( $_query)
     */
    function	getJSONNext( $_key="", $_id=-1, $_val="") {
        try {
            $keyCol	=	$this->keyCol ;
            $myQuery	=	"SELECT * FROM " . FDb::fTableName( $this->className) . " "
                .	"WHERE " . $this->keyCol . ">'" . $this->$keyCol . "' "
                .	"ORDER BY ".$this->keyCol." ASC "
                .	"LIMIT 1 " ;
            $this->_getPrevNext( $myQuery) ;
            return $this->getJSONComplete() ;
        } catch ( Exception $e) {
            try {
                $keyCol	=	$this->keyCol ;
                $myQuery	=	"SELECT * FROM " . FDb::fTableName( $this->className) . " "
                    .	"ORDER BY ".$this->keyCol." ASC "
                    .	"LIMIT 1 " ;
                $this->_getPrevNext( $myQuery) ;
                return $this->getJSONComplete() ;
            } catch ( Exception $e) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
                    $e) ;
            } catch ( FException $e) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
                    $e) ;
            }
        } catch ( Exception $e) {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
                $e) ;
        }
    }
    /**
     * getJSONPrev( $_query)
     */
    function	getJSONPrev( $_key="", $_id=-1, $_val="") {
        try {
            $keyCol	=	$this->keyCol ;
            $myQuery	=	"SELECT * FROM " . FDb::fTableName( $this->className) . " "
                .	"WHERE " . $this->keyCol . "<'" . $this->$keyCol . "' "
                .	"ORDER BY " . $this->keyCol . " DESC "
                .	"LIMIT 1 " ;
            $this->_getPrevNext( $myQuery) ;
            return $this->getJSONComplete() ;
        } catch ( Exception $e) {
            try {
                $keyCol	=	$this->keyCol ;
                $myQuery	=	"SELECT * FROM " . FDb::fTableName( $this->className) . " "
                    .	"ORDER BY " . $this->keyCol . " DESC "
                    .	"LIMIT 1 " ;
                $this->_getPrevNext( $myQuery) ;
                return $this->getJSONComplete() ;
            } catch ( Exception $e) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
                    $e) ;
            } catch ( FException $e) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
                    $e) ;
            }
        } catch ( Exception $e) {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
                $e) ;
        }
    }
    /**
     * _lock( _$_lockState)
     *
     * Set the Lock state of THIS object to $_lockState and update the object in the database accordingly.
     * This function requires the object to have the 'LockState' attribute.
     * If the object does not have the 'LockState' attribute an exception is thrown
     *
     * @param $_lockState
     * @return bool	validity of this object
     * @throws FException
     */
    function	_lock( $_lockState = 1) {
        $this->_status	=	0 ;
        $this->_valid	=	false ;
        if ( isset( $this->attrs['LockState'])) {
            $this->LockState	=	$_lockState ;
            $this->updateColInDb( "LockState", self::$db[$this->cacheName]) ;
            if ( $_lockState == 0)
                $this->_addRem( "object unlocked!") ;
            else
                $this->_addRem( "object locked!") ;
        } else {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( $_lockState)",
                "object[".$this->cacheName."] has no property 'LockState'!") ;
        }
        return $this->_valid ;
    }
    /**
     * _unlock( _$_lockState)
     *
     * Set the Lock state of THIS object to unloacked (lock=0) and update the object in the database accordingly.
     * This function requires the object to have the 'LockState' attribute.
     * If the object does not have the 'LockState' attribute an exception is thrown
     *
     * @return bool	validity of this object
     */
    function	_unlock() {
        return $this->_lock( 0) ;
    }
    /**
     * addRem()
     *
     * This function adds the given comment to the object.
     * If the object does not have the 'Rem' attribute an
     * exception is thrown (through the used _lock method).
     * @param $_rem
     * @param $_user
     * @throws  FException
     */
    function	_addRem( $_rem, $_user="") {
        try {
            if ( $_user != "") {
                $myText	=	date( "Ymd/His") . ": " . $_user . ": " . $_rem . "\n" ;
            } else {
                $myText	=	date( "Ymd/His") . ": " . $this->UserId . ": " . $_rem . "\n" ;
            }
            if ( isset( $this->attrs['Rem'])) {
                $myText	.=	$this->Rem ;
                $this->Rem	=	$myText ;
                $this->updateColInDb( "Rem", self::$db[$this->cacheName]) ;
            } else if ( isset( $this->attrs['Remark'])) {
                $myText	.=	$this->Remark ;
                $this->Remark	=	$myText ;
                $this->updateColInDb( "Remark", self::$db[$this->cacheName]) ;
            } else {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_rem', '$_user')",
                    "object[".$this->cacheName."] has no property 'Remark'!") ;
            }
        } catch( Exception $e) {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_rem', '$_user')",
                $e) ;
        }
    }
    /**
     * isValid()
     *
     * @return	bool	wether the object is vali dor not
     */
    function	isValid() {		return $this->_valid ;		}
    /**
     * copyFrom( $_src)
     *
     * Copies all attribute values common to the $this object and the $_src object
     * from the $_src object to $this object.
     * @param object $_src
     */
    function	copyFrom( $_src) {
        $idKey	=	self::$idKey[$this->cacheName] ;
        foreach ( $this->attrs as $colName => $attrInfo) {
            if ( $colName != $idKey && substr( $colName, 0, 1) != "_") {
                if ( isset( $_src->$colName) ) {
                    $this->$colName	=	$_src->$colName ;
                }
            }
        }
    }
    /**
     * copyTo( $_dest)
     *
     * Copies all attribute values common to the $this object and the $_dest object
     * from the $this object to $_dest object.
     * @param object $_dest
     */
    function	copyTo( $_dest) {
        foreach ( $this->attrs as $colName => $attrInfo) {
            if ( $colName != "Id" && substr( $colName, 0, 1) != "_") {
                if ( isset( $_dest->$colName)) {
                    $_dest->$colName	=	$this->$colName ;
                }
            }
        }
    }
    /**
     * add( $_key, $_id, $_val)
     * @param   string  $_key
     * @param   int     $_id
     * @param   mixed   $_val
     * @throws FException
     */
    function	add( $_key="", $_id=-1, $_val="") {
        throw new FException( basename( __FILE__), __CLASS__, __METHOD__."(...)",
            "object[".$this->cacheName."]->add must be defined in derived class!") ;
    }
    /**
     * add( $_key, $_id, $_val)
     * @param   string  $_key
     * @param   int     $_id
     * @param   mixed   $_val
     * @throws FException
     */
    function	upd( $_key="", $_id=-1, $_val="") {
        throw new FException( basename( __FILE__), __CLASS__, __METHOD__."(...)",
            "object[".$this->cacheName."]->upd must be defined in derived class!") ;
    }
    /**
     * add( $_key, $_id, $_val)
     * @param   string  $_key
     * @param   int     $_id
     * @param   mixed   $_val
     * @throws FException
     */
    function	del( $_key="", $_id=-1, $_val="") {
        throw new FException( basename( __FILE__), __CLASS__, __METHOD__."(...)",
            "object[".$this->cacheName."]->del must be defined in derived class!") ;
    }
    /**
     * add( $_key, $_id, $_val)
     * @param   string  $_key
     * @param   int     $_id
     * @param   mixed   $_val
     * @throws FException
     */
    function	copy( $_key="", $_id=-1, $_val="") {
        throw new FException( basename( __FILE__), __CLASS__, __METHOD__."(...)",
            "object[".$this->cacheName."]->copy must be defined in derived class!") ;
    }
    /**
     * mayNull( $_attr)
     *
     * Return information about the null-value option for the given attribute.
     *
     *	@param	string		$_attr	name of the attribute to enquire information about
     *	@return	mixed				info about the null-value for the given attribute
     *	@throws	Exception when attribute not defined for this object
     *
     *
     */
    function	mayNull( $_attr) {
        $status	=	false ;
        if ( isset( $this->attrs[$_attr]->Null)) {
            $status	=	$this->attrs[$_attr]->Null ;
        } else {
            FDbg::abort() ;
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_attr')",
                "attribute not defined") ;
        }
        return $status ;
    }

    /**
     * define the iterator interface
     */

    /**
     * setIterCond
     * set iteration condition
     * example: setIterCond( "ArtikelNr = '123456') ;
     * @param	string	$_cond	condition for the where clause
     */
    function	clearIterCond() {
        $this->iterQuery->clearWhere() ;
    }
    function	setIterCond( $_cond) {
        $_cond =   str_replace( array_keys( self::$nameTransTables[ $this->cacheName]), array_values( self::$nameTransTables[ $this->cacheName]), $_cond) ;
        $this->iterQuery->addWhere( $_cond) ;
    }

    /**
     * clearIterOrder
     * clear iteration ordering
     * example: setIterOrder( "ORDER BY ArtikelNr DESC ") ;
     */
    function	clearIterOrder() {
        $this->iterQuery->clearOrder() ;
    }
    /**
     * setIterOrder
     * set iteration ordering
     * example: setIterOrder( "ORDER BY ArtikelNr DESC ") ;
     * @param	string	$_order	order statement
     */
    function	setIterOrder( $_order) {
        $_order =   str_replace( array_keys( self::$nameTransTables[ $this->cacheName]), array_values( self::$nameTransTables[ $this->cacheName]), $_order) ;
        $this->iterQuery->addOrder( $_order) ;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $_join
     * @param unknown_type $_joinCols
     */
    function	setIterJoin( $_join, $_joinCols) {
        $this->iterJoin	=	$_join ;
        $this->iterJoinCols	=	$_joinCols ;
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::current()
     */
    function	current() {
        return $this ;
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::key()
     */
    function	key() {
        $keyCol	=	$this->keyCol ;
        $key    =   $this->$keyCol ;
        return $key ;
    }

    /**
     * place the iterator on the next element
     * here:
     * (non-PHPdoc)
     * @see Iterator::next()
     */
    function	next() {
        if ( $this->iterCount > 0) {
            try {
                $obj    =       FDb::getObject( $this->sqlIterResult, self::$db[$this->cacheName]) ;
                $this->assignFromObj( $obj) ;
                $this->_valid   =       true ;
                $this->iterCount-- ;
            } catch ( Exception $e) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."()", $e) ;
            }
        } else {
            $this->_valid	=	false ;
        }
    }

    /**
     * place the iterator on the first element
     * here: obtain the number of matching elements, submit the query and pull the first record into $this
     * @throws FException
     */
    function	rewind() {
        $this->iterCount	=	FDb::getCount( $this->iterQuery, self::$db[$this->cacheName]) ;
//        error_log( $this->iterQuery) ;
        if ( $this->iterQuery->limit != null)
            if ( $this->iterQuery->limit->count < $this->iterCount)
                $this->iterCount	=	$this->iterQuery->limit->count ;
        try {
//            error_log( $this->iterQuery) ;
            $this->sqlIterResult      =       FDb::query( $this->iterQuery, self::$db[$this->cacheName]) ;
            if ( $this->iterCount > 0) {
                $obj    =       FDb::getObject( $this->sqlIterResult, self::$db[$this->cacheName]) ;
                $this->assignFromObj( $obj) ;
                $this->_valid   =       true ;
                $this->iterCount-- ;
            } else {
                $this->_valid   =       false ;
            }
        } catch ( Exception $e) {
            throw new FException( basename( __FILE__), __CLASS__, __METHOD__."()",
                $e) ;
        }
    }
    /**
     * (non-PHPdoc)
     * @see Iterator::valid()
     */
    function	valid() {
        return $this->_valid ;
    }
    /**
     *
     */
    function	dumpDb() {
        foreach ( self::$db as $key => $value) {
        }
    }

    /**
     * returns a quera object to deal with the database
     * will fail non-graciously if provided driver can npot be loaded; this happens solely with un-clean
     * initialization of the FDb layer.
     *
     * @param   string          $_type      type of access object to retrieve:
     *                                      'Select'=
     *                                      'Insert'=
     *                                      'Update'=
     *                                      'Delete'=
     *                                      'Join'=
     *                                      'Structure'=
     * @return  FSqlQuery                   query object
     */
    function	getQueryObj( $_type) {
        $obj	=	FDb::getQueryObj( $_type, $this->tableName, self::$db[$this->cacheName]) ;
        return $obj ;
    }

    /**
     * @param string $_le
     */
    function	dumpStructure( $_le="\n") {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, "dumpStructure( )") ;
        error_log( sprintf( "Class: %s \n", $this->className)) ;
        foreach ( $this->attrs as $colName => $attrInfo) {
            error_log( sprintf( "\tAttribute: %s \n", $colName)) ;
        }
    }

    /**
     * @param string $_le
     * @return string
     */
    function	__dump( $_le="\n") {
        $buffer	=	__CLASS__ . " Object dump ........: {$_le}" ;
        $buffer	.=	"    Database....... : " . self::$db[$this->cacheName] . "{$_le}" ;
        $buffer	.=	"    Driver......... : " . FDb::getDriver( self::$db[$this->cacheName]) . "{$_le}" ;
        $buffer	.=	"    Driver......... : " . FDb::getDriver( self::$db[$this->cacheName]) . "{$_le}" ;
        $buffer	.=	"    Class name..... : " . $this->className . "{$_le}" ;
        $buffer	.=	"    Table name..... : " . $this->cacheName. "{$_le}" ;
        $buffer	.=	"    Valid ......... : " . ( $this->_valid ? "true" : "false") . "{$_le}" ;
        foreach ( $this->attrs as $colName => $attrInfo) {
            $buffer	.=	"        Field .... : " . $colName . " := " . ( $this->$colName !== null ? $this->$colName : "<-NULL->") . "{$_le}" ;
        }
        return $buffer ;
    }

    /**
     *
     */
    function	__toString() {
        return $this->__dump() ;
    }

    /**
     *
     */
    protected   function    _postInstantiate() {
        error_log( __CLASS__ . "[{$this->className}]::_postInstantiate() ... please declare in derived class") ;
    }

    /**
     *
     */
    protected   function    _postLoad() {
        error_log( __CLASS__ . "[{$this->className}]::_postLoad() ... please declare in derived class") ;
    }

    /**
     *
     */
    function _getDbAttr( $_vName) {
        if ( count( self::$nameTransTables[ $this->cacheName]) > 0) {
            if ( array_key_exists( $_vName, self::$nameTransTables[ $this->cacheName])) {
                $vName  =   self::$nameTransTables[ $this->cacheName][ $_vName] ;
            }
        }
        if ( $vName == "") {
            $vName  =   $_vName ;
        }
        return $vName ;
    }
}
?>