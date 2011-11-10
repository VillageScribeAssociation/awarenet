<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a single image at a given size
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of an Images_Image object [string]
//opt: imageUID - overrides raUID if present [string]
//opt: link - link to larger version, default is 'yes' (yes|no) [string]
//arg: size - scale type [string]
//arg: align - (left|right) float the image [string]
//arg: caption - display caption (yes|no) [string]

function images__widthx($args) {
	global $kapenta;
	$link = 'yes';
	$caption = 'no';			//%	display caption with image (yes|no) [string]
	$size = 'width300';			//%	must be one of the hardcoded sizes [string]
	$align = '';
	$pad = '';	
	$html = '';					//%	return value [string]

	//---------------------------------------------------------------------------------------------
	//	read and check arguments
	//---------------------------------------------------------------------------------------------
	if (true == array_key_exists('imageUID', $args)) { $args['raUID'] = $args['imageUID']; }
	if (true == array_key_exists('imgUID', $args)) { $args['raUID'] = $args['imgUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(image not specified)'; }
	if (true == array_key_exists('link', $args)) { $link = $args['link']; }
	if (true == array_key_exists('c', $args)) { $caption = $args['c']; }
	if (true == array_key_exists('caption', $args)) { $caption = $args['caption']; }
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	if (true == array_key_exists('align', $args)) { $align = $args['align']; }
	
	if (true == array_key_exists('pad', $args)) {
		$padBy = (int)$args['pad'];
		$pad = "vspace='" . $padBy . "px' hspace='" . $padBy . "px' ";
	}

	//---------------------------------------------------------------------------------------------
	//	make html
	//---------------------------------------------------------------------------------------------

	$linkUrl = '%%serverPath%%images/' . $args['raUID'];
	$imgUrl = '%%serverPath%%images/' . $size . '/' . $args['raUID'];

	$html =  "<img src='" . $imgUrl . "' border='0' $pad/>";	
	if ('yes' == $link) { $html = "<a href='" . $linkUrl . "'>" . $html . "</a>"; }

	if ('yes' == $caption) {
		$model = new Images_Image($args['raUID']);
		if (strtolower($align) == 'left') { $align = "style='float: left;'"; }
		if (strtolower($align) == 'right') { $align = "style='float: right;'"; }

		$html = "<div class='caption' $align>$html<br/><small>". $model->caption ."</small></div>";
		if ('width300' == $size) { 
			$html = str_replace("class='caption'", "class='captionpad'", $html);
		}
	}

	return $html;
}


?>
