<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a single image at a given size
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of an image record [string]
//opt: link - link to larger version (yes|no) [string]
//opt: onClick - contents of onClick handler, if any [string]
//opt: size - scale type, default is width300 [string]
//opt: align - deprecated (left|right) [string]
//opt: display - css display mode (inline|block) [string]
//opt: caption - display caption, deprecated (yes|no) [string]
//opt: imageUID - overrides raUID [string]

function images_show($args) {
	global $kapenta;
	global $kapenta;
	global $session;

	$display = 'block';							//%	css display mode (inline|block) [string]
	$link = 'yes';	
	$caption = 'no'; 
	$size = 'widthnav'; 
	$align = '';
	$style = '';								//%	additional, per image style [string]
	$profile = $session->get('deviceprofile');	//%	device context
	$html = ''; 								//%	return value [string]

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

	$model = new Images_Image($args['raUID']);
	if (false == $model->loaded) { return '(image not found)'; }
	if (false == $model->transforms->presetExists($size)) { return '(no such preset)'; }

	if (true == array_key_exists('display', $args)) { $display = $args['display']; }

	//----------------------------------------------------------------------------------------------
	//	scale down on mobile
	//----------------------------------------------------------------------------------------------

	//echo ''
	//	 . "area size: " . $args['area'] . ':=' . $model->transforms->getWidth($args['area'])
	//	 . " image size: $size := " . $model->transforms->getWidth($size) . "<br/>\n"; 

	if ($model->transforms->getWidth($args['area']) < $model->transforms->getWidth($size)) {
		if ('' !== $args['area']) { $size = $args['area']; }
	}



	if ('desktop' !== $profile) {
		switch($size) {
			case 'width570':		$size = $profile;			break;
			case 'width560':		$size = $profile;			break;
			case 'widthcontent':	$size = $profile;			break;
			case 'widthindent':		$size = $profile;			break;
			case 'slide':			$size = 'mobileslide';		break;
			case 'slideindent':		$size = 'mobileslide';		break;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$linkUrl = '%%serverPath%%images/' . $model->alias;
	$imgUrl = '%%serverPath%%images/s_' . $size . '/' . $model->alias;

	if ('inline' == $display) { $style = " style='display: inline;'"; }

	$html .= "<img src='" . $imgUrl . "' class='rounded' border='0' $style/>";

	if ('yes' == $link) { $html = "<a href='" . $linkUrl . "'>$html</a>"; }

	if ('yes' == $caption) {
		//(TODO)
		//if (strtolower($align) == 'left') { $align = "style='float: left;'"; }
		//if (strtolower($align) == 'right') { $align = "style='float: right;'"; }

		$title = $model->title;
		if (strlen($title) > 30) { $title = substr($title, 0, 30) . '...'; }

		$html = ''
		 . "<div class='imagesframe' >"
		 . "$html<br/>"
		 . "<small><b>" . $title . "</b> " . $model->caption . "</small></div>";

	}

	$kapenta->page->requireCss('%%serverPath%%modules/images/css/images.css');

	return $html;
}


?>
