<?php
/**
 * Magical and central piece of the openEISS WebShop
 *
 * Die zentrale Tabelle die den Aufbau des Web Shops bestimmt ist 'ProductGroup'. Folgende Spaltyen in 'ProductGroup' haben signifikante Bedeutung fuer
 * die STruktur
 *	Eigene Seite
 *		=0	fuer diese ProduktGruppe wird keine eigene Seite erzeugt, d.h. auf diese Seite darf daher auch kein Link erzeugt werden
 *		=1	diese ProduktGruppe hat eine eigene, statische Seite
 *
 *	Seiten Typ
 *		=0	dies ist reserviert fuer die 'root' Seite des Web Shops
 *		=1	eine normale, automatsche Seite
 *		=2	dies ist eine statische Seite, d. eine HTML Seite die ausschliesslich manuell gepflegt wird. Im Menu wird ein Eintrag
 *			erzeugt der auf 'TargetURL' zeigt.
 *		=3	dynamische Seite (.php) mit dynamischem Inhalt (aus html_man)
 *			dies ist eine automatisch generierte PHP-Seite die eine 'include' Anweisung der Form include( 'exec_'<ZielUrl>.php);
 *			beinhaltet.
 *		=4	statische Seite (.html) mit statischem Inhalt (aus html_code)
 *
 *	Level
 *		=0	reserviert fuer die 'root' Seite
 *		=1	Uebersichtsseite
 *		=2	reserviert fuer die 'root' Seite
 *		=3	reserviert fuer die 'root' Seite
 *		=4	reserviert fuer die 'root' Seite
 *
 */
/**
 * get my personal configuration data
 */
include( "ini.php") ;
/**
 * we do not servce "Bilder" through this index.php / index.html
 * bail out as quickly as possible
 */
$uri	=	$_SERVER['REQUEST_URI'] ;
if ( ! strpos( $uri, "Bilder") === false || ! strpos( $uri, "pdf") === false || ! strpos( $uri, "jpg") === false) {
	error_log( "index.php::*::main: bailing out on access to not-existing resources" . "---> '" . $uri."' ") ;
	die() ;
}
error_log( basename( __FILE__) . "::" . __CLASS__ . "::" . __METHOD__ . "LINE: " . __LINE__) ;
/**
 *
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../../../Config/config.inc.php") ;
error_log( basename( __FILE__) . "::" . __CLASS__ . "::" . __METHOD__ . "LINE: " . __LINE__) ;
/**
 *
 */
$lineEnd	=	"\n" ;
$mySysConfig	=	EISSCoreObject::__getSysConfig() ;
$myAppConfig	=	EISSCoreObject::__getAppConfig() ;
/**
 *
 */
error_log( basename( __FILE__) . "::" . __CLASS__ . "::" . __METHOD__ . "LINE: " . __LINE__) ;
$mySysConfig->classId	=	"shop.wimtecc.de.local" ;
$myDb	=	$mySysConfig->classId ;
error_log( basename( __FILE__) . "::" . __CLASS__ . "::" . __METHOD__ . "LINE: " . __LINE__) ;
$myAppConfig->addFromAppDb( $mySysConfig->classId) ;
/**
 *
 */
error_log( basename( __FILE__) . "::" . __CLASS__ . "::" . __METHOD__ . "LINE: " . __LINE__) ;
FDbg::setApp( "apps/erm/shop.php") ;
FDbg::begin( 1, "index.php", "main", "main", "(behind require_once(...))") ;
if ( isset( $_SERVER["REDIRECT_STATUS"]))
	$httpStatus	=	$_SERVER["REDIRECT_STATUS"] ;
else
	$httpStatus	=	-1 ;
/**
 * log the client access to database for later evaluation
 */
error_log( basename( __FILE__) . "::" . __CLASS__ . "::" . __METHOD__ . "LINE: " . __LINE__) ;
$myClientLog	=	new FDbObject( "ClientLog") ;
$myClientLog->HttpStatus	=	$httpStatus ;
$myClientLog->RemoteAddr	=	$_SERVER['REMOTE_ADDR'] ;
$myClientLog->CalledURI	=	$_SERVER['REQUEST_URI'] ;
if ( isset( $_SERVER['HTTP_REFERER']))
	$myClientLog->Referer	=	$_SERVER['HTTP_REFERER'] ;
else
	$myClientLog->Referer	=	"---undefined---" ;
$myClientLog->UserAgent	=	$_SERVER['HTTP_USER_AGENT'] ;
$myClientLog->AcceptLang	=	$_SERVER['HTTP_ACCEPT_LANGUAGE'] ;
$myClientLog->storeInDb() ;
unset( $myClientLog) ;
/**
 *
 */
error_log( "From: '" . $_SERVER['REMOTE_ADDR'] . "' '" . $_SERVER['HTTP_USER_AGENT'] . "' '" . $_SERVER['REQUEST_URI'] ."' ") ;
error_log( "HTTP-Status: " . $httpStatus) ;
error_log( "Server name: " . $_SERVER['SERVER_NAME']) ;
/**
 * pull the GET variables out of the request
 * and store'm as POST variables
 */
EISSCoreObject::dumpGET() ;
EISSCoreObject::dumpPOST() ;
/**
 * pull the GET variables out of the request
 * and store'm as POST variables
 */
$pairs	=	explode( "&", file_get_contents("php://input"));
foreach ( $pairs as $pair) {
	FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "working on url pair '" . $pair . "' ") ;
	$nv	=	explode( "=", $pair);
	$name	=	urldecode( $nv[0]);
	FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "    +--> name '" . $name . "' ") ;
	if ( isset( $nv[1])) {
		$value	=	urldecode( $nv[1]);
		$_POST[ $name]	=	$value;
	}
}
/**
 * pull the GET variables out of the url
 */
$urlps	=	explode("?", $_SERVER['REQUEST_URI']);
$coreURL	=	$urlps[0] ;				// remember this one for complex URL analysis
if ( count( $urlps) > 1) {
	$pairs	=	explode( "&", $urlps[1]);
	foreach ( $pairs as $pair) {
		FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "working on url pair '" . $pair . "' ") ;
		$nv	=	explode( "=", $pair);
		$name	=	urldecode( $nv[0]);
		$value	=	urldecode( $nv[1]);
		$_GET[ $name]	=	$value;
	}
}
/**
 *
 */
$uri	=	$_SERVER['REQUEST_URI'] ;
if ( $_SERVER['SERVER_NAME'] == "www.modis-gmbh.de") {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://www.modis-gmbh.eu".$uri);
	exit();
} else if ( $_SERVER['SERVER_NAME'] == "www.pasco-scientific.de") {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://www.modis-gmbh.eu".$uri);
	exit();
} else if ( $_SERVER['SERVER_NAME'] == "www.pasco-scientific.eu") {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://www.modis-gmbh.eu".$uri);
	exit();
} else {
	header("HTTP/1.1 200 OK");
}
if ( $httpStatus == 401) {
	$uri	=	"/httpFehler401" ;
} else if ( $httpStatus == 403) {
	$uri	=	"/httpFehler401" ;
}

/**
 * disassembly of URI in order to figure out what we need to do:
 * URIs
 * http:/fqdn/		show catalog page
 * http:/fqdn/index.html
 * http:/fqdn/<product group name>
 * http:/fqdn/<page number>
 */
if ( $uri == "/") {
	$uri	=	"/index.html" ;	// if nothing specified => index.html
}
FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "stripped uri reads '" . $uri . "' ") ;
if ( strncmp( $uri, "/index.html", 11) == 0) {
	FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "(1) standard behaviour for '$uri'") ;
	$myUri	=	$uri ;
	$webPage	=	"CatalogData" ;
} else if ( strncmp( $uri, "/index.php", 10) == 0) {
	FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "(2) standard behaviour for '$uri'") ;
	$myUri	=	$uri ;
	if ( isset( $_GET[ 'webPage'])) {
		$webPage	=	$_GET[ 'webPage'] ;
	} else {
		$webPage	=	"CatalogData" ;
	}
} else {
	FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "uri is not as simple as 'index.html' but reads '" . $uri . "' ") ;
	$webPage	=	"CatalogData" ;
	/**
	 * Get the URI string in which the sequences with percent (%) signs followed by two hex digits have been replaced with literal characters.
	 */
	$myUri	=	rawurldecode( substr( $coreURL, 1)) ;		// remove this / and decode the URL stuff
	FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "stripped uri reads '" . $myUri . "' ") ;
	/**
	 *
	 */
	/**
 	 * try to remove the .html or .php suffix (and everythign else which might be there)
	 */
	FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "stripped myUri reads '" . $myUri . "' ") ;
	$parts	=	explode( ".", $myUri) ;
	$nrParts	=	count( $parts) ;
	if ( $nrParts > 1) {
		$myBasePage	=	$parts[0] ;
		if ( $parts[ $nrParts-1] == "html" || $parts[ $nrParts-1] == "php") {
			unset( $parts[ $nrParts-1]) ;
			$myUri	=	implode( ".", $parts) ;
		}
	} else {
		$myBasePage	=	$parts[0] ;
	}
	/*
	 * test what ist most appropriate for this data
	 */
//error_log( "myUri := '$myUri'") ;
	$myWebPage	=	new WebPage() ;
	$myProductGroup	=	new ProductGroup() ;
	$myArticle	=	new Article() ;
//	if ( $myProductGroup->_firstFromDb( " ProductGroupNo = '$myUri' OR ProductGroupNameStripped = '$myUri' ")) {
	if ( $myProductGroup->_firstFromDb( " ProductGroupNo = '$myBasePage' OR ProductGroupNameStripped = '$myBasePage' ")) {
		FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "target identified as ProductGroup") ;
		if ( $myProductGroup->TargetURL != "") {
			$_GET['ziel']	=	$myProductGroup->TargetURL ;
		} else {
			$_GET['prodGrNo']	=	$myProductGroup->ProductGroupNo ;
		}
	} else 	if ( $myProductGroup->_firstFromDb( " ProductGroupName like '%%".$myUri."%%' ")) {
		FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "target identified as close to ProductGroup") ;
		$_GET['prodGrNo']	=	$myProductGroup->ProductGroupNo ;
	} else 	if ( $myWebPage->_firstFromDb( " Name = '".$myBasePage."' ")) {
		FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "target identified as WebPage") ;
		$_GET['webPage']	=	$myWebPage->Name ;
		$webPage	=	$_GET[ 'webPage'] ;
	} else {
		$parts	=	explode( "/", $myUri) ;
		if ( count( $parts) >= 2) {
			$prodGrNo	=	$parts[0] ;
			$articleNo	=	$parts[1] ;
			if ( $myProductGroup->setKey( $prodGrNo)) {
				if ( $myArticle->setKey( $articleNo)) {
					FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "target identified as Article") ;
					$_GET['prodGrNo']	=	$prodGrNo ;
					$_GET['articleNo']	=	$articleNo ;
				} else {
					FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "target not identified, will treat as 'Search[1]'") ;
					$_GET['webPage']	=	"MySearch" ;
					$webPage	=	$_GET[ 'webPage'] ;
					$_GET['action']	=	"search" ;
					$_POST['searchTerm']	=	$myUri ;
				}
			} else {
				FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "target not identified, will treat as 'Search[2]'") ;
				$_GET['webPage']	=	"MySearch" ;
				$webPage	=	$_GET[ 'webPage'] ;
				$_GET['action']	=	"search" ;
				$_POST['searchTerm']	=	$myUri ;
			}
		} else {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "index.html", "main", "*", "target not identified, will treat as 'Search[3]'") ;
			$_GET['webPage']	=	"MySearch" ;
			$webPage	=	$_GET[ 'webPage'] ;
			$_GET['action']	=	"search" ;
			$_POST['searchTerm']	=	$myUri ;
		}
	}
}
/**
 * Defaultwerte setzen fuer den Fall, dass keine Kommandozeilen Parameter gesetzt werden
 *
 *	prodGr	=	%		Alle Produktgruppen
 *	lang	=	'de'	Sprache: deutch (alternativ: en, es)
 *	country	=	'de'	Markt: Deutschland (alternativ: cl)
 */
$lang	=	'de' ;
$country	=	'de' ;
/**
 * Check which kind of page we need to generate
 */
$myProductGroup	=	new ProductGroup() ;
if ( isset( $_GET['articleNo'])) {
	$articleNo	=	$_GET['articleNo'] ;
} else {
	$articleNo	=	"" ;
}
$artGrNr	=	"" ;
if ( isset( $_GET['webPage'])) {
	$webPageNo	=	$_GET['webPage'] ;
}
if ( isset( $_GET['prodGrNo'])) {
	$myprodGrNo	=	$_GET['prodGrNo'] ;
	if ( ! $myProductGroup->setKey( $myprodGrNo)) {
		$prodGrNo	=	"00000000" ;
	} else {
		$prodGrNo	=	$myProductGroup->ProductGroupNo ;
	}
} else if ( isset( $_GET['ziel'])) {
	$ziel	=	$_GET['ziel'] ;
	$myProductGroup->_firstFromDb( "TargetURL = '$ziel' ") ;
	if ( ! $myProductGroup->_valid) {
		$myProductGroup->fetchFromDbWhere( "WHERE ProductGroupName = '$ziel' ") ;
		if ( ! $myProductGroup->_valid) {
			$prodGrNo	=	"00000000" ;
		} else {
			$prodGrNo	=	$myProductGroup->ProductGroupNo ;
		}
	} else {
		$prodGrNo	=	$myProductGroup->ProductGroupNo ;
	}
} else {
	$prodGrNo	=	"00000000" ;
}
error_log( "(1)------------------------------------------------------------------------------------------------------------") ;
//error_log( $mySysConfig->dFump( "mySysConfig.")) ;
error_log( "(2)------------------------------------------------------------------------------------------------------------") ;
//error_log( $myAppConfig->dump( "myAppConfig.", true)) ;
error_log( "(3)------------------------------------------------------------------------------------------------------------") ;
$myWebPage	=	new WebPage( $webPage) ;
error_log( sprintf( "myWebPage->isValid() := %d", $myWebPage->_valid)) ;
//die() ;
/**
 * Verbindung zur Datenbank herstellen
 */
if ( $myWebPage->_valid) {
	/**
	 *
	 */
	FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), "*", "main()", "Login received;") ;
	foreach ( $_POST as $_name => $_val) {
		error_log( $_name . " = " . $_val) ;
	}
	if ( isset( $_GET[ "CustomerNo"])) {
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), "*", "main()", "Login received;") ;

	}
	/**
	 *
	 */
	$myObj	=	new $myWebPage->Template() ;
	$xmldoc	=	$myObj->go( $myWebPage, $prodGrNo, $articleNo) ;

	$xsl	=	$myObj->get( $myWebPage->Stylesheet, "r");

	if ( $xsl !== false) {
		$xsldoc	=	DOMDocument::loadXML( $xsl);
		$proc	=	new XSLTProcessor();
		$proc->registerPHPFunctions();
		$proc->importStyleSheet( $xsldoc);
		$buffer	=	$proc->transformToXML( $xmldoc);
	} else {
		$buffer	=	"even more uuuppppsssss ......." ;
	}

error_log( $buffer) ;

	echo $buffer ;
} else {
	echo "Uuups ..." ;
}

FDbg::end( 1, "index.php", "main", "main") ;

	function	getCatalogXML(  $_doc, $_prodGrNo="", $_lang="de", $_country="de", $_subBuf=null, $_subProductGroupNo="") {
		FDbg::begin( 2, basename( __FILE__), __CLASS__, __METHOD__."( '$_webPageName', '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		$resNode	=	$_doc->createElement( "CatalogData") ;
		/**
		 *
		 */
		$rootProductGroup	=	new ProductGroup( $_prodGrNo) ;
		$productGroup	=	new ProductGroup( $_prodGrNo) ;
		$myTexte	=	new Texte() ;
		/**
		 * generate the basic artikel data
		 */
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_webPageName', '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName'): try-ing") ;
		try {
			$prodGrNode	=	$rootProductGroup->_exportXML( $_doc, $resNode) ;
			$prodGrComp	=	new ProductGroupItem() ;
			$prodGrComp->setIterCond( "ProductGroupNo = '" . $productGroup->ProductGroupNo . "' ") ;
			$prodGrComp->setIterOrder( "ItemNo ") ;
			foreach( $prodGrComp as $ndx => $obj) {
				if ( $obj->CompProductGroupNo != "") {
					$prodGr	=	new ProductGroup( $obj->CompProductGroupNo) ;
					$prodGr->_exportXML( $_doc, $prodGrNode) ;
				} else if ( $obj->CompArticleGroupNo != "") {
					$articleGr	=	new ArticleGroup( $obj->CompArticleGroupNo) ;
					$artGrNode	=	$articleGr->_exportXML( $_doc, $prodGrNode) ;
					/**
					 *
					 */
					$artGrComp	=	new ArticleGroupItem() ;
					$artGrComp->setIterCond( "ArticleGroupNo = '" . $articleGr->ArticleGroupNo . "' ") ;
					$artGrComp->setIterOrder( "ItemNo ") ;
					foreach( $artGrComp as $ndx => $obj) {
						if ( $obj->CompProductGroupNo != "") {
//							$artGr	=	new ProductGroup( $obj->CompProductGroupNo) ;
//							$artGr->_exportXML( $_doc, $resNode) ;
//							$newNode	=	$resNode->appendChild( $_doc->createElement( "ProductGroup")) ;
						} else if ( $obj->CompArticleGroupNo != "") {
//							$articleGr	=	new ArticleGroup( $obj->CompArticleGroupNo) ;
//							$articleGr->_exportXML( $_doc, $resNode) ;
//							$newNode	=	$resNode->appendChild( $_doc->createElement( "ArticleGroup")) ;
						} else if ( $obj->CompArticleNo != "") {
							/**
							 *
							 */
							$article	=	new Article() ;
							$article->setKey( $obj->CompArticleNo) ;
							$articleNode	=	$article->_exportXML( $_doc, $artGrNode) ;
							$articleSalesPriceCache	=	new ArticleSalesPriceCache() ;
							$articleSalesPriceCache->setIterCond( "ArticleNo = '" . $obj->CompArticleNo . "' ") ;
//							$articleSalesPriceCache->setIterOrder( "ItemNo ") ;
							foreach( $articleSalesPriceCache as $ndx => $obj) {
								$artileSalesPriceCacheNode	=	$obj->_exportXML( $_doc, $articleNode) ;
							}
						} else {
						}
					}
				} else {
				}
			}
		} catch ( FException $e) {
			error_log( $e) ;
		} catch ( Exception $e) {
			error_log( $e) ;
		}
		FDbg::end( 0) ;
		return $resNode ;
	}

	function	getNavigatorXML(  $_doc, $_prodGrNo="", $_lang="de", $_country="de", $_subBuf=null, $_subProductGroupNo="") {
		FDbg::begin( 2, "Navigator.php", "Navigator", "getNavigatorXML( <DOMDocument>, '$_prodGrNo', '$_lang', '$_country', '<_subBuf>', '$_subProductGroupNo')") ;
		$resNode	=	$_doc->createElement( "ProductGroupTree") ;
		$myTexte	=	new Texte() ;
		$subProductGroup	=	new ProductGroup() ;
		$myProductGroup	=	new ProductGroup( $_prodGrNo) ;
		$myProductGroupItem	=	new ProductGroupItem() ;
		/**
		 * jetzt das menu fuer die linke spalte zusammenbasteln
		 */
		$prodGrItem	=	new ProductGroupItem() ;
		$prodGrItem->setIterCond( "ProductGroupNo = '" . $myProductGroup->ProductGroupNo . "' ") ;
		$prodGrItem->setIterOrder( "ItemNo ") ;
		foreach ( $prodGrItem as $key => $obj) {
			//
			$subProductGroup->setProductGroupNo( $prodGrItem->CompProductGroupNo) ;
			FDbg::trace( 2, "Navigator.php", "Navigator", "writeMenu(...)", "$subProductGroup->ProductGroupNo --> $subProductGroup->ProductGroupName") ;
			if ( $subProductGroup->MenuEntry == 1) {
				/**
				 * PageType's:
				 *	0	= entry page (corresponds to index.html)
				 *	1	= normal survey of product group
				 *	2	=
				 *	3	=
				 *	4	=
				 *	5	= external page in new browser window
				 */
				$active	=	false ;
				if ( $subProductGroup->ProductGroupNo == $_subProductGroupNo && $_subBuf == null) {
					$active	=	true ;
				}
				if ( $subProductGroup->PageType == 1) {
					if ( $subProductGroup->Level < 5) {
						$newNode	=	$resNode->appendChild( $_doc->createElement( "ProductGroup")) ;
						$newNode->setAttribute( "LineType", "1") ;
						$newNode->setAttribute( "ProdGrNo", $subProductGroup->ProductGroupNo) ;
						$newNode->setAttribute( "PageType", $subProductGroup->PageType) ;
						$newNode->setAttribute( "class", "g" . $subProductGroup->Level . ( $active == true ? "a" : "p")) ;
						$newNode->setAttribute( "href", "/".$subProductGroup->ProductGroupNameStripped.".html") ;
						$newNode->setAttribute( "text", $subProductGroup->ProductGroupName) ;
					}
				} else if ( $subProductGroup->PageType == 2) {
					$newNode	=	$resNode->appendChild( $_doc->createElement( "ProductGroup")) ;
					$newNode->setAttribute( "LineType", "2") ;
					$newNode->setAttribute( "ProdGrNo", $subProductGroup->ProductGroupNo) ;
					$newNode->setAttribute( "PageType", $subProductGroup->PageType) ;
					$newNode->setAttribute( "class", "g" . $subProductGroup->Level) ;
					$newNode->setAttribute( "href", "/".$subProductGroup->ProductGroupNameStripped.".html") ;
				} else if ( $subProductGroup->PageType == 3 && strlen( $subProductGroup->Condition) > 0) {
					$newNode	=	$resNode->appendChild( $_doc->createElement( "ProductGroup")) ;
					$newNode->setAttribute( "LineType", "3a") ;
					$newNode->setAttribute( "ProdGrNo", $subProductGroup->ProductGroupNo) ;
					$newNode->setAttribute( "PageType", $subProductGroup->PageType) ;
					$newNode->setAttribute( "class", "g" . $subProductGroup->Level) ;
					$newNode->setAttribute( "href", "/".$subProductGroup->ProductGroupNameStripped.".html") ;
				} else if ( $subProductGroup->PageType == 3) {
					$newNode	=	$resNode->appendChild( $_doc->createElement( "ProductGroup")) ;
					$resNode->setAttribute( "LineType", "3b") ;
					$newNode->setAttribute( "ProdGrNo", $subProductGroup->ProductGroupNo) ;
					$newNode->setAttribute( "PageType", $subProductGroup->PageType) ;
					$newNode->setAttribute( "class", "g" . $subProductGroup->Level) ;
					$newNode->setAttribute( "href", "/".$subProductGroup->ProductGroupNameStripped.".html") ;
				} else if ( $subProductGroup->PageType == 4) {
					$newNode	=	$resNode->appendChild( $_doc->createElement( "ProductGroup")) ;
					$newNode->setAttribute( "LineType", "4") ;
					$newNode->setAttribute( "ProdGrNo", $subProductGroup->ProductGroupNo) ;
					$newNode->setAttribute( "PageType", $subProductGroup->PageType) ;
					$newNode->setAttribute( "class", "g" . $subProductGroup->Level ."a") ;
					$newNode->setAttribute( "href", "/".$subProductGroup->ProductGroupNameStripped.".html") ;
				} else if ( $subProductGroup->PageType == 5) {
					$newNode	=	$resNode->appendChild( $_doc->createElement( "ProductGroup")) ;
					$newNode->setAttribute( "LineType", "5") ;
					$newNode->setAttribute( "ProdGrNo", $subProductGroup->ProductGroupNo) ;
					$newNode->setAttribute( "PageType", $subProductGroup->PageType) ;
					$newNode->setAttribute( "class", "g" . $subProductGroup->Level) ;
					$newNode->setAttribute( "href", "/".$subProductGroup->ProductGroupNameStripped.".html") ;
					$newNode->setAttribute( "text", $subProductGroup->ProductGroupName) ;
				}
				if ( $subProductGroup->ProductGroupNo == $_subProductGroupNo) {
					$newNode->appendChild( $_subBuf) ;
				}
			}
		}
		$myProductGroupItem->fetchFromDbWhere( "CompProductGroupNo = '" . $_prodGrNo . "' ") ;
		if ( $myProductGroupItem->isValid()) {
			if ( $myProductGroupItem->ProductGroupNo != "") {
				$resNode	=	getNavigatorXML( $_doc, $myProductGroupItem->ProductGroupNo, $_lang, $_country, $resNode, $_prodGrNo) ;
			}
		} else {
			$myProductGroup	=	new ProductGroup( "00000000") ;
			$newNode	=	$_doc->createElement( "ProductGroup") ;
			$newNode->setAttribute( "LineType", "1") ;
			$newNode->setAttribute( "ProdGrNo", $myProductGroup->ProductGroupNo) ;
			$newNode->setAttribute( "PageType", $myProductGroup->PageType) ;
			$newNode->setAttribute( "class", "g" . $myProductGroup->Level . "a") ;
			$newNode->setAttribute( "href", "/".$myProductGroup->ProductGroupNameStripped.".html") ;
			$newNode->setAttribute( "text", $myProductGroup->ProductGroupName) ;
			$resNode->appendChild( $newNode) ;
		}
		FDbg::end() ;
		return $resNode ;
	}
?>
