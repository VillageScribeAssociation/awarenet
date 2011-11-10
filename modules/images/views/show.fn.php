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

function images_show($args) {
	global $kapenta;

	$link = 'yes';	
	$caption = 'no'; 
	$size = 'width300'; 
	$align = '';
	$pad = '';
	$html = ''; 				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('imageUID', $args)) { $args['raUID'] = $args['imageUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(UID not given)'; }
	if (true == array_key_exists('link', $args)) { $link = $args['link']; }
	if (true == array_key_exists('c', $args)) { $caption = $args['c']; }
	if (true == array_key_exists('caption', $args)) { $caption = $args['caption']; }
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	if (true == array_key_exists('align', $args)) { $align = $args['align']; }	

	if (true == array_key_exists('pad', $args)) {
		$pad = "vspace='" . (int)$args['pad'] . "px' hspace='" . (int)$args['pad'] . "px'";
	}

	$model = new Images_Image($args['raUID']);
	if (false == $model->loaded) { return '(image not found)'; }
	if (false == $model->transforms->presetExists($size)) { return '(no such preset)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$linkUrl = '%%serverPath%%images/' . $model->alias;
	$imgUrl = '%%serverPath%%images/s_' . $size . '/' . $model->alias;

	$html =  "<img src='" . $imgUrl . "' border='0' $pad />";	
	if ('yes' == $link) { $html = "<a href='" . $linkUrl . "'>$html</a>"; }

	if ('yes' == $caption) {
		if (strtolower($align) == 'left') { $align = "style='float: left;'"; }
		if (strtolower($align) == 'right') { $align = "style='float: right;'"; }

		$html = "<div class='caption' $align>$html<br/><small>"
			  . $model->caption . "</small></div>";

		//DEPRECATED: TODO: improve this feature
		if ('width300' == $size) {
			$html = str_replace("class='caption'", "class='captionpad'", $html);
		}
	}

	return $html;
}


?>
