<?php
include 'inc.php';

$query = <<<_q_
prefix void: <http://rdfs.org/ns/void#>
prefix dc: <http://purl.org/dc/elements/1.1/>
prefix dct: <http://purl.org/dc/terms/>
prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
prefix wv: <http://vocab.org/waiver/terms/>
prefix foaf: <http://xmlns.com/foaf/0.1/>

select distinct ?datasetsBy ?label (count( distinct ?dataset) as ?count) 
{
	?dataset a void:Dataset .

	?dataset  <{$datasetsByPredicate}> ?datasetsBy . 

	optional { ?datasetsBy rdfs:label ?label  }
	optional { ?datasetsBy dct:title ?label   }		
	optional { ?datasetsBy dc:title ?label    }		
	optional { ?datasetsBy foaf:name ?label    }		
	optional { ?datasetsBy <http://www.aktors.org/ontology/portal#full-name> ?label    }		
} 
	group by  ?datasetsBy  ?label
	order by desc(?count) 
_q_;
$array = $store->get_sparql_service()->select_to_array($query);
$graph = new SimpleGraph();
foreach($array as $n => $row){
	if(!isset($row['label'])){
	$array[$n]['label'] = array('type' =>'literal', 'value' => $graph->get_label($row['datasetsBy']['value']));	
	}
}

$filename = 'top.html';

include 'templates/template.html';
?>