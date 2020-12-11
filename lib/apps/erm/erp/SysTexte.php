<?php

/**
 * SysTexte.php - Specialized sub-class of Texte, table containing system text-blocks in arbitrary langages
 * 
 * The class serves as an interface towards the systemized - and in fact quite versatile - system text object in the
 * database. A system text, like a regular text (therfor derived of ...) is identified by it's name, e.g. CuOrdrEMail
 * (customr order e-mail), an optional reference number and a language.
 * The reference number, however, SHOULD NOT be used for systemized text.
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * requires the WTF Debugger and the Base Class
 */
/**
 * Texte - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BTexte which should
 * not be modified.
 *
 * @package Application
 * @subpackage Basic
 */
class	SysTexte	extends	Texte	{

	/*
	 * construct()
	 * 
	 * assign the name, refNr and lanuage to 'this' object and tries to retrieve it from the database with the
	 * given keys.
	 * If the 'Texte' object can not be found an exception is thrown.
	 * 
	 * @param string $_name 	name of the text to be retrieved
	 * @param string $_refNr	optional reference number of the text to be retrieved
	 * @param string $_sprache	optional language of the text to be retrieved, defaults to de_DE (german _in_ Germany)
	 */
	function	__construct( $_name="", $_refNr="", $_sprache="de_DE") {
		parent::__construct( "SysTexte", "Id") ;
		if ( strlen( $_name) > 0) {
			try {
				$this->setKeys( $_name, $_refNr, $_sprache) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		}
	}
}

?>
