<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
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
	<xsl:variable name="thumb">katgr</xsl:variable>
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

</xsl:stylesheet>