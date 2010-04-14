<?php
set_include_path('../:.');
include '../inc.php';
require '../moriarty/changeset.class.php';

$queriesAndDimensions = array(
	
	'void:numberOfTriples' => "select (count(?s) as ?count) { ?s ?p ?o }",
	'void:numberOfDistinctSubjects' => "select (count( distinct ?s) as ?count) { ?s ?p ?o } "
	);


$array = $store->get_sparql_service()->select_to_array("prefix void: <{$VOID}> \n select ?dataset ?endpoint { ?dataset a void:Dataset ; void:sparqlEndpoint ?endpoint . FILTER(REGEX(STR(?dataset), \"iand\")) } ");

$graph = new SimpleGraph();
$S = new Store('foo');
$Endpoint = $S->get_sparql_service();

foreach($array as $row){
	$endpoint = $row['endpoint']['value'];
	$datasetURI = $row['dataset']['value'];	
	$Endpoint->uri = $endpoint;
	echo $endpoint."\n";
	foreach($queriesAndDimensions as $dimension => $query){
			$count = $Endpoint->select_to_array($query);
			if(isset($count[0])){
			$count = $count[0]['count']['value'];
			$date = date(DATE_ATOM);
			$label = "{$dimension}: $count";
			echo $label;
			$turtle = <<<_T_
		@prefix void: <{$VOID}> .
		@prefix scovo: <http://purl.org/NET/scovo#> .
		@prefix dct: <http://purl.org/dc/terms/> .
		@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .

		<{$datasetURI}> void:statItem [
		    rdfs:label "{$label}"@en ;
			a scovo:Item ;
			scovo:dimension {$dimension} ;
			rdf:value "{$count}" ;
			dct:created "{$date}" .
		] .

_T_;
		$q = "prefix void: <{$VOID}> \n prefix scovo: <http://purl.org/NET/scovo#> \n construct { <{$datasetURI}> void:statItem ?statItem . ?statItem ?p ?o  .  } WHERE { <{$datasetURI}> void:statItem ?statItem . ?statItem ?p ?o ; scovo:dimension {$dimension} .  }";

		$r = $store->get_sparql_service()->graph($q);

		echo $turtle;
		echo "\n";
		echo $r->body;
		$A = array(
					'before' => $r->body, 
					'after' => trim($turtle),
				);
		$CS = new ChangeSet($A);

		($store->get_metabox()->apply_versioned_changeset($CS));
			$graph->add_rdf($turtle);
		}
	}

}
$turtle =  $graph->to_turtle();
file_put_contents("noOfTriples.n3",$turtle);
//$store->get_metabox()->submit_turtle($turtle);
?>