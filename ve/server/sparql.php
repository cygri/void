<?php

include_once("C:/Program Files/Apache Software Foundation/Apache2.2/htdocs/arc2/ARC2.php");

$config = array(
  'db_host' => 'localhost',
	'db_name' => 'arcdb',
	'db_user' => 'arc',
	'db_pwd' => '',
	'store_name' => 've',
	'sem_html_formats' => 'adr-foaf dc erdf hcard-foaf openid rdfa rel-tag-skos xfn',
  /* endpoint */
  'endpoint_features' => array( 'select', 'ask', 'load'),
  'endpoint_timeout' => 60, /* not implemented in ARC2 preview */
  'endpoint_max_limit' => 1000, /* optional */
);

/* instantiation */
$ep = ARC2::getStoreEndpoint($config);

/* request handling */
$ep->go();