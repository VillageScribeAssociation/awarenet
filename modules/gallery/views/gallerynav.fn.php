<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display gallery navigation (TODO: make more efficient, only load the records we need, by weight)
//--------------------------------------------------------------------------------------------------
//arg: galleryUID - UID of a gallery [string]
//arg: imageUID - UID of current image [string]
//opt: size - image size (thumb90, thumbsm, width300, etc) [string]

function gallery_gallerynav($args) {
	global $db, $user, $utils;
	$html = '';					//%	return value [string]
	$size = 'thumbsm';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permission
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('galleryUID', $args)) { return ''; }
	if (false == array_key_exists('imageUID', $args)) { return ''; }
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }

	//----------------------------------------------------------------------------------------------
	//	load images js into array
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refUID='" . $db->addMarkup($args['galleryUID']) . "'";
	$conditions[] = "refModule='gallery'";

	$range = $db->loadRange('images_image', '*', $conditions, 'weight');

	//$sql = "select UID, title, weight, recordAlias from Images_Image "
	//	 . "where refUID='" . $db->addMarkup($args['galleryUID']) . "' and refModule='gallery' "
	//	 . "order by weight ";

	$images = array();
	$idx = 0;
	$currIdx = 0;

	$js = "var galThumbs = new Array();\n";

	foreach ($range as $row) {
		if ($row['UID'] == $args['imageUID']) { $currIdx = $idx; }
		$jsUID = $utils->jsMarkup($row['UID']);
		$jsTitle = $utils->jsMarkup($row['title']);
		$jsRA = $utils->jsMarkup($row['alias']);
		$js .= "galThumbs[$idx] = new Array('". $jsUID ."','". $jsTitle ."', '" . $jsRA . "');\n";
		$idx++;
	}

	$js .= "var galleryNavCurrIdx = " . $currIdx . ";\n";

	$html .= "<script src='%%serverPath%%modules/gallery/js/gallerynav.js'></script>\n";
	$html .= "<div id='galleryNavJs'><span class='ajaxmsg'>Loading...</span></div>";
	$html .= "<script>\n" . $js . "</script>\n";

	//----------------------------------------------------------------------------------------------
	//	display
	//----------------------------------------------------------------------------------------------

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
