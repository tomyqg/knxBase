<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:fo="http://www.w3.org/1999/XSL/Format">
<xsl:output method="xml" encoding="UTF-8" indent="yes"/>

<xsl:template match="/">
	<fo:root>
		<fo:layout-master-set>
			<!-- Define the cover page. -->
			<fo:simple-page-master master-name="cover"
										page-height="297mm" page-width="210mm"
										margin-top="20mm" margin-bottom="20mm"
										margin-left="20mm" margin-right="10mm">
				<fo:region-body margin-top="0.25in" margin-bottom="0.25in"/>
			</fo:simple-page-master>

			<!-- Define a body (or default) page. -->
			<fo:simple-page-master master-name="leftPage" page-height="297mm"
					                   page-width="210mm" margin-top="20mm"
					                   margin-bottom="20mm" margin-left="10mm"
					                   margin-right="20mm">

				<!-- Central part of page -->
				<fo:region-body column-count="1" margin-top="10mm"
					              margin-bottom="10mm"/>

				<!-- Header -->
				<fo:region-before extent="10mm"/>

				<!-- Footer -->
				<fo:region-after extent="10mm"/>
			</fo:simple-page-master>
			<fo:simple-page-master master-name="rightPage" page-height="297mm"
					                   page-width="210mm" margin-top="20mm"
					                   margin-bottom="20mm" margin-left="20mm"
					                   margin-right="10mm">

				<!-- Central part of page -->
				<fo:region-body column-count="1" margin-top="10mm"
					              margin-bottom="10mm"/>

				<!-- Header -->
				<fo:region-before extent="10mm"/>

				<!-- Footer -->
				<fo:region-after extent="10mm"/>
			</fo:simple-page-master>
			<fo:page-sequence-master master-name="contents">
				<fo:repeatable-page-master-alternatives>
					<fo:conditional-page-master-reference
							master-reference="leftPage"
							odd-or-even="even"/>
					<fo:conditional-page-master-reference
							master-reference="rightPage"
							odd-or-even="odd"/>
				</fo:repeatable-page-master-alternatives>
			</fo:page-sequence-master>
		</fo:layout-master-set>

		<!-- The data to be diplayed in the pages, cover page first -->
		<fo:page-sequence master-reference="cover">
			<fo:flow flow-name="xsl-region-body">
				<fo:block text-align="center" font-size="28.0pt">
					<xsl:text>MODIS Inventurliste</xsl:text>
				</fo:block>
				<fo:block text-align="center" font-size="14.0pt">
					<xsl:text>Stand: --.--.----</xsl:text>
				</fo:block>
				<fo:block text-align="left" font-size="10.0pt">
					<xsl:text></xsl:text>
				</fo:block>
				<fo:block text-align="left" font-size="10.0pt">
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
					<xsl:text></xsl:text>
				</fo:block>
				<fo:block text-align="left" font-size="10.0pt">
					<xsl:text></xsl:text>
				</fo:block>
			</fo:flow>
		</fo:page-sequence>

		<!-- Body page -->
		<fo:page-sequence master-reference="contents" initial-page-number="1">

			<!-- Define the contents of the header. -->
			<fo:static-content flow-name="xsl-region-before">
				<fo:block font-size="14.0pt" font-family="sans-serif" padding-after="4.0pt"
					space-before="4.0pt" text-align="center"
					border-bottom-style="solid" border-bottom-width="1.0pt">
					<xsl:text>MODIS Lagerliste</xsl:text>
				</fo:block>
			</fo:static-content>

			<!-- Define the contents of the footer. -->
			<fo:static-content flow-name="xsl-region-after">
				<fo:block font-size="8.0pt" font-family="sans-serif"
							padding-before="2mm"
							padding-after="2.0pt"
					        space-before="3mm" text-align="center"
					        border-top-style="solid" border-bottom-width="1.0pt">
					        <xsl:text>Page </xsl:text>
					        <fo:page-number/>
				</fo:block>
			</fo:static-content>

			<!-- The main contents of the body page, that is, the catalog
					 entries -->
			<fo:flow flow-name="xsl-region-body">
				<fo:block font-size="10.0pt" font-family="sans-serif">
					<xsl:apply-templates select="doc">
					</xsl:apply-templates>
				</fo:block>
			</fo:flow>
		</fo:page-sequence>
	</fo:root>
</xsl:template>

<!-- Format entries from angebot.xml -->
<xsl:template match="TableInventoryItem">
		<xsl:call-template name="InventoryItem">
			<xsl:with-param name="ItemList" select="InventoryItem"/>
		</xsl:call-template>
</xsl:template>

<!-- All entries have the same basic format: a title, an author (or
			director), a description and an optional graphic. Each type of entry is
			taken care of by matching one of the above templates. Those templates, in
			turn, call this one to do the real formatting. -->
<xsl:template name="InventoryItem">
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
			<fo:table-column column-width="10mm"/>
			<fo:table-column column-width="15mm"/>
			<fo:table-column column-width="30mm"/>
			<fo:table-column column-width="60mm"/>
			<fo:table-column column-width="20mm"/>
			<fo:table-column column-width="20mm"/>
			
			<fo:table-header>
				<fo:table-row border-top="thin solid">
					<fo:table-cell>
						<fo:block font-weight="bold">Lager</fo:block>
					</fo:table-cell>
					<fo:table-cell>
						<fo:block font-weight="bold">Position</fo:block>
					</fo:table-cell>
					<fo:table-cell>
						<fo:block font-weight="bold">Artikelnr.</fo:block>
					</fo:table-cell>
					<fo:table-cell>
						<fo:block font-weight="bold">Beschreibung</fo:block>
					</fo:table-cell>
					<fo:table-cell>
						<fo:block font-weight="bold" text-align="right">Bestand</fo:block>
						<fo:block font-weight="bold" text-align="right">System</fo:block>
					</fo:table-cell>
					<fo:table-cell>
						<fo:block font-weight="bold" text-align="right">Bestand</fo:block>
						<fo:block font-weight="bold" text-align="right">Real</fo:block>
					</fo:table-cell>
				</fo:table-row>
			</fo:table-header>
			<fo:table-body>
			
				<xsl:for-each select="$list[position() > 0]">
					<fo:table-row border-top="thin solid">
						<fo:table-cell>
							<fo:block><xsl:value-of select="./StockNr"/></fo:block>
						</fo:table-cell>
						<fo:table-cell>
							<fo:block><xsl:value-of select="./StockId"/></fo:block>
						</fo:table-cell>
						<fo:table-cell>
							<fo:block><xsl:value-of select="./ArticleNo"/></fo:block>
							<fo:block><xsl:value-of select="./ERPNo"/></fo:block>
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
								<xsl:value-of select="./QtyOut"/>
							</fo:block>
						</fo:table-cell>
						<fo:table-cell>
							<fo:block text-align="right">
								<xsl:value-of select="./Value"/>
							</fo:block>
						</fo:table-cell>
					</fo:table-row>
				</xsl:for-each>

			</fo:table-body>
		</fo:table>
	</xsl:template>
	
	<xsl:template match="Copyright">
	</xsl:template>

	<xsl:template match="Scope">
	</xsl:template>

	<xsl:template match="Date">
	</xsl:template>

	<xsl:template match="Image">
	</xsl:template>
	
</xsl:stylesheet>
