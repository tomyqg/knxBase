<?php
/**
 * DataMinesMisc.php - Class to gather data related to Miascellaneous objects
 * 
 * currently this class can handle various dM requests for the following obejcts:<br/>
 * <ul>
 * <li>Texte</li>
 * <li>SysTexte</li>
 * </ul>
 * 
 * In order to allow for paged requests this class maintains various SESSION variables. It is important that
 * start_session has been called before any method of this class is called.
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
 * DataMinerMisc - User-Level Klasse
 *
 * This class acts as an interface for data-mining in general.
 * The following core methods are provided.
 *
 *
 * @package Application
 * @subpackage DataMiner
 */
class	DataMinerMisc	extends	DataMiner	{
	
	public	$objName ;

	/**
	 * __construct
	 * 
	 * Creates a dataminer object
	 *
	 * @return void
	 */
	function	__construct( $_objName="", $_id="", $_val="") {
		DataMiner::__construct( $_objName) ;
		return $this->valid ;
	}

	function	setKey( $_key, $_val="", $_id="") {
		$this->objName	=	$_key ;
		return $this->valid ;
	}

	/**
	 * getTableCuCommForCuOrdr
	 *
	 * returns the table of all customer invoices
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableDocument( $_key="", $_id="", $_val="") {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "RefType", "var") ;
		$myObj->addCol( "RefNr", "var") ;
		$myObj->addCol( "Filename", "var") ;
		$ret	.=	"<StartRow><![CDATA[" . $this->startRow . "]]></StartRow>" ;
		$ret	.=	"<RowCount><![CDATA[" . $this->rowCount . "]]></RowCount>" ;
		$ret	.=	$myObj->tableFromDb( " ",
								" ",
								"1 ",
								"ORDER BY C.RefType, C.RefNr ASC LIMIT ". $this->startRow . ", " . $this->rowCount . " ",
								"ResultSet",
								"Document",
								"C.Id, C.RefType, C.RefNr, C.Filename") ;
		error_log( $ret) ;
		return $ret ;
	}

	function	getTableTexte( $_key="", $_id="", $_val="") {
		$_POST['_step']	=	$_key ;
		if ( isset( $_POST['_SLang'])) {
			$this->sLang	=	$_POST['_SLang'] ;
			$_SESSION['Sess_Texte_sLang']	=	$this->sLang ;
		} else if ( isset( $_SESSION['Sess_Texte_sLang'])) {
			$this->sLang	=	$_SESSION['Sess_Texte_sLang'] ;
		}
		if ( isset( $_POST['_SName'])) {
			$this->sName	=	$_POST['_SName'] ;
			$_SESSION['Sess_Texte_sName']	=	$this->sName ;
		} else if ( isset( $_SESSION['Sess_Texte_sName'])) {
			$this->sName	=	$_SESSION['Sess_Texte_sName'] ;
		}
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "RefNr", "var") ;
		$myObj->addCol( "Sprache", "var") ;
		$myObj->addCol( "Volltext", "var") ;
		$ret	.=	$myObj->tableFromDb( " ",
								" ",
								"Name LIKE '%" . $this->sName . "%' AND Sprache = '" . $this->sLang . "' ",
								"ORDER BY C.Name, C.RefNr ASC ",
								"ResultSet",
								"Texte",
								"C.Id, C.Name, C.RefNr, C.Sprache, C.Volltext") ;
		$_SESSION['Sess_Texte_sLang']	=	$this->sLang ;
		$_SESSION['Sess_Texte_sName']	=	$this->sName ;
		return $ret ;
	}
	/**
	 * getTableSysTexte
	 *
	 * returns the table of all customer invoices
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableSysTexte( $_key="", $_id="", $_val="") {
		$_POST['_step']	=	$_key ;
		if ( isset( $_POST['_SLang'])) {
			$this->sLang	=	$_POST['_SLang'] ;
			$_SESSION['Sess_SysTexte_sLang']	=	$this->sLang ;
		} else if ( isset( $_SESSION['Sess_SysTexte_sLang'])) {
			$this->sLang	=	$_SESSION['Sess_SysTexte_sLang'] ;
		}
		if ( isset( $_POST['_SName'])) {
			$this->sName	=	$_POST['_SName'] ;
			$_SESSION['Sess_SysTexte_sName']	=	$this->sName ;
		} else if ( isset( $_SESSION['Sess_SysTexte_sName'])) {
			$this->sName	=	$_SESSION['Sess_SysTexte_sName'] ;
		}
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "RefNr", "var") ;
		$myObj->addCol( "Sprache", "var") ;
		$myObj->addCol( "Volltext", "var") ;
		$ret	.=	$myObj->tableFromDb( " ",
								" ",
								"Name LIKE '%" . $this->sName . "%' AND Sprache = '" . $this->sLang . "' ",
								"ORDER BY C.Name, C.RefNr ASC ",
								"ResultSet",
								"SysTexte",
								"C.Id, C.Name, C.RefNr, C.Sprache, SUBSTR( C.Volltext, 1, 50) AS Volltext") ;
		$_SESSION['Sess_Texte_sLang']	=	$this->sLang ;
		$_SESSION['Sess_Texte_sName']	=	$this->sName ;
		return $ret ;
	}

}

?>
