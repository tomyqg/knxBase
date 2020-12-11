<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * Address - Base Class
 *
 * @package Application
 * @subpackage Address
 */
class	Address	extends	AppObject	{
	/**
	 *
	 * @param   string $_myAddressNo
	 * @throws  Exception
	 */
	function	__construct( $_myAddressNo="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myAddressNo')") ;
		parent::__construct( "Address", "AddressNo") ;
		if ( strlen( $_myAddressNo) > 0) {
			try {
				$this->setAddressNo( $_myAddressNo) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
		FDbg::end() ;
	}

	/**
	 *
	 * @param   string $_myAddressNo
	 * @throws  Exception
	 */
	function	setAddressNo( $_myAddressNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myAddressNo')") ;
		$this->AddressNo	=	$_myAddressNo ;
		$this->fetchFromDb() ;
		if ( ! $this->_valid) {
			$e	=	new Exception( "Address.php::Addresst::setAddressNo( '$_myAddressNo'): key does not exist!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end() ;
	}

	/**
	 * @return  string
	 */
	function	getAddrStreet() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		switch ( $this->Country) {
		case	"de"	:
		case	"nl"	:
			$buffer	=	$this->Strasse . " " . $this->Hausnummer ;
			break ;
		case	"us"	:
			$buffer	=	$this->Hausnummer . ", " . $this->Strasse ;
			break ;
		default	:
			$buffer	=	"UN-CODED COUNTRY" ;
			break ;
		}
		FDbg::end() ;
		return $buffer ;
	}

	/**
	 *
	 */
	function	getAddrCity() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$buffer	=	"UN-CODED COUNTRY" ;
		switch ( $this->Country) {
		case	"de"	:
		case	"nl"	:
			$buffer	=	$this->ZIP . " " . $this->Ort ;
			break ;
		case	"us"	:
			$buffer	=	$this->Ort . ", " . $this->ZIP ;
			break ;
		default	:
			$buffer	=	$this->ZIP . " " . $this->Ort ;
			break ;
		}
		FDbg::end() ;
		return $buffer ;
	}

	/**
	 *
	 */
	function	getAddrCountry() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$buffer	=	"UN-CODED COUNTRY" ;
		switch ( $this->Country) {
		case	"de"	:
			$buffer	=	"" ;
			break ;
		default	:
			$buffer	=	$this->Country ;
			break ;
		}
		FDbg::end() ;
		return $buffer ;
	}

	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 * @param null $reply
	 * @throws  Exception
	 * @return null|Reply|void
	 */
	function	add( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$myAddress	=	new Address() ;
		if ( $_val == "AddressContact"){
			$this->addDep( $_key, $_id, $_val, $_reply) ;
		} else if ( $myAddress->first( "LENGTH(AddressNo) = 8", "AddressNo DESC")) {
			$this->getFromPostL() ;
			$this->AddressNo	=	sprintf( "%08d", intval( $myAddress->AddressNo) + 1) ;
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)", "newKey := " . $this->AddressNo) ;
			$this->storeInDb() ;
		} else {
			$this->getFromPostL() ;
			$this->AddressNo	=	sprintf( "%08d", intval( $myAddress->AddressNo) + 1) ;
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)", "newKey := " . $this->AddressNo) ;
			if ( ! $this->storeInDb()) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], Address invalid after creation!'") ;
			}
		}
		$this->getXMLString( $_key, $_id, $_val, $_reply) ;
		FDbg::end() ;
		return $_reply ;
	}

	/**
	 *
	 * @param   string  $_key
	 * @param   int     $_id
	 * @param   mixed   $_val
	 * @param   Reply   $_reply
	 * @return  Reply   $_reply
	 */
	function	del( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		parent::del( $_key, $_id, $_val, $_reply) ;
		$this->getList( $_key, $_id, $_val, $_reply) ;
		FDbg::end() ;
		return $_reply ;
	}

	/**
	 *
	 * @param   string $_key
	 * @param   int $_id
	 * @param   mixed $_val
	 * @param   Reply $_reply
	 * @return Reply $_reply
	 * @throws Exception
	 */
	function	copy( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$this->newKey( 6, "000000", "899999") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( "ERP::add(): Address invalid after creation") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
	}

	/**
	 *
	 * @param   string $_key
	 * @param   int $_id
	 * @param   mixed $_val
	 * @param   Reply $_reply
	 * @return  Reply $_reply
	 * @throws  FException
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$myAddressContact	=	new AddressContact() ;
		$myAddressContact->AddressNo	=	$this->AddressNo ;
		$myAddressContact->first( "AddressNo = '".$this->AddressNo."'", "AddressContactNo DESC" ) ;
		$myContactNo	=	$myAddressContact->AddressContactNo ;
		$myAddressContact->getFromPostL() ;
		$myAddressContact->AddressContactNo	=	sprintf( "%03d", intval( $myContactNo) + 1) ;
		$myAddressContact->storeInDb() ;
		FDbg::end() ;
		return $_reply ;
	}

	/**
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @param null $_reply
	 * @return null|Reply
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
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
			parent::updDep( $_key, $_id, $_val, $_reply) ;
			$_reply->message	=	FTr::tr( "Contact succesfully updated!") ;
		break ;
		}
		FDbg::end() ;
		return $_reply ;
	}

	/**
	 *
	 */
	function	getAsXML( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		else {
			$_reply->instClass	=	__class__ ;
			$_reply->replyingClass	=	$this->className ;
		}
		if ( $_val == "AddressContact"){
			$this->getDepAsXML( $_key, $_id, $_val, $_reply);
		} else {
			$_reply->replyData	.=	$this->getXMLF() ;
		}
		FDbg::end() ;
		return $_reply ;
	}

	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"AddressContact"	:
			$myAddressContact	=	new AddressContact() ;
			if ( $_id == -1) {
				$myAddressContact->Id	=	-1 ;
			} else {
				$myAddressContact->setId( $_id) ;
			}
			$_reply->replyData	=	$myAddressContact->getXMLF() ;
			break ;
		default	:
			$_reply	=	parent::getDepAsXML( $_key, $_id, $_val, $_reply) ;
			break ;
		}
		return $_reply ;
	}

	/**
	 *
	 * @param   string $_key
	 * @param   int $_id
	 * @param   mixed $_val
	 * @param   Reply $_reply
	 * @return  Reply $_reply
	 * @throws  FException
	 */
	function	getList( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
		case    "Address"   :
			$myObj	=	new FDbObject( "Address", "AddressNo", "def", "v_AddressSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"AddressName like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$_reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"AddressContact"	:
			$myObj	=	new FDbObject( "AddressContact", "Id", "def", "v_AddressContactSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( AddressNo = '" . $this->AddressNo . "') " ;
			$filter2	=	"( Name like '%" . $sCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["AddressNo", "AddressContactNo"]) ;
			$_reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $_reply ;
	}
	/**
	 *
	 * @param   string $_key
	 * @param   int $_id
	 * @param   mixed $_val
	 * @param   Reply $_reply
	 * @return  Reply $_reply
	 * @throws  FException
	 */
	function	getListAsXML( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$filter	=	"" ;
		$_searchCrit	=	"" ;
		$_adrNrCrit	=	"" ;
		$_firmaCrit	=	"" ;
		$_nameCrit	=	"" ;
		$_zipCrit	=	"" ;
		if ( isset( $_POST['_SSearch']))
			$_searchCrit	=	$_POST['_SSearch'] ;
		if ( isset( $_POST['_SAddressNo']))
			$_adrNrCrit	=	$_POST['_SAddressNo'] ;
		if ( isset( $_POST['_SCompany']))
			$_firmaCrit	=	$_POST['_SCompany'] ;
		if ( isset( $_POST['_SName']))
			$_nameCrit	=	$_POST['_SName'] ;
		if ( isset( $_POST['_step']))
			$_zipCrit	=	$_POST['_SZIP'] ;
		$_POST['_step']	=	$_id ;
		$filter	=	"( C.AddressNo like '%" . $_adrNrCrit . "%' ) " ;
		$filter	.=	"  AND ( C.Name1 like '%" . $_firmaCrit . "%' OR C.Name2 LIKE '%" . $_firmaCrit . "%') " ;
		if ( $_nameCrit != "")
			$filter	.=	"  AND ( AK.FirstName like '%$_nameCrit%' OR AK.LastName like '%$_nameCrit%' ) " ;
		if ( $_zipCrit != "")
			$filter	.=	"  AND ( C.ZIP like '%$_zipCrit%' ) " ;
		if ( $_searchCrit != "")
			$filter	.=	"  AND ( AK.FirstName like '%$_searchCrit%' OR AK.LastName like '%$_searchCrit%' ) " ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "AddressNo", "var") ;
		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "ZIP", "var") ;
		$myObj->addCol( "ContactName", "var") ;
//		$_reply->replyData	=	$myObj->tableFromDb( ", CONCAT( C.Name1, \" \", C.Name2) AS Name, CONCAT( AK.FirstName, \" \", AK.LastName) AS ContactName ",
//								"LEFT JOIN AddressContact AS AK ON AK.AddressNo = C.AddressNo ",
		$_reply->replyData	=	$myObj->tableFromDb( ", CONCAT( C.Name1, \" \", C.Name2) AS Name, \"NO NAME\" AS ContactName ",
								$filter,
								"ORDER BY C.AddressNo ASC ",
								"Address",
								"Address",
								"C.Id, C.AddressNo, C.ZIP ") ;
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
		$myAddress	=	new Address() ;
		$myAddress->setIterCond( "AddressNo like '%" . $sCrit . "%' OR Name1 like '%" . $sCrit . "%'  OR ZIP like '%" . $sCrit . "%'  OR City like '%" . $sCrit . "%' ") ;
		$il0	=	0 ;
		foreach ( $myAddress as $institution) {
			if ( $il0 < 50) {
				$a_json_row["id"]		=	$institution->Id ;
				$a_json_row["value"]	=	$institution->AddressNo ;
				$a_json_row["label"]	=	$institution->Name1 . ", " . $institution->Name2 ;
				$a_json_row["Name1"]	=	$institution->Name1 ;
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
