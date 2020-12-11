<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE DOCUMENT [
	<!ENTITY ALPHA "@0123456789AÄBCDEFGHIJKLMNOÖPQRSTUÜVWXYZ">
	<!ENTITY LOWER "aäbcdefghijklmnoöpqrstuüvwxyz">
	<!ENTITY UPPER "AÄBCDEFGHIJKLMNOÖPQRSTUÜVWXYZ">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:fo="http://www.w3.org/1999/XSL/Format">
	<xsl:output method="xml" encoding="UTF-8" indent="yes"/>

	<xsl:include href="parameters.xsl"/>
	<xsl:include href="attribute-sets.xsl"/>

	<xsl:param name="lang">none</xsl:param>

	<xsl:template match="/">
		<fo:root>
			<fo:layout-master-set>
			
				<!-- Define the cover page. -->
				<fo:simple-page-master master-name="cover" page-height="297mm"
						                   page-width="210mm" margin-top="20mm"
						                   margin-bottom="20mm" margin-left="20mm"
						                   margin-right="10mm">
					<fo:region-body margin-top="0.25in" margin-bottom="0.25in"/>
				</fo:simple-page-master>
	
				<!-- Define a body (or default) page. -->
				<fo:simple-page-master master-name="leftPage"
					page-height="297mm" page-width="210mm" margin-top="8mm"
					margin-bottom="8mm" margin-left="10mm" margin-right="20mm">
					<!-- Central part of page -->
					<fo:region-body column-count="2" column-gap="5mm"
						margin-top="15mm" margin-bottom="10mm" />
					<!-- Header -->
					<fo:region-before region-name="Left-header" extent="10mm" display-align="after"/>
					<!-- Footer -->
					<fo:region-after extent="10mm" />
				</fo:simple-page-master>
				<fo:simple-page-master master-name="rightPage"
					page-height="297mm" page-width="210mm" margin-top="8mm"
					margin-bottom="8mm" margin-left="20mm" margin-right="10mm">
					<!-- Central part of page -->
					<fo:region-body column-count="2" column-gap="5mm"
						margin-top="15mm" margin-bottom="10mm"/>
					<!-- Header -->
					<fo:region-before region-name="Right-header" extent="10mm" display-align="after"/>
					<!-- Footer -->
					<fo:region-after extent="10mm" />
				</fo:simple-page-master>
				<fo:page-sequence-master master-name="contents">
					<fo:repeatable-page-master-alternatives>
						<fo:conditional-page-master-reference
							master-reference="leftPage" odd-or-even="even" />
						<fo:conditional-page-master-reference
							master-reference="rightPage" odd-or-even="odd" />
					</fo:repeatable-page-master-alternatives>
				</fo:page-sequence-master>
			</fo:layout-master-set>

			<!-- cover page -->
			<fo:page-sequence master-reference="cover">
				<fo:flow flow-name="xsl-region-body">
					<xsl:variable name="imageURI" select="doc/Image" />
					<fo:block text-align="center">
						<fo:external-graphic src="url('{$imageURI}')"
							content-height="30mm"
							keep-with-next="always" />
					</fo:block>
					<fo:block text-align="center" font-size="28.0pt">
						<xsl:text>Preisliste</xsl:text>
					</fo:block>
					<fo:block text-align="center" font-size="18.0pt" space-before="14pt">
						<xsl:value-of select="doc/Scope"/>
					</fo:block>
					<fo:block text-align="center" font-size="14.0pt" space-before="14pt">
						<xsl:text>Stand: </xsl:text><xsl:value-of select="doc/Date"/>
					</fo:block>
					<fo:block break-before="page" text-align="left" font-size="10.0pt" space-before="14pt">
						<xsl:text>MODIS GmbH</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt">
						<xsl:text>Robert-Bosch-Str. 1</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt">
						<xsl:text>D-51674 Wiehl - Bomig</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt">
						<xsl:text>Telefon: 02262 7999 00-0</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt">
						<xsl:text>Fax: 02262 7999 00-9</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt">
						<xsl:text>Internet: http://www.modis-gmbh.eu/</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt">
						<xsl:text>E-Mail: mail@modis-gmbh.eu</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt">
						<xsl:text></xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="11.0pt" space-before="14pt" font-weight="bold">
						<xsl:text>Bitte beachten Sie:</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt" space-before="14pt">
						<xsl:text>Wir liefern Chemikalien AUSSCHLIESSLICH an Schulen,
						Universitäten sowie gewerbliche Abnehmer.</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt" space-before="14pt">
						<xsl:text>Bei Internetbestellungen werden Chemikalien automatisch
						aus der Bestellung gelöscht wenn der Besteller
						diese Voraussetzung nicht erfüllt.</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt" space-before="14pt">
						<xsl:text>Bei Neuregisterierung über unsere Internetseite müssen 
						Sie sich, um diese Voraussetzung zu erfüllen, entsprechend anmelden.
						Hierzu ist die Angabe von Telefonnummer, Telefaxnummer sowie der
						Umsatzsteuerident Nr. erforderlich. Wir behalten uns vor diese Angabe
						nachzuprüfen und im Zweifelsfall eine Gewerbeanmeldung einzufordern.
						</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt" space-before="14pt">
						<xsl:text>Für Bestellung von Kaliumpermanganat sowie Essigsäureanhydrid
						ist zusätzliche eine sogenannte Endverbleibserklärung
						erforderlich.
						Das hierzu erforderliche Formular finden Sie in dem Download Bereich.
						Beide Chemikalien werden ausschliesslich an Schulen und Universitäten
						abgegeben.</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt" space-before="14pt">
						<xsl:text>Alle Preise in dieser Preisliste sind netto, d.h. zzgl. der
						gesetzlichen Mwst., derzeit 19% bzw. 7% für Bücher.
						Gewerbliche Abnehmer innerhalb der EU beliefern wir Mwst. frei wenn eine
						nachprüfbare Umsatzsteuerident Nr. (UStId) vorliegt.
						</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt" space-before="14pt">
						<xsl:text>Preisänderungen vorbehalten. Diese Preisliste behält Gültigkeit
						bis zum Erscheinen einer neuen Preisliste.
						</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="11.0pt" space-before="14pt" font-weight="bold">
						<xsl:text>Eine Anmerkung:</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt" space-before="14pt">
						<xsl:text>Wir standardisieren derzeit unsere Artikelnummern.
						Im Zuge dessen kann es vorkommen, daß sich die eine oder andere Artikelnummer in dieser Liste
						kurzfristig ändert und damit im WebShop ggf. nicht zu finden ist.
						Hiervon nicht betroffen sind die bereits 8-stelligen Artikelnummern die ausschliesslich aus Zahlen bestehen.
						Wir bitten um Verständnis für diese Maßnahme.</xsl:text>
					</fo:block>
					<fo:block text-align="left" font-size="10.0pt" space-before="14pt">
						<xsl:text>Produktbilder können vom aktuellen Produkt abweichen.
						Das auf den Fotos/Bildern abgebildete Zubehör ist idR nicht im Lieferumfang enthalten.
						Fragen Sie im Zweifelsfalle bitte an.
						Die Bilder dienen ausschliesslich der Orientierung.
						Technische Änderungen, Irrtümer und Liefermöglichkeit vorbehalten.
						</xsl:text>
					</fo:block>
				</fo:flow>
			</fo:page-sequence>


			<!-- Body page -->
			<fo:page-sequence master-reference="contents" initial-page-number="1">
	
				<fo:static-content flow-name="Right-header">
					<fo:block font-size="9pt" text-align="outside" space-after="3mm" border-bottom-style="solid" border-bottom-width="1.0pt">
						<fo:inline font-size="17pt"><fo:page-number/></fo:inline>
					</fo:block>
				</fo:static-content>
				<fo:static-content flow-name="Left-header">
					<fo:block font-size="9pt" text-align="inside" space-after="3mm" border-bottom-style="solid" border-bottom-width="1.0pt">
						<fo:inline font-size="17pt"><fo:page-number/></fo:inline>
					</fo:block>
				</fo:static-content>
	
				<!-- Define the contents of the footer. -->
				<fo:static-content flow-name="xsl-region-after">
					<fo:block font-size="8.0pt" font-family="sans-serif"
						padding-before="2mm" padding-after="2.0pt" space-before="3mm"
						text-align="center"
						border-top-style="solid" border-top-width="1.0pt">
						<xsl:text>MODIS GmbH, Robert-Bosch-Str. 1, D-61674 Wiehl - Bomig, Tel. 02262 7999 000, Fax 02262 7999 009</xsl:text>
					</fo:block>
				</fo:static-content>
	
				<!-- The main contents of the body page, that is, the catalog
						 entries -->
				<fo:flow flow-name="xsl-region-body">
					<fo:block font-size="10.0pt" font-family="sans-serif">
						<xsl:apply-templates select="doc/TableArticle">
						</xsl:apply-templates>
					</fo:block>
				</fo:flow>
			</fo:page-sequence>
		</fo:root>
	</xsl:template>

	<!-- Format entries from angebot.xml -->
	<xsl:template match="TableArticle">
			<xsl:call-template name="Articles">
				<xsl:with-param name="ItemList" select="Article"/>
			</xsl:call-template>
	</xsl:template>
	
	<!-- All entries have the same basic format: a title, an author (or
				director), a description and an optional graphic. Each type of entry is
				taken care of by matching one of the above templates. Those templates, in
				turn, call this one to do the real formatting. -->
	<xsl:template name="Articles">
			<xsl:param name="ItemList"/>
	
			<!-- Put entire entry within a single block. -->
			<fo:block space-before="4.0pt" space-after="4.0pt" start-indent="0mm" end-indent="0mm">
	
				<!-- Author (Director) -->
				<fo:block font-family="sans-serif" font-size="9.0pt" space-after="2.0pt">
					<xsl:call-template name="items-list">
						<xsl:with-param name="list" select="$ItemList"/>
					</xsl:call-template>
				</fo:block>
	
			</fo:block>
	</xsl:template>
			
	<!-- Useful template for listing multiple items (such as authors). -->
	<xsl:template name="items-list">
			<xsl:param name="list"/>
		<fo:table>
			<fo:table-column column-width="17mm"/>
			<fo:table-column column-width="45mm"/>
			<fo:table-column column-width="13mm"/>
			<fo:table-column column-width="10mm"/>
			<fo:table-header>
				<fo:table-row border-top="thin solid">
					<fo:table-cell>
						<fo:block font-weight="bold">Artikelnr.</fo:block>
					</fo:table-cell>
					<fo:table-cell>
						<fo:block font-weight="bold">Beschreibung</fo:block>
					</fo:table-cell>
					<fo:table-cell>
						<fo:block font-weight="bold" text-align="right">Menge</fo:block>
					</fo:table-cell>
					<fo:table-cell>
						<fo:block font-weight="bold" text-align="right">Preis</fo:block>
					</fo:table-cell>
				</fo:table-row>
			</fo:table-header>
			<fo:table-body>	
				<xsl:for-each select="$list[position() > 0]">
					<fo:table-row border-top="thin solid">
						<fo:table-cell>
							<fo:block><xsl:value-of select="./ArtikelNr"/></fo:block>
						</fo:table-cell>
						<fo:table-cell>
						<fo:block>
							<xsl:value-of select="./ArtikelBez1"/>
						</fo:block>
						<fo:block>
							<xsl:value-of select="./ArtikelBez2"/>
						</fo:block>
						</fo:table-cell>
						<fo:table-cell>
						<fo:block text-align="right">
							<xsl:value-of select="./MengenText"/>
						</fo:block>
						</fo:table-cell>
						<fo:table-cell>
							<fo:block text-align="right"><xsl:value-of select="./Preis"/></fo:block>
						</fo:table-cell>
					</fo:table-row>
				</xsl:for-each>
			</fo:table-body>
		</fo:table>
	</xsl:template>
</xsl:stylesheet>
