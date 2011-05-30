<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	small summary, formatted for nav (300px or so wide)
//--------------------------------------------------------------------------------------------------
//args: raUID - alias or UID of an Announcements_Announcement object [string]
//opt: UID - overrides raUID [string]

function announcements_summarynav($args) {
	global $theme, $user;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Announcements_Announcement($args['raUID']);
	if (false == $user->authHas('announcements', 'announcements_announcement', 'show', $model->UID))
		{ return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();
	$labels['rawblock64'] = base64_encode($args['rawblock']);
	$block = $theme->loadBlock('modules/announcements/views/summarynav.block.php');

	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
