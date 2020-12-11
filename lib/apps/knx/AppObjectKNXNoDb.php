<?php
/**
 * AppObject - Application Object for ERM ( Enterprise Resource Management )
 *
 * Base class for all objects which have
 *
 * @author [wimteccgen] Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcAppERPBaseObjects
 * @filesource
 */
/**
 * AppObject - Application Object
 *
 * @package wtcAppERPBaseObjects
 * @subpackage AppObjects
 */
abstract	class	AppObjectKNXNoDb	extends	AppObject	{
	static	$const ;
	/**
	 *
	 */
	function	__construct( $_className, $_keyCol, $_db="def", $_tableName="") {
	}
	/**
	 * getXMLF( $_className)
	 *
	 * Return a string with the XML representation of THIS object. If $_className is specified
	 * this name will be used as the classname instead of the class given during instantiation.
	 * This method uses the php::DOMDocument to construct the XML object.

	 * @param	DOMDocument 		DOCDocument, must exist
	 * @param
	 * @return	string				XML representation of the object
	 */
	function	_importXML( $_doc, $_node, $_attribs=false) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", ".......................: " . $_node->tagName) ;
		$myNodes	=	$_node->childNodes ;
		$className	=	"" ;
//		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "sub-nodes available....: " . $myNodes->length) ;
		if ( $_attribs) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "--> will check attributes ...") ;
			if ( $_node->attributes->length > 0) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "--> there ARE attributes ...") ;
				for ( $i=0 ; $i<$_node->attributes->length ; $i++) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "--> there IS AN attribute " . $_node->attributes->item( $i)->nodeName) ;
					if ( $_node->attributes->item( $i)->nodeName == "Id") {
						$attr	=	str_replace( "KNX", "", $this->className) . $_node->attributes->item( $i)->nodeName ;
					} else {
						$attr	=	$_node->attributes->item( $i)->nodeName ;
					}
					$this->$attr	=	$_node->attributes->item( $i)->nodeValue ;
				}
			}
		}
		foreach ( $myNodes as $node) {
			if ( $node->nodeType == 1) {
				$attr	=	$node->tagName ;
				if ( isset( $this->$attr)) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "--> " . $node->tagName . " := " . $node->firstChild->textContent) ;
					$this->$attr	=	$node->firstChild->textContent ;
				} else {
					$className	=	"KNX" . $attr ;
					$myObj	=	new $className() ;
					$myObj->_importXML( $_doc, $node, $_attribs) ;
				}
			} else {
//				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "--> " . $node->nodeType) ;
			}
		}
		$this->Imported	=	1 ;
		FDbg::end() ;
	}
}

?>
