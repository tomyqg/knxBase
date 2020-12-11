<?php
/**
 * IS_ModSize	-	Class for Intersport (IS) DataTable "ModSize"
 * 
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "base/AppObject_R2.php") ;
/**
 * ModSize - Base Class
 *
 * @package Application
 * @subpackage ModSize
 */
class	IS_ModSize	extends	AppObject_R2	{
	/**
	 *
	 */
	function	__construct( $_myItemNo="") {
		FDbg::begin( 1, "IS_ModSize.php", "IS_ModSize", "__construct( '$_myItemNo')") ;
		parent::__construct( "IS_ModSize", "ItemNo") ;
		if ( strlen( $_myItemNo) > 0) {
			try {
				$this->setItemNo( $_myItemNo) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setItemNo( $_myItemNo) {
		FDbg::begin( 1, "IS_ModSize.php", "IS_ModSize", "setItemNo( '$_myItemNo')") ;
		$this->ItemNo	=	$_myItemNo ;
		$this->reload() ;
		return $this->_valid ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setKey( $_myItemNo) {
		$this->ItemNo	=	$_myItemNo ;
		$this->reload() ;
		$this->_valid	=	true ;
		return $this->_valid ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "IS_ModSize.php", "IS_ModSize", "add( '$_key', $_id, '$_val')") ;
		$myKey	=	$this->newKey( 6, "000000", "899999") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->ItemNo	=	$myKey ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( "ModSize.php::ModSize::add(): 'ModSize' invalid after creation!") ;
			error_log( $e) ;
			throw $e ;
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
	function	upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "IS_ModSize.php", "IS_ModSize", "upd( '$_key', $_id, '$_val')") ;
		$this->_upd() ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "IS_ModSize.php", "IS_ModSize", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		$this->_addRem( FTr::tr( "ModSize updated")) ;
		FDbg::end( 1, "ModSize.php", "ModSize", "_upd()") ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "IS_ModSize.php::IS_ModSize::del(...)") ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->className ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		$objName	=	$this->className . "Kontakt" ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		return $this->getAsXML() ;
	}
	/**
	 *
	 */
	function	newModSize( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "IS_ModSize.php::IS_ModSize::newModSize( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  ItemNo >= '$_nsStart' AND ItemNo <= '$_nsEnd' " .
						"ORDER BY $this->keyCol DESC LIMIT 1 ), $_nsStart+1)  AS newKey" ;
		$myRow	=	FDb::queryRow( $myQuery) ;
		$keyCol	=	$this->keyCol ;
		$this->$keyCol	=	sprintf( "%06s", $myRow['newKey']) ;
		$this->Tax	=	1 ;
		$this->Remark	=	"" ;
		$this->storeInDb() ;
		$this->reload() ;
		return $this->_status ;
	}
	function	getAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "IS_ModSize.php", "IS_ModSize", "getAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyData	.=	$this->getXMLF() ;
		return $reply ;
	}
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="") {
		$sCrit	=	$_POST['_ISearch'] ;
		$_kundeNrCrit	=	$sCrit ;
//		$_kundeNrCrit	=	$_POST['_SCustNo'] ;
// 		$_firmaCrit	=	$_POST['_SCompany'] ;
// 		$_nameCrit	=	$_POST['_SName'] ;
// 		$_phoneCrit	=	$_POST['_SPhone'] ;
// 		$_eMail	=	$_POST['_SeMail'] ;
 		$_POST['_step']	=	$_val ;
		$filter	=	"( C.Denotation like '%" . $_denotationCrit . "%') " ;
//		$filter	=	"( C.ItemNo like '%" . $_kundeNrCrit . "%' ) " ;
// 		$filter	.=	"  AND ( C.ModSizeName1 like '%" . $_firmaCrit . "%' OR C.ModSizeName2 LIKE '%" . $_firmaCrit . "%') " ;
// 		if ( $_POST['_SName'] != "")
// 			$filter	.=	"  AND ( KK.FirstName like '%" . $_POST['_SName'] . "%' OR KK.Name like '%" . $_POST['_SName'] . "%' ) " ;
// 		if ( $_POST['_SZIP'] != "")
// 			$filter	.=	"  AND ( C.ZIP like '%" . $_POST['_SZIP'] . "%' ) " ;
// 		$filter	.=	"AND ( C.eMail LIKE '%" . $_eMail . "%') " ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "ItemNo", "var") ;
		$myObj->addCol( "Code", "var") ;
		$myObj->addCol( "Denotation", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.ItemNo ASC ",
								"IS_ModSize",
								"IS_ModSize",
								"C.Id, C.ItemNo, C.Code, C.Denotation ") ;
//		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getListAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "IS_ModSize.php", "IS_ModSize", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$filter	=	"" ;
		$_searchCrit	=	"" ;
		$_modNoCrit	=	"" ;
		$_denotationCrit	=	"" ;
		if ( isset( $_POST['_SSearch']))
			$_searchCrit	=	$_POST['_SSearch'] ;
		if ( isset( $_POST['_SItemNo']))
			$_adrNrCrit	=	$_POST['_SItemNo'] ;
		$_POST['_step']	=	$_id ;
		if ( $_searchCrit != "") {
			$filter	.=	"( C.ItemNo like '%$_searchCrit%' OR C.Denotation like '%$_searchCrit%' ) " ;
		} else {
			$filter	.=	"(" ;
			$filter	.=	"( C.ItemNo like '%" . $_modNoCrit . "%' OR C.ItemNo) " ;
			$filter	.=	"  AND ( C.Denotation like '%" . $_denotationCrit . "%') " ;
			$filter	.=	")" ;
		}
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "ItemNo", "var") ;
		$myObj->addCol( "Code", "var") ;
		$myObj->addCol( "Denotation", "var") ;
		$myObj->addCol( "ColorDenotation", "var") ;
		$reply->replyData	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.ItemNo ASC ",
								"IS_ModSize",
								"IS_ModSize",
								"C.Id, C.ItemNo, C.Code, C.Denotation, C.ColorDenotation ") ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getTableDepAsXML()
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "IS_ModSize.php", "IS_ModSize", "getTableDepAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$tmpObj	=	new $objName() ;
		$_POST['_step']	=	$_id ;
		switch ( $_val) {
			case	"IS_ModelFacts"	:
				$orderBy	=	" " ;
				$reply->replyData	=	$tmpObj->tableFromDb(
											" ",
											" ",
											"C.Code = '" . $this->Code . "' ",
											$orderBy) ;
				break ;
		}
		return $reply ;
	}
	/**
	 * 
	 * @param unknown $_key
	 * @param unknown $_id
	 * @param unknown $_val
	 * @return string
	 */
	function	uploadData( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 0x00000001, "IS_ModSize.php", "IS_ModSize", "uploadData( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$tmpFilename	=	$_FILES['_IFilename']['tmp_name'] ;
		$filename	=	$_FILES['_IFilename']['name'] ;
		$fullFilename	=	$this->path->xml . $filename ;
		if (move_uploaded_file($_FILES['_IFilename']['tmp_name'], $fullFilename)) {
			$this->_fromFile( $_key, $_id, $fullFilename) ;
		} else {
			if ( $_FILES['_IFilename']['error'] != UPLOAD_ERR_OK) {
				FDbg::trace( 0x00000001, FDbg::mdTrcInfo1, "IS_ModSize.php", "IS_ModSize", "bookingData( ...)", "error := '".$_FILES['_IFilename']['error']."'") ;
				FDbg::trace( 0x00000001, FDbg::mdTrcInfo1, "IS_ModSize.php", "IS_ModSize", "bookingData( ...)", "error := '".$_FILES['_IFilename']['size']."'") ;
			}
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * 
	 */
	public	function	_fromFile( $_key="", $_id=-1, $_file="", $reply=null) {
		/**
		 *	IF moving uploaded file ok, proceed with reading the XML content
		 *	ELSE
		 */
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$buffer	=	"" ;
		$text	=	"" ;
		$myFile	=	fopen( $_file, "r") ;
		$myXMLText	=	fread( $myFile, 250000000) ;
		fclose( $myFile) ;
		if ( strlen( $myXMLText) == 0) {
			$buffer	=	"XML File is empty" ;
			return $buffer ;
		}
		$xml	=	new XMLReader() ;
		$xml->XML( $myXMLText) ;
		$lineNo	=	0 ;
		while ( $xml->read()) {
			error_log( $lineNo++ + "------->" . $xml->name) ;
			switch ( $xml->nodeType) {
				case	XMLReader::ELEMENT	:			// start element
					switch ( $xml->name) {
						case	"ModSize"	:
							break ;
					}
				case	XMLReader::TEXT	:			// text node
					$text	.=	trim( $xml->value, "\n\t") ;
					break ;
				case	XMLReader::CDATA	:
					$myCData	=	$xml->value ;
					break ;
				case	XMLReader::WHITESPACE	:			// whitespace node
					break ;
				case	XMLReader::END_ELEMENT	:			// end element
					error_log( "end ...") ;
					$name	=	$xml->name ;
					switch ( $xml->name) {
						case	"ModSize"	:
							error_log( "writing object " . $name) ;
							$this->storeInDb() ;
							break ;
						default	:
							if ( isset( $this->$name)) {
								error_log( "assigning attribute " . $name) ;
								$this->$name	=	$text ;
							}
							$text	=	"" ;
							break ;
					}
					break ;
			}
		}
	}
}
?>
