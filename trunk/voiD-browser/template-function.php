<?php
function renderBrowseLink($uri, $graph){
	echo '<a href="Describer?uri='.urlencode($uri).'">'.htmlentities($graph->get_label($uri)).'</a>';
}
function renderBnodeLink($uri, $graph){
	echo '<a class="bnode" href="#BNODE_'.($uri).'">'.htmlentities($graph->get_label($uri)).'</a>';
}

function renderDatasetsByVocabLink($uri, $graph){
		echo '<a href="datasetsByVocab?uri='.urlencode($uri).'">'.htmlentities($graph->get_label($uri)).'</a>';
}

function renderDatasetsBySubjectLink($uri, $graph){
		echo '<a href="datasetsBySubject?uri='.urlencode($uri).'">'.htmlentities($graph->get_label($uri)).'</a>';
}

function renderSparqlLink($uri, $graph){
		echo '<a href="sparql?uri='.urlencode($uri).'">'.htmlentities($graph->get_label($uri)).'</a>';
}

function renderLink($uri, $graph){
	echo '<a href="'.($uri).'">'.htmlentities($graph->get_label($uri)).'</a>';	
}

function renderLiteral($o){
	echo strip_tags($o['value']);
}
function renderObject($o, $property, $g){
	switch($o['type']){
		case 'uri':
			switch($property){
				case VOID.'vocabulary':
					renderDatasetsByVocabLink($o['value'], $g);
				break;
				case 'http://purl.org/dc/terms/subject':
					renderDatasetsBySubjectLink($o['value'], $g);
				break;
				case VOID.'sparqlEndpoint':
					renderSparqlLink($o['value'], $g);
				break;
				default:
					renderLink($o['value'], $g);
					// renderBrowseLink($o['value'], $g);
				break;
			}
			break;
		case 'bnode' :
			renderBnodeLink($o['value'], $g) ;
			break;
		default:
			renderLiteral($o);
			break;
	}
}

?>