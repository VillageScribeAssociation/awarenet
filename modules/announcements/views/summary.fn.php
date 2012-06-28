<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------
//args: raUID - alias or UID of an Announcements_Announcement object [string]
//opt: UID - overrides raUID [string]

function announcements_summary($args) {
	global $theme, $user, $page, $session;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Announcements_Announcement($args['raUID']);
	if (false == $model->loaded) { return '(announcement not found)'; }
	if (false == $user->authHas('announcements', 'announcements_announcement', 'show', $model->UID))
		{ return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();
	$labels['rawblock64'] = base64_encode($args['rawblock']);
	$block = $theme->loadBlock('modules/announcements/views/summary.block.php');
	
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
