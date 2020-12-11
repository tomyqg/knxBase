<?php

class	Page	extends	EISSCoreObject	{
	static	$myShopSession	=	null ;
	static	$SearchTermBase	=	"" ;
	static	$SearchTerm	=	"" ;
	static	$myCustomer	=	null ;
	static	$myCustomerContact	=	null ;
	static	$customerValid	=	0 ;
	var	$parent = null ;
	var	$lang ;
	var	$country ;
	/**
	 *
	 */
	function	__construct( $_parent=null, $_lang="de", $_country="de") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <parent>, '$_lang', '$_country')") ;
		parent::__construct( "Page") ;
		self::$myShopSession	=	new ShopSession() ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param string $_webPage		name of the WebPage to be displayed
	 * @param string $_prodGrNo		name of the product group
	 * @param string $_artGrNo		name of the article group
	 * @param string $_articleNo	article number
	 * @param string $_tmplName		name of the required template
	 */
	function	go( $_webPage, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		$xmldoc =       new DOMDocument() ;
		$xmldoc->xmlStandAlone	=	false ;		// force the <?xml version="1.0"> line
		$xmldoc->formatOutput	=	true ;		// make it readable

		$startNode	=	$xmldoc->appendChild( $xmldoc->createElement( "pagedata")) ;
		$refData	=	$startNode->appendChild( $xmldoc->createElement( "refData")) ;
		$refData->setAttribute( "CurrentProdGrNo", $_prodGrNo) ;

		$startNode->appendChild( getNavigatorXML( $xmldoc, $_prodGrNo)) ;
		$contentNode	=	$startNode->appendChild( $xmldoc->createElement( "content")) ;
		$contentNode->appendChild( $xmldoc->createTextNode( $_webPage->Fulltext)) ;

error_log( $xmldoc->saveXML( $startNode)) ;

		return $xmldoc ;
	}
	/**
	 * Search sequence
	 * - current path
	 * - current path + "templates/" + shop.siteName + language + country
	 * - current path + "templates/" + shop.siteName + language
	 * - current path + "templates/" + shop.siteName
	 * - current path + "templates/"
	 *
	 * @param string $_file
	 * @param unknown_type $_mode
	 */
	function	get( $_file, $_mode="r") {
		global	$includeBase ;
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_file', '$_mode')") ;
error_log( ".............:   " . $this->shop->templatesPath) ;
//		$this->lang="en";
		$file	=	@file_get_contents( $_file) ;
error_log( "trying .............:   " . $_file) ;
		if ( $file) {
			FDbg::end( "done") ;
			return $file ;
		}
		$file	=	@file_get_contents( $includeBase."/".$this->shop->templatesPath. $this->shop->siteName . "/" . $this->lang . "/" . $this->country . "/" . $_file, $_mode) ;
error_log( "trying .............:   " . $includeBase."/".$this->shop->templatesPath. $this->shop->siteName . "/" . $this->lang . "/" . $this->country . "/" . $_file) ;
		if ( $file) {
			FDbg::end( "done") ;
			return $file ;
		}
		$file	=	@file_get_contents( $includeBase."/".$this->shop->templatesPath. $this->shop->siteName . "/" . $this->lang . "/" . $_file, $_mode) ;
error_log( "trying .............:   " . $includeBase."/".$this->shop->templatesPath. $this->shop->siteName . "/" . $this->lang . "/" . $_file) ;
		if ( $file) {
			FDbg::end() ;
			return $file ;
		}
		$file	=	@file_get_contents( $includeBase."/".$this->shop->templatesPath. $this->shop->siteName . "/" . $_file, $_mode) ;
error_log( "trying .............:   " . $includeBase."/".$this->shop->templatesPath. $this->shop->siteName . "/" . $_file) ;
		if ( $file) {
			FDbg::end() ;
			return $file ;
		}
		$file	=	@file_get_contents( $includeBase."/".$this->shop->templatesPath. $_file, $_mode) ;
error_log( "trying .............:   " . $includeBase."/".$this->shop->templatesPath. $_file) ;
		if ( $file) {
			error_log( "done .........XX....:   " . $includeBase."/".$this->shop->templatesPath. $_file) ;
			return $file ;
			FDbg::end( "done") ;
		}
		$file	=	@file_get_contents( $includeBase."/".$this->shop->templatesPath."/default/" . $this->lang . "/" . $this->country . "/" . $_file, $_mode) ;
error_log( "trying .............:   " . $includeBase."/".$this->shop->templatesPath."/default/" . $this->lang . "/" . $this->country . "/" . $_file) ;
		if ( $file) {
			FDbg::end() ;
			return $file ;
		}
		$file	=	@file_get_contents( $includeBase."/".$this->shop->templatesPath."/default/" . $this->lang . "/" . $_file, $_mode) ;
error_log( "trying .............:   " . $includeBase."/".$this->shop->templatesPath."/default/" . $this->lang . "/" . $_file) ;
		if ( $file) {
			FDbg::end() ;
			return $file ;
		}
		$file	=	@file_get_contents( $includeBase."/".$this->shop->templatesPath."/default/" . $_file, $_mode) ;
error_log( "trying .............:   " . $includeBase."/".$this->shop->templatesPath."/default/" . $_file) ;
		if ( $file) {
			FDbg::end() ;
			return $file ;
		}
		FDbg::end() ;
		return null ;
	}
}
?>
