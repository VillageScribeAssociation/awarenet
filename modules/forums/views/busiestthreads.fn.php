<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//	list busiest threads on all forums (formatted for nav)
//--------------------------------------------------------------------------------------------------
// * $args['num'] = number of threads to show, optional

function forums_busiestthreads($args) {
	$num = 10; $html = '';
	if (array_key_exists('num', $args) == true) { $num = floor($args['num']); }
	// TODO: auth	

	// thread with the smallest ammount of time between replies is the winner
	$sql = "select *, ((now() - TIMESTAMP(createdOn)) / replies) as score from forumthreads "
		 . "where replies > 0 order by score";

	$summaryBlock = loadBlock('modules/forums/views/threadsummarynav.block.php');

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		if ($num > 0) {		
			$thisThread = new ForumThread();
			$thisThread->loadArray(sqlRMArray($row));

			$html .= replaceLabels($thisThread->extArray(), $summaryBlock);

		} else { break; }
	}

	return $html;
}



?>