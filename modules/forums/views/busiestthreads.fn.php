<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list busiest threads on all forums (formatted for nav)
//--------------------------------------------------------------------------------------------------
//opt: num - number of threads to show (default is 10) [string]

function forums_busiestthreads($args) {
	global $db, $theme, $user;
	$num = 6;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	// TODO: permissions check here

	// thread with the smallest ammount of time between replies is the winner
	$sql = "select *, ((now() - TIMESTAMP(createdOn)) / replies) as score from forums_thread "
		 . "where replies > 0 order by score";

	$block = $theme->loadBlock('modules/forums/views/threadsummarynav.block.php');

	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		if ($num > 0) {		
			$thisThread = new Forums_Thread();
			$thisThread->loadArray($db->rmArray($row));
			$html .= $theme->replaceLabels($thisThread->extArray(), $block);
			$num--;

		} else { break; }
	}

	return $html;
}



?>
