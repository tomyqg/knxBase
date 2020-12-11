<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * Kasse - Base Class
 *
 * @package Application
 * @subpackage Kasse
 */
class	Kasse	extends	AppObject	{
	public	$Rights	=	0x00000000 ;
	public	$Remark	=	"" ;

	/**
	 *
	 */
	public	$Name ;

	/**
	 *
	 */
	function	__construct( $_myERPNr="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myERPNr')") ;
		parent::__construct( "Kasse", "ERPNr") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myERPNr) > 0) {
			try {
				$this->setKasseNr( $_myERPNr) ;
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
	function	setKasseNr( $_myERPNr) {
		$this->ERPNr	=	$_myERPNr ;
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
		if ( $_val != "") {
			$this->_addDep( $_key, $_id, $_val, $reply) ;
		} else {
			$myKasse	=	new Kasse() ;
			if ( $myKasse->first( "LENGTH(ERPNr) = 12", "ERPNr DESC")) {
				$this->getFromPostL() ;
				$this->ERPNr	=	sprintf( "%012d", intval( $myKasse->ERPNr) + 1) ;
				$this->storeInDb() ;
			} else {
				$this->getFromPostL() ;
				$this->ERPNr	=	sprintf( "%012d", intval( $myKasse->ERPNr) + 1) ;
				$this->storeInDb() ;
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
						"object[".$this->cacheName."], Kasse invalid after creation!'") ;
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
	function	upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_val != "") {
			$this->_updDep( $_key, $_id, $_val, $reply) ;
		} else {
			$this->_upd( $_key, $_id, $_val, $_reply) ;
		}
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, "Kasse.php", "Kasse", "_upd()") ;
		parent::_upd( $_key, $_id, $_val, $_reply) ;
		$this->_addRem( FTr::tr( "Kasse updated " . ( $this->lastUpdateList != "" ? "\n" : "") . str_replace( ",", "\n", $this->lastUpdateList) . "")) ;
		FDbg::end() ;
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
		return $this->getAsXML() ;
		FDbg::end() ;
	}

	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="", $reply = NULL) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "Filiale") {
			$myFiliale	=	new Filiale() ;
			$myFiliale->getFromPostL() ;
			$myFiliale->ERPNr	=	$this->ERPNr ;
			$myFiliale->storeInDb() ;
			$reply->message	=	FTr::tr( "Filiale hinzugefügt!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
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
			$reply->message	=	FTr::tr( "{$objName} aktualisiert!") ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}

	/**
	 *
	 */
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		try {
			switch( $objName) {
			default	:
				parent::delDep( $_key, $_id, $_val, $reply) ;
				$reply->message	=	FTr::tr( "{$objName} gelöscht!") ;
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"Filiale"	:
			$myFiliale	=	new Filiale() ;
			if ( $_id == -1) {
				$myFiliale->Id	=	-1 ;
				$myFiliale->ERPNr	=	$this->ERPNr ;
				$myFiliale->KasseNr	=	$this->KasseNr ;
			} else {
				$myFiliale->setId( $_id) ;
			}
			$reply	=	$myFiliale->getAsXML( $_key, $_id, $_val, $reply) ;
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
	function	getFilialeAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myFiliale	=	new Filiale() ;
		$myFiliale->setId( $_id) ;
		$ret	.=	$myFiliale->getXMLF() ;
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
		case	"Kasse"	:
			$myObj	=	new FDbObject( "Kasse", "ERPNr", "def", "v_KasseSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"ERPNr like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}

	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	acList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$a_json = array();
		$a_json_row = array();
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyMediaType	=	Reply::mediaTextJSON ;
		$sCrit	=	"" ;
		if ( isset( $_REQUEST['term']))
			$sCrit	=	$_REQUEST['term'] ;
		$myKasse	=	new Kasse() ;
		$myKasse->setIterCond( "ERPNr like '%" . $sCrit . "%' OR Name1 like '%" . $sCrit . "%' ") ;
		$il0	=	0 ;
		foreach ( $myKasse as $firma) {
			if ( $il0 < 50) {
				switch ( $_val) {
				case	2	:
					$a_json_row["id"]		=	$firma->Id ;
					$a_json_row["value"]	=	$firma->ERPNr . "\n"
											.	$firma->Name1 . " " . $firma->Name2 . "\n"
											.	$firma->Strasse . " " . $firma->Hausnummer . "\n"
											.	$firma->PLZ . " " . $firma->Ort ;
					array_push( $a_json, $a_json_row);
					break ;
				default	:
					$a_json_row["id"]		=	$firma->Id ;
					$a_json_row["value"]	=	$firma->ERPNr ;
					$a_json_row["label"]	=	$firma->ERPNr . ", " . $firma->Name1 ;
					$a_json_row["kundeNr"]	=	$firma->KasseNr ;
					$a_json_row["Name1"]	=	$firma->Name1 ;
					array_push( $a_json, $a_json_row);
					break ;
				}
			}
			$il0++ ;
		}
		$reply->data = json_encode($a_json);
		FDbg::end() ;
		return $reply ;
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
