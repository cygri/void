<?php

include_once("../arc/ARC2.php");

$config = array(
  'db_name' => 'arc2',
  'db_user' => 'root',
  'db_pwd' => 'root',
  'store_name' => 'vox',
  'endpoint_features' => array( 'select', 'ask', 'construct', 'describe')
);

/* instantiation */
$ep = ARC2::getStoreEndpoint($config);

/* request handling */
$ep->go();