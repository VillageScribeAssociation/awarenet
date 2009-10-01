<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//	make calendar for current month
//--------------------------------------------------------------------------------------------------

function calendar_thismonthnav($args) {
	$c = new Calendar();
	return $c->drawMonth(date('m'), date('Y'), 'small');
}

//--------------------------------------------------------------------------------------------------

?>