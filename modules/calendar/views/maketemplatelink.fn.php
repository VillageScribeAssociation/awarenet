<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	link to create a calendar entry from a template
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID or alias of a Calendar_Entry object [string]
//opt: entryUID - overrides raUID if present
//opt: UID - overrides raUID if present

function calendar_maketemplatelink($args) {
	global $user;
	$html = '';
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permisisons
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('entryUID', $args)) { $args['raUID'] = $args['entryUID']; }
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }

	$model = new Calendar_Entry($args['raUID']);
	if (false == $model->loaded) { return '(no such calendar entry)'; }
	if (false == $user->authHas('calendar', 'calendar_template', 'new', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the link
	//----------------------------------------------------------------------------------------------
	$url = "%%serverPath%%calendar/maketemplate/" . $model->alias;
	$html = "<a href='$url'>[ make a template from this entry &gt;&gt; ]</a>";

	return $html;
}

?>
