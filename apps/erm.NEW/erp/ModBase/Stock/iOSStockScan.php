<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
$myStockId	=	"VOID" ;
$myArticleNo	=	"VOID" ;
if ( isset( $_SESSION['StockId'])) {
	$myStockId	=	$_SESSION['StockId'] ;
}
error_log( "myStockId := '$myStockId', myArticleNo := '$myArticleNo'") ;
$myArticle	=	new Artikel() ;
$myStock	=	new Stock() ;
if ( isset( $_GET['scanData'])) {
	$myScanData	=	$_GET['scanData'] ;
	$myArticle	=	new Artikel() ;
	$myArtikelBestand	=	new ArtikelBestand() ;
	try {
		if ( $myArticle->setArtikelNr( $myScanData)) {
			$myArticleNo	=	$myArticle->ArtikelNr ;
			$myArtikelBestand->getDefault( $myArticle->ArtikelNr) ;
		} else {
		}
	} catch ( Exception $e){
		error_log( "setting session data") ;
		$myStockId	=	$_GET['scanData'] ;
		$_SESSION['StockId']	=	$myStockId ;
	}		
}
error_log( "myStockId := '$myStockId', myArticleNo := '$myArticleNo'") ;
?>
{
	"answer":{
		"result":"ok",
		"status":"0",
		"debug":[],
		"data":[{
			"StockId":"<?php echo $myStockId ; ?>",
			"ArticleNo":"<?php echo $myArticleNo ; ?>",
			"ArticleDescr":"<?php echo $myArticle->ArtikelBez1.$myArticle->ArtikelBez1 ; ?>",
			"Qty":"<?php echo $myArtikelBestand->Lagerbestand ; ?>"
		}]
	}
}
