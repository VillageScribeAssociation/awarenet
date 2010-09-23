<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	create form for editing an announcement record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of post to edit [string]

function announcements_editform($args) {
	global $theme, $user, $utils;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Announcements_Announcement($args['raUID']);
	if (false == $model->loaded) { return ''; }

	$refModule = $model->refModule;
	$refModel = $model->refModel;
	$refUID = $model->refUID;

	if (false == $user->authHas($refModule, $refModel, 'announcements-edit', $model->refUID))
		{ return ''; }

	//TODO: further auth permutations

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$ext['contentJs64'] = $utils->base64EncodeJs('contentJs64', $ext['content']);
	$block = $theme->loadBlock('modules/announcements/views/editform.block.php');
	$html = $theme->replaceLabels($ext, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
