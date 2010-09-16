<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return thumbnails of random forums images <---- NOT USED AS YET
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of user (and not recordAlias) [string]
//opt: size - size to show thumbs (default is 'thumbsm') [string]
//opt: num - maximum number of thumbs to show (most recent first) (default is no limit) [string]
//TODO: move this to the images module

function forums_randomthumbs($args) {
	global $db;

	$limit = '';
	$size = 'thumbsm';
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return ''; }
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	if (true == array_key_exists('num', $args)) { $limit = 'limit ' . (int)$args['num']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$sql = "select * from Images_Image "
		 . "where createdBy='" . $db->addMarkup($args['userUID']) . "' and refModule='forums' "
		 . "order by RAND() $limit";

	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$viewUrl = '%%serverPath%%forums/image/' . $row['alias'];
		$thumbUrl = '%%serverPath%%images/' . $size . '/' . $row['alias'];
		$html .= "<a href='" . $viewUrl . "'>"
			  . "<img src='" . $thumbUrl . "' title='" . $row['title']
			  . "' border='0' vspace='2px' hspace='2px' /></a>\n";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
