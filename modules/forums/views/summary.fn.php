<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//	summarise
//--------------------------------------------------------------------------------------------------
// * $args['forumUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID of forums page

function forums_summary($args) {
	if (array_key_exists('forumUID', $args)) { $args['forumUID'] = $args['pageUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Forum(sqlMarkup($args['raUID']));	
	return replaceLabels($model->extArray(), loadBlock('modules/forums/views/summary.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>