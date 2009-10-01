<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//	show a mini calendar of events in the same month
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or calendar entry

function calendar_samemonthnav($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$c = new Calendar($args['raUID']);
	return $c->drawMonth($c->data['month'], $c->data['year'], 'small');
}

//--------------------------------------------------------------------------------------------------

?>