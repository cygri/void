<?php
$urls = array(
	'/subjects' => 'subjects.php',
	'/vocabularies' => 'vocabs.php',
	'/datasetsBySubject' => 'datasetsBySubject.php',
	'/datasetsByVocab' => 'datasetsByVocab.php',
	'/datasetsByLicense' => 'datasetsByLicense.php',
	'/datasetsBy-(.*)\?'=> 'datasetsBy.php',
	'/dataset' => 'dataset.php',
	'/sparql' => 'sparql.php',
	'/submit' => 'submit.php',
	'/rights' => 'licenses.php',
	'/top-(.*)'=> 'top.php',
	'/(Describer)?' => 'describer.php',

);
foreach ($urls as $regex => $file) {
	if(preg_match('@'.$regex.'@', $_SERVER['REQUEST_URI'], $m)){
		if(!empty($m[1]))
		{
			 define('datasetsBy', $m[1]);
			$options = array(
				'creators' => 'http://purl.org/dc/terms/creator',
				'subjects' => 'http://purl.org/dc/terms/subjects',
				'vocabularies' => 'http://rdfs.org/ns/void#vocabulary',
				);

			@$datasetsByPredicate = $options[datasetsBy];
			
		}
		
		require $file;
		exit;
	}
}
?>