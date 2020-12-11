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
 * option.php Definition of general options on application level
 *
 * @author Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package wwsbe-app
 */
/**
 * Opt - Base class to deal with general options
 *
 * not be modified.
 *
 * @package wwsbe-app
 * @subpackage options
 */
class	Option extends FDbObject	{
	public	static	$myOption ;
	/**
	 *
	 */
	function	__construct( $_db="appSys", $_class="AppOption") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_db', '$_class')") ;
		parent::__construct( $_class, "Id", $_db) ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->storeInDb() ;
		} else {
			throw new FException( __FILE__, __CLASS__, __METHOD__."( '<_query>', '$_db')", "object invalid after creation") ;
		}
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->updateInDb() ;
		} else {
			throw new FException( __FILE__, __CLASS__, __METHOD__."( '<_query>', '$_db')", "object invalid after creation") ;
		}
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $this->_valid) {
			$this->removeFromDb() ;
			$reply	=	$this->getNextAsXML( $_key, $_id, $_val) ;
		} else {
			throw new FException( __FILE__, __CLASS__, __METHOD__."( '<_query>', '$_db')", "object invalid after creation") ;
		}
		FDbg::end() ;
		return $reply() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::getXMLString()
	 */
	function	getAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Trans.php", "Trans", "getAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->className ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
		FDbg::end() ;
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
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$cond	=	"" ;
		if ( isset( $_POST['cond']))
			$cond	=	$_POST['cond'] ;
		$order	=	"Id" ;
		if ( isset( $_POST['order']))
			$order	=	$_POST['order'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			if ( isset( $_POST['StartRow'])) {
				$this->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( Class LIKE '%" . $sCrit . "%' OR OptionName like '%" . $sCrit . "%')" ;
			$filter2	=	$cond ;
			$myQuery	=	$this->getQueryObj( "Select") ;
			$myQuery->addOrder( [ $order]) ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$this->tableFromQuery( $myQuery) ;
			break ;
		}
		return $reply ;
	}
	static	function	getArray( $_opt, $_indexCol, $_optCol, $_crit="1 = 1 ", $_order="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_opt', '$_indexCol', '$_optCol', '$_crit', '$_order')") ;
		self::$myOption	=	array() ;
		$myObj	=	new FDbObject( $_opt)  ;
		$myObj->setIterCond( $_crit) ;
		$myObj->setIterOrder( $_order) ;
		foreach ( $myObj as $key => $obj) {
			self::$myOption[$obj->$_indexCol]	=	$obj->$_optCol ;
		}
		FDbg::end() ;
		return self::$myOption ;
	}
	static	function	getConst( $_opt, $_indexCol, $_optCol, $_crit="1 = 1 ", $_order="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_opt', '$_indexCol', '$_optCol', '$_crit', '$_order')") ;
		self::$myOption	=	array() ;
		$myObj	=	new FDbObject( $_opt)  ;
		$myObj->setIterCond( $_crit) ;
		$myObj->setIterOrder( $_order) ;
		foreach ( $myObj as $key => $obj) {
			self::$myOption[$obj->$_indexCol]	=	$obj->$_optCol ;
		}
		FDbg::end() ;
		return self::$myOption ;
	}
	/**
	 *
	 * @param unknown $_opt
	 * @param unknown $_indexCol
	 * @param unknown $_optCol
	 * @param string $_crit
	 * @param string $_order
	 * @param string $_def
	 * @return string
	 */
	static	function	getOptions( $_opt, $_indexCol, $_optCol, $_crit="1 = 1 ", $_order="", $_def="") {
		FDbg::begin( 1, "Option.php", "Option", "getOption( '$_opt', '$_indexCol', '$_optCol', '$_crit', '$_order', '$_def')") ;
		$myObj	=	new FDbObject( $_opt)  ;
		$myObj->setIterCond( $_crit) ;
		$myObj->setIterOrder( $_order) ;
		$buffer	=	"" ;
		foreach ( $myObj as $key => $obj) {
			if ( $obj->$_indexCol == $_def) {
				$buffer	.=	"<option selected=\"1\" value=\"".$obj->$_indexCol."\">".$obj->$_optCol."</option>\n" ;
			} else {
				$buffer	.=	"<option value=\"".$obj->$_indexCol."\">".$obj->$_optCol."</option>\n" ;
			}
		}
		FDbg::trace( 2, FDbg::mdTrcInfo1, "Option.php", "Option", "getOption( <...>)", "$buffer") ;
		FDbg::end( 1, "Option.php", "Option", "getOption( '$_opt', '$_indexCol', '$_optCol', '$_crit', '$_order', '$_def')") ;
		return $buffer ;
	}
	static	function	getSelect( $_opt, $_indexCol, $_optCol, $_crit="1 = 1 ", $_order="", $_def="") {
		$buffer	=	"<select name=\"".$_opt."\">\n" ;
		$buffer	.=	self::getOption( $_opt, $_indexCol, $_optCol, $_crit, $_order, $_def) ;
		$buffer	.=	"</select>\n" ;
		return $buffer ;
	}
	/**
	 * display an SELECT-node in the form key=>value
	 *
	 * @param	array	$values	Array with the key-value pairs.
	 * @param	string	$val	Current value which shall be set as selected.
	 * @param	string	$name 	Name of the input element.
	 * @return void
	 */
	static	function	option( $values, $val, $name) {
		printf( "<select name=\"%s\" size=\"1\" onchange=\"mark( %s) ; \" >\n", $name, $name) ;
		foreach ($values as $key => $value) {
			if ( $key == $val) {
				printf( "<option value=\"%s\" selected>%s</option>\n", $key, $value) ;
			} else {
				printf( "<option value=\"%s\">%s</option>\n", $key, $value) ;
			}
	        }
		printf( "</select>\n") ;
	}

	/**
	 * display an SELECT-node only for the selected value in the form key=>value
	 *
	 * @param	array	$values	Array with the key-value pairs.
	 * @param	string	$val	Current value which shall be set as selected.
	 * @param	string	$name 	Name of the input element.
	 * @return void
	 */
	static	function	optionDisp2( $values, $val, $name) {
		printf( "<select name=\"%s\" size=\"1\" onchange=\"mark( %s) ; \" >\n", $name, $name) ;
		foreach ($values as $key => $value) {
			if ( $key == $val) {
				printf( "<option value=\"%s\" selected>%s</option>\n", $key, $value) ;
			} else {
			}
		}
		printf( "</select>\n") ;
	}

	/**
	 * display an SELECT-node in the form value=>value
	 *
	 * @param	array	$values	Array with the key-value pairs.
	 * @param	string	$val	Current value which shall be set as selected.
	 * @param	string	$name 	Name of the input element.
	 * @return	void
	 */
	static	function	optionListe( $values, $val, $name) {
		printf( "<select name=\"%s\" id=\"%s\" size=\"1\" onchange=\"mark( %s) ; \" >\n", $name, $name, $name) ;
		foreach ($values as $key => $value) {
			if ( $key == $val) {
				printf( "<option value=\"%s\" selected>%s</option>\n", $value, $value) ;
			} else {
				printf( "<option value=\"%s\">%s</option>\n", $value, $value) ;
			}
		}
		printf( "</select>\n") ;
	}

	/**
	 * assemble and return a SELECT-node as string in the form key=>value
	 *
	 * @param	array	$values	Array with the key-value pairs.
	 * @param	string	$val	Current value which shall be set as selected.
	 * @param	string	$name 	Name of the input element.
	 * @return	string			Assembled SELECT-node.
	 */
	static	function	optionRet( $values, $val, $name, $_js="") {
		$buf	=	"" ;
		$buf	.=	sprintf( "<select id=\"%s\" name=\"%s\" size=\"1\" onchange=\"mark( %s) ; " . $_js . "\" >\n", $name, $name, $name) ;
		foreach ($values as $key => $value) {
			if ( $key == $val) {
				$buf	.=	sprintf( "<option value=\"%s\" selected>%s</option>\n", $key, FTr::tr( $value)) ;
			} else {
				$buf	.=	sprintf( "<option value=\"%s\">%s</option>\n", $key, FTr::tr( $value)) ;
			}
		}
		$buf	.=	sprintf( "</select>\n") ;
		return $buf ;
	}
	static	function	flagRet( $values, $val, $name, $_js="") {
		$buf	=	"" ;
		foreach ($values as $key => $value) {
			if ( $key == $val) {
				$buf	.=	"<input type='radio' id='$name' name='$name' onchange='$_js' value='$key' checked>".FTr::tr( $value)."\n" ;
			} else {
				$buf	.=	"<input type='radio' id='$name' name='$name' onchange='$_js' value='$key'>".FTr::tr( $value)."\n" ;
			}
		}
		return $buf ;
	}
	static	function	cbRet( $values, $val, $name, $_js="") {
		$buf	=	"" ;
		foreach ($values as $key => $value) {
			if ( $key & $val) {
				$buf	.=	"<input type='checkbox' id='$name' name='$name' onchange='$_js' value='$key' checked>".FTr::tr( $value)."<br/>\n" ;
			} else {
				$buf	.=	"<input type='checkbox' id='$name' name='$name' onchange='$_js' value='$key'>".FTr::tr( $value)."<br/>\n" ;
			}
		}
		return $buf ;
	}

	/**
	 * assemble and return a SELECT-node in the form value=>value
	 *
	 * @param	array	$values	Array with the key-value pairs.
	 * @param	string	$val	Current value which shall be set as selected.
	 * @param	string	$name 	Name of the input element.
	 * @return	string			Assembled SELECT-node.
	 */
	static	function	optionRetS( $values, $val, $name) {
		$buf	=	"" ;
		$buf	.=	sprintf( "<select id=\"%s\" name=\"%s\" size=\"1\" onchange=\"mark( %s) ; \" >\n", $name, $name, $name) ;
		foreach ($values as $value) {
			if ( $key == $val) {
				$buf	.=	sprintf( "<option value=\"%s\" selected>%s</option>\n", $value, $value) ;
			} else {
				$buf	.=	sprintf( "<option value=\"%s\">%s</option>\n", $value, $value) ;
			}
		}
		$buf	.=	sprintf( "</select>\n") ;
		return $buf ;
	}

	/**
	 * asseemble and return SELECT-node only for the selected value in the form key=>value and
	 * marked as READONLY
	 *
	 * @param	array	$values	Array with the key-value pairs.
	 * @param	string	$val	Current value which shall be set as selected.
	 * @param	string	$name 	Name of the input element.
	 * @return	string			Assembled SELECT-node.
	 */
	static	function	optionRetRO( $values, $val, $name) {
		$buf	=	"" ;
		$buf	.=	sprintf( "<select readonly id=\"%s\" name=\"%s\" size=\"1\" onchange=\"mark( %s) ; \" >\n", $name, $name, $name) ;
		foreach ($values as $key => $value) {
			if ( $key == $val) {
				$buf	.=	sprintf( "<option value=\"%s\" selected>%s</option>\n", $key, $value) ;
			} else {
				$buf	.=	sprintf( "<option value=\"%s\">%s</option>\n", $key, $value) ;
			}
		}
		$buf	.=	sprintf( "</select>\n") ;
		return $buf ;
	}

	/**
	 * display an INPUT-node for the selected value in the form key=>value
	 *
	 * @param	array	$values	Array with the key-value pairs.
	 * @param	string	$val	Current value which shall be set as selected.
	 * @param	string	$name 	Name of the input element.
	 * @return void
	 */
	static	function	optionDisp( $values, $val, $name, $len=10) {
		foreach ($values as $key => $value) {
			if ( $key == $val) {
				printf( "<input type=\"text\" size=\"%d\" readonly value=\"%s\" />\n", $len, $value) ;
			}
		}
	}

	/**
	 * return the text of the selected item
	 *
	 * @param	array	$values	Array with the key-value pairs.
	 * @param	string	$val	Current value which shall be set as selected.
	 * @param	string	$name 	Name of the input element.
	 * @return void
	 */
	static	function	optionReturn( $values, $val) {
		foreach ($values as $key => $value) {
			if ( $key == $val) {
				return( $value) ;
			}
		}
	}
}

?>
