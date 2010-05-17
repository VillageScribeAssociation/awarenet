<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a random image from a forum  <--- NOT USED YET
//--------------------------------------------------------------------------------------------------
//arg: forumsUID - UID of a forums [string]
//opt: size - size of image (default is 'thumbsm') [string]

function forums_randomimage($args) {
	$size = 'thumbsm';
	if (array_key_exists('forumsUID', $args) == false) { return false; }
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }

	$sql = "select * from images "
		 . "where refUID='" . sqlMarkup($args['forumsUID']) . "' and refModule='forums' "
		 . "order by RAND() limit 0,1";

	$result = dbQuery($sql);

	while ($row = sqlRMArray(dbFetchAssoc($result))) {
		$imgUrl = '%%serverPath%%images/' . $size . '/' . $row['recordAlias'];
		$forumsUrl = '%%serverPath%%forums/' . raGetDefault('forums', $args['forumsUID']);
		$html .= "<a href='" . $forumsUrl . "'><img src='" . $imgUrl . "' border='0'></a>";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

