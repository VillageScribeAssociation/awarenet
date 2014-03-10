<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	count the number of images in a gallery
//--------------------------------------------------------------------------------------------------
//arg: galleryUID - UID of a gallery [string]

function gallery_imagecount($args) {
	global $kapenta;

	if (false == array_key_exists('galleryUID', $args)) { return ''; }

	$sql = "select count(UID) as numRecords from images_image "
		 . "where refModule='gallery' and refUID='" . $kapenta->db->addMarkup($args['galleryUID']) . "'";

	//TODO: use $kapenta->db->countRange

	$result = $kapenta->db->query($sql);
	$row = $kapenta->db->rmArray($kapenta->db->fetchAssoc($result));
	return $row['numRecords'];
}


//--------------------------------------------------------------------------------------------------

?>
