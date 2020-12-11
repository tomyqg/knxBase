<?php
/**
 * DataMiner.php - Basic class to retrieve data in a datamining fashion
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
require_once( "base/DbObject.php") ;
require_once( "base/AppObject.php") ;
/**
 * DataMiner - User-Level Klasse
 *
 * This class acts as an interface for data-mining in general.
 * The following core methods are provided.
 *
 *
 * @package Application
 * @subpackage DataMiner
 */
class	DataMiner	extends	EISSCoreObject	{
	public	$objName ;
	/**
	 * __construct
	 * 
	 * Creates an instance of a dataminer for an object of class <$_objName>.
	 *
	 * @param	string	$_objName	class for which a dataminer shall be created
	 */
	function	__construct( $_objName="") {
		parent::__construct( "DataMiner:" . $_objName) ;
		$this->valid	=	true ;			// data-mminer is cvalid upon creation
		$this->_valid	=	$this->valid ;
		$this->objName	=	$_objName ;
	}
	/**
	 * setKey
	 * 
	 * Needed in order to conform to the EISS calling standard. Not really used.
	 * Only returns the validity of the current instance, which should usually be true (means: valid)
	 * 
	 * @param	string	$_key
	 * @param	int		$_id
	 * @param	mixed	$_val
	 * @return	bool	validity
	 */
	function	setKey( $_key="", $_id=-1, $_val="") {
		$this->key	=	$_key ;
		return $this->valid ;
	}
}
?>
