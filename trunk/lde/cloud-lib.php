<?php  
// source: http://www.bitrepository.com/web-programming/php/how-to-create-a-tag-cloud.html

function randomize_array($array) {
	$rand_items = array_rand($array, count($array));
	$new_array = array();
	foreach($rand_items as $value) {
		$new_array[$value] = $array[$value];
	}
	return $new_array;
}

function renderCloud($terms, $termsLinks, $doRandom, $doMultiWord) {
	// increasing this number will make the words bigger; decreasing will do reverse
	$factor = 0.5;
	// smallest font size possible
	$starting_font_size = 12;
	// tag separator
	$tag_separator = '&nbsp; &nbsp; &nbsp;';
	
	if($doRandom){
		$terms = randomize_array($terms);
	}
	
	$r = "<div align=\"center\" class=\"cloud-box\">";
	$max_count = array_sum($terms);
	foreach($terms as $term => $rating) {
		$x = round(($rating * 100) / $max_count) * $factor;
		$font_size = $starting_font_size + $x.'px';
		if($doMultiWord) {
			$r .= "<span style=\"font-size:$font_size;\">'<a class=\"tag\" href=\"" . $termsLinks[$term] . "\">$term</a>'</span>$tag_separator";
		}
		else {
			$r .= "<span style=\"font-size:$font_size;\"> <a class=\"tag\" href=\"" . $termsLinks[$term] . "\">$term</a></span>$tag_separator";
		}
	}
	$r .= "</div>";
	return $r;
}
?>
