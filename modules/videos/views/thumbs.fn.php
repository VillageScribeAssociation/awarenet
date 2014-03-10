<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return thumbnails of videos in a given gallery
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Videos_Gallery object (and not alias) [string]
//opt: size - size to show thumbs (default is 'thumb') [string]
//opt: num - maximum number of thumbs to show (most recent first) (default is no limit) [string]

function videos_thumbs($args) {
	global $kapenta;
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
	$conditions[] = "refModule='videos'";
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($args['UID']) . "'";

	$range = $kapenta->db->loadRange('videos_video', '*', $conditions, 'weight ASC', $limit, '');

	foreach($range as $item) {
		$viewUrl = '%%serverPath%%videos/play/' . $item['alias'];

		$thumbUrl = ''
		 . '%%serverPath%%images/showdefault'
		 . '/refModule_videos'
		 . '/refModel_videos_video'
		 . '/refUID_' . $item['UID']
		 . '/size_' . $size . '/';

		$html .= ''
		 . "<a href='" . $viewUrl . "'>"
	 	 . "<img"
		 . " src='" . $thumbUrl . "'"
		 . " border='0'"
		 . " width='100'"
		 . " height='100'"
		 . " class='rounded'"
		 . " style='background-color: #aaaaaa; display: inline;'"
		 . " />"
		 . "</a>\n";
		

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

