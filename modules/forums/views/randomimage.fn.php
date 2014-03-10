<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a random image from a forum  <--- TODO: pass this off to images module
//--------------------------------------------------------------------------------------------------
//arg: forumsUID - UID of a forums [string]
//opt: size - size of image (default is 'thumbsm') [string]

function forums_randomimage($args) {
		global $db;
		global $aliases;


	$size = 'thumbsm';
	if (false == array_key_exists('forumsUID', $args)) { return ''; }
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }

	$sql = "select * from images_image "
		 . "where refUID='" . $db->addMarkup($args['forumsUID']) . "' and refModule='forums' "
		 . "order by RAND() limit 0,1";

	$result = $db->query($sql);

	//TODO: get rid of this $aliases check, 
	while ($row = $db->rmArray($db->fetchAssoc($result))) {
		$imgUrl = '%%serverPath%%images/' . $size . '/' . $row['alias'];
		$forumsUrl = '%%serverPath%%forums/' . $aliases->getDefault('forums', $args['forumsUID']);
		$html .= "<a href='" . $forumsUrl . "'><img src='" . $imgUrl . "' border='0'></a>";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
