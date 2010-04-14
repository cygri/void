<?php
include 'inc.php';
$endpointURI = $_GET['uri'];
$prefixes = "prefix void: <http://rdfs.org/ns/void#>
prefix dc: <http://purl.org/dc/elements/1.1/>
prefix dct: <http://purl.org/dc/terms/>
prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
";

$query = <<<_q_
{$prefixes}
describe ?dataset { ?dataset a void:Dataset ; void:vocabulary ?vocab ; void:sparqlEndpoint <{$endpointURI}> .  } 
_q_;
$G = $store->get_sparql_service()->graph_to_simple_graph($query);

$letters = range('A','Z');
$datasets = $G->get_subjects_where_resource(VOID.'sparqlEndpoint', $endpointURI);
$Graph = new SimpleGraph();
foreach($G->get_resource_triple_values($datasets[0], VOID.'vocabulary' ) as $vocabUri){
	$prefix = $Graph->uri_to_qname($vocabUri);
	if(empty($prefix)) $prefix = array_shift($letters);
	$prefixes .= 'prefix '.$prefix.': <'.$vocabUri.">\n";
}

$filename = 'sparql.html';

include 'templates/template.html';
?>