<?php
/*
 * 2007-2012 openEISS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@wimtecc.de so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade openEISS to newer
 * versions in the future. If you wish to customize openEISS for your
 * needs please refer to http://www.openeiss.com for more information.
 *  
 *  Revision history
 *  
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2013-05-13	PA1		khw		added to rev. control
 *
 *  @author Karl-Heinz Welter <khwelter@me.com>
 *  @copyright  2007-2012 wimtecc.de
 *  @version  Release: $Revision$
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * AbKorr.php - Package: Article Stock Correction
 * 
 * Main Class:
 * <ul>
 * <li>AbKorr</li>
 * </ul>
 * 
 * Dependent Classes:
 * <ul>
 * <li>AbKorrPosten</li>
 * </ul>
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * requires the WTF Debugger and the Base Class
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/AppObject.php") ;
require_once( "base/AppDepObject.php") ;
require_once( "option.php") ;
/**
 * Class AbKorr - Stock corrections main data
 *
 * This class acts as an interface towards the automatically generated BAbKorr which should
 * not be modified.
 *
 * @package Application
 * @subpackage AbKorr
 */
class	AbKorr	extends	AppObject	{
	/**
	 * definition of constants
	 * @var $rType	array of types of a stock correction
	 */
	const	STD		=	0 ;
	const	INV_OUT	=	11 ;
	const	INV_IN	=	12 ;
	private	static	$rType	=	array (
						-1				=> "ALL",
						AbKorr::STD		=> "standard",
						AbKorr::INV_OUT	=> "inventory out",
						AbKorr::INV_IN	=> "inventory in"
					) ;
	/**
	 * 
	 * @var unknown_type
	 */
	var	$startRow	=	0 ;
	var	$rowCount	=	10 ;					
	/*
	 * The constructor can be passed an ArticleNr (AbKorrNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		FDbg::dumpL( 0x00000100, "AbKorr::__construct(...): ") ;
		AppObject::__construct( "AbKorr", "AbKorrNr") ;
		if ( ! isset( $_SESSION['Sess_AbKorr_startRow'])) {
			$this->setStartRow( 0) ;
			$this->setRowCount( 10) ;
			$_SESSION['Sess_AbKorr_startRow']	=	$this->getStartRow() ;	
			$_SESSION['Sess_AbKorr_rowCount']	=	$this->getRowCount() ;	
		} else {
			$this->setStartRow( $_SESSION['Sess_AbKorr_startRow']) ;	
			$this->setRowCount( $_SESSION['Sess_AbKorr_rowCount']) ;	
		}
		if ( $_id >= 0) {
			$this->setId	=	$_id ;
		} else {
		}
	}

	/**
	 *
	 */
	function	setAbKorrNr( $_abKorrNr='') {
		FDbg::dumpL( 0x00000100, "AbKorr::setId(...): ") ;
		if ( strlen( $_abKorrNr) > 0) {
			$this->AbKorrNr	=	$_abKorrNr ;
			$this->reload() ;
		} else {
		}
	}
	function	_addPos( $_artikelNr, $_id, $_menge, $_posNr=0) {
		try {
			$myArtikel	=	new Artikel() ;
			$myArtikel->setKey( $_artikelNr) ;
			if ( $myArtikel->_valid) {
				while ( strlen( $myArtikel->ArtikelNrNeu) > 0 || strlen( $myArtikel->ArtikelNrErsatz) > 0) {
					if ( strlen( $myArtikel->ArtikelNrNeu) > 0) {
						$myArtikel->setArtikelNr( $myArtikel->ArtikelNrNeu) ;
					} else if ( strlen( $myArtikel->ArtikelNrErsatz) > 0) {
						$myArtikel->setArtikelNr( $myArtikel->ArtikelNrErsatz) ;
					}
				}
				$newAbKorrPosten	=	new AbKorrPosten( $this->AbKorrNr) ;
				if ( $_posNr == 0)
					$newAbKorrPosten->getNextPosNr() ;
				else
					$newAbKorrPosten->PosNr	=	$_posNr ;
				$newAbKorrPosten->ArtikelNr	=	$myArtikel->ArtikelNr ;
				$newAbKorrPosten->Menge	=	$_menge ;
				$newAbKorrPosten->MengeProVPE	=	1 ;
				$newAbKorrPosten->storeInDb() ;
				if ( $myArtikel->Comp == 0) {
				} else {
//					if ( $myArtikel->ModeAbKorr == 1) {
//						FDbg::dumpL( 0x00000100, "AbKorr.php::addPos(...), Composed Article needs to be broken up") ;
//						$this->addSubPos( $newAbKorrPosten->PosNr, $myArtikel->ArtikelNr, $newAbKorrPosten->Menge, "") ;
//					}
				}
			} else {
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $newAbKorrPosten ;
	}

	function	addPos( $_artikelNr, $_id, $_menge) {
		FDbg::dumpL( 0x00000001, "AbKorr.php::AbKorr::addPos( $_artikelNr, $_id, $_menge): begin") ;
		try {
			$myArtikel	=	new Artikel() ;
			$myArtikel->setId( $_id) ;
			if ( $myArtikel->_valid) {
				while ( strlen( $myArtikel->ArtikelNrNeu) > 0 || strlen( $myArtikel->ArtikelNrErsatz) > 0) {
					if ( strlen( $myArtikel->ArtikelNrNeu) > 0) {
						$myArtikel->setArtikelNr( $myArtikel->ArtikelNrNeu) ;
					} else if ( strlen( $myArtikel->ArtikelNrErsatz) > 0) {
						$myArtikel->setArtikelNr( $myArtikel->ArtikelNrErsatz) ;
					}
				}
				if ( $myArtikel->Comp == 0) {
					FDbg::dumpL( 0x00000008, "AbKorr.php::AbKorr::addPos: no components, adding new item") ;
					$newAbKorrPosten	=	new AbKorrPosten( $this->AbKorrNr) ;
					$newAbKorrPosten->getNextPosNr() ;
					$newAbKorrPosten->ArtikelNr	=	$myArtikel->ArtikelNr ;
					$newAbKorrPosten->Menge	=	$_menge ;
					$newAbKorrPosten->MengeProVPE	=	1 ;
					$newAbKorrPosten->storeInDb() ;
				} else {
					$newAbKorrPosten	=	new AbKorrPosten( $this->AbKorrNr) ;
					$newAbKorrPosten->getNextPosNr() ;
					$newAbKorrPosten->ArtikelNr	=	$myArtikel->ArtikelNr ;
					$newAbKorrPosten->Menge	=	$_menge ;
					$newAbKorrPosten->MengeProVPE	=	1 ;
					$newAbKorrPosten->storeInDb() ;
//					if ( $myArtikel->ModeAbKorr == 1) {
//						FDbg::dumpL( 0x00000100, "AbKorr.php::addPos(...), Composed Article needs to be broken up") ;
//						$this->addSubPos( $newAbKorrPosten->PosNr, $myArtikel->ArtikelNr, $newAbKorrPosten->Menge, "") ;
//					}
				}
			} else {
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "AbKorr.php::AbKorr::addPos( $_artikelNr, $_id, $_menge): end") ;
		return $this->getTablePostenAsXML() ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	delPosX( $_key, $_id, $_val) {
		$this->_buche( -1) ;
		$objName	=	$this->className . "Posten" ;
		if ( $this->LockState == 0) {
			try {
				$tmpObj	=	new $objName() ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				if ( $tmpObj->setId( $_id)) {
					$query	=	sprintf( "%s_delPos( @status, '%s', %d) ; ", $this->className, $myKey, $tmpObj->PosNr) ;
					$sqlRows	=	FDb::callProc( $query) ;
					$tmpObj->reload() ;
				} else {
					throw new Exception( 'AppObject::delPos[Id='.$_id.'] is INVALID !') ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			throw new Exception( 'AppObject::delPos: Das Objekt ist schreibgeschuetzt !') ;
		}
		$this->_buche( 1) ;
		return $this->getTablePostenAsXML() ;
	}

	/**
	 * 
	 * @param $_sign
	 */
	function	_buche( $_sign) {
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorr::_buche($_sign): begin") ;
		$actAbKorrPosten	=	new AbKorrPosten() ;
		$cond	=	"AbKorrNr = '$this->AbKorrNr' ORDER BY PosNr, SubPosNr " ;
		for ( $actAbKorrPosten->_firstFromDb( $cond) ;
				$actAbKorrPosten->isValid() ;
				$actAbKorrPosten->_nextFromDb()) {
			error_log( "AbKorr.php::AbKorr::_buche: ArtikelNr = $actAbKorrPosten->ArtikelNr") ;
			try {
				$actAbKorrPosten->_buche( $_sign) ;
			} catch( Exception $e) {
				error_log( $e->getMessage()) ;
			}
		}
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorr::_buche($_sign): end") ;
	}

	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	buche( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorr::buche( '$_key', $_id, '$_val'): begin") ;
		$this->_buche( 1) ;
		if ( $_key != "") {
			$ret	=	$this->getXMLComplete() ;
		} else {
			$ret	=	"" ;
		}
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorr::buche( '$_key', $_id, '$_val'): end") ;
		return $ret ;
	}

	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	unbuche( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorr::unbuche( '$_key', $_id, '$_val'): begin") ;
		$this->_buche( -1) ;
		if ( $_key != "") {
			$ret	=	$this->getXMLComplete() ;
		} else {
			$ret	=	"" ;
		}
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorr::unbuche( '$_key', $_id, '$_val'): end") ;
		return $ret ;
	}

		/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	function	bucheAll( $_key, $_id, $_val) {
		error_log( "AbKorr.php::bucheAll(): ") ;
		$ret	=	"" ;
		$actAbKorr	=	new AbKorr() ;
		for ( $actAbKorr->_firstFromDb( "AbKorrNr like '%' ") ;
				$actAbKorr->_valid ;
				$actAbKorr->_nextFromDb()) {
			$actAbKorr->buche() ;
		}
		if ( $_key != "") {
			$this->setAbKorrNr( $_key) ;
			$ret	=	$this->getTablePostenAsXML() ;
		} else {
			$ret	=	"" ;
		}
		return $ret ;
	}
	
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	static	function	unbucheAll( $_key, $_id, $_val) {
		$ret	=	"" ;
		$actAbKorr	=	new AbKorr() ;
		for ( $actAbKorr->_firstFromDb( "AbKorrNr like '%' ") ;
				$actAbKorr->_valid ;
				$actAbKorr->_nextFromDb()) {
			$actAbKorr->unbuche() ;
		}
		return $ret ;
	}
	
	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTablePostenAsXML() ;
		return $ret ;
	}

	function	getXMLString() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}

	function	getAbKorrPostenAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myAbKorrPosten	=	new AbKorrPosten() ;
		$myAbKorrPosten->setId( $_id) ;
		$ret	.=	$myAbKorrPosten->getXMLF() ;
		return $ret ;
	}

	function	updAbKorrPosten( $_key, $_id, $_val) {
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorr::updateAbKorrPosten( '$_key', $_id, '$_val'): begin") ;
		$this->unbuche( $_key, $_id, $_val) ;
		AppObject::updPos( $_key, $_id, $_val) ;
		$this->buche( $_key, $_id, $_val) ;
		$ret	=	$this->getTablePostenAsXML() ;
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorr::updateAbKorrPosten( '$_key', $_id, '$_val'): end") ;
		return $ret ;
	}

	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	static	function	createInventory( $_key="", $_id=0, $_val=0) {
		$out	=	new AbKorr() ;
		$out->newAbKorr() ;
		$out->Description	=	"Inventory book-out (unstock)" ;
		$out->Type	=	AbKorr::INV_OUT ;
		$out->updateInDb() ;
		$in	=	new AbKorr() ;
		$in->newAbKorr() ;
		$in->Description	=	"Inventory book-in (restock)" ;
		$in->Type	=	AbKorr::INV_IN ;
		$in->updateInDb() ;
		$stock	=	new ArtikelBestand() ;
		$myPosNr	=	10 ;
		for ( $valid = $stock->_firstFromDb( "LagerBestand <> 0 ORDER BY ArtikelNr") ;
				$valid == true ;
				$valid = $stock->_nextFromDb()) {
					echo $stock->ArtikelNr . "\n" ;
			$out->_addPos( $stock->ArtikelNr, 0, $stock->Lagerbestand * -1, $myPosNr) ;
			$myPosNr	+=	10 ;
		}
		$myPosNr	=	10 ;
		for ( $valid = $stock->_firstFromDb( "LagerBestand > 0 ORDER BY ArtikelNr") ;
				$valid == true ;
				$valid = $stock->_nextFromDb()) {
			$in->_addPos( $stock->ArtikelNr, 0, $stock->Lagerbestand, $myPosNr) ;
			$myPosNr	+=	10 ;
		}
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param string $_val	determines what to load:
	 * 						reload	get 50 records from where we are
	 * 						f50		get the first 50 records
	 * 						p50		previous 50 records
	 * 						t50		these 50 (synonym for reload)
	 * 						n50		next 50 records
	 * 						l50		last 50 records
	 */
	function	getTableAsXML( $_key="", $_id="", $_val="") {
		error_log( "AbKorr::getTableAsXML:") ;
		$this->AbKorrNr	=	$_key ;
		$ret	=	"" ;
		if ( isset( $_POST['_SStartRow'])) {
			$this->startRow	=	intval( $_POST['_SStartRow']) ;
		}
		if ( isset( $_POST['_SLang'])) {
			$this->sLang	=	$_POST['_SLang'] ;
		}
		if ( isset( $_POST['_SName'])) {
			$this->sName	=	$_POST['_SName'] ;
		}
		switch ( $_val) {
		case	"f50"	:
			$this->startRow	=	0 ;
			break ;
		case	"p50"	:
			if ( $this->startRow > 10) {
				$this->startRow	-=	10 ;
			} else {
				$this->startRow	=	0 ;
			}
			break ;
		case	"t50"	:
			break ;
		case	"n50"	:
			$this->startRow	+=	10 ;
			break ;
		case	"l50"	:
			break ;
		}
		$ret	.=	"<StartRow><![CDATA[" . $this->startRow . "]]></StartRow>" ;
		$ret	.=	"<RowCount><![CDATA[" . $tmpObj->getRowCount() . "]]></RowCount>" ;
		$order	=	"ORDER BY PosNr LIMIT " . $this->startRow . ", " . $this->rowCount . " " ;
		$this->setStartRow( 0) ;
		$this->setRowCount( 10) ;
		$ret	.=	$this->tableFromDb( "", "", "AbKorrNr = '" . $this->AbKorrNr . "' ", $order) ;
		return $ret ;
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param string $_val	determines what to load:
	 * 						reload	get 50 records from where we are
	 * 						f50		get the first 50 records
	 * 						p50		previous 50 records
	 * 						t50		these 50 (synonym for reload)
	 * 						n50		next 50 records
	 * 						l50		last 50 records
	 */
	function	getTablePostenAsXML( $_key="", $_id="", $_val="") {
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorr::getTablePostenAsXML( '$_key', $_id, '$_val'): begin") ;
		$tmpObj	=	new AbKorrPosten() ;
		$tmpObj->AbKorrNr	=	$this->AbKorrNr ;
		$ret	=	"" ;
		if ( isset( $_POST['_SStartRow'])) {
			$tmpObj->startRow	=	intval( $_POST['_SStartRow']) ;
		}
		switch ( $_val) {
		case	"f50"	:
			$tmpObj->setStartRow( 0) ;
			break ;
		case	"p50"	:
			if ( $tmpObj->getStartRow() > 10) {
				$tmpObj->setStartRow( $tmpObj->getStartRow() - 10) ;
			} else {
				$tmpObj->setStartRow( 0) ;
			}
			break ;
		case	"t50"	:
			break ;
		case	"n50"	:
			error_log( "AbKorr::getTableAsXML:case[n50]") ;
			$tmpObj->setStartRow( $tmpObj->getStartRow() + 10) ;
			break ;
		case	"l50"	:
			break ;
		}
		if ( $tmpObj->getRowCount() < 10)
			$tmpObj->setRowCount( 10) ;
		$_SESSION['Sess_AbKorrPosten_startRow']	=	$tmpObj->getStartRow() ;	
		$_SESSION['Sess_AbKorrPosten_rowCount']	=	$tmpObj->getRowCount() ;	
		FDbg::dumpL( 0x00000002, "AbKorr.php::getTablePostenAsXML: startRow := " . $tmpObj->getStartRow()) ;
		FDbg::dumpL( 0x00000002, "AbKorr.php::getTablePostenAsXML: rowCount := " . $tmpObj->getRowCount()) ;
		$ret	.=	"<StartRow><![CDATA[" . $tmpObj->getStartRow() . "]]></StartRow>" ;
		$ret	.=	"<RowCount><![CDATA[" . $tmpObj->getRowCount() . "]]></RowCount>" ;
		$tmpObj->addCol( "ERPNo", "var") ;
		$tmpObj->addCol( "ArtikelBez1", "var") ;
		$tmpObj->addCol( "ArtikelBez2", "var") ;
		$tmpObj->addCol( "MengenText", "var") ;
		$ret	.=	$tmpObj->tableFromDb( ",A.ERPNo,A.ArtikelBez1, A.ArtikelBez2, A.MengenText",
								"LEFT JOIN Artikel AS A ON A.ArtikelNr = C.ArtikelNr",
								"C.AbKorrNr = '" . $this->AbKorrNr . "' ",
								"ORDER BY C.PosNr, C.SubPosNr LIMIT " . $tmpObj->getRowCount() . " OFFSET " . $tmpObj->getStartRow()) ;
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorr::getTablePostenAsXML( '$_key', $_id, '$_val'): end") ;
		return $ret ;
	}
	/**
	 *
	 */
	function	getRType() {	return self::$rType ;	}
}
/**
 * Class AbKorrPosten - Stock corrections item data
 *
 * This class implements the individual booking records of a stock correction.
 * The referenced 'Article' is part of the 
 * 
 * Depends on:
 * <ul>
 * <li>AbKorr</li
 * </ul>
 *
 * @package Application
 * @subpackage AbKorr
 */
class	AbKorrPosten	extends	AppDepObject	{
	/**
	 * 
	 * @var unknown_type
	 */
	var	$myArtikel	=	null ;
	/*
	 * The constructor can be passed an ArticleNr (AbKorrNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_myAbKorrNr='') {
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::__construct( '$_myAbKorrNr'): begin") ;
		AppDepObject::__construct( "AbKorrPosten", "Id") ;
		if ( ! isset( $_SESSION['Sess_AbKorrPosten_startRow'])) {
			$this->setStartRow( 0) ;
			$this->setRowCount( 10) ;
			$_SESSION['Sess_AbKorrPosten_startRow']	=	$this->getStartRow() ;	
			$_SESSION['Sess_AbKorrPosten_rowCount']	=	$this->getRowCount() ;	
		} else {
			$this->setStartRow( $_SESSION['Sess_AbKorrPosten_startRow']) ;	
			$this->setRowCount( $_SESSION['Sess_AbKorrPosten_rowCount']) ;	
		}
		$this->AbKorrNr	=	$_myAbKorrNr ;
		$this->myArtikel	=	new Artikel() ;
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::__construct( '$_myAbKorrNr'): end") ;
	}
	/**
	 * reload 
	 */
	function    reload() {
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::reload(): begin") ;
		$this->fetchFromDbById() ;
		$this->myArtikel->setArtikelNr( $this->ArtikelNr) ;
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::reload(): end") ;
	}
	/**
	 * _buche
	 * 
	 * Books this stock correction in the stock
	 * @param $_sign
	 */
	function	_buche( $_sign) {
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::_buche( $_sign): begin") ;
		$this->dump() ;
		if ( $_sign == -1) {
			$menge	=	$this->MengeGebucht * $_sign ;
		} else {
			$menge	=	$this->Menge - $this->MengeGebucht ;
		}
		try {
			$actArtikel	=	new Artikel( $this->ArtikelNr) ;
			$qtyCorrected	=	$actArtikel->correct( $menge) ;
			$this->MengeGebucht	+=	$qtyCorrected ;
			$this->updateColInDb( "MengeGebucht") ;
		} catch( Exception $e) {
			throw $e ;
		}
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::_buche( $_sign): end") ;
	}
	/**
	 * buche
	 * 
	 * Books this stock correction in the stock
	 * @return 	void
	 */
	function	buche() {
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::buche(): begin") ;
		$this->_buche( 1) ;
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::buche(): end") ;
	}
	/**
	 * unbuche()
	 * 
	 * Undoes the booking of this stock correction in the stock
	 */
	function	unbuche() {
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::unbuche(): begin") ;
		$this->_buche( -1) ;
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::unbuche(): end") ;
	}
	/**
	 * getNextPosNr()
	 * 
	 * assigns the next available item no. 
	 */
	function	getNextPosNr() {
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::getNextPosNr(): begin") ;
		$query	=	sprintf( "SELECT PosNr FROM AbKorrPosten WHERE AbKorrNr='%s' ORDER BY PosNr DESC LIMIT 0, 1 ", $this->AbKorrNr) ;
		$sqlResult	=	FDb::query( $query) ;
		if ( !$sqlResult) { 
			FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::getNextPosNr(): void sqlResult") ;
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->PosNr	=	$row[0] + 10 ;
		}
		FDbg::dumpL( 0x00000100, "AbKorr.php::AbKorrPosten::getNextPosNr(): end") ;
		return $this->_status ;
	}
}
?>
