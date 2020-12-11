<?php
/**
 * DbTableObjReport.php Application Level Class for printed version of a
 * configuration report
 * 
 * Required following POST variables to be set:
 * 
 * _FMarketId		required to filter the "Market" for this price list
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDocRegReport.php") ;
/**
 * DbTableObjReport - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage DbTableObj
 */
class	DbDictionaryReport	extends BDocRegReport {
	private	$myDbTableObj ;
	/**
	 *
	 */
	function	__construct() {
		FDbg::dumpL( 0x00000002, "DbTableObjReport.php::DbTableObjReport::__construct():") ;
	}
	/**
	 * 
	 * @param $_invNo
	 */
	function	setKey( $_invNo="") {
		FDbg::dumpL( 0x00000002, "DbTableObjReport.php::DbTableObjReport::setKey( _invNo):") ;
	}
	/**
	 * 
	 */
	function	setId( $_id=-1) {
		$this->_valid	=	true ;
	}
	/**
	 * createPDF
	 * create the PDF document and returns the complete filename (path+file)
	 * hooked to hdlObjectPDF()
	 */
	function	createPDF( $_key="", $_id=-1, $_pdfName="") {
		$this->_createPDF( $_key, $_id, $_pdfName) ;
	}
	function	getPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "DbDictionary.pdf" ;
		}
		return $_pdfName ;
	}
	function	printPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "DbTableObj Report/" . $this->myDbTableObj->DbTableObjNo . ".pdf" ;
		}
		if ( $this->cuComm->autoprint) {
			$cmd	=	"lpr -P " . $this->printer->cuComm . " " . $_pdfName ;
			system( $cmd) ;
		}
		return $_pdfName ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_pdfName
	 */
	function	_createPDF( $_key="", $_id=-1, $_pdfName="") {
		$myColWidths	=	array( 40, 50, 50) ;
		/**
		 * 
		 */
		$this->cellCharFmt	=	new BCharFmt() ;
		$this->cellCharFmt->setCharSize( 8) ;

		$this->cellParaFmtLeft	=	new BParaFmt() ;
		$this->cellParaFmtLeft->setCharFmt( $this->cellCharFmt) ;
		$this->cellParaFmtLeft->setAlignment( BParaFmt::alignLeft) ;
		
		$this->cellParaFmtCenter	=	new BParaFmt() ;
		$this->cellParaFmtCenter->setCharFmt( $this->cellCharFmt) ;
		$this->cellParaFmtCenter->setAlignment( BParaFmt::alignCenter) ;

		$this->cellParaFmtRight	=	new BParaFmt() ;
		$this->cellParaFmtRight->setCharFmt( $this->cellCharFmt) ;
		$this->cellParaFmtRight->setAlignment( BParaFmt::alignRight) ;
		
		/**
		 * 
		 */
		$myTable	=	new BTable() ;
		$myTable->addCols( $myColWidths) ;

		/**
		 * setup the first table header line
		 */
		$this->psRowMain	=	new BRow( BRow::RTHeaderPS) ;
		$this->psRowMain->addCell( 0, new BCell( "Attribute", $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( "Datatype", $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( "Tables", $this->cellParaFmtLeft)) ;
		$myTable->addRow( $this->psRowMain) ;

		/**
		 * setup the first table data row
		 */
		$myRow	=	new BRow( BRow::RTDataIT) ;
		$this->cellClass	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellClass) ;
		$this->cellBlock	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellBlock) ;
		$this->cellParameter	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellParameter) ;
		$myTable->addRow( $myRow) ;
				
		/**
		 *
		 */
		BDocRegReport::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegReport) ;

		$this->tableName	=	$_POST["_IFiltTableName"] ;
		
		$this->begin() ;
		$this->addTable( $myTable) ;
		/**
		 *
		 */
		$dbDict	=	array() ;
		$tabRes	=	FDb::query( "SHOW TABLES ") ;
		while ( $row = mysql_fetch_array( $tabRes)) {
			$myTableName	=	$row[0] ;
			$result	=	FDb::query( "SHOW COLUMNS FROM " . $myTableName . " ") ;
			while ( $row = mysql_fetch_assoc( $result)) {
				$ndx	=	$row["Field"] . ":" . $row["Type"] ;
				if ( isset( $dbDict[$ndx])) {
					$dbDict[$ndx]	.=	"\n" . $myTableName ;
				} else {
					$dbDict[$ndx]	=	$myTableName ;
				}
			}
		}
		ksort( &$dbDict, SORT_STRING) ;
		foreach ( $dbDict as $ndx => $val) {
			$v	=	explode( ":", $ndx) ;
			/**
			 * set the table cell data
			 */
			$this->cellClass->setData( $v[0]) ;
			$this->cellBlock->setData( $v[1]) ;
			$this->cellParameter->setData( $val) ;
//			$this->cellClass->setData( "#-#") ;
//			$this->cellBlock->setData( "#-#") ;
//			$this->cellParameter->setData( "#-#") ;
			/**
			 * determine if we need to print this line and how we have to print it
			 * IF this is a main line, ->print it
			 * IF this is an option to an article, ->print it
			 */
			$this->punchTable() ;
		}
		/**
		 * now let's analyze for optimization
		 */
		$this->cellClass->setData( "Optimization Data") ;
		$this->cellBlock->setData( "-----") ;
		$this->cellParameter->setData( "-----") ;
		$this->punchTable() ;
		$lastAttribute	=	"" ;
		$newData	=	false ;
		$attrList	=	"" ;
		foreach ( $dbDict as $ndx => $val) {
			$v	=	explode( ":", $ndx) ;
			$attribute	=	$v[0] ;
			if ( $attribute == $lastAttribute) {
				$newData	=	true ;
				if ( strlen( $attrList) == 0) {
					$attrList	=	$attribute ;
					$typeList	=	$lastType . "\n-----\n" . $v[1] ;
					$dbList	=	$lastDb . "\n-----\n" . $val ;
				} else {
					$typeList	.=	"\n-----\n" . $v[1] ;
					$dbList	.=	"\n-----\n" . $val ;
				}
			} else {
				if ( $newData) {
					$this->cellClass->setData( $attrList) ;
					$this->cellBlock->setData( $typeList) ;
					$this->cellParameter->setData( $dbList) ;
					$this->punchTable() ;
					$newData	=	false ;
					$attrList	=	"" ;
					$typeList	=	"" ;
					$dbList	=	"" ;
				}				
			}
			$lastAttribute	=	$attribute ;
			$lastType	=	$v[1] ;
			$lastDb	=	$val ;
		}
		
		/**
		 * now we can complete the setup the teble-end row
		 */
		$this->endTable() ;

		//
		$myPostfix	=	"" ;
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPostfix)) ;
		
		if ( $_pdfName == '') {
			$pdfName	=	$this->path->Archive . "DbDictionaryReport.pdf" ;
		} else {
		}
		$this->end( $pdfName) ;
	}
	/**
	 * setupHeaderFirst
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderFirst( $_frm) {

		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "DbTable Report")), $this->defParaFmt) ;
		$_frm->addLine( "Name of Database Table: " . $this->tableName, $this->defParaFmt) ;
		/**
		 * draw the separating line between the header and the document content
		 */
		$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs + $_frm->height + mmToPt( 1.0),
					$_frm->horOffs + $_frm->width, $_frm->verOffs + $_frm->height + mmToPt( 1.0)) ;
	}
	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "DbTable Report")), $this->defParaFmt) ;
		$_frm->addLine( "Name of Database Table: " . $this->tableName, $this->defParaFmt) ;
		/**
		 * draw the separating line between the header and the document content
		 */
		$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs + $_frm->height + mmToPt( 1.0),
					$_frm->horOffs + $_frm->width, $_frm->verOffs + $_frm->height + mmToPt( 1.0)) ;
	}
	/**
	 *
	 */
	function	cascTokenStart( $_token) {
	}
	/**
	 *
	 */
	function	cascTokenEnd( $_token) {
	}
}
?>
