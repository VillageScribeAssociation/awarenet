<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a random image from a gallery
//--------------------------------------------------------------------------------------------------
//arg: galleryUID - UID of a gallery [string]
//opt: size  - size of image [string]
//TODO: move this to the images module

function gallery_randomimage($args) {
	global $db, $user, $aliases;
	$size = 'thumbsm';		//%	default size of thumbnail [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('galleryUID', $args)) { return ''; }
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	$model = new Gallery_Gallery($args['galleryUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions check on gallery object

	//----------------------------------------------------------------------------------------------
	//	load a random image from the databse
	//----------------------------------------------------------------------------------------------
	$sql = "select * from images_image "
		 . "where refUID='" . $db->addMarkup($args['galleryUID']) . "' and refModule='gallery' "
		 . "order by RAND() limit 0,1";

	$result = $db->query($sql);

	while ($row = $db->rmArray($db->fetchAssoc($result))) {
		$imgUrl = '%%serverPath%%images/' . $size . '/' . $row['alias'];
		$galleryUrl = '%%serverPath%%gallery/' . $model->alias;
		$html .= "<a href='" . $galleryUrl . "'><img src='" . $imgUrl . "' border='0'></a>";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
