<?php

/**
 * FmkTable.php Base class for tables in PDF-printed matters
 *
 * This file contains all classes which are vital for the proper
 *type-setting of tables in the BDoc sub-package.
 * Data in the table is row-centric, this is to say that the row
 * objects contain the references to the data, the column objects
 * don't. The main task of the column object is to provide column
 * specific formatting information like column-width, default alignment
 * and default character format.
 *
 * Each table has a fundamental layout, linewise setup as follows:
 *    TABLE START ROW PER TABLE: only printed at table start
 *    TABLE START ROW PER PAGE: printed at table start and on each new page
 *    CARRY FROM ROW: printed at table start on each new page
 *    ITEM DATA ROW: ...
 *    TABLE TO ROW: printed at table end on each page
 *    TABLE END ROW PER PAGE: printed at table end and on each page end
 *    TABLE END ROW PER TABLE: only printed at table end
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcCoreSubSystem
 * @filesource
 */

/**
 * FmkTableCell - Base Class for TableCell
 *
 * This class mainly maintains data for a single cell content
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */
class	FmkTableCell	{

	const	CTText	=	1 ;
	const	CTInt	=	2 ;
	const	CTFloat	=	3 ;
	const	CTImage	=	4 ;

	const	BTNone	=	0 ;
	const	BTTop	=	1 ;
	const	BTLeft	=	2 ;
	const	BTRight	=	4 ;
	const	BTBottom	=	8 ;
	const	BTFull	=	15 ;

	private	$name	=	"" ;
	private	$parent	=	null ;
	private	$myWidth ;
	private	$parFmt ;
	private	$myData ;
	private	$myFormat ;
	private	$myType ;
	private	$myBorder ;

	public	$currHorOffs ;
	public	$currVerOffs ;

	function	__construct( $_parent, $_name) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <_parent>, '$_name')") ;
		$this->parent	=	$_parent ;
		$this->name	=	$_name ;
		$this->myWidth	=	-1 ;
		$this->parFmt	=	FmkParFmt::getFormat( "tableCell") ;
		$this->myData	=	"" ;
		$this->myFormat	=	"" ;
		$this->myType	=	"" ;
		$this->myBorder	=	FmkTableCell::BTNone ;
		$this->currHorOffs	=	0.0 ;
		$this->currVerOffs	=	0.0 ;
		FDbg::end() ;
	}

	/**
	 *
	 */
	function	setData( $_data) {
		$this->myData	=	$_data ;
	}

	/**
	 *
	 */
	function	getData() {
		return $this->myData ;
	}

	/**
	 *
	 */
	function	setFormat( $_format) {
		$this->myFormat	=	$_format ;
	}

	/**
	 *
	 */
	function	getFormat() {
		return $this->myFormat ;
	}

	/**
	 *
	 */
	function	setType( $_type) {
		$this->myType	=	$_type ;
	}

	/**
	 *
	 */
	function	getType() {
		return $this->myType ;
	}

	/**
	 *
	 */
	function	setBorder( $_border) {
		$this->myBorder	=	$_border ;
	}

	/**
	 *
	 */
	function	getBorder() {
		return $this->myBorder ;
	}

	/**
	 *
	 */
	function	getWidth() {
		return $this->myWidth ;
	}

	/**
	 *
	 */
	function	getParFmt() {
		return $this->parFmt ;
	}
	function	_debug( $_prefix="") {
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix."+--------------------------------------------------") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." Cell: own attributes") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => name='".$this->name."'") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => parFmt='".$this->parFmt->name."'") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => myWidth='".$this->myWidth."'") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => myData='".$this->myData."'") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => myType='".$this->myType."'") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => myBorder='".$this->myBorder."'") ;
	}

}

/**
 * FmkTableCol - Base Class for TableCol
 *
 * This class mainly maintains data for a column
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */

class	FmkTableColumn	{

	const	CTNumber	=  1 ;		// data row at end of table, i.e. after

	private	$name	=	"" ;
	private	$parent	=	null ;
	private	$colType ;
	private	$colWidth ;
	private	$colHorPos ;
	private	$width ;
	private	$readCellPos	=	0 ;
	private	$currHorOffs	=	0 ;

	function	__construct( $_parent, $_name) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <_parent>, '$_name')") ;
		$this->parent	=	$_parent ;
		$this->name	=	$_name ;
		$this->colType	=	FmkTableColumn::CTNumber ;
		$this->colHorPos	=	0 ;
		$this->colWidth	=	10 ;
		FDbg::end() ;
	}

	function	getType() {
		return $this->colType ;
	}

	function	setColHorPos( $_colHorPos) {
		$this->colHorPos	=	$_colHorPos ;
	}

	function	getHorPos() {
		return $this->colHorPos ;
	}

	function	getWidth() {
		return $this->colWidth ;
	}
	function	_debug( $_prefix="") {
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix."+--------------------------------------------------") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." Column: own attributes") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => name='".$this->name."'") ;
	}
}

/**
 * FmkTableRow - Base Class for TableRow
 *
 * This class mainly maintains data for a row
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */

class	FmkTableRow	{

	const	RTHeaderTS	=  1 ;		// header row at table start
	const	RTHeaderPS	=  2 ;		// header row at page start
	const	RTHeaderCF	=  3 ;		// header row at page start
	const	RTDataIT	=  4 ;		// data row in table
	const	RTFooterCT	=  5 ;		// header row at page start
	const	RTFooterPE	=  6 ;		// footer row at page end
	const	RTFooterTE	=  7 ;		// footer row at table end
	const	RTDataET	=  8 ;		// data row at end of table, i.e. after

	private	$name	=	"" ;
	private	$parent	=	null ;
	public	$rowType ;
	public	$cells	=	array() ;
	private	$cellCnt	=	0 ;
	private	$readCellPos	=	0 ;
	private	$enabled ;

	function	__construct( $_parent, $_name) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <_parent>, '$_name')") ;
		$this->parent	=	$_parent ;
		$this->name	=	$_name ;
		$this->rowType	=	FmkTableRow::RTDataIT ;
		$this->enabled	=	TRUE ;
		FDbg::end() ;
	}

	function	addCell( $_ndx, $_cell) {
		if ( $_ndx >= $this->cellCnt) {
			$this->cellCnt	=	$_ndx ;
		}
		$this->cells[$this->cellCnt++]	=	$_cell ;
	}

	function	getRowType() {
		return $this->rowType ;
	}

	function	getFirst() {
		$this->readCellPos	=	0 ;
		return $this->getNext() ;
	}

	function	getNext() {
		if ( $this->readCellPos < $this->cellCnt) {
			return $this->cells[$this->readCellPos++] ;
		} else {
			return FALSE ;
		}
	}

	function	getColNdx() {
		return ( $this->readCellPos - 1 ) ;	// he are already at the next position, so ( - 1 ) !
	}

	function	getCellAt( $_ndx) {
		if ( $_ndx < $this->cellCnt) {
			if ( isset( $this->cells[$_ndx])) {
				return $this->cells[$_ndx] ;
			} else {
				return FALSE ;
			}
		} else {
			return FALSE ;
		}
	}

	function	enable() {
		$this->enabled	=	TRUE ;
	}

	function	disable() {
		$this->enabled	=	FALSE ;
	}

	function	isEnabled() {
		return $this->enabled ;
	}
	function	_debug( $_prefix="") {
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix."+--------------------------------------------------") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." Row: own attributes") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => name='".$this->name."'") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => type='".$this->rowType."'") ;
		foreach ( $this->cells as $index => $obj) {
			$obj->_debug( $_prefix) ;
		}
	}
}

/**
 * FmkTable - Base Class for Table
 *
 * This class mainly glues together all the table related stuff.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */

class	FmkTableFmt	{
	static	$tableFormats	=	array() ;

	private	$name	=	"" ;
	private	$parent	=	null ;
	private	$rowsHeaderTS	=	array() ;		// table start rows
	private	$rowsHeaderPS	=	array() ;		// table page start rows
	private	$rowsHeaderCF	=	array() ;		// table carry over from rows
	private	$rowsDataIT	=	array() ;			// table data rows
	private	$rowsFooterCT	=	array() ;		// table carry over to rows
	private	$rowsFooterPE	=	array() ;		// table page end rows
	private	$rowsFooterTE	=	array() ;		// table end rows
	private	$rowsDataET	=	array() ;
	public	$cols		=	array() ;			// array of columns
	public	$rows		=	array() ;			// array of columns
	private	$readRowPos	=	0 ;
	private	$readRowType ;
	private	$readColPos	=	0 ;
	private	$colHorPos	=	0.0 ;

	public	$currHorPos	=	0.0 ;
	public	$currVerPos	=	0.0 ;

	function	__construct( $_parent, $_name) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <_parent>, '$_name')") ;
		$this->parent	=	$_parent ;
		$this->name	=	$_name ;
		self::$tableFormats[ $_name]	=	$this ;
		FDbg::end() ;
		$this->reset() ;
	}
	function	reset() {
		$this->rowCntrHeaderTS	=	0 ;
		$this->rowCntrHeaderPS	=	0 ;
		$this->rowCntrHeaderCF	=	0 ;
		$this->rowCntrDataIT	=	0 ;
		$this->rowCntrFooterCT	=	0 ;
		$this->rowCntrFooterPE	=	0 ;
		$this->rowCntrFooterTE	=	0 ;
		$this->rowCntrDataET	=	0 ;
		$this->colCntr		=	0 ;
		$this->currHorPos		=	0 ;
		$this->currVerPos		=	0 ;
	}
	static	function	getFormat( $_name) {
		if ( isset( self::$tableFormats[ $_name])) {
			return self::$tableFormats[ $_name] ;
		} else {
			return null ;
		}
	}
	/**
	 *
	 * @param FmkTableRow $_row
	 */
	function	addRow( $_row) {
		switch ( $_row->getRowType()) {
		case	FmkTableRow::RTHeaderTS	:
			$this->rowsHeaderTS[$this->rowCntrHeaderTS++]	=	$_row ;
			break ;
		case	FmkTableRow::RTHeaderPS	:
			$this->rowsHeaderPS[$this->rowCntrHeaderPS++]	=	$_row ;
			break ;
		case	FmkTableRow::RTHeaderCF	:
			$this->rowsHeaderCF[$this->rowCntrHeaderCF++]	=	$_row ;
			break ;
		case	FmkTableRow::RTDataIT	:
			$this->rowsDataIT[$this->rowCntrDataIT++]	=	$_row ;
			break ;
		case	FmkTableRow::RTFooterCT	:
			$this->rowsFooterCT[$this->rowCntrFooterCT++]	=	$_row ;
			break ;
		case	FmkTableRow::RTFooterPE	:
			$this->rowCntrFooterPE++ ;
			break ;
		case	FmkTableRow::RTFooterTE	:
			$this->rowsFooterTE[$this->rowCntrFooterTE++]	=	$_row ;
			break ;
		case	FmkTableRow::RTFooterET	:
			$this->rowCntrDataET++ ;
			break ;
		}
	}

	function	getFirstRow( $_rt) {
		$this->readRowType	=	$_rt ;
		$this->readRowPos	=	0 ;
		return $this->getNextRow() ;
	}

	function	getNextRow() {
		switch ( $this->readRowType) {
		case	FmkTableRow::RTHeaderTS	:
			if ( $this->readRowPos < $this->rowCntrHeaderTS) {
				return $this->rowsHeaderTS[$this->readRowPos++] ;
			} else {
				return FALSE ;
			}
			break ;
		case	FmkTableRow::RTHeaderPS	:
			if ( $this->readRowPos < $this->rowCntrHeaderPS) {
				return $this->rowsHeaderPS[$this->readRowPos++] ;
			} else {
				return FALSE ;
			}
			break ;
		case	FmkTableRow::RTHeaderCF	:
			if ( $this->readRowPos < $this->rowCntrHeaderCF) {
				return $this->rowsHeaderCF[$this->readRowPos++] ;
			} else {
				return FALSE ;
			}
			break ;
		case	FmkTableRow::RTDataIT	:
			if ( $this->readRowPos < $this->rowCntrDataIT) {
				return $this->rowsDataIT[$this->readRowPos++] ;
			} else {
				return FALSE ;
			}
			break ;
		case	FmkTableRow::RTFooterCT	:
			if ( $this->readRowPos < $this->rowCntrFooterCT) {
				return $this->rowsFooterCT[$this->readRowPos++] ;
			} else {
				return FALSE ;
			}
			break ;
		case	FmkTableRow::RTFooterPE	:
			if ( $this->readRowPos < $this->rowCntrFooterPE) {
				return $this->rowsFooterPE[$this->readRowPos++] ;
			} else {
				return FALSE ;
			}
			break ;
		case	FmkTableRow::RTFooterTE	:
			if ( $this->readRowPos < $this->rowCntrFooterTE) {
				return $this->rowsFooterTE[$this->readRowPos++] ;
			} else {
				return FALSE ;
			}
			break ;
		case	FmkTableRow::RTDataET	:
			if ( $this->readRowPos < $this->rowCntrFooterET) {
				return $this->rowsFooterTE[$this->readRowPos++] ;
			} else {
				return FALSE ;
			}
			break ;
		}
	}

	function	getFirstCol() {
		$this->readColPos	=	0 ;
		return $this->getNextCol() ;
	}

	function	getNextCol() {
		if ( $this->readColPos < $this->colCntr) {
			return $this->cols[$this->readColPos++] ;
		} else {
			return FALSE ;
		}
	}

	function	getColNdx() {
		return ( $this->readColPos - 1 ) ;	// he are already at the next position, so ( - 1 ) !
	}

	function	addCol( $_col, $_noSpace=false) {
		$this->cols[$this->colCntr]	=	$_col ;
		$this->cols[$this->colCntr]->setColHorPos( $this->colHorPos) ;
		$this->colHorPos	+=	$this->cols[$this->colCntr]->getWidth() ;
		if ( ! $_noSpace) {
			$this->colHorPos	+=	1.5 ;
		}
		$this->colCntr++ ;
	}

	function	addCols( $_cols) {
		if ( is_array( $_cols)) {
			foreach ( $_cols as $colWidth) {
				$this->addCol( new FmkTableCol( $colWidth)) ;
			}
		} else {
			die() ;
		}
	}

	function	getColAt( $_colNdx) {
		if ( $_colNdx < $this->colCntr) {
			return $this->cols[ $_colNdx] ;
		} else {
			return FALSE ;
		}
	}
	function	_debug( $_prefix="") {
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix."+--------------------------------------------------") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." Table: own attributes") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => name='".$this->name."'") ;
		foreach ( $this->cols as $index => $obj) {
			$obj->_debug( $_prefix) ;
		}
		foreach ( $this->rows as $index => $obj) {
			$obj->_debug( $_prefix) ;
		}
	}
}

?>
