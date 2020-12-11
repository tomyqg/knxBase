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
 * PLCSystem - Base Class
 *
 * @package Application
 * @subpackage PLCSystem
 */
class	PLCSystem	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myPLCSystemId="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myPLCSystemId')") ;
		parent::__construct( "PLCSystem", "PLCSystemId") ;
		if ( strlen( $_myPLCSystemId) > 0) {
			try {
				$this->setPLCSystemId( $_myPLCSystemId) ;
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
	function	setPLCSystemId( $_myPLCSystemId) {
		$this->PLCSystemId	=	$_myPLCSystemId ;
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
		$myPLCSystem	=	new PLCSystem() ;
		if ( $_val == "PLCSystemVariant"){
			$this->addDep( $_key, $_id, $_val, $reply) ;
		} else {
			try {
				$this->getFromPost() ;		// take object attrbutes from POST including KEY(s)
				$this->Remark	=	"" ;
				$this->storeInDb() ;
			}
			catch ( Exception $_e) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], PLCSystem invalid after creation!'") ;
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
		$myPLCSystemVariant	=	new PLCSystemVariant() ;
		$myPLCSystemVariant->PLCSystemId	=	$this->PLCSystemId ;
		$myPLCSystemVariant->first( "PLCSystemId = '".$this->PLCSystemId."'", "PLCSystemVariantNo DESC" ) ;
		$myContactNo	=	$myPLCSystemVariant->PLCSystemVariantNo ;
		$myPLCSystemVariant->getFromPostL() ;
		$myPLCSystemVariant->PLCSystemVariantNo	=	sprintf( "%3d", intval( $myContactNo) + 1) ;
		$myPLCSystemVariant->storeInDb() ;
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
		if ( $objName == "PLCSystemVariant") {
			$myPLCSystemVariant	=	new PLCSystemVariant() ;
			$myPLCSystemVariant->PLCSystemId	=	$this->PLCSystemId ;
			$myPLCSystemVariant->newPLCSystemVariant() ;
			$myPLCSystemVariant->getFromPostL() ;
			$myPLCSystemVariant->updateInDb() ;
			return $myPLCSystemVariant->PLCSystemVariantNo ;
		} else if ( $objName == "LiefPLCSystem") {
			return $this->_addDepPLCSystem( "L") ;
		} else if ( $objName == "RechPLCSystem") {
			return $this->_addDepPLCSystem( "R") ;
		} else if ( $objName == "AddPLCSystem") {
			return $this->_addDepPLCSystem( "A") ;
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
	function	getPLCSystemVariantAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myPLCSystemVariant	=	new PLCSystemVariant() ;
		$myPLCSystemVariant->setId( $_id) ;
		$ret	.=	$myPLCSystemVariant->getXMLF() ;
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
		case	"PLCSystem"	:
			$myObj	=	new FDbObject( "PLCSystem", "PLCSystemId", "def", "v_PLCSystemSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"PLCSystemId like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"PLCSystemVariant"	:
			$myObj	=	new FDbObject( "PLCSystemVariant", "Id", "def", "v_PLCSystemVariantSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_PLCSystemIdCrit	=	$sCrit ;
			$filter1	=	"( PLCSystemId = '" . $this->PLCSystemId . "') " ;
			$filter2	=	"( Description1 like '%" . $sCrit . "%') " ;
			$filter3	=	"( SoftwareId like '%" . $sCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2, $filter3]) ;
			$myQuery->addOrder( ["PLCSystemId", "PLCSystemVariantId"]) ;
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
		$myPLCSystem	=	new PLCSystem() ;
		$myPLCSystem->setIterCond( "PLCSystemId like '%" . $sCrit . "%' OR PLCSystemName1 like '%" . $sCrit . "%' ") ;
		$il0	=	0 ;
		foreach ( $myPLCSystem as $data) {
			if ( $il0 < 50) {
				$a_json_row["id"]		=	$data->Id ;
				$a_json_row["value"]	=	$data->PLCSystemId ;
				$a_json_row["label"]	=	$data->PLCSystemName1 . ", " . $data->PLCSystemName2 ;
				$a_json_row["Name1"]	=	$data->PLCSystemName1 ;
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
