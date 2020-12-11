<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * Project - Base Class
 *
 * @package Application
 * @subpackage Project
 */
class	Project	extends	AppObjectCore	{
	/**
	 *
	 */
	function	__construct( $_myProjectNo="") {
		parent::__construct( "Project", "ProjectNo") ;
		if ( strlen( $_myProjectNo) > 0) {
			try {
				$this->setProjectNo( $_myProjectNo) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
	}
	/**
	 *
	 */
	function	setProjectNo( $_myProjectNo) {
		$this->ProjectNo	=	$_myProjectNo ;
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
			$this->ProjectNo	=	$myKey ;
			$this->storeInDb() ;
		} else {
			$e	=	new Exception( "Project.php::Project::add(): 'Project' invalid after creation!") ;
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
		$this->_upd() ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "Project.php", "Project", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		$this->_addRem( FTr::tr( "Project updated")) ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Project.php::Project::del(...)") ;
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Document.php", "Document", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		case "Document"	:
			$myDocument	=	new Document() ;
			$myDocument->add() ;
			$this->getList( $_key, $_id, $_val, $reply) ;
			break;
		default	:
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="") {
		$objName	=	$_val ;
		if ( $objName == "ProjectContact") {
			$myProjectContact	=	new ProjectContact() ;
			$myProjectContact->ProjectNo	=	$this->ProjectNo ;
			$myProjectContact->newProjectContact() ;
			$myProjectContact->getFromPostL() ;
			$myProjectContact->updateInDb() ;
			return $myProjectContact->ProjectContactNo ;
		} else if ( $objName == "LiefProject") {
			return $this->_addDepProject( "L") ;
		} else if ( $objName == "RechProject") {
			return $this->_addDepProject( "R") ;
		} else if ( $objName == "AddProject") {
			return $this->_addDepProject( "A") ;
		}
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, FDbg::basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			parent::updDep( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>))") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
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
		}
		return $this->getXMLComplete() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case "Document"	:
			$tmpObj	=	new $objName() ;
			if ( $_id == -1) {
				$tmpObj->ABCClass	=	$this->ABCClass ;
				$tmpObj->ProductId	=	$this->ProductId ;
			} else {
				$tmpObj->setId( $_id) ;
			}
			$reply->replyData	=	$tmpObj->getXML() ;
			break;
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
	function	_addDepProject( $_pref) {

		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->ProjectNo) ;
		$this->ProjectNo	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT ProjectNo FROM Project WHERE ProjectNo LIKE '%s-$_pref%%' ORDER BY ProjectNo DESC LIMIT 0, 1 ", $this->ProjectNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myProjectDepAdr	=	new Project() ;
			if ( $numrows == 0) {
				$myProjectDepAdr->ProjectNo	=	$this->ProjectNo . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myProjectDepAdr->ProjectNo	=	sprintf( "%s-$_pref%03d", $this->ProjectNo, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myProjectDepAdr->storeInDb() ;
			$myProjectDepAdr->getFromPostL() ;
			$myProjectDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myProjectDepAdr->ProjectNo ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
		 	$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$myObj	=	new FDbObject( "Project") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_documentNoCrit	=	$sCrit ;
			$filter	=	"( C.ProjectNo like '%" . $_documentNoCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addOrder( ["ProjectNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Project") ;
			break ;
		case	"ProjectRevision"	:
			break ;
		case	"Document"	:
			$myObj	=	new FDbObject( "Document") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_documentNoCrit	=	$sCrit ;
			$filter1	=	"( ABCClass = '" . $this->ABCClass . "') " ;
			$filter2	=	"( ProductId = '" . $this->ProductId . "') " ;
			if ( $sCrit != "") {
				$filter3	=	"( DecimalClass = '".$sCrit."' )" ;
			} else {
				$filter3	=	"" ;
			}
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addOrder( [ "DecimalClass", "Prefix", "Postfix"]) ;
			$myQuery->addWhere( [ $filter1, $filter2, $filter3]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Document") ;
			break ;
		}
		FDbg::end() ;
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
	function	newKey( $_digits=10, $_nsStart="0010000000", $_nsEnd="0019999999", $_store=true) {
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
