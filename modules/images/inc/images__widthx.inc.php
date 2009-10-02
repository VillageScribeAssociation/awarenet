<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	display a single image at a given size
//--------------------------------------------------------------------------------------------------
// * $args['imageUID'] = overrides raUID
// * $args['raUID'] = record alias or UID
// * $args['link'] = link to larger version (yes|no)
// * $args['size'] = scale type
// * $args['align'] = left|right
// * $args['caption'] = yes|no - display caption

function images__widthx($args) {
	global $serverPath;
	$link = 'yes';	$caption = 'no'; $size = 'width300'; $html = ''; $align = '';
	if (array_key_exists('imageUID', $args) == true) { $args['raUID'] = $args['imageUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('link', $args)) { $link = $args['link']; }
	if (array_key_exists('c', $args) == true) { $caption = $args['c']; }
	if (array_key_exists('caption', $args) == true) { $caption = $args['caption']; }
	if (array_key_exists('size', $args)) { $size = $args['size']; }
	if (array_key_exists('align', $args)) { $align = $args['align']; }
	
	$linkUrl = $serverPath . 'images/' . $args['raUID'];
	$imgUrl = $serverPath . 'images/' . $size . '/' . $args['raUID'];

	$html =  "<img src='" . $imgUrl . "' border='0' />";	
	if ($link == 'yes') { $html = "<a href='" . $linkUrl . "'>$html</a>"; }

	if ($caption == 'yes') {
		echo "image rauid: " . $args['raUID'] . "<br/>\n";
		$model = new Image($args['raUID']);
		if (strtolower($align) == 'left') { $align = "style='float: left;'"; }
		if (strtolower($align) == 'right') { $align = "style='float: right;'"; }

		$html = "<div class='caption' $align>$html<br/><small>"
			  . $model->data['caption'] . "</small></div>";
		if ($size == 'width300') 
			{ $html = str_replace("class='caption'", "class='captionpad'", $html); }
	}
	return $html;
}


?>
