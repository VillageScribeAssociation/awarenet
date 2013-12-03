<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	count the number of images in a gallery
//--------------------------------------------------------------------------------------------------
//arg: galleryUID - UID of a gallery [string]

function gallery_imagecount($args) {
	global $db;

	if (false == array_key_exists('galleryUID', $args)) { return ''; }

	$sql = "select count(UID) as numRecords from images_image "
		 . "where refModule='gallery' and refUID='" . $db->addMarkup($args['galleryUID']) . "'";

	//TODO: use $db->countRange

	$result = $db->query($sql);
	$row = $db->rmArray($db->fetchAssoc($result));
	return $row['numRecords'];
}


//--------------------------------------------------------------------------------------------------

?>
