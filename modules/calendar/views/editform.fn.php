<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//	show the edit form
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or calendar entry

function calendar_editform($args) {
	if (authHas('calendar', 'edit', '') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$c = new Calendar($args['raUID']);
	return replaceLabels($c->extArray(), loadBlock('modules/calendar/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>