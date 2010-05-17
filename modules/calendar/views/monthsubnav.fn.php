<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//|	subnav for month display
//--------------------------------------------------------------------------------------------------
//arg: year - year (yyyy) [string]
//arg: month - month (mm) 01 to 12 [string]

function calendar_monthsubnav($args) {
	global $serverPath;
	if (array_key_exists('month', $args) == false) { return false; }
	if (array_key_exists('year', $args) == false) { return false; }
	$year = sqlMarkup($args['year']);
	$month = sqlMarkup($args['month']);
	$html = '';
	
	$c = new Calendar();
	$labels = array();
	$prev = $c->getPrevMonth($month, $year);
	$labels['prevMonthUrl'] = $serverPath .'calendar/month_'. $prev['year'] .'_'. $prev['month'];
	$next = $c->getNextMonth($month, $year);
	$labels['nextMonthUrl'] = $serverPath .'calendar/month_'. $next['year'] .'_'. $next['month'];
	
	$labels['plus1'] = $c->drawMonth($next['month'], $next['year'], 'small');
	$next = $c->getNextMonth($next['month'], $next['year']);
	$labels['plus2'] = $c->drawMonth($next['month'], $next['year'], 'small');
	$next = $c->getNextMonth($next['month'], $next['year']);
	$labels['plus3'] = $c->drawMonth($next['month'], $next['year'], 'small');
	$next = $c->getNextMonth($next['month'], $next['year']);
	$labels['plus4'] = $c->drawMonth($next['month'], $next['year'], 'small');
	
	$html .= replaceLabels($labels, loadBlock('modules/calendar/views/monthsubnav.block.php'));
	return $html;
}


//--------------------------------------------------------------------------------------------------

?>

