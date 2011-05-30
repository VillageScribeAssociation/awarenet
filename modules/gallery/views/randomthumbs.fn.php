<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return thumbnails of random gallery images
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a user (and not recordAlias) [string]
//opt: size - size to show thumbs (optional) [string]
//opt: num - maximum number of thumbs to show (most recent first) (default is all images) [string]
//: note the direct use of images table - TODO: work around this

function gallery_randomthumbs($args) {
		global $db;
	$limit = ''; $html = ''; $size = 'thumbsm';

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (array_key_exists('userUID', $args) == false) { return false; }
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }
	if (array_key_exists('num', $args) == true) { $limit = (int)$args['num']; }

	//---------------------------------------------------------------------------------------------
	//	get random image UIDs
	//---------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "createdBy='" . $db->addMarkup($args['userUID']) . "'";
	$conditions[] = "refModule='gallery'";

	$range = $db->loadRange('images_image', '*', $conditions, 'RAND()', $limit, '');

	foreach($range as $row) {
		$viewUrl = '%%serverPath%%gallery/image/' . $row['alias'];
		$thumbUrl = '%%serverPath%%images/' . $size . '/' . $row['alias'];
		$html .= "<a href='" . $viewUrl . "'>"
			  . "<img src='" . $thumbUrl . "' title='" . $row['title']
			  . "' border='0' vspace='2px' hspace='2px' /></a>\n";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

