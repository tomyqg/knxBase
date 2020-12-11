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
 * CustomerContact - Base Class
 *
 * @package Application
 * @subpackage Basic
 */
class	CustomerContact	extends	AppObjectCore	{

	const	ALLG		=	"ALLG" ;
	const	FBPH		=	"FBPH" ;
	const	FBBI		=	"FBBI" ;
	const	FBCH		=	"FBCH" ;
	const	FBLPH		=	"FBLPH" ;	//
	const	FBLBI		=	"FBLBI" ;
	const	FBLCH		=	"FBLCH" ;
	const	FBLPHA		=	"FBLPHA" ;	// Fachbereichsleiter Physik, allgemein (keine Person)
	const	FBLBIA		=	"FBLBIA" ;
	const	FBLCHA		=	"FBLCHA" ;
	const	SCHL		=	"SCHL" ;
	const	SCHLL		=	"SCHLL" ;
	const	BSCH		=	"BSCH" ;
	const	FVV			=	"FVV" ;	// F�rderverein Vorsitzender

	private	static	$rFunktion	=	array (
						CustomerContact::ALLG		=> "Allgemein",
						CustomerContact::FBPH		=> "FB Physik",
						CustomerContact::FBBI		=> "FB Biologie",
						CustomerContact::FBCH		=> "FB Chemie",
						CustomerContact::FBLPH		=> "FB Leiter Physik",
						CustomerContact::FBLBI		=> "FB Leiter Biologie",
						CustomerContact::FBLCH		=> "FB Leiter Chemie",
						CustomerContact::FBLPHA	=> "FB Physik Allgemein",
						CustomerContact::FBLBIA	=> "FB Biologie Allgemein",
						CustomerContact::FBLCHA	=> "FB Chemie Allgemein",
						CustomerContact::SCHL		=> "Schulleiter",
						CustomerContact::SCHLL		=> "Schulleitung",
						CustomerContact::BSCH		=> "Beschaffer",
						CustomerContact::FVV		=> "Vorsitzender F�rderverein"
					) ;

	/**
	 *
	 */
	function	__construct( $_myCustomerNo="", $_myCustomerContactNo="") {
		FDbObject::__construct( "CustomerContact", array( "CustomerNo", "CustomerContactNo")) ;
		$this->Salutation	=	"Herr" ;
		$this->Title	=	"" ;
		$this->Opening	=	"Hallo" ;
		if ( strlen( $_myCustomerNo) > 0) {
			try {
				$this->setKey( array( $_myCustomerNo, $_myCustomerContactNo)) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		}
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	add( $_key, $_id, $_val) {
		FDbg::dumpL( 0x00000001, "CustomerContact::new(...)") ;
		return $this->_status ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "Customer.php", "CustomerContact", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		FDbg::end( 1, "Customer.php", "CustomerContact", "_upd()") ;
	}
		/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key, $_id, $_val) {
	}
	/**
	 *
	 */
	function	setCustomerContactNo( $_myCustomerNo, $_myCustomerContactNo) {
		FDbg::begin( 1, "CustomerContact.php", "CustomerContact", "setCustomerContactNo( '$_myCustomerNo', '$_myCustomerContactNo')") ;
		$this->setKey( array( $_myCustomerNo, $_myCustomerContactNo)) ;
		if ( $this->_valid) {
			$this->Opening	=	$this->getSalutationLine() ;
		} else {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "Customer.php", "Customer", "setCustomerContactNo( '$_myCustomerNo', '$_myCustomerContactNo')", "customer not valid!") ;
		}
		FDbg::end() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @return char
	 */
	function	newCustomerContact() {
		FDbg::begin( 1, "Customer.php", "CustomerContact", "newCustomerContact()") ;
		$query	=	sprintf( "SELECT CustomerContactNo FROM CustomerContact WHERE CustomerNo='%s' ORDER BY CustomerContactNo DESC LIMIT 0, 1 ", $this->CustomerNo) ;
		$sqlResult	=	FDb::query( $query) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$row    =       mysql_fetch_array( $sqlResult) ;
			$this->CustomerContactNo	=	sprintf( "%03d", intval( $row[0]) + 1) ;
			$this->storeInDb() ;
		}
		FDbg::end() ;
		return $this->_status ;
	}
	/**
	 *
	 */
	function	_getNextItemNo() {
		FDbg::dumpL( 0x00000001, "Customer.php::CustomerContact::_getNextItemNo(): begin") ;
		$query	=	"SELECT CustomerContactNo FROM CustomerContact WHERE CustomerNo='".$this->CustomerNo."' ORDER BY CustomerContactNo DESC LIMIT 0, 1 " ;
		try {
			$row	=	FDb::queryRow( $query) ;
			if ( $row) {
				$this->CustomerContactNo	=	sprintf( "%03d", intval( $row['CustomerContactNo']) + 1) ;
			} else {
				$this->CustomerContactNo	=	"001" ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "Customer.php::CustomerContact::_getNextItemNo(): end") ;
	}
	/**
	 * Use Case Methods
	 */
	function	getCustomerContactNo() {
		$this->_status	=	0 ;
		$query	=	"select CustomerContactNo from CustomerContact " ;
		$query	.=	"where CustomerNo = '" . $this->CustomerNo . "' " ;
		$query	.=	"order by CustomerContactNo DESC " ;
		$query	.=	"Limit 0, 1 " ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
			$this->CustomerContactNo	=	sprintf( "%03d", 1) ;
			$this->_valid   =       true ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			if ( $numrows == 1) {
				$row    =       mysql_fetch_assoc( $sqlResult) ;
				$this->CustomerContactNo	=	sprintf( "%03d", intval( $row['CustomerContactNo']) + 1) ;
				$this->_valid   =       true ;
			} else {
				$this->CustomerContactNo	=	sprintf( "%03d", 1) ;
				$this->_valid   =       true ;
			}
		}
		return $this->_status ;
	}
	/**
	 *	Funktion:	identifyCustomerContact
	 */
	/**
	 *
	 * @param	string	$password
	 * @return	bool	true = password ok, false = password wrong
	 */
	function	identifyCustomerContact( $password) {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$this->fetchFromDbByAll() ;
		if ( !$this->_valid) {
			$this->eMail	=	$this->CustomerNo ;
			$this->fetchFromDbByEmail( md5( $password)) ;
		}
		if ( $this->_valid) {
			$this->_valid	=	false ;
			if ( $this->Password != md5( $password)) {
				$this->_status	=	-6 ;
			} else {
				$this->_valid	=	true ;
			}
		} else {
			$this->_status	=	-7 ;
		}
		return $this->_valid ;
	}
	/**
	 *
	 */
	function	updatePassword( $_password) {
		$this->Password	=	md5( $_password) ;
		$this->updateInDb() ;
	}

	/**
	 *
	 */
	function	fetchFromDbByEmail( $_optPassword='') {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$query	=	"SELECT * " ;
		$query	.=	"FROM CustomerContact " ;
		$query	.=	"WHERE eMail='" . $this->eMail . "' " ;
		if ( strlen( $_optPassword) > 0) {
			$query	.=	"AND Password='" . $_optPassword . "' " ;
		}
		$query	.=	"ORDER BY CustomerNo, CustomerContactNo " ;
		$query	.=	"LIMIT 1 " ;
		$sqlResult      =       mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			if ( $numrows == 1) {
				$row    =       mysql_fetch_assoc( $sqlResult) ;
				$this->assignFromRow( $row) ;
				$this->_valid   =       true ;
			} else {
				$this->_status  =       -2 ;
			}
		}
		return $this->_status ;
	}

	/**
	 *
	 */
	function	sendPassword( $_mode="") {
		$myMailText	=	new SysTexte() ;
		if ( $_mode == "") {
			$myMailText->setKeys( "EMailCustomerPassword") ;
			$mySubject	=	FTr::tr( "Your registration, your Password") ;
		} else if ( $_mode == "wCustNo") {
			$myMailText->setKeys( "EMailCustomerPasswordWCustomerNo") ;
			$mySubject	=	FTr::tr( "Your registration, your Password") ;
		} else {
			$myMailText->setKeys( "CustomerEMailPasswordNew") ;
			$mySubject	=	FTr::tr( "Your New Password Request") ;
		}
		$this->_sendPassword( $mySubject, $myMailText->Volltext) ;
	}
	function	_sendPassword( $_subject, $_body) {
		$newPassword    =   "" ;
        for ( $count=0 ; $count < 8 ; $count++) {
            $c  =   rand(0, 62) ;
            if ( $c < 10) {
                $newPassword    .=  chr( $c + 48) ;
            } else if ( $c < 36) {
                $newPassword    .=  chr( $c - 10 + 65) ;
            } else if ( $c < 62) {
                $newPassword    .=  chr( $c - 36 + 97) ;
            }
        }
error_log( ".....................'$newPassword'.....................") ;
        $this->Password =   md5( $newPassword) ;
        $this->updateInDb() ;
		FDb::query( "UPDATE Customer SET Password = '" . md5( $newPassword) . "' WHERE CustomerNo = '" . $this->CustomerNo . "' ") ;
		FDb::query( "UPDATE CustomerContact SET Password = '" . md5( $newPassword) . "' WHERE CustomerNo = '" . $this->CustomerNo . "' ") ;
        try {
			$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
        	$myConfig	=	EISSCoreObject::__getConfig() ;
			$newMail	=	new mimeMail( $myConfig->eMail->Sales,
								$this->eMail,
								$myConfig->eMail->Sales,
								$_subject,
								"Bcc: ".$myConfig->eMail->Archive."\n") ;

			$mailData	=	array( "#CustomerNo" => $this->CustomerNo,
									"#Salutation" => $this->getSalutation(),
									"#WebSite" => $this->url->fullShop,
									"#Signature" => $this->eMail->Greeting,
									"#Disclaimer" => $myDisclaimerHTML->Volltext,
									"#Password" => $newPassword
							) ;
			$out	=	$_body ;
			foreach ( $mailData as $key => $val) {
				$in	=	$out ;
				$out	=	str_replace( $key, $val, $in) ;
			}
			$myText	=	new mimeData( "multipart/alternative") ;
			$myText->addData( "text/plain", xmlToPlain( "<div>".$out."</div>")) ;
			$myText->addData( "text/html", "<HTML><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><HEAD<body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n".$out."</HTML>", "", true) ;
			$newMail->addData( "multipart/mixed", $myText->getData(), $myText->getHead()) ;
			$mailSendResult	=	$newMail->send() ;
		} catch ( Exeption $e) {
			throw $e ;
		}
		return $this->isValid() ;
	}
	/**
	 *
	 */
	function	getRFunktion() {	return self::$rFunktion ;		}

}

?>
