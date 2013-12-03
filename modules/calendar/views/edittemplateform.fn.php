<?

	require_once($kapenta->installPath . 'modules/calendar/models/template.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a Template object
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Calendar_Template object [string]
//opt: UID - UID of a Calendar_Template object, overrides raUID [string]
//opt: templateUID - UID of a Calendar_Template object, overrides raUID [string]

function calendar_edittemplateform($args) {
	global $user;
	global $theme;
	global $utils;

	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('templateUID', $args)) { $raUID = $args['templateUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Calendar_Template($raUID);	//% the object we're editing [object:Calendar_Template]

	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('calendar', 'calendar_template', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/calendar/views/edittemplateform.block.php');
	$labels = $model->extArray();
	$labels['content64'] = $utils->b64wrap($labels['content']);
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
