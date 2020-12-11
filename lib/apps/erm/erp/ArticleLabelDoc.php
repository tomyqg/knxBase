<?php
/**
 *
 *
 */
class	ArticleLabelDoc	extends	EISSCoreObject {
	/**
	 *
	 */
	const	ArticleLabel100x50 =	11 ;
	const	ArticleLabel75x25 =	12 ;
	const	ArticleLabel55x25 =	13 ;
	const	ArticleLabel150x100 =	14 ;
	/**
	 *
	 */
	private	$myArticle ;
	private	$myArticleLabel ;
	private	$mySize ;
	/**
	 *
	 */
	function	__construct( $_articleNo, $_size=ArtLabelDoc::ArtLabel100x50, $_lang="de_de") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_articleNo', $_size, '$_lang')") ;
		$this->pdfName	=	$_articleNo . ".pdf" ;
		$this->fullPDFName	=	$this->path->Archive . "ArticleLabel/" . $this->pdfName ;
		$this->myArticle	=	new Article( $_articleNo) ;
		switch ( $_size) {
		case	ArticleLabelDoc::ArticleLabel100x50	:
			$this->myArticleLabel	=	new BDocArtLbl100x50() ;
			$this->mySize	=	$_size ;
			break ;
		case	ArticleLabelDoc::ArticleLabel75x25	:
			$this->myArticleLabel	=	new BDocArtLbl75x25() ;
			$this->mySize	=	$_size ;
			break ;
		case	ArticleLabelDoc::ArticleLabel55x25	:
			$this->myArticleLabel	=	new BDocArtLbl55x25() ;
			$this->mySize	=	$_size ;
			break ;
		case	ArticleLabelDoc::ArticleLabel150x100	:
			$this->myArticleLabel	=	new BDocArtLbl150x100() ;
			$this->mySize	=	$_size ;
			break ;
		}
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	createPDF() {
		$this->myArticleLabel->begin() ;
		/**
		 *
		 */
		switch ( $this->mySize) {
		case	ArticleLabelDoc::ArticleLabel100x50	:
			$this->myArticleLabel->addMyText( $this->myArticle->ArticleDescription1) ;
			$this->myArticleLabel->addMyText( $this->myArticle->ArticleDescription2) ;
			$this->myArticleLabel->addMyText( $this->myArticle->QuantityText) ;
			$this->myArticleLabel->addArticleNr( $this->myArticle->ArticleNo) ;
			break ;
		case	ArticleLabelDoc::ArticleLabel75x25	:
			$this->myArticleLabel->addMyText( $this->myArticle->ArticleDescription1) ;
			$this->myArticleLabel->addArticleNr( $this->myArticle->ArticleNo) ;
			break ;
		case	ArticleLabelDoc::ArticleLabel55x25	:
			$this->myArticleLabel->addMyText( $this->myArticle->ArticleDescription1) ;
			$this->myArticleLabel->addArticleNr( $this->myArticle->ArticleNo) ;
			break ;
		case	ArticleLabelDoc::ArticleLabel150x100	:
			$this->myArticleLabel->addMyText( $this->myArticle->ArticleDescription1) ;
			$this->myArticleLabel->addMyText( $this->myArticle->ArticleDescription2) ;
			$this->myArticleLabel->addMyText( $this->myArticle->QuantityText) ;
			$this->myArticleLabel->addArticleNr( $this->myArticle->ArticleNo) ;
			break ;
		}
		/**
		 *
		 */
		$_pdfName	=	$this->fullPDFName ;
		$this->myArticleLabel->end( $_pdfName) ;
		return $_pdfName ;
	}
	/**
	 *
	 */
	function	cascTokenStart( $_token) {
		echo "Here's my token: " . $_token . " to start \n" ;
	}

	/**
	 *
	 */
	function	cascTokenEnd( $_token) {
		echo "Here's my token: " . $_token . " to end \n" ;
	}

}

?>
