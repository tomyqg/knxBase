<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	 xmlns:fo="http://www.w3.org/1999/XSL/Format">
<xsl:output method="xml" encoding="UTF-8" indent="yes"/>

<xsl:variable name="examples-dir" select="'C:\Program Files\Stylus Studio 2011 XML Home Edition\examples'"/>

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
			 <fo:simple-page-master master-name="default-master" page-height="297mm"
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
		 </fo:layout-master-set>

		 <!-- The data to be diplayed in the pages, cover page first -->
		 <fo:page-sequence master-reference="cover">
			 <fo:flow flow-name="xsl-region-body">
				<fo:block text-align="center" font-size="28.0pt">
					<xsl:text>ERP Article List</xsl:text>
				</fo:block>
			 </fo:flow>
		 </fo:page-sequence>

		 <!-- Body page -->
		 <fo:page-sequence master-reference="default-master">

			 <!-- Define the contents of the header. -->
			 <fo:static-content flow-name="xsl-region-before">
				<fo:block font-size="14.0pt" font-family="serif" padding-after="4.0pt"
					space-before="4.0pt" text-align="center"
					border-bottom-style="solid" border-bottom-width="1.0pt">
					<xsl:text>ERP Article List</xsl:text>
				</fo:block>
			 </fo:static-content>

			 <!-- Define the contents of the footer. -->
			 <fo:static-content flow-name="xsl-region-after">
				 <fo:block font-size="8.0pt" font-family="sans-serif" padding-after="2.0pt"
				           space-before="4.0pt" text-align="center"
				           border-top-style="solid" border-bottom-width="1.0pt">
				           <xsl:text>Page </xsl:text>
				           <fo:page-number/>
				 </fo:block>
			 </fo:static-content>

			 <!-- The main contents of the body page, that is, the catalog
				    entries -->
			 <fo:flow flow-name="xsl-region-body">
				 <fo:block font-size="10.0pt" font-family="serif">
<!--
				<xsl:apply-templates select="document('articles.xml')/TableArtikel">
-->
				<xsl:apply-templates select="TableArtikel">
				   </xsl:apply-templates>
				 </fo:block>
			 </fo:flow>
		 </fo:page-sequence>
	 </fo:root>
</xsl:template>

<!-- Format entries from angebot.xml -->
<xsl:template match="TableArtikel">
	 <xsl:call-template name="Articles">
		 <xsl:with-param name="ItemList" select="Artikel"/>
	 </xsl:call-template>
</xsl:template>

<!-- All entries have the same basic format: a title, an author (or
			director), a description and an optional graphic. Each type of entry is
			taken care of by matching one of the above templates. Those templates, in
			turn, call this one to do the real formatting. -->
<xsl:template name="Articles">
	 <xsl:param name="ItemList"/>

	 <!-- Put entire entry within a single block. -->
	 <fo:block space-before="4.0pt" space-after="4.0pt" start-indent="0.25in" end-indent="0.25in">

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
<fo:table-column column-width="35mm"/>
<fo:table-column column-width="120mm"/>

<fo:table-header>
	 <fo:table-row>
		 <fo:table-cell>
			 <fo:block font-weight="bold">Artikelnr.</fo:block>
		 </fo:table-cell>
		 <fo:table-cell>
			 <fo:block font-weight="bold">Beschreibung</fo:block>
		 </fo:table-cell>
	 </fo:table-row>
</fo:table-header>
<fo:table-body>

	 <xsl:for-each select="$list[position() > 0]">
		 <fo:table-row>
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
		 </fo:table-row>
	 </xsl:for-each>

</fo:table-body>
</fo:table>

</xsl:template>
</xsl:stylesheet>
