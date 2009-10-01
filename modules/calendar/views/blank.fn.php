<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//	display a blank calendar (testing)
//--------------------------------------------------------------------------------------------------
// * $args['month'] = show a day, month or year
// * $args['year'] = day, month or year to show
// * $args['size'] = large or small

function calendar_blank($args) {
	$month = '10'; $year = '2008'; $size = 'large';
	if (array_key_exists('month', $args)) { $month = $args['month']; }
	if (array_key_exists('year', $args)) { $month = $args['year']; }
	if (array_key_exists('size', $args)) { $size = $args['size']; }
	$c = new Calendar();
	$days = array();
	$daysInMonth = $c->daysInMonth($month, $year);	
	for($i = 1; $i <= $daysInMonth; $i++) {
		$days[$i] = array('bgcolor' => '#cccccc', 'label' => '0 entries');
	}
	return $c->drawMonthTable($month, $year, $days, $size);
}

//--------------------------------------------------------------------------------------------------

?>