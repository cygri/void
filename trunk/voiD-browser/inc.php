<?php

define('MORIARTY_ARC_DIR', 'arc/');
define('VOID', 'http://rdfs.org/ns/void#');
define('voiD_Store_Uri', 'http://api.talis.com/stores/kwijibo-dev3');
require 'moriarty/moriarty.inc.php';
require_once 'moriarty/simplegraph.class.php';
require 'moriarty/store.class.php';
require 'template-function.php';
$VOID = 'http://rdfs.org/ns/void#';
$store = new Store(voiD_Store_Uri);
?>