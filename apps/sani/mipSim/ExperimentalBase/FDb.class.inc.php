<?php
/**
 * FDb.php		-	Database Abstraction Layer
 *
 * This version supports MySQL and MSSQL databases.
 * This class is follows the Singleton pattern, saying there can only
 * be one static instance of it. Therefor the constructor is private and
 * further derivation from this class is prohibited by declaring __clone as private.
 * FDb does, however, allow for accessing multiple different database. In order
 * to do this, every database to be used needs to be registered before it
 * can be used through the register() method.
 * In cases where SQL results are handled, these are always the database driver specific
 * resources.
 *
 * Revision history
 *
 * Date		Rev.	Who	What
 * ----------------------------------------------------------------------------
 * 200x-xx-xx	PA1	khw	added to rev. control
 * 2014-10-06	RA	khw	frozen R1 base; start to replace the old
 * 				mysql API with the new mysqli API
 * 2015-04-13	PB1	khw	further comments
 * 2015-06-16	PB2	khw	added prefix for database table
 * 2016-03-30	PB3	khw	added some diagnostic stuff during issues with
 *				MSSQL driver;
 *
 * To-Do
 *
 * Date			Who		What
 * ----------------------------------------------------------------------------
 * 2015-12-17	khw		rework insertId for the MS SQL path;
 *
 * Requires
 *
 * File			Rev.			Function
 * ----------------------------------------------------------------------------
 * FSqlQuery	*				Query builder for MySQL and MSSQL
 *
 * @author	Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.2
 * @package wapCore
 * @filesource
 */
/**
 * FDb implements a simple database connection for MySQL or MS SQL databases
 *
 * @package wapCore
 * @subpackage database
 */
class	FDb {
    /**
     *
     *
     */
    private	static	$inst	=	array() ;
    private	static	$db		=	array() ;
    private	static	$dbHost	=	array() ;
    private	static	$dbUser	=	array() ;
    private	static	$dbPasswd	=	array() ;
    private	static	$dbName	=	array() ;
    private	static	$driver	=	array() ;
    private	static	$prefix	=	array() ;
    private	static	$serverCharset	=	array() ;
    private static  $dbNumSpaceStart        =       "" ;
    private static  $dbNumSpaceEnd  =       "" ;
    private	static	$queryBaseName	=	array() ;
    /**
     * __construct( $_db)
     *
     * instantiates 'the' object for the given database alias. There can be only a single instance for
     * every alias.
     *
     * @param string $_db alias under which the connection must be used
     */
    private function __construct( $_db="def") {
        if ( ! isset( self::$db[$_db])) {
            if ( self::$driver[$_db] == "mysql") {
                $parts  =   explode( ":", self::$dbHost[$_db]) ;
                self::$db[$_db]	=	mysqli_connect( $parts[0], self::$dbUser[$_db], self::$dbPasswd[$_db], self::$dbName[$_db], ( $parts[1]?$parts[1]:"")) ;
            } else if ( self::$driver[$_db] == "pdo_mysql") {
                $parts  =   explode( ":", self::$dbHost[$_db]) ;
                $myDSN  =   "mysql:dbname=" . self::$dbName[$_db] . ";host=" . self::$dbHost[$_db] . "" ;
                try {
                    self::$db[$_db]	=	new PDO($myDSN, self::$dbUser[$_db], self::$dbPasswd[$_db]) ;
                } catch ( PDOException $pdoE) {
                    self::$db[$_db] =   false ;
                }
            } else if ( self::$driver[$_db] == "mssql") {
                self::$db[$_db]	=	mssql_connect( self::$dbHost[$_db], self::$dbUser[$_db], self::$dbPasswd[$_db], true) ;
                mssql_select_db( self::$dbName[$_db]) ;
            } else {
                error_log("!!!!!!!!!!!!!!!!!!!!!!!!!!! NO VALID DRIVER SELECTED !!!!!!!!!!!!!!!!!!!!!!!!!!") ;
                die() ;
            }
            // place for a possible database select
            if ( self::$db[$_db] !== false) {
            } else {
                die() ;
            }
        } else {
        }
    }
    /**
     * setCharset
     *
     * Return the instance of the database connection
     *
     * @param	string	$_db	alias of the connection to be ued
     * @param	string	$_charset	character set for the connection
     * @return	void
     */
    static	function	setCharset( $_db="def", $_charset) {
        switch ( $_charset) {
            case "UTF-8"		:
            case "ISO-8859-1"	:
                self::$serverCharset[ $_db]	=	$_charset ;
                break;
            default	:
                break ;
        }
    }
    /**
     * getCharset
     *
     * Return the instance of the database connection
     *
     * @param	string	$_db	alias of the connection to be ued
     * @return	string          character set of the connection specified by <alias>
     */
    static	function getCharset( $_db="def") {
        return self::$serverCharset[ $_db] ;
    }
    /**
     * setNumSpace
     *
     * Sets the number space for specific operations in the database.
     *
     * @param string	$_db	alias of the connection to be used
     * @return void
     */
    static	function	setNumSpace( $_db="def") {
       if ( self::$driver[$_db] == "mysql") {
        } else if ( self::$driver[$_db] == "mssql") {
        }
    }
    /**
     * setEncoding
     *
     * Sets the encoding for the connection to utf8.
     *
     * @param string	$_db	alias of the connection to be used
     * @return	void
     */
    static	function	setEncoding( $_db="def") {
        if ( self::$driver[$_db] == "mysql") {
            mysqli_set_charset( self::$db[$_db], "utf8") ;
        } else if ( self::$driver[$_db] == "pdo_mysql") {
//            ini_set('mssql.charset', 'UTF-8');
        } else if ( self::$driver[$_db] == "mssql") {
            ini_set('mssql.charset', 'UTF-8');
        }
    }
    /**
     * get
     *
     * Return the instance of the database connection
     *
     * @param	string	$_db	alias of the connection to bs ued
     * @return	resource
     */
    static	function get( $_db="def") {
        if ( !isset( self::$inst[$_db])) {
            self::$inst[$_db]	=	new self( $_db) ;
            self::setNumSpace( $_db) ;
            self::setEncoding( $_db) ;
        }
        return self::$db[ $_db] ;
    }
    /**
     * callProc
     *
     * Performs a call to an SQL stored procedure and retrieves the result value.<br/>
     * SQL stored procedures needs to conform to a certain basic pattern, even though the complete call
     * comes from the caller. This is necessary in order to determine the result of the execuion of the
     * stored procedure on this level. The form is:<br/>
     * proc_name( OUT INT status, OUT * result)<br/>
     * the stored procedure needs to store a result value ( 0 =ok, !=0 =not ok) in the first return parameter, and
     * a possible result (like a generated number) in the parameter named result.
     *
     * Example:<br/>
     * callProc( "increment( @status, 12345, @newValue)", "@newValue") ;<br/>
     * This would call the procedure "increment", expect the general status of the procedure to be returned in the variable "@newValue"
     *
     * @param	string	$_proc		complete function call of the stored procedure
     * @param	string	$_resultVar	Name der Ergenisvariable die dne Status der Stored Procedure beinhaltet
     * @param	string	$_db		alias of the connection to be used
     * @return	string       		array containing the result of the query for $_resultVar
     * @throws  Exception
     */
    public	function	callProc( $_proc, $_resultVar="@result", $_db="def") {
        if ( !isset( self::$inst[$_db])) {
            self::$inst[$_db]	=	new self($_db) ;
            self::setNumSpace( $_db) ;
            self::setEncoding( $_db) ;
        }
        $resValue   =   "INVALID" ;
        if ( mysqli_select_db( self::$dbName[$_db], self::$db[$_db])) {
            /**
             * pull the query together and submit to the mysql server
             */
            $query	=	"call " . $_proc ;
            $sqlResult	=	mysqli_query( self::$db[$_db], $query) ;
            if ( !$sqlResult) {
                throw new Exception( "FDb::callProc( '$_proc'): failure: " . mysqli_error( self::$db[$_db])) ;
            } else {
                /**
                 * get the general status of the procedure execution
                 */
                $query	=	"select @status " ;
                /** @var resource $query */
                $sqlResult	=	mysqli_query( self::$db[$_db], $query) ;
                if ( !$sqlResult) {
                    throw new Exception( "FDb::callProc( '$_proc'): failure: " . mysqli_error( self::$db[$_db])) ;
                } else {
                    $row	=	mysqli_fetch_assoc( $sqlResult) ;
                    $status	=	intval( $row['@status']) ;
                    if ( $status != 0) {
                        throw new Exception( "FDb::callProc( '$_proc'): failure, @status != 0, i.e. $status !") ;
                    } else {
                        /**
                         * get the user-specified result variable
                         */
                        $query	=	"select " . $_resultVar . " " ;
                        $sqlResult	=	mysqli_query( self::$db[$_db], $query) ;
                        if ( !$sqlResult) {
                            throw new Exception( "FDb::callProc( '$_proc'): failure, can't get result variable !") ;
                        } else {
                            $row    =       mysqli_fetch_assoc( $sqlResult) ;
                            $resValue	=	$row[$_resultVar] ;
                        }
                    }
                }
            }
        }
        return $resValue ;
    }
    /**
     * query( _query, _db)
     *
     * Send a query to the database and return the result provided through the used driver.
     * The driver is dynamically selected based on the driver chosen during registration of the
     * database (registerDb).
     *
     * @param	FSqlQuery	$_query		sql-query to be executed
     * @param	string		$_db		alias of the connection to be used
     * @return	mixed	result of the sql query
     *						false			=	failure
     *						true			=	successfull in case of not SELECT, SHOW, DESCRIBE, EXPLAIN
     *						mysqli_result	=	in case of MYSQL driver
     *						mssql_result	=	in case of MSSQL driver
     *                      PDOStatement    =   in case of pdo_* driver
     * @throws  FException
     * @throws  Exception
     */
    static	function	query( $_query, $_db="def") {
        if ( !isset( self::$inst[$_db])) {
            self::$inst[$_db]	=	new self( $_db) ;
            self::setNumSpace( $_db) ;
            self::setEncoding( $_db) ;
        }
        /**
         * special code to uncover faults related to unadopted calling parties!!!
         */
        if ( ! is_object( $_query)) {
            error_log( "PANIC: ---> FDb.php(".__LINE__.")::FDb::query( '$_query', '$_db')::NOTE: no string allowed, FSqlQuery base object must be passed as parameter!") ;
            die() ;
        }
//
        $myQuery	=	iconv( "UTF-8", self::$serverCharset[$_db], $_query->getQuery()) ;
//
        if ( self::$driver[$_db] == "mysql") {
            $sqlResult = mysqli_query(self::$db[$_db], $myQuery);
        } else if ( self::$driver[$_db] == "pdo_mysql") {
            $sqlResult	=	self::$db[$_db]->query( $myQuery) ;
        } else if ( self::$driver[$_db] == "mssql") {
//			mssql_select_db( self::$dbName[$_db]) ;
            $sqlResult	=	mssql_query( $myQuery, self::$db[$_db]) ;
        } else {
            throw new Exception( "FDb::query( ...): failure: invalid diver specified") ;
        }
        if ( !$sqlResult) {
            if ( self::$driver[$_db] == "mysql") {
                error_log( $myQuery) ;
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."('<_query>', '$_db')", "failure: " . mysqli_error( self::$db[$_db]) . ">>>" . $myQuery . "<<<") ;
            } else {
                throw new Exception( "FDb::query('<_query>', '$_db'): failure: " . mssql_get_last_message()) ;
            }
        } else {
        }
        return $sqlResult ;
    }
    /**
     * insertId( $_db)
     *
     * Return the id of the last inserted row.
     * With MySQL that's rather easy since the value is provided on request.
     * The code for MS SQL probably needs to be reworked as this is only a quick solution needed for
     * a specific implementation abd is doomed to fail in otehr cases ...
     *
     * @param	string		$_db		alias of the connection to be used
     * @return	integer 				id of the last inserted row
     * @throws  FException
     * @throws  Exception
     */
    static	function	insertId( $_db="def") {
        if ( self::$driver[$_db] == "mysql") {
            $id = mysqli_insert_id(self::$db[$_db]);
        } else if ( self::$driver[$_db] == "pdo_mysql") {
            $id	=	self::$db[$_db]->lastInsertId() ;
        } else {
//			mssql_select_db( self::$dbName[$_db]) ;
            $sqlResult	=	mssql_query( "SELECT SCOPE_IDENTITY() AS Id", self::$db[$_db]) ;
            if ( $sqlResult === false) {
                throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '<_query>', '$_db')", "returned invalid result: " . mssql_get_last_message()) ;
            } else if ( $sqlResult === true) {
                throw new Exception( "FDb::queryRow( '<_query>', '$_db'): FDb::".__LINE__." returned weird result: " . mssql_get_last_message()) ;
            } else {
                $row    =       mssql_fetch_assoc( $sqlResult) ;
                $id	=	$row["Id"] ;
            }
        }
        return $id ;
    }

    /**
     * queryRow( $_query, $_db)
     *
     * Send a query to the database and return the (first) result row provided through the used driver.
     * The driver is dynamically selected based on the driver chosen during registration of the
     * database (registerDb).
     *
     * @param	FSqlQuery	$_query		sql-query to be executed
     * @param	string		$_db		alias of the connection to be used
     * @return	array					associative array containing the result from the provided query
     * @throws  Exception
     */
    static	function	queryRow( $_query, $_db="def") {
        if ( !isset( self::$inst[$_db])) {
            self::$inst	=	new self( $_db) ;
            self::setNumSpace() ;
            self::setEncoding() ;
        }
        $sqlResult  =   false ;
        if ( self::$driver[$_db] == "mysql") {
            $sqlResult = mysqli_query(self::$db[$_db], $_query->getQuery());
        } else if ( self::$driver[$_db] == "pdo_mysql") {
            $sqlResult	=	self::$db[$_db]->query( $_query->getQuery()) ;
        } else if ( self::$driver[$_db] == "mssql") {
//			mssql_select_db( self::$dbName[$_db]) ;
            $sqlResult	=	mssql_query( $_query->getQuery(), self::$db[$_db]) ;
        }
        if ( $sqlResult === false) {
            $e	=	new Exception( "FDb.php::FDb::queryRow('<_query>', '$_db'): failure: " . mysqli_error( self::$db[$_db])) ;
            error_log( $e->getMessage()) ;
            throw $e ;
        } else {
            $row    =   array() ;
            if ( self::$driver[$_db] == "mysql") {
                $row = mysqli_fetch_assoc($sqlResult);
            } else if ( self::$driver[$_db] == "pdo_mysql") {
                $row    =   $sqlResult->fetch( PDO::FETCH_ASSOC) ;
            } else if ( self::$driver[$_db] == "mssql") {
                $row    =       mssql_fetch_assoc( $sqlResult) ;
            }
        }
        return $row ;
    }
    /**
     * getRow( $_sqlResult, $_db)
     * ==========================
     *
     * Return the result row for the provided sqlResult.
     * @param	mixed	$_sqlResult	sql resource
     * @param	string		$_db		alias of the connection to be used
     * @return	array					associative array containing the result from the provided query
     */
    public	static	function	getRow( $_sqlResult, $_db="def") {
        $row    =   array() ;
        if ( self::$driver[$_db] == "mysql") {
            $row = mysqli_fetch_assoc($_sqlResult);
        } else if ( self::$driver[$_db] == "pdo_mysql") {
                $row    =       $_sqlResult->fetch( PDO::FETCH_ASSOC) ;
        } else if ( self::$driver[$_db] == "mssql") {
            $row    =       mssql_fetch_assoc( $_sqlResult) ;
        }
        return $row ;
    }
    /**
     * getRow( $_sqlResult, $_db)
     * ==========================
     *
     * Return the result row for the provided sqlResult.
     * @param	mixed   	$_sqlResult	sql resource
     * @param	string		$_db		alias of the connection to be used
     * @return	object					associative array containing the result from the provided query
     */
    public	static	function	getObject( $_sqlResult, $_db="def") {
        $obj    =   new stdClass() ;
        if ( self::$driver[$_db] == "mysql") {
            $obj = mysqli_fetch_object($_sqlResult);
        } else if ( self::$driver[$_db] == "mysql") {
            $obj    =   $_sqlResult->fetch( PDO::FETCH_OBJ) ;
        } else if ( self::$driver[$_db] == "mssql") {
            $obj    =   mssql_fetch_object( $_sqlResult) ;
        }
        return $obj ;
    }
    /**
     * queryForXMLTable( $_query, $_db, $_fncEoL, $_fncEoT)
     * ====================================================
     *
     * Send a query to the database and return an XML-formatted string with all datarows returned by the query.
     * If provided an end-of-line function will be called at the end of each row.
     * If provided an end-of-table function will be called at the end of the table.
     * The driver is dynamically selected based on the driver chosen during registration of the
     * database (registerDb).
     *
     * @param	FSqlQuery	$_query		sql-query to be executed
     * @param	string		$_objName	name of the object, used as the XML table node name
     * 									and as the node name
     * @return	string					string containing the XML formatted table of objects
     */
//    function	queryForXMLTable( $_query, $_objName, $_fncEoL=null, $_fncEoT=null) {
//        if ( mysqli_select_db( self::$dbName[$_db], self::$db[$_db])) {
//            $res	=	"" ;
//            $res	.=	"<Table" . $_objName . ">\n" ;
//            $sqlRes	=	FDb::query( $_query) ;
//            while ( $row = FDb::getRow( $sqlRes)) {
//                $res	.=	FDb::_getRowAsXML( $row, $_objName, $_fncEoL) ;
//            }
//            if ( $_fncEoT != null) {
//                $res	.=	$_fncEoT() ;
//            }
//            $res	.=	"</Table" . $_objName . ">\n" ;
//        }
//        return $res ;
//    }
    /**
     * _getRowAsXML( $_row, $_objName, $_fncEoL)
     * =========================================
     *
     * Return an XML-formatted string with all attributes of the provided associative array.
     *
     * @param	array		$_row		associatve array containing all attributes
     * @param	string		$_objName	name of the object, used as the XML node name
     * @return	string					string containing the XML formatted object
     */
    private	function	_getRowAsXML( $_row, $_objName, $_fncEoL="\n") {
        $buffer	=	"<" . $_objName . ">" . $_fncEoL ;
        foreach ( $_row as $colName => $val) {
            $buffer	.=	"<" . $colName . " title=\"" . $colName . "\"><![CDATA[" . $val . "]]></" . $colName . ">" . $_fncEoL ;
        }
        $buffer	.=	"</" . $_objName . ">" . $_fncEoL ;
        return( $buffer) ;
    }
    /**
     * rowCount( $_db)
     * ===============
     * Return the number of rows affected by the last query.
     *
     * @param	string	$_db		alias of the connection to be used
     * @return	integer $rowCount   number of rows in the current result set
     */
    static	function	rowCount( $_db="def") {
        $rowCount   =   -1 ;
        if ( !isset( self::$inst[$_db])) {
            self::$inst[$_db]	=	new self( $_db) ;
        }
        if ( self::$driver[$_db] == "mysql") {
            $rowCount = mysqli_affected_rows(self::$db[$_db]);
//        } else if ( self::$driver[$_db] == "pdo_mysql") {
//            $rowCount    =   mysqli_affected_rows( self::$db[$_db]) ;
        } else if ( self::$driver[$_db] == "mssql") {
            $rowCount    =   mssql_rows_affected( self::$db[$_db]) ;
        }
        return $rowCount ;
    }

    /**
     * getCount( $_count, $_db)
     * ========================
     *
     * Return the number of rows which are .
     *
     * Example:<br/>
     * getCount( "FROM Artikel WHERE ArtikelNr like '123%'")
     *
     * @param	FSqlQuery   $_query		criteria, including the "FROM * WHERE *"
     * @param	string	    $_db		alias of the connection to be used
     * @return	integer		    		number of rows matching the criteria
     * @throws  Exception
     */
    public	static	function	getCount( $_query, $_db="def") {
        if ( !isset( self::$inst[$_db])) {
            self::$inst[$_db]	=	new self( $_db) ;
            self::setNumSpace() ;
            self::setEncoding() ;
        }
        $result	=	-1 ;
        $query	=	iconv( "UTF-8", self::$serverCharset[$_db], $_query->getCountQuery()) ;
        $sqlResult  =   null ;
        if ( self::$driver[$_db] == "mysql") {
            $sqlResult = mysqli_query(self::$db[$_db], $query);
        } else if ( self::$driver[$_db] == "pdo_mysql") {
            $sqlResult	=	self::$db[$_db]->query( $query) ;
        } else if ( self::$driver[$_db] == "mssql") {
//			mssql_select_db( self::$dbName[$_db]) ;
            $sqlResult	=	mssql_query( $query, self::$db[$_db]) ;
        }
        if ( !$sqlResult) {
            $e	=	new Exception( "FDb.php::FDb::getCount(...): mysqli_error[" . mysqli_error( self::$db[$_db]) . "]{$query}") ;
            error_log( $e) ;
            throw $e ;
        } else {
            $count  =   -1 ;
            if ( self::$driver[$_db] == "mysql") {
                if (mysqli_affected_rows(self::$db[$_db]) == 1) {
                    $row = mysqli_fetch_assoc($sqlResult);
                    $count = intval($row['Count']);
                }
            } else if ( self::$driver[$_db] == "pdo_mysql") {
                    if ( $sqlResult->rowCount() == 1) {
                        $row = $sqlResult->fetch( PDO::FETCH_ASSOC);
                        $count = intval($row['Count']);
                    }
            } else if ( self::$driver[$_db] == "mssql") {
                if ( mssql_rows_affected( self::$db[$_db]) == 1) {
                    $row		=	mssql_fetch_assoc( $sqlResult) ;
                    $count		=	intval( $row['Count']) ;
                }
            }
        }
        return ( $count) ;
    }

    /**
     * @param   FSqlQuery   $_query
     * @param   string      $_db
     * @param   $_sumcolumn
     * @return  float
     * @throws  Exception
     */
    public	static	function	getSum( $_query, $_db="def", $_sumcolumn) {
        if ( !isset( self::$inst[$_db])) {
            self::$inst[$_db]	=	new self( $_db) ;
            self::setNumSpace() ;
            self::setEncoding() ;
        }
        $result	=	-1 ;
        $query	=	iconv( "UTF-8", self::$serverCharset[$_db], $_query->getSumQuery( $_sumcolumn)) ;
        $sqlResult  =   null ;
        if ( self::$driver[$_db] == "mysql") {
            $sqlResult = mysqli_query(self::$db[$_db], $query);
        } else if ( self::$driver[$_db] == "mysql") {
            $sqlResult	=	self::$db[$_db]->query( $query) ;
        } else if ( self::$driver[$_db] == "mssql") {
//			mssql_select_db( self::$dbName[$_db]) ;
            $sqlResult	=	mssql_query( $query, self::$db[$_db]) ;
        }
        if ( !$sqlResult) {
            $e	=	new Exception( "FDb.php::FDb::getSum(...): mysqli_error[" . mysqli_error( self::$db[$_db]) . "]{$query}") ;
            error_log( $e) ;
            throw $e ;
        } else {
            $sum    =   -1 ;
            if ( self::$driver[$_db] == "mysql") {
                if (mysqli_affected_rows(self::$db[$_db]) == 1) {
                    $row = mysqli_fetch_assoc($sqlResult);
                    $sum = floatval($row['Sum']);
                }
            } else if ( self::$driver[$_db] == "mysql") {
                if ( $sqlResult->rowCount() == 1) {
                    $row    =   $sqlResult->fetchssoc( PDO::FETCH_ASSOC);
                    $sum	=	floatval( $row['Sum']) ;
                }
            } else if ( self::$driver[$_db] == "mssql") {
                if ( mssql_rows_affected( self::$db[$_db]) == 1) {
                    $row    =   mssql_fetch_assoc($sqlResult);
                    $sum	=	floatval( $row['Sum']) ;
                }
            }
        }
        return ( $sum) ;
    }
    /**
     * registerDb
     *
     * Register a new vector for database access under a given alias
     *
     * @param	string	$_dbHost	FQN of the database server
     * @param	string	$_dbUser	user name
     * @param	string	$_dbPasswd	user password
     * @param	string	$_dbName	name of the database
     * @param	string	$_alias		alias name to associate this connection
     * @param   string  $_driver    driver name, either mysql or mssql
     * @param   string  $_prefix    driver name, either mysql or mssql
     * @param   string  $_numSpaceStart driver name, either mysql or mssql
     * @param   string  $_numSpaceEnd   driver name, either mysql or mssql
     * @return	void
     */
    static	function	registerDb( $_dbHost, $_dbUser, $_dbPasswd, $_dbName, $_alias="def", $_driver="mysql", $_prefix="", $_numSpaceStart="000000", $_numSpaceEnd="999999") {
        self::$dbHost[ $_alias]	=	$_dbHost ;
        self::$dbUser[ $_alias]	=	$_dbUser ;
        self::$dbName[ $_alias]	=	$_dbName ;
        self::$dbPasswd[ $_alias]	=	$_dbPasswd ;
        self::$driver[ $_alias]	=	$_driver ;
        self::$prefix[ $_alias]	=	$_prefix ;
        self::$dbNumSpaceStart[ $_alias]	=	$_numSpaceStart ;
        self::$dbNumSpaceEnd[ $_alias]	=	$_numSpaceEnd ;
        self::$serverCharset[ $_alias]	=	"UTF-8" ;
        switch ( $_driver) {
            case 'mysql'	:
                self::$queryBaseName[$_alias]	=	"FSqlMySQL" ;
                break;
            case 'pdo_mysql'	:
                self::$queryBaseName[$_alias]	=	"FSqlPDO" ;
                break;
            case 'mssql'	:
                self::$queryBaseName[$_alias]	=	"FSqlMSSQL" ;
                break;
        }
    }

    /**
     * @param   string      $_alias     alias of the connection to be used
     * @return  mixed|null
     */
    static	function	getDbName( $_alias="def") {
        if ( isset( self::$dbName[ $_alias])) {
            $res	=	self::$dbName[ $_alias] ;
        } else {
            $res	=	null ;
        }
        return $res ;
    }

    /**
     * @param   string      $_alias     alias of the connection to be used
     * @return  mixed|null
     */
    static	function	getDriver( $_alias="def") {
        if ( isset( self::$dbName[ $_alias])) {
            $res	=	self::$queryBaseName[ $_alias] ;
        } else {
            $res	=	null ;
        }
        return $res ;
    }

    /**
     * getId(...)
     * ==========
     *
     * Return a string of the form <dbhost>@<dbname> for the connection
     *
     * @param	string	$_alias		alias of the connection to be used
     * @return	string  $res
     */
    public	static	function	getId( $_alias="def") {
        if ( isset( self::$dbHost[ $_alias])) {
            $res    =   self::$dbHost[ $_alias] . "@" . self::$dbName[ $_alias] ;
        } else {
            $res    =   "No database for alias '$_alias' registered!" ;
        }
        return $res ;
    }

    /**
     * @param $_type
     * @param $_table
     * @param string $_db
     * @return mixed
     */
    public	static	function	getQueryObj( $_type, $_table, $_db="def") {
//        require_once( self::$queryBaseName[$_db] . "Query" . ".php") ;		// load include file
        $name	=	self::$queryBaseName[$_db] . $_type ;
        return new $name( $_table, self::$prefix[$_db]) ;
    }

    /**
     *
     */
    /**
     * @param   string      $_db
     * @param   string      $_value
     * @return  string      $result
     */
    public	static	function	getDateTime( $_db, $_value) {
//        require_once( self::$queryBaseName[$_db] . "Query" . ".php") ;		// load include file
        /**
         * @var FSqlQuery   $name
         */
        $name	=	self::$queryBaseName[$_db] . "Query" ;
        /**
         *
         */
        $result	=	$name::getDateTime( $_value) ;
        return $result ;
    }
    /**
     * __clone()
     * =========
     *
     * Dummy method to prohibit cloning !
     */
    private function __clone() {}

    /**
     * showTables(...)
     * ===============
     * @param   string  $_db
     * @return  array
     * @throws  FException
     */
    static	function	getTables( $_db="def") {
        $tables	=	array() ;
        if ( !isset( self::$inst[$_db])) {
            self::$inst[$_db]	=	new self( $_db) ;
            self::setNumSpace( $_db) ;
            self::setEncoding( $_db) ;
        }
        $sqlResult	=	mysqli_query( self::$db[$_db], "SHOW TABLES FROM ".self::$dbName[$_db]." ") ;
        if ( !$sqlResult) {
            $e	=	new FException( basename( __FILE__), __CLASS__, __METHOD__."( '<_query>', '$_db')", "failure: " . mysqli_error( self::$db[$_db])) ;
            error_log( $e) ;
            throw $e ;
        } else {
            while ( $row = mysqli_fetch_array( $sqlResult)) {
                error_log( "---------> '" . $row[0]) ;
                $tables[]	=	$row[0] ;
            }
        }
        return $tables ;
    }

    /**
     * showTables(...)
     * ===============
     * @param   string  $_db
     * @return  array
     * @throws  FException
     *
     */
    static	function	getViews( $_db="def") {
        $tables	=	array() ;
        if ( !isset( self::$inst[$_db])) {
            self::$inst[$_db]	=	new self( $_db) ;
            self::setNumSpace( $_db) ;
            self::setEncoding( $_db) ;
        }
        $sqlResult	=	mysqli_query( self::$db[$_db], "SHOW FULL TABLES WHERE TABLE_TYPE LIKE 'VIEW' ") ;
        if ( !$sqlResult) {
            $e	=	new FException( basename( __FILE__), __CLASS__, __METHOD__."( '<_query>', '$_db')", "failure: " . mysqli_error( self::$db[$_db])) ;
            error_log( $e) ;
            throw $e ;
        } else {
            while ( $row = mysqli_fetch_array( $sqlResult)) {
                error_log( "---------> '" . $row[0]) ;
                $tables[]	=	$row[0] ;
            }
        }
        return $tables ;
    }

    /**
     * @param   string      $_str
     * @param   string      $_db        alias of the connection to be used
     * @return  mixed|string
     */
    static	function	escapeString( $_str, $_db) {
        if ( !isset( self::$inst[$_db])) {
            self::$inst[$_db]	=	new self( $_db) ;
            self::setNumSpace( $_db) ;
            self::setEncoding( $_db) ;
        }
        if ( self::$driver[$_db] == "mysql") {
            return mysqli_real_escape_string( self::$db[$_db], $_str) ;
        } else if ( self::$driver[$_db] == "mssql") {
            return str_replace( "'", "''", $_str) ;
        } else {
            return $_str ;
        }
    }

    /**
     * dump a dictionary for all tables in the database called mas_sys associated with the
     * alias 'sys'. This might be done nicer, as it's just a development feature we can live with
     * the simple coding.
     */
    static	function	showDict() {
        $sqlResult	=	mysqli_query( self::$db["sys"], "SHOW TABLES FROM mas_sys AS TableName") ;
        while ( $row = mysqli_fetch_assoc( $sqlResult)) {
            foreach ( $row as $i => $v) {
                error_log( "Type.......: " . $v["TableName"]) ;
                $subRes	=	mysqli_query( self::$db["def"], "SHOW COLUMNS FROM " . $v . " ") ;
                while ($row = mysqli_fetch_assoc( $subRes)) {
                    $v1	=	$row['Type'] ;
                    error_log( "Type.......: " . $v1) ;
                    $d	=	explode( "(", $v1) ;
                    error_log( $d[0]) ;
                    switch ( $d[0]) {
                        case	"date"	:
                            $length	=	10 ;
                            break ;
                        case	"double"	:
                        case	"float"	:
                            $length	=	9 ;
                            break ;
                        default	:
                            $d1	=	explode( ")", $d[1]) ;
                            $length	=	intval( $d1[0]) ;
                            break ;
                    }
                    error_log( "Length.....: " . $length) ;
                }
            }
        }
    }
    /**
     * dump a list of registered databases
     * @param   string  $_alias     alias of the connection to be used
     * @return  void
     */
    static	function	__dump( $_alias="") {
        if ( $_alias == "") {
            foreach ( self::$dbHost as $alias => $val) {
                error_log( "Alias ............ : " . $alias) ;
                error_log( "....dbHost ....... : " . self::$dbHost[ $alias]) ;
                error_log( "....dbUser ....... : " . self::$dbUser[ $alias]) ;
                error_log( "....dbPasswd ..... : " . self::$dbPasswd[ $alias]) ;
                error_log( "....dbName ....... : " . self::$dbName[ $alias]) ;
                error_log( "....driver ....... : " . self::$driver[ $alias]) ;
                error_log( "....prefix ....... : " . self::$prefix[ $alias]) ;
                error_log( "....serverCharset  : " . self::$serverCharset[ $alias]) ;
                error_log( "....queryBaseName  : " . self::$queryBaseName[ $alias]) ;
            }
        } else {
            $alias	=	$_alias ;
            error_log( "Alias ............ : " . $alias) ;
            error_log( "....dbHost ....... : " . self::$dbHost[ $alias]) ;
            error_log( "....dbUser ....... : " . self::$dbUser[ $alias]) ;
            error_log( "....dbPasswd ..... : " . self::$dbPasswd[ $alias]) ;
            error_log( "....dbName ....... : " . self::$dbName[ $alias]) ;
            error_log( "....driver ....... : " . self::$driver[ $alias]) ;
            error_log( "....prefix ....... : " . self::$prefix[ $alias]) ;
            error_log( "....serverCharset  : " . self::$serverCharset[ $alias]) ;
            error_log( "....queryBaseName  : " . self::$queryBaseName[ $alias]) ;
        }
    }
}
?>