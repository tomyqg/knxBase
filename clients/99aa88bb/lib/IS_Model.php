<?php
/**
 * IS_Model	-	Class for Intersport (IS) DataTable "Model"
 * 
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "base/AppObject_R2.php") ;
/**
 * Model - Base Class
 *
 * @package Application
 * @subpackage Model
 */
class	IS_Model	extends	AppObject_R2	{
	/**
	 *
	 */
	function	__construct( $_myCode="") {
		FDbg::begin( 1, "Model.php", "Model", "__construct( '$_myCode')") ;
		parent::__construct( "IS_Model", "Code") ;
		if ( strlen( $_myCode) > 0) {
			try {
				$this->setCode( $_myCode) ;
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
	function	setCode( $_myCode) {
		FDbg::begin( 1, "Model.php", "Model", "setCode( '$_myCode')") ;
		$this->Code	=	$_myCode ;
		$this->reload() ;
		return $this->_valid ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setKey( $_myCode) {
		$this->Code	=	$_myCode ;
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
		FDbg::begin( 1, "Model.php", "Model", "add( '$_key', $_id, '$_val')") ;
		$myKey	=	$this->newKey( 6, "000000", "899999") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->Code	=	$myKey ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( "Model.php::Model::add(): 'Model' invalid after creation!") ;
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
		FDbg::begin( 1, "Model.php", "Model", "upd( '$_key', $_id, '$_val')") ;
		$this->_upd() ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "Model.php", "Model", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		$this->_addRem( FTr::tr( "Model updated")) ;
		FDbg::end( 1, "Model.php", "Model", "_upd()") ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Model.php::Model::del(...)") ;
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
	function	newModel( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Model.php::Model::newModel( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  Code >= '$_nsStart' AND Code <= '$_nsEnd' " .
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
		FDbg::begin( 1, "Model.php", "Model", "getAsXML( '$_key', $_id, '$_val')") ;
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
//		$filter	=	"( C.Code like '%" . $_kundeNrCrit . "%' ) " ;
// 		$filter	.=	"  AND ( C.ModelName1 like '%" . $_firmaCrit . "%' OR C.ModelName2 LIKE '%" . $_firmaCrit . "%') " ;
// 		if ( $_POST['_SName'] != "")
// 			$filter	.=	"  AND ( KK.FirstName like '%" . $_POST['_SName'] . "%' OR KK.Name like '%" . $_POST['_SName'] . "%' ) " ;
// 		if ( $_POST['_SZIP'] != "")
// 			$filter	.=	"  AND ( C.ZIP like '%" . $_POST['_SZIP'] . "%' ) " ;
// 		$filter	.=	"AND ( C.eMail LIKE '%" . $_eMail . "%') " ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "Code", "var") ;
		$myObj->addCol( "ModNo", "var") ;
		$myObj->addCol( "Denotation", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.ModNo ASC ",
								"IS_Model",
								"IS_Model",
								"C.Id, C.Code, C.ModNo, C.Denotation ") ;
//		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getListAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Model.php", "Model", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$filter	=	"" ;
		$_searchCrit	=	"" ;
		$_codeCrit	=	"" ;
		$_modNoCrit	=	"" ;
		$_denotationCrit	=	"" ;
		if ( isset( $_POST['_SSearch']))
			$_searchCrit	=	$_POST['_SSearch'] ;
		if ( isset( $_POST['_SModNo']))
			$_adrNrCrit	=	$_POST['_SModNo'] ;
		if ( isset( $_POST['_SCode']))
			$_codeCrit	=	$_POST['_SCode'] ;
		$_POST['_step']	=	$_id ;
		if ( $_searchCrit != "") {
			$filter	.=	"( C.Code like '%$_searchCrit%' OR C.ModNo like '%$_searchCrit%' OR C.Denotation like '%$_searchCrit%' ) " ;
		} else {
			$filter	.=	"(" ;
			$filter	.=	"( C.Code like '%" . $_codeCrit . "%') " ;
			$filter	.=	" AND ( C.ModNo like '%" . $_modNoCrit . "%') " ;
			$filter	.=	" AND ( C.Denotation like '%" . $_denotationCrit . "%') " ;
			$filter	.=	")" ;
		}
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "Code", "var") ;
		$myObj->addCol( "ModNo", "var") ;
		$myObj->addCol( "BrnNo", "var") ;
		$myObj->addCol( "Denotation", "var") ;
				$reply->replyData	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.ModNo ASC ",
								"IS_Model",
								"IS_Model",
								"C.Id, C.Code, C.ModNo, C.BrnNo, C.Denotation ") ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getTableDepAsXML()
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "AppObject_R2.php", "AppObject_R2", "getTableDepAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$tmpObj	=	new $objName() ;
		$_POST['_step']	=	$_id ;
		switch ( $_val) {
			case	"IS_ItemFacts"	:
				$orderBy	=	" " ;
				$reply->replyData	=	$tmpObj->tableFromDb(
											" ",
											" ",
											"C.ItemNo like '" . $myKey . "%' ",
											$orderBy) ;
				break ;
			case	"IS_ModelFacts"	:
				$orderBy	=	" " ;
				$reply->replyData	=	$tmpObj->tableFromDb(
											" ",
											" ",
											"C.Code like '" . $myKey . "%' ",
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
		FDbg::begin( 0x00000001, "IS_Model.php", "IS_Model", "uploadData( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$tmpFilename	=	$_FILES['_IFilename']['tmp_name'] ;
		$filename	=	$_FILES['_IFilename']['name'] ;
		$fullFilename	=	$this->path->xml . $filename ;
		if (move_uploaded_file($_FILES['_IFilename']['tmp_name'], $fullFilename)) {
			$this->_fromFile( $_key, $_id, $fullFilename) ;
		} else {
			if ( $_FILES['_IFilename']['error'] != UPLOAD_ERR_OK) {
				FDbg::trace( 0x00000001, FDbg::mdTrcInfo1, "IS_Model.php", "IS_Model", "bookingData( ...)", "error := '".$_FILES['_IFilename']['error']."'") ;
				FDbg::trace( 0x00000001, FDbg::mdTrcInfo1, "IS_Model.php", "IS_Model", "bookingData( ...)", "error := '".$_FILES['_IFilename']['size']."'") ;
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
						case	"Mod"	:
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
						case	"Mod"	:
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
