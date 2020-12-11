<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
if ( isset( $_GET['LiefNr'])) {
	$myLiefNr	=	$_GET['LiefNr'] ;
} else {
	$myLiefNr	=	"" ;
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" media="screen" href="/styles/v1<?php echo $_SERVER['PHP_AUTH_USER'] ; ?>.css" title="Version 1 <?php echo $_SERVER['PHP_AUTH_USER'] ; ?>" />
</head>
<body class="tundra">
	<div id="content">
		<div id="maindata">
			<form name="formselArticleSPFilter" id="formselArticleSPFilter" onsubmit="return false ;">
				<table><?php
					rowOption( FTr::tr( "Market id."), "_SMarketId", Market::getMarkets(), "shop", "") ;
					rowEdit( FTr::tr( "Article no."), "_SArticleNo", 24, 24, "", "", "onkeyup=\"screenCurrent.selArticleSP.cache( event, true, 3, this) ;\"") ;
					rowEdit( FTr::tr( "Description"), "_SDescr", 32, 32, "", "", "onkeyup=\"screenCurrent.selArticleSP.cache( event, true, 3, this) ;\"") ;
					rowEdit( FTr::tr( "Description 1"), "_SDescr1", 24, 24, "", "", "onkeyup=\"screenCurrent.selArticleSP.cache( event, true, 3, this) ;\"") ;
					rowEdit( FTr::tr( "Description 2"), "_SDescr2", 24, 24, "", "", "onkeyup=\"screenCurrent.selArticleSP.cache( event, true, 3, this) ;\"") ;
					?></table>
			</form>
			<button data-dojo-type="dijit/form/Button" name="actionUpdArtikel"
					onclick="screenCurrent.selArticleSP.show( '', -1, '') ;">
				<?php echo FTr::tr( "Refresh") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="ArticleSurveyRoot">
				<?php tableBlock( "itemViews[currSelector.name+'DTV']", "formselArticleSPTop") ;		?>
				<table id="TableselArticleSPSurvey" eissClass="Artikel" eissSelKey="Id" width="640">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="ArtikelNr">Artikel Nr.</th>
							<th eissAttribute="ArtikelBez1">Bezeichnung 1</th>
							<th eissAttribute="ArtikelBez2">Bezeichnung 2</th>
							<th eissAttribute="MengenText">Menge</th>
							<th eissAttribute="Preis">Preis</th>
							<th eissAttribute="MengeProVPE">Menge/VPE</th>
							<th colspan="1" eissFunctions="select">Functions</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</body>
</html>
