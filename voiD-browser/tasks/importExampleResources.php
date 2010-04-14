<?php
set_include_path('../:.');
include '../inc.php';
require '../moriarty/changeset.class.php';

$graph = $store->get_sparql_service()->graph_to_simple_graph("prefix void: <{$VOID}> \n construct { ?dataset  void:exampleResource ?exampleResource ; void:sparqlEndpoint ?endpoint . }  WHERE { ?dataset  void:exampleResource ?exampleResource ; void:sparqlEndpoint ?endpoint . } ");

$S = new Store(voiD_Store_Uri);
$Endpoint = $S->get_sparql_service();
$index = $graph->get_index();
foreach($index as $uri => $ps){
	$query = 'DESCRIBE ';
	foreach($ps[$VOID.'exampleResource'] as $o){
		$query.= ' <'.$o['value'].'>';
	}
	$endpoint = $ps[$VOID.'sparqlEndpoint'][0]['value'];
	$Endpoint->uri = $endpoint;
	echo "\n\n$query | $endpoint \n\n";
	$r = $Endpoint->graph($query);
	if($r->is_success()){
		echo $store->get_metabox()->submit_rdfxml($r->body)->is_success() ? "got exampleResources from {$uri}\n" : "\ndidn't manage to import resources from {$uri}";
	}
}

?>