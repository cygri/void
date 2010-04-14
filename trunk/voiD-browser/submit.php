<?php
include 'inc.php';
if(isset($_POST['url']) AND $Url = trim($_POST['url']) ){
	if(preg_match('@^http://.*$@', $Url)){
		$Graph = new SimpleGraph();
		$rdf = @file_get_contents($Url);
		$Graph->add_rdf($rdf);
		$res = $store->get_metabox()->submit_rdfxml($Graph->to_rdfxml());
		if($res->is_success()){
			$message = 'voiD Data saved';
		} else {
			$message = 'Not Saved, Sorry: '.$res->body;
		}
	} else {
		$message = 'Please enter a valid HTTP url';
	}
}
$filename = 'submit.html';

include 'templates/template.html';
?>