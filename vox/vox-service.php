<?php
include_once("../arc/ARC2.php");

$DEBUG = false;

// web app params
$VOX_BASE = "vox/";
$TEMPLATE_BASIC = "void-desc-basic.html";
$TEMPLATE_SPARQLEP = "void-desc-with-sparqlep.html";
$TEMPLATE_LOOKUPEP = "void-desc-with-lookupep.html";

// DBPedia lookup interface
$BASE_DBPEDIA_LOOKUP_URI = "http://lookup.dbpedia.org/api/search.asmx/KeywordSearch?QueryClass=string&MaxHits=5&QueryString=";

// voiD stores interface
$BASE_TALIS_LOOKUP_URI ="http://api.talis.com/stores/kwijibo-dev3/services/sparql?output=json&query=";
$BASE_RKB_LOOKUP_URI = "http://void.rkbexplorer.com/sparql/?format=json&query=";

$defaultprefixes = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> PREFIX dcterms: <http://purl.org/dc/terms/> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX void: <http://rdfs.org/ns/void#> PREFIX dbpedia-owl: <http://dbpedia.org/ontology/> ";

/* ARC2 RDF store config - START */
$config = array(
	'db_name' => 'arc2',
	'db_user' => 'root',
	'db_pwd' => 'root',
	'store_name' => 'vox'
); 

$store = ARC2::getStore($config);

if (!$store->isSetUp()) {
  $store->setUp();
  echo 'set up';
}
/* ARC2 RDF store config - END */


/* voX INTERFACE */

//// GET interface

if(isset($_GET['reset'])) {
	$store->reset();
	echo "RESET store done.<br />\n";
	echo "<p>go <a href='index.html'>home</a> ...</p>\n";     
}

if(isset($_GET['uri'])){
	$overwrite = false;
	if(isset($_GET['overwrite'])) $overwrite = true;
	echo renderVoiD($_GET['uri'], $overwrite);
}

if(isset($_GET['all'])){
	echo listAllDatasets();
}



if(isset($_GET['topic'])){
	echo getTopicDescription($_GET['topic']);
}

if(isset($_GET['example'])){
	echo getExampleDescription($_GET['example']);
}

///// POST interface
if(isset($_POST['qParams'])){ // 
	$qParams = json_decode($_POST['qParams'], true);
	
	$result = executeQuery($qParams);
	if(!$result) { // conneg for application/sparql-results+json failed
		$result = executeQuery($qParams, "format"); // try again with param &format=json
		if(!$result) { // param &format=json failed
			$result = executeQuery($qParams, "output"); // try again with param &ouput=json		
		}
	}
	echo $result;
}

if(isset($_POST['lParams'])){ // 
	$lParams = json_decode($_POST['lParams'], true);
	echo executeLookup($lParams);
}




/* voX METHODS */

function renderVoiD($voidURI, $overwrite){
	global $DEBUG;
	global $TEMPLATE_BASIC;
	global $TEMPLATE_SPARQLEP;
	global $TEMPLATE_LOOKUPEP;
	global $store;
	global $defaultprefixes;

	$entityConceptList = array();

	if(!isDataLocal($voidURI)) { // we haven't tried to dereference the voiD URI yet
		loadData($voidURI); //... hence we dereference it and load it into the store
	}
	else { // data is already in store, check if we need to overwrite the content
		if($overwrite) {
			removeData($voidURI);
			loadData($voidURI);
		}
	}
	
	$cmd = $defaultprefixes;
	$cmd .= "SELECT DISTINCT *  FROM <" . $voidURI . "> WHERE "; 
	$cmd .= "{ ?ds a void:Dataset ;  
		OPTIONAL { ?ds dcterms:title ?title ; }
		OPTIONAL { ?ds dcterms:description ?description ; }
		OPTIONAL { ?ds dcterms:publisher ?publisher ; }
		OPTIONAL { ?ds dcterms:date ?date1 ; }
		OPTIONAL { ?ds dcterms:modified ?date2 ; }
		OPTIONAL { ?ds foaf:homepage ?homepage ;}
		OPTIONAL { ?ds dcterms:subject ?topic ;}
		OPTIONAL { ?ds void:vocabulary ?vocab ;}
		OPTIONAL { ?ds void:exampleResource ?exampleRes ;}
		OPTIONAL { ?ds void:sparqlEndpoint ?sparqlEndpoint ;}
		OPTIONAL { ?ds void:uriLookUpEndpoint ?lookupEndpoint ;}
		OPTIONAL { ?ds void:uriRegexPattern ?uriRegEx ;}
	}";
	
	if($DEBUG) echo htmlentities($cmd) . "<br />";
	
	$results = $store->query($cmd);
	
	$retVal = "<p style='padding-left: 10px'>The voiD file <a href='$voidURI' title='voiD file'>$voidURI</a> contains the following dataset descriptions:</p><div class='dsdescription'>";
	$dsList = array();
	$dsListGlobal = array();
	$dsURI = "";
	$dsDataset2Topics = array();
	$dsDataset2Examples = array();
	$dsDataset2Vocs = array();
		
	// gather dataset metadata and render general information
	if($results['result']['rows']) {
		foreach ($results['result']['rows'] as $row) {
			$dsURI = $row['ds'];
			
			// global dataset information
			if(!in_array($dsURI, $dsList)) { // remember dataset, pull global info and pre-fill template
				$dsDatasetGlobal = array();
				$dsDatasetGlobal['URI'] = $dsURI;
				if($row['title']) $dsDatasetGlobal['title'] = $row['title'];
				else $dsDatasetGlobal['title'] = "unknown";
				if($row['description']) $dsDatasetGlobal['description'] = $row['description'];
				else $dsDatasetGlobal['description'] = "unknown";
				if($row['publisher']) $dsDatasetGlobal['publisher'] = $row['publisher'];
				else $dsDatasetGlobal['publisher'] = "";
				if($row['date1']) $dsDatasetGlobal['date'] = $row['date1'];
				else { // prefered is dct:date, but if this is not available use dct:modified
					if($row['date2']) $dsDatasetGlobal['date'] = $row['date2'];
					else $dsDatasetGlobal['date'] = "unknown";
				}				
				if($row['homepage']) $dsDatasetGlobal['homepage'] = $row['homepage'];
				else $dsDatasetGlobal['homepage'] = "";
				if($row['sparqlEndpoint']) $dsDatasetGlobal['sparqlEndpoint'] = $row['sparqlEndpoint'];
				else $dsDatasetGlobal['sparqlEndpoint'] = "unknown";
				if($row['lookupEndpoint']) $dsDatasetGlobal['lookupEndpoint'] = $row['lookupEndpoint'];
				else $dsDatasetGlobal['lookupEndpoint'] = "unknown";
				array_push($dsListGlobal, $dsDatasetGlobal);
				array_push($dsList, $dsURI);
			}
			
			// remember dataset topics
			if($row['topic']){
				if(!isset($dsDataset2Topics[$dsURI])){
					$dsDataset2Topics[$dsURI] = array();
				}
				if(!in_array($row['topic'], $dsDataset2Topics[$dsURI])){
					array_push($dsDataset2Topics[$dsURI], $row['topic']); 
				}	
			}
			
			// remember dataset examples resources
			if($row['exampleRes']){
				if(!isset($dsDataset2Examples[$dsURI])){
					$dsDataset2Examples[$dsURI] = array();
				}
				if(!in_array($row['exampleRes'], $dsDataset2Examples[$dsURI])){
					array_push($dsDataset2Examples[$dsURI], $row['exampleRes']); 
				}	
			}
			
			// remember dataset vocs
			if($row['vocab']){
				if(!isset($dsDataset2Vocs[$dsURI])){
					$dsDataset2Vocs[$dsURI] = array();
				}
				if(!in_array($row['vocab'], $dsDataset2Vocs[$dsURI])){
					array_push($dsDataset2Vocs[$dsURI], $row['vocab']); 
				}	
			}
		}
	}
	
	// render TOC
	if(!empty($dsListGlobal) && count($dsListGlobal) > 1){
		$retVal .= "<div class='toc'>";
		$retVal .= "<h2 class='ui-widget-header ui-corner-all'>Summary</h2>";
		$dsCounter = 1;
		foreach ($dsListGlobal as $dsglobalinfo){
			$retVal .= "<div class='tocentry'><a class='ui-state-default ui-corner-all smallbtn' href='#ds" . $dsCounter++ ."' title='Explore details of the dataset'>Explore details</a><span style='margin-left: 5px'><a href='" . $dsglobalinfo['URI'] ."' title='" . $dsglobalinfo['title']."'>" . $dsglobalinfo['URI'] . "</a></span></div>";
		}
		$retVal .= "</div>";
	}
	
	// render dataset details
	if(!empty($dsListGlobal)){
		$dsCounter = 1;
		$retVal .= "<h2 class='ui-widget-header ui-corner-all'>Details</h2>";
		foreach ($dsListGlobal as $dsglobalinfo){
			// global dataset metadata
			$descTemplate = file_get_contents($TEMPLATE_BASIC);
			$search  = array('%DATASET_URI%', '%DATASET_TITLE%', '%DATASET_DESCRIPTION%', '%DATASET_PUBLISHER%' ,'%DATASET_DATE%', '%DATASET_HOMEPAGE%' );
			$replace = array($dsglobalinfo['URI'], $dsglobalinfo['title'], $dsglobalinfo['description'], $dsglobalinfo['publisher'], $dsglobalinfo['date'], $dsglobalinfo['homepage']);
			$globalInfo = str_replace($search, $replace, $descTemplate);
			
			if($dsglobalinfo['sparqlEndpoint'] !== "unknown") { // we have a SPARQL endpoint
				$descTemplate = file_get_contents($TEMPLATE_SPARQLEP);
				$search  = array('%DATASET_SPARQLEP%');
				$replace = array($dsglobalinfo['sparqlEndpoint']);
				$sparqlEndpointInfo = str_replace($search, $replace, $descTemplate);
			}
			else $sparqlEndpointInfo = "";
			
			if($dsglobalinfo['lookupEndpoint'] !== "unknown") { // we have a URI lookup endpoint
				$descTemplate = file_get_contents($TEMPLATE_LOOKUPEP);
				$search  = array('%DATASET_LOOKUPEP%');
				$replace = array($dsglobalinfo['lookupEndpoint']);
				$lookupEndpointInfo = str_replace($search, $replace, $descTemplate);
			}
			else $lookupEndpointInfo = "";
		
			$retVal .= "<a id='ds" . $dsCounter++ . "'></a>";
			if(substr($dsglobalinfo['URI'], 0, 2) === "_:") {
				$retVal .= "<h1>". $dsglobalinfo['URI'] ."</h1>";				
			}
			else {
				$retVal .= "<h1><a href='". $dsglobalinfo['URI'] ."' target='_new' title='" . $dsglobalinfo['title'] ."'>". $dsglobalinfo['URI'] ."</a></h1>";
			}
			$retVal .= $globalInfo;
			$retVal .= $sparqlEndpointInfo;
			$retVal .= $lookupEndpointInfo;	
			$retVal .= "</div></div><div class='sectseparator'></div>";
			
			if(!empty($dsDataset2Topics) && count($dsDataset2Topics) > 0 ){ // we have topics to render
				foreach ($dsDataset2Topics as $topicsKey => $topicsValue){
					if($topicsKey === $dsglobalinfo['URI']) {
						$retVal .= "<h2>Topics</h2>";
						$retVal .= "<p class='topic'>The dataset is about:</p>";
						foreach ($topicsValue as $topicsURI){
							$retVal .=  getTopicDescription($topicsURI);
						}
						$retVal .= "<div class='sectseparator'></div>";
					}
				}

			}
			/*
			else {
				$retVal .= "<h2>Topics</h2>";
				$retVal .= "<p class='topic'>Dataset topics are unknown.</p><div class='sectseparator'></div>";
			}
			*/
			if(!empty($dsDataset2Examples) && count($dsDataset2Examples) > 0){ // we have examples to render
				foreach ($dsDataset2Examples as $examplesKey => $examplesValue){
					if($examplesKey === $dsglobalinfo['URI']) {
						$retVal .= "<h2>Examples</h2>";
						$retVal .= "<p class='exampleres'>Some example resources of the dataset:</p>";
						foreach ($examplesValue as $examplesURI){
							$retVal .=  getExampleDescription($examplesURI);
						}
						$retVal .= "<div class='sectseparator'></div>";
					}
				}
			}
			/*
			else {
				$retVal .= "<h2>Examples</h2>";
				$retVal .= "<p class='exampleres'>Example resources of the dataset are unknown.</p><div class='sectseparator'></div>";
			}
			*/
			if(!empty($dsDataset2Vocs) && count($dsDataset2Vocs) > 0){ // we have vocabs to render
				foreach ($dsDataset2Vocs as $vocabKey => $vocabValue){
					if($vocabKey === $dsglobalinfo['URI']) {
						$retVal .= "<h2>Vocabularies</h2>";
						$retVal .= "<p class='vocabs'>Some vocabularies used in the dataset:</p>";
						foreach ($vocabValue as $vocabURI){
							$retVal .=  getVocabDescription($vocabURI);
						}
						$retVal .= "<div class='sectseparator'></div>";
					}
				}
			}
			/*
			else {
				$retVal .= "<h2>Vocabularies</h2>";
				$retVal .= "<p class='exampleres'>Vocabs used in the dataset are unknown.</p><div class='sectseparator'></div>";
			}
			*/
		}
	}
	else $retVal = "<p style='padding-left: 20px'>Sorry, didn't find any dataset descriptions.</p>";
	
	return $retVal . "</div>";
}

// dereferences topic resource and retrieves dcterms:title and/or rdfs:label of the topic resource
function getTopicDescription($topicURI){
	global $DEBUG;
	global $store;
	global $defaultprefixes;
	
	if(!isDataLocal($topicURI)) { // we haven't tried to dereference the topic URI yet
		loadData($topicURI); // ... hence we dereference it and load it into the store
	}
	
	$cmd = $defaultprefixes;
	$cmd .= "SELECT DISTINCT * FROM <" . $topicURI . "> WHERE "; 
	$cmd .= "{  
		<" . $topicURI . "> rdfs:label ?title .
		OPTIONAL {	<" . $topicURI . "> dbpedia-owl:abstract ?abstract ; }
		FILTER langMatches( lang(?title), 'EN' )
		FILTER langMatches( lang(?abstract), 'EN' )
	}";
	
	if($DEBUG) echo htmlentities($cmd) . "<br />";
	
	$results = $store->query($cmd);
	
	if($results['result']['rows']) {
		foreach ($results['result']['rows'] as $row) {
			if($row['title']) {
				if($row['abstract']) $abstract = $row['abstract'];
				else $abstract = "???";  
				return "<div resource='$topicURI' class='dstopic'><span class='ui-state-default ui-corner-all smallbtn' title='Expand to view description'>+</span> <a href='$topicURI' target='_new'>". $row['title'] . "</a><div class='topicdetails'>$abstract</div></div>";
				
			}
			else return "Didn't find the topic title, sorry ..."; 
		}
	}
	else return "<div resource='$topicURI' class='dstopic'><a href='$topicURI' target='_new'>$topicURI</a> ...</div>";

}

// renders example resource
function getExampleDescription($exampleURI){
 return "<div resource='$exampleURI' class='dsexample'><span class='ui-state-default ui-corner-all smallbtn' title='View details about resource in Sig.ma'><a href='http://sig.ma/search?singlesource=$exampleURI&raw=1' target='_new'>View details ...</a></span> <a href='$exampleURI' target='_new'>$exampleURI</a></div>";	

}

// renders vocab
function getVocabDescription($vocabURI){
 return "<div resource='$vocabURI' class='dsvocab'><span class='ui-state-default ui-corner-all smallbtn' title='View details about vocabulary in Sig.ma'><a href='http://sig.ma/search?singlesource=$vocabURI&raw=1' target='_new'>View details ...</a></span> <a href='$vocabURI' target='_new'>$vocabURI</a></div>";	

}

function listAllDatasets(){
	global $DEBUG;
	global $store;
	global $defaultprefixes;
	$retVal = "<div>All voiD files I have:</div>";
	
	$cmd = $defaultprefixes;
	$cmd .= "SELECT DISTINCT ?g  WHERE {"; 
	$cmd .= " GRAPH ?g { ?ds a void:Dataset . } }";  
	
	if($DEBUG) echo htmlentities($cmd) . "<br />";
	
	$results = $store->query($cmd);
	if($results['result']['rows']) {
		foreach ($results['result']['rows'] as $row) {
			$voidURI = $row['g'];
			$retVal .= "<div>" . $voidURI . "</div>";
		}
	}
	return $retVal;
}


// low-level ARC2 store methods
function isDataLocal($graphURI){
	global $store;
	
	$cmd = "SELECT ?s FROM <$graphURI> WHERE { ?s ?p ?o .}";

	$results = $store->query($cmd);
	
	if($results['result']['rows']) return true;
	else return false;
}

function loadData($dataURI) {
	global $store;
	global $DEBUG;
	
	$cmd .= "LOAD <$dataURI> INTO <$dataURI>"; 
	
	if($DEBUG) echo htmlentities($cmd) . "<br />";

	$store->query($cmd);
	$errs = $store->getErrors();
	
	return $errs;
}

function removeData($dataURI) {
	global $store;
	global $DEBUG;
	
	$cmd .= "DELETE FROM <$dataURI>"; 
	
	if($DEBUG) echo htmlentities($cmd) . "<br />";

	$store->query($cmd);
	$errs = $store->getErrors();
	
	return $errs;
}



// various utility methods
function lookupSubjectInDBPedia($keyword){
	global $BASE_DBPEDIA_LOOKUP_URI;
	global $DEBUG;
	
	$matches = array();
	
	$data = file_get_contents($BASE_DBPEDIA_LOOKUP_URI . $keyword);
	$parser = xml_parser_create();
	xml_parse_into_struct($parser, $data, $values);
	xml_parser_free($parser);
	for( $i=0; $i < count($values); $i++ ){
		$match = array();
		
		if($values[$i]['tag'] == strtoupper("Description")) {
			$desc =  $values[$i]['value'];
		}
		if($values[$i]['tag']==strtoupper("Label")){
			$label =  $values[$i]['value'];
		}
		if($values[$i]['tag']==strtoupper("URI") && 
			(strpos($values[$i]['value'], "http://dbpedia.org/resource") == 0) &&
			(strpos($values[$i]['value'], "Category:") === false)
		){ // use only resource URIs and exclude category resource URIs
			$URI =  $values[$i]['value'];
		}
		
		if(isset($URI) && isset($desc)&& isset($label)) {
			$match['URI'] = $URI;
			$match['label'] = $label;
			$match['desc'] = $desc;
			
			array_push($matches, $match);
			if($DEBUG) {
				echo "<strong>" . $URI . "</strong>:<p>" . $desc . "</p>" ;
			}
			unset($URI);
			unset($desc);
			unset($label);
		}
	}
	return json_encode($matches);
}


function listSPARQLEndpoints($lookupURI){
	global $DEBUG;
	$ret = array();
			
	$query = "SELECT DISTINCT ?endpoint ?ds WHERE { ?ds a <http://rdfs.org/ns/void#Dataset> ; <http://rdfs.org/ns/void#sparqlEndpoint> ?endpoint . }";
	
	if($DEBUG) echo $query . "<br />\n";
	
	$jsondata = file_get_contents($lookupURI . urlencode($query));
	if($DEBUG) var_dump($jsondata);
	
	$data = json_decode($jsondata, true); 	
	
	foreach($data["results"]["bindings"] as $binding){
		$endpointList["ds"] =  $binding["ds"]["value"];		
		$endpointList["endpoint"] =  $binding["endpoint"]["value"];
		$ret[] = $endpointList;
	}
	
	return json_encode($ret);
}

function executeQuery($queryParams, $guessformatparam){
	global $DEBUG;

	$endpointURI = $queryParams["endpointURI"];
	$queryStr = $queryParams["queryStr"];

	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_HEADER, 0);
	if(isset($guessformatparam)) { // forced to guess result format via param
		curl_setopt($c, CURLOPT_URL, $endpointURI . "?query=" . urlencode($queryStr) . "&$guessformatparam=json");
	}
	else { // use conneg
		curl_setopt($c, CURLOPT_HTTPHEADER, array ("Accept: application/sparql-results+json"));
		curl_setopt($c, CURLOPT_URL, $endpointURI . "?query=" . urlencode($queryStr));// . "&output=json"); . "&format=json");
	}
	curl_setopt($c, CURLOPT_TIMEOUT, 30);
	$result = curl_exec($c);
	
	if(!curl_errno($c)) {
		$info = curl_getinfo($c);
		if($info['http_code'] != "200") $result = false;
	}
	
	curl_close($c);
	return $result;
}


function executeLookup($lookupParams){
	global $DEBUG;

	$endpointURI = $lookupParams["endpointURI"];
	$queryStr = $lookupParams["queryStr"];

 	return $endpointURI . urlencode($queryStr);

 	//return "<div style='margin:0px; padding: 0px; clear:left'><a class='ui-state-default ui-corner-all smallbtn' href='$execURI' target='_new' title='Result of lookup'>View results in new window ...</a></div>";
	
}




?>