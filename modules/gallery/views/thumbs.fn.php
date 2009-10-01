<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	return thumbnails of gallery images
//--------------------------------------------------------------------------------------------------
// * $args['UID'] = UID of gallery (and not recordAlias)
// * $args['size'] = size to show thumbs (optional)
// * $args['num'] = maximum number of thumbs to show (most recent first) (optional)

function gallery_thumbs($args) {
	$limit = ''; $html = ''; $size = 'thumb';
	if (array_key_exists('UID', $args) == false) { return false; }
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }
	if (array_key_exists('num', $args) == true) { $limit = 'limit ' . $args['num']; }

	$sql = "select * from images "
		 . "where refUID='" . sqlMarkup($args['UID']) . "' and refModule='gallery' "
		 . "order by createdOn DESC $limit";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$viewUrl = '%%serverPath%%gallery/image/' . $row['recordAlias'];
		$thumbUrl = '%%serverPath%%images/' . $size . '/' . $row['recordAlias'];
		$html .= "<a href='" . $viewUrl . "'>"
			  . "<img src='" . $thumbUrl . "' title='" . $row['title'] . "' border='0' vspace='2px' hspace='2px' /></a>\n";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>