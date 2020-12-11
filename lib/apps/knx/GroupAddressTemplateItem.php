<?php
/**
 * @author		Karl-Heinz Welter <khwelter@icloud.com>
 * @version		0.1
 * @package		GroupAddressTemplate
 * @subpackage	Client
 * @filesource
 */
/**
 * Client - Base Class
 *
 */
class	GroupAddressTemplateItem	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myId="") {
		parent::__construct( "GroupAddressTemplateItem", "Id", "def") ;
		if ( strlen( $_myId) > 0) {
			try {
				$this->setId( $_myId) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
	}
}
?>
