<?php
/**
 * FSqlMySQLQuery.php	-	Database independent query builder.
 *
 * Fundamental class for accessing databases.
 * This implementation is good for any databases.
 * This class follows the Singleton pattern, saying there can only
 * be one static instance of it. Therefor the constructor is private and
 * further sub-classing from this class is prohibited by declaring __clone as private.
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
 * FSqlMySQLQuery
 * ==============
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
class	FSqlMySQLQuery		extends	FSqlQuery	{
    function	__construct( $_table, $_prefix="", $_as="") {
        parent::__construct( $_table, $_prefix, $_as) ;
    }

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
                $field	=	"`" . $attr . "`" ;
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
            $field	=	"`" . $attr . "`" ;
            $field	=	$attr[0] ;
            $as		=	$attr[1] ;
            $buffer	.=	$field . " AS " . $as ;
        }
        $buffer	.=	" " ;
        return $buffer ;
    }
    static	function	getDateTime( $_value) {
        return $_value ;
    }
}
/**
 * FSqlMySQLSelect
 * ===============
 *
 * Object to handle database select queries towards a database.
 *
 * Implementation status:
 *		mysql		tested
 *		mssql		experimental
 */
class	FSqlMySQLSelect	extends	FSqlMySQLQuery		{
    function	__construct( $_table, $_prefix="", $_as="") {
        parent::__construct( $_table, $_prefix, $_as) ;
    }

    /**
     * @return string
     */
    function	getQuery() {
        if ( isset( $this->mysqlSelect) || isset( $this->mssqlSelect)) {
            $buffer	=	$this->mysqlSelect ;
        } else {
            $buffer	=	"" ;
            $buffer	.=	"SELECT " ;
            $buffer	.=	$this->_getFieldList() ;
            if ( $this->table != "") {
                $buffer	.=	" FROM `" . $this->prefix . $this->table . "` " ;
            }
            if ( $this->as != "")
                $buffer	.=	"AS " . $this->as . " " ;
            $buffer	.=	$this->_getJoins() . " " ;
            $whereBuffer	=	$this->_getWhere() ;
            $buffer	.=	$whereBuffer . " " ;
            $buffer	.=	$this->_getGroup() . " " ;
            $buffer	.=	$this->_getOrder() . " " ;
            if ( $this->limit != null) {
                $buffer	.=	"LIMIT " . $this->limit->from . ", " . $this->limit->count . " " ;
            }
        }
        return $buffer ;
    }

    /**
     *
     */
    function	getCountQuery() {
        $buffer =   "" ;
        if ( isset( $this->mysqlSelect) || isset( $this->mssqlSelect)) {
//            $buffer	=	$this->mysqlCount ;
        } else {
            $buffer	=	"SELECT Count(*) as Count " ;
            $buffer	.=	" FROM `" . $this->prefix . $this->table . "` " ;
            if ( $this->as != "")
                $buffer	.=	"AS " . $this->as . " " ;
            $buffer	.=	$this->_getJoins() . " " ;
//			$buffer	.=	$this->_getGroup() . " " ;
            $buffer	.=	$this->_getWhere() . " " ;
        }
        return $buffer ;
    }

    /**
     * @param $_sumcolumn
     * @return string
     */
    function	getSumQuery( $_sumcolumn) {
        $buffer =   "" ;
        if ( isset( $this->mysqlSelect) || isset( $this->mssqlSelect)) {
//            $buffer	=	$this->mysqlCount ;
        } else {
            $buffer	=	"SELECT SUM(".$_sumcolumn.") as Sum " ;
            $buffer	.=	" FROM `" . $this->prefix . $this->table . "` " ;
            if ( $this->as != "")
                $buffer	.=	"AS " . $this->as . " " ;
            $buffer	.=	$this->_getJoins() . " " ;
//			$buffer	.=	$this->_getGroup() . " " ;
            $buffer	.=	$this->_getWhere() . " " ;
        }
        return $buffer ;
    }

    /**
     * @return string
     */
    function	_getGroup() {
        if ( count( $this->group) > 0) {
            $buffer	=	"GROUP BY " . implode( ",", $this->group) ;
        } else {
            $buffer	=	"" ;
        }
        return $buffer ;
    }

    /**
     * @return string
     */
    function	_getOrder() {
        if ( count( $this->order) > 0) {
            $buffer	=	"ORDER BY " . implode( ",", $this->order) ;
        } else {
            $buffer	=	"" ;
        }
        return $buffer ;
    }

    /**
     * @return string
     */
    function	_getWhere() {
        if ( count( $this->where) > 0) {
            $buffer	=	"WHERE " . implode( " AND ", $this->where) ;
        } else {
            $buffer	=	"" ;
        }
        return $buffer ;
    }

    /**
     * @return string
     */
    function	_getJoins() {
        $buffer	=	"" ;
        foreach ( $this->joins as $index => $join) {
            $buffer	.=	$join->getQuery( $this->as) . " " ;
        }
        return $buffer ;
    }

    /**
     * @return string
     */
    function	__toString() {
        return $this->getQuery() ;
    }
}
/**
 * FSqlMySQLJoin
 * =============
 *
 * Object to handle database select queries towards a database.
 *
 */
class	FSqlMySQLJoin	extends	FSqlMySQLQuery		{
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
        $buffer	.=	$this->prefix . $this->table . " " ;
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

    /**
     * @return string
     */
    function	_getJoins() {
        $buffer	=	"" ;
        foreach ( $this->joins as $index => $join) {
            $buffer	.=	$join->getQuery( $this->as) . " " ;
        }
        return $buffer ;
    }
}
/**
 * FSqlInsert()
 * ============
 *
 */
class	FSqlMySQLInsert	extends	FSqlMySQLQuery		{
    function	__construct( $_table, $_prefix="", $_as="") {
        parent::__construct( $_table, $_prefix, $_as) ;
    }
    function	getQuery() {
        $buffer	=	"INSERT " ;
        $buffer	.=	"INTO `" . $this->prefix . $this->table . "` " ;
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
 * FSqlUpdate()
 * ============
 *
 */
class	FSqlMySQLUpdate	extends	FSqlMySQLQuery		{

    /**
     * FSqlMySQLUpdate constructor.
     * @param $_table
     * @param string $_prefix
     * @param string $_as
     */
    function	__construct( $_table, $_prefix="", $_as="") {
        parent::__construct( $_table, $_prefix, $_as) ;
    }

    /**
     * @return string
     */
    function	getQuery() {
        $buffer	=	"UPDATE `" . $this->prefix . $this->table . "` " ;
        $buffer	.=	"SET " ;
        $values	=	new ArrayIterator( $this->valueList) ;
        $values->rewind() ;
        $dataList	=	"" ;
        foreach ( $this->fieldList as $attr) {
            if ( $dataList != "")
                $dataList	.=	"," ;
            $dataList	.=	"`" . $attr . "`" ;
            $dataList	.=	" = " . $values->current() . " " ;
            $values->next() ;
        }
        $buffer	.=	$dataList ;
        $buffer	.=	$this->_getWhere() ;
        return $buffer ;
    }

    /**
     * @param mixed $_where
     */
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

    /**
     * @return string
     */
    function	_getWhere() {
        if ( count( $this->where) > 0) {
            $buffer	=	"WHERE " . implode( " AND ", $this->where) ;
        } else {
            $buffer	=	"" ;
        }
        return $buffer ;
    }

    /**
     * @return string
     */
    function	__toString() {
        return $this->getQuery() ;
    }
}
/**
 * FSqlDelete
 * ==========
 *
 * Object to handle deletion of objects in a database.
 *
 * Implementation status:
 *		mysql		open
 *		mssql		open
 */
class	FSqlMySQLDelete	extends	FSqlMySQLQuery		{

    /**
     * FSqlMySQLDelete constructor.
     * @param $_table
     * @param string $_prefix
     * @param string $_as
     */
    function	__construct( $_table, $_prefix="", $_as="") {
        parent::__construct( $_table, $_prefix, $_as) ;
    }

    /**
     * @return string
     */
    function	getQuery() {
        $buffer	=	"DELETE " ;
        $buffer	.=	"FROM `" . $this->prefix . $this->table . "` " ;
        $buffer	.=	$this->_getWhere() ;
        return $buffer ;
    }

    /**
     * @param mixed $_where
     */
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
 * FSqlStructureQuery
 * ==================
 *
 * Object to handle structure queries towards a database.
 *
 * Implementation status:
 *		mysql		tested
 *		mssql		tested
 */
class	FSqlMySQLStructure		extends	FSqlMySQLQuery		{

    /**
     * FSqlMySQLStructure constructor.
     * @param $_table
     * @param string $_prefix
     * @param string $_as
     */
    function	__construct( $_table, $_prefix="", $_as="") {
        parent::__construct( $_table, $_prefix, $_as) ;
    }

    /**
     * @return string
     */
    function	getQuery() {
        $buffer	=	"SHOW FULL COLUMNS FROM `" . $this->prefix . $this->table . "` " ;
        return $buffer ;
    }

    /**
     * @return string
     */
    function	__toString() {
        return $this->getQuery() ;
    }
}
?>