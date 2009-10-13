<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	display gallery navigation (TODO: make more efficient, only load the records we need, by weight)
//--------------------------------------------------------------------------------------------------
// * $args['galleryUID'] = UID of a gallery (required)
// * $args['imageUID'] = UID of current image (required)
// * $args['size'] = optional

function gallery_gallerynav($args) {
	$size = 'thumbsm';
	if (array_key_exists('galleryUID', $args) == false) { return false; }
	if (array_key_exists('imageUID', $args) == false) { return false; }
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	load images js into array
	//----------------------------------------------------------------------------------------------

	$sql = "select UID, title, weight, recordAlias from images "
		 . "where refUID='" . sqlMarkup($args['galleryUID']) . "' and refModule='gallery' "
		 . "order by weight ";

	$result = dbQuery($sql);
	$images = array();
	$idx = 0;
	$currIdx = 0;

	$js .= "var galThumbs = new Array();\n";

	while ($row = sqlRMArray(dbFetchAssoc($result))) {
		$row = sqlRMArray($row);

		if ($row['UID'] == $args['imageUID']) { $currIdx = $idx; }

		$jsUID = jsMarkup($row['UID']);
		$jsTitle = jsMarkup($row['title']);
		$jsRA = jsMarkup($row['recordAlias']);

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
