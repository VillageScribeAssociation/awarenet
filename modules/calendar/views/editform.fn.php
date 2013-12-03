<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the edit form
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or calendar entry [string]

function calendar_editform($args) {
	global $theme, $user, $utils;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Calendar_Entry($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('calendar', 'calendar_entry', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$ext['content64'] = $utils->b64wrap($ext['content']);
	$block = $theme->loadBlock('modules/calendar/views/editform.block.php');
	$html = $theme->replaceLabels($ext, $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------
?>
