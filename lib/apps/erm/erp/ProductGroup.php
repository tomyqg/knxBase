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
 * ProductGroup - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BProductGroup which should
 * not be modified.
 *
 * @package Application
 * @subpackage ProductGroup
 */

class	ProductGroup	extends	AppObjectERM	{

	private	$tmpProductGroupItem ;

	/*
	 * The constructor can be passed an ArticleNr (ProductGroupNo), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_prodGrNo='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_prodGrNo')") ;
		parent::__construct( "ProductGroup", "ProductGroupNo") ;
		if ( strlen( $_prodGrNo) > 0) {
			$this->setProductGroupNo( $_prodGrNo) ;
		} else {
		}
		FDbg::end() ;
	}
	function	setProductGroupNo( $_prodGrNo='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_prodGrNo')") ;
		if ( strlen( $_prodGrNo) > 0) {
			$this->ProductGroupNo	=	$_prodGrNo ;
			$this->reload() ;
		} else {
		}
		FDbg::end() ;
	}

	/**
	 *
	 * @return CuOrdrPos
	 */
	function	getFirstItem() {
		$this->tmpProductGroupItem	=	new ProductGroupItem() ;
		$this->tmpProductGroupItem->ProductGroupNo	=	$this->ProductGroupItem ;
		$this->tmpProductGroupItem->firstFromDb() ;
		return $this->tmpProductGroupItem ;
	}

	/**
	 *
	 * @return CuOrdrPos
	 */
	function	getNextItem() {
		$this->tmpProductGroupItem->nextFromDb() ;
		return $this->tmpProductGroupItem ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @throws Exception
	 * @return string
	 */
	function	newProductGroup( $_key="", $_id=-1, $_val) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$newTexte	=	new Texte() ;
		try {
			$this->newKey( 8, "80100000", "80199999") ;
			$myKey	=	$this->ProductGroupNo ;
			$this->getFromPostL() ;
			$this->ProductGroupNo	=	$myKey ;
			$this->updateInDb() ;
			$newTexte->Name	=	"ProductGroupName" ;
			$newTexte->RefNr	=	$this->ProductGroupNo ;
			$newTexte->Volltext	=	$this->ProductGroupName ;
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
	 * add(...)
	 *
	 * this method automatically creates 'untranslated' entries in the table Texte.
	 * w/o these basic entries the site generation would fail
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$newTexte	=	new Texte() ;
		try {
			$this->_newKey( 8, "80100000", "80199999") ;
			$myKey	=	$this->ProductGroupNo ;
			$this->getFromPostL() ;
			$this->ProductGroupNo	=	$myKey ;
			$this->updateInDb() ;
			$newTexte->Name	=	"ProductGroupName" ;
			$newTexte->RefNr	=	$this->ProductGroupNo ;
			$newTexte->Volltext	=	$this->ProductGroupName ;
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
		$elem	=	explode( ".", $this->ProductGroupNo) ;
		$path1	=	$this->path->Images . "/" ;
		$path2	=	"_PG/" ;
		$file	=	$this->ProductGroupNo ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "ProductGroup.php", "ProductGroup", "setImage( ...)", "storage path := '$path2'") ;
		$fn	=	( isset( $_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "ProductGroup.php", "ProductGroup", "setImage( ...)", print_r($_FILES, true)) ;
		if ( $fn) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "ProductGroup.php", "ProductGroup", "setImage( ...)", "fn is set") ;
			//			file_put_contents(
//				'uploads/' . $fn,
//				file_get_contents('php://input')
//			);
			FDbg::trace( 1, FDbg::mdTrcInfo1, "ProductGroup.php", "ProductGroup", "setImage( ...)", "$fn uploaded") ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "ProductGroup.php", "ProductGroup", "setImage( ...)", "fn is * NOT * set") ;
			$idx	=	0 ;
			foreach ( $_FILES as $idx => $data) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "ProductGroup.php", "ProductGroup", "setImage( ...)", "ImageName['$idx']") ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "ProductGroup.php", "ProductGroup", "setImage( ...)", print_r( $data, true)) ;
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
				FDbg::trace( 1, FDbg::mdTrcInfo1, "ProductGroup.php", "ProductGroup", "setImage( ...)", "$filename") ;
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
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "ProductGroup.php", "ProductGroup", "updDep( '$_key', $_id, '$_val')") ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Article.php", "Article", "updDep( '$_key', $_id, '$_val')",
		"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
			default	:
				return parent::updDep( $_key, $_id, $_val) ;
				break ;
		}
		FDbg::end() ;
		return $this->getTableDepAsXML( $_key, $_id, $objName) ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::delDep()
	 */
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Article.php", "Article", "delDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
			switch( $_val) {
			default	:
				parent::delDep( $_key, $_id, $_val, $reply) ;
				break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "ProductGroupItem") ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "SubProductGroupItem") ;
		return $ret ;
	}

	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "ProductGroup.php", "ProductGroup", "getAsXML( '$_key', $_id, '$_val')") ;
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
			$myObj	=	new FDbObject( "ProductGroup") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_productGroupNoCrit	=	$sCrit ;
			$filter	=	"( ProductGroupName like '%" . $_customerNoCrit . "%') " ;
			/**
			*
			*/
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter]) ;
			$myQuery->addOrder( ["ProductGroupNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"ProductGroupItem"	:
			$myObj	=	new FDbObject( "ProductGroupItem", "ProductGroupNo", "def", "v_ProductGroupItemList") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( ProductGroupNo = '" . $this->ProductGroupNo . "') " ;
			$filter2	=	"( ProductGroupName like '%" . $sCrit . "%' OR ArticleGroupName  like '%" . $sCrit . "%' OR ArticleDescription1  like '%" . $sCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( [ "ItemNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "ProductGroupItem") ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * #
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	renumProductGroup( $_key="", $_id=-1, $_val="") {
		if ( "80100000" <= $this->ProductGroupNo && $this->ProductGroupNo <= "80199999") {
			$e	=	new exception( "ProductGroup.php::ProductGroup::renumProductGroup(...): ProductGroupNo already in range!") ;
			error_log( $e->getMessage()) ;
			throw $e ;
		}
		$newProductGroupNo	=	$this->newKeyKO( 8, "80100000", "80199999") ;
		if ( strlen ( $newProductGroupNo) >= 7) {
			$count	=	FDb::getCount( "FROM $this->className WHERE ProductGroupNo = '$newProductGroupNo' ") ;
			if ( $count != 0) {
				throw new Exception( "ProductGroup.php::ProductGroup::renumProductGroup( '$_key', $_id, '$_val'): new article group no. already in use! [#:$count]") ;
			}
			try {
				FDb::query( "UPDATE ProductGroupItem SET ItemProductGroupNo = '$newProductGroupNo' WHERE ItemProductGroupNo = '$this->ProductGroupNo' ") ;
				FDb::query( "UPDATE ProductGroupItem SET ProductGroupNo = '$newProductGroupNo' WHERE ProductGroupNo = '$this->ProductGroupNo' ") ;
				FDb::query( "UPDATE ProductGroup SET ProductGroupNo = '$newProductGroupNo' WHERE ProductGroupNo = '$this->ProductGroupNo' ") ;
				$this->ProductGroupNo	=	$newProductGroupNo ;
				$this->reload() ;
			} catch ( Exception $e) {
				error_log( "ProductGroup.php::ProductGroup::renumProductGroup( '$_key', $_id, '$_val'): exception '" . $e->getMessage() . "'!") ;
			}
		} else {
			throw new Exception( "ProductGroup.php::ProductGroup::renumProductGroup( '$_key', $_id, '$_val'): new article group no. is too short!") ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @param unknown_type $_digits
	 */
	function	_newKey( $_digits=8, $_nsStart="00000000", $_nsEnd="99999999") {
		parent::newKey( 8, "80100000", "80199999") ;
	}
	/**
	 *
	 * @param string	$_key
	 * @param int	$_id
	 * @param string	$_val
	 */
	function	_rotImage( $_val="0") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_val')") ;
		$filename	=	$this->PGBildRef ;
		$fullPathname	=	$this->path->Images ;
		$fullFilename	=	$fullPathname . $filename ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "ProductGroup.php", "ProductGroup", "_rotImage( '$_val')",
						"fullPathname  := '$fullPathname'") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "ProductGroup.php", "ProductGroup", "_rotImage( '$_val')",
						"fullFilename  := '$fullFilename'") ;
		if ( chdir( $fullPathname)) {
			if ( $this->server->os == "MacOS") {
				$sysCmd	=	"cd " . $this->path->Images . " ; /opt/local/bin/convert " . $fullFilename . " -rotate " . $_val . " " . $fullFilename ;
				system( $sysCmd) ;
				$sysCmd	=	"cd " . $this->path->Images . " ; export USER=_www ; make " ;
				system( $sysCmd) ;
			} else if ( $this->server->os == "linux") {

			}
		} else {
			mkdir( $fullPathname) ;
		}
		FDbg::end() ;
	}
	/**
	 *
	 * @param string	$_key
	 * @param int	$_id
	 * @param string	$_val
	 */
	function	rotImageCw( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$this->_rotImage( "90") ;
		FDbg::end() ;
		return $this->getXMLComplete( $_key, $_id, $_val) ;
	}
	/**
	 *
	 * @param string	$_key
	 * @param int	$_id
	 * @param string	$_val
	 */
	function	rotImageCCw( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$this->_rotImage( "-90") ;
		FDbg::end() ;
		return $this->getXMLComplete( $_key, $_id, $_val) ;
	}
}

?>
