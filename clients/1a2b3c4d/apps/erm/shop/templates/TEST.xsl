<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
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
						<a href="/Wiederruf">Wiederruf</a><br/>
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
				<div class="block block-cart">
					<div class="block-title">
						<strong>
							<span>Benutzer</span>
						</strong>
					</div>
					<div class="block-content" id="CustomerInfo">
						***
					</div>
				</div>
				<form method="post" action="/index.php?webPage=MyAccount&amp;show=MyAccount">
					<input type="submit" value="Neuer Kunde" />
				</form>
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>
