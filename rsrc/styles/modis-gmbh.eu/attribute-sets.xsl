<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:attribute-set name="h1">
		<xsl:attribute name="font-size">24pt</xsl:attribute>
		<xsl:attribute name="font-family">"sans-serif"</xsl:attribute>
		<xsl:attribute name="font-weight">bold</xsl:attribute>
		<xsl:attribute name="space-after">5pt</xsl:attribute>
		<xsl:attribute name="border-after-style">solid</xsl:attribute>
		<xsl:attribute name="border-after-width">2pt</xsl:attribute>
		<xsl:attribute name="keep-with-next.within-page">always</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="h2">
		<xsl:attribute name="break-before">page</xsl:attribute>
		<xsl:attribute name="font-size">16pt</xsl:attribute>
		<xsl:attribute name="font-family">"sans-serif"</xsl:attribute>
		<xsl:attribute name="font-weight">bold</xsl:attribute>
		<xsl:attribute name="space-before">5pt</xsl:attribute>
		<xsl:attribute name="space-after">0pt</xsl:attribute>
		<xsl:attribute name="border-after-style">solid</xsl:attribute>
		<xsl:attribute name="border-after-width">1pt</xsl:attribute>
		<xsl:attribute name="keep-with-next.within-page">always</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="h3">
		<xsl:attribute name="font-size">13pt</xsl:attribute>
		<xsl:attribute name="font-family">"sans-serif"</xsl:attribute>
		<xsl:attribute name="font-weight">bold</xsl:attribute>
		<xsl:attribute name="space-before">5pt</xsl:attribute>
		<xsl:attribute name="space-after">0pt</xsl:attribute>
		<xsl:attribute name="border-after-style">solid</xsl:attribute>
		<xsl:attribute name="border-after-width">1pt</xsl:attribute>
		<xsl:attribute name="keep-with-next.within-page">always</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="h4">
		<xsl:attribute name="font-size">12pt</xsl:attribute>
		<xsl:attribute name="font-family">"sans-serif"</xsl:attribute>
		<xsl:attribute name="font-weight">bold</xsl:attribute>
		<xsl:attribute name="space-before">5pt</xsl:attribute>
		<xsl:attribute name="space-after">0pt</xsl:attribute>
		<xsl:attribute name="keep-with-next.within-page">always</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="h5">
		<xsl:attribute name="font-size">10pt</xsl:attribute>
		<xsl:attribute name="font-family">"sans-serif"</xsl:attribute>
		<xsl:attribute name="font-weight">bold</xsl:attribute>
		<xsl:attribute name="space-before">3pt</xsl:attribute>
		<xsl:attribute name="space-after">0pt</xsl:attribute>
		<xsl:attribute name="keep-with-next.within-page">always</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="h6">
		<xsl:attribute name="font-size">9pt</xsl:attribute>
		<xsl:attribute name="font-family">"sans-serif"</xsl:attribute>
		<xsl:attribute name="font-weight">bold</xsl:attribute>
		<xsl:attribute name="space-before">3pt</xsl:attribute>
		<xsl:attribute name="space-after">3pt</xsl:attribute>
		<xsl:attribute name="keep-with-next.within-page">always</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="p">
		<xsl:attribute name="text-indent">1em</xsl:attribute>
		<xsl:attribute name="space-before">0.6em</xsl:attribute>
		<xsl:attribute name="space-after">0.6em</xsl:attribute>
		<xsl:attribute name="text-align">justify</xsl:attribute>
		<xsl:attribute name="keep-together.within-page">always</xsl:attribute>
	</xsl:attribute-set>
	<xsl:attribute-set name="list.item" >
	  <xsl:attribute name="space-before">0.4em</xsl:attribute>
	  <xsl:attribute name="space-after">0.4em</xsl:attribute>
	  <xsl:attribute name="relative-align">baseline</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="div.toc">
	</xsl:attribute-set>

	<xsl:attribute-set name="index-artnr-line">
	  <xsl:attribute name="font-size">9pt</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="index-artnr">
	</xsl:attribute-set>

	<xsl:attribute-set name="index-artgr-line">
	  <xsl:attribute name="font-size">9pt</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="index-artgr">
	</xsl:attribute-set>

	<xsl:attribute-set name="table.data.th" >
	  <xsl:attribute name="background-color">#EEEEEE</xsl:attribute>
	  <xsl:attribute name="border-style">solid</xsl:attribute>
	  <xsl:attribute name="border-width">1pt</xsl:attribute>
	  <xsl:attribute name="padding-start">0.3em</xsl:attribute>
	  <xsl:attribute name="padding-end">0.2em</xsl:attribute>
	  <xsl:attribute name="padding-before">2pt</xsl:attribute>
	  <xsl:attribute name="padding-after">2pt</xsl:attribute>
	  <xsl:attribute name="font-weight">bold</xsl:attribute>
	  <xsl:attribute name="font-size">0.9em</xsl:attribute>
	</xsl:attribute-set>
	
	<xsl:attribute-set name="table.data.td" >
	  <xsl:attribute name="border-style">solid</xsl:attribute>
	  <xsl:attribute name="border-width">0.5pt</xsl:attribute>
	  <xsl:attribute name="padding-start">0.3em</xsl:attribute>
	  <xsl:attribute name="padding-end">0.2em</xsl:attribute>
	  <xsl:attribute name="padding-before">2pt</xsl:attribute>
	  <xsl:attribute name="padding-after">2pt</xsl:attribute>
	  <xsl:attribute name="font-size">0.9em</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="thumb-class">
	<xsl:attribute name="retrieve-boundary">page</xsl:attribute>
	<xsl:attribute name="retrieve-position">last-ending-within-page</xsl:attribute>
	</xsl:attribute-set>

	<xsl:attribute-set name="toc" >
	  <xsl:attribute name="font-size">13pt</xsl:attribute>
	  <xsl:attribute name="font-family"><xsl:choose><xsl:when test="/doc/@lang='en'">Arial,sans-serif</xsl:when><xsl:when test="$lang ='en'">Arial</xsl:when><xsl:when test="/doc/@lang = 'ja'">Arial,'ＭＳ ゴシック',sans-serif</xsl:when><xsl:when test="$lang = 'ja'">Arial,'ＭＳ ゴシック',sans-serif</xsl:when><xsl:otherwise>Arial,'ＭＳ ゴシック',sans-serif</xsl:otherwise></xsl:choose></xsl:attribute>
	  <xsl:attribute name="font-weight">bold</xsl:attribute>
	  <xsl:attribute name="space-before">0pt</xsl:attribute>
	  <xsl:attribute name="space-after">0pt</xsl:attribute>
	  <xsl:attribute name="keep-with-next.within-page">always</xsl:attribute>
	</xsl:attribute-set>

</xsl:stylesheet>