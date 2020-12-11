<?php
/**
 * DataMinesArtikel.php - Class to gather data related to an Article
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * requires mostly platform stuff
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "DataMiner.php" );
/**
 * DataMinerArtikel - DataMiner for drilling into Artikel-related data.
 *
 * Methods:<br/>
 * <ul>
 * <li>getTableSuOrdrForArtikel - return a table containing all supplier orders containing this Artikel</li>
 * <li>getTableSuDlvrForArtikel - return a table containing all supplier goods-receivable containing this Artikel</li>
 * <li>getTableCuOrdrForArtikel - return a table containing all customer orders containing this Artikel</li>
 * <li>getTableCuCommForArtikel - return a table containing all customer commissions containing this Artikel</li>
 * <li>getTableCuDlvrForArtikel - return a table containing all customer deliveries containing this Artikel</li>
 * <li>getTableCuInvcForArtikel - return a table containing all customer invoices containing this Artikel</li>
 * <li>getTableArticleUnreserved - return a table containing all customer order lines where the Artikel is not correctly reserved</li>
 * <li>getTableArticleToOrder - return a table containing all Articles which need to be ordered</li>
 * <li>getTableArticlePricing - return a table containing all Articles which have a sales price which is lower than the purchasing price</li>
 * </ul>
 *
 * @package Application
 * @subpackage DataMiner
 */
class	DataMinerArtikel	extends	DataMiner	{
	/**
	 * __construct
	 * 
	 * Creates an instance of a dataminer for an object of class <$_objName>.
	 *
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 */
	function	__construct( $_key="", $_id="", $_val="") {
		parent::__construct() ;
		return $this->valid ;
	}
	/**
	 * getTableSuOrdrForArtikel
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableSuOrdrForArtikel( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "SuOrdrNo", "var") ;
		$myObj->addCol( "LiefNr", "var") ;
		$myObj->addCol( "LiefKontaktNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "FirmaName2", "var") ;
		$myObj->addCol( "Vorname", "var") ;
		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "PosNr", "int") ;
		$myObj->addCol( "Menge", "int") ;
		$myObj->addCol( "Preis", "dou") ;
		$ret	=	$myObj->tableFromDb( ",LfB.SuOrdrNo, LfB.LiefNr, LfB.LiefKontaktNr, L.FirmaName1, L.FirmaName2, LK.Vorname, LK.Name ",
								"LEFT JOIN SuOrdr AS LfB ON LfB.SuOrdrNo = C.SuOrdrNo LEFT JOIN Lief AS L on L.LiefNr = LfB.LiefNr LEFT JOIN LiefKontakt AS LK on LK.LiefNr = LfB.LiefNr AND LK.LiefKontaktNr = LfB.LiefKontaktNr ",
								"C.ArtikelNr = '" . $this->key . "' ",
								"ORDER BY C.SuOrdrNo ",
								"ResultSet",
								"SuOrdrItem",
								"C.PosNr,C.Menge,C.Preis") ;
		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * getTableSuDlvrForArtikel
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableSuDlvrForArticle( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "SuDlvrNo", "var") ;
		$myObj->addCol( "LiefNr", "var") ;
		$myObj->addCol( "LiefKontaktNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "FirmaName2", "var") ;
		$myObj->addCol( "Vorname", "var") ;
		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "PosNr", "int") ;
		$myObj->addCol( "Menge", "int") ;
		$myObj->addCol( "MengeEmpfangen", "int") ;
		$myObj->addCol( "Preis", "dou") ;
		$ret	=	$myObj->tableFromDb( ",LfL.SuDlvrNo, LfL.LiefNr, LfL.LiefKontaktNr, L.FirmaName1, L.FirmaName2, LK.Vorname, LK.Name ",
												"LEFT JOIN SuDlvr AS LfL ON LfL.SuDlvrNo = C.SuDlvrNo " .
												"LEFT JOIN Lief AS L on L.LiefNr = LfL.LiefNr " .
												"LEFT JOIN LiefKontakt AS LK on LK.LiefNr = LfL.LiefNr AND LK.LiefKontaktNr = LfL.LiefKontaktNr ",
								"C.ArtikelNr = '" . $this->key . "' ",
								"ORDER BY C.SuDlvrNo ",
								"ResultSet",
								"SuDlvrItem",
								"C.PosNr,C.Menge,C.MengeEmpfangen,C.Preis") ;
		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * getTableCuCartForArtikel
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableCuCartForArtikel( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuCartNo", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "FirmaName2", "var") ;
		$myObj->addCol( "Vorname", "var") ;
		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "PosNr", "var") ;
		$myObj->addCol( "SubPosNr", "var") ;
		$myObj->addCol( "Menge", "int") ;
		$myObj->addCol( "MengeProVPE", "int") ;
		$myObj->addCol( "Preis", "dou") ;
		$myObj->addCol( "RefPreis", "dou") ;
		$ret	=	$myObj->tableFromDb( ",KdA.CuCartNo, KdA.KundeNr, KdA.KundeKontaktNr, K.FirmaName1, K.FirmaName2, KK.Vorname, KK.Name ",
								"LEFT JOIN CuCart AS KdA ON KdA.CuCartNo = C.CuCartNo LEFT JOIN Kunde AS K on K.KundeNr = KdA.KundeNr LEFT JOIN KundeKontakt AS KK on KK.KundeNr = KdA.KundeNr AND KK.KundeKontaktNr = KdA.KundeKontaktNr ",
								"C.ArtikelNr = '" . $this->key . "' ",
								"ORDER BY C.CuCartNo ",
								"ResultSet",
								"CuCartItem",
								"C.CuCartNo, C.PosNr, C.SubPosNr, C.Menge, C.MengeProVPE, C.Preis, C.RefPreis ") ;
		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * getTableCuRFQForArtikel
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableCuRFQForArtikel( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuRFQNo", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "FirmaName2", "var") ;
		$myObj->addCol( "Vorname", "var") ;
		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "PosNr", "var") ;
		$myObj->addCol( "SubPosNr", "var") ;
		$myObj->addCol( "Menge", "int") ;
		$myObj->addCol( "MengeProVPE", "int") ;
		$myObj->addCol( "Preis", "dou") ;
		$myObj->addCol( "RefPreis", "dou") ;
		$ret	=	$myObj->tableFromDb( ",KdA.CuRFQNo, KdA.KundeNr, KdA.KundeKontaktNr, K.FirmaName1, K.FirmaName2, KK.Vorname, KK.Name ",
								"LEFT JOIN CuRFQ AS KdA ON KdA.CuRFQNo = C.CuRFQNo LEFT JOIN Kunde AS K on K.KundeNr = KdA.KundeNr LEFT JOIN KundeKontakt AS KK on KK.KundeNr = KdA.KundeNr AND KK.KundeKontaktNr = KdA.KundeKontaktNr ",
								"C.ArtikelNr = '" . $this->key . "' ",
								"ORDER BY C.CuRFQNo ",
								"ResultSet",
								"CuRFQItem",
								"C.CuRFQNo, C.PosNr, C.SubPosNr, C.Menge, C.MengeProVPE, C.Preis, C.RefPreis ") ;
		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * getTableCuOffrForArtikel
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableCuOffrForArtikel( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuOffrNo", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "FirmaName2", "var") ;
		$myObj->addCol( "Vorname", "var") ;
		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "PosNr", "var") ;
		$myObj->addCol( "SubPosNr", "var") ;
		$myObj->addCol( "Menge", "int") ;
		$myObj->addCol( "MengeProVPE", "int") ;
		$myObj->addCol( "Preis", "dou") ;
		$myObj->addCol( "RefPreis", "dou") ;
		$ret	=	$myObj->tableFromDb( ",KdA.CuOffrNo, KdA.KundeNr, KdA.KundeKontaktNr, K.FirmaName1, K.FirmaName2, KK.Vorname, KK.Name ",
								"LEFT JOIN CuOffr AS KdA ON KdA.CuOffrNo = C.CuOffrNo LEFT JOIN Kunde AS K on K.KundeNr = KdA.KundeNr LEFT JOIN KundeKontakt AS KK on KK.KundeNr = KdA.KundeNr AND KK.KundeKontaktNr = KdA.KundeKontaktNr ",
								"C.ArtikelNr = '" . $_key . "' ",
								"ORDER BY C.CuOffrNo ",
								"ResultSet",
								"CuOffrItem",
								"C.CuOffrNo, C.ItemNo, C.SubItemNo, C.Menge, C.MengeProVPE, C.Preis, C.RefPreis ") ;
		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * getTableCuOrdrForArtikel
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableCuOrdrForArtikel( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuOrdrNo", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "FirmaName2", "var") ;
//		$myObj->addCol( "Vorname", "var") ;
//		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "ItemNo", "var") ;
		$myObj->addCol( "SubItemNo", "var") ;
		$myObj->addCol( "Menge", "int") ;
		$myObj->addCol( "MengeProVPE", "int") ;
//		$myObj->addCol( "MengeReserviert", "int") ;
		$myObj->addCol( "Preis", "dou") ;
		$myObj->addCol( "RefPreis", "dou") ;
		$ret	=	$myObj->tableFromDb( ",KdB.CuOrdrNo, KdB.KundeNr, KdB.KundeKontaktNr, K.FirmaName1, K.FirmaName2, KK.Vorname, KK.Name ",
								"LEFT JOIN CuOrdr AS KdB ON KdB.CuOrdrNo = C.CuOrdrNo LEFT JOIN Kunde AS K on K.KundeNr = KdB.KundeNr LEFT JOIN KundeKontakt AS KK on KK.KundeNr = KdB.KundeNr AND KK.KundeKontaktNr = KdB.KundeKontaktNr ",
								"C.ArtikelNr = '" . $this->key . "' ",
								"ORDER BY C.CuOrdrNo ",
								"ResultSet",
								"CuOrdrItem",
								"C.CuOrdrNo, C.ItemNo, C.SubItemNo, C.Menge, C.MengeProVPE, C.Preis, C.RefPreis ") ;
		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * getTableCuCommForArtikel
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableCuCommForArtikel( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuCommNo", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "FirmaName2", "var") ;
		$myObj->addCol( "ItemNo", "var") ;
		$myObj->addCol( "SubItemNo", "var") ;
		$myObj->addCol( "Menge", "int") ;
		$myObj->addCol( "MengeProVPE", "int") ;
		$ret	=	$myObj->tableFromDb( ",KdB.CuCommNo, KdB.KundeNr, KdB.KundeKontaktNr, K.FirmaName1, K.FirmaName2, KK.Vorname, KK.Name ",
								"LEFT JOIN CuComm AS KdB ON KdB.CuCommNo = C.CuCommNo LEFT JOIN Kunde AS K on K.KundeNr = KdB.KundeNr LEFT JOIN KundeKontakt AS KK on KK.KundeNr = KdB.KundeNr AND KK.KundeKontaktNr = KdB.KundeKontaktNr ",
								"C.ArtikelNr = '" . $this->key . "' ",
								"ORDER BY C.CuCommNo ",
								"ResultSet",
								"CuCommItem",
								"C.CuCommNo, C.ItemNo, C.SubItemNo, C.Menge, C.MengeProVPE, C.Preis,C.RefPreis") ;
		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * getTableCuDlvrForArtikel
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableCuDlvrForArtikel( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuDlvrNo", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "FirmaName2", "var") ;
		$myObj->addCol( "ItemNo", "var") ;
		$myObj->addCol( "SubItemNo", "var") ;
		$myObj->addCol( "Menge", "int") ;
		$myObj->addCol( "MengeGeliefert", "int") ;
		$myObj->addCol( "MengeProVPE", "int") ;
		$ret	=	$myObj->tableFromDb( ",KdB.CuDlvrNo, KdB.KundeNr, KdB.KundeKontaktNr, K.FirmaName1, K.FirmaName2, KK.Vorname, KK.Name ",
								"LEFT JOIN CuDlvr AS KdB ON KdB.CuDlvrNo = C.CuDlvrNo LEFT JOIN Kunde AS K on K.KundeNr = KdB.KundeNr LEFT JOIN KundeKontakt AS KK on KK.KundeNr = KdB.KundeNr AND KK.KundeKontaktNr = KdB.KundeKontaktNr ",
								"C.ArtikelNr = '" . $this->key . "' ",
								"ORDER BY C.CuDlvrNo ",
								"ResultSet",
								"CuDlvrItem",
								"C.CuDlvrNo, C.ItemNo, C.SubItemno, C.Menge, C.MengeGeliefert, C.MengeProVPE, C.MengeGebucht, C.Preis, C.RefPreis") ;
		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * getTableCuInvcForArtikel
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableCuInvcForArtikel( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuInvcNo", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "FirmaName2", "var") ;
		$myObj->addCol( "ItemNo", "var") ;
		$myObj->addCol( "SubItemNo", "var") ;
		$myObj->addCol( "Menge", "int") ;
		$ret	=	$myObj->tableFromDb( ",KdB.CuInvcNo, KdB.KundeNr, KdB.KundeKontaktNr, K.FirmaName1, K.FirmaName2, KK.Vorname, KK.Name ",
								"LEFT JOIN CuInvc AS KdB ON KdB.CuInvcNo = C.CuInvcNo LEFT JOIN Kunde AS K on K.KundeNr = KdB.KundeNr LEFT JOIN KundeKontakt AS KK on KK.KundeNr = KdB.KundeNr AND KK.KundeKontaktNr = KdB.KundeKontaktNr ",
								"C.ArtikelNr = '" . $this->key . "' ",
								"ORDER BY C.CuInvcNo ",
								"ResultSet",
								"CuInvcItem",
								"C.CuInvcNo, C.ItemNo, C.SubItemNo, C.Menge, C.Preis, C.RefPreis") ;
		error_log( $ret) ;
		return $ret ;
	}
	function	getTableAbKorrForArtikel( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "AbKorrNr", "var") ;
		$myObj->addCol( "PosNr", "var") ;
		$myObj->addCol( "SubPosNr", "int") ;
		$myObj->addCol( "Menge", "int") ;
		$myObj->addCol( "MengeProVPE", "int") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.ArtikelNr = '" . $this->key . "' ",
								"ORDER BY C.AbKorrNr ",
								"ResultSet",
								"AbKorrPosten",
								"C.AbKorrNr, C.PosNr, C.SubPosNr, C.Menge, C.MengeProVPE ") ;
		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * getTableArticleUnreserved
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableArticleUnreserved( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuOrdrNo", "varchar") ;
		$myObj->addCol( "ArtikelNr", "varchar") ;
		$myObj->addCol( "ArtikelBez1", "varchar") ;
		$myObj->addCol( "ArtikelBez2", "varchar") ;
		$myObj->addCol( "Menge", "varchar") ;
		$myObj->addCol( "MengeReserviert", "int") ;
		$ret	.=	$myObj->tableFromDb( ", A.ArtikelBez1 AS ArtikelBez1, A.ArtikelBez2 AS ArtikelBez2, A.MengenText AS MengenText ",
										"LEFT JOIN Artikel AS A ON A.ArtikelNr = C.ArtikelNr LEFT JOIN CuOrdr AS KdB ON KdB.CuOrdrNo = C.CuOrdrNo ", 
										"C.MengeReserviert < C.Menge AND KdB.Status < " . CuOrdr::CLOSED . " ",
										"ORDER BY C.CuOrdrNo ASC",
										"ResultSet",
										"CuOrdrItem",
										"C.CuOrdrNo, C.ArtikelNr,C.Menge,C.MengeReserviert"
					) ;
		return $ret ;
	}
	/**
	 * getTableArticleToOrder
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableArticleToOrder( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "ArtikelNr", "varchar") ;
		$myObj->addCol( "ArtikelBez1", "varchar") ;
		$myObj->addCol( "ArtikelBez2", "varchar") ;
		$myObj->addCol( "Lagerbestand", "int") ;
		$myObj->addCol( "Reserviert", "int") ;
		$myObj->addCol( "Bestellt", "int") ;
		$ret	.=	$myObj->tableFromDb( ", A.ArtikelBez1 AS ArtikelBez1, A.ArtikelBez2 AS ArtikelBez2, A.MengenText AS MengenText ",
										"LEFT JOIN Artikel AS A ON A.ArtikelNr = C.ArtikelNr ", 
										"C.Reserviert > C.Lagerbestand + C.Bestellt ",
										"ORDER BY C.ArtikelNr",
										"ResultSet",
										"ArtikelBestand",
										"C.ArtikelNr,C.Lagerbestand,C.Reserviert,C.Bestellt"
					) ;
		return $ret ;
	}
	/**
	 * getTableArticlePricing
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableArticlePricing( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "ArtikelNr", "varchar") ;
		$myObj->addCol( "ArtikelBez1", "varchar") ;
		$myObj->addCol( "AutoPreis", "varchar") ;
		$myObj->addCol( "EKPreis", "varchar") ;
		$myObj->addCol( "VKPreis", "int") ;
		$ret	.=	$myObj->tableFromDb( ",AEP.Preis AS EKPreis,VKP.Preis AS VKPreis ",
										"LEFT JOIN EKPreisR AS EKPR ON EKPR.ArtikelNr = C.ArtikelNr AND KalkBasis > 0 "
											. "LEFT JOIN ArtikelEKPreis AS AEP ON AEP.LiefNr = EKPR.LiefNr AND AEP.LiefArtNr  = EKPR.LiefArtNr AND AEP.Menge = EKPR.KalkBasis "
											. "LEFT JOIN VKPreisCache AS VKP ON VKP.ArtikelNr = C.ArtikelNr ",
										"VKP.Preis <= (( AEP.Preis / AEP.MengeFuerPreis ) / EKPR.MKF ) * VKP.MengeProVPE ",
										"ORDER BY C.ArtikelNr LIMIT 100 ",
										"ResultSet",
										"Artikel",
										"C.ArtikelNr,C.ArtikelBez1,C.AutoPreis"
					) ;
		return $ret ;
	}	
	/**
	 * getTableArticleReplaced
	 * 
	 * @param	string	$_key	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	int		$_id	Not used. Needed in order to conform to the EISS calling standard.
	 * @param	mixed	$_val	Not used. Needed in order to conform to the EISS calling standard.
	 * @return	string			XML document containing the result 
	 */
	function	getTableArticleReplaced( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "ArtikelNr", "varchar") ;
		$myObj->addCol( "ArtikelBez1", "varchar") ;
		$myObj->addCol( "ArtikelNrNeu", "varchar") ;
		$myObj->addCol( "ArtikelBez1Neu", "varchar") ;
		$myObj->addCol( "Lagerbestand", "int") ;
		$ret	.=	$myObj->tableFromDb( ", AN.ArtikelBez1 AS ArtikelBez1Neu, AB.Lagerbestand ",
										"LEFT JOIN Artikel AS AN ON AN.ArtikelNr = C.ArtikelNrNeu " .
											"LEFT JOIN ArtikelBestand AS AB ON AB.ArtikelNr = C.ArtikelNr ",
										"C.ArtikelNrNeu != '' ",
										"ORDER BY C.ArtikelNr LIMIT 100 ",
										"ResultSet",
										"Artikel",
										"C.ArtikelNr,C.ArtikelBez1,C.ArtikelNrNeu"
					) ;
		return $ret ;
	}	
}
?>
