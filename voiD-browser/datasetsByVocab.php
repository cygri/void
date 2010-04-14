<?php
include 'inc.php';
$vocab  = $_GET['uri'];
$query = <<<_q_
prefix void: <http://rdfs.org/ns/void#>
prefix dc: <http://purl.org/dc/elements/1.1/>
prefix dct: <http://purl.org/dc/terms/>
prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
DESCRIBE ?dataset ?vocab { ?dataset void:vocabulary <{$vocab}> . optional{ ?dataset void:vocabulary ?vocab . } }
_q_;
$graph = $store->get_sparql_service()->graph_to_simple_graph($query);

$filename = 'datasetsByVocab.html';

include 'templates/template.html';
?>