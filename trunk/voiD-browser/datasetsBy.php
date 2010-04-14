<?php
include 'inc.php';
$uri  = $_GET['uri'];
$query = <<<_q_
prefix void: <http://rdfs.org/ns/void#>
prefix dc: <http://purl.org/dc/elements/1.1/>
prefix dct: <http://purl.org/dc/terms/>
prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
DESCRIBE ?dataset ?thing 
{
	 ?dataset <{$datasetsByPredicate}> <{$uri}> . 
	optional{ ?dataset <{$datasetsByPredicate}> ?thing . } 
}
_q_;
$graph = $store->get_sparql_service()->graph_to_simple_graph($query);

$filename = 'datasetsBy.html';

include 'templates/template.html';
?>