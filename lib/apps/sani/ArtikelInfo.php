<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * HMV_EP - Base Class
 *
 * @package Application
 * @subpackage HMV_EP
 */
class	ArtikelInfo	extends	AppObject	{

	/**
	 *
	 */
	function	__construct() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDBg::end() ;
	}

	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	acList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$a_json = array();
		$a_json_row = array();
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyMediaType	=	Reply::mediaTextJSON ;
		$sCrit	=	"" ;
		if ( isset( $_REQUEST['term']))
			$sCrit	=	$_REQUEST['term'] ;

		/**
		 *
		 */
		$a_json_row["id"]			=	null ;
		$a_json_row["label"]		=	"---GKV Liste---"  ;
		$a_json_row["value"]		=	null ;
		$a_json_row["Source"]		=	"void" ;
		$a_json_row["ArtikelNr"]	=	"-" ;
		$a_json_row["HMVNr"]		=	"" ;
		array_push( $a_json, $a_json_row);
		$il0	=	0 ;
		$myObject	=	new HMV_EP() ;
		$myObject->setIterCond( "HMVNr like '%" . $sCrit . "%' OR Bezeichnung like '%" . $sCrit . "%' ") ;
		foreach ( $myObject as $object) {
			if ( $il0 < 25) {
				$a_json_row["id"]			=	$object->Id ;
				$a_json_row["label"]		=	$object->HMVNr . ", " . $object->Bezeichnung  ;
				$a_json_row["value"]		=	$object->HMVNr ;
				$a_json_row["Source"]		=	"HMVVerzeichnis" ;
				$a_json_row["ArtikelNr"]	=	"-" ;
				$a_json_row["HMVNr"]		=	$object->HMVNr ;
				array_push( $a_json, $a_json_row);
			} else {
				break ;
			}
			$il0++ ;
		}

		/**
		 *
		 */
		$a_json_row["id"]			=	null ;
		$a_json_row["label"]		=	"---Artikel Liste---"  ;
		$a_json_row["value"]		=	null ;
		$a_json_row["Source"]		=	"void" ;
		$a_json_row["ArtikelNr"]	=	"-" ;
		$a_json_row["HMVNr"]		=	"" ;
		array_push( $a_json, $a_json_row);
		$il0	=	0 ;
		$myObject	=	new Artikel() ;
		$myObject->setIterCond( "ArtikelNr like '%" . $sCrit . "%' OR Bezeichnung1 like '%" . $sCrit . "%' ") ;
		foreach ( $myObject as $object) {
			if ( $il0 < 25) {
				$a_json_row["id"]			=	$object->Id ;
				$a_json_row["label"]		=	$object->ArtikelNr . ", " . $object->Bezeichnung1  ;
				$a_json_row["value"]		=	$object->ArtikelNr ;
				$a_json_row["Source"]		=	"Artikel" ;
				$a_json_row["ArtikelNr"]	=	$object->ArtikelNr ;
				$a_json_row["HMVNr"]		=	$object->HMVNr ;
				array_push( $a_json, $a_json_row);
			} else {
				break ;
			}
			$il0++ ;
		}

		/**
		 *
		 */
		$reply->data = json_encode($a_json);
		FDbg::end() ;
		return $reply ;
	}
}
?>
