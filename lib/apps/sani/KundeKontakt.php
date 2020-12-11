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
 * KundeKontakt - Base Class
 *
 * @package Application
 * @subpackage Basic
 */
class	KundeKontakt	extends	AddressKontakt	{
	/**
	 *
	 */
	function	__construct( $_myKundeNr="", $_myKundeKontaktNr="") {
		FDbObject::__construct( "KundeKontakt", array( "KundeNr", "KundeKontaktNr")) ;
		$this->defComplexKey( array( "KundeNr", "KundeKontaktNr")) ;
		$this->Anrede	=	"Herr" ;
		$this->Titel	=	"" ;
		$this->Ansprache	=	"" ;
		if ( strlen( $_myKundeNr) > 0) {
			try {
				$this->setKey( array( $_myKundeNr, $_myKundeKontaktNr)) ;
			} catch ( FException $e) {
				throw $e ;
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
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::dumpL( 0x00000001, "KundeKontakt::new(...)") ;
		return $this->_status ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "KundeKontakt.php", "KundeKontakt", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		FDbg::end( 1, "KundeKontakt.php", "KundeKontakt", "_upd()") ;
	}
		/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="", $reply=null) {
	}
	/**
	 *
	 */
	function	setKundeKontaktNr( $_myKundeNr, $_myKundeKontaktNr) {
		FDbg::begin( 1, "KundeKontakt.php", "KundeKontakt", "setKundeKontaktNr( '$_myKundeNr', '$_myKundeKontaktNr')") ;
		$this->setKey( array( $_myKundeNr, $_myKundeKontaktNr)) ;
		if ( $this->_valid) {
			$this->Ansprache	=	$this->getAnredeLine() ;
		} else {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "KundeKontakt.php", "Kunde", "setKundeKontaktNr( '$_myKundeNr', '$_myKundeKontaktNr')", "customer not valid!") ;
		}
		FDbg::end() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @return char
	 */
	function	newKundeKontakt() {
		FDbg::begin( 1, "KundeKontakt.php", "KundeKontakt", "newKundeKontakt()") ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myQuery->addWhere( "KundeNr='" . $this->KundeNr . "'") ;
		$myQuery->addOrder( ["KundeKontaktNr DESC"]) ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		try {
			$row	=	FDb::queryRow( $myQuery) ;
			error_log( $this->KundeNr . "..............................> " . $row['KundeKontaktNr']) ;
			if ( $row) {
			error_log( $this->KundeNr . "..............................> " . $row['KundeKontaktNr']) ;
				$this->KundeKontaktNr	=	sprintf( "%03d", intval( $row['KundeKontaktNr']) + 1) ;
			} else {
				$this->KundeKontaktNr	=	"001" ;
			}
			$this->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->_status ;
	}
	/**
	 *	Funktion:	identifyKundeKontakt
	 */
	/**
	 *
	 * @param	string	$password
	 * @return	bool	true = password ok, false = password wrong
	 */
	function	identifyKundeKontakt( $password) {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$this->fetchFromDbByAll() ;
		if ( !$this->_valid) {
			$this->eMail	=	$this->KundeNr ;
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
		$query	.=	"FROM KundeKontakt " ;
		$query	.=	"WHERE eMail='" . $this->eMail . "' " ;
		if ( strlen( $_optPassword) > 0) {
			$query	.=	"AND Password='" . $_optPassword . "' " ;
		}
		$query	.=	"ORDER BY KundeNr, KundeKontaktNr " ;
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
			$myMailText->setKeys( "EMailKundePassword") ;
			$mySubject	=	FTr::tr( "Your registration, your Password") ;
		} else if ( $_mode == "wCustNo") {
			$myMailText->setKeys( "EMailKundePasswordWKundeNr") ;
			$mySubject	=	FTr::tr( "Your registration, your Password") ;
		} else {
			$myMailText->setKeys( "KundeEMailPasswordNew") ;
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
		FDb::query( "UPDATE Kunde SET Password = '" . md5( $newPassword) . "' WHERE KundeNr = '" . $this->KundeNr . "' ") ;
		FDb::query( "UPDATE KundeKontakt SET Password = '" . md5( $newPassword) . "' WHERE KundeNr = '" . $this->KundeNr . "' ") ;
        try {
			$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
        	$myConfig	=	EISSCoreObject::__getConfig() ;
			$newMail	=	new mimeMail( $myConfig->eMail->Sales,
								$this->eMail,
								$myConfig->eMail->Sales,
								$_subject,
								"Bcc: ".$myConfig->eMail->Archive."\n") ;

			$mailData	=	array( "#KundeNr" => $this->KundeNr,
									"#Anrede" => $this->getAnrede(),
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
