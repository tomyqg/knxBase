<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
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
			<fo:region-body column-count="2" column-gap="5mm"
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
			<fo:region-body column-count="2" column-gap="5mm"
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
</xsl:stylesheet>