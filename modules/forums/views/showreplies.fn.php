<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a thread and paginated replies
//--------------------------------------------------------------------------------------------------
//arg: threadUID - UID of a forum thread [string]
//opt: pageno - page to display (default is 1) [string]

function forums_showreplies($args) {
	$pageno = 1; $num = 5; $html = '';
	if (array_key_exists('threadUID', $args) == false) { return false; }
	if (array_key_exists('pageno', $args) == true) { $pageno = $args['pageno']; }	
	$tUID = sqlMarkup($args['threadUID']);

	//----------------------------------------------------------------------------------------------
	//	load thread model
	//----------------------------------------------------------------------------------------------
	$model = new ForumThread($tUID);

	//----------------------------------------------------------------------------------------------
	//	count all replies in this thread
	//----------------------------------------------------------------------------------------------
	$sql = "select count(UID) as numRecords from forumreplies where thread='" . $tUID . "'";
	$result = dbQuery($sql);
	$row = sqlRMArray(dbFetchAssoc($result));
	$total = ceil($row['numRecords'] / $num);

	//----------------------------------------------------------------------------------------------
	//	show the current page
	//----------------------------------------------------------------------------------------------
	$limit = "limit " . (($pageno - 1) * $num) . ", ". sqlMarkup($num);
	$sql = "select * from forumreplies "
		 . "where thread='" . $tUID . "' "
		 . "order by createdOn ASC " . $limit;	

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$reply = new ForumReply();
		$reply->loadArray($row);
		$ext = $reply->extArray();
		$ext['threadTitle'] = $model->data['title'];

		$html .= replaceLabels($ext, loadBlock('modules/forums/views/reply.block.php'));
	}

	$link = '%%serverPath%%forums/showthread/' . $model->data['recordAlias'] . '/';

	$pagination .= "[[:theme::pagination::page=" . sqlMarkup($page) 
				. "::total=" . $total . "::link=" . $link . ":]]\n";

	if (0 == $total) { $html = "(no replies yet)";	}

	return $pagination . $html . $pagination;

}

//--------------------------------------------------------------------------------------------------

?>

