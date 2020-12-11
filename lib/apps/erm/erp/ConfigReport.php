<?php
/**
 * ConfigObjReport.php Application Level Class for printed version of a
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
 * ConfigObjReport - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage ConfigObj
 */
class	ConfigReport	extends BDocRegReport {
	private	$myConfigObj ;
	/**
	 *
	 */
	function	__construct() {
		FDbg::dumpL( 0x00000002, "ConfigObjReport.php::ConfigObjReport::__construct():") ;
	}
	/**
	 * 
	 * @param $_invNo
	 */
	function	setKey( $_invNo="") {
		FDbg::dumpL( 0x00000002, "ConfigObjReport.php::ConfigObjReport::setKey( _invNo):") ;
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
			$_pdfName	=	$this->path->Archive . "ConfigObj Report/" . $this->myConfigObj->ConfigObjNo . ".pdf" ;
		}
		return $_pdfName ;
	}
	function	printPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "ConfigObj Report/" . $this->myConfigObj->ConfigObjNo . ".pdf" ;
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
		$myColWidths	=	array( 30, 30, 40, 80) ;
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
		$this->psRowMain->addCell( 0, new BCell( "Class", $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( "Block", $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( "Parameter", $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( "Value", $this->cellParaFmtLeft)) ;
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
		$this->cellValue	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellValue) ;
		$myTable->addRow( $myRow) ;
				
		/**
		 *
		 */
		BDocRegReport::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegReport) ;

		$this->begin() ;

		/**
		 *
		 */
//		$this->addMyXML( iconv( 'UTF-8', 'windows-1252', $this->myConfigObj->Prefix)) ;

		/**
		 *
		 */
		$lastCuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;
		$myConfigObj	=	new ConfigObj() ;				// no specific object we need here
		$cond	=	"C.Class like '".$_POST['_IFiltClass']."' " ;
		$cond	.=	"AND C.Block like '".$_POST['_IFiltBlock']."' " ;
		$cond	.=	"AND C.Parameter like '".$_POST['_IFiltParameter']."' " ;
		$cond	.=	"AND C.Value like '".$_POST['_IFiltValue']."' " ;
		$myConfigObj->setIterCond( $cond) ;
		$myConfigObj->setIterOrder( "ORDER BY C.Class, C.Block, C.Parameter ") ;
		foreach ( $myConfigObj as $key => $obj) {
			/**
			 * set the table cell data
			 */
			$this->cellClass->setData( $myConfigObj->Class) ;
			$this->cellBlock->setData( $myConfigObj->Block) ;
			$this->cellParameter->setData( $myConfigObj->Parameter) ;
			$this->cellValue->setData( iconv( 'UTF-8', 'windows-1252', $myConfigObj->Value)) ;
			/**
			 * determine if we need to print this line and how we have to print it
			 * IF this is a main line, ->print it
			 * IF this is an option to an article, ->print it
			 */
			$this->punchTable() ;
		}
		/**
		 * now we can complete the setup the teble-end row
		 */
		$this->endTable() ;
		/**

		 */
		$myPostfix	=	"" ;
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPostfix)) ;
		/**
		 * 
		 */
		if ( $_pdfName == '') {
			$pdfName	=	$this->path->Archive . "ConfigReport.pdf" ;
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

		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Configuration Report")), $this->defParaFmt) ;
		
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

		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Configuration Report")), $this->defParaFmt) ;
		
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
