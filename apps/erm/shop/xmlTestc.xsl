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
				<code codeFile="handleSession.php" />
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
								<div id="infos">
									<div class="block block-list block-compare">
										<div class="block block-cart">
											<div class="block-title">
												<strong>
													<span>Links</span>
												</strong>
											</div>
											<div class="block-content">
												<a href="Impressum">Impressum</a><br/>
												<a href="/Ruecknahme">Rücksendungen</a><br/>
												<a href="/Datenschutz">Datenschutz</a><br/>
												<a href="/AGB">Geschäftsbedingungen</a><br/>
												<a href="/Kontakt">Kontakt</a><br/>
											</div>
										</div>
									</div>
									<dynamic file="ShoppingCartInfo.html" />
									<object class="CustomerInfo" />
								</div>
								<div class="block block-list block-compare">
									<div class="block block-cart">
										<div class="block-title">
											<strong>
												<span><a href="/index.php?webPage=MyCart&amp;action=show">$trans.SHOP-HEADER-MYCART$</a></span>
											</strong>
										</div>
										<div class="block-content" id="CartInfo">
											<p class="empty">$trans.SHOP-INFO-CARTEMPTY$</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="center">
							<div id="content">
								<h2 class="page-title">TEST TEXT</h2>
								<table class="tab_a">
									<tbody>
										<xsl:apply-templates select="Catalog/ProductGroup" />
										<xsl:apply-templates select="Catalog/ArticleGroup" />
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="footer">
						<dynamic file="Footer.html" />
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
	<xsl:template match="Catalog/ProductGroup">
		<tr>
			<td class="zelle_a_11" colspan="2">
				<a href="{ImageReference}" target="new">
					<img class="art_pic" src="/Images/thumbs/{ImageReference}" alt="{ProductGroupName}" />
				</a>
			</td>
			<td class="zelle_a_12">
				<h3><a href="{ProductGroupNameStripped}">PG: <xsl:value-of select="ProductGroupName"/></a></h3>
				<div class="zelle_a_12_text">
					<b><xsl:value-of select="Fulltext"/></b><br/>
				</div>
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="Catalog/ArticleGroup">
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
						<xsl:value-of select="Fulltext"/>
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
	<xsl:template name="CustomerInfoLogin">
		<div class="block block-list block-compare">
			<div class="block block-cart">
				<div class="block-title">
					<strong>
						<span>$trans.SHOP-HEADER-REGISTEREDUSERS$</span>
					</strong>
				</div>
				<div class="block-content" id="CartInfo">
					<form action="" method="post">
						<label for="CustId">$trans.SHOP-LBL-USERID$</label>
						<input id="CustId" name="CustId" size="15" maxlength="64" />
						<label for="CustPwd">$trans.SHOP-LBL-PASSWORD$</label>
						<input id="CustPwd" name="CustPwd" size="15" maxlength="64" type="password" />
						<input type="submit" name="Login" value="$trans.SHOP-BTN-LOGIN$" />
					</form>
					<form action="/index.php?webPage=MyAccount&amp;show=NewPassword" method="post">
						<input type="submit" name="NewPwd" value="$trans.SHOP-BTN-LOSTPWD$" />
					</form>
				</div>
			</div>
			<form method="post" action="/index.php?webPage=MyAccount&amp;show=MyAccount">
				<input type="submit" value="$trans.SHOP-BTN-REGISTER$" />
			</form>
		</div>
	</xsl:template>
	<!--														-->
	<!--														-->
	<!--														-->
	<!--														-->
	<!--														-->
	<xsl:template name="CustomerInfoLoggedIn">
		<div class="block block-list block-compare">
			<div class="block block-cart">
				<div class="block-title">
					<strong>
						<span>$trans.SHOP-HEADER-MYACCOUNT$</span>
					</strong>
				</div>
				<div class="block-content" id="CartInfo">
					$Customer.CustomerNo$<br/>
					$Customer.CustomerName1$<br/>
					$Customer.Street$ $Customer.Number$<br/>
					$Customer.ZIP$ $Customer.City$
					<div class="block-content" id="CartInfo">
						<a href="/index.php?webPage=MyAccount&amp;show=MyAccount">$trans.SHOP-LNK-CUADR$</a><br/>
						<a href="/index.php?webPage=MyAccount&amp;show=MyPassword">$trans.SHOP-LNK-CUPWD$</a><br/>
						<a href="/index.php?webPage=MyAccount&amp;show=MyOrders">$trans.SHOP-LNK-CUORDR$</a><br/>
						<a href="/index.php?webPage=MyAccount&amp;show=MyCarts">$trans.SHOP-LNK-CUCART$</a>
					</div>
					<form method="post">
						<input type="submit" name="Logoff" value="$trans.SHOP-BTN-LOGOFF$" />
					</form>
				</div>
			</div>
		</div>
	</xsl:template>
	<!--																-->
	<!-- MyOrders table													-->
	<!-- describes the layout for the description of a single article	-->
	<!--																-->
	<xsl:template name="MyOrdersTable">
		<table class="tab_i">	<!-- catalog	-->
			<tbody>
				<tr class="zeile_i_1">
					<th class="zelle_i_1">CustomerOrder No.</th>
					<th class="zelle_i_3">Date</th>
					<th class="zelle_i_3">Status</th>
					<th class="zelle_i_4">Total Price</th>
					<th class="zelle_i_4">Tax</th>
					<th class="zelle_i_5">Payment Status</th>
					<th class="zelle_i_4">PDF</th>
					<th class="zelle_i_4">Bezahlung</th>
				</tr>
				$MyOrders$
			</tbody>
		</table>				<!-- catalog	-->
	</xsl:template>
	<!--																-->
	<!-- MyOrders table													-->
	<!-- describes the layout for the description of a single article	-->
	<!--																-->
	<xsl:template name="MyOrder">
		<tr class="zeile_i_n">
			<td class="zelle_i_1">$CustomerOrder.CustomerOrderNo$</td>
			<td class="zelle_i_3">$CustomerOrder.Date$</td>
			<td class="zelle_i_3">$CustomerOrder.Status$</td>
			<td class="zelle_i_4">$CustomerOrder.TotalPrice$</td>
			<td class="zelle_i_4">$CustomerOrder.TotalTax$</td>
			<td class="zelle_i_5">$CustomerOrder.StatPayment$</td>
			<td class="zelle_i_4"><a href="/Archive/CustomerOrder/$CustomerOrder.CustomerOrderNo$.pdf">PDF</a></td>
			<td class="zelle_i_4">$PayInfo$</td>
		</tr>
	</xsl:template>
	<!--																-->
	<!-- MyOrders table													-->
	<!-- describes the layout for the description of a single article	-->
	<!-- order payment is handled via the cart no. !!!					-->
	<!--																-->
	<xsl:template name="MyOrderPayNow">
		<a href="/index.php?webPage=MyCart&amp;action=order&amp;step=31&amp;CustomerCartNo=$CustomerOrder.CustomerCartNo$">Pay now</a>
	</xsl:template>
	<!--																-->
	<!-- MyOrders table													-->
	<!-- describes the layout for the description of a single article	-->
	<!--																-->
	<xsl:template name="MyOrderPaid">
		Already paid
	</xsl:template>
	<!--																-->
	<!-- MyCarts table													-->
	<!-- describes the layout for the description of a single article	-->
	<!--																-->
	<xsl:template name="MyCartsTable">
		<table class="tab_i">	<!-- catalog	-->
			<tbody>
				<tr class="zeile_i_1">
					<th class="zelle_i_1">CustomerCart No.</th>
					<th class="zelle_i_3">Date</th>
					<th class="zelle_i_3">Status</th>
					<th class="zelle_i_4">Total Price</th>
					<th class="zelle_i_4">Tax</th>
					<th class="zelle_i_4">PDF</th>
					<th class="zelle_i_5">Funktionen</th>
				</tr>
				$MyCarts$
			</tbody>
		</table>				<!-- catalog	-->
	</xsl:template>
	<!--																-->
	<!-- MyCarts table													-->
	<!-- describes the layout for the description of a single article	-->
	<!--																-->
	<xsl:template name="MyCart">
		<tr class="zeile_i_n">
			<td class="zelle_i_1">$CustomerCart.CustomerCartNo$</td>
			<td class="zelle_i_3">$CustomerCart.Date$</td>
			<td class="zelle_i_3">$CustomerCart.Status$</td>
			<td class="zelle_i_4">$CustomerCart.TotalPrice$</td>
			<td class="zelle_i_4">$CustomerCart.TotalTax$</td>
			<td class="zelle_i_4"><a href="/Archive/CustomerCart/$CustomerCart.CustomerCartNo$.pdf">PDF</a></td>
			<td class="zelle_i_5">
				<form method="post" name="activateCart$CustomerCart.CustomerCartNo$" action="">
					<input type="hidden" name="_ICustomerCartNo" value="$CustomerCart.CustomerCartNo$" />
					<input type="hidden" name="itemAction" value="moveItemUp" />
					<div class="memu-icon sprite-iup"
						onclick="activateCart$CustomerCart.CustomerCartNo$.submit() ;" title="$trans.SHOP-TITLE-ACTIVATECART$">
					</div>
				</form>
			</td>
		</tr>
	</xsl:template>
	<!--														-->
	<!-- MyAccountDetails										-->
	<!--														-->
	<!--														-->
	<!--														-->
	<xsl:template name="MyAccountDetails">
		<form method="post" action="">
			<fieldset>
				<table>
					<tr>
						<td>Anrede:</td>
						<td></td>
						<td>
							<select name="_IAnrede">
								$options.Salutation.CustomerContact.Salutation$
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
						<td>$trans.SHOP-LBL-COMPANY$:</td>
						<td></td>
						<td><input class="$err._ICustomerName1$ input_basic" name="CustomerName1" value="$Customer.CustomerName1$" /></td>
						<td>$trans.SHOP-HELP-COMPANY$</td>
					</tr>
					<tr>
						<td>$trans.SHOP-LBL-LASTNAME$:</td>
						<td>*</td>
						<td><input class="$err._ILastName$ inputBasic" name="_ILastName" value="$CustomerContact.LastName$" /></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>$trans.SHOP-LBL-FIRSTNAME$:</td>
						<td></td>
						<td><input class="$err._IFirstName$ input_basic" name="_IFirstName" value="$CustomerContact.FirstName$" /></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>$trans.SHOP-LBL-STREETNO$:</td>
						<td>*</td>
						<td>
							<input class="$err._IStreet$ input_basic" name="_IStreet" value="$Customer.Street$" />
							<input size="5" class="$err._INumber$ input_basic" name="_INumber" value="$Customer.Number$" />
						</td>
					</tr>
					<tr>
						<td>$trans.SHOP-LBL-ZIPCITY$:</td>
						<td>*</td>
						<td>
							<input class="$err._IZIP$ input_basic" name="_IZIP" size="8" value="$Customer.ZIP$" />
							<input class="$err._ICity$ inputFalse" name="_ICity" value="$Customer.City$" />
						</td>
					</tr>
					<tr>
						<td>$trans.SHOP-LBL-COUNTRY$:</td>
						<td></td>
						<td>
							<select name="_ICountry">
								$options.Country.Customer.Country$
							</select>
						</td>
						<td></td>
					</tr>
					<tr>
						<td>$trans.SHOP-LBL-PHONE$:</td>
						<td></td>
						<td><input name="Phone" class="inputBasic" value="" /></td>
						<td colspan="3">$trans.SHOP-HELP-PHONE$</td>
					</tr>
					<tr>
						<td>$trans.SHOP-LBL-FAX$:</td>
						<td></td>
						<td><input name="_IFax" class="inputBasic" value="" /></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>$trans.SHOP-LBL-CELLPHONE$:</td>
						<td></td>
						<td><input name="_IMobil" class="inputBasic" value="" /></td>
						<td colspan="3">$trans.SHOP-HELP-CELLPHONE$</td>
					</tr>
					<tr>
						<td>$trans.SHOP-LBL-EMAIL$:</td>
						<td>*</td>
						<td><input name="_IeMail" class="$err._IeMail$ inputBasic" value="$Customer.eMail$" /></td>
						<td rowspan="2" colspan="3">$trans.SHOP-HELP-EMAIL$</td>
					</tr>
					<tr>
						<td>$trans.SHOP-LBL-EMAILCONF$:</td>
						<td>*</td>
						<td><input name="_IeMailVerify" class="$err._IeMail$ inputBasic" value="$Customer.eMail$" /></td>
					</tr>
				</table>
				<input type="submit" value="$trans.SHOP-BTN-MYACCOUNT-UPDATE$" class="buttonBasic" name="custUpd"/>
			</fieldset>
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
</xsl:stylesheet>
