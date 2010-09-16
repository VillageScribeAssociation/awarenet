<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a mini calendar of events in the same month
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or calendar entry [string]

function calendar_samemonthnav($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$c = new Calendar_Entry($args['raUID']);
	return $c->drawMonth($c->month, $c->year, 'small');
}

//--------------------------------------------------------------------------------------------------

?>

