<?php

if(isset($_GET['lookup'])){ 
	$term = $_GET['lookup']; 				
  echo file_get_contents("http://143.224.254.32/ve/service.php?lookup=" . $term); 
}

if(isset($_GET['find'])){ 
	$name = $_GET['find']; 				
  echo file_get_contents("http://143.224.254.32/ve/service.php?find=" . $name);
}

?>