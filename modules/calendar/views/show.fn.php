<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or calendar entry [string]

function calendar_show($args) {
	global $theme;

	if (array_key_exists('raUID', $args) == false) { return false; }
	$c = new Calendar_Entry($args['raUID']);
	return $theme->replaceLabels($c->extArray(), $theme->loadBlock('modules/calendar/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>