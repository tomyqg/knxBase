<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
	<xsl:output method="html" encoding="utf-8" indent="yes"/>
	<xsl:template match="pagedata">
		<html>
			<head>
				<link title="Default screen stylesheet" media="screen" href="StileR2.css" type="text/css" rel="stylesheet" />
				<link href="/rsrc/img/wimtecc.de/favicon.ico" rel="SHORTCUT ICON" />
				<script src="ajaxstuff.js" type="text/javascript" />
			</head>
			<body>
				<center>Dieser WebShop ist zur Zeit nicht in Betrieb. Aufträge werden NICHT ausgeführt.</center>
				<div id="page">
<!--
					<if var="offline" eq="1">
						<div id="header-status">
							<object class="HeaderStatus" />
						</div>
					</if>
-->
					<div id="header">
						<div id="headerLeft">
							<div id="header" style="background: #ffffff url(/flaschen24-banner.png) repeat-x ;">
							</div>
						</div>
						<div id="headerRight">
							<div>
								<img class="text_links" src="/flaschen24-banner.png" height="120" />
							</div>
						</div>
						<div id="headerSearch">
							<form id="search_mini_form" action="/index.php?webPage=MySearch&amp;action=search" method="post">
								<div class="form-search">
									<label for="SearchTerm">Suche:</label>
									<input id="SearchTerm" type="text" name="SearchTerm" value="Suchen ..." class="input-text" maxlength="128"
											onClick="if ( this.value == '$self.SearchTermBase$') this.value = '' ;" />
									<input type="image" onclick="submit() ;" src="/rsrc/img/search.png" />
								</div>
							</form>
						</div>
					</div>
					<div id="centerContainer">
						<div id="centerLeft">
							<div id="content">
								<div id="Navigator">
									<xsl:apply-templates select="ProductGroupTree/ProductGroup" />
								</div>
							</div>
						</div>
						<div id="centerRight">
							<div id="content">
								<xsl:call-template name="info" />
							</div>
						</div>
						<div id="center">
							<div id="content">
<!--
								<h2 class="page-title">TEST TEXT</h2>
-->
								<xsl:apply-templates select="CatalogData/ProductGroup" />
								<table class="tab_a">
									<tbody>
										<xsl:apply-templates select="CatalogData/ProductGroup/ProductGroup" />
										<xsl:apply-templates select="CatalogData/ProductGroup/ArticleGroup" />
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="footer">
						<center>
						<p>&#169; 2007-2016</p>
						The content of this website is intended solely for customers within Germany.<br/>
<!--
						Other European Clients please refer to my <a href="/en_eu/">European Site</a> <a href="$main.urlContact$">$shop.siteName$</a><br/>
						Rest of the World Clients please refer to my <a href="/en_in/">Worldwide Site $date$</a><br/>
-->
						</center>
					</div>
				</div>
			</body>
		</html>
	</xsl:template>
	<xsl:template match="ProductGroupTree/ProductGroup">
		<a class="{@class}" href="{@href}">
			<xsl:value-of select="@text" />
		</a>
		<xsl:apply-templates select="ProductGroupTree/ProductGroup" />
	</xsl:template>
	<xsl:template match="CatalogData/ProductGroup">
		<h1><xsl:value-of select="ProductGroupName"/></h1>
		<xsl:value-of disable-output-escaping="yes" select="Fulltext"/>
	</xsl:template>
	<xsl:template match="CatalogData/ProductGroup/ProductGroup">
		<tr>
			<td class="zelle_a_11" colspan="2">
				<a href="{ImageReference}" target="new">
					<img class="art_pic" src="/Images/thumbs/{ImageReference}" alt="{ProductGroupName}" />
				</a>
			</td>
			<td class="zelle_a_12">
				<h3><a href="{ProductGroupNameStripped}">PG: <xsl:value-of select="ProductGroupName"/></a></h3>
				<div class="zelle_a_12_text">
					<b><xsl:value-of disable-output-escaping="yes" select="Fulltext"/></b><br/>
				</div>
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="CatalogData/ProductGroup/ArticleGroup">
		<tr>
			<td class="zelle_a_11" colspan="1">
				<a href="{ImageReference}" target="new">
					<img class="art_pic" src="/Images/thumbs/{ImageReference}" alt="{ArticleGroupName}" />
				</a>
			</td>
			<td class="zelle_a_12">
				<h3><a href="{ArticleGroupNameStripped}">AG: <xsl:value-of select="ArticleGroupName"/></a></h3>
				<div class="zelle_a_12_text">
					<h3><xsl:value-of select="ArtGrName"/></h3>
					<div class="zelle_a_12_text">
						<xsl:value-of disable-output-escaping="yes" select="Fulltext"/>
					</div>
					<table class="tab_i">
						<tbody>
							<tr class="zeile_i_1">
								<th class="zelle_i_1">Artikel Nr.</th>
								<th class="zelle_i_2">Bezeichnung/Variante</th>
								<th class="zelle_i_3">&#8364; inkl. Mwst.<br/>&#8364; exkl. Mwst.</th>
								<th class="zelle_i_4">bis zu</th>
								<th class="zelle_i_5">Anzahl</th>
							</tr>
							<xsl:apply-templates select="Article/ArticleSalesPriceCache" />
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="Article/ArticleSalesPriceCache">
		<tr class="zeile_i_n">
			<td class="zelle_i_1"><xsl:value-of select="ArticleNo" /></td>
			<td class="zelle_i_2"><xsl:value-of select="../ArticleDescription1" /><br/><xsl:value-of select="../ArticleDescription2" /></td>
			<td class="zelle_i_3"><xsl:value-of select="Price" /> / <xsl:value-of select="SalesPrice.MengeProVPE" /></td>
			<td class="zelle_i_4"><xsl:value-of select="Discount" /></td>
			<td class="zelle_i_5">
				<form class="eingabe1" method="post" id="Form{../ERPNo}_{QtyPerPU}"
					action="erm/shop/htmlMan/mzInfo.php" onsubmit="addToCuCart( 'Form{../ERPNo}_{QtyPerPU}') ; return false ;">
					<fieldset style="border: 0px">
						<input type="hidden" name="_IArticleNo" value="{../ArticleNo}" />
						<input type="hidden" name="_IArticleDescription1" value="{../ArticleDescription1}" />
						<input type="hidden" name="_IPrice" value="{Price}" />
						<input type="hidden" name="_IQuantityPerPU" value="{QuantityPerPU}" />
						<input class="eingabe2" maxlength="3" size="3" name="_IQuantity" value="{Quantity}" />
						<input class="eingabe3" type="button" name="submit" value="MERKEN"
								onclick="addToCuCart( 'Form{../ERPNo}_{QtyPerPU}') ; " />
					</fieldset>
				</form>
			</td>
		</tr>
	</xsl:template>
	<xsl:template name="info">
		<div id="infos">
			<div class="block block-list block-compare">
				<div class="block block-cart">
					<div class="block-title">
						<strong>
							<span>Links</span>
						</strong>
					</div>
					<div class="block-content">
						<a href="/News">Neuigkeiten</a><br/>
						<a href="/Impressum">Impressum</a><br/>
						<a href="/Widerruf">Widerruf</a><br/>
						<a href="/Datenschutz">Datenschutz</a><br/>
						<a href="/Haftungsausschluss">Haftungsausschluss</a><br/>
						<a href="/AGB">Geschäftsbedingungen</a><br/>
						<a href="/Kontakt">Kontakt</a><br/>
					</div>
				</div>
			</div>
			<div class="block block-list block-compare">
				<div class="block block-cart">
					<div class="block-title">
						<strong>
							<span><a href="/CustomerCartData?action=show">Merkzettel</a></span>
						</strong>
					</div>
					<div class="block-content" id="CustomerCartInfo">
						***
					</div>
				</div>
			</div>
			<div class="block block-list block-compare">
				<div class="block-title">
					<strong>
						<span>Benutzer</span>
					</strong>
				</div>
				<div class="block-content" id="CustomerInfo">
					***
				</div>
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>
