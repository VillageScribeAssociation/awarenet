<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	display a random image from a gallery
//--------------------------------------------------------------------------------------------------
// * $args['galleryUID'] = UID of a gallery (required)
// * $args['size'] = size of image (optional)

function gallery_randomimage($args) {
	$size = 'thumbsm';
	if (array_key_exists('galleryUID', $args) == false) { return false; }
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }

	$sql = "select * from images "
		 . "where refUID='" . sqlMarkup($args['galleryUID']) . "' and refModule='gallery' "
		 . "order by RAND() limit 0,1";

	$result = dbQuery($sql);

	while ($row = sqlRMArray(dbFetchAssoc($result))) {
		$imgUrl = '%%serverPath%%images/' . $size . '/' . $row['recordAlias'];
		$galleryUrl = '%%serverPath%%gallery/' . raGetDefault('gallery', $args['galleryUID']);
		$html .= "<a href='" . $galleryUrl . "'><img src='" . $imgUrl . "' border='0'></a>";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>