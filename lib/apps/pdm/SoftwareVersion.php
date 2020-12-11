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
 * SoftwareVersion - Base Class
 *
 * @package Application
 * @subpackage SoftwareVersion
 */
class	SoftwareVersion	extends	AppObject	{
	public $RevisionNew ;
	public $VersionNew ;
	/**
	 *
	 */
	function	__construct( $_myId="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myId')") ;
		parent::__construct( "SoftwareVersion", "Id") ;
		if ( $_myId > 0) {
			try {
				$this->setId( $_myId) ;
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
	function	setId( $_myId=-1) {
		$this->Id	=	$_myId ;
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
		$mySoftwareVersion	=	new SoftwareVersion() ;
		if ( $mySoftwareVersion->first( "LENGTH(SoftwareVersionId) = 8", "SoftwareVersionId DESC")) {
			$this->getFromPostL() ;
			$this->Sha1	=	"" ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
				"object[".$this->cacheName."], SoftwareVersion invalid after creation!'") ;
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
		$mySoftwareVersionVersion	=	new SoftwareVersionVersion() ;
		$mySoftwareVersionVersion->SoftwareVersionId	=	$this->SoftwareVersionId ;
		$mySoftwareVersionVersion->first( "SoftwareVersionId = '".$this->SoftwareVersionId."'", "SoftwareVersionVersionNo DESC" ) ;
		$myContactNo	=	$mySoftwareVersionVersion->SoftwareVersionVersionNo ;
		$mySoftwareVersionVersion->getFromPostL() ;
		$mySoftwareVersionVersion->SoftwareVersionVersionNo	=	sprintf( "%3d", intval( $myContactNo) + 1) ;
		$mySoftwareVersionVersion->storeInDb() ;
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
		if ( $objName == "SoftwareVersionVersion") {
			$mySoftwareVersionVersion	=	new SoftwareVersionVersion() ;
			$mySoftwareVersionVersion->SoftwareVersionId	=	$this->SoftwareVersionId ;
			$mySoftwareVersionVersion->newSoftwareVersionVersion() ;
			$mySoftwareVersionVersion->getFromPostL() ;
			$mySoftwareVersionVersion->updateInDb() ;
			return $mySoftwareVersionVersion->SoftwareVersionVersionNo ;
		} else if ( $objName == "LiefSoftwareVersion") {
			return $this->_addDepSoftwareVersion( "L") ;
		} else if ( $objName == "RechSoftwareVersion") {
			return $this->_addDepSoftwareVersion( "R") ;
		} else if ( $objName == "AddSoftwareVersion") {
			return $this->_addDepSoftwareVersion( "A") ;
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
		case	"LiefSoftwareVersion"	:
			$mySoftwareVersion	=	new SoftwareVersion() ;
			if ( $_id == -1) {
			} else {
				$mySoftwareVersion->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "SoftwareVersion>", "LiefSoftwareVersion>", $mySoftwareVersion->getAsXML()) ;
		break ;
		case	"RechSoftwareVersion"	:
			$mySoftwareVersion	=	new SoftwareVersion() ;
			if ( $_id == -1) {
			} else {
				$mySoftwareVersion->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "SoftwareVersion>", "RechSoftwareVersion>", $mySoftwareVersion->getAsXML()) ;
		break ;
		case	"AddSoftwareVersion"	:
			$mySoftwareVersion	=	new SoftwareVersion() ;
			if ( $_id == -1) {
			} else {
				$mySoftwareVersion->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "SoftwareVersion>", "AddSoftwareVersion>", $mySoftwareVersion->getAsXML()) ;
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
	function	getSoftwareVersionVersionAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$mySoftwareVersionVersion	=	new SoftwareVersionVersion() ;
		$mySoftwareVersionVersion->setId( $_id) ;
		$ret	.=	$mySoftwareVersionVersion->getXMLF() ;
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
		case	"SoftwareVersion"	:
			$myObj	=	new FDbObject( "SoftwareVersion", "Id", "def", "v_SoftwareVersionSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"SoftwareId like '%" . $sCrit . "%' " ;
			$filter2	=	"Version like '%" . $sCrit . "%' " ;
			$order	=	"Version" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( [ $order]) ;
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
		$mySoftwareVersion	=	new SoftwareVersion() ;
		$mySoftwareVersion->setIterCond( "SoftwareVersionId like '%" . $sCrit . "%' OR SoftwareVersionName1 like '%" . $sCrit . "%' ") ;
		$il0	=	0 ;
		foreach ( $mySoftwareVersion as $data) {
			if ( $il0 < 50) {
				$a_json_row["id"]		=	$data->Id ;
				$a_json_row["value"]	=	$data->SoftwareVersionId ;
				$a_json_row["label"]	=	$data->SoftwareVersionName1 . ", " . $data->SoftwareVersionName2 ;
				$a_json_row["Name1"]	=	$data->SoftwareVersionName1 ;
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
	 * @return  Reply
	 */
	function	checkIn( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
//		error_log( "-------------------------------------------------------------------------------------------------------") ;
//		error_log( print_r( $_POST, true)) ;
//		error_log( print_r( $_FILES, true)) ;
//		error_log( "-------------------------------------------------------------------------------------------------------") ;
		if ( strlen( $_POST[ "RevisionNew"]) > 0 && count( $_FILES) > 0) {
			$myParts	=	explode( ".", $_FILES['DataFile']['name']) ;
			$ext	=	$myParts[ count( $myParts) - 1] ;
			$filename	=	$this->SoftwareId . "_" . $_POST[ "RevisionNew"] . "." . $ext ;
			$newName	=	"/srv/www/vhosts/wimtecc.de.local/mas_r1/data/00000002/pdm/svm/vault/" . $filename ;
			$myRes	=	move_uploaded_file( $_FILES['DataFile']['tmp_name'], $newName) ;
			if ( $myRes) {
				$mySoftwareVersion = new SoftwareVersion() ;
				$mySoftwareVersion->SoftwareId	=	$this->SoftwareId ;
				$mySoftwareVersion->Version	=	$_POST[ "VersionNew"] ;
				$mySoftwareVersion->Sha1	=	sha1_file( $newName) ;
				$mySoftwareVersion->Filename	=	$filename ;
				$mySoftwareVersion->storeInDb() ;
//				error_log( "-------------------------------------------------------------------------------------------------------") ;
//				error_log( $_reply) ;
				$mySoftwareVersion->reload() ;									// => will trigger _postLoad( ...) !!!
				$mySoftwareVersion->getAsXML( $_key, $_id, $_val, $_reply) ;
//				error_log( $_reply) ;
//				error_log( "-------------------------------------------------------------------------------------------------------") ;
			} else{
				throw new FException(basename(__FILE__),__CLASS__,__METHOD__."( '$_key', $_id, '$_val')","object[".$this->cacheName."], File upload failed!'");
			}
		}
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
	 * @return  Reply
	 */
	function	checkOut( $_key="", $_id=-1, $_val="", $_reply=null){
		FDbg::begin(1,basename(__FILE__),__CLASS__,__METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if( $_reply == null){
			$_reply	=	new Reply(__class__,$this->className, Reply::mediaAppOctet) ;
		}
		$_reply->replyMediaType	=	Reply::mediaAppOctet ;
		$_reply->fileName	=	$this->Filename ;
		$_reply->fullFileName	=	"/srv/www/vhosts/wimtecc.de.local/mas_r1/data/00000002/pdm/svm/vault/" . $this->Filename ;
		
		error_log("-------------------------------------------------------------------------------------------------------") ;
		error_log( $_reply) ;
		error_log("-------------------------------------------------------------------------------------------------------") ;
		FDbg::end() ;
		return $_reply ;
	}

	/**
	 *
	 */
	protected	function	_postInstantiate() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$this->_declare( "RevisionNew") ;
		$this->_declare( "VersionNew") ;
		FDbg::end() ;
	}
	
	/**
	 *
	 */
	protected	function	_postLoad() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$this->RevisionNew	=	"" ;
//		$myParts	=	explode( "_", $this->Revision) ;
//		error_log( print_r( $myParts, true)) ;
//		if ( count( $myParts) > 0) {
//			$currentRevNo	=	new RevNo( $myParts[1]) ;
//			$this->RevisionNew	=	$myParts[0] . "_" . $currentRevNo->step() ;
//		} else {
//			$currentRevNo	=	new RevNo() ;
//			$this->RevisionNew	=	"R1A" . "_" . $currentRevNo . " :::";
//		}
		FDbg::end() ;
	}
}
?>
