<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of post to edit [string]

function announcements_show($args) {
	global $theme, $user, $page;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Announcements_Announcement($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('announcements', 'announcements_announcement', 'show', $model->UID))
		{ return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();
	$labels['rawblock64'] = base64_encode($args['rawblock']);

	$block = $theme->loadBlock('modules/announcements/views/show.block.php');
	$html = $theme->replaceLabels($labels, $block);

	//----------------------------------------------------------------------------------------------
	//	set AJAX triggers
	//----------------------------------------------------------------------------------------------
	$channel = 'announcement-' . $model->UID;
	$page->setTrigger('announcements', $channel, $args['rawblock']);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
