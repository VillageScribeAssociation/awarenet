<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make calendar for current month
//--------------------------------------------------------------------------------------------------

function calendar_thismonthnav($args) {
	$c = new Calendar_Entry();
	return $c->drawMonth(date('m'), date('Y'), 'small');
}

//--------------------------------------------------------------------------------------------------

?>
