<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return thumbnails of gallery images
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of gallery (and not recordAlias) [string]
//opt: size - size to show thumbs (default is 'thumb') [string]
//opt: num - maximum number of thumbs to show (most recent first) (default is no limit) [string]

function gallery_thumbs($args) {
	global $db;
	$limit = '';
	$html = '';
	$size = 'thumb';

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (array_key_exists('UID', $args) == false) { return false; }
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }
	if (array_key_exists('num', $args) == true) { $limit = (int)$args['num']; }

	//---------------------------------------------------------------------------------------------
	//	load images
	//---------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='gallery'";
	$conditions[] = "refUID='" . $db->addMarkup($args['UID']) . "'";

	$range = $db->loadRange('images_image', '*', $conditions, 'weight ASC', $limit, '');

	foreach($range as $row) {
		$viewUrl = '%%serverPath%%gallery/image/' . $row['alias'];
		//$thumbUrl = '%%serverPath%%images/' . $size . '/' . $row['UID'];
		//	  . "<img src='" . $thumbUrl . "' title='" . $row['title'] . "' border='0' vspace='2px' hspace='2px' /></a>\n";
		$html .= "<a href='" . $viewUrl . "'>"
			   . "[[:images::$size::imageUID=" . $row['UID'] . "::pad=2::link=no:]]"
			   . "</a>\n";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

