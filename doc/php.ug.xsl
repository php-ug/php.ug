<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet  
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
    version="1.0"> 

    <xsl:import href="http://www.heigl.org/docbook/xsl/html/chunk.xsl"/> 

    <xsl:param name="html.stylesheet">css/style.css</xsl:param> 
    <xsl:param name="admon.graphics" select="1"/>
    <xsl:param name="chunker.output.encoding" select="'UTF-8'"/>
    <xsl:param name="chunk.section.depth" select="0"/>
    
    <xsl:template match="lineannotation">
        <fo:inline font-style="italic">
            <xsl:call-template name="inline.charseq"/>
        </fo:inline>
    </xsl:template>
</xsl:stylesheet>  