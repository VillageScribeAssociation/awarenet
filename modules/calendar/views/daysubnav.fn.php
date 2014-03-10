<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	subnav for day display
//--------------------------------------------------------------------------------------------------
//arg: year - year (yyyy) [string]
//arg: month - day (mm) 01 to 12 [string]
//arg: day - day (dd) 01 to 31 [string]

function calendar_daysubnav($args) {
		global $kapenta;
		global $theme;

	if (array_key_exists('day', $args) == false) { return false; }
	if (array_key_exists('month', $args) == false) { return false; }
	if (array_key_exists('year', $args) == false) { return false; }
	$year = $kapenta->db->addMarkup($args['year']);
	$month = $kapenta->db->addMarkup($args['month']);
	$day = $kapenta->db->addMarkup($args['day']);
	$html = '';
	
	$model = new Calendar_Entry();
	$labels = array();
	$prev = $model->getPrevDay($day, $month, $year);
	$labels['prevDayUrl'] = '%%serverPath%%calendar/day_' . $prev['year'] . '_' 
							. $prev['month'] . '_' . $prev['day'];
	$next = $model->getNextDay($day, $month, $year);
	$labels['nextDayUrl'] = '%%serverPath%%calendar/day_' . $next['year'] . '_' 
							. $next['month'] . '_' . $next['day'];
	
	$labels['plus1'] = $model->drawMonth($next['month'], $next['year'], 'small');
	$next = $model->getNextMonth($next['month'], $next['year']);
	$labels['plus2'] = $model->drawMonth($next['month'], $next['year'], 'small');
	$next = $model->getNextMonth($next['month'], $next['year']);
	$labels['plus3'] = $model->drawMonth($next['month'], $next['year'], 'small');
	$next = $model->getNextMonth($next['month'], $next['year']);
	$labels['plus4'] = $model->drawMonth($next['month'], $next['year'], 'small');
	
	$html .= $theme->replaceLabels($labels, $theme->loadBlock('modules/calendar/views/daysubnav.block.php'));
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

