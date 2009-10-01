<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//	return thumbnails of random forums images <---- NOT USED AS YET
//--------------------------------------------------------------------------------------------------
// * $args['userUID'] = UID of user (and not recordAlias)
// * $args['size'] = size to show thumbs (optional)
// * $args['num'] = maximum number of thumbs to show (most recent first) (optional)

function forums_randomthumbs($args) {
	$limit = ''; $html = ''; $size = 'thumbsm';
	if (array_key_exists('userUID', $args) == false) { return false; }
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }
	if (array_key_exists('num', $args) == true) { $limit = 'limit ' . $args['num']; }

	$sql = "select * from images "
		 . "where createdBy='" . sqlMarkup($args['userUID']) . "' and refModule='forums' "
		 . "order by RAND() $limit";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$viewUrl = '%%serverPath%%forums/image/' . $row['recordAlias'];
		$thumbUrl = '%%serverPath%%images/' . $size . '/' . $row['recordAlias'];
		$html .= "<a href='" . $viewUrl . "'>"
			  . "<img src='" . $thumbUrl . "' title='" . $row['title']
			  . "' border='0' vspace='2px' hspace='2px' /></a>\n";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>