<?php
/**
 * WebPage.php Base Class of any type of Page object
 *
 * WebPage provides an interface towards the basic data for any single web page showing up
 * in the front-end of openEISS.
 * The construtcion of a web page always starts with the construction and execution of the "Page" object
 * (see: templates directory).
 *
 * A WebPage can have either of the following types (see: MySQL table Options):
 *
 * Type Id.		Type description
 *  3			Simple content as stored in the Fulltext attribute of the "WebPage" database object
 *  6			Simple content as referenced by the TargetURL attribute of the "WebPage" database object
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * WebPage - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BWebPage which should
 * not be modified.
 *
 * @package Application
 * @subpackage WebPage
 */

class	WebPage	extends	AppObject	{
	/*
	 * The constructor can be passed an ArticleNr (WebPageNo), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_name='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_name')") ;
		parent::__construct( "WebPage", "WebPageNo") ;
		if ( $_name != "") {
			$this->setName( $_name) ;
		} else {
		}
		FDbg::end() ;
	}
	/**
	 *
	 * @param string $_name
	 */
	function	setName( $_name='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_name')") ;
		if ( strlen( $_name) > 0) {
			$this->Name	=	$_name ;
			$this->reload() ;
			if ( ! $this->isValid()) {
				$this->fetchFromDbWhere( [ "Name = '" . $_name . "' "]) ;
			}
		} else {
		}
		FDbg::end() ;
	}
	/**
	 * add( $_key, $_id, $_val...)
	 *
	 * this method automatically creates 'untranslated' entries in the table Texte.
	 * w/o these basic entries the site generation would fail
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_name')") ;
		$newTexte	=	new Texte() ;
		FDbg::dumpL( 0x01000000, "WebPage::add(...)") ;
		try {
			$this->getFromPostL() ;
			$this->WebPageNo	=	$_key ;
			$this->storeInDb() ;
			$newTexte->Name	=	"Name" ;
			$newTexte->RefNr	=	$this->WebPageNo ;
			$newTexte->Volltext	=	$this->Name ;
			$newTexte->Sprache	=	"de" ;
			$newTexte->storeInDb() ;
			$newTexte->Sprache	=	"en" ;
			$newTexte->storeInDb() ;
			$newTexte->Sprache	=	"es" ;
			$newTexte->storeInDb() ;
			$newTexte->Sprache	=	"fr" ;
			$newTexte->storeInDb() ;
			$newTexte->Sprache	=	"nl" ;
			$newTexte->storeInDb() ;
		} catch ( Exception $e) {
			FDbg::abort() ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 * getList
	 *
	 * return a 'reply' containing a list of WapPage objects.

	 * @var Reply	reply object
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
			case	""	:
 			$myObj	=	new FDbObject( "WebPage") ;				// no specific object we need here
 			if ( isset( $_POST['StartRow'])) {
 				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
 			}
 			$_webPageNoCrit	=	$sCrit ;
 			$filter	=	"( WebPageNo like '%" . $_webPageNoCrit . "%') " ;
 			/**
 			*
 			*/
 			$myQuery	=	$myObj->getQueryObj( "Select") ;
 			$myQuery->addWhere( [ $filter]) ;
 			$myQuery->addOrder( ["WebPageNo"]) ;
 			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
 			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
}

?>
