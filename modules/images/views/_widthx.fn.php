<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a single image at a given size
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of an image record [string]
//opt: link - link to larger version (yes|no) [string]
//opt: size - scale type, default is width300 [string]
//opt: align - deprecated (left|right) [string]
//opt: caption - display caption, deprecated (yes|no) [string]
//opt: imageUID - overrides raUID [string]
//opt: pad - blank space to elave around image [string]
//TODO: discover if this is used by anything, remove if not

function images__widthx($args) {
	global $kapenta;
	$link = 'yes';	
	$caption = 'no'; 
	$size = 'width300'; 
	$html = ''; 
	$align = '';

	if (array_key_exists('imageUID', $args) == true) { $args['raUID'] = $args['imageUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('link', $args)) { $link = $args['link']; }
	if (array_key_exists('c', $args) == true) { $caption = $args['c']; }
	if (array_key_exists('caption', $args) == true) { $caption = $args['caption']; }
	if (array_key_exists('size', $args)) { $size = $args['size']; }
	if (array_key_exists('align', $args)) { $align = $args['align']; }	

	$linkUrl = '%%serverPath%%images/' . $args['raUID'];
	$imgUrl = '%%serverPath%%images/' . $size . '/' . $args['raUID'];

	$pad = '';
	if (array_key_exists('pad', $args) {
		$padBy = (int)$args['pad'];
		$pad = "vspace='" . $padBy . "px' hspace='" . $padby . "px'";
	}

	$html =  "<img src='" . $imgUrl . "' border='0' $pad />";	
	if ($link == 'yes') { $html = "<a href='" . $linkUrl . "'>$html</a>"; }

	if ($caption == 'yes') {
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


?>
