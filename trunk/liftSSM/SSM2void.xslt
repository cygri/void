<?xml version="1.0" encoding="UTF-8"?>
<stylesheet
    xmlns:xsl  ="http://www.w3.org/1999/XSL/Transform" version="1.0"
    xmlns      ="http://www.w3.org/1999/XSL/Transform"
    xmlns:sc   ="http://sw.deri.org/2007/07/sitemapextension/scschema.xsd"
    xmlns:rdf  ="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:rdfs ="http://www.w3.org/2000/01/rdf-schema#"
    xmlns:dcterms ="http://purl.org/dc/terms/"
    xmlns:foaf ="http://xmlns.com/foaf/0.1/"
    xmlns:scovo="http://purl.org/NET/scovo#"
    xmlns:void ="http://rdfs.org/ns/void#">


<output indent="yes" method="xml" media-type="application/rdf+xml" encoding="UTF-8" omit-xml-declaration="no"/>


<template match="sc:dataset">
<rdf:RDF xmlns:rdf ="http://www.w3.org/1999/02/22-rdf-syntax-ns#" >
  
	<xsl:element name="void:Dataset">

		<!-- map sc:datasetURI to dataset -->	
		<xsl:attribute  name="rdf:about">
			<xsl:value-of select="sc:datasetURI" />
		</xsl:attribute> 
		
		<!-- map sc:datasetLabel to rdfs:comment -->
		<xsl:element name="rdfs:comment">
			<xsl:value-of select="sc:datasetLabel" />
		</xsl:element>		
		
		<!-- create placeholder for homepage -->
		<xsl:element name="foaf:homepage">
			<xsl:attribute name="rdf:resource">http://example.org/dataset.html</xsl:attribute> 
   		</xsl:element>  	
   		
   		<!-- create placeholder for category -->
		<xsl:element name="dcterms:subject">
			<xsl:attribute name="rdf:resource">http://dbpedia.org/resource/EXAMPLE</xsl:attribute> 
   		</xsl:element>  	
   		
   		<!-- create placeholder for creator -->
		<xsl:element name="dcterms:creator">
			<xsl:attribute name="rdf:resource">http://example.org/creator#me</xsl:attribute> 
   		</xsl:element>  	
   		
   		<!-- create placeholder for license -->
		<xsl:element name="dcterms:license">
			<xsl:attribute name="rdf:resource">http://creativecommons.org/licenses/by/3.0/</xsl:attribute> 
   		</xsl:element>  
   		
   		<!-- create placeholder for statistics -->
   		<xsl:element name="void:statItem">
   			<xsl:attribute name="rdf:parseType">Resource</xsl:attribute>
   			<xsl:element name="scovo:dimension">
				<xsl:attribute name="rdf:resource">http://rdfs.org/ns/void#numOfTriples</xsl:attribute> 
   			</xsl:element>
   			<xsl:element name="rdf:value">
				<xsl:attribute name="rdf:datatype">http://www.w3.org/2001/XMLSchema#integer</xsl:attribute>0</xsl:element>
   		</xsl:element>     		   		
   		
   		<!-- process sub-elements  -->
		<apply-templates select="sc:sampleURI" /> 		
		<apply-templates select="sc:sparqlEndpointLocation" /> 
		<apply-templates select="sc:dataDumpLocation" /> 	
		<apply-templates select="sc:linkedDataPrefix" /> 		
   </xsl:element> 
   
</rdf:RDF>
</template>


<!-- map sc:sampleURI to void:exampleResource -->
<template match="sc:sampleURI">
	<xsl:element name="void:exampleResource">
		<xsl:attribute name="rdf:resource">
			<xsl:value-of select="." />
		</xsl:attribute> 
   </xsl:element>  
</template>
 
<!-- map sc:sparqlEndpointLocation to void:sparqlEndpoint -->
<template match="sc:sparqlEndpointLocation">
	<xsl:element name="void:sparqlEndpoint">
		<xsl:attribute name="rdf:resource">
			<xsl:value-of select="." />
		</xsl:attribute> 
   </xsl:element>  
</template>  

<!-- map sc:dataDumpLocation to void:dataDumpLocation -->
<template match="sc:dataDumpLocation">
	<xsl:element name="void:dataDumpLocation">
		<xsl:attribute name="rdf:resource">
			<xsl:value-of select="." />
		</xsl:attribute> 
   </xsl:element>  
</template>  


<!-- map sc:linkedDataPrefix to void:uriPattern -->
<template match="sc:linkedDataPrefix">
	<xsl:element name="void:uriRegexPattern">^<xsl:value-of select="." />$</xsl:element>  
</template>  



<!-- ignore the rest of the DOM -->
<template match="text()|@*|*"><apply-templates /></template>


</stylesheet>
