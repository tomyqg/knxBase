<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * CustomerActivity - Base Class
 *
 * @package Application
 * @subpackage Basic
 */
class	CustomerActivity	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myCustomerNo="", $_myCustomerActivityNo="") {
		FDbObject::__construct( "CustomerActivity", array( "CustomerNo", "CustomerActivityNo")) ;
		$this->Salutation	=	"Herr" ;
		$this->Title	=	"" ;
		$this->Opening	=	"" ;
		if ( strlen( $_myCustomerNo) > 0) {
			try {
				$this->setKey( array( $_myCustomerNo, $_myCustomerActivityNo)) ;
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
	function	add( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::dumpL( 0x00000001, "CustomerActivity::new(...)") ;
		return $this->_status ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	_upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, "CustomerActivity.php", "CustomerActivity", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		FDbg::end( 1, "CustomerActivity.php", "CustomerActivity", "_upd()") ;
	}
		/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="", $_reply=null) {
	}
	/**
	 *
	 */
	function	setCustomerActivityNo( $_myCustomerNo, $_myCustomerActivityNo) {
		FDbg::begin( 1, "CustomerActivity.php", "CustomerActivity", "setCustomerActivityNo( '$_myCustomerNo', '$_myCustomerActivityNo')") ;
		$this->setKey( array( $_myCustomerNo, $_myCustomerActivityNo)) ;
		if ( $this->_valid) {
			$this->Opening	=	$this->getSalutationLine() ;
		} else {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "CustomerActivity.php", "Customer", "setCustomerActivityNo( '$_myCustomerNo', '$_myCustomerActivityNo')", "customer not valid!") ;
		}
		FDbg::end() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @return char
	 */
	function	newCustomerActivity() {
		FDbg::begin( 1, "CustomerActivity.php", "CustomerActivity", "newCustomerActivity()") ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myQuery->addWhere( "CustomerNo='" . $this->CustomerNo . "'") ;
		$myQuery->addOrder( ["ActivityNo DESC"]) ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		try {
			$row	=	FDb::queryRow( $myQuery) ;
			if ( $row) {
				$this->CustomerActivityNo	=	intval( $row['CustomerActivityNo']) + 1 ;
			} else {
				$this->CustomerActivityNo	=	1 ;
			}
			$this->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->_status ;
	}
	/**
	 *	Funktion:	identifyCustomerActivity
	 */
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
		FDb::query( "UPDATE CustomerActivity SET Password = '" . md5( $newPassword) . "' WHERE CustomerNo = '" . $this->CustomerNo . "' ") ;
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
