<?php

include 'inc.php';
$query = <<<_q_
prefix void: <http://rdfs.org/ns/void#>
prefix dc: <http://purl.org/dc/elements/1.1/>
prefix dct: <http://purl.org/dc/terms/>
prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
select ?label ?vocab (count(?vocab) as ?count) { ?dataset a void:Dataset ; void:vocabulary ?vocab . optional { ?vocab dc:title ?label } optional { ?vocab dct:title ?label } optional { ?vocab rdfs:label ?label } } group by ?vocab ?label order by desc(?count) LIMIT 100
_q_;
$array = $store->get_sparql_service()->select_to_array($query);
foreach($array as $n => $row){
	if(!isset($row['label'])){
	$array[$n]['label'] = array('type' =>'literal', 'value' => $row['vocab']['value']);	
	}
}
$filename = 'vocabs.html';

include 'templates/template.html';
?>
