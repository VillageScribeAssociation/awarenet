<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
// * $args['forumUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID of forums page

function forums_summarynav($args) {
	if (array_key_exists('forumUID', $args)) { $args['raUID'] = $args['forumUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Forum($args['raUID']);	
	$labels = $model->extArray();
	return replaceLabels($labels, loadBlock('modules/forums/views/summarynav.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>