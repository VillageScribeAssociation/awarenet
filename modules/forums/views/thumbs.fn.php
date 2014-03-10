<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return thumbnails of forums images <-- NOT USED AS YET
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a forum (and not recordAlias) [string]
//opt: size - size to show thumbs (default is 'thumb')  [string]
//opt: num - maximum number of thumbs to show (most recent first) (default is no limit) [string]
//TODO: move this to the images module

function forums_thumbs($args) {
	global $kapenta;

	$limit = ''; $html = ''; $size = 'thumb';
	if (false == array_key_exists('UID', $args)) { return ''; }
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	if (true == array_key_exists('num', $args)) { $limit = 'limit ' . $args['num']; }

	$sql = "select * from images_image "
		 . "where refUID='" . $kapenta->db->addMarkup($args['UID']) . "' and refModule='forums' "
		 . "order by createdOn DESC $limit";

	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$viewUrl = '%%serverPath%%forums/image/' . $row['alias'];
		$thumbUrl = '%%serverPath%%images/' . $size . '/' . $row['alias'];
		$html .= "<a href='" . $viewUrl . "'>"
			  . "<img src='" . $thumbUrl . "' title='" . $row['title'] . "'"
			  . " border='0' vspace='2px' hspace='2px' /></a>\n";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
