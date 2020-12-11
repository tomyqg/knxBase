<?php
/**
 * FSqlMSSQLQuery.php	-	Database independent query builder.
 *
 * Fundamental class for accessing databases.
 * This implementation is good for any databases.
 * This class is follows the Singleton pattern, saying there can only
 * be one static instance of it. Therefor the constructor is private and
 * further derivation from this class is prohibited by declaring __clone as private.
 * FDb does, however, allow for accessing multiple different database. In order
 * to do this, every database to be used needs to be registered before it
 * can be used through the register() method.
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 200x-xx-xx	PA1		khw		added to rev. control
 * 2014-10-06	R1		khw		frozen R1 base; start to replace the old
 * 								mysql API with the new mysqli API
 *
 * @author	Karl-Heinz Welter <kwelter@icloud.com>
 * @version	0.1
 * @package	wapCore
 * @filesource
 */
/**
 * FDb implements a simple database connection for MySQL databases
 *
 * @package 	wapLib
 * @subpackage	Core
 */
/**
 * FSqlQuery
 * =========
 *
 * Object to handle database dialect independent queries towards a database.
 *
 * the FSqlQuery object manages the data required for a database access. the object maintains:
 * 	- a list of all query-relevant table attribute names
 *	- a list of all insert-relevant table attribute values
 *	- a list of where-conditions
 *	- a list of order-terms
 *	- a list of joins
 *	- a value pair for limiting the output of a select statement
 *
 * the FSqlQuery object provides methods for:
 *	- adding an attribute/an array of attributes to the query relevant table attribute list
 *	- adding a value for a query relevant atribute value
 *	- retrieving a database-specific list of all field names compliant to the requirements of the specified
 *		database
 *
 * Implementation status:
 *		mysql		tested
 *		mssql		experimental
 */
class	FSqlMSSQLQuery	extends	FSqlQuery	{
	/**
	 *
	 */
	function	_getFieldList() {
		$buffer	=	"" ;
		/**
		 *	IF field list contains (single) entries
		 *		add entries to the comma-seperated list
		 *	ELSE
		 *		add [alias.]* to the field list
		 *	FOR ALL joins
		 *		add field list of join to the field list
		 *	return string
		 */
		if ( count( $this->fieldList) > 0) {
			foreach ( $this->fieldList as $attr) {
				if ( $buffer != "")		$buffer	.=	"," ;
				$field	=	"[" . $attr . "]" ;
				$buffer	.=	( $this->as != "" ? $this->as . "." . $field : $field) ;
			}
		} else {
			$buffer	.=	( $this->as != "" ? $this->as . "." . "*" : "*") ;
		}
		foreach ( $this->joins as $index => $join) {
			$buffer	.=	"," ;
			$buffer	.=	$join->_getFieldList() ;
		}
		foreach ( $this->functionList as $attr) {
			$buffer	.=	"," ;
			$field	=	"[" . $attr . "]" ;
			$field	=	$attr[0] ;
			$as		=	$attr[1] ;
			$buffer	.=	$field . " AS " . $as ;
		}
		$buffer	.=	" " ;
		return $buffer ;
	}
	static	function	getDateTime( $_value) {
		$r	=	date_parse_from_format( "M j Y g:i:s:ua", $_value) ;
		$result	=	sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $r["year"], $r["month"], $r["day"], $r["hour"], $r["minute"], $r["second"]) ;
		return $result ;
	}
}
/**
 * FSqlMSSQLSelect
 * ===============
 *
 * Object to handle database select queries towards a database.
 *
 * Implementation status:
 *		mysql		tested
 *		mssql		experimental
 */
class	FSqlMSSQLSelect	extends	FSqlMSSQLQuery		{
	function	__construct( $_table, $_as="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		parent::__construct( $_table) ;
		$this->as		=	$_as ;
		FDbg::end() ;
	}
	function	getQuery() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		if ( isset( $this->mysqlSelect) || isset( $this->mssqlSelect)) {
			$buffer	=	$this->mssqlSelect ;
		} else {
			$buffer	=	"" ;
			if ( $this->limit) {
				$buffer	.=	"SELECT * FROM ( " ;
			}
			$buffer	.=	"SELECT " ;
			$buffer	.=	$this->_getFieldList() ;
			if ( $this->limit != null) {
				$myOrder	=	$this->_getOrder() ;
				$buffer	.=	",ROW_NUMBER() OVER( " . ( $myOrder == "" ? "ORDER BY Id" : $myOrder) . ") AS _index " ;
			}
			if ( $this->table != "") {
				$buffer	.=	" FROM " . $this->table . " " ;
			}
			if ( $this->as != "")
				$buffer	.=	"AS " . $this->as . " " ;
			$buffer	.=	$this->_getJoins() . " " ;
			$whereBuffer	=	$this->_getWhere() ;
			$buffer	.=	$whereBuffer . " " ;
			if ( $this->limit) {
				$buffer	.=	") AS C " ;
				$whereBuffer	=	" WHERE " ;
				$exp1	=	"_index >= " . ($this->limit->from + 1)  ;
				$exp2	=	"_index < " . ($this->limit->from + 1 + $this->limit->count) ;
				$whereBuffer	.=	implode( " AND ", [ $exp1, $exp2]) ;
				$buffer	.=	$whereBuffer . " " ;
			}
			$buffer	.=	$this->_getOrder() . " " ;
		}
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $buffer) ;
		FDbg::end() ;
		return $buffer ;
	}
	/**
	 *
	 */
	function	getCountQuery() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		if ( isset( $this->mysqlSelect) || isset( $this->mssqlSelect)) {
			$buffer	=	$this->mssqlCount ;
		} else {
			$buffer	=	"SELECT Count(*) as Count " ;
			$buffer	.=	" FROM " . $this->table . " " ;
			if ( $this->as != "")
				$buffer	.=	"AS " . $this->as . " " ;
			$buffer	.=	$this->_getJoins() . " " ;
			$buffer	.=	$this->_getWhere() . " " ;
		}
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $buffer) ;
		FDbg::end() ;
		return $buffer ;
	}

	function	getSumQuery($_sumcolumn) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		if ( isset( $this->mysqlSelect) || isset( $this->mssqlSelect)) {
			$buffer	=	$this->mssqlCount ;
		} else {
			$buffer	=	"SELECT Sum(".$_sumcolumn.") as Sum " ;
			$buffer	.=	" FROM " . $this->table . " " ;
			if ( $this->as != "")
				$buffer	.=	"AS " . $this->as . " " ;
			$buffer	.=	$this->_getJoins() . " " ;
			$buffer	.=	$this->_getWhere() . " " ;
		}
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $buffer) ;
		FDbg::end() ;
		return $buffer ;
	}
	function	_getOrder() {
		if ( count( $this->order) > 0) {
			$buffer	=	"ORDER BY " . implode( ",", $this->order) ;
		} else {
			$buffer	=	"" ;
		}
		return $buffer ;
	}
	function	_getWhere() {
		if ( count( $this->where) > 0) {
			$buffer	=	"WHERE " . implode( " AND ", $this->where) ;
		} else {
			$buffer	=	"" ;
		}
		return $buffer ;
	}
	function	_getJoins() {
		$buffer	=	"" ;
		foreach ( $this->joins as $index => $join) {
			$buffer	.=	$join->getQuery( $this->as) . " " ;
		}
		return $buffer ;
	}
	function	__toString() {
		return $this->getQuery() ;
	}
}
/**
 * FSqlMSSQLJoin()
 * ===============
 *
 */
class	FSqlMSSQLJoin	extends	FSqlMSSQLQuery		{
	private	static	$code	=	"" ;
	private	$parent	=	null ;
	private	$joinOn	=	array() ;
	function	__construct( $_parent, $_table="") {
		parent::__construct( $_table) ;
		$this->parent	=	$_parent ;
		if ( self::$code == "") {
			self::$code	=	"A" ;
		} else {
			self::$code++ ;
		}
		if ( self::$code == "C")
			self::$code++ ;
		$this->as		=	self::$code ;
		$_parent->addJoin( $this) ;
	}
	function	getQuery( $_prefix="") {
		$buffer	=	"LEFT JOIN " ;
		$buffer	.=	$this->table . " " ;
		if ( $this->as != "")
			$buffer	.=	"AS " . $this->as . " " ;
		$buffer	.=	$this->_getJoinOn( $_prefix) . " " ;
		return $buffer ;
	}
	function	joinOn( $_joinOn) {
		if ( is_array( $_joinOn)) {
			foreach ( $_joinOn as $index => $joinOn) {
				$this->joinOn[]	=	( $this->as != "" ? $this->as . "." . $joinOn : $joinOn)
								.	" = "
								.	( $this->parent->as != "" ? $this->parent->as . "." . $_joinOn : $_joinOn) ;
			}
		} else {
			$this->joinOn[]	=	( $this->as != "" ? $this->as . "." . $_joinOn : $_joinOn)
							.	" = "
							.	( $this->parent->as != "" ? $this->parent->as . "." . $_joinOn : $_joinOn) ;
		}
	}
	function	addJoin( $_join) {
		if ( is_array( $_join)) {
		} else {
			$this->joins[]	=	$_join ;
		}
	}
	function	_getJoinOn( $_prefix) {
		if ( count( $this->joinOn) > 0) {
			$buffer	=	"ON " . implode( " AND ", $this->joinOn) ;
		} else {
			$buffer	=	"" ;
		}
		return $buffer ;
	}
	function	_getJoins() {
		$buffer	=	"" ;
		foreach ( $this->joins as $index => $join) {
			$buffer	.=	$join->getQuery( $this->as) . " " ;
		}
		return $buffer ;
	}
}
/**
 * FSqlMSSQLInsert()
 * =================
 *
 */
class	FSqlMSSQLInsert	extends	FSqlMSSQLQuery		{
	function	__construct( $_table, $_instance=null) {
		parent::__construct( $_table) ;
		$this->instance	=	$_instance ;
	}
	function	getQuery() {
		$buffer	=	"INSERT " ;
		$buffer	.=	"INTO " . $this->table . " " ;
		$buffer	.=	"( " ;
		$buffer	.=	$this->_getFieldList() ;
		$buffer	.=	") VALUES ( " ;
		$buffer	.=	$this->getValueList() ;
		$buffer	.=	") " ;
		return $buffer ;
	}
	function	getValueList() {
		$buffer	=	"" ;
		if ( count( $this->valueList) > 0) {
			$buffer	.=	implode( ",", $this->valueList) ;
		}
		$buffer	.=	" " ;
		return $buffer ;
	}
	function	__toString() {
		return $this->getQuery() ;
	}
}
/**
 * FSqlMSSQLUpdate()
 * =================
 *
 */
class	FSqlMSSQLUpdate	extends	FSqlMSSQLQuery		{
	function	__construct( $_table, $_instance=null) {
		parent::__construct( $_table) ;
		$this->instance	=	$_instance ;
	}
	function	getQuery() {
		$buffer	=	"UPDATE " . $this->table . " " ;
		$buffer	.=	"SET " ;
		$values	=	new ArrayIterator( $this->valueList) ;
		$values->rewind() ;
		$dataList	=	"" ;
		foreach ( $this->fieldList as $attr) {
			if ( $dataList != "")		$dataList	.=	"," ;
			$dataList	.=	"[" . $attr . "]" ;
			$dataList	.=	" = " . $values->current() . " " ;
			$values->next() ;
		}
		$buffer	.=	$dataList ;
		$buffer	.=	$this->_getWhere() ;
		return $buffer ;
	}
	function	addWhere( $_where) {
		if ( is_array( $_where)) {
			foreach ( $_where as $index => $attr) {
				$this->where[]	=	( $this->as != "" ? $this->as . "." . $attr : $attr) ;
			}
		} else {
			if ( $_where != "") {
				$this->where[]	=	( $this->as != "" ? $this->as . "." . $_where : $_where) ;
			}
		}
	}
	function	_getWhere() {
		if ( count( $this->where) > 0) {
			$buffer	=	"WHERE " . implode( " AND ", $this->where) ;
		} else {
			$buffer	=	"" ;
		}
		return $buffer ;
	}
	function	__toString() {
		return $this->getQuery() ;
	}
}
/**
 * FSqlMSSQLDelete
 * ===============
 *
 * Object to handle deletion of objects in a database.
 *
 * Implementation status:
 *		mysql		open
 *		mssql		open
 */
class	FSqlMSSQLDelete	extends	FSqlMSSQLQuery		{
	function	__construct( $_table, $_instance=null) {
		parent::__construct( $_table) ;
		$this->instance	=	$_instance ;
	}
	function	getQuery() {
		$buffer	=	"DELETE " ;
		$buffer	.=	"FROM " . $this->table . " " ;
		$buffer	.=	$this->_getWhere() ;
		return $buffer ;
	}
	function	addWhere( $_where) {
		if ( is_array( $_where)) {
			foreach ( $_where as $index => $attr) {
				$this->where[]	=	( $this->as != "" ? $this->as . "." . $attr : $attr) ;
			}
		} else {
			if ( $_where != "") {
				$this->where[]	=	( $this->as != "" ? $this->as . "." . $_where : $_where) ;
			}
		}
	}
	function	_getWhere() {
		if ( count( $this->where) > 0) {
			$buffer	=	"WHERE " . implode( " AND ", $this->where) ;
		} else {
			$buffer	=	"" ;
		}
		return $buffer ;
	}
	function	__toString() {
		return $this->getQuery() ;
	}
}
/**
 * FSqlMSSQLStructure
 * ==================
 *
 * Object to handle structure queries towards a database.
 *
 * Implementation status:
 *		mysql		tested
 *		mssql		tested
 */
class	FSqlMSSQLStructure	extends	FSqlMSSQLQuery		{
	function	__construct( $_table) {
		parent::__construct( $_table) ;
	}
	function	getQuery() {
		$buffer	=	"SELECT TABLE_NAME, COLUMN_NAME AS Field, DATA_TYPE AS Type, IS_NULLABLE AS 'Null', '' AS 'Default' FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$this->table."' ORDER BY TABLE_NAME, ORDINAL_POSITION " ;
		return $buffer ;
	}
	function	__toString() {
		return $this->getQuery() ;
	}
}
?>
