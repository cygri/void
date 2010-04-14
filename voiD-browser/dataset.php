<?php
include 'inc.php';
$DatasetUri  = $_GET['uri'];
$query = <<<_q_
prefix void: <http://rdfs.org/ns/void#>
prefix dc: <http://purl.org/dc/elements/1.1/>
prefix dct: <http://purl.org/dc/terms/>
prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
prefix owl: <http://www.w3.org/2002/07/owl#>
prefix foaf: <http://xmlns.com/foaf/0.1/>

construct {

<{$DatasetUri}> ?p ?o .

?o ?rp ?ro .

}
where {
	<{$DatasetUri}> ?p ?o .
	optional { ?o ?rp ?ro . }
}
_q_;
$graph = $store->get_sparql_service()->graph_to_simple_graph($query);

$filename = 'dataset.html';

include 'templates/template.html';
?>