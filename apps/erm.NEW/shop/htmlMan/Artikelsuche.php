<?php 
/**
 * Articlesuche.php
 *
 * performs:
 *	some basic connectivity tests
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
error_log( "Dumping GET") ;
EISSCoreObject::dumpGET() ;
error_log( "Dumping POST") ;
EISSCoreObject::dumpPOST() ;
/**
 *
 */
$startrow	=	0 ;
if ( isset( $_POST['startrow'])) {
	$startrow	=	$_POST['startrow'] ;
}
echo "<h1>".FTr::tr( "Search result")."</h1>" ;
/**
 * generate the basic artikel data
 */
if ( isset( $_POST['Suchbegriff'])) {
	writeProducts( $_POST['Suchbegriff'], $startrow) ;
} else if ( isset( $_POST['AltSuchbegriff'])) {
	writeProducts( $_POST['AltSuchbegriff'], $startrow) ;
} else {
	writeProducts( "PS-2002", $startrow) ;
}
/**
 *
 * @param unknown_type $_suchbegriff
 * @param unknown_type $_startrow
 */
function	writeProducts( $_suchbegriff, $_startrow) {
	$myProductGroup	=	new ProductGroup() ;
	/**
	 *
	 */
	if ( $_suchbegriff == "") {
		$_suchbegriff	=	"PS-" ;
	}
	if ( $_suchbegriff[0] == "#") {
		$query	=	"FROM Article " ;
		$where	=	"ArticleNo like '" . substr( $_suchbegriff, 1) . "' " ;
		$where	.=	"AND LieferStatus < 9 " ;
	} else {
		$query	=	"FROM Article " ;
		$where	=	"(ArticleBez1 like '%" . $_suchbegriff . "%' " ;
		$where	.=	"OR ArticleBez2 like '%" . $_suchbegriff . "%' " ;
		$where	.=	"OR Volltext like '%" . $_suchbegriff . "%' " ;
		$where	.=	"OR ArticleNo like '%" . $_suchbegriff . "%' " ;
		$where	.=	"OR PhonText like '%" . Phonetics::makePhonetic( $_suchbegriff) . "%') " ;
		$where	.=	"AND ShopArticle > 0 " ;
		$where	.=	"AND LieferStatus < 9 " ;
	}
	$anzahlArticle      =       FDb::getCount( $query . "WHERE " . $where) ;
	echo FTr::tr( "In total #1 articles could be found which match your search criteria '#2'.", array( "%d:".$anzahlArticle, "%s:".$_suchbegriff)) . "<br/>" ;
	/**
	 * @var unknown_type
	 */
	$startrow	=	$_startrow ;
	$rowcount	=	25 ;
	$lowestrow	=	0 ;
	if ( $startrow >= $rowcount)
		$lowerrow	=	$startrow - $rowcount ;
	else
		$lowerrow	=	0 ;
	if ( $startrow < ($anzahlArticle - $rowcount))
		$higherrow	=	$startrow + $rowcount ;
	else
		$higherrow	=	$anzahlArticle - $rowcount ;
	$highestrow	=	$anzahlArticle - $rowcount ;
	$pages	=	ceil( $anzahlArticle / $rowcount) ;
	$pageNr	=	ceil( $startrow / $rowcount) + 1 ;
	echo FTr::tr( "You are on page #1 of #2", array( "%d:".$pageNr, "%d:".$pages)) . "<br/>" ;
//	echo FTr::tr( "Sie sind auf Seite " . sprintf( "%d", ceil( $startrow / $rowcount)+1) . " von " . $pages . " Seiten<br/>\n" ;
	if ( $pages > 1) {
		echo "<div align=center><table> \n" ;
		echo "<tr> \n" ;
		echo "<td> \n" ;
		echo "<form method=\"post\" action=\"/artikelsuche.php\"> \n" ;
		echo "<input type=\"hidden\" class=\"siteButton\" name=\"startrow\" value=\"" . $lowestrow . "\" /> \n" ;
		echo "<input type=\"hidden\" class=\"siteButton\" name=\"rowcount\" value=\"" . $rowcount . "\" /> \n" ;
		echo "<input type=\"hidden\" class=\"siteButton\" name=\"Suchbegriff\" value=\"" . $_suchbegriff . "\" /> \n" ;
		echo "<input type=\"submit\" class=\"siteButton\" name=\"submit\" value=\"<<\" /> \n" ;
		echo "</form> \n" ;
		echo "</td> \n" ;
		$minShow	=	( $pageNr <= 2 ? 0 : ( $pageNr - 2)) ;
		$maxShow	=	( $pageNr >= ( $pages - 2) ? $pages : $pageNr+2) ;
//		echo "Min: $minShow . "<br/>" ;
//		echo "Max: $maxShow . "<br/>" ;
		if ( $minShow != 0) {
			echo "<td> \n" ;
			echo "<form method=\"post\" action=\"/artikelsuche.php\"> \n" ;
			echo "<input type=\"hidden\" class=\"siteButton\" name=\"startrow\" value=\"" . $lowerrow . "\" /> \n" ;
			echo "<input type=\"hidden\" class=\"siteButton\" name=\"rowcount\" value=\"" . $rowcount . "\" /> \n" ;
			echo "<input type=\"hidden\" class=\"siteButton\" name=\"Suchbegriff\" value=\"" . $_suchbegriff . "\" /> \n" ;
			echo "<input type=\"submit\" class=\"siteButton\" name=\"submit\" value=\"<\" /> \n" ;
			echo "</form> \n" ;
			echo "</td> \n" ;
		} else {
			echo "<td>...</td>\n" ;
		}
		for ( $pagecount = $minShow ; $pagecount < $maxShow ; $pagecount++) {
			$realpage	=	$pagecount + 1 ;
			if ( $minShow <= $pagecount && $pagecount <= $maxShow) {
				echo "<td> \n" ;
				echo "<form method=\"post\" action=\"/artikelsuche.php\"> \n" ;
				echo "<input type=\"hidden\" class=\"siteButton\" name=\"startrow\" value=\"" . $pagecount * $rowcount . "\" /> \n" ;
				echo "<input type=\"hidden\" class=\"siteButton\" name=\"rowcount\" value=\"" . $rowcount . "\" /> \n" ;
				echo "<input type=\"hidden\" class=\"siteButton\" name=\"Suchbegriff\" value=\"" . $_suchbegriff . "\" /> \n" ;
				echo "<input type=\"submit\" class=\"siteButton\" name=\"submit\" value=\"" . $realpage . "\" /> \n" ;
				echo "</form> \n" ;
				echo "</td> \n" ;
			}
		}
		if ( $maxShow != $pages) {
			echo "<td> \n" ;
			echo "<form method=\"post\" action=\"/artikelsuche.php\"> \n" ;
			echo "<input type=\"hidden\" class=\"siteButton\" name=\"startrow\" value=\"" . $higherrow . "\" /> \n" ;
			echo "<input type=\"hidden\" class=\"siteButton\" name=\"rowcount\" value=\"" . $rowcount . "\" /> \n" ;
			echo "<input type=\"hidden\" class=\"siteButton\" name=\"Suchbegriff\" value=\"" . $_suchbegriff . "\" /> \n" ;
			echo "<input type=\"submit\" class=\"siteButton\" name=\"submit\" value=\">\" /> \n" ;
			echo "</form> \n" ;
			echo "</td> \n" ;
		} else {
			echo "<td>...</td>\n" ;
		}
		echo "<td> \n" ;
		echo "<form method=\"post\" action=\"/artikelsuche.php\"> \n" ;
		echo "<input type=\"hidden\" class=\"siteButton\" name=\"startrow\" value=\"" . $highestrow . "\" /> \n" ;
		echo "<input type=\"hidden\" class=\"siteButton\" name=\"rowcount\" value=\"" . $rowcount . "\" /> \n" ;
		echo "<input type=\"hidden\" class=\"siteButton\" name=\"Suchbegriff\" value=\"" . $_suchbegriff . "\" /> \n" ;
		echo "<input type=\"submit\" class=\"siteButton\" name=\"submit\" value=\">>\" /> \n" ;
		echo "</form> \n" ;
		echo "</td> \n" ;
		echo "</tr> \n" ;
		echo "</table> \n </div>" ;
	}
	/**
	 *
	 * @var unknown_type
	 */
	$myArticle	=	new Article() ;
	$lastArticleNo	=	"" ;
	try {
		$myProductGroup->setProductGroupNr( "00") ;
		$rc	=	0 ;
		printf( "<table class=\"tab_a\"> \n") ;
		error_log( "criteria ... '" . $where . "' ") ;
		error_log( "from ... " . $startrow) ;
		error_log( "to ... " . $startrow . " + " . $rowcount) ;
		for ( $myArticle->_firstFromDb( $where) ;
				$myArticle->isValid()  ;
				$myArticle->_nextFromDb()) {
			if ( $startrow <= $rc && $rc < ($startrow + $rowcount)) {
				if ( $myArticle->ArticleNo != $lastArticleNo) {
					einzelArticleStart( $myProductGroup, $myArticle, 0) ;
					writeArticle( $myProductGroup, $myArticle, 0, 0) ;
					einzelArticleEnd( $myArticle) ;
				}
				$lastArticleNo	=	$myArticle->ArticleNo ;
			}
			$rc++ ;
		}
		printf( "</table> \n") ;
	} catch ( Exception $e) {

	}
}

?>
