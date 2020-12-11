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
 * Trans.php - Basic class for dealing with translations of arbitrary text
 *
 * Primary key for this object is the MD5Checksum of the text to be translated [RefNo] together with
 * the target language [Language].
 * 'Trans' supports country specific version of a translation, e.g. british english (en_UK), US english (en-US),
 * as well as generic version, e.g. englisch (en), french (fr).
 * From a user point-of-view 'Trans' should not be used directly but through the derived classes
 * 'AppTrans' for application level translations or 'SysTrans' for system level translations.
 *
 *
 * @author Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package Application
 * @subpackage Trans
 */
/**
 * Trans - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BTrans which should
 * not be modified.
 *
 * @package Application
 */
class	Trans	extends	FDbObject	{
	/*
	 * The constructor can be passed an ArticleNr (TransNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_db="appSys", $_class="AppTrans", $_name="", $_refNo="", $_sprache="en_US") {
		parent::__construct( $_class, "Id", $_db) ;
		if ( strlen( $_name) > 0) {
			try {
				$this->setKeys( $_name, $_refNo, $_sprache) ;
			} catch ( FException $e) {
			}
		}
	}
	/**
	 *
	 */
	function	setKeys( $_name, $_refNo="", $_sprache="en_US") {
		if ( strlen( $_name) > 0) {
			$this->Name	=	$_name ;
			$this->RefNo	=	$_refNo ;
			$this->Language	=	$_sprache ;
			try {
				$this->reloadByKeys() ;
			} catch ( FException $e) {
				FDbg::abort() ;
				throw $e ;
			} catch ( Exception $e) {
				FDbg::abort() ;
				throw $e ;
			}
		}
		return $this->isValid() ;
	}
	/**
	 * reloadByKeys
	 */
	function	reloadByKeys() {
		$basicLang	=	explode( "_", str_replace( "/", "_", $this->Language)) ;		// split language at '_'
		$cond	=	array(	"Name = '".$this->Name."'"
						,	"RefNo = '".$this->RefNo."' "
						,	"( Language='".$this->Language."' OR Language LIKE '".$basicLang[0]."%%' )") ;
		$order	=	array( "LENGTH( Language) DESC") ;
		$this->fetchFromDbWhere( $cond, $order) ;
		if ( $this->isValid() != true) {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."()",
						"Name:='$this->Name, RefNo:='$this->RefNo', Language:='$this->Language' could not be loaded.") ;
		}
//		$this->incUseCount() ;
		return $this->isValid() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::add()
	 */
	function	add( $_key="", $_id=-1, $_val="", $_reply=null) {
	}
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::upd()
	 */
	function	upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		$this->getFromPostL() ;
		$this->updateInDb() ;
		return $this->getXMLString() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::del()
	 */
	function	del( $_key="", $_id=-1, $_val="", $_reply=null) {
	}
	/**
	 *
	 * @param unknown $_key
	 * @param unknown $_id
	 * @param unknown $_val
	 * @return Ambigous <boolean, string>
	 */
	function	setText( $_key="", $_id=-1, $_val="", $_reply=null) {
		$this->Fulltext2	=	$_val ;
		$this->updateInDb() ;
		return $this->getTableAsXML() ;
	}
	/**
	 *
	 * @param unknown $_key
	 * @param unknown $_id
	 * @param unknown $_val
	 * @return Ambigous <boolean, string>
	 */
	function	updTrans( $_key="", $_id=-1, $_val="", $_reply=null) {
		$this->getFromPostL() ;
		$this->updateInDb() ;
		return $this->getTableAsXML() ;
	}
	function	incUseCount() {
		try {
			$this->UseCount++ ;
			$this->updateColInDb( "UseCount") ;
		} catch ( Exception $e) {
			throw $e ;
		} catch( FException $e) {
		}
	}
	/**
	 *	RETRIEVAL METHODS
	 */
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::getXMLString()
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->className ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
		return $reply ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	$_POST['Search'] ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			if ( isset( $_POST['StartRow'])) {
				$this->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"Fulltext2 LIKE '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery	=	$this->getQueryObj( "Select") ;
			$myQuery->addOrder( [ "Id"]) ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$this->tableFromQuery( $myQuery) ;
			break ;
		}
		return $reply ;
	}
	/**
	 *
	 */
	static	function	__setDbAlias( $_dbAlias) {
		self::$dbAlias	=	$_dbAlias ;
	}
}

?>
