<?php
/**
 * IS_Branch	-	Class for Intersport (IS) DataTable "Branch"
 * 
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "base/AppObject_R2.php") ;
/**
 * Branch - Base Class
 *
 * @package Application
 * @subpackage Branch
 */
class	IS_Branch	extends	AppObject_R2	{
	/**
	 *
	 */
	function	__construct( $_myBrcCount="") {
		FDbg::begin( 1, "Branch.php", "Branch", "__construct( '$_myBrcCount')") ;
		parent::__construct( "IS_Branch", "BrcCount") ;
		if ( strlen( $_myBrcCount) > 0) {
			try {
				$this->setBrcCount( $_myBrcCount) ;
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
	function	setBrcCount( $_myBrcCount) {
		FDbg::begin( 1, "Branch.php", "Branch", "setBrcCount( '$_myBrcCount')") ;
		$this->BrcCount	=	$_myBrcCount ;
		$this->reload() ;
		return $this->_valid ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setKey( $_myBrcCount) {
		$this->BrcCount	=	$_myBrcCount ;
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
		FDbg::begin( 1, "Branch.php", "Branch", "add( '$_key', $_id, '$_val')") ;
		$myKey	=	$this->newKey( 6, "000000", "899999") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->BrcCount	=	$myKey ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( "Branch.php::Branch::add(): 'Branch' invalid after creation!") ;
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
		FDbg::begin( 1, "Branch.php", "Branch", "upd( '$_key', $_id, '$_val')") ;
		$this->_upd() ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "Branch.php", "Branch", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		$this->_addRem( FTr::tr( "Branch updated")) ;
		FDbg::end( 1, "Branch.php", "Branch", "_upd()") ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Branch.php::Branch::del(...)") ;
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
	function	newBranch( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Branch.php::Branch::newBranch( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  BrcCount >= '$_nsStart' AND BrcCount <= '$_nsEnd' " .
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
	/**
	 * create a new customer from a form sending POST data
	 */
	function	newBranchFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new BranchContact() ;
		$myCuContact->getFromPost() ;
		if ( $this->BranchName1 == "") {
			$this->BranchName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 * 
		 */
		if ( strlen( $this->BranchName1) < 8) {
			self::$err['_IBranchName1']	=	true ;
		}
		if ( strlen( $myCuContact->Name) < 3) {
			self::$err['_IName']	=	true ;
		}
		if ( strlen( $myCuContact->FirstName) < 3) {
			self::$err['_IFirstName']	=	true ;
		}
		if ( strlen( $this->Street) < 3) {
			self::$err['_IStreet']	=	true ;
		}
		if ( strlen( $this->Number) < 3) {
			self::$err['_INumber']	=	true ;
		}
		if ( strlen( $this->ZIP) < 3) {
			self::$err['_IZIP']	=	true ;
		}
		if ( strlen( $this->City) < 3) {
			self::$err['_ICity']	=	true ;
		}
		/**
		 * check eMail Address for length
		 * for match
		 * all ok
		 */
		if ( strlen( $this->eMail) < 10) {
			self::$err['_IeMail']	=	true ;
		} else if ( $this->eMail != $_POST['_IeMailVerify']) {
			self::$err['_IeMail']	=	true ;
		} else {
			$this->newBranch( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->BrcCount	=	$this->BrcCount ;
			$myCuContact->newBranchContact() ;
		}
	}
	function	getAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Branch.php", "Branch", "getAsXML( '$_key', $_id, '$_val')") ;
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
	function	getListAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Branch.php", "Branch", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$filter	=	"" ;
		$_searchCrit	=	"" ;
		$_adrNrCrit	=	"" ;
		$_firmaCrit	=	"" ;
		$_nameCrit	=	"" ;
		$_zipCrit	=	"" ;
		if ( isset( $_POST['_SSearch']))
			$_searchCrit	=	$_POST['_SSearch'] ;
		if ( isset( $_POST['_SBrcCount']))
			$_adrNrCrit	=	$_POST['_SBrcCount'] ;
		$_POST['_step']	=	$_id ;
		$filter	.=	"(" ;
		$filter	.=	"( C.BrcCount like '%" . $_adrNrCrit . "%' OR C.BrcCount like '%" . $_searchCrit . "%' ) " ;
		$filter	.=	"  AND ( C.Denotation like '%" . $_firmaCrit . "%') " ;
		if ( $_searchCrit != "")
			$filter	.=	"  AND ( C.Denotation like '%$_searchCrit%') " ;
		$filter	.=	")" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "BrcCount", "var") ;
		$myObj->addCol( "Denotation", "var") ;
		$reply->replyData	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.BrcCount ASC ",
								"IS_Branch",
								"IS_Branch",
								"C.Id, C.BrcCount, C.Denotation ") ;
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
		FDbg::begin( 0x00000001, "BankAccount.php", "BankAccount", "bookingData( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$tmpFilename	=	$_FILES['_IFilename']['tmp_name'] ;
		$filename	=	$_FILES['_IFilename']['name'] ;
		$fullFilename	=	$this->path->xml . $filename ;
		if (move_uploaded_file($_FILES['_IFilename']['tmp_name'], $fullFilename)) {
			$this->_fromFile( $_key, $_id, $fullFilename) ;
		} else {
			if ( $_FILES['_IFilename']['error'] != UPLOAD_ERR_OK) {
				FDbg::trace( 0x00000001, FDbg::mdTrcInfo1, "IS_Branch.php", "IS_Branch", "bookingData( ...)", "error := '".$_FILES['_IFilename']['error']."'") ;
				FDbg::trace( 0x00000001, FDbg::mdTrcInfo1, "IS_Branch.php", "IS_Branch", "bookingData( ...)", "error := '".$_FILES['_IFilename']['size']."'") ;
			}
		}
		FDbg::end( 0x00000001, "IS_Branch.php", "IS_Branch", "bookingData( '$_key', $_id, '$_val')") ;
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
						case	"Branch"	:
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
						case	"Branch"	:
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
