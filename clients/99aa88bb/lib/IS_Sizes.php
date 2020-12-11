<?php
/**
 * IS_Size	-	Class for Intersport (IS) DataTable "Size"
 * 
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "base/AppObject_R2.php") ;
/**
 * Size - Base Class
 *
 * @package Application
 * @subpackage Size
 */
class	IS_Sizes	extends	AppObject_R2	{
	/**
	 *
	 */
	function	__construct( $_myModNo="") {
		FDbg::begin( 1, "Size.php", "Size", "__construct( '$_myModNo')") ;
		parent::__construct( "IS_Sizes", "SizeIdx") ;
		if ( strlen( $_myModNo) > 0) {
			try {
				$this->setModNo( $_myModNo) ;
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
	function	setSizeIdx( $_mySizeIdx) {
		FDbg::begin( 1, "Size.php", "Size", "setModNo( '$_mySizeIdx')") ;
		$this->SizeIdx	=	$_mySizeIdx ;
		$this->reload() ;
		return $this->_valid ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setKey( $_mySizeIdx) {
		$this->SizeIdx	=	$_mySizeIdx ;
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
		FDbg::begin( 1, "Size.php", "Size", "add( '$_key', $_id, '$_val')") ;
		$myKey	=	$this->newKey( 6, "000000", "899999") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->ModNo	=	$myKey ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( "Size.php::Size::add(): 'Size' invalid after creation!") ;
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
		FDbg::begin( 1, "Size.php", "Size", "upd( '$_key', $_id, '$_val')") ;
		$this->_upd() ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "Size.php", "Size", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		$this->_addRem( FTr::tr( "Size updated")) ;
		FDbg::end( 1, "Size.php", "Size", "_upd()") ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Size.php::Size::del(...)") ;
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
	function	newSize( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Size.php::Size::newSize( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  ModNo >= '$_nsStart' AND ModNo <= '$_nsEnd' " .
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
		FDbg::begin( 1, "Size.php", "Size", "getAsXML( '$_key', $_id, '$_val')") ;
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
//		$filter	=	"( C.ModNo like '%" . $_kundeNrCrit . "%' ) " ;
// 		$filter	.=	"  AND ( C.SizeName1 like '%" . $_firmaCrit . "%' OR C.SizeName2 LIKE '%" . $_firmaCrit . "%') " ;
// 		if ( $_POST['_SName'] != "")
// 			$filter	.=	"  AND ( KK.FirstName like '%" . $_POST['_SName'] . "%' OR KK.Name like '%" . $_POST['_SName'] . "%' ) " ;
// 		if ( $_POST['_SZIP'] != "")
// 			$filter	.=	"  AND ( C.ZIP like '%" . $_POST['_SZIP'] . "%' ) " ;
// 		$filter	.=	"AND ( C.eMail LIKE '%" . $_eMail . "%') " ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "ModNo", "var") ;
		$myObj->addCol( "ModNo", "var") ;
		$myObj->addCol( "Code", "var") ;
		$myObj->addCol( "Denotation", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.ModNo ASC ",
								"IS_Size",
								"IS_Size",
								"C.Id, C.ModNo, C.ModNo, C.Code, C.Denotation ") ;
//		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getListAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Size.php", "Size", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$filter	=	"" ;
		$_searchCrit	=	"" ;
		$_sizeRangeCrit	=	"" ;
		if ( isset( $_POST['_SSearch']))
			$_searchCrit	=	$_POST['_SSearch'] ;
		if ( isset( $_POST['_SSizeIdx']))
			$_sizeIdxCrit	=	$_POST['_SSizeIdx'] ;
		$_POST['_step']	=	$_id ;
		if ( $_searchCrit != "") {
			$filter	.=	"( C.SizeRange like '%$_searchCrit%' OR C.Denotation like '%$_searchCrit%' ) " ;
		} else {
			$filter	.=	"(" ;
			$filter	.=	"( C.SizeRange like '%" . $_sizeRangeCrit . "%') " ;
			$filter	.=	")" ;
		}
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "SizeIdx", "var") ;
		$myObj->addCol( "SizeRange", "var") ;
		$myObj->addCol( "MainSizeRange", "var") ;
		$myObj->addCol( "Size", "var") ;
		$reply->replyData	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.SizeIdx ASC ",
								"IS_Sizes",
								"IS_Sizes",
								"C.Id, C.SizeIdx, C.SizeRange, C.MainSizeRange, C.Size ") ;
		FDbg::end() ;
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
		FDbg::begin( 0x00000001, "IS_Size.php", "IS_Size", "uploadData( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$tmpFilename	=	$_FILES['_IFilename']['tmp_name'] ;
		$filename	=	$_FILES['_IFilename']['name'] ;
		$fullFilename	=	$this->path->xml . $filename ;
		if (move_uploaded_file($_FILES['_IFilename']['tmp_name'], $fullFilename)) {
			$this->_fromFile( $_key, $_id, $fullFilename) ;
		} else {
			if ( $_FILES['_IFilename']['error'] != UPLOAD_ERR_OK) {
				FDbg::trace( 0x00000001, FDbg::mdTrcInfo1, "IS_Size.php", "IS_Size", "bookingData( ...)", "error := '".$_FILES['_IFilename']['error']."'") ;
				FDbg::trace( 0x00000001, FDbg::mdTrcInfo1, "IS_Size.php", "IS_Size", "bookingData( ...)", "error := '".$_FILES['_IFilename']['size']."'") ;
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
						case	"Sizes"	:
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
						case	"Sizes"	:
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
