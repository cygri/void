<?php
include_once("../arc/ARC2.php");
include_once("cloud-lib.php");
              
$DEBUG = true;
$SINDICE_GRAPH  = "http://sindice.com/";
$VOID_SEEDS_URI = "http://localhost:8888/lde/void-seeds.ttl";
$VOID_SEEDS_GRAPH = "http://ld2sd.deri.org/void-seeds/";
$DBPEDIA_GRAPH = "http://dbpedia.org/";

// visualsation
$MIN_RANGE = 1;
$MAX_RANGE = 100;


/* ARC RDF store config */
$config = array(
	'db_name' => 'arc2',
	'db_user' => 'root',
	'db_pwd' => 'root',
	'store_name' => 'lde',
); 

$store = ARC2::getStore($config);

if (!$store->isSetUp()) {
  $store->setUp();
  echo 'set up';
}

if(isset($_GET['reset'])) {
                $store->reset();
		        echo "store has been reseted, master<br />\n";
                initSeeds();
                echo "init seeds:<br />\n";     
                echo listSeeds();
                echo "<p>go <a href=\"index.html\">back</a> ...</p>\n";     
}


if(isset($_GET['lookup'])){ 
        $name = $_GET['lookup'];                        
        echo lookupNameInDBpedia(ucfirst($name)); // turn first character into upper cases as DBpedia concepts always start uppercase
}

if(isset($_GET['browse'])){                        
        echo listSeeds();               
}

if(isset($_GET['find'])){ 
        $topic = $_GET['find'];  
		echo listLinkedDatasetWithTopic($topic);
}

if(isset($_GET['explore'])){ 
        $dataset = $_GET['explore'];  
		if($DEBUG) echo $dataset;
		echo renderLinkedDataset($dataset);
}

if(isset($_GET['listbysize'])){ 
		echo renderLinkedDatasetbySize();
}



/* VOID SEEDS */

// load the seeds
function initSeeds(){
        global $store;
        global $DEBUG;
        global $VOID_SEEDS_URI;
        global $VOID_SEEDS_GRAPH;
        
        $load = "LOAD <$VOID_SEEDS_URI> INTO <$VOID_SEEDS_GRAPH>"; 
  		if($DEBUG) echo htmlentities($load) . "<br />";
  		$store->query($load);
}

// list the seeds
function listSeeds(){
		global $VOID_SEEDS_URI;
        $datasets = getLinkedDataset();
        $r = "<div><p>I know the following linked datasets (from <a href=\"$VOID_SEEDS_URI\">$VOID_SEEDS_URI</a>):</p>";
		$r .= renderDSCloud();
        if($datasets != null) {
            $r .= "<ul>";
			foreach ($datasets['result']['rows'] as $dataset) {
				$datasetURI = $dataset['dataset'];
				$label = $dataset['label'];                                         
                $home = $dataset['home'];                                      
                $r .= "<li style=\"padding-bottom: 20px;\"><a href=\"$home\">$label</a> ";  
				$r .= "(<a href=\"javascript:exploreDataset('" . urlencode($datasetURI) ."');\">explore</a>)<br />";
				/*
				$topics = getTopicsOfLinkedDataset($datasetURI); 
				if($topics != null) {
					$r .= "<div style=\"background-color: #f0f0f0; width: 40%; padding: 10px;\">";
					foreach ($topics['result']['rows'] as $topic) {
						$r .= "<div>";
						$topicRef = $topic['topic']; 
						$topicDesc = getDBpediaInfo($topicRef);	
						if($topicDesc != null) {
							foreach ($topicDesc['result']['rows'] as $desc) {
								$r .= "<a href=\"$topicRef\">" . $desc['label'] . "</a><br />";
								$r .= "<div style=\"border: 1px #c0c0c0 dotted; padding: 10px; margin-bottom: 5px; text-align: justify; font-size: 80%\">" . $desc['desc'] . "</div>"; 
							}
						}
						else $r .= "no description found";
						$r .= "</div>";
					}
					$r .= "</div>";
				}
				else $r .= "no topics found";
				*/
				$r .= "</li>";
			}
			$r .= "</ul>";
		}               
        $r .= "</div>";
        return $r;
}
        

function renderDSCloud(){
		global $VOID_SEEDS_URI;
        $datasets = getLinkedDatasetFull();
		$ds2numtriple = array();
		$ds2URI = array();
        if($datasets != null) {
			foreach ($datasets['result']['rows'] as $dataset) {
				$datasetURI = $dataset['dataset'];
				$label = $dataset['label'];                                         
                $home = $dataset['home'];  
				$numTriple = $dataset['numTriple'];                                     
                $ds2numtriple[$label] = $numTriple;
 				$ds2URI[$label] = "javascript:exploreDataset('" . urlencode($datasetURI) ."');";
			}
		}               
		$r = renderCloud($ds2numtriple, $ds2URI, true, true);
        return $r;
}



/* LINKED DATASETS OPERATIONS */

// finds linked datasets (short description)
// as soon as  datasets providers use voiD and sindice crawls it, this needs to be changed!
function getLinkedDataset(){
        global $store;
        global $DEBUG;
        global $VOID_SEEDS_GRAPH;
        
		$q = "PREFIX dcterms: <http://purl.org/dc/terms/> PREFIX foaf: <http://xmlns.com/foaf/0.1/>  SELECT DISTINCT * FROM <$VOID_SEEDS_GRAPH> { ?dataset foaf:homepage ?home; dcterms:title ?label. }";     
        if($DEBUG) echo htmlentities($q) . "<br />";
        $rs = $store->query($q);
        return $rs;
}

// finds linked datasets (long description)
// as soon as  datasets providers use voiD and sindice crawls it, this needs to be changed!
function getLinkedDatasetFull(){
        global $store;
        global $DEBUG;
        global $VOID_SEEDS_GRAPH;
          
		$q = "PREFIX dcterms: <http://purl.org/dc/terms/> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX scovo: <http://purl.org/NET/scovo#> PREFIX void: <http://rdfs.org/ns/void#>   SELECT DISTINCT * FROM <$VOID_SEEDS_GRAPH> { ?dataset foaf:homepage ?home; dcterms:title ?label. OPTIONAL { ?dataset void:statItem ?stat. ?stat rdf:value ?numTriple; scovo:dimension void:numberOfTriples . } }";     
	    if($DEBUG) echo htmlentities($q) . "<br />";
        $rs = $store->query($q);
        return $rs;
}


function getLinkedDatasetBySize(){
        global $store;
        global $DEBUG;
        global $VOID_SEEDS_GRAPH;
          
		$q = "PREFIX scovo: <http://purl.org/NET/scovo#> PREFIX void: <http://rdfs.org/ns/void#> SELECT DISTINCT * FROM <$VOID_SEEDS_GRAPH> { ?dataset a void:Dataset; void:statItem ?stat. ?stat rdf:value ?numTriple; scovo:dimension void:numberOfTriples .  }";     
	    if($DEBUG) echo htmlentities($q) . "<br />";
        $rs = $store->query($q);
        return $rs;
}



function getTopicsOfLinkedDataset($dsURI){
        global $store;
        global $DEBUG;
        global $VOID_SEEDS_GRAPH;
        
		$q = "PREFIX dcterms: <http://purl.org/dc/terms/> SELECT DISTINCT * FROM <$VOID_SEEDS_GRAPH> { <$dsURI> dcterms:subject ?topic . }";     
        if($DEBUG) echo htmlentities($q) . "<br />";
        $rs = $store->query($q);
        return $rs;
}


function findLinkedDatasetWithTopic($topic){
        global $store;
        global $DEBUG;
        global $VOID_SEEDS_GRAPH;
        
		$q = "PREFIX dcterms: <http://purl.org/dc/terms/> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX scovo: <http://purl.org/NET/scovo#> PREFIX void: <http://rdfs.org/ns/void#>   SELECT DISTINCT * FROM <$VOID_SEEDS_GRAPH> { ?dataset dcterms:subject <$topic> ; foaf:homepage ?home; dcterms:title ?label. OPTIONAL { ?dataset void:statItem ?stat. ?stat rdf:value ?numTriple; scovo:dimension void:numberOfTriples . } }";     
        if($DEBUG) echo htmlentities($q) . "<br />";
        $rs = $store->query($q);
        return $rs;
}

function listLinkedDatasetLinks($dataset){
        global $store;
        global $DEBUG;
        global $VOID_SEEDS_GRAPH;
        
		$q = "PREFIX dcterms: <http://purl.org/dc/terms/> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX void: <http://rdfs.org/ns/void#>   SELECT DISTINCT * FROM <$VOID_SEEDS_GRAPH> { <$dataset> void:subset ?linking. ?linking void:target ?target .  ?target dcterms:title ?label .  FILTER ( ?target != <$dataset>)}";     
        if($DEBUG) echo htmlentities($q) . "<br />";
        $rs = $store->query($q);
        return $rs;
}


function listLinkedDatasetWithTopic($targettopic){
		global $VOID_SEEDS_URI;
        $datasets = findLinkedDatasetWithTopic($targettopic);
        $r = "<div><p>I know the following linked datasets (from <a href=\"$VOID_SEEDS_URI\">$VOID_SEEDS_URI</a>) that cover topic <a href=\"$targettopic\">$targettopic</a>:</p>";
        if($datasets != null) {
            $r .= "<ul>";
			foreach ($datasets['result']['rows'] as $dataset) {
				$datasetURI = $dataset['dataset'];
				$label = $dataset['label'];                                         
                $home = $dataset['home'];            
				$numTriple = $dataset['numTriple'];     
				$topics = getTopicsOfLinkedDataset($datasetURI);       
				$links = listLinkedDatasetLinks($datasetURI);                     
                $r .= "<li style=\"padding-bottom: 20px;\"><b>$label</b>:";  
  				$r .= "<div style=\"background-color: #f0f0f0;  width: 60%; padding: 10px; padding-bottom: 0px;\">";
  				$r .= "<b>home page</b>: <a href=\"$home\">$home</a><br />";  
				$r .= "<b>number of triples</b>: $numTriple<br />";  
				$r .= "<b>links to</b>:";
				if($links != null) {
					$r .= "<div style=\"border-left: 1px #f0f0f0 solid; width: 60%; padding: 10px; padding-left: 60px;\">";
					foreach ($links['result']['rows'] as $link) {
						$target = $link['target']; 
						$label = $link['label']; 
						$r .= "&rsaquo; <a href=\"$target\">$label</a> (<a href=\"javascript:exploreDataset('" . urlencode($target) ."');\">explore</a>)<br />";
					}
					$r .= "</div>";
				}
				else $r .= " none";  
				$r .= "<br />";  
				$r .= "<b>topics</b>:"; 
				$r .= "</div>";
				
				if($topics != null) {
					$r .= "<div style=\"border: 1px #f0f0f0 solid; width: 60%; padding: 10px; padding-left: 60px;\">";
					foreach ($topics['result']['rows'] as $topic) {
						$r .= "<div>";
						$topicRef = $topic['topic']; 
						$topicDesc = getDBpediaInfo($topicRef);		
						if($topicDesc != null) {
							foreach ($topicDesc['result']['rows'] as $desc) {
								$r .= "<a href=\"$topicRef\">" . $desc['label'] . "</a><br />";
								$r .= "<div style=\"border: 1px #c0c0c0 dotted; width: 90%; padding: 10px; margin-bottom: 5px; text-align: justify; font-size: 80%\">" . $desc['desc'] . "</div>"; 
							}
						}
						else $r .= "no description found";
						$r .= "</div>";
					}
					$r .= "</div>";
				}
				else $r .= "no topics found";
				$r .= "</li>";
			}
			$r .= "</ul>";
		}               
        $r .= "</div>";
        return $r;
}


function renderLinkedDataset($datasetURI){
		global $VOID_SEEDS_URI;
		global $VOID_SEEDS_GRAPH;
		global $store;
        global $DEBUG;

		$q = "PREFIX dcterms: <http://purl.org/dc/terms/> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX scovo: <http://purl.org/NET/scovo#> PREFIX void: <http://rdfs.org/ns/void#>   SELECT DISTINCT * FROM <$VOID_SEEDS_GRAPH> { <$datasetURI> foaf:homepage ?home; dcterms:title ?label. OPTIONAL { <$datasetURI> void:statItem ?stat. ?stat rdf:value ?numTriple; scovo:dimension void:numberOfTriples . } }";     
        if($DEBUG) echo htmlentities($q) . "<br />";
        $datasets = $store->query($q);       

        $r = "<div><p>From <a href=\"$VOID_SEEDS_URI\">$VOID_SEEDS_URI</a>:</p>";
        if($datasets != null) {
            $r .= "<ul>";
			foreach ($datasets['result']['rows'] as $dataset) {
				$label = $dataset['label'];                                         
                $home = $dataset['home'];            
				$numTriple = $dataset['numTriple'];     
				$topics = getTopicsOfLinkedDataset($datasetURI);       
				$links = listLinkedDatasetLinks($datasetURI);                     
                $r .= "<li style=\"padding-bottom: 20px;\"><b>$label</b>:";  
  				$r .= "<div style=\"background-color: #f0f0f0;  width: 60%; padding: 10px; padding-bottom: 0px;\">";
  				$r .= "<b>home page</b>: <a href=\"$home\">$home</a><br />";  
				$r .= "<b>number of triples</b>: $numTriple<br />";  
				$r .= "<b>links to</b>:";
				if($links != null) {
					$r .= "<div style=\"border-left: 1px #f0f0f0 solid; width: 60%; padding: 10px; padding-left: 60px;\">";
					foreach ($links['result']['rows'] as $link) {
						$target = $link['target']; 
						$label = $link['label']; 
						$r .= "&rsaquo; <a href=\"$target\">$label</a> (<a href=\"javascript:exploreDataset('" . urlencode($target) ."');\">explore</a>)<br />";
					}
					$r .= "</div>";
				}
				else $r .= " none";  
				$r .= "<br />";  
				$r .= "<b>topics</b>:"; 
				$r .= "</div>";
				
				if($topics != null) {
					$r .= "<div style=\"border: 1px #f0f0f0 solid; width: 60%; padding: 10px; padding-left: 60px;\">";
					foreach ($topics['result']['rows'] as $topic) {
						$r .= "<div>";
						$topicRef = $topic['topic']; 
						$topicDesc = getDBpediaInfo($topicRef);	
						if($topicDesc != null) {
							foreach ($topicDesc['result']['rows'] as $desc) {
								$r .= "<a href=\"$topicRef\">" . $desc['label'] . "</a><br />";
								$r .= "<div style=\"border: 1px #c0c0c0 dotted; width: 90%; padding: 10px; margin-bottom: 5px; text-align: justify; font-size: 80%\">" . $desc['desc'] . "</div>"; 
							}
						}
						else $r .= "no description found";
						$r .= "</div>";
					}
					$r .= "</div>";
				}
				else $r .= "no topics found";
				$r .= "</li>";
			}
			$r .= "</ul>";
		}               
        $r .= "</div>";
        return $r;
}


function renderLinkedDatasetbySize(){
	global $VOID_SEEDS_URI;
	global $MAX_RANGE;
	$chartBaseURI ="http://chart.apis.google.com/chart?chs=500x600&chds=1,100,1,100&cht=s&chf=c,lg,90,006600,1,000000,0|bg,s,ffff,1,2&chm=o,f0f0f0,1,1.0,100.0&chf=c,lg,90,006600,1,000000,0|bg,s,ffffff&chd=t:20,30,40,50,60,70,80,90|20,30,40,50,60,70,80|";
	
    $datasets = getLinkedDatasetBySize();
    $r = "<div>";
    if($datasets != null) {
		foreach ($datasets['result']['rows'] as $dataset) {
			$datasetURI = $dataset['dataset'];         
			$numTriple = $dataset['numTriple'];
			$dMap[$datasetURI] = $numTriple;
		}
		arsort($dMap);
		$maxVal = max($dMap);
		foreach ($dMap as $ds => $num) {
			$r .= "$ds: $num (normalised:". ($num*$MAX_RANGE)/$maxVal  . ")<br />";
		}
		$r .= "<a href=\"" .  $chartBaseURI;
		foreach ($dMap as $ds => $num) {
			$r .= ($num*$MAX_RANGE)/$maxVal;
			$i++;
			if ($i < count($dMap)) $r .= "," ;
		}
		$r .= "\" >view</a>";
		
	}
	$r .= "</div>";
	return $r;
}

/* DBPEDIA */
function getDBpediaInfo($resource){
	global $store;
    global $DEBUG;
	global $DBPEDIA_GRAPH;
    
	if(!dbpediaInfoAvailable($resource)){ // not yet in store, try to fetch from DBpedia
    	$load = "LOAD <$resource> INTO <$DBPEDIA_GRAPH>"; 
		if($DEBUG) echo htmlentities($load) . "<br />";
		$store->query($load);
	}
	$q = "PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> PREFIX dp: <http://dbpedia.org/property/> SELECT DISTINCT * FROM <$DBPEDIA_GRAPH> { <$resource> rdfs:label ?label; dp:abstract ?desc . FILTER ( lang(?label) = \"en\" && lang(?desc) = \"en\" ) }";     
	if($DEBUG) echo htmlentities($q) . "<br />";
    $rs = $store->query($q);
    return $rs;
}

function dbpediaInfoAvailable($resource){
	global $store;
    global $DEBUG;
	global $DBPEDIA_GRAPH;        
    
	$q = "ASK WHERE { GRAPH <$DBPEDIA_GRAPH>  { <$resource> ?p ?o . } }";
    if($DEBUG) echo htmlentities($q) . "<br />";
    $rs = $store->query($q);
    return $rs['result'];
}


function lookupNameInDBpedia($name){
	global $DBPEDIA_GRAPH;     
	
	$resource = $DBPEDIA_GRAPH . "resource/" . $name;
	$r = "<p>Do you mean <a href=\"$resource\">$name</a>? <a href=\"javascript:useAsTopic('" . $resource . "')\" title=\"use as topic to search for datasets\">Yes!</a> <a href=\"index.html\">No ...</a></p>";    	
	$r .= "<div style=\"background-color: #f0f0f0; width: 40%; padding: 10px;\">";
	$topicDesc = getDBpediaInfo($resource);	
	if($topicDesc != null) {
		foreach ($topicDesc['result']['rows'] as $desc) {
			$r .= "<div style=\"border: 1px #c0c0c0 dotted; padding: 10px; margin-bottom: 5px; text-align: justify; font-size: 100%\">" . $desc['desc'] . "</div>"; 
		}
	}
	else $r .= "no description found";
	$r .= "</div>";
	
	return $r;
}


/* SINDICE */

// lookup a name in sindice, restrict to a certain domain (dbpedia.org)
// use, e.g., service.php?lookup=statistics
function lookupNameInSindice($name){
        global $store;
        global $SINDICE_GRAPH;
        global $DEBUG;
        $sindiceLookupURIRaw = "http://api.sindice.com/v2/search?q=". urlencode($name . " domain:dbpedia.org");
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
			$r .= "<p>Found the following topics (from sindice.com):</p><ul>";
			foreach ($rows['result']['rows'] as $row) {
				$link = $row['link'];    
				$label = $row['label']; 
				$r .= "<li><a href=\"$link\">$label</a> (<a href=\"javascript:useAsTopic('" . $link . "')\" title=\"use as topic to search for datasets\">use as topic</a>)";       
				/*
				$r .= "<div style=\"background-color: #f0f0f0; width: 40%; padding: 10px;\">";
				$topicDesc = getDBpediaInfo($link);	
				if($topicDesc != null) {
					foreach ($topicDesc['result']['rows'] as $desc) {
						$r .= "<div style=\"border: 1px #c0c0c0 dotted; padding: 10px; margin-bottom: 5px; text-align: justify; font-size: 80%\">" . $desc['desc'] . "</div>"; 
					}
				}
				else $r .= "no description found";
				$r .= "</div>";
				*/
				$r .= "</li>";       
          
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
        
        $q = "PREFIX s: <http://sindice.com/vocab/search#> PREFIX dcterms: <http://purl.org/dc/terms/> SELECT DISTINCT ?link ?label FROM <$sindiceNameURI> { ?res a s:Result ; s:link ?link ; dcterms:title ?label . }";
        if($DEBUG) echo htmlentities($q) . "<br />";
        $rs = $store->query($q);
        return $rs;
}




?>