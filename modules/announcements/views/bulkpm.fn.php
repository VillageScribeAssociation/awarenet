<?php

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show form to send an announcement as a PM to all members fo a group or project
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an Announcements_Announcement object [string]

function announcements_bulkpm($args) {
	global $user;
	global $theme;

	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }

	$model = new Announcements_Announcement($args['UID']);
	if (false == $model->loaded) { return '(Announcement not found)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/announcements/views/bulkpm.block.php');

	$html = $theme->replaceLabels($args, $block);

	return $html;
}

?>
