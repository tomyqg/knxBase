<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE DOCUMENT [
	<!ENTITY ALPHA "@0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ">
	<!ENTITY LOWER "abcdefghijklmnopqrstuvwxyz">
	<!ENTITY UPPER "ABCDEFGHIJKLMNOPQRSTUVWXYZ">
]>

<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">
	<xsl:output method="xml" encoding="UTF-8" indent="yes" />

	<!-- Specify a paper size -->
	<!-- Refer to $paper-width, $paper-height for the value. -->
	<xsl:param name="paper-width-default">
		210mm
	</xsl:param>
	<xsl:param name="paper-height-default">
		297mm
	</xsl:param>
	<xsl:param name="paper-width">
		210mm
	</xsl:param>
	<xsl:param name="paper-height">
		297mm
	</xsl:param>

	<xsl:param name="list-startdist" />
	<xsl:param name="list-gap" />
	<xsl:param name="dl-startdist" />
	<xsl:param name="dl-gap" />
	<xsl:param name="list-startdist-default-ja" select="string('2em')"/>
	<xsl:param name="list-gap-default-ja" select="string('0.2em')"/>
	<xsl:param name="dl-startdist-default-ja" select="string('3cm')"/>
	<xsl:param name="dl-gap-default-ja" select="string('0.25cm')"/>
	<xsl:param name="list-startdist-default-en" select="string('2em')"/>
	<xsl:param name="list-gap-default-en" select="string('0.5em')"/>
	<xsl:param name="dl-startdist-default-en" select="string('3cm')"/>
	<xsl:param name="dl-gap-default-en" select="string('0.5cm')"/>
	<xsl:variable name="list-startdist-default">
	<!-- param already set -->
	<xsl:if test="$list-startdist!=''">
	<xsl:value-of select="$list-startdist" />
	</xsl:if>
	<!-- param not setting -->
	<xsl:if test="$list-startdist=''">
	<xsl:if test="/doc/@lang='en'">
	<xsl:value-of select="$list-startdist-default-en" />
	</xsl:if>
	<xsl:if test="/doc/@lang = 'ja'">
	<xsl:value-of select="$list-startdist-default-ja" />
	</xsl:if>
	<xsl:if test="not(/doc/@lang)">
	<xsl:value-of select="$list-startdist-default-ja" />
	</xsl:if>
	</xsl:if>
	</xsl:variable>
	
	<xsl:variable name="list-gap-default">
	<xsl:if test="$list-gap!=''">
	<xsl:value-of select="$list-gap" />
	</xsl:if>
	<xsl:if test="$list-gap=''">
	<xsl:if test="/doc/@lang='en'">
	<xsl:value-of select="$list-gap-default-en" />
	</xsl:if>
	<xsl:if test="/doc/@lang = 'ja'">
	<xsl:value-of select="$list-gap-default-ja" />
	</xsl:if>
	<xsl:if test="not(/doc/@lang)">
	<xsl:value-of select="$list-gap-default-ja" />
	</xsl:if>
	</xsl:if>
	</xsl:variable>
	
	<xsl:variable name="dl-startdist-default">
	<xsl:if test="$dl-startdist!=''">
	<xsl:value-of select="$dl-startdist" />
	</xsl:if>
	<xsl:if test="$dl-startdist=''">
	<xsl:if test="/doc/@lang='en'">
	<xsl:value-of select="$dl-startdist-default-en" />
	</xsl:if>
	<xsl:if test="/doc/@lang = 'ja'">
	<xsl:value-of select="$dl-startdist-default-ja" />
	</xsl:if>
	<xsl:if test="not(/doc/@lang)">
	<xsl:value-of select="$dl-startdist-default-ja" />
	</xsl:if>
	</xsl:if>
	</xsl:variable>
	
	<xsl:variable name="dl-gap-default">
	<xsl:if test="$dl-gap!=''">
	<xsl:value-of select="$dl-gap" />
	</xsl:if>
	<xsl:if test="$dl-gap=''">
	<xsl:if test="/doc/@lang='en'">
	<xsl:value-of select="$dl-gap-default-en" />
	</xsl:if>
	<xsl:if test="/doc/@lang = 'ja'">
	<xsl:value-of select="$dl-gap-default-ja" />
	</xsl:if>
	<xsl:if test="not(/doc/@lang)">
	<xsl:value-of select="$dl-gap-default-ja" />
	</xsl:if>
	</xsl:if>
	</xsl:variable>


	<!--
	-->
	<xsl:param name="toc-make" select="true()" />
	<xsl:param name="cover-make" select="false()" />
	<xsl:param name="index-make" select="true()" />
	<xsl:param name="lang">none</xsl:param>
	<!--
		global parameter and variable used when creating the table of
		contents.
	-->
	<xsl:param name="toc-level-default" select="3" />
		
	<xsl:key name="index-key" match="index"
		use="translate(substring(normalize-space(concat(@key,@keyword,.)),1,1),'&LOWER;','&UPPER;') " />
	<xsl:key name="index-value" match="index" use="." />


	<xsl:template match="/">

		<fo:root>
			<fo:layout-master-set>
				<!-- Define the cover page. -->
				<fo:simple-page-master master-name="cover"
					page-height="297mm" page-width="210mm" margin-top="20mm"
					margin-bottom="20mm" margin-left="20mm" margin-right="10mm">
					<fo:region-body margin-top="0.25in" margin-bottom="0.25in" />
				</fo:simple-page-master>

				<!-- Define the table-of-content (TOC) page. -->
				<fo:simple-page-master margin="25mm 25mm 25mm 25mm"
					master-name="PageMaster-index">
					<xsl:attribute name="page-height">
					<xsl:value-of select="$paper-height-default" />
				</xsl:attribute>
					<xsl:attribute name="page-width">
					<xsl:value-of select="$paper-width-default" />
				</xsl:attribute>
					<fo:region-body margin="0mm 0mm 0mm 0mm"
						column-count="3" column-gap="4mm" />
				</fo:simple-page-master>

				<!-- Define a body (or default) page. -->
				<fo:simple-page-master margin="25mm 25mm 25mm 25mm"
					master-name="PageMaster-TOC">
					<xsl:attribute name="page-height">
					<xsl:value-of select="$paper-height" />
				</xsl:attribute>
					<xsl:attribute name="page-width">
					<xsl:value-of select="$paper-width" />
				</xsl:attribute>
					<fo:region-body margin="0mm 0mm 0mm 0mm" />
				</fo:simple-page-master>

				<!-- Define a body (or default) page. -->
				<fo:simple-page-master master-name="leftPage"
					page-height="297mm" page-width="210mm" margin-top="8mm"
					margin-bottom="8mm" margin-left="10mm" margin-right="20mm">

					<!-- Central part of page -->
					<fo:region-body column-count="1" column-gap="5mm"
						margin-top="10mm" margin-bottom="10mm" />

					<!-- Header -->
					<fo:region-before extent="10mm" />

					<!-- Footer -->
					<fo:region-after extent="10mm" />
				</fo:simple-page-master>
				<fo:simple-page-master master-name="rightPage"
					page-height="297mm" page-width="210mm" margin-top="8mm"
					margin-bottom="8mm" margin-left="20mm" margin-right="10mm">

					<!-- Central part of page -->
					<fo:region-body column-count="1" column-gap="5mm"
						margin-top="10mm" margin-bottom="10mm" />

					<!-- Header -->
					<fo:region-before extent="10mm" />

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

			<!-- Body page -->
			<fo:page-sequence master-reference="contents"
				initial-page-number="1">

				<!-- Define the contents of the header. -->
				<fo:static-content flow-name="xsl-region-before">
					<fo:block font-size="14.0pt" font-family="sans-serif"
						padding-after="4.0pt" space-before="4.0pt" text-align="center"
						border-bottom-style="solid" border-bottom-width="1.0pt">
						<xsl:text>Katalog Outline</xsl:text>
					</fo:block>
				</fo:static-content>

				<!-- Define the contents of the footer. -->
				<fo:static-content flow-name="xsl-region-after">
					<fo:block font-size="8.0pt" font-family="sans-serif"
						padding-before="2mm" padding-after="2.0pt" space-before="3mm"
						text-align="center" border-top-style="solid" border-bottom-width="1.0pt">
						<xsl:text>Page </xsl:text>
						<fo:page-number />
					</fo:block>
				</fo:static-content>

				<!--
					The main contents of the body page, that is, the catalog entries
				-->
				<fo:flow flow-name="xsl-region-body">
					<fo:block font-size="10.0pt" font-family="sans-serif">
						<xsl:apply-templates select="doc">
						</xsl:apply-templates>
					</fo:block>
				</fo:flow>
			</fo:page-sequence>

		</fo:root>
	</xsl:template>

	<xsl:template match="Chapters">
		<xsl:apply-templates />
	</xsl:template>

	<!-- Format entries from angebot.xml -->
	<xsl:template match="ArtGr">
		<!--
			<fo:block font-size="10.0pt" font-weight="bold" padding-top="6mm"
			keep-with-next="always"><xsl:value-of
			select="./ArtGrName"/></fo:block>
		<xsl:variable name="imageURI" select="./Image" />
		<fo:external-graphic src="url('{$imageURI}')"
			padding-left="22.5mm" padding-bottom="2mm" content-height="30mm"
			content-width="40mm" keep-with-next="always" />
		<fo:block font-size="9.0pt" padding-top="2mm" keep-with-next="always">
			<xsl:apply-templates select="Description">
			</xsl:apply-templates>
		</fo:block>
		-->
		<xsl:apply-templates select="Prices" />
	</xsl:template>

	<!-- Format entries from angebot.xml -->
	<xsl:template match="Artikel">
<!--
 		<xsl:apply-templates select="Prices" />
-->
	</xsl:template>

	<xsl:template match="Prices">
		<!--
		Put entire entry within a single block.
		<xsl:if test="Price">
			<fo:block font-family="sans-serif" font-size="9.0pt"
				space-after="2.0pt">
				<xsl:call-template name="PriceListTempl">
					<xsl:with-param name="PriceList" select="Price" />
				</xsl:call-template>
			</fo:block>
		</xsl:if>
		-->
	</xsl:template>

	<!-- Useful template for listing multiple items (such as authors). -->
	<xsl:template name="PriceListTempl">
		<xsl:param name="PriceList" />
		<fo:table padding-bottom="4mm">
			<fo:table-column column-width="20mm" />
			<fo:table-column column-width="40mm" />
			<fo:table-column />
			<fo:table-column column-width="12mm" />
			<fo:table-header>
				<fo:table-row>
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
				<xsl:for-each select="$PriceList[position()]">
					<fo:table-row>
						<fo:table-cell>
							<fo:block>
<!-- 								<xsl:value-of select="ArtikelNr" /> -->
								<xsl:apply-templates select="ArtikelNr" />
							</fo:block>
						</fo:table-cell>
						<fo:table-cell>
							<fo:block>
								<xsl:value-of select="Text" />
							</fo:block>
						</fo:table-cell>
						<fo:table-cell>
							<fo:block text-align="right">
								<xsl:value-of select="./Menge" />
							</fo:block>
						</fo:table-cell>
						<fo:table-cell>
							<fo:block text-align="right">
								<xsl:value-of select="./Preis" />
							</fo:block>
						</fo:table-cell>
					</fo:table-row>
				</xsl:for-each>
			</fo:table-body>
		</fo:table>
	</xsl:template>

	<xsl:template match="KatGr | SubKatGr | SubSubKatGr | ArtGr">
		<xsl:call-template name="title.out" />
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template
		match="KatGr/Title | SubKatGr/Title | SubSubKatGr/Title | ArtGr/Title">
	</xsl:template>

	<xsl:template name="title.out">
		<xsl:variable name="level"
			select="count(ancestor-or-self::KatGr |
		ancestor-or-self::SubKatGr |
		ancestor-or-self::SubSubKatGr |
		ancestor-or-self::ArtGr)" />
		<xsl:choose>
			<xsl:when test="$level=1">
				<fo:block xsl:use-attribute-sets="h1" id="{generate-id()}">
					<xsl:call-template name="title.out.sub" />
					<xsl:value-of select="KatGrNr" />:<xsl:value-of select="Title" />
				</fo:block>
			</xsl:when>
			<xsl:when test="$level=2">
				<fo:block xsl:use-attribute-sets="h2" id="{generate-id()}">
					<xsl:call-template name="title.out.sub" />
					<xsl:value-of select="KatGrNr" />:<xsl:value-of select="Title" />
				</fo:block>
			</xsl:when>
			<xsl:when test="$level=3">
				<fo:block xsl:use-attribute-sets="h3" id="{generate-id()}">
					<xsl:call-template name="title.out.sub" />
					<xsl:if test="KatGrNr">
						<xsl:value-of select="KatGrNr" />:<xsl:value-of select="Title" />
					</xsl:if>
					<xsl:if test="ArtGrNr">
						<xsl:value-of select="ArtGrNr" />:<xsl:value-of select="Title" />
					</xsl:if>
				</fo:block>
			</xsl:when>
			<xsl:when test="$level=4">
				<fo:block xsl:use-attribute-sets="h4" id="{generate-id()}">
					<xsl:call-template name="title.out.sub" />
					<xsl:if test="KatGrNr">
						<xsl:value-of select="KatGrNr" />:<xsl:value-of select="Title" />
					</xsl:if>
					<xsl:if test="ArtGrNr">
						<xsl:value-of select="ArtGrNr" />:<xsl:value-of select="Title" />
					</xsl:if>
				</fo:block>
			</xsl:when>
			<xsl:when test="$level=5">
				<fo:block xsl:use-attribute-sets="h5" id="{generate-id()}">
					<xsl:call-template name="title.out.sub" />
					<xsl:if test="KatGrNr">
						<xsl:value-of select="KatGrNr" />:<xsl:value-of select="Title" />
					</xsl:if>
					<xsl:if test="ArtGrNr">
						<xsl:value-of select="ArtGrNr" />:<xsl:value-of select="Title" />
					</xsl:if>
				</fo:block>
			</xsl:when>
			<xsl:otherwise>
				<fo:block xsl:use-attribute-sets="h6" id="{generate-id()}">
					<xsl:call-template name="title.out.sub" />
					<xsl:if test="KatGrNr">
						<xsl:value-of select="KatGrNr" />:<xsl:value-of select="Title" />
					</xsl:if>
					<xsl:if test="ArtGrNr">
						<xsl:value-of select="ArtGrNr" />:<xsl:value-of select="Title" />
					</xsl:if>
				</fo:block>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="title.out.sub">
	</xsl:template>

	<xsl:template match="Copyright">
	</xsl:template>

	<xsl:template match="Scope">
	</xsl:template>

	<xsl:template match="ArtGrNr">
	</xsl:template>

	<xsl:template match="KatGrNr">
	</xsl:template>

	<xsl:template match="Description">
<!-- 
		<fo:block font-size="9.0pt" padding-top="2mm" keep-with-next="always">
 			<xsl:apply-templates />
		</fo:block>
 -->
	</xsl:template>

	<xsl:attribute-set name="h1">
		<xsl:attribute name="font-size">10pt</xsl:attribute>
		<xsl:attribute name="font-family">"sans-serif"</xsl:attribute>
		<xsl:attribute name="font-weight">normal</xsl:attribute>
		<xsl:attribute name="space-before">1pt</xsl:attribute>
		<xsl:attribute name="space-after">1pt</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="h2">
		<xsl:attribute name="text-indent">5mm</xsl:attribute>
		<xsl:attribute name="font-size">10pt</xsl:attribute>
		<xsl:attribute name="font-family">"sans-serif"</xsl:attribute>
		<xsl:attribute name="font-weight">normal</xsl:attribute>
		<xsl:attribute name="space-before">1pt</xsl:attribute>
		<xsl:attribute name="space-after">1pt</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="h3">
		<xsl:attribute name="text-indent">10mm</xsl:attribute>
		<xsl:attribute name="font-size">10pt</xsl:attribute>
		<xsl:attribute name="font-family">"sans-serif"</xsl:attribute>
		<xsl:attribute name="font-weight">normal</xsl:attribute>
		<xsl:attribute name="space-before">1pt</xsl:attribute>
		<xsl:attribute name="space-after">1pt</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="h4">
		<xsl:attribute name="text-indent">15mm</xsl:attribute>
		<xsl:attribute name="font-size">10pt</xsl:attribute>
		<xsl:attribute name="font-family">"sans-serif"</xsl:attribute>
		<xsl:attribute name="font-weight">normal</xsl:attribute>
		<xsl:attribute name="space-before">1pt</xsl:attribute>
		<xsl:attribute name="space-after">1pt</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="h5">
		<xsl:attribute name="text-indent">20mm</xsl:attribute>
		<xsl:attribute name="font-size">10pt</xsl:attribute>
		<xsl:attribute name="font-family">"sans-serif"</xsl:attribute>
		<xsl:attribute name="font-weight">normal</xsl:attribute>
		<xsl:attribute name="space-before">1pt</xsl:attribute>
		<xsl:attribute name="space-after">1pt</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="h6">
		<xsl:attribute name="font-size">10pt</xsl:attribute>
		<xsl:attribute name="font-family">"sans-serif"</xsl:attribute>
		<xsl:attribute name="font-weight">normal</xsl:attribute>
		<xsl:attribute name="space-before">1pt</xsl:attribute>
		<xsl:attribute name="space-after">1pt</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="p">
		<xsl:attribute name="text-indent">1em</xsl:attribute>
		<xsl:attribute name="space-before">0.6em</xsl:attribute>
		<xsl:attribute name="space-after">0.6em</xsl:attribute>
		<xsl:attribute name="text-align">justify</xsl:attribute>
		<xsl:attribute name="keep-together.within-page">always</xsl:attribute>
	</xsl:attribute-set>
	<xsl:template match="p">
		<fo:block xsl:use-attribute-sets="p">
			<xsl:apply-templates />
		</fo:block>
	</xsl:template>

	<xsl:attribute-set name="toc" >
	  <xsl:attribute name="font-size">13pt</xsl:attribute>
	  <xsl:attribute name="font-family"><xsl:choose><xsl:when test="/doc/@lang='en'">Arial,sans-serif</xsl:when><xsl:when test="$lang ='en'">Arial</xsl:when><xsl:when test="/doc/@lang = 'ja'">Arial,'ＭＳ ゴシック',sans-serif</xsl:when><xsl:when test="$lang = 'ja'">Arial,'ＭＳ ゴシック',sans-serif</xsl:when><xsl:otherwise>Arial,'ＭＳ ゴシック',sans-serif</xsl:otherwise></xsl:choose></xsl:attribute>
	  <xsl:attribute name="font-weight">bold</xsl:attribute>
	  <xsl:attribute name="space-before">0pt</xsl:attribute>
	  <xsl:attribute name="space-after">0pt</xsl:attribute>
	  <xsl:attribute name="keep-with-next.within-page">always</xsl:attribute>
	</xsl:attribute-set>

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

	<xsl:template match="div">
	</xsl:template>

</xsl:stylesheet>