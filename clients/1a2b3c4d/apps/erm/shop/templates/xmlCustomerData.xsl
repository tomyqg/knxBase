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
				<div id="page">
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
								<table class="tab_a">
									<tbody>
										<xsl:apply-templates select="content/Customer" />
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
	<!--														-->
	<!-- content/Customer												-->
	<!--														-->
	<xsl:template match="Customer">
		<h1>Kundendaten</h1>
		<form method="post" action="CustomerData?action=update">
			<table>
				<tr>
					<td>Anrede:</td>
					<td></td>
					<td>
						<select name="Anrede">
							<xsl:value-of select="Salutation" />
						</select>
					</td>
					<td colspan="3">$trans.SHOP-HELP-ADDRESS$</td>
				</tr>
				<tr>
					<td>Titel:</td>
					<td></td>
					<td>
						<select name="_ITitel">
							$options.Title.CustomerContact.Title$
						</select>
					</td>
					<td colspan="3">$trans.SHOP-HELP-TITLE$</td>
				</tr>
				<tr>
					<td>Firma</td>
					<td></td>
					<td><input class="$err._ICustomerName1$ input_basic" name="CustomerName1" value="{CustomerName1}" /></td>
					<td>$trans.SHOP-HELP-COMPANY$</td>
				</tr>
				<tr>
					<td>Name:</td>
					<td>*</td>
					<td><input class="$err._ILastName$ inputBasic" name="_ILastName" value="{CustomerContact/LastName}" /></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Vorname:</td>
					<td></td>
					<td><input class="$err._IFirstName$ input_basic" name="_IFirstName" value="{CustomerContact/FirstName}" /></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Strasse:</td>
					<td>*</td>
					<td>
						<input class="$err._IStreet$ input_basic" name="_IStreet" value="{Street}" />
						<input size="5" class="$err._INumber$ input_basic" name="_INumber" value="{Number}" />
					</td>
				</tr>
				<tr>
					<td>Postleitzahl:</td>
					<td>*</td>
					<td>
						<input class="$err._IZIP$ input_basic" name="_IZIP" size="8" value="{ZIP}" />
						<input class="$err._ICity$ inputFalse" name="_ICity" value="{City}" />
					</td>
				</tr>
				<tr>
					<td>Land:</td>
					<td></td>
					<td>
						<select name="_ICountry">
							$options.Country.Customer.Country$
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>Telefon:</td>
					<td></td>
					<td><input name="Phone" class="inputBasic" value="{Phone}" /></td>
					<td colspan="3">$trans.SHOP-HELP-PHONE$</td>
				</tr>
				<tr>
					<td>Fax:</td>
					<td></td>
					<td><input name="_IFax" class="inputBasic" value="{Fax}" /></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Mobil:</td>
					<td></td>
					<td><input name="_IMobil" class="inputBasic" value="{Cellphone}" /></td>
					<td colspan="3">$trans.SHOP-HELP-CELLPHONE$</td>
				</tr>
				<tr>
					<td>E-Mail:</td>
					<td>*</td>
					<td><input name="_IeMail" class="$err._IeMail$ inputBasic" value="{eMail}" /></td>
					<td rowspan="2" colspan="3">$trans.SHOP-HELP-EMAIL$</td>
				</tr>
				<tr>
					<td>E-Mail (Wiederholung):</td>
					<td>*</td>
					<td><input name="_IeMailVerify" class="$err._IeMail$ inputBasic" value="{eMail}" /></td>
				</tr>
			</table>
			<xsl:if test="not(CustomerNo)">
				<input type="submit" value="Registrieren ..." class="buttonBasic" />
			</xsl:if>
			<xsl:if test="CustomerNo">
				<input type="submit" value="Aktualisieren ..." class="buttonBasic" />
			</xsl:if>
		</form>
	</xsl:template>
	<xsl:template match="FncCustomerUpdate">
	</xsl:template>
	<!--														-->
	<!-- MyAccountPassword										-->
	<!--														-->
	<!--														-->
	<!--														-->
	<xsl:template name="MyPassword">
		<form method="post" action="">
			<fieldset>
				<table>
					<tr>
						<td>$trans.SHOP-LBL-EMAIL$:</td>
						<td>*</td>
						<td><input type="password" name="_INewPwd" class="$err._INewPwd$ inputBasic" value="" /></td>
						<td rowspan="2" colspan="3">$trans.SHOP-HELP-EMAIL$</td>
					</tr>
					<tr>
						<td>$trans.SHOP-LBL-EMAILCONF$:</td>
						<td>*</td>
						<td><input type="password" name="_INewPwdV" class="$err._INewPwdV$ inputBasic" value="" /></td>
					</tr>
				</table>
				<input type="submit" value="$trans.SHOP-BTN-MYPASSWORD-UPDATE$" class="buttonBasic" name="custUpdPwd"/>
			</fieldset>
		</form>
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
