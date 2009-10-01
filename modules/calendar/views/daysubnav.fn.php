<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//	subnav for day display
//--------------------------------------------------------------------------------------------------
// * $args['year'] = year (yyyy)
// * $args['month'] = month (mm) 01 to 12
// * $args['day'] = day (dd) 01 to 31

function calendar_daysubnav($args) {
	global $serverPath;
	if (array_key_exists('day', $args) == false) { return false; }
	if (array_key_exists('month', $args) == false) { return false; }
	if (array_key_exists('year', $args) == false) { return false; }
	$year = sqlMarkup($args['year']);
	$month = sqlMarkup($args['month']);
	$day = sqlMarkup($args['day']);
	$html = '';
	
	$c = new Calendar();
	$labels = array();
	$prev = $c->getPrevDay($day, $month, $year);
	$labels['prevDayUrl'] = $serverPath . 'calendar/day_' . $prev['year'] . '_' 
							. $prev['month'] . '_' . $prev['day'];
	$next = $c->getNextDay($day, $month, $year);
	$labels['nextDayUrl'] = $serverPath . 'calendar/day_' . $next['year'] . '_' 
							. $next['month'] . '_' . $next['day'];
	
	$labels['plus1'] = $c->drawMonth($next['month'], $next['year'], 'small');
	$next = $c->getNextMonth($next['month'], $next['year']);
	$labels['plus2'] = $c->drawMonth($next['month'], $next['year'], 'small');
	$next = $c->getNextMonth($next['month'], $next['year']);
	$labels['plus3'] = $c->drawMonth($next['month'], $next['year'], 'small');
	$next = $c->getNextMonth($next['month'], $next['year']);
	$labels['plus4'] = $c->drawMonth($next['month'], $next['year'], 'small');
	
	$html .= replaceLabels($labels, loadBlock('modules/calendar/views/daysubnav.block.php'));
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>