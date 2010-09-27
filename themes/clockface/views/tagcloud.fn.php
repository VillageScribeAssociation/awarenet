<?

//--------------------------------------------------------------------------------------------------
//*	make tag cloud
//--------------------------------------------------------------------------------------------------
//	data is a base64_encoded, serialised array of [weight][link][label] triplets
//TODO: make this compatable with block system

function theme_tagcloud($args) {
	if (false == array_key_exists('data', $args)) { return '(no tag data)'; }
	global $serverPath;
	$html = '';

	$data = unserialize(base64_decode($args['data']));

	$maxWeight = 1;
	$minWeight = 0;

	foreach($data as $UID => $triple) {	// add min for negative values?
		if ($triple['weight'] > $maxWeight) { $maxWeight = $triple['weight']; }
	}

	foreach($data as $UID => $triple) {
		$size = floor((5 / $maxWeight) * $triple['weight']);
		$html .= "<a href='" . $serverPath . $triple['link'] . "' style='color: #444444;'>"
			 	. "<font size='" . $size . "'>" . $triple['label'] . "</font></a>\n";
	}

	return $html;
}

?>
