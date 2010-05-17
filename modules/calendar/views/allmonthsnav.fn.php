<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//|	mini nav of all months
//--------------------------------------------------------------------------------------------------
//arg: year - year (yyyy) [string]

function calendar_allmonthsnav($args) {
	if (array_key_exists('year', $args) == false) { return false; }
	if (strlen($args['year']) != 4) { return false; }
	$c = new Calendar();
	
	$html = '';
	for ($i = 1; $i <= 12; $i++) { $html .= $c->drawMonth($i, $args['year'], 'small'); }
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

