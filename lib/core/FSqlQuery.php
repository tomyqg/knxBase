<?php
/**
 * Copyright (c) 2015, 2016 wimtecc, Karl-Heinz Welter
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 * FSqlQuery.php	-	Database independent query builder.
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
 * @package 	wapCore
 * @subpackage	Database
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
 *		database driver
 *
 * Implementation status:
 *		mysql		tested
 *		mssql		experimental
 */
abstract class	FSqlQuery	{
	public	$table	=	"" ;
	public	$prefix	=	"" ;
	public	$fieldList	=	array() ;
	public	$functionList	=	array() ;
	public	$values	=	array() ;
	public	$where	=	array() ;
	public	$order	=	array() ;
	public	$group	=	array() ;
	public	$limit	=	null ;
	public	$joins	=	array() ;
	public	$as		=	"" ;
	private	$driver	=	"mysql" ;
	function	__construct( $_table, $_prefix="", $_as="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_table', '$_prefix')") ;
		$this->table	=	$_table ;
		$this->prefix	=	$_prefix ;
		$this->as	=	$_as ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	addField( $_field) {
		if ( is_array( $_field)) {
			foreach ( $_field as $index => $attr) {
				$this->fieldList[]	=	$attr ;
			}
		} else {
			$this->fieldList[]	=	$_field ;
		}
	}
	/**
	 *
	 */
	function	addValue( $_value) {
		$this->valueList[]	=	$_value ;
	}
	/**
	 *
	 */
	function	addFunction( $_function) {
		$this->functionList[]	=	$_function ;
	}
	/**
	 *
	 */
	function	clearWhere() {
		unset( $this->where) ;
		$this->where	=	array() ;
	}
	/**
	 *
	 */
	function	addWhere( $_where) {
		if ( is_array( $_where)) {
			foreach ( $_where as $index => $attr) {
				if ( $attr != "") {
					$this->where[]	=	( $this->as != "" ? $this->as . "." . $attr : $attr) ;
				}
			}
		} else {
			if ( $_where != "") {
				$this->where[]	=	( $this->as != "" ? $this->as . "." . $_where : $_where) ;
			}
		}
	}
	/**
	 *
	 */
	function	clearGroup() {
		unset( $this->Group) ;
	}
	/**
	 *
	 */
	function	addGroup( $_group) {
		if ( is_array( $_group)) {
			foreach ( $_group as $index => $attr) {
				if ( $attr != "") {
					$this->group[]	=	( $this->as != "" ? $this->as . "." . $attr : $attr) ;
				}
			}
		} else {
			if ( $_group != "") {
				$this->group[]	=	( $this->as != "" ? $this->as . "." . $_group : $_group) ;
			}
		}
	}
	/**
	 *
	 */
	function	clearOrder() {
		unset( $this->order) ;
	}
	/**
	 *
	 */
	function	addOrder( $_order) {
		if ( is_array( $_order)) {
			foreach ( $_order as $index => $attr) {
				if ( $attr != "") {
					$this->order[]	=	( $this->as != "" ? $this->as . "." . $attr : $attr) ;
				}
			}
		} else {
			if ( $_order != "") {
				$this->order[]	=	( $this->as != "" ? $this->as . "." . $_order : $_order) ;
			}
		}
	}
	/**
	 *
	 */
	function	addJoin( $_join) {
		if ( is_array( $_join)) {
			foreach ( $_join as $index => $join) {
				$this->joins[]	=	$join ;
			}
		} else {
			$this->joins[]	=	$_join ;
		}
	}
	/**
	 *
	 */
	function	addLimit( $_limit) {
		$this->limit	=	$_limit ;
	}
	/**
	 *
	 */
	function	setAs( $_as) {
		$this->as	=	$_as ;
	}
}
/**
 * FSqlLimit
 * =========
 *
 * Object representing a record limits.
 *
 * Implementation independent.
 */
class	FSqlLimit	{
	public	$from	=	0 ;
	public	$count	=	1 ;
	function	__construct( $_from=0, $_count=1) {
		$this->from	=	$_from ;
		$this->count	=	$_count ;
	}
}
?>
