<?php
include 'inc.php';
if(isset($_GET['uri'])){
	if(strpos($_GET['uri'], 'http://')== 0){
		
		$graph = new SimpleGraph();
		$parser = ARC2::getRDFParser();
		$parser->parse($_GET['uri']);
		$rdf = json_encode($parser->getSimpleIndex(0));
		$graph->add_json($rdf);
		$query = <<<_SPARQL_
		prefix void: <{$VOID}>
		DESCRIBE ?dataset {
			 { 
				?dataset void:uriRegexPattern ?regex ; void:sparqlEndpoint ?sparql ; a void:Dataset . 
				FILTER(REGEX(STR("{$_GET['uri']}"), ?regex))
			} UNION {
				?dataset void:uriRegexPattern ?regex ; void:uriLookupEndpoint ?endpoint ; a void:Dataset . 
				FILTER(REGEX(STR("{$_GET['uri']}"), ?regex))
			}
		}
_SPARQL_;

		$queries = array();
		$response = $store->get_sparql_service()->graph($query);
		if($response->is_success()){
			$void = new SimpleGraph();
			$void->from_rdfxml($response->body);
			$count =0;
			foreach ($void->get_index() as $uri => $props) {
				$count++;
				if(isset($props[$VOID.'uriLookupEndpoint'])){
					$url = $props[$VOID.'uriLookupEndpoint'][0]['value'].urlencode($_GET['uri']);
					$parser = ARC2::getRDFParser();
					$parser->parse($url);
					$rdf = $parser->getSimpleIndex(0);
					$graph->add_json(json_encode($rdf));
					$queries[]=$url;
				}
				 else if(isset($props[$VOID.'sparqlEndpoint'])){
					$url = $props[$VOID.'sparqlEndpoint'][0]['value'].'?query='.urlencode('DESCRIBE <'.$_GET['uri'].'>');
					$rdf = file_get_contents($url);
					$graph->add_rdf($rdf);
					$queries[]=$url;
				} 
			}
	
		}
		else
		{
			$error = "Bad Input {$response->status_code} {$response->body}";
		}
	} 	
	else 
	{
				$error = "Bad Input: URI should begin with http://";
	}
} else {
			$query = <<<_SPARQL_
			prefix void: <{$VOID}>
			SELECT ?example {
				 { 
					?dataset void:exampleResource ?example  .
				}
			LIMIT 3
_SPARQL_;
			$examples = $store->get_sparql_service()->select_to_array($query);
}


$filename = 'describer.html';
include 'templates/template.html';
?>


