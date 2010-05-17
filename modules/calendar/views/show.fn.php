<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or calendar entry [string]

function calendar_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$c = new Calendar($args['raUID']);
	return replaceLabels($c->extArray(), loadBlock('modules/calendar/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

