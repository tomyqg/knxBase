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
										<xsl:apply-templates select="content/CustomerCart" />
										<xsl:apply-templates select="content/OrderStatus" />
										<xsl:apply-templates select="cartop" />
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
		<form method="post" action="">
			<table>
				<tr>
					<td>Anrede:</td>
					<td></td>
					<td>
						<select name="Anrede">
							<xsl:value-of select="Salutation" />
						</select>
					</td>
					<td colspan="3"></td>
				</tr>
				<tr>
					<td>Titel:</td>
					<td></td>
					<td>
						<select name="_ITitel">
							$options.Title.CustomerContact.Title$
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>Firma:</td>
					<td></td>
					<td><input class="$err._ICustomerName1$ input_basic" name="CustomerName1" value="{CustomerName1}" /></td>
					<td></td>
				</tr>
				<tr>
					<td>Name:</td>
					<td>*</td>
					<td><input class="$err._ILastName$ inputBasic" name="_ILastName" value="$CustomerContact.LastName$" /></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Vorname:</td>
					<td></td>
					<td><input class="$err._IFirstName$ input_basic" name="_IFirstName" value="$CustomerContact.FirstName$" /></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>E-Mail:</td>
					<td>*</td>
					<td><input name="_IeMail" class="$err._IeMail$ inputBasic" value="$Customer.eMail$" /></td>
					<td></td>
				</tr>
			</table>
<!--
			<input type="submit" value="$trans.SHOP-BTN-MYACCOUNT-UPDATE$" class="buttonBasic" name="custUpd"/>
-->
		</form>
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
	<xsl:template match="CustomerCartItem">
		<tr class="zeile_i_n">
			<td class="zelle_m_1"><xsl:value-of select="ItemNo" />.<xsl:value-of select="SubItemNo" /></td>
			<td class="zelle_m_2">
				<img class="art_pic" src="/Images/thumbs/{Article/ImageReference}" alt="{Article/ImageReference}" /><br/>
				<a href="{ArticleNo}">
					<xsl:value-of select="ArticleNo" />
				</a>
			</td>
			<td class="zelle_m_3"><xsl:value-of select="Article/ArticleDescription1" /><br/><xsl:value-of select="Article/ArticleDescription2" /></td>
			<td class="zelle_m_4"><xsl:value-of select="Quantity" /></td>
			<td class="zelle_m_5"><xsl:value-of select="Price" /></td>
			<td class="zelle_m_6"><xsl:value-of select="TotalPrice" /></td>
			<td class="zelle_m_7" align="center"><xsl:value-of select="Article/TaxClass" /></td>
			<td class="zelle_m_8">
				<form method="post" name="moveItemUp$CartLine.Id$" action="">
					<input type="hidden" name="_ICustomerCartNo" value="{../CustomerCartNo}" />
					<input type="hidden" name="_IId" value="{Id}" />
					<input type="hidden" name="itemAction" value="moveItemUp" />
					<div class="memu-icon sprite-iup"
						onclick="moveItemUp{Id}.submit() ;" title="$trans.SHOP-TITLE-MOVEITEMUP$">
					</div>
				</form>
				<form method="post" name="moveItemDown$CartLine.Id$" action="">
					<input type="hidden" name="_ICustomerCartNo" value="{../CustomerCartNo}" />
					<input type="hidden" name="_IId" value="{Id}" />
					<input type="hidden" name="itemAction" value="moveItemDown" />
					<div class="memu-icon sprite-idown"
						onclick="moveItemDown{Id}.submit() ;" title="$trans.SHOP-TITLE-MOVEITEMDOWN$">
					</div>
				</form>
				<form method="post" name="deleteChange$CartLine.Id$" action="">
					<input type="hidden" name="_ICustomerCartNo" value="{../CustomerCartNo}" />
					<input type="hidden" name="_IId" value="{Id}" />
					<input type="hidden" name="itemAction" value="deleteItem" />
					<div class="memu-icon sprite-garbage"
						onclick="deleteChange{Id}.submit() ;" title="$trans.SHOP-TITLE-REMOVEITEM$">
					</div>
				</form>
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="content/CustomerCart">
		<h2 class="page-title"><trans>SHOP-HEADER-CUCART-SHOW</trans></h2>
		<trans>SHOP-TEXT-SHOWCART</trans>
		<h3 class="section-title"><trans>CuCart</trans></h3>
		<table class="tab_a">	<!-- catalog	-->
			<tbody>
				<tr class="zeile_i_1">
					<th class="zelle_m_1">Pos.</th>
					<th class="zelle_m_2">Artikel Nr.</th>
					<th class="zelle_m_3">Beschreibung</th>
					<th class="zelle_m_4">Quantity</th>
					<th class="zelle_m_5">Price</th>
					<th class="zelle_m_6">Gesamtpreis</th>
					<th class="zelle_m_7">Mwst.</th>
					<th class="zelle_m_8">Funktionen</th>
				</tr>
				<xsl:apply-templates select="CustomerCartItem" />
				<tr class="zeile_i_n">
					<td class="zelle_m_1"></td>
					<td class="zelle_m_2" colspan="2">Gesamtwarenwert netto (ohne Mwst.)</td>
					<td class="zelle_m_4"></td>
					<td class="zelle_m_5"></td>
					<td class="zelle_m_6"><xsl:value-of select="TotalPrice" /></td>
				</tr>
				<tr class="zeile_i_n">
					<td class="zelle_m_1"></td>
					<td class="zelle_m_2" colspan="2">Mehrwertsteuer, $CartTaxClass$ %</td>
					<td class="zelle_m_4"></td>
					<td class="zelle_m_5"><xsl:value-of select="TotalTax" /></td>
					<td class="zelle_m_6"></td>
				</tr>
				<tr class="zeile_i_n">
					<td class="zelle_m_1"></td>
					<td class="zelle_m_2" colspan="2">Mehrwertsteuer gesamt</td>
					<td class="zelle_m_4"></td>
					<td class="zelle_m_5"></td>
					<td class="zelle_m_6"><xsl:value-of select="TotalTax" /></td>
				</tr>
				<tr class="zeile_i_n">
					<td class="zelle_m_1"></td>
					<td class="zelle_m_2" colspan="2">Gesamt brutto (inkl. Mwst.)</td>
					<td class="zelle_m_4"></td>
					<td class="zelle_m_5"></td>
					<td class="zelle_m_6"><xsl:value-of select="TotalPrice" /></td>
				</tr>
			</tbody>
		</table>				<!-- catalog	-->
	</xsl:template>
	<xsl:template match="content/OrderStatus">
		<h1>Bestellstatus</h1>
	</xsl:template>
	<xsl:template match="cartop">
		<div id="cartop">
			<xsl:if test="@action = 'show'">
				<h3 class="section-title"><trans>Funktionsauswahl:</trans></h3>
				<table>
					<tr>
						<td>
							<form method="post" action="htmlMan/prnMZ.php" target="pdfwin">
								<input type="hidden" name="action" value="wlGetPDF" />
								<input type="submit" value="Merkzettel als PDF" class="buttonBasic" />
							</form>
						</td>
						<td>
							<form method="post" action="/CustomerCartData?action=RFQ&amp;step=1">
								<input type="submit" value="Angebot anfordern" class="buttonBasic" />
							</form>
						</td>
						<td>
							<form method="post" action="/CustomerCartData?action=order&amp;step=1">
								<input type="submit" value="Bestellung" class="buttonBasic" />
							</form>
						</td>
						<td>
							<form method="post" action="/CustomerCartData?action=store">
								<input type="submit" value="Speichern" class="buttonBasic" />
							</form>
						</td>
						<td>
							<form method="post" action="/CustomerCartData?action=delete">
								<input type="submit" value="Löschen" class="buttonBasic" />
							</form>
						</td>
					</tr>
				</table>
			</xsl:if>
			<xsl:if test="@action = 'RFQ'">
				<xsl:if test="@step = '1'">
					<h3 class="section-title"><trans>Bitte akzeptieren Sie unsere AGB:</trans></h3>
					<table>
						<tr>
							<td colspan="5">
	Ich habe die <a class="hier" href="/Hinweis" target="_subInfo" title="Wird in neuem Fenster geöffnet">AGB</a>
	(Allgemeinen Geschäftsbedingungen) von mein-mikroskop.de Karl-Heinz Welter gelesen und erkenne diese an:
							</td>
							<td>
								<input type="radio" id="AGBFrage0" value="$trans.No$" checked="true" onClick="enable( 'AGBFrage1', '_ISubmitRFQ') ; ">No</input>
								<input type="radio" id="AGBFrage1" value="$trans.Yes$" onClick="enable( 'AGBFrage1', '_ISubmitRFQ') ; ">Yes</input>
							</td>
						</tr>
						<tr>
							<td colspan="5">
	Ich habe die <a class="hier" href="/Widerruf" target="_subInfo" title="Wird in neuem Fenster geöffnet">AGB</a>
	(Allgemeinen Geschäftsbedingungen) von mein-mikroskop.de Karl-Heinz Welter gelesen und erkenne diese an:
							</td>
							<td>
								<input type="radio" id="AGBFrage0" value="$trans.No$" checked="true" onClick="enable( 'AGBFrage1', '_ISubmitRFQ') ; ">No</input>
								<input type="radio" id="AGBFrage1" value="$trans.Yes$" onClick="enable( 'AGBFrage1', '_ISubmitRFQ') ; ">Yes</input>
							</td>
						</tr>
						<tr>
							<td>Eigene Referenznummer:</td>
							<td colspan="4"><input type="text" name="CuRefNo" value="webShop" /></td>
						</tr>
						<tr>
							<td>Anmerkung:</td>
							<td colspan="4"><textarea name="CustRemark" cols="30" rows="4"></textarea></td>
						</tr>
					</table>
					<form method="post" action="/CustomerCartData?action=RFQ&amp;step=2">
						<input type="submit" value="Angebot anfordern" class="buttonBasic" />
					</form>
				</xsl:if>
				<xsl:if test="@step = '2'">
					<h3 class="section-title"><trans>Ihre Anfrage wurde abgeschickt:</trans></h3>
				</xsl:if>
			</xsl:if>
			<xsl:if test="@action = 'order'">
				<xsl:if test="@step = '1'">
					Order-Step = 1
					<xsl:call-template name="OrderStep1" />
				</xsl:if>
				<xsl:if test="@step = '2'">
					Order-Step = 2
					<xsl:call-template name="OrderStep2" />
				</xsl:if>
				<xsl:if test="@step = '3'">
					Order-Step = 3
					<xsl:call-template name="OrderStep3" />
				</xsl:if>
				<xsl:if test="@step = '4'">
					Order-Step = 4
					<xsl:call-template name="OrderStep4" />
				</xsl:if>
			</xsl:if>
		</div>
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
	<xsl:template name="OrderStep1">
		<form name="formOrder" method="post" action="/CustomerCartData?action=order&amp;step=2">
			<table>
				<tr>
		<!-- Gesamtkosten akzeptieren		-->
					<td colspan="5">$trans.SHOP-TEXT-ORDER-ACCEPTHDLG$</td>
					<td>
						<input type="radio" name="HdlgFrage" value="Nein" checked="false" onClick="enable( '_ISubmitOrder') ; ">$trans.No$</input>
						<input type="radio" name="HdlgFrage" value="Ja" onClick="enable( '_ISubmitOrder') ; ">$trans.Yes$</input>
					</td>
				</tr>
				<tr>
		<!-- Gutschein Code					-->
					<td>$trans.SHOP-TEXT-ORDER-PROMOTIONCODE$</td>
					<td colspan="4">
						<input type="text" name="_ICouponCode" value="" title="SHOP-INFO-PROMOTION-CODE"/>
					</td>
				</tr>
				<tr>
		<!-- Eigene Referenznummer			-->
				<td>$trans.SHOP-TEXT-OWN-REFERENCE-NO$</td>
					<td colspan="4">
						<input type="text" name="_IKdRefNr" value="webShop" title="SHOP-INFO-OWN-REFERENCE-NO" />
					</td>
				</tr>
				<tr>
		<!-- Anmerkungen					-->
				<td>$trans.SHOP-TEXT-REMARK$</td>
					<td colspan="4"><textarea name="_ICustText" cols="30" rows="4"></textarea></td>
				</tr>
				<tr>
					<td colspan="3"></td>
					<td>
						<input name="_ISubmitOrder" type="submit" value="$trans.SHOP-BTN-ACCPT-HDLG$" enabled="0" class="buttonBasic" />
					</td>
				</tr>
			</table>
		</form>
	</xsl:template>
	<xsl:template name="OrderStep2">
		<form name="formOrder" method="post" action="/CustomerCartData?action=order&amp;step=3">
			<input type="hidden" name="step" value="3" />
			<table>
				<tr>
					<td colspan="5">
						Ich habe die
						<a class="hier" href="/hinweise.html" target="_subInfo" title="Wird in neuem Fenster geöffnet">AGB</a>
						(Allgemeinen Geschäftsbedingungen) von mein-mikroskop.de Karl-Heinz Welter gelesen und erkenne diese an:
					</td>
					<td>
						<input type="radio" name="AGBFrage" value="Nein" onClick="enable( '_ISubmitOrder') ; ">$trans.No$</input>
						<input type="radio" name="AGBFrage" value="Ja" onClick="enable( '_ISubmitOrder') ; ">$trans.Yes$</input>
					</td>
				</tr>
				<tr>
					<td>Eigene Referenz Nr.:</td>
					<td colspan="4"><input type="text" name="_IKdRefNr" value="webShop" /></td>
				</tr>
				<tr>
					<td>Anmerkungen:</td>
					<td colspan="4"><textarea name="_ICustText" cols="30" rows="4"></textarea></td>
				</tr>
				<tr>
					<td colspan="5"></td>
					<td><input name="_ISubmitOrder" type="submit" value="$trans.SHOP-BTN-ACCPT-TERMS$" class="buttonBasic" /></td>
				</tr>
			</table>
		</form>
	</xsl:template>
	<xsl:template name="OrderStep3">
		<form name="formOrder" method="post" action="/CustomerCartData?action=order&amp;step=4">
			<table>
		<!--
				<tr>
					<td>
						<input type="radio" name="PaymentAgent" value="Paypal""></td>
					<td>
						<a href="https://www.paypal.com/de/" target="_blank">
							<img src="/Rsrc/logos/de-pp-logo-100px.png" border="0" alt="Logo 'PayPal empfohlen'">
						</a>
					</td>
					<td>$trans.SHOP-TEXT-PAYPAL$</td>
					<td></td>
				</tr>
				<tr>
					<td><input type="radio" name="PaymentAgent" value="PaypalExpress"></td>
					<td><img class="text_links" src="/Rsrc/logos/de-btn-expresscheckout.gif" alt="wimtecc.de"/></td>
					<td>$trans.SHOP-TEXT-PAYPAL-EXPRESS$</td>
					<td></td>
				</tr>
		 -->
				<tr>
					<td><input type="radio" name="PaymentAgent" value="PostPay" /></td>
					<td><img class="text_links" src="/Rsrc/logos/logo_giropay_small.gif" alt="PostPay"/></td>
					<td></td>
					<td>
					</td>
					<td rowspan="4">
						<input name="_ISubmitOrder" type="submit" value="$trans.SHOP-BTN-PAY-NOW-GIROPAY$" class="buttonBasic" />
					</td>
				</tr>
				<tr>
					<td><input type="radio" name="PaymentAgent" value="GiroPay" checked="true"/></td>
					<td><img class="text_links" src="/rsrc/logos/logo_giropay_small.gif" alt="GiroPay"/></td>
					<td>$trans.SHOP-TEXT-BANK-CODE$</td>
					<td>
							<input type="text" name="bankcode" size="8" MAXLENGTH="8" value="" title="SHOP-INFO-BANKCODE" />
					</td>
				</tr>
		<!--
		 		<tr>
					<td><input class="disabled" type="radio" name="PaymentAgent" value="Bank""></td>
					<td>$trans.SHOP-TEXT-WIRETRANSFER$</td>
					<td></td>
					<td>
						<form name="formOrder" method="post" action="/index.php?webPage=MyCart&action=order&step=31">
							<input name="_ISubmitOrder" type="submit" value="$trans.SHOP-BTN-PAY-INVOICE$" class="buttonBasic" />
						</form>
					</td>
				</tr>
				<tr>
					<td>
						<input name="_ISubmitOrder" type="submit" value="$trans.SHOP-BTN-PAY$" class="buttonBasic" />
					</td>
				</tr>
		 -->
			</table>
		</form>
	</xsl:template>
	<xsl:template name="OrderStep4">
		Schritt 4 der Bestellabwicklung.
	</xsl:template>
</xsl:stylesheet>
