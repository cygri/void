<?php

include 'inc.php';
$query = <<<_q_
prefix void: <http://rdfs.org/ns/void#>
prefix dc: <http://purl.org/dc/elements/1.1/>
prefix dct: <http://purl.org/dc/terms/>
prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
select ?label ?subject (count(?dataset) as ?count) { ?dataset a void:Dataset ; dct:subject ?subject .  } group by ?subject  order by desc(?count) 
_q_;
$array = $store->get_sparql_service()->select_to_array($query);
$graph = new SimpleGraph();
foreach($array as $n => $row){
	if(!isset($row['label'])){
	$array[$n]['label'] = array('type' =>'literal', 'value' => $graph->get_label($row['subject']['value']));	
	}
}
// var_dump($array);
$filename = 'subjects.html';

include 'templates/template.html';
?>
