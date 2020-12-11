<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
	<xsl:output method="html" encoding="utf-8" indent="yes"/>
	<xsl:template match="pagedata">
		<xsl:if test="not(content/Customer)">
			<label for="CustomerNo">Benutzer</label>
			<input id="CustomerNo" name="CustomerNo" size="15" maxlength="64" />
			<label for="Password">Passwort</label>
			<input id="Password" name="Password" size="15" maxlength="64" type="password" />
			<input type="submit" name="Login" value="Anmelden" onclick="login() ; " />
			<input type="submit" name="NewPassword" value="Passwort vergessen?" onclick="newPassword() ;" />
			<form method="post" action="/CustomerData">
				<input type="submit" value="Neuer Kunde" />
			</form>
		</xsl:if>
		<xsl:if test="content/Customer">
			Meine Daten:<br/>
			<xsl:value-of select="content/Customer/CustomerNo" /><br/>
			<xsl:value-of select="content/Customer/CustomerName1" /><br/>
			<input type="submit" name="Logout" value="Abmelden" onclick="logout() ; " />
			<div class="block-content" id="CustomerData">
				<a href="/CustomerData?action=showMyAccount">Meine Daten</a><br/>
				<a href="/CustomerData?action=show=MyPassword">Mein Passwort</a><br/>
				<a href="/CustomerData?action=show=MyOrders">Meine Bestellungen</a><br/>
				<a href="/CustomerData?action=show=MyCarts">Meine Merkzettel</a>
			</div>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
