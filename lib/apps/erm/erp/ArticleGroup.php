<?php
/**
 * BDocument.php Base class for PDF-format printed matters
 *
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * ArticleGroup - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArticleGroup which should
 * not be modified.
 *
 * @package Application
 * @subpackage Article
 */

class	ArticleGroup	extends	AppObjectERM	{
	private	$tmpArticleGroupItem ;
	/*
	 * The constructor can be passed an ArticleNr (ArticleGroupNo), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_artGrNr='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_artGrNr')") ;
		parent::__construct( "ArticleGroup", "ArticleGroupNo") ;
		if ( strlen( $_artGrNr) > 0) {
			$this->setArticleGroupNo( $_artGrNr) ;
		} else {
		}
		FDbg::end() ;
	}

	/**
	 *
	 */
	function	setArticleGroupNo( $_artGrNr='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_artGrNr')") ;
		if ( strlen( $_artGrNr) > 0) {
			$this->ArticleGroupNo	=	$_artGrNr ;
			$this->reload() ;
		} else {
		}
		FDbg::end() ;
	}

	/**
	 * Setzen der Schluessels.
	 *
	 * Setzt die Artikelnummer des Artikels und versucht ggf. diesen Artikel aus der Db zu laden.
	 *
	 * @param string $_artikelNr='' Artikelnummer
	 * @return void
	 */
	function	setKey( $_key) {
		if ( strlen( $_key) > 0) {
			$this->setArticleGroupNo( $_key) ;
		} else {
		}
	}

	/**
	 * Setzen der Schluessels.
	 *
	 * Setzt die Artikelnummer des Artikels und versucht ggf. diesen Artikel aus der Db zu laden.
	 *
	 * @param string $_artikelNr='' Artikelnummer
	 * @return void
	 */
	function	getKey() {
		return $this->ArticleGroupNo ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @throws Exception
	 * @return string
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$newTexte	=	new Texte() ;
		try {
			$this->_newKey( 8, "80200000", "80299999") ;
			$myKey	=	$this->ArticleGroupNo ;
			$this->getFromPostL() ;
			$this->ArticleGroupNo	=	$myKey ;
			$this->updateInDb() ;
			$newTexte->Name	=	"ArticleGroupName" ;
			$newTexte->RefNr	=	$this->ArticleGroupNo ;
			$newTexte->Volltext	=	$this->ArticleGroupName ;
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
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 */
	function	setImage( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 *
		 */
		$elem	=	explode( ".", $this->ArticleGroupNo) ;
		$path1	=	$this->path->Images . "/" ;
		$path2	=	"_AG/" ;
		$file	=	$this->ArticleGroupNo ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", "storage path := '$path2'") ;
		$fn	=	( isset( $_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", print_r($_FILES, true)) ;
		if ( $fn) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", "fn is set") ;
			//			file_put_contents(
//				'uploads/' . $fn,
//				file_get_contents('php://input')
//			);
			FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", "$fn uploaded") ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", "fn is * NOT * set") ;
			$idx	=	0 ;
			foreach ( $_FILES as $idx => $data) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", "ImageName['$idx']") ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", print_r( $data, true)) ;
				$filename	=	$path1 . $path2 . $file . "-" . sprintf( "%03d", $idx) ;
				$filenameWM	=	$path1 . $path2 . "wm_" . $file . "-" . sprintf( "%03d", $idx) ;
				$imageReference	=	$path2 . "wm_" . $file . "-" . sprintf( "%03d", $idx) ;
				switch ( $data["type"]) {
				case	"image/png"	:
					$filename	.=	".png" ;
					$filenameWM	.=	".png" ;
					break ;
				case	"image/jpeg"	:
					$filename	.=	".jpg" ;
					$filenameWM	.=	".jpg" ;
					break ;
				}
				$imageReference	.=	".jpg" ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", "$filename") ;
				if (move_uploaded_file( $data["tmp_name"], $filename)) {
					echo "File is valid, and was successfully uploaded.<br/>\n";
					/**
					 * create the watermarked (embossed comment) files
					 */
					$sysCmd	=	"cd " . $this->path->Images . "; export PATH=\$PATH:/opt/local/bin ; "
							.	"/opt/local/bin/convert " . $filename . " "
							.	"-font Arial -pointsize 40 "
							.	"-draw \"gravity southwest fill black text 0,12 'Copyright: flaschen24.eu' fill white text 1,11 'Copyright: flaschen24.eu' \" " . $filenameWM . " " ;
					error_log( "system command: '" . $sysCmd . "'") ;
					system( $sysCmd, $res) ;
					error_log( $res) ;
					/**
					 * create thumb nails and various other formats
					 */
					$sysCmd	=	"cd " . $this->path->Images . "; export USER=_www ; make " ;
					error_log( "system command: '" . $sysCmd) ;
					system( $sysCmd, $res) ;
					error_log( $res) ;
					/**
					 *
					 */
					$this->ImageReference	=	$imageReference ;
					$this->updateColInDb( "ImageReference") ;
				} else {
					echo "Possible file upload attack!<br/>\n";
				}
			}
		}
		FDbg::end() ;
		return $reply ;
	}
	function	delPos( $_key="", $_id=-1, $_val="", $reply=null) {
		try {
			$myArticleGroupItem	=	new ArticleGroupItem() ;
			$myArticleGroupItem->setId( $_id) ;
			$myArticleGroupItem->removeFromDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getTableArticleGroupItemAsXML() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getXMLString()
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->className ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * #
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	renumArticleGroup( $_key="", $_id=-1, $_val="") {
		if ( "80200000" <= $this->ArticleGroupNo && $this->ArticleGroupNo <= "80299999") {
			$e	=	new exception( "ArticleGroup.php::ArticleGroup::renumArticleGroup(...): ArticleGroupNo already in range!") ;
			error_log( $e->getMessage()) ;
			throw $e ;
		}
		$newArticleGroupNo	=	$this->newKeyKO( 8, "80200000", "80299999") ;
		if ( strlen ( $newArticleGroupNo) >= 7) {
			$count	=	FDb::getCount( "FROM $this->className WHERE ArticleGroupNo = '$newArticleGroupNo' ") ;
			if ( $count != 0) {
				throw new Exception( "ArticleGroup.php::ArticleGroup::renumArticleGroup( '$_key', $_id, '$_val'): new article group no. already in use! [#:$count]") ;
			}
			try {
				FDb::query( "UPDATE ArticleGroupItem SET CompArticleGroupNo = '$newArticleGroupNo' WHERE CompArticleGroupNo = '$this->ArticleGroupNo' ") ;
				FDb::query( "UPDATE ArticleGroupItem SET ArticleGroupNo = '$newArticleGroupNo' WHERE ArticleGroupNo = '$this->ArticleGroupNo' ") ;
				FDb::query( "UPDATE ProdGrComp SET CompArticleGroupNo = '$newArticleGroupNo' WHERE CompArticleGroupNo = '$this->ArticleGroupNo' ") ;
				FDb::query( "UPDATE ArticleGroup SET ArticleGroupNo = '$newArticleGroupNo' WHERE ArticleGroupNo = '$this->ArticleGroupNo' ") ;
				$this->ArticleGroupNo	=	$newArticleGroupNo ;
				$this->reload() ;
			} catch ( Exception $e) {
				error_log( "ArticleGroup.php::ArticleGroup::renumArticleGroup( '$_key', $_id, '$_val'): exception '" . $e->getMessage() . "'!") ;
			}
		} else {
			throw new Exception( "ArticleGroup.php::ArticleGroup::renumArticleGroup( '$_key', $_id, '$_val'): new article group no. is too short!") ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @param unknown_type $_digits
	 */
	function	_newKey( $_digits=8, $_nsStart="00000000", $_nsEnd="99999999") {
		parent::newKey( 8, "80200000", "80299999") ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
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
			$myObj	=	new FDbObject( "ArticleGroup") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			/**
			*
			*/
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addOrder( ["ArticleGroupNo"]) ;
			$filter1	=	"( ArticleGroupName like '%" . $sCrit . "%') " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"ArticleGroupItem"	:
			$myObj	=	new FDbObject( "ArticleGroupItem", "ArticleGroupNo", "def", "v_ArticleGroupItemList") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( ArticleGroupNo = '" . $this->ArticleGroupNo . "') " ;
			$filter2	=	"( ProductGroupName like '%" . $sCrit . "%' OR ArticleGroupName  like '%" . $sCrit . "%' OR ArticleDescription1  like '%" . $sCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( [ "ItemNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "ArticleGroupItem") ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
}
?>
