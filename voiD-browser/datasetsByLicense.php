<?php
include 'inc.php';
$licenseURI  = $_GET['uri'];
$query = <<<_q_
prefix void: <http://rdfs.org/ns/void#>
prefix dc: <http://purl.org/dc/elements/1.1/>
prefix dct: <http://purl.org/dc/terms/>
prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
prefix wv: <http://vocab.org/waiver/terms/>

DESCRIBE <$licenseURI> ?dataset  { 
	{ <$licenseURI> ?p ?o }
	UNION
	{ ?dataset dct:license <{$licenseURI}> . }
	 UNION 
	{ ?dataset wv:waiver <{$licenseURI}> . } 
}
_q_;
$graph = $store->get_sparql_service()->graph_to_simple_graph($query);

$filename = 'datasetsByLicense.html';

include 'templates/template.html';
?>