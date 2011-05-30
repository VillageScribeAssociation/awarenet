<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/inc/images__widthx.inc.php');

//--------------------------------------------------------------------------------------------------
//|	display a single image 560px wide
//--------------------------------------------------------------------------------------------------
//arg: raUID - record alias or UID [string]
//opt: imageUID - overrides raUID [string]
//opt: link - link to larger version (yes|no) [string]

function images_widtheditor($args) { 
	global $kapenta;
	$link = 'yes';	$caption = 'no'; $size = 'width300'; $html = ''; $align = '';
	if (array_key_exists('imageUID', $args) == true) { $args['raUID'] = $args['imageUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('c', $args) == true) { $caption = $args['c']; }
	if (array_key_exists('caption', $args) == true) { $caption = $args['caption']; }
	if (array_key_exists('size', $args)) { $size = $args['size']; }
	if (array_key_exists('align', $args)) { $align = $args['align']; }
	
	$linkUrl = '%%serverPath%%images/' . $args['raUID'];
	$imgUrl = '%%serverPath%%images/' . $size . '/' . $args['raUID'];

	$html =  "<img src='" . $imgUrl . "' border='0' />";	
	if ($link == 'yes') { $html = "<a href='" . $linkUrl . "'>$html</a>"; }

	if ($caption == 'yes') {
		//echo "image rauid: " . $args['raUID'] . "<br/>\n";
		$model = new Images_Image($args['raUID']);
		if (strtolower($align) == 'left') { $align = "style='float: left;'"; }
		if (strtolower($align) == 'right') { $align = "style='float: right;'"; }

		$html = "<div class='caption' $align>$html<br/><small>"
			  . $model->caption . "</small></div>";
		if ($size == 'width300') 
			{ $html = str_replace("class='caption'", "class='captionpad'", $html); }
	}
	return $html;

}

//--------------------------------------------------------------------------------------------------

?>

