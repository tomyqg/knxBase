<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
	<xsl:output method="html" encoding="utf-8" indent="yes"/>
	<xsl:template match="pagedata">
		Ihr Warenkorb enthält <xsl:value-of select="content/ItemCount" /> Positionen zu einem Warenwert-
		von <xsl:value-of select="content/TotalNet" /> € zzgl. Mwst..
	</xsl:template>
</xsl:stylesheet>
