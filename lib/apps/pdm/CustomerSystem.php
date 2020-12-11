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
 * CustomerSystem - Base Class
 *
 * @package Application
 * @subpackage CustomerSystem
 */
class	CustomerSystem	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myId="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myId')") ;
		parent::__construct( "CustomerSystem", "SerialNo") ;
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
	function	setId( $_myId) {
		$this->Id	=	$_myId ;
		$this->reload() ;
		return $this->_valid ;
	}

	function new( $_key="", $_id=-1, $_val="", $reply = NULL) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$myCustomerSystem	=	new CustomerSystem() ;
		FDbg::end() ;
		return $myCustomerSystem->getAsXML() ;
	}

	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply = NULL) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$myCustomerSystem	=	new CustomerSystem() ;
		if ( $_val == "XX"){
			$this->addDep( $_key, $_id, $_val, $reply) ;
		} else {
			try {
				$this->getFromPost() ;		// take object attrbutes from POST including KEY(s)
				$myCoder	=	new AccessCode( $this->SystemTypeId, $this->SerialNo) ;
				$this->LicenseKey	=	$myCoder->getAccessCode() ;
				$this->storeInDb() ;
			}
			catch ( Exception $_e) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], CustomerSystem invalid after creation!'") ;
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
		$myCustomerSystemVersion	=	new CustomerSystemVersion() ;
		$myCustomerSystemVersion->CustomerSystemId	=	$this->CustomerSystemId ;
		$myCustomerSystemVersion->first( "CustomerSystemId = '".$this->CustomerSystemId."'", "CustomerSystemVersionNo DESC" ) ;
		$myContactNo	=	$myCustomerSystemVersion->CustomerSystemVersionNo ;
		$myCustomerSystemVersion->getFromPostL() ;
		$myCustomerSystemVersion->CustomerSystemVersionNo	=	sprintf( "%3d", intval( $myContactNo) + 1) ;
		$myCustomerSystemVersion->storeInDb() ;
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
		if ( $objName == "CustomerSystemVersion") {
			$myCustomerSystemVersion	=	new CustomerSystemVersion() ;
			$myCustomerSystemVersion->CustomerSystemId	=	$this->CustomerSystemId ;
			$myCustomerSystemVersion->newCustomerSystemVersion() ;
			$myCustomerSystemVersion->getFromPostL() ;
			$myCustomerSystemVersion->updateInDb() ;
			return $myCustomerSystemVersion->CustomerSystemVersionNo ;
		} else if ( $objName == "LiefCustomerSystem") {
			return $this->_addDepCustomerSystem( "L") ;
		} else if ( $objName == "RechCustomerSystem") {
			return $this->_addDepCustomerSystem( "R") ;
		} else if ( $objName == "AddCustomerSystem") {
			return $this->_addDepCustomerSystem( "A") ;
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
		case	"LiefCustomerSystem"	:
			$myCustomerSystem	=	new CustomerSystem() ;
			if ( $_id == -1) {
			} else {
				$myCustomerSystem->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "CustomerSystem>", "LiefCustomerSystem>", $myCustomerSystem->getAsXML()) ;
		break ;
		case	"RechCustomerSystem"	:
			$myCustomerSystem	=	new CustomerSystem() ;
			if ( $_id == -1) {
			} else {
				$myCustomerSystem->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "CustomerSystem>", "RechCustomerSystem>", $myCustomerSystem->getAsXML()) ;
		break ;
		case	"AddCustomerSystem"	:
			$myCustomerSystem	=	new CustomerSystem() ;
			if ( $_id == -1) {
			} else {
				$myCustomerSystem->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "CustomerSystem>", "AddCustomerSystem>", $myCustomerSystem->getAsXML()) ;
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
	function	getCustomerSystemVersionAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myCustomerSystemVersion	=	new CustomerSystemVersion() ;
		$myCustomerSystemVersion->setId( $_id) ;
		$ret	.=	$myCustomerSystemVersion->getXMLF() ;
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
		case	"CustomerSystem"	:
			$myObj	=	new FDbObject( "CustomerSystem", "Id", "def", "v_CustomerSystemSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"( ProjectNo like '%" . $sCrit . "%' OR SerialNo like '%" . $sCrit . "%' OR SystemTypeId like '%" . $sCrit . "%') " ;
			$myQuery->addWhere( [ $filter1]) ;
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
		$myCustomerSystem	=	new CustomerSystem() ;
		$myCustomerSystem->setIterCond( "CustomerSystemId like '%" . $sCrit . "%' OR CustomerSystemName1 like '%" . $sCrit . "%' ") ;
		$il0	=	0 ;
		foreach ( $myCustomerSystem as $data) {
			if ( $il0 < 50) {
				$a_json_row["id"]		=	$data->Id ;
				$a_json_row["value"]	=	$data->CustomerSystemId ;
				$a_json_row["label"]	=	$data->CustomerSystemName1 . ", " . $data->CustomerSystemName2 ;
				$a_json_row["Name1"]	=	$data->CustomerSystemName1 ;
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
		$myCoder	=	new AccessCode( $this->SystemTypeId, $this->SerialNo) ;
		$this->LicenseKey	=	$myCoder->getAccessCode() ;
		FDbg::end() ;
	}
}
?>
