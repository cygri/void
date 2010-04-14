<?php
if(isset($_GET['output'])){
	switch($_GET['output']){
		case 'xml':
			header("Content-type: application/rdf+xml");
			echo $graph->to_rdfxml();
			exit;
		case 'turtle':
		case 'n3':
			header("Content-type: text/turtle");
			echo $graph->to_turtle();
			exit;
		case 'json':
			header("Content-type: application/json");
			echo $graph->to_json();
			exit;
		
	}
}
?>