<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * Document - Base Class
 *
 * @package Application
 * @subpackage Document
 */
class	Document	extends	AppObjectCore	{
	/**
	 *
	 */
	function	__construct( $_myDocumentNo="") {
		parent::__construct( "Document", "DocumentNo") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myDocumentNo) > 0) {
			try {
				$this->setDocumentNo( $_myDocumentNo) ;
				$this->actDocumentContact	=	new DocumentContact() ;
				$this->Opening	=	"Hallo" ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
	}
	/**
	 *
	 */
	function	setDocumentNo( $_myDocumentNo) {
		$this->DocumentNo	=	$_myDocumentNo ;
		$this->reload() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$myKey	=	$this->newKey( 10, "0000000000", "9999999999", false) ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->DocumentNo	=	$myKey ;
			$this->storeInDb() ;
		} else {
			$e	=	new Exception( "Document.php::Document::add(): 'Document' invalid after creation!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		$this->_addRem( FTr::tr( "Document updated")) ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Document.php::Document::del(...)") ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->className ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		$objName	=	$this->className . "Kontakt" ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "DocumentContact") {
			$myDocumentContact	=	new DocumentContact() ;
			$myDocumentContact->DocumentNo	=	$this->DocumentNo ;
			$myDocumentContact->newDocumentContact() ;
			$myDocumentContact->getFromPostL() ;
			$myDocumentContact->updateInDb() ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, $_val, $reply) ;
		} else if ( $objName == "LiefDocument") {
			$this->_addDepDocument( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefDocument", $reply) ;
		} else if ( $objName == "RechDocument") {
			$this->_addDepDocument( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechDocument", $reply) ;
		} else if ( $objName == "AddDocument") {
			$this->_addDepDocument( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddDocument", $reply) ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$objName	=	$_val ;
		if ( $objName == "DocumentContact") {
			$myDocumentContact	=	new DocumentContact() ;
			$myDocumentContact->DocumentNo	=	$this->DocumentNo ;
			$myDocumentContact->newDocumentContact() ;
			$myDocumentContact->getFromPostL() ;
			$myDocumentContact->updateInDb() ;
			return $myDocumentContact->DocumentContactNo ;
		} else if ( $objName == "LiefDocument") {
			return $this->_addDepDocument( "L") ;
		} else if ( $objName == "RechDocument") {
			return $this->_addDepDocument( "R") ;
		} else if ( $objName == "AddDocument") {
			return $this->_addDepDocument( "A") ;
		}
		FDbg::end() ;
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Document.php", "Document", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			return parent::updDep( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $this->getTableDepAsXML( $_key, $_id, $objName) ;
	}
	function	delDep( $_key, $_id, $_val, $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>))") ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Document.php", "Document", "delDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			parent::delDep( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	newDocument( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Document.php::Document::newDocument( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  DocumentNo >= '$_nsStart' AND DocumentNo <= '$_nsEnd' " .
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
	 *
	 */
	function	createRevision( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 * get the data of the latest revision in the database
		 */
		$myDocumentRevision	=	new DocumentRevision() ;
		$myQuery	=	$myDocumentRevision->getQueryObj( "Select") ;
		$myQuery->addWhere( "DocumentNo = '".$this->DocumentNo."' ") ;
		$myQuery->addOrder( "Id DESC") ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		error_log( $myQuery) ;
		$myRow	=	FDb::queryRow( $myQuery, self::$db[$this->className]) ;
		if ( $myRow !== null) {
			$myDocumentRevision->_assignFromRow( $myRow) ;
			$myRev	=	new RevNo( $myDocumentRevision->Revision) ;
			$myDocumentRevision->Revision	=	$myRev->step() ;
			$myDocumentRevision->Status	=	$_POST["Status"] ;
			$myDocumentRevision->storeInDb() ;
		} else {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "Document.php", "Document", "createRevision( '$_key', $_id, '$_val')",
							"no record exists yet") ;
			$myRev	=	new RevNo() ;
			FDbg::trace( 2, FDbg::mdTrcInfo1, "Document.php", "Document", "createRevision( '$_key', $_id, '$_val')",
							"new Revision No := '".$myRev->getRevNo()."'") ;
							$myDocumentRevision->DocumentNo	=	$this->DocumentNo ;
			$myDocumentRevision->Revision	=	$myRev->getRevNo() ;
			$myDocumentRevision->Status	=	$_POST["Status"] ;
			$myDocumentRevision->storeInDb() ;
		}
		$myDocumentRevision->Filename	=	$this->upload( $myDocumentRevision->Revision) ;
		$myDocumentRevision->updateInDb() ;
		$this->getXMLString( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	createRelease( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 * get the data of the latest revision in the database
		 */
		$myDocumentRevision	=	new DocumentRevision() ;
		$myQuery	=	$myDocumentRevision->getQueryObj( "Select") ;
		$myQuery->addWhere( "DocumentNo = '".$this->DocumentNo."' ") ;
		$myQuery->addOrder( "Id DESC") ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		error_log( $myQuery) ;
		$myRow	=	FDb::queryRow( $myQuery, self::$db[$this->className]) ;
		if ( $myRow !== null) {
			$myDocumentRevision->_assignFromRow( $myRow) ;
			$myRev	=	new RevNo( $myDocumentRevision->Revision) ;
			$myDocumentRevision->DocumentNo	=	$this->DocumentNo ;
			$myDocumentRevision->Revision	=	$myRev->release() ;
			$myDocumentRevision->Status	=	"APPR" ;
			$myDocumentRevision->storeInDb() ;
		}
		$this->upload( $_key, $_id, $_val, $reply, $myDocumentRevision->Revision) ;
		$this->getXMLString( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	upload( $_revision="PA1") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		/**
		 *
		 */
		$path1	=	$this->path->DocumentDbDms ;
		$path2	=	"" ;
		$file	=	"" ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "Document.php", "Document", "upload( ...)", "storage path := '$path2'") ;
		$idx	=	0 ;
		foreach ( $_FILES as $idx => $data) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "Document.php", "Document", "upload( ...)", "ImageName['$idx']") ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, "Document.php", "Document", "upload( ...)", print_r( $data, true)) ;
			$filename	=	$this->DocumentNo . "-" . $_revision ;
			$myFNParts	=	explode( ".", $data["name"]) ;
			switch ( $data["type"]) {
			default	:
				$filename	.=	"." . $myFNParts[1] ;
				break ;
			}
			FDbg::trace( 1, FDbg::mdTrcInfo1, "Document.php", "Document", "upload( ...)", "$path1$filename") ;
			if (move_uploaded_file( $data["tmp_name"], $path1 . $filename)) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "Document.php", "Document", "upload( ...)", "File is valid, and was successfully uploaded.") ;
			} else {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "Document.php", "Document", "upload( ...)", "Possible file upload attack!") ;
			}
		}
		FDbg::end() ;
		return $filename ;
	}
	/**
	 *
	 */
	function	getLatestRevision( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyMediaType	=	Reply::mediaAppPDF ;
		$myDocumentRevision	=	new DocumentRevision() ;
		$myQuery	=	$myDocumentRevision->getQueryObj( "Select") ;
		$myQuery->addWhere( "DocumentNo = '".$this->DocumentNo."' ") ;
		$myQuery->addOrder( "Id DESC") ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		$myRow	=	FDb::queryRow( $myQuery, self::$db[$this->className]) ;
		$myDocumentRevision->_assignFromRow( $myRow) ;
		$this->pdfName	=	$myDocumentRevision->Filename ;
		$this->fullPDFName	=	$this->path->DocumentDbDms . $myDocumentRevision->Filename ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')", "PDF.....: " . $this->fullPDFName) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * Kommentar zu der Colli hinzufuegen
	 *
	 *	Dies Funktion fuegt einen Kommentar, $_rem, an die Colli an.
	 *	Datum/Uhrzeit sowie die Id des angemeldeten Benutzers, oder - im Falle eines unauthtntizierten Zugriffs - der Zusatz: "Hintergrunf Prozess"
	 *	werden automatisch in dem Kommentar vermerkt.
	 *
	 *	@param	char	$_rem
	 *	@return	void
	 */
	function	addRem( $_key="", $_id=-1, $_val="") {
		try {
			$this->_addRem( $_POST[ '_IRem']) ;
		} catch( Exception $e) {
			throw $e ;
		} catch( FException $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 */

	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
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
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$_POST['_step']	=	$_id ;
		$objName	=	$_val ;
		if ( $objName == "DocumentContact") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$reply->replyData	=	$tmpObj->tableFromDb( "", "", "DocumentNo = '$this->DocumentNo' ") ;
		} else if ( $objName == "LiefDocument") {
			$tmpObj	=	new Document() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$tmpObj->addCol( "City", "varchar") ;
			$tmpObj->addCol( "Address", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "DocumentNo like '".$this->DocumentNo."-L%' ",
												"ORDER BY DocumentNo ", "DocumentLiefDocument",
												"",
												"C.Id, C.DocumentNo, CONCAT( C.DocumentName1, \" \", C.DocumentName2) AS Company, "
													. "CONCAT( C.ZIP, \" \", C.City) AS City, "
													. "CONCAT( C.Street, \" \", C.Number) AS Address "
			) ;
			$reply->replyData	=	str_replace( "Document>", "LiefDocument>", $ret) ;
			return $reply ;
		} else if ( $objName == "RechDocument") {
			$tmpObj	=	new Document() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "DocumentNo like '".$this->DocumentNo."-R%' ",
												"ORDER BY DocumentNo ",
												"DocumentRechDocument",
												"",
												"C.Id, C.DocumentNo, CONCAT( C.DocumentName1, C.DocumentName2) AS Company ") ;
			$reply->replyData	=	str_replace( "Document>", "RechDocument>", $ret) ;
		} else if ( $objName == "AddDocument") {
			$tmpObj	=	new Document() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "DocumentNo like '".$this->DocumentNo."-A%' ",
												"ORDER BY DocumentNo ",
												"DocumentAddDocument",
												"",
												"C.Id, C.DocumentNo, CONCAT( C.DocumentName1, C.DocumentName2) AS Company ") ;
			$reply->replyData	=	str_replace( "Document>", "AddDocument>", $ret) ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"LiefDocument"	:
			$myDocument	=	new Document() ;
			if ( $_id == -1) {
			} else {
				$myDocument->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Document>", "LiefDocument>", $myDocument->getXMLString()) ;
			break ;
		case	"RechDocument"	:
			$myDocument	=	new Document() ;
			if ( $_id == -1) {
			} else {
				$myDocument->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Document>", "RechDocument>", $myDocument->getXMLString()) ;
			break ;
		case	"AddDocument"	:
			$myDocument	=	new Document() ;
			if ( $_id == -1) {
			} else {
				$myDocument->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Document>", "AddDocument>", $myDocument->getXMLString()) ;
			break ;
		default	:
			parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	_addDepDocument( $_pref) {

		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->DocumentNo) ;
		$this->DocumentNo	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT DocumentNo FROM Document WHERE DocumentNo LIKE '%s-$_pref%%' ORDER BY DocumentNo DESC LIMIT 0, 1 ", $this->DocumentNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myDocumentDepAdr	=	new Document() ;
			if ( $numrows == 0) {
				$myDocumentDepAdr->DocumentNo	=	$this->DocumentNo . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myDocumentDepAdr->DocumentNo	=	sprintf( "%s-$_pref%03d", $this->DocumentNo, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myDocumentDepAdr->storeInDb() ;
			$myDocumentDepAdr->getFromPostL() ;
			$myDocumentDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myDocumentDepAdr->DocumentNo ;
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
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$myObj	=	new FDbObject( "Document", "DocumentNo", "def", "v_DocumentSurvey") ;				// no specific object we need here
			error_log( $myObj->__dump()) ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_documentNoCrit	=	$sCrit ;
			$filter	=	"( D.DocumentNo like '%" . $_documentNoCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			error_log( $myQuery) ;
			$myQuery->addOrder( ["DocumentNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"DocumentRevision"	:
			$myObj	=	new FDbObject( "DocumentRevision") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( DocumentNo = '" . $this->DocumentNo . "') " ;
			$filter2	=	"" ;
			/**
			*
			*/
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( [ "Id DESC"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "DocumentRevision") ;
			break ;
		}
//		error_log( $ret) ;
		return $reply ;
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
	 */
	function	newKey( $_digits=10, $_nsStart="0090000000", $_nsEnd="0099999999", $_store=true) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_digits, '$_nsStart', '$_nsEnd')") ;
		$keyCol	=	$this->keyCol ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myQuery->addOrder( $this->keyCol . " DESC") ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		$myRow	=	FDb::queryRow( $myQuery, self::$db[$this->className]) ;
		$this->_assignFromRow( $myRow) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( $_digits, '$_nsStart', '$_nsEnd')", "Last Key := '".$this->$keyCol."'") ;
		$this->$keyCol	=	sprintf( "%010d", intval( $this->$keyCol) + 1) ;
		/**
		 *
		 */
		if ( $_store) {
			$this->storeInDb() ;
			$this->reload() ;
		} else {
			$this->_valid	=	true ;
		}
		FDbg::end() ;
		return $this->$keyCol ;		// anmd return the newly assigned primary object key
	}
}
?>
