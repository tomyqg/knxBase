<?php

require_once( "config.inc.php") ;

FrontEndConnect::registerConn( "modis-gmbh.eu", "mg_eu", "psion0") ;

class	FrontEndConnect	{
	public	$_valid ;
	private	static	$ftp_server	=	array() ;
	private	static	$ftp_user_name	=	array() ;
	private	static	$ftp_user_pass	=	array() ;
	private	static	$conn_id	=	array() ;
	private	$myKey ;

	function	__construct( $_alias="def") {
		$this->_login( $_alias) ;
		$this->_valid	=	true ;
		$this->_goThere( "", $_alias) ;
		ftp_pasv( self::$conn_id[$_alias], true) ;
	}
	
	/**
	 * setKey
	 */
	function	setKey( $_key) {
		$this->myKey	=	$_key ;
	}
	
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getTableMZAsXML( $_key, $_id, $_val, $_alias="def") {
		return $this->_getTable( "CuCart", "CuCart", $_alias) ;
	}
	function	importCuWish( $_key="", $_id=0, $_val="", $_alias="def") {
		$fileName	=	$this->_getFile( $this->myKey, $_alias) ;
		$newCuWish	=	new CuCart() ;
		$newCuWishItem	=	new CuCartItem() ;
		if ( $iFile = fopen( $fileName, "r")) {
			$contents	=	fread( $iFile, filesize( $fileName));
			$xml	=	new XMLReader() ;
//			$xml->XML( iconv( 'ISO-8859-1', 'UTF-8', $contents)) ;
			$xml->XML( $contents) ;
			$itemCnt	=	0 ;
			for ( $cont = $newCuWish->_fetchFromXML( $xml) ;
					$cont ;
					$cont = $newCuWishItem->_fetchFromXML( $xml)) {
				if ( $itemCnt == 0) {
					$newCuWish->storeInDb() ;
				} else {
					$newCuWishItem->CuCartNo	=	$newCuWish->CuCartNo ;
					$newCuWishItem->storeInDb() ;
				}
				$itemCnt++ ;
			}
//			if ( $newCuWish->isValid() && $newCustContact->isValid()) {
//				$this->_delFile( $this->myKey) ;
//				unlink( $fileName) ;
//			}
		}
		return $this->getTableCuWishAsXML( $_key, $_id, $_val) ;
	}
	function	deleteCuWish( $_key="", $_id=0, $_val="", $_alias="def") {
		$this->_delFile( $this->myKey, $_alias) ;
		return $this->getTableCuWishAsXML( $_key, $_id, $_val, $_alias) ;
	}
		/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getTableCuRfqtAsXML( $_key, $_id, $_val, $_alias="def") {
		return $this->_getTable( "CuRFQ*", "CuRfqt") ;
	}
	function	importCuRfqt( $_key="", $_id=0, $_val="", $_alias="def") {
		$fileName	=	$this->_getFile( $this->myKey, $_alias) ;
		$newCuRfqt	=	new CuRFQ() ;
		$newCuRfqtItem	=	new CuRFQItem() ;
		if ( $iFile = fopen( $fileName, "r")) {
			$contents	=	fread( $iFile, filesize( $fileName));
			$xml	=	new XMLReader() ;
//			$xml->XML( iconv( 'ISO-8859-1', 'UTF-8', $contents)) ;
			$xml->XML( $contents) ;
			$itemCnt	=	0 ;
			for ( $cont = $newCuRfqt->_fetchFromXML( $xml) ;
					$cont ;
					$cont = $newCuRfqtItem->_fetchFromXML( $xml)) {
				if ( $itemCnt == 0) {
					$newCuRfqt->storeInDb() ;
				} else {
					$newCuRfqtItem->CuRFQNo	=	$newCuRfqt->CuRFQNo ;
					$newCuRfqtItem->storeInDb() ;
				}
				$itemCnt++ ;
			}
//			if ( $newCuRfqt->isValid() && $newCustContact->isValid()) {
//				$this->_delFile( $this->myKey) ;
//				unlink( $fileName) ;
//			}
		}
		return $this->getTableCuRfqtAsXML( $_key, $_id, $_val) ;
	}
	function	deleteCuRfqt( $_key="", $_id=0, $_val="", $_alias="def") {
		$this->_delFile( $this->myKey, $_alias) ;
		return $this->getTableCuRfqtAsXML( $_key, $_id, $_val, $_alias) ;
	}
	function	getCuRfqtAll( $_key="", $_id=0, $_val="", $_alias="def") {
		FDbg::dumpL( 0x000001, "FrontEndConnect.php::FrontEndConnect::getCuRfqtAll( '$_key', $_id, '$_val', '$_alias'): begin") ;
		$_filter	=	"CuRFQ*" ;
		$files	=	ftp_nlist( self::$conn_id[$_alias], $_filter."*") ;
		error_log( "$_filter") ;
		if ( $files !== false) {
			FDbg::dumpL( 0x000008, "FrontEndConnect.php::FrontEndConnect::getCuRfqtAll: There are files to be imported") ;
			sort( $files) ;
			foreach ( $files as $a => $b) {
				error_log( "Importing $b") ;
				$this->setKey( $b) ;
				$this->importCuRfqt() ;
				$this->deleteCuRfqt() ;
			}
		} else {
			FDbg::dumpL( 0x000008, "FrontEndConnect.php::FrontEndConnect::getCuRfqtAll: There are NO files to be imported") ;
		}
		FDbg::dumpL( 0x000001, "FrontEndConnect.php::FrontEndConnect::getCuRfqtAll( '$_key', $_id, '$_val', '$_alias'): end") ;
		return $this->getTableCuRfqtAsXML( $_key, $_id, $_val) ;
	}
	
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getTableCuOrdrAsXML( $_key, $_id, $_val, $_alias="def") {
		return $this->_getTable( "CuOrdr*", "CuOrdr", $_alias) ;
	}
	function	importCuOrdr( $_key="", $_id=0, $_val="", $_alias="def") {
		$fileName	=	$this->_getFile( $this->myKey) ;
		$newCuOrdr	=	new CuOrdr() ;
		$newCuOrdrItem	=	new CuOrdrItem() ;
		if ( $iFile = fopen( $fileName, "r")) {
			$contents	=	fread( $iFile, filesize( $fileName));
			$xml	=	new XMLReader() ;
//			$xml->XML( iconv( 'ISO-8859-1', 'UTF-8', $contents)) ;
			$xml->XML( $contents) ;
			$itemCnt	=	0 ;
			for ( $cont = $newCuOrdr->_fetchFromXML( $xml) ;
					$cont ;
					$cont = $newCuOrdrItem->_fetchFromXML( $xml)) {
				if ( $itemCnt == 0) {
					$tmpCuOrdr	=	new CuOrdr() ;
					$tmpCuOrdr->add() ;
					$newCuOrdr->Id	=	$tmpCuOrdr->Id ;
					$newCuOrdr->CuOrdrNo	=	$tmpCuOrdr->CuOrdrNo ;
					$newCuOrdr->updateInDb() ;
					$newCuOrdr->_addRem( "from FrontEnd CuOrdr: " . $this->myKey) ;
				} else {
					$newCuOrdrItem->CuOrdrNo	=	$newCuOrdr->CuOrdrNo ;
					$newCuOrdrItem->storeInDb() ;
				}
				$itemCnt++ ;
			}
//			if ( $newCuOrdr->isValid() && $newCustContact->isValid()) {
//				$this->_delFile( $this->myKey) ;
//				unlink( $fileName) ;
//			}
		}
		return $this->getTableCuOrdrAsXML( $_key, $_id, $_val) ;
	}
	function	deleteCuOrdr( $_key="", $_id=0, $_val="", $_alias="def") {
		$this->_delFile( $this->myKey, $_alias) ;
		return $this->getTableCuOrdrAsXML( $_key, $_id, $_val) ;
	}
	function	getCuOrdrAll( $_key="", $_id=0, $_val="", $_alias="def") {
		FDbg::dumpL( 0x000001, "FrontEndConnect.php::FrontEndConnect::getCuOrdrAll( '$_key', $_id, '$_val', '$_alias'): begin") ;
		$_filter	=	"CuOrdr*" ;
		$files	=	ftp_nlist( self::$conn_id[$_alias], $_filter."*") ;
		error_log( "$_filter") ;
		if ( $files !== false) {
			FDbg::dumpL( 0x000008, "FrontEndConnect.php::FrontEndConnect::getCuOrdrAll: There are files to be imported") ;
			sort( $files) ;
			foreach ( $files as $a => $b) {
				error_log( "Importing $b") ;
				$this->setKey( $b) ;
				$this->importCuOrdr() ;
				$this->deleteCuOrdr() ;
			}
		} else {
			FDbg::dumpL( 0x000008, "FrontEndConnect.php::FrontEndConnect::getCuOrdrAll: There are NO files to be imported") ;
		}
		FDbg::dumpL( 0x000001, "FrontEndConnect.php::FrontEndConnect::getCuOrdrAll( '$_key', $_id, '$_val', '$_alias'): end") ;
		return $this->getTableCuOrdrAsXML( $_key, $_id, $_val) ;
	}
	
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getTableCustAsXML( $_key, $_id, $_val, $_alias="def") {
		return $this->_getTable( "Cust*", "CustFiles", $_alias) ;
	}
	function	importCust( $_key, $_id, $_val, $_alias="def") {
		$fileName	=	$this->_getFile( $this->myKey) ;
		$newCust	=	new Kunde() ;
		$newCust->fetchFromXML( $fileName) ;
		$newCust->storeInDb() ;
		$newCustContact	=	new KundeKontakt() ;
		$newCustContact->fetchFromXML( $fileName) ;
		$newCustContact->storeInDb() ;
		if ( $newCust->isValid() && $newCustContact->isValid()) {
			$this->_delFile( $this->myKey, $_alias) ;
			unlink( $fileName) ;
		}
		return $this->getTableCustAsXML( $_key, $_id, $_val) ;
	}
	function	deleteCust( $_key, $_id, $_val, $_alias="def") {
		$this->_delFile( $this->myKey, $_alias) ;
		return $this->getTableCustAsXML( $_key, $_id, $_val) ;
	}
	function	getCustAll( $_key="", $_id=0, $_val="", $_alias="def") {
		FDbg::dumpL( 0x000001, "FrontEndConnect.php::FrontEndConnect::getCustAll( '$_key', $_id, '$_val', '$_alias'): begin") ;
		$_filter	=	"Kunde*" ;
		$files	=	ftp_nlist( self::$conn_id[$_alias], $_filter."*") ;
		error_log( "$_filter") ;
		if ( $files !== false) {
			FDbg::dumpL( 0x000008, "FrontEndConnect.php::FrontEndConnect::getCustAll: There are files to be imported") ;
			sort( $files) ;
			foreach ( $files as $a => $b) {
				error_log( "Importing $b") ;
				$this->setKey( $b) ;
				$this->importCust() ;
				$this->deleteCust() ;
			}
		} else {
			FDbg::dumpL( 0x000008, "FrontEndConnect.php::FrontEndConnect::getCustAll: There are NO files to be imported") ;
		}
		FDbg::dumpL( 0x000001, "FrontEndConnect.php::FrontEndConnect::getCustAll( '$_key', $_id, '$_val', '$_alias'): end") ;
		return $this->getTableCustAsXML( $_key, $_id, $_val) ;
	}
	
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	_getTable( $_filter, $_name, $_alias="def") {
		
		/**
		 * get all remotely created MZ = CuCart
		 */
		$ret	=	"" ;
		$files	=	ftp_nlist( self::$conn_id[$_alias], $_filter."*") ;
		error_log( "$_filter") ;
		if ( $files !== false) {
			sort( $files) ;
			$ret	.=	"<" . $_name . ">\n" ;
			foreach ( $files as $a => $b) {
				$ret	.=	"<Filename>" . $b . "</Filename>\n" ;
			}
			$ret	.=	"</" . $_name . ">\n" ;
		} else {
		}
		return $ret ;
	}
		
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	_goThere( $_name, $_alias="def") {
		ftp_chdir( self::$conn_id[$_alias], "Archiv") ;
		ftp_chdir( self::$conn_id[$_alias], "XML") ;
		ftp_chdir( self::$conn_id[$_alias], "down") ;
	}
		
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	_getFile( $_name, $_alias="def") {
		
		/**
		 * get all remotely created MZ = CuCart
		 */
		$tmpName	=	tempnam( "/tmp", "import") ;
		if ( ftp_get( self::$conn_id[$_alias], $tmpName, $this->myKey, FTP_BINARY)) {
//			ftp_delete( $conn_id, $b) ;
		} else {
			
		}
		return $tmpName ;
	}
		
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	_delFile( $_name, $_alias="def") {
		
		/**
		 * get all remotely created MZ = CuCart
		 */
		return ftp_delete( self::$conn_id[$_alias], $_name) ;
	}
		
	function	_login( $_alias="def")	{
		self::$conn_id[$_alias] = ftp_connect( self::$ftp_server[$_alias]); 
		/**
		 *	login with username and password
		 */
		$login_result = ftp_login( self::$conn_id[$_alias], self::$ftp_user_name[$_alias], self::$ftp_user_pass[$_alias]); 
		/**
		 *	check connection
		 */
		if ( ( ! self::$conn_id[$_alias]) || ( ! $login_result)) { 
			$e	=	new Exception( "FrontEndConnect.php::FrontEndConnect::login(...): Connection to '"
									. self::$ftp_server[$_alias]
									. "', for user '"
									. self::$ftp_user_name[$_alias]
									. "' failed") ;
			error_log( $e) ;
			throw $e ;
		} else {
			FDbg::dumpL( 0x000008, "FrontEndConnect.php::FrontEndConnect::login(...): Connection to '"
								. self::$ftp_server[$_alias]
								. "', for user '"
								. self::$ftp_user_name[$_alias]
								. "' ok !") ;
		}
	}

	function	_logout( $_alias="def")	{
		ftp_close( self::$this->conn_id[$_alias]); 
	}

	function	registerConn( $_server, $_user, $_pass, $_alias="def") {
		self::$ftp_server[ $_alias]		=	$_server ;
		self::$ftp_user_name[ $_alias]	=	$_user ;
		self::$ftp_user_pass[ $_alias]	=	$_pass ;
	}
}

?>
