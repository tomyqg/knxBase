<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="DataMinerRoot" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="DataMinerData" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="DataMinerDataTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="DataMinerOpenCu" data-dojo-type="dijit/layout/ContentPane" style=""title="<?php echo FTr::tr( "Open Customer ...") ; ?>">
				<div id="DataMinerOpenCuTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="DMDataOpenCuComm" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Commissions") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerKdKomm', '/Common/hdlObject.php', 'getTableKdKommOpen', '', 'divDMKdKommOpen', 'KdKommNr', 'screenKdKomm', 'retToDataMiner', '', null) ; return false ; ">
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerKdKomm', '/Common/hdlObject.php', 'getTableKdKommOpen', '', 'divDMKdKommOpen', 'KdKommNr', 'screenKdKomm', 'retToDataMiner', '', null) ; return false ; " />
						<div id="divDMKdKommOpen"></div>
					</div>
					<div id="DMDataOpenCuOrdr" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Invoices") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerKdRech', '/Common/hdlObject.php', 'getTableKdRechOpen', '', 'divDMKdRechOpen', 'KdRechNr', 'screenKdRech', 'retToDataMiner', '', null) ; return false ; ">
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerKdRech', '/Common/hdlObject.php', 'getTableKdRechOpen', '', 'divDMKdRechOpen', 'KdRechNr', 'screenKdRech', 'retToDataMiner', '', null) ; return false ; " />
						<div id="divDMKdRechOpen"></div>
					</div>
				</div>
			</div>
			<div id="DataMinerOpenSu" data-dojo-type="dijit/layout/ContentPane" style=""title="<?php echo FTr::tr( "Open Supplier ...") ; ?>">
				<div id="DataMinerOpenSuTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="DMDataOpenSuOrdr" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Orders") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerLfBest', '/Common/hdlObject.php', 'getTableLfBestOpen', '', 'divDMLfBestOpen', 'LfBestNr', 'screenLfBest', 'retToDataMiner', '', null) ; return false ; ">
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerLfBest', '/Common/hdlObject.php', 'getTableLfBestOpen', '', 'divDMLfBestOpen', 'LfBestNr', 'screenLfBest', 'retToDataMiner', '', null) ; return false ; " />
						<?php tableBlock( "refOpenSuOrdrSurvey", "formOpenSuOrdrTop") ;		?>
						<div id="divDMLfBestOpen"></div>
						<?php tableBlock( "refOpenSuOrdrSurvey", "formOpenSuOrdrBot") ;		?>
						</div>
					<div id="DMDataOpenSuGore" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Goods Receivables") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerLfLief', '/Common/hdlObject.php', 'getTableLfLiefOpen', '', 'divDMLfLiefOpen', 'LfLiefNr', 'screenLfLief', 'retToDataMiner', '', null) ; return false ; ">
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerLfLief', '/Common/hdlObject.php', 'getTableLfLiefOpen', '', 'divDMLfLiefOpen', 'LfLiefNr', 'screenLfLief', 'retToDataMiner', '', null) ; return false ; " />
						<div id="divDMLfLiefOpen"></div>
					</div>
				</div>
			</div>
			<div id="DataMinerSupp" data-dojo-type="dijit/layout/ContentPane" style=""title="<?php echo FTr::tr( "Supplier Related") ; ?>">
				<div id="DataMinerSuppTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="DMSuppTCCP1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "All") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerLief', '/Common/hdlObject.php', 'getTableSuppliers', '', 'divSuppAll', 'LiefNr', 'screenLief', 'retToDataMiner', '', null) ; return false ; ">
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerLief', '/Common/hdlObject.php', 'getTableSuppliers', '', 'divSuppAll', 'LiefNr', 'screenLief', 'retToDataMiner', '', null) ; return false ; " />
						<div id="content">
							<div id="maindata">
								<form method="post" name="formDMSA" id="formDMAS" enctype="multipart/form-data" onsubmit="return false ;" >
									<table><?php
									?></table>
								</form>
							</div>
						</div>
						<div id="divSuppAll"></div>
					</div>
					<div id="DMSuppTCCP2" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "ALl by Prefix") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerLief', '/Common/hdlObject.php', 'getTableSuppliersByPrefix', '', 'divSuppAllBP', 'LiefNr', 'screenLief', 'retToDataMiner', '', null) ; return false ; ">
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerLief', '/Common/hdlObject.php', 'getTableSuppliersByPrefix', '', 'divSuppAllBP', 'LiefNr', 'screenLief', 'retToDataMiner', '', null) ; return false ; " />
						<div id="content">
							<div id="maindata">
								<form method="post" name="formDMSA" id="formDMASBP" enctype="multipart/form-data" onsubmit="return false ;" >
									<table><?php
									?></table>
								</form>
							</div>
						</div>
						<div id="divSuppAllBP"></div>
					</div>
				</div>
			</div>
			<div id="DataMinerArticle" data-dojo-type="dijit/layout/ContentPane" style=""title="<?php echo FTr::tr( "Articles") ; ?>">
				<div id="DataMinerArticleTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="DMArticleTCCP1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Unreserved") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerArtikel', '/Common/hdlObject.php', 'getTableArticleUnreserved', '', 'divArtikelUnreserved', 'KdBestNr', 'screenKdBest', 'retToDataMiner', '', null) ; return false ; ">
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerArtikel', '/Common/hdlObject.php', 'getTableArticleUnreserved', '', 'divArtikelUnreserved', 'KdBestNr', 'screenKdBest', 'retToDataMiner', '', null) ; return false ; " />
						<div id="divArtikelUnreserved"></div>
					</div>
					<div id="DMArticleTCCP2" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "to order") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerArtikel', '/Common/hdlObject.php', 'getTableArticleToOrder', '', 'divArtikelToOrder', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', null) ; return false ; ">
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerArtikel', '/Common/hdlObject.php', 'divArtikelToOrder', '', 'divArtikelToOrder', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', null) ; return false ; " />
						<div id="divArtikelToOrder"></div>
					</div>
					<div id="DMArticleTCCP3" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "w/o sales price") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerArtikelWOP', '/Common/hdlObject.php', 'getF50', 'ArticleWOP', 'divArtikelWOP', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', null) ; return false ; ">
						<div id="content">
							<div id="maindata">
								<form method="post" name="formArtikelWOPOV" id="formArtikelWOPOV" enctype="multipart/form-data" onsubmit="return false ;" >
									<table><?php
										rowEdit( FTr::tr( "Name"), "_IName", 10, 10, "", "") ;
										rowEdit( FTr::tr( "Reference no."), "_IRefNr", 24, 32, "", "") ;
										rowOption( FTr::tr( "Language"), "_ISprache", Opt::getRLangCodes(), "", "") ;
										?></table>
									<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerArtikelWOP', '/Common/hdlObject.php', 'getF50', 'ArticleWOP', 'divArtikelWOP', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', null) ; return false ; " />
<center>
<table>
	<tr>
		<td><input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="requestDataMinerSysTexte( 'Base', 'DataMinerArtikelWOP', '/Common/hdlObject.php', 'getF50', 'ArticleWOP', 'divArtikelWOP', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', cbArticleWOP, 'formArtikelWOPOV', null) ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="requestDataMinerSysTexte( 'Base', 'DataMinerArtikelWOP', '/Common/hdlObject.php', 'getP50', 'ArticleWOP', 'divArtikelWOP', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', cbArticleWOP, 'formArtikelWOPOV', null) ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" onClick="requestDataMinerSysTexte( 'Base', 'DataMinerArtikelWOP', '/Common/hdlObject.php', 'getT50', 'ArticleWOP', 'divArtikelWOP', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', cbArticleWOP, 'formArtikelWOPOV', null) ; return false ; "/></td>
		<td><input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="requestDataMinerSysTexte( 'Base', 'DataMinerArtikelWOP', '/Common/hdlObject.php', 'getN50', 'ArticleWOP', 'divArtikelWOP', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', cbArticleWOP, 'formArtikelWOPOV', null) ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="requestDataMinerSysTexte( 'Base', 'DataMinerArtikelWOP', '/Common/hdlObject.php', 'getL50', 'ArticleWOP', 'divArtikelWOP', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', cbArticleWOP, 'formArtikelWOPOV', null) ; return false ; " /></td>
	</tr>
</table>
</center>
								</form>
								<div id="divArtikelWOP"></div>
							</div>
						</div>
					</div>
					<div id="DMArticleTCCP4" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Sales price < purchase price") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerArtikel', '/Common/hdlObject.php', 'getTableArticlePricing', '', 'divArtikelPricing', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', null) ; return false ; ">
						<div id="content">
							<div id="maindata">
								<form method="post" name="formDMAP" id="formDMAP" enctype="multipart/form-data" onsubmit="return false ;" >
									<table><?php
									?></table>
<center>
<table>
	<tr>
		<td><input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/Repeat.png" name="setStartRow" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="selDMAPFirstTen( 'formDMAP') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="selDMAPPrevTen( 'formDMAP') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" onClick="selDMAPReload( 'formDMAP') ; return false ; "/></td>
		<td><input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="selDMAPNextTen( 'formDMAP') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="selDMAPLastTen( 'formDMAP') ; return false ; " /></td>
	</tr>
</table>
</center>
								</form>
							</div>
						</div>
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerArtikel', '/Common/hdlObject.php', 'getTableArticlePricing', '', 'divArtikelPricing', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', null) ; return false ; " />
						<div id="divArtikelPricing"></div>
					</div>
					<div id="DMArticleTCCP5" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Replaced") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerArtikel', '/Common/hdlObject.php', 'getTableArticleReplaced', '', 'divArtikelReplaced', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', null) ; return false ; ">
						<div id="content">
							<div id="maindata">
								<form method="post" name="formDMAP" id="formDMAP" enctype="multipart/form-data" onsubmit="return false ;" >
									<table><?php
									?></table>
<center>
<table>
	<tr>
		<td><input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/Repeat.png" name="setStartRow" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="selDMAPFirstTen( 'formDMAP') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="selDMAPPrevTen( 'formDMAP') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" onClick="selDMAPReload( 'formDMAP') ; return false ; "/></td>
		<td><input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="selDMAPNextTen( 'formDMAP') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="selDMAPLastTen( 'formDMAP') ; return false ; " /></td>
	</tr>
</table>
</center>
								</form>
							</div>
						</div>
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerArtikel', '/Common/hdlObject.php', 'getTableArticleReplaced', '', 'divArtikelReplaced', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', null) ; return false ; " />
						<div id="divArtikelReplaced"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
