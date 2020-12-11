<?php
/**
 * BDocument.php Base class for PDF-format printed matters
 *
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * ArticlePurchasePrice - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArticlePurchasePrice which should
 * not be modified.
 *
 * @package Application
 * @subpackage Article
 */

class	ArticlePurchasePrice	extends	Price	{

	/*
	 * The constructor can be passed an ArticleNr (ArticlePurchasePriceNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		FDbg::begin( 1, "ArticlePurchasePrice.php", "ArticlePurchasePrice{Price}", "__construct( $_id)") ;
		parent::__construct( "ArticlePurchasePrice", "Id") ;
		if ( $_id >= 0) {
			$this->Id	=	$_id ;
			$this->fetchFromDbById() ;
		} else {
		}
		FDbg::end() ;
	}
	/**
	 *
	 * @param unknown_type $_name
	 * @param unknown_type $_refNr
	 * @param unknown_type $_sprache
	 */
	function	getMostActual( $_liefNr, $_liefArtNr) {
		try {
			$cond	=	"WHERE LiefNr = '$_liefNr' AND LiefArtNr = '$_liefArtNr' "
					.	"AND GueltigVon <= '".$this->today()."' "
					.	"AND '".$this->today()."' < GueltigBis " ;
			$this->fetchFromDbWhere( $cond) ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->_valid ;
	}
	/**
	 *
	 * @param $_a_markup
	 * @param $_autoPriceSrc
	 */
	function	_calcOwnVK() {
		$mySupp	=	new Lief() ;
		$mySupp->setLiefNr( $this->LiefNr) ;
		if ( ! $mySupp->_valid) {
			$e	=	new Exception( "Artikel.php::Artikel::_calcOwnVK( '$_key', $_id, '$_val'): could not fetch Supplier by 'Supplier no=$mySupp->LiefNr'") ;
			error_log( $e) ;
			throw $e ;
		}
		/**
		 *
		 */
		$myEKPreisR	=	new EKPreisR() ;
		$myEKPreisR->fetchFromDbWhere( "WHERE LiefNr = '$this->LiefNr' AND LiefArtNr = '$this->LiefArtNr' ") ;
		if ( $myEKPreisR->_valid) {
			/**
			 *
			 */
			$myCurrency	=	new Currency() ;
			$myCurrency->fetchFromDbWhere( "WHERE VonWaehrung = '$this->Waehrung' AND NachWaehrung = '".$this->erp->currency."' AND CGueltigVon <= curdate() AND curdate() <= CGueltigBis ") ;
			if ( ! $myCurrency->_valid) {
				$e	=	new Exception( "Artikel.php::Artikel::_calcOwnVK(...): could not fetch currency conversion $this->Waehrung -> ".$this->erp->currency." ") ;
				error_log( $e) ;
				throw $e ;
			}
			/**
			 *
			 */
			$myArtikel	=	new Artikel( $myEKPreisR->ArtikelNr) ;
			if ( ! $myArtikel->_valid) {
				$e	=	new Exception( "ArticlePurchasePrice.php::ArticlePurchasePrice::_calcOwnVK( $_a_markup): could not load article no. '$myEKPreisR->ArtikelNr'") ;
				error_log( $e) ;
				throw $e ;
			}
			$this->ArticleMarkup	=	$myArtikel->Marge ;
			$this->autoPriceSrc	=	$myArtikel->AutoPriceSrc ;
			/**
			 * determine SalesPrice ($mySP)
			 */
			$mySP	=	$this->Preis * $myArtikel->Marge * $mySupp->Marge ;
			if ( $mySP > 100.00) {
				$this->OwnVKPreis	=	round( $mySP, 0) ;
			} else if ( $mySP > 10.00) {
				$this->OwnVKPreis	=	round( $mySP, 1) ;
			} else {
				$this->OwnVKPreis	=	$mySP ;
			}
		} else {
			$e	=	new Exception( "Artikel.php::Artikel::_calcOwnVK(...): could not fetch purchase price relation (EKPreisR), LiefNr='$this->LiefNr', SuppArtNo='$this->LiefArtNr' ") ;
			error_log( $e) ;
//			throw $e ;
			$this->OwnVKPreis	=	0.0 ;
			$this->OwnRabatt	=	0.0 ;
		}
		$this->updateColInDb( "OwnVKPreis") ;
		$this->updateColInDb( "OwnRabatt") ;
	}
	/**
	 * calcEKSumKomp()
	 *
	 * Berechnet die Summe der Einkaufspreise aller Artikelkomponeneten fuer 1 Stueck dieses Artikels.
	 *
	 * @return void
	 */
	function	calcEKSumKomp() {
		FDbg::dumpL( 0x01000000, "ArticlePurchasePrice::calcEKSum()") ;
		$actEKPreisR	=	new EKPreisR() ;
		$actEKPreisR->fetchFromDbWhere( "WHERE LiefNr = '" . $this->LiefNr . "' AND LiefArtNr = '" . $this->LiefArtNr . "' ") ;
		if ( $actEKPreisR->_valid == 1) {
			FDbg::dumpL( 0x02000000, "ArticlePurchasePrice::calcEKSum()") ;
			$actArtikel	=	new Artikel( $actEKPreisR->ArtikelNr) ;
			if ( $actArtikel->_valid == 1) {
				FDbg::dumpL( 0x04000000, "ArticlePurchasePrice::calcEKSum()") ;
				if ( $actArtikel->Comp == Artikel::COMPGROUP) {
					FDbg::dumpL( 0x08000000, "ArticlePurchasePrice::calcEKSum()") ;
					$subArtikel	=	new Artikel() ;
					$subEKPreisR	=	new EKPreisR() ;
					$subArticlePurchasePrice	=	new ArticlePurchasePrice() ;
					$comp	=	new ArtKomp() ;

					/**
					 * hole die erste Komponente
					 */
					$comp->firstFromDb( "ArtikelNr = '" . $actArtikel->ArtikelNr . "' ORDER BY PosNr ") ;
					$sumEK	=	0.0 ;
					$sumLiefVK	=	0.0 ;
					while ( $comp->_valid == 1) {
						FDbg::dumpL( 0x10000000, "ArticlePurchasePrice::calcEKSum(): comp->CompArtikelNr='%s' ", $comp->CompArtikelNr) ;
						$subArtikel->setArtikelNr( $comp->CompArtikelNr) ;

						/**
						 * hole die EKPreisR f�r diesen Artikel, und zwar die Kalkulationsbasis
						 */
						$subEKPreisR->fetchFromDbWhere( "WHERE ArtikelNr = '" . $subArtikel->ArtikelNr . "' AND KalkBasis > 0 ") ;
						if ( $subArtikel->_valid == 1 && $subEKPreisR->_valid == 1) {
							FDbg::dumpL( 0x20000000, "ArticlePurchasePrice::calcEKSum(): subEKPreisR->LiefNr='%s', ->LiefArtNr='%s' ",
															$subEKPreisR->LiefNr, $subEKPreisR->LiefArtNr) ;

							/**
							 *
							 */
							$subArticlePurchasePrice->LiefNr	=	$subEKPreisR->LiefNr ;
							$subArticlePurchasePrice->LiefArtNr	=	$subEKPreisR->LiefArtNr ;
							$subArticlePurchasePrice->firstFromDb() ;
							if ( $subArticlePurchasePrice->_valid == 1) {
								FDbg::dumpL( 0x40000000, "ArticlePurchasePrice::calcEKSum(): ArticlePurchasePrice->LiefNr='%s'", $subArticlePurchasePrice->LiefNr) ;
								FDbg::dumpL( 0x40000000, "ArticlePurchasePrice::calcEKSum(): ArticlePurchasePrice->LiefArtNr='%s'", $subArticlePurchasePrice->LiefArtNr) ;
								FDbg::dumpL( 0x40000000, "Adding Article: " . $subArtikel->ArtikelNr .
															" at " . $subArticlePurchasePrice->Preis / $subEKPreisR->MKF / $subArticlePurchasePrice->MengeFuerPreis .
															" Euro / piece") ;
								$myPosEKPreis	=	intval( $comp->CompMenge) * $subArticlePurchasePrice->Preis / $subEKPreisR->MKF / $subArticlePurchasePrice->MengeFuerPreis ;
								$myPosLiefVKPreis	=	intval( $comp->CompMenge) * $subArticlePurchasePrice->LiefVKPreis / $subEKPreisR->MKF / $subArticlePurchasePrice->MengeFuerPreis ;
							} else {
								throw new Exception( "ArticlePurchasePrice::calcEKSumKomp: ungueltige Artikelkomponente, ArtikelNr='".$subArtikel->ArtikelNr."' ") ;
							}
						} else {
							throw new Exception( "ArticlePurchasePrice::calcEKSumKomp: ungueltige Artikelkomponente, ArtikelNr='".$subArtikel->ArtikelNr."' ") ;
						}
						$sumEK	+=	$myPosEKPreis ;
						$sumLiefVK	+=	$myPosLiefVKPreis ;
						$comp->nextFromDb() ;
					}
				} else {
				}
			}
			$this->Preis	=	$sumEK ;
			$this->LiefVKPreis	=	$sumLiefVK ;
			$this->updateInDb() ;
		} else {
			throw new Exception( "ArticlePurchasePrice::calcEKSumKomp: keine gueltige EKPreisR fuer diesen Artikel") ;
		}
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="") {
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "ArticlePurchasePrice.php", "ArticlePurchasePrice", "getDepAsXML( '$_key', $_id, '$_val', <reply>)") ;
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
	 * @param $_vp
	 * @param $_hr
	 */
	function	calcEK( $_vp, $_hr) {
		return $_vp * ( 100.0 - $_hr ) / 100.0 ;
	}
	/**
	 * calcVP
	 * return the sales price for the given purchasing price and purchasing discount
	 * @param float $_ek
	 * @param float $_hr
	 */
	function	calcVP( $_ek, $_hr) {
		return ( $_ek / ( 1 - $_hr / 100.0 )) ;
	}
}

?>
