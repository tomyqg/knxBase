<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "MimeMail.php") ;
require_once( "XmlTools.php") ;
/**
 * Software - Base Class
 *
 * @package Application
 * @subpackage Software
 */
class	Software	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_mySoftwareId="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_mySoftwareId')") ;
		parent::__construct( "Software", "SoftwareId") ;
		if ( strlen( $_mySoftwareId) > 0) {
			try {
				$this->setSoftwareId( $_mySoftwareId) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
		FDBg::end() ;
	}
	/**
	 *
	 */
	function	setSoftwareId( $_mySoftwareId) {
		$this->SoftwareId	=	$_mySoftwareId ;
		$this->reload() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply = NULL) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$mySoftware	=	new Software() ;
		if ( $_val == "SoftwareVersion"){
			$this->addDep( $_key, $_id, $_val, $reply) ;
		} else {
			try {
				$this->getFromPost() ;		// take object attrbutes from POST including KEY(s)
				$this->Remark	=	"" ;
				$this->storeInDb() ;
			}
			catch ( Exception $_e) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], Software invalid after creation!'") ;
			}
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
	function	del( $_key="", $_id=-1, $_val="", $reply = NULL) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->className ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		$objName	=	$this->className . "Kontakt" ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		return $this->getAsXML() ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$mySoftwareVersion	=	new SoftwareVersion() ;
		$mySoftwareVersion->SoftwareId	=	$this->SoftwareId ;
		$mySoftwareVersion->first( "SoftwareId = '".$this->SoftwareId."'", "SoftwareVersion DESC" ) ;
		$myContactNo	=	$mySoftwareVersion->SoftwareVersion ;
		$mySoftwareVersion->getFromPostL() ;
		$mySoftwareVersion->SoftwareVersion	=	sprintf( "%3d", intval( $myContactNo) + 1) ;
		$mySoftwareVersion->storeInDb() ;
		FDbg::end() ;
		return $_reply ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		$objName	=	$_val ;
		if ( $objName == "SoftwareVersion") {
			$mySoftwareVersion	=	new SoftwareVersion() ;
			$mySoftwareVersion->SoftwareId	=	$this->SoftwareId ;
			$mySoftwareVersion->newSoftwareVersion() ;
			$mySoftwareVersion->getFromPostL() ;
			$mySoftwareVersion->updateInDb() ;
			return $mySoftwareVersion->SoftwareVersion ;
		} else if ( $objName == "LiefSoftware") {
			return $this->_addDepSoftware( "L") ;
		} else if ( $objName == "RechSoftware") {
			return $this->_addDepSoftware( "R") ;
		} else if ( $objName == "AddSoftware") {
			return $this->_addDepSoftware( "A") ;
		}
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			parent::updDep( $_key, $_id, $_val, $reply) ;
			$reply->message	=	FTr::tr( "Contact succesfully updated!") ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		try {
			switch( $objName) {
			default	:
				parent::delDep( $_key, $_id, $_val, $reply) ;
				$reply->message	=	FTr::tr( "Contact succesfully deleted!") ;
				break ;
			}
		} catch ( Exception $e) {
			throw $e ;
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
	function	addRem( $_key="", $_id=-1, $_val="", $_reply=null) {
		try {
			$this->_addRem( $_POST[ '_IRem']) ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 */
	function	getAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
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
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"LiefSoftware"	:
			$mySoftware	=	new Software() ;
			if ( $_id == -1) {
			} else {
				$mySoftware->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Software>", "LiefSoftware>", $mySoftware->getAsXML()) ;
			break ;
		case	"RechSoftware"	:
			$mySoftware	=	new Software() ;
			if ( $_id == -1) {
			} else {
				$mySoftware->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Software>", "RechSoftware>", $mySoftware->getAsXML()) ;
			break ;
		case	"AddSoftware"	:
			$mySoftware	=	new Software() ;
			if ( $_id == -1) {
			} else {
				$mySoftware->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Software>", "AddSoftware>", $mySoftware->getAsXML()) ;
			break ;
		default	:
			$reply	=	parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		}
		return $reply ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getSoftwareVersionAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$mySoftwareVersion	=	new SoftwareVersion() ;
		$mySoftwareVersion->setId( $_id) ;
		$ret	.=	$mySoftwareVersion->getXMLF() ;
		return $ret ;
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
		case	"Software"	:
			$myObj	=	new FDbObject( "Software", "SoftwareId", "def", "v_SoftwareSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"SoftwareId like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"SoftwareVersion"	:
			$myObj	=	new FDbObject( "SoftwareVersion", "Id", "def", "v_SoftwareVersionSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_SoftwareIdCrit	=	$sCrit ;
			$filter1	=	"( SoftwareId = '" . $this->SoftwareId . "') " ;
			$filter2	=	"( Version like '%" . $sCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( [ "Build DESC"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}

	/**
	 *
	 * Enter description here ...
	 * @param   string $_key
	 * @param   int $_id
	 * @param   mixed $_val
	 * @param   Reply $_reply
	 * @return  Reply
	 */
	function	acList( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$a_json = array();
		$a_json_row = array();
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$_reply->replyMediaType	=	Reply::mediaTextJSON ;
		$sCrit	=	"" ;
		if ( isset( $_REQUEST['term']))
			$sCrit	=	$_REQUEST['term'] ;
		$mySoftware	=	new Software() ;
		$mySoftware->setIterCond( "SoftwareId like '%" . $sCrit . "%' OR SoftwareName1 like '%" . $sCrit . "%' ") ;
		$il0	=	0 ;
		foreach ( $mySoftware as $data) {
			if ( $il0 < 50) {
				$a_json_row["id"]		=	$data->Id ;
				$a_json_row["value"]	=	$data->SoftwareId ;
				$a_json_row["label"]	=	$data->SoftwareName1 . ", " . $data->SoftwareName2 ;
				$a_json_row["Name1"]	=	$data->SoftwareName1 ;
				array_push( $a_json, $a_json_row);
			}
			$il0++ ;
		}
		$_reply->data = json_encode($a_json);
		FDbg::end() ;
		return $_reply ;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param   string $_key
	 * @param   int $_id
	 * @param   mixed $_val
	 * @param   Reply $_reply
	 * @throws	FException
	 * @return  Reply
	 */
	function	checkIn( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$myUser	=	$this->__getAppUser() ;
		$mySoftwareVersion	=	new SoftwareVersion() ;
		$lookupVersion	=	"" ;
		$filter1	=	"SoftwareId = '" . $this->SoftwareId . "' " ;
		$order		=	"Build DESC" ;
		$mySoftwareVersion->first( $filter1, $order) ;
		if ( $mySoftwareVersion->isValid()) {
			$mySoftwareVersion->Build	+=	1 ;
		} else {
			$mySoftwareVersion->Build	=	1 ;
		}
		
		if ( count( $_FILES) > 0) {
			$myParts	=	explode( ".", $_FILES['DataFile']['name']) ;
			$ext	=	$myParts[ count( $myParts) - 1] ;
			$filename	=	$this->SoftwareId . "_" . $mySoftwareVersion->Version . "." . $ext ;
			$newName	=	"/srv/www/vhosts/wimtecc.de.local/mas_r1/data/00000002/pdm/svm/vault/" . $filename ;
			$myRes	=	move_uploaded_file( $_FILES['DataFile']['tmp_name'], $newName) ;
			if ( $myRes) {
				$mySoftwareVersion->CheckedInBy	=	$myUser->UserId ;
				$mySoftwareVersion->SoftwareId	=	$this->SoftwareId ;
				$mySoftwareVersion->Version	=	"" ;
				$mySoftwareVersion->Sha1	=	sha1_file( $newName) ;
				$mySoftwareVersion->Filename	=	$filename ;
				$mySoftwareVersion->DateReview	=	null ;
				$mySoftwareVersion->DateApproval	=	null ;
				$mySoftwareVersion->DateAvailable	=	null ;
				$mySoftwareVersion->DateEndOfLife	=	null ;
				$mySoftwareVersion->storeInDb() ;
			} else{
				throw new FException(basename(__FILE__),__CLASS__,__METHOD__."( '$_key', $_id, '$_val')","object[".$this->cacheName."], File upload failed!'");
			}
		}
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	
	/**
	 *
	 */
	protected	function	_postInstantiate() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}
	
	/**
	 *
	 */
	protected	function	_postLoad() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}
}
?>
