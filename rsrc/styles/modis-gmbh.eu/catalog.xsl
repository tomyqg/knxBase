<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE DOCUMENT [
	<!ENTITY ALPHA "@0123456789AÄBCDEFGHIJKLMNOÖPQRSTUÜVWXYZ">
	<!ENTITY LOWER "aäbcdefghijklmnoöpqrstuüvwxyz">
	<!ENTITY UPPER "AÄBCDEFGHIJKLMNOÖPQRSTUÜVWXYZ">
]>

<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">
	<xsl:output method="xml" encoding="UTF-8" indent="yes" />

	<xsl:include href="parameters.xsl"/>
	<xsl:include href="attribute-sets.xsl"/>

	<!--
	-->
	<xsl:param name="toc-make" select="true()" />
	<xsl:param name="cover-make" select="false()" />
	<xsl:param name="index-artnr-make" select="true()" />
	<xsl:param name="index-artgr-make" select="true()" />
	<xsl:param name="lang">none</xsl:param>
	<!--
		global parameter and variable used when creating the table of
		contents.
	-->
	<xsl:param name="toc-level-default" select="3" />
		
	<xsl:key name="index-artnr-key" match="index-artnr"
		use="translate(substring(normalize-space(concat(@key,@keyword,.)),1,1),'&LOWER;','&UPPER;') " />
	<xsl:key name="index-artnr-value" match="index-artnr" use="." />

	<xsl:key name="index-artgr-key" match="index-artgr"
		use="translate(substring(normalize-space(concat(@key,@keyword,.)),1,1),'&LOWER;','&UPPER;') " />
	<xsl:key name="index-artgr-value" match="index-artgr" use="." />

	<xsl:template match="/">

		<fo:root>
			<fo:layout-master-set>
				<!-- Define the cover page. -->
				<fo:simple-page-master master-name="cover"
					page-height="297mm" page-width="210mm" margin-top="20mm"
					margin-bottom="20mm" margin-left="20mm" margin-right="10mm">
					<fo:region-body margin-top="0.25in" margin-bottom="0.25in" />
				</fo:simple-page-master>
				<!-- Define the table-of-contents (TOC) page. -->
				<fo:simple-page-master master-name="PageMaster-index-artnrleft"
					page-height="297mm" page-width="210mm" margin-top="8mm"
					margin-bottom="8mm" margin-left="10mm" margin-right="20mm">
					<fo:region-body margin-top="15mm" margin-bottom="10mm" column-count="5" column-gap="5mm" />
					<!-- Header -->
					<fo:region-before region-name="Header-index-artnrleft" extent="10mm" display-align="after"/>
					<!-- Footer -->
					<fo:region-after extent="10mm" />
				</fo:simple-page-master>
				<fo:simple-page-master master-name="PageMaster-index-artnrright"
					page-height="297mm" page-width="210mm" margin-top="8mm"
					margin-bottom="8mm" margin-left="20mm" margin-right="10mm">
					<fo:region-body margin-top="15mm" margin-bottom="10mm" column-count="5" column-gap="5mm" />
					<!-- Header -->
					<fo:region-before region-name="Header-index-artnrright" extent="10mm" display-align="after"/>
					<!-- Footer -->
					<fo:region-after extent="10mm" />
				</fo:simple-page-master>
				<fo:page-sequence-master master-name="PageMaster-index-artnr">
					<fo:repeatable-page-master-alternatives>
						<fo:conditional-page-master-reference
							master-reference="PageMaster-index-artnrleft" odd-or-even="even" />
						<fo:conditional-page-master-reference
							master-reference="PageMaster-index-artnrright" odd-or-even="odd" />
					</fo:repeatable-page-master-alternatives>
				</fo:page-sequence-master>

				<!-- Define the table-of-contents (TOC) page. -->
				<fo:simple-page-master master-name="PageMaster-index-artgrleft"
					page-height="297mm" page-width="210mm" margin-top="8mm"
					margin-bottom="8mm" margin-left="10mm" margin-right="20mm">
					<fo:region-body margin-top="15mm" margin-bottom="10mm" column-count="2" column-gap="5mm" />
					<!-- Header -->
					<fo:region-before region-name="Header-index-artgrleft" extent="10mm" display-align="after"/>
					<!-- Footer -->
					<fo:region-after extent="10mm" />
				</fo:simple-page-master>
				<fo:simple-page-master master-name="PageMaster-index-artgrright"
					page-height="297mm" page-width="210mm" margin-top="8mm"
					margin-bottom="8mm" margin-left="20mm" margin-right="10mm">
					<fo:region-body margin-top="15mm" margin-bottom="10mm" column-count="2" column-gap="5mm" />
					<!-- Header -->
					<fo:region-before region-name="Header-index-artgrright" extent="10mm" display-align="after"/>
					<!-- Footer -->
					<fo:region-after extent="10mm" />
				</fo:simple-page-master>
				<fo:page-sequence-master master-name="PageMaster-index-artgr">
					<fo:repeatable-page-master-alternatives>
						<fo:conditional-page-master-reference
							master-reference="PageMaster-index-artgrleft" odd-or-even="even" />
						<fo:conditional-page-master-reference
							master-reference="PageMaster-index-artgrright" odd-or-even="odd" />
					</fo:repeatable-page-master-alternatives>
				</fo:page-sequence-master>

				<!-- Define the table-of-contents (TOC) page. -->
				<fo:simple-page-master master-name="PageMaster-TOCleft"
					page-height="297mm" page-width="210mm" margin-top="8mm"
					margin-bottom="8mm" margin-left="10mm" margin-right="20mm">
					<fo:region-body margin-top="15mm" margin-bottom="10mm" />
					<!-- Header -->
					<fo:region-before region-name="Header-tocleft" extent="10mm" display-align="after"/>
					<!-- Footer -->
					<fo:region-after extent="10mm" />
				</fo:simple-page-master>
				<fo:simple-page-master master-name="PageMaster-TOCright"
					page-height="297mm" page-width="210mm" margin-top="8mm"
					margin-bottom="8mm" margin-left="20mm" margin-right="10mm">
					<fo:region-body margin-top="15mm" margin-bottom="10mm" />
					<!-- Header -->
					<fo:region-before region-name="Header-tocright" extent="10mm" display-align="after"/>
					<!-- Footer -->
					<fo:region-after extent="10mm" />
				</fo:simple-page-master>
				<fo:page-sequence-master master-name="PageMaster-TOC">
					<fo:repeatable-page-master-alternatives>
						<fo:conditional-page-master-reference
							master-reference="PageMaster-TOCleft" odd-or-even="even" />
						<fo:conditional-page-master-reference
							master-reference="PageMaster-TOCright" odd-or-even="odd" />
					</fo:repeatable-page-master-alternatives>
				</fo:page-sequence-master>

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

			<!-- table-of-contents (TOC) page -->
			<xsl:call-template name="toc" />

			<!-- Body page -->
			<fo:page-sequence master-reference="contents"
				initial-page-number="1">

				<!-- Define the contents of the header. -->
<!--
				<fo:static-content flow-name="xsl-region-before">
					<fo:block font-size="14.0pt" font-family="sans-serif"
						padding-after="4.0pt" space-before="4.0pt" text-align="center"
						border-bottom-style="solid" border-bottom-width="1.0pt">
						<xsl:text>MODIS Preisliste</xsl:text>
					</fo:block>
				</fo:static-content>
-->
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

				<!--
					The main contents of the body page, that is, the catalog entries
				-->
				<fo:flow flow-name="xsl-region-body">
					<fo:block font-size="10.0pt" font-family="sans-serif"
						space-before="3mm">
						<xsl:apply-templates select="doc">
						</xsl:apply-templates>
					</fo:block>
				</fo:flow>
			</fo:page-sequence>

			<!-- table-of-contents (TOC) page -->
			<xsl:call-template name="index-artnr.create" />

			<!-- table-of-contents (TOC) page -->
			<xsl:call-template name="index-artgr.create" />

		</fo:root>
	</xsl:template>

	<xsl:template match="Chapters">
		<xsl:apply-templates />
	</xsl:template>

	<!-- Format entries from angebot.xml -->
<!--
	<xsl:template match="ArtGr">
		<xsl:variable name="imageURI" select="./Image" />
		<fo:external-graphic src="url('{$imageURI}')"
			padding-left="22.5mm" padding-bottom="2mm" content-height="30mm"
			content-width="40mm" keep-with-next="always" />
		<fo:block font-size="9.0pt" padding-top="2mm" keep-with-next="always">
			<xsl:apply-templates select="Description">
			</xsl:apply-templates>
		</fo:block>
		<xsl:apply-templates select="Prices" />
	</xsl:template>
-->
	<xsl:template match="ArtGr">
		<fo:block
			border-top-style="solid"
			border-bottom-style="solid" 
			border-left-style="solid" 
			border-right-style="solid"
			keep-together.within-column="always"
			space-before="2mm"
			space-after="2mm"
			>
			<fo:block
				margin-top="2mm"
				margin-bottom="2mm"
				margin-left="2mm"
				margin-right="2mm"
				>
				<xsl:call-template name="title.out" />
				<xsl:variable name="imageURI" select="./Image" />
				<fo:external-graphic src="url('{$imageURI}')"
					padding-left="22.5mm" padding-bottom="2mm" content-height="30mm"
					content-width="40mm" keep-with-next="always" />
				<fo:block font-size="9.0pt" padding-top="2mm">
					<xsl:apply-templates select="Description" />
				</fo:block>
				<xsl:apply-templates select="index-artgr" />
				<xsl:apply-templates select="Prices" />
			</fo:block>
		</fo:block>
	</xsl:template>


	<!-- Format entries from angebot.xml -->
	<xsl:template match="Artikel">
		<fo:block
			border-top-style="solid"
			border-bottom-style="solid" 
			border-left-style="solid" 
			border-right-style="solid"
			keep-together.within-column="always"
			space-before="2mm"
			space-after="2mm"
			>
			<fo:block
				margin-top="2mm"
				margin-bottom="2mm"
				margin-left="2mm"
				margin-right="2mm"
				>
				<xsl:call-template name="title.out" />
				<xsl:variable name="imageURI" select="./Image" />
				<fo:block font-size="10.0pt" font-weight="bold" padding-top="6mm"
					keep-with-next="always">
					<xsl:value-of select="./ArtGrName" />
				</fo:block>
				<fo:external-graphic src="url('{$imageURI}')"
					padding-left="22.5mm" padding-bottom="2mm" content-height="30mm"
					content-width="40mm" keep-with-next="always" />
				<fo:block font-size="9.0pt" padding-top="2mm">
					<xsl:apply-templates select="./Description">
					</xsl:apply-templates>
				</fo:block>
				<xsl:apply-templates select="Prices" />
			</fo:block>
		</fo:block>
	</xsl:template>

	<xsl:template match="Prices">
		<!-- Put entire entry within a single block. -->
		<xsl:if test="Price">
			<fo:block font-family="sans-serif" font-size="8.0pt"
				space-after="2.0pt">
				<xsl:call-template name="PriceListTempl">
					<xsl:with-param name="PriceList" select="Price" />
				</xsl:call-template>
			</fo:block>
		</xsl:if>
	</xsl:template>

	<!-- Useful template for listing multiple items (such as authors). -->
	<xsl:template name="PriceListTempl">
		<xsl:param name="PriceList" />
		<fo:table>
			<fo:table-column column-width="21mm" />
			<fo:table-column column-width="46mm" />
			<fo:table-column column-width="13mm" />
			<fo:table-header>
				<fo:table-row>
					<fo:table-cell xsl:use-attribute-sets="table.data.th">
						<fo:block font-weight="bold">Artikelnr.</fo:block>
					</fo:table-cell>
					<fo:table-cell xsl:use-attribute-sets="table.data.th">
						<fo:block font-weight="bold">Beschreibung</fo:block>
					</fo:table-cell>
					<fo:table-cell xsl:use-attribute-sets="table.data.th">
						<fo:block font-weight="bold" text-align="right">Preis</fo:block>
					</fo:table-cell>
				</fo:table-row>
			</fo:table-header>
			<fo:table-body>
				<xsl:for-each select="$PriceList[position()]">
					<fo:table-row>
						<fo:table-cell xsl:use-attribute-sets="table.data.td">
							<fo:block>
<!-- 								<xsl:value-of select="ArtikelNr" /> -->
								<xsl:apply-templates select="ArtikelNr" />
							</fo:block>
						</fo:table-cell>
 						<fo:table-cell xsl:use-attribute-sets="table.data.td">
							<fo:block>
								<xsl:value-of select="Text" />
							</fo:block>
						</fo:table-cell>
<!-- 
						<fo:table-cell xsl:use-attribute-sets="table.data.td">
							<fo:block text-align="right">
								<xsl:value-of select="./Menge" />
							</fo:block>
						</fo:table-cell>
 -->
						<fo:table-cell xsl:use-attribute-sets="table.data.td">
							<fo:block text-align="right">
								<xsl:value-of select="./Preis" />
							</fo:block>
						</fo:table-cell>
					</fo:table-row>
				</xsl:for-each>
			</fo:table-body>
		</fo:table>
	</xsl:template>

	<xsl:template name="toc">
		<fo:page-sequence master-reference="PageMaster-TOC" force-page-count="no-force">
			<fo:flow flow-name="xsl-region-body">
				<fo:block xsl:use-attribute-sets="div.toc">
					<fo:block xsl:use-attribute-sets="toc" space-after="10pt">
						<xsl:choose>
							<xsl:when test="(/doc/@lang = 'ja') or (/doc/@lang = '') or not(/doc/@lang)">目 次</xsl:when>
							<xsl:otherwise>Verzeichnis der Produktgruppen</xsl:otherwise>
						</xsl:choose>
					</fo:block>
					<xsl:for-each select="//KatGr | //SubKatGr | //SubSubKatGr | //SubSubSubKatGr | //ArtGr | //appendix">
						<xsl:call-template name="toc.line"/>
					</xsl:for-each>

					<!-- add index page -->
					<xsl:if test="$index-artnr-make or @index-artnr!='false'">
						<xsl:if test="//index-artnr">
							<fo:block text-align-last="justify" space-before="5pt" font-weight="700">
								<fo:basic-link internal-destination="index-artnr-page">
									<xsl:choose>
										<xsl:when test="(/doc/@lang = 'ja') or (/doc/@lang = '') or not(/doc/@lang)">索引</xsl:when>
										<xsl:otherwise>Verzeichnis der Artikelnummern</xsl:otherwise>
									</xsl:choose>
								</fo:basic-link>
								<fo:leader leader-pattern="dots"/>
								<fo:page-number-citation ref-id="index-artnr-page"/>
							</fo:block>
						</xsl:if>
					</xsl:if>

					<!-- add index page -->
					<xsl:if test="$index-artgr-make or @index-artgr!='false'">
						<xsl:if test="//index-artgr">
							<fo:block text-align-last="justify" space-before="5pt" font-weight="700">
								<fo:basic-link internal-destination="index-artgr-page">
									<xsl:choose>
										<xsl:when test="(/doc/@lang = 'ja') or (/doc/@lang = '') or not(/doc/@lang)">索引</xsl:when>
										<xsl:otherwise>Verzeichnis der Artikel</xsl:otherwise>
									</xsl:choose>
								</fo:basic-link>
								<fo:leader leader-pattern="dots"/>
								<fo:page-number-citation ref-id="index-artgr-page"/>
							</fo:block>
						</xsl:if>
					</xsl:if>

				</fo:block>
			</fo:flow>
		</fo:page-sequence>
	</xsl:template>
	<xsl:template name="toc.line">
		<xsl:variable name="level" select="count(ancestor-or-self::KatGr | ancestor-or-self::SubKatGr | ancestor-or-self::SubSubKatGr | ancestor-or-self::SubSubSubKatGr | ancestor-or-self::ArtGr)"/>
		<xsl:if test="$level &lt;= $toc-level-max">
			<fo:block text-align-last="justify">
				<xsl:attribute name="margin-left">
					<xsl:value-of select="($level - 1) * 2"/>
					<xsl:text>em</xsl:text>
				</xsl:attribute>
				<xsl:attribute name="space-before">
					<xsl:choose>
						<xsl:when test="$level=1">4pt</xsl:when>
						<xsl:when test="$level=2">3pt</xsl:when>
						<xsl:when test="$level=3">3pt</xsl:when>
						<xsl:otherwise>1pt</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				<xsl:attribute name="space-before.conditionality">retain</xsl:attribute>
				<xsl:attribute name="space-after">
					<xsl:choose>
						<xsl:when test="$level=2">3pt</xsl:when>
						<xsl:when test="$level=3">1pt</xsl:when>
						<xsl:otherwise>1pt</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				<xsl:attribute name="space-after.conditionality">retain</xsl:attribute>
				<xsl:attribute name="font-size">
					<xsl:choose>
						<xsl:when test="$level=1">1em</xsl:when>
						<xsl:otherwise>0.9em</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				<xsl:attribute name="font-weight">
					<xsl:value-of select="800 - $level * 100"/>
				</xsl:attribute>
				<fo:basic-link internal-destination="{generate-id()}">
					<xsl:value-of select="Title"/>
				</fo:basic-link>
				<fo:inline font-weight="normal" font-size="10pt">
					<fo:leader leader-pattern="dots"/>
					<fo:page-number-citation ref-id="{generate-id()}"/>
				</fo:inline>
			</fo:block>
		</xsl:if>
	</xsl:template>

	<xsl:template match="index-artnr">
		<xsl:variable name="key" select="concat(@key,@keyword,.)" />
		<xsl:variable name="initial" select="substring($key,1,1)" />
		<xsl:if test="$initial=''">
			<xsl:message terminate="yes">
				<xsl:text>Empty keyword.&#10;</xsl:text>
			</xsl:message>
		</xsl:if>
		<xsl:if
			test="not(contains('&ALPHA;',$initial) or
                  contains('&LOWER;',$initial))">
			<xsl:message terminate="yes">
				<xsl:text>Invalid keyword: </xsl:text>
				<xsl:value-of select="." />
				<xsl:if test="@key!=''">
					<xsl:text> (key="</xsl:text>
					<xsl:value-of select="@key" />
					<xsl:text>")</xsl:text>
				</xsl:if>
				<xsl:text>&#10;</xsl:text>
			</xsl:message>
		</xsl:if>
		<fo:inline id="{generate-id()}">
 			<xsl:apply-templates />
		</fo:inline>
	</xsl:template>

	<xsl:template match="index-artnr" mode="line">
		<fo:basic-link internal-destination="{generate-id()}">
			<fo:page-number-citation ref-id="{generate-id()}" />
		</fo:basic-link>
		<xsl:if test="position() != last()">
			,
		</xsl:if>
	</xsl:template>

	<xsl:template name="index-artnr.create">
		<fo:page-sequence master-reference="PageMaster-index-artnr"
				initial-page-number="i">
			<!-- Set a document name in the header region. -->
<!--
			<fo:static-content flow-name="xsl-region-before">
				<fo:block font-size="7pt" border-after-width="thin">
					<xsl:value-of select="/doc/head/title" />
				</fo:block>
			</fo:static-content>
-->
				<fo:static-content flow-name="Header-index-artnrright">
					<fo:block font-size="9pt" text-align="outside" space-after="3mm" border-bottom-style="solid" border-bottom-width="1.0pt">
						<fo:inline font-size="17pt"><fo:page-number/></fo:inline>
					</fo:block>
				</fo:static-content>
				<fo:static-content flow-name="Header-index-artnrleft">
					<fo:block font-size="9pt" text-align="inside" space-after="3mm" border-bottom-style="solid" border-bottom-width="1.0pt">
						<fo:inline font-size="17pt"><fo:page-number/></fo:inline>
					</fo:block>
				</fo:static-content>
			<fo:flow flow-name="xsl-region-body">
				<fo:block span="all" text-align="center">
					<fo:block line-height="15mm" background-color="#9bd49d"
						font-size="20pt" space-after="10mm" id="index-artnr-page">
						Verzeichnis der Artikelnummern
				</fo:block>
					<fo:block>
						&#xA0; </fo:block>
				</fo:block>
				<fo:block>
					<xsl:call-template name="index-artnr.create.main">
						<xsl:with-param name="initial-str" select="'&ALPHA;'" />
					</xsl:call-template>
				</fo:block>
			</fo:flow>
		</fo:page-sequence>
	</xsl:template>

	<xsl:template name="index-artnr.create.main">
		<xsl:param name="initial-str" />
		<xsl:if test="$initial-str != ''">
			<xsl:call-template name="index-artnr.create.section">
				<xsl:with-param name="letter" select="substring($initial-str,1,1)" />
			</xsl:call-template>
			<xsl:call-template name="index-artnr.create.main">
				<xsl:with-param name="initial-str" select="substring($initial-str,2)" />
			</xsl:call-template>
		</xsl:if>
	</xsl:template>

	<xsl:template name="index-artnr.create.section">
		<!-- Recieve the character to be processed -->
		<xsl:param name="letter" />
		<!--
			Set the nodes who have the 'letter' value of the key to the terms
			variable and sort them.
		-->
		<xsl:variable name="terms" select="key('index-artnr-key',$letter)" />
		<!--
			Get the index which has the same head-one character as the parameter
			which received.
		-->
		<xsl:if test="$terms">
			<!-- Output key characters -->
			<fo:block font-weight="bold" text-align="center"
				space-before="1em">
				<!-- Output the received one character as is. -->
				<xsl:value-of select="$letter" />
			</fo:block>
			<xsl:for-each
				select="$terms[ not(
  (    @keyword  and (@keyword=preceding::index[not(@keyword)]/text() or
                      @keyword=preceding::index/@keyword)) or
  (not(@keyword) and (       .=preceding::index[not(@keyword)]/text() or
                             .=preceding::index/@keyword)) ) ]">
				<!-- Make it a rule not to process the same text redundantly -->
				<xsl:sort
					select="@key | @keyword[not(@key)] | text()[not(@key) and not(@keyword)]" />
				<!-- Sort the text in key order -->
				<fo:block text-align="justify" text-align-last="justify"  xsl:use-attribute-sets="index-artnr-line">
					<!--
						[<xsl:value-of select="@key | @keyword[not(@key)] |
						text()[not(@key) and not(@keyword)]"/>]
					-->
					<xsl:variable name="kw">
						<xsl:choose>
							<xsl:when test="string(@keyword)=''">
								<xsl:value-of select="." />
								<!-- Output text as is -->
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="@keyword" />
							</xsl:otherwise>
						</xsl:choose>
						<!--xsl:value-of select="@addkeyword"/-->
					</xsl:variable>
					<xsl:value-of select="$kw" />
					<fo:leader leader-pattern="use-content">.</fo:leader>
					<fo:inline keep-together.within-line="always">
						<xsl:variable name="terms2"
							select="$terms[
            (string(@keyword)!='' and @keyword=$kw) or
            (string(@keyword) ='' and .=$kw) ]" />
						<!-- Get the same text -->
						<xsl:if test="$terms2">
							<fo:inline>
								<!--
									Output all of those page numbers when the same text exists in
									two or more places
								-->
								<xsl:apply-templates select="$terms2" mode="line" />
							</fo:inline>
						</xsl:if>
					</fo:inline>
				</fo:block>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>

	<xsl:template match="index-artgr">
		<xsl:variable name="key" select="concat(@key,@keyword,.)" />
		<xsl:variable name="initial" select="substring($key,1,1)" />
		<xsl:if test="$initial=''">
			<xsl:message terminate="yes">
				<xsl:text>Empty keyword.&#10;</xsl:text>
			</xsl:message>
		</xsl:if>
		<xsl:if
			test="not(contains('&ALPHA;',$initial) or
                  contains('&LOWER;',$initial))">
			<xsl:message terminate="yes">
				<xsl:text>Invalid keyword: </xsl:text>
				<xsl:value-of select="." />
				<xsl:if test="@key!=''">
					<xsl:text> (key="</xsl:text>
					<xsl:value-of select="@key" />
					<xsl:text>")</xsl:text>
				</xsl:if>
				<xsl:text>&#10;</xsl:text>
			</xsl:message>
		</xsl:if>
		<fo:inline id="{generate-id()}">
<!-- 			<xsl:apply-templates />-->
		</fo:inline>
	</xsl:template>

	<xsl:template match="index-artgr" mode="line">
		<fo:basic-link internal-destination="{generate-id()}">
			<fo:page-number-citation ref-id="{generate-id()}" />
		</fo:basic-link>
		<xsl:if test="position() != last()">
			,
		</xsl:if>
	</xsl:template>

	<xsl:template name="index-artgr.create">
		<fo:page-sequence master-reference="PageMaster-index-artgr"
				initial-page-number="I">
			<!-- Set a document name in the header region. -->
<!--
			<fo:static-content flow-name="xsl-region-before">
				<fo:block font-size="7pt" border-after-width="thin">
					<xsl:value-of select="/doc/head/title" />
				</fo:block>
			</fo:static-content>
-->
				<fo:static-content flow-name="Header-index-artgrright">
					<fo:block font-size="9pt" text-align="outside" space-after="3mm" border-bottom-style="solid" border-bottom-width="1.0pt">
						<fo:inline font-size="17pt"><fo:page-number/></fo:inline>
					</fo:block>
				</fo:static-content>
				<fo:static-content flow-name="Header-index-artgrleft">
					<fo:block font-size="9pt" text-align="inside" space-after="3mm" border-bottom-style="solid" border-bottom-width="1.0pt">
						<fo:inline font-size="17pt"><fo:page-number/></fo:inline>
					</fo:block>
				</fo:static-content>
			<fo:flow flow-name="xsl-region-body">
				<fo:block span="all" text-align="center">
					<fo:block line-height="15mm" background-color="#9bd49d"
						font-size="20pt" space-after="10mm" id="index-artgr-page">
						Verzeichnis der Artikel
				</fo:block>
					<fo:block>
						&#xA0; </fo:block>
				</fo:block>
				<fo:block>
					<xsl:call-template name="index-artgr.create.main">
						<xsl:with-param name="initial-str" select="'&ALPHA;'" />
					</xsl:call-template>
				</fo:block>
			</fo:flow>
		</fo:page-sequence>
	</xsl:template>

	<xsl:template name="index-artgr.create.main">
		<xsl:param name="initial-str" />
		<xsl:if test="$initial-str != ''">
			<xsl:call-template name="index-artgr.create.section">
				<xsl:with-param name="letter" select="substring($initial-str,1,1)" />
			</xsl:call-template>
			<xsl:call-template name="index-artgr.create.main">
				<xsl:with-param name="initial-str" select="substring($initial-str,2)" />
			</xsl:call-template>
		</xsl:if>
	</xsl:template>

	<xsl:template name="index-artgr.create.section">
		<!-- Recieve the character to be processed -->
		<xsl:param name="letter" />
		<!--
			Set the nodes who have the 'letter' value of the key to the terms
			variable and sort them.
		-->
		<xsl:variable name="terms" select="key('index-artgr-key',$letter)" />
		<!--
			Get the index which has the same head-one character as the parameter
			which received.
		-->
		<xsl:if test="$terms">
			<!-- Output key characters -->
			<fo:block font-weight="bold" text-align="center"
				space-before="1em">
				<!-- Output the received one character as is. -->
				<xsl:value-of select="$letter" />
			</fo:block>
			<xsl:for-each
				select="$terms[ not(
  (    @keyword  and (@keyword=preceding::index[not(@keyword)]/text() or
                      @keyword=preceding::index/@keyword)) or
  (not(@keyword) and (       .=preceding::index[not(@keyword)]/text() or
                             .=preceding::index/@keyword)) ) ]">
				<!-- Make it a rule not to process the same text redundantly -->
				<xsl:sort
					select="@key | @keyword[not(@key)] | text()[not(@key) and not(@keyword)]" />
				<!-- Sort the text in key order -->
				<fo:block text-align="justify" text-align-last="justify"  xsl:use-attribute-sets="index-artgr-line">
					<!--
						[<xsl:value-of select="@key | @keyword[not(@key)] |
						text()[not(@key) and not(@keyword)]"/>]
					-->
					<xsl:variable name="kw">
						<xsl:choose>
							<xsl:when test="string(@keyword)=''">
								<xsl:value-of select="." />
								<!-- Output text as is -->
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="@keyword" />
							</xsl:otherwise>
						</xsl:choose>
						<!--xsl:value-of select="@addkeyword"/-->
					</xsl:variable>
					<xsl:value-of select="$kw" />
					<fo:leader leader-pattern="use-content">.</fo:leader>
					<fo:inline keep-together.within-line="always">
						<xsl:variable name="terms2"
							select="$terms[
            (string(@keyword)!='' and @keyword=$kw) or
            (string(@keyword) ='' and .=$kw) ]" />
						<!-- Get the same text -->
						<xsl:if test="$terms2">
							<fo:inline>
								<!--
									Output all of those page numbers when the same text exists in
									two or more places
								-->
								<xsl:apply-templates select="$terms2" mode="line" />
							</fo:inline>
						</xsl:if>
					</fo:inline>
				</fo:block>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>

	<!-- The template that creates the table of contents -->
	<xsl:variable name="toc-level-max">
		<xsl:choose>
			<xsl:when test="not (doc/@toclevel)">
				<xsl:value-of select="$toc-level-default" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="number(doc/@toclevel)" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>

	<xsl:template match="KatGr | SubKatGr | SubSubKatGr | SubSubSubKatGr">
		<xsl:call-template name="title.out" />
		<xsl:variable name="imageURI" select="./Image" />
		<fo:external-graphic src="url('{$imageURI}')"
			padding-left="22.5mm" padding-bottom="2mm" content-height="30mm"
			content-width="40mm" />
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template
		match="KatGr/Title | SubKatGr/Title | SubSubKatGr/Title | SubSubSubKatGr/Title | ArtGr/Title">
	</xsl:template>

	<xsl:template name="title.out">
		<xsl:variable name="level"
			select="count(ancestor-or-self::KatGr |
		ancestor-or-self::SubKatGr |
		ancestor-or-self::SubSubKatGr |
		ancestor-or-self::SubSubSubKatGr |
		ancestor-or-self::ArtGr)" />
		<xsl:choose>
			<xsl:when test="$level=1">
				<fo:block xsl:use-attribute-sets="h1" id="{generate-id()}">

					<xsl:value-of select="Title" />
				</fo:block>
			</xsl:when>
			<xsl:when test="$level=2">
				<fo:block xsl:use-attribute-sets="h2" id="{generate-id()}">
					<xsl:value-of select="Title" />
				</fo:block>
			</xsl:when>
			<xsl:when test="$level=3">
				<fo:block xsl:use-attribute-sets="h3" id="{generate-id()}">
					<xsl:value-of select="Title" />
				</fo:block>
			</xsl:when>
			<xsl:when test="$level=4">
				<fo:block xsl:use-attribute-sets="h4" id="{generate-id()}">
					<xsl:value-of select="Title" />
				</fo:block>
			</xsl:when>
			<xsl:when test="$level=5">
				<fo:block xsl:use-attribute-sets="h5" id="{generate-id()}">
					<xsl:value-of select="Title" />
				</fo:block>
			</xsl:when>
			<xsl:otherwise>
				<fo:block xsl:use-attribute-sets="h6" id="{generate-id()}">

					<xsl:value-of select="Title" />
				</fo:block>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="ul">
		<!-- The distance between the list-label and the list-body is decided.  -->
		<xsl:variable name="start-dist-local">
			<xsl:choose>
				<xsl:when test="./@startdist">
					<xsl:value-of select="./@startdist"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$list-startdist-default"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="gap-local">
			<xsl:choose>
				<xsl:when test="./@gap">
					<xsl:value-of select="./@gap"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$list-gap-default"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<!-- fo:list-block generate -->
		<fo:list-block provisional-distance-between-starts="{$start-dist-local}" provisional-label-separation="{$gap-local}">
			<!-- processing child element -->
			<xsl:apply-templates/>
		</fo:list-block>
	</xsl:template>
	<xsl:template match="ul/li">
		<fo:list-item xsl:use-attribute-sets="list.item">
			<!-- list-label generation-->
			<!-- The label character is decided by the type attribute. -->
			<fo:list-item-label end-indent="label-end()">
				<fo:block text-align="end">
					<xsl:choose>
						<xsl:when test="../@type='disc'">
							<xsl:text>●</xsl:text>
						</xsl:when>
						<xsl:when test="../@type='circle'">
							<xsl:text>○</xsl:text>
						</xsl:when>
						<xsl:when test="../@type='square'">
							<xsl:text>□</xsl:text>
						</xsl:when>
						<xsl:when test="../@type='bsquare'">
							<xsl:text>■</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>&#8226;</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</fo:block>
			</fo:list-item-label>
			<!-- list-body generation -->
			<fo:list-item-body start-indent="body-start()" text-align="justify">
				<fo:block>
					<xsl:apply-templates/>
				</fo:block>
			</fo:list-item-body>
		</fo:list-item>
	</xsl:template>
	<!-- Template to use image for label-->
	<xsl:template match="ul[substring(@type,1,4)='img:']/li">
		<fo:list-item xsl:use-attribute-sets="list.item">
			<fo:list-item-label end-indent="label-end()">
				<fo:block text-align="end">
					<fo:external-graphic src="{substring-after(../@type,substring(../@type,1,4))}" content-height="1.2em" content-width="1.2em"/>
				</fo:block>
			</fo:list-item-label>
			<fo:list-item-body start-indent="body-start()" text-align="justify">
				<fo:block>
					<xsl:apply-templates/>
				</fo:block>
			</fo:list-item-body>
		</fo:list-item>
	</xsl:template>
	<xsl:template match="ol">
		<xsl:variable name="start-dist-local">
			<xsl:choose>
				<xsl:when test="./@startdist">
					<xsl:value-of select="./@startdist"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$list-startdist-default"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="gap-local">
			<xsl:choose>
				<xsl:when test="./@gap">
					<xsl:value-of select="./@gap"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$list-gap-default"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<!-- fo:list-block generation -->
		<fo:list-block provisional-distance-between-starts="{$start-dist-local}" provisional-label-separation="{$gap-local}">
			<!-- processing child element -->
			<xsl:apply-templates/>
		</fo:list-block>
	</xsl:template>
	<xsl:template match="ol/li">
		<fo:list-item xsl:use-attribute-sets="list.item">
			<!-- list-label generation -->
			<!-- The label format is specified by the type attribute. The default is '1'.-->
			<fo:list-item-label end-indent="label-end()">
				<fo:block text-align="end">
					<xsl:choose>
						<xsl:when test="../@type">
							<xsl:number format="{../@type}"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:number format="1."/>
						</xsl:otherwise>
					</xsl:choose>
				</fo:block>
			</fo:list-item-label>
			<!-- list-body generation-->
			<fo:list-item-body start-indent="body-start()" text-align="justify">
				<fo:block>
					<!-- processing child element-->
					<xsl:apply-templates/>
				</fo:block>
			</fo:list-item-body>
		</fo:list-item>
	</xsl:template>
	<xsl:template match="dl">
		<xsl:choose>
			<xsl:when test="@type='list'">
				<xsl:call-template name="dl.format.list"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="dl.format.block"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="dl.format.block">
		<xsl:apply-templates/>
	</xsl:template>
	<xsl:template name="dl.format.list">
		<xsl:variable name="start-dist-local">
			<xsl:choose>
				<xsl:when test="./@startdist">
					<xsl:value-of select="./@startdist"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$dl-startdist-default"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="gap-local">
			<xsl:choose>
				<xsl:when test="./@gap">
					<xsl:value-of select="./@gap"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$dl-gap-default"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<fo:list-block provisional-distance-between-starts="{$start-dist-local}" provisional-label-separation="{$gap-local}">
			<xsl:call-template name="process.dl.list"/>
		</fo:list-block>
	</xsl:template>
	<xsl:template name="process.dl.list">
		<xsl:param name="dts" select="/.."/>
		<xsl:param name="dds" select="/.."/>
		<xsl:param name="nodes" select="*"/>
		<xsl:choose>
			<xsl:when test="count($nodes)=0">
				<!-- Data end : processing of data in dts and dds -->
				<xsl:if test="count($dts)&gt;0 or count($dds)&gt;0">
					<fo:list-item xsl:use-attribute-sets="list.item">
						<fo:list-item-label end-indent="label-end()">
							<xsl:apply-templates select="$dts"/>
						</fo:list-item-label>
						<fo:list-item-body start-indent="body-start()">
							<xsl:apply-templates select="$dds"/>
						</fo:list-item-body>
					</fo:list-item>
				</xsl:if>
			</xsl:when>
			<xsl:when test="name($nodes[1])='dd'">
				<!-- 'dd' is memorized in variable 'dds'. -->
				<xsl:call-template name="process.dl.list">
					<xsl:with-param name="dts" select="$dts"/>
					<xsl:with-param name="dds" select="$dds|$nodes[1]"/>
					<xsl:with-param name="nodes" select="$nodes[position()&gt;1]"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="name($nodes[1])='dt'">
				<!-- processing of data in dts and dds -->
				<xsl:if test="count($dts)&gt;0 or count($dds)&gt;0">
					<fo:list-item xsl:use-attribute-sets="list.item">
						<fo:list-item-label end-indent="label-end()">
							<xsl:apply-templates select="$dts"/>
						</fo:list-item-label>
						<fo:list-item-body start-indent="body-start()">
							<xsl:apply-templates select="$dds"/>
						</fo:list-item-body>
					</fo:list-item>
				</xsl:if>
				<!-- 'dt' is memorized in variable 'dts'. -->
				<xsl:call-template name="process.dl.list">
					<xsl:with-param name="dts" select="$nodes[1]"/>
					<xsl:with-param name="nodes" select="$nodes[position()&gt;1]"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<!-- error -->
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="dt">
		<xsl:element name="fo:block" use-attribute-sets="dt">
			<xsl:if test="../@mode='debug'">
				<xsl:attribute name="border-color">blue</xsl:attribute>
				<xsl:attribute name="border-style">dashed</xsl:attribute>
				<xsl:attribute name="border-width">thin</xsl:attribute>
			</xsl:if>
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>

	<xsl:template match="dd">
		<xsl:choose>
			<xsl:when test="../@type='list'">
				<xsl:element name="fo:block" use-attribute-sets="dd.list">
					<xsl:if test="../@mode='debug'">
						<xsl:attribute name="border-color">red</xsl:attribute>
						<xsl:attribute name="border-style">solid</xsl:attribute>
						<xsl:attribute name="border-width">thin</xsl:attribute>
					</xsl:if>
					<xsl:apply-templates/>
				</xsl:element>
			</xsl:when>
			<xsl:otherwise>
				<xsl:element name="fo:block" use-attribute-sets="dd.block">
					<xsl:if test="../@mode='debug'">
						<xsl:attribute name="border-color">red</xsl:attribute>
						<xsl:attribute name="border-style">solid</xsl:attribute>
						<xsl:attribute name="border-width">thin</xsl:attribute>
					</xsl:if>
					<xsl:apply-templates/>
				</xsl:element>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="Copyright">
	</xsl:template>

	<xsl:template match="Scope">
	</xsl:template>

	<xsl:template match="Date">
	</xsl:template>

	<xsl:template match="ArtGrNr">
	</xsl:template>

	<xsl:template match="KatGrNr">
	</xsl:template>

	<xsl:template match="Description">
		<fo:block font-size="9.0pt" padding-top="2mm" keep-with-next="always">
			<xsl:apply-templates />
		</fo:block>
	</xsl:template>

	<xsl:template match="p">
		<fo:block xsl:use-attribute-sets="p">
			<xsl:apply-templates />
		</fo:block>
	</xsl:template>

	<xsl:template match="b">
		<fo:block font-weight="bold">
			<xsl:value-of select="." />
		</fo:block>
	</xsl:template>

	<xsl:template match="br">
		<fo:block>
		</fo:block>
	</xsl:template>

	<xsl:template match="Image">
	</xsl:template>

	
</xsl:stylesheet>