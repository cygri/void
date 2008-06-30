<?php
include_once("C:/Program Files/Apache Software Foundation/Apache2.2/htdocs/arc2/ARC2.php");

$DEBUG = false;
$VOID_SEEDS_URI = "http://sw.joanneum.at/ve/void-seeds.rdf";
$VOID_SEEDS_GRAPH = "http://sw.joanneum.at/void-seeds/";
$DBPEDIA_GRAPH = "http://dbpedia.org";
$DBPEDIA_RES_URI = "http://dbpedia.org/resource/";
$SINDICE_GRAPH  = "http://sindice.com/";

/* ARC RDF store config */
$config = array(
  'db_host' => 'localhost',
	'db_name' => 'arcdb',
	'db_user' => 'arc',
	'db_pwd' => '',
	'store_name' => 've'
);

/* global store init (one shot)*/	
$store = ARC2::getStore($config);
if (!$store->isSetUp()) {
	$store->setUp();
	echo "done";
}


$NAMESPACES = array(
		'xsd' => 'http://www.w3.org/2001/XMLSchema#',
  	'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
  	'rdfa' => 'http://www.w3.org/1999/xhtml/vocab#',
  	'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
		'owl' => 'http://www.w3.org/2002/07/owl#',
  	'foaf' => 'http://xmlns.com/foaf/0.1/',
  	'dc' => 'http://purl.org/dc/elements/1.1/', 
  	'dcterms' => 'http://purl.org/dc/terms/',
  	'skos' => 'http://www.w3.org/2004/02/skos/core#',
  	'sioc' => 'http://rdfs.org/sioc/ns#',
  	'sioct' => 'http://rdfs.org/sioc/types#',
  	'xfn' => 'http://gmpg.org/xfn/11#',
  	'twitter' => 'http://twitter.com/'   	
);


/* INTERFACE */

// SPARQL end point at http://143.224.254.32/ve/sparql.php

// http://143.224.254.32/ve/service.php?reset
if(isset($_GET['reset'])) {
		$store->reset();
		echo "store has been reseted, master<br />\n";
		initSeeds();
		echo "init seeds:<br />\n";	
		echo listSeeds();			
}

if(isset($_GET['lookup'])){ 
	$term = $_GET['lookup']; 			
	echo lookupDBpediaConcept($term);		
}

if(isset($_GET['find'])){ 
	$name = $_GET['find']; 			
	//echo lookupNameInSindice($name);		
	echo listSeeds($name);
}		


/* LIB */

// lookup a concept in DBpedia.org and offer alternatives
// use, e.g., http://143.224.254.32/ve/service.php?lookup=computers
function lookupDBpediaConcept($term){
	global $store;
	global $DEBUG;
	global $DBPEDIA_RES_URI;
	
	$dbpResourceURI = $DBPEDIA_RES_URI . ucfirst($term);  
	
	if(!hasDBpediaConcept($term)){ // not yet in store, try to load a DBpedia resource for a term
		$load = "LOAD <$dbpResourceURI> INTO <$dbpResourceURI>"; 
  	if($DEBUG) echo htmlentities($load) . "<br />";
  	$store->query($load);
 	}
 	 	
  $rows = getDBpediaConcept($term);
  $r .= "<div>";  
  if($rows != null) {
    //echo var_dump($rows['result']['rows']);
  	$r .= "<p>Found &lt;<a href=\"$dbpResourceURI\">$dbpResourceURI</a>&gt; as a subject [<a href=\"javascript:useAsSubject('" .  $dbpResourceURI . "');\">use this</a>].</p>";
	 	$r .= "<p>There are aliases that might have more information:</p><ul>";
	 	foreach ($rows['result']['rows'] as $row) {
	 		$alias = $row['redirect'];	  		  			
		  $r .= "<li><a href=\"$alias\">$alias</a> [<a href=\"javascript:useAsSubject('" .  $alias . "');\">use this</a>]</li>";				
		 }
		$r .= "</ul>";
	}
	else $r .= "<p>Sorry, no subject found ... </p>";
	$r .= "</div>";
	return $r;
}

function hasDBpediaConcept($term){
	global $store;
	global $DEBUG;
	global $DBPEDIA_RES_URI;
	
	$dbpResourceURI = $DBPEDIA_RES_URI . ucfirst($term);  
	
	$q = "ASK WHERE { GRAPH <$dbpResourceURI>  {?s ?p ?o . } }";
	if($DEBUG) echo htmlentities($q) . "<br />";
	$rs = $store->query($q);
	return $rs['result'];
}

function getDBpediaConcept($term){
	global $store;
	global $DEBUG;
	global $DBPEDIA_RES_URI;
	
	$dbpResourceURI = $DBPEDIA_RES_URI . ucfirst($term);  
	
	$q = "PREFIX dbpediaprop: <http://dbpedia.org/property/> SELECT DISTINCT ?redirect FROM <$dbpResourceURI> { <$dbpResourceURI>  dbpediaprop:redirect ?redirect . }";
	if($DEBUG) echo htmlentities($q) . "<br />";
	$rs = $store->query($q);
	return $rs;
}


function initSeeds(){
	global $store;
	global $DEBUG;
	global $VOID_SEEDS_URI;
	global $VOID_SEEDS_GRAPH;
	
	$load = "LOAD <$VOID_SEEDS_URI> INTO <$VOID_SEEDS_GRAPH>"; 
  if($DEBUG) echo htmlentities($load) . "<br />";
  $store->query($load);
}


function listSeeds($name){
	$rows = getSeeds($name);
	$r = "<div><p>Following seed datasets are available:</p>";
	if($rows != null) {
	    $r .= "<ul>";
		 	foreach ($rows['result']['rows'] as $row) {
		 		$label = $row['label'];	  		  			
		 		$home = $row['home'];	  		  			
		 		$r .= "<li><a href=\"$home\">$label</a> [<a href=\"javascript:useAsTarget('" . $home . "')\">use this</a>]</li>";				
			}
			$r .= "</ul>";
		}		
	$r .= "</div>";
	return $r;
}
	
function getSeeds($name){
	global $store;
	global $DEBUG;
	global $VOID_SEEDS_GRAPH;
	
	if(isset($name)){
		$q = "PREFIX owl: <http://www.w3.org/2002/07/owl#> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>  SELECT DISTINCT * FROM <$VOID_SEEDS_GRAPH> { ?dataset owl:sameAs ?home; rdfs:label ?label . FILTER regex(?label, \"$name\", \"i\") }";	
	}
	else {
		$q = "PREFIX owl: <http://www.w3.org/2002/07/owl#> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>  SELECT DISTINCT * FROM <$VOID_SEEDS_GRAPH> { ?dataset owl:sameAs ?home; rdfs:label ?label . }";	
	}	
	if($DEBUG) echo htmlentities($q) . "<br />";
	$rs = $store->query($q);
	return $rs;
}


// lookup a name in sindice (to find a target dataset)
// use, e.g., http://143.224.254.32/ve/service.php?find=statistics
function lookupNameInSindice($name){
	global $store;
	global $SINDICE_GRAPH;
 	global $DEBUG;
 	$sindiceLookupURIRaw = "http://api.sindice.com/v2/search?q=". urlencode($name);
 	$sindiceLookupURI = $sindiceLookupURIRaw . "&format=rdfxml";
 	 	
	if(!hasSindiceName($name)){ // not yet in store, try to look up in sindice.com
 		if($DEBUG) echo "looking up $name in sindice.com <br />";
		$load = "LOAD <$sindiceLookupURI> INTO <" . $SINDICE_GRAPH . $name . ">"; 
  	if($DEBUG) echo htmlentities($load) . "<br />";
  	$store->query($load);
	}
	
  $rows = getSindiceName($name);
  $r .= "<div>";  
  if($rows != null) {
			$r .= "<p>Found the following on sindice.com:</p><ul>";
	 		foreach ($rows['result']['rows'] as $row) {
	 			$res = $row['link'];	
	  		$r .= "<li><a href=\"$link \">" . $res . "</a> [<a href=\"javascript:useAsTarget('" .$res . "')\">use this</a>]</li>";  	  	
			}
			$r .= "</ul>";
	}	
	else $r .= "<p>No results from sindice.com</p>";	
	$r .= "</div>";
	return $r;
}


function hasSindiceName($name){
	global $store;
	global $DEBUG;
	global $SINDICE_GRAPH;
	
	$sindiceNameURI = $SINDICE_GRAPH . $name; 
	
	$q = "ASK WHERE { GRAPH <$sindiceNameURI>  {?s ?p ?o . } }";
	if($DEBUG) echo htmlentities($q) . "<br />";
	$rs = $store->query($q);
	return $rs['result'];
}

function getSindiceName($name){
	global $store;
	global $DEBUG;
	global $SINDICE_GRAPH;
	
	$sindiceNameURI = $SINDICE_GRAPH . $name; 
	
	$q = "PREFIX s: <http://sindice.com/vocab/search#> SELECT DISTINCT ?link FROM <$sindiceNameURI> { ?res a s:Result ; s:link ?link . }";
	if($DEBUG) echo htmlentities($q) . "<br />";
	$rs = $store->query($q);
	return $rs;
}





?>