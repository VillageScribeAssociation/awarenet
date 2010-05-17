<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	count the number of images in a gallery
//--------------------------------------------------------------------------------------------------
//arg: galleryUID - UID of a gallery [string]

function gallery_imagecount($args) {
	if (array_key_exists('galleryUID', $args) == false) { return false; }

	$sql = "select count(UID) as numRecords from images "
		 . "where refModule='gallery' and refUID='" . sqlMarkup($args['galleryUID']) . "'";

	$result = dbQuery($sql);
	$row = sqlRMArray(dbFetchAssoc($result));
	return $row['numRecords'];
}


//--------------------------------------------------------------------------------------------------

?>

