<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	subnav for month display
//--------------------------------------------------------------------------------------------------
//arg: year - year (yyyy) [string]
//arg: month - month (mm) 01 to 12 [string]

function calendar_monthsubnav($args) {
		global $db;
		global $theme;

	if (array_key_exists('month', $args) == false) { return false; }
	if (array_key_exists('year', $args) == false) { return false; }
	$year = $db->addMarkup($args['year']);
	$month = $db->addMarkup($args['month']);
	$html = '';
	
	$model = new Calendar_Entry();
	$labels = array();
	$prev = $model->getPrevMonth($month, $year);
	$labels['prevMonthUrl'] = '%%serverPath%%calendar/month_'. $prev['year'] .'_'. $prev['month'];
	$next = $model->getNextMonth($month, $year);
	$labels['nextMonthUrl'] = '%%serverPath%%calendar/month_'. $next['year'] .'_'. $next['month'];
	
	$labels['plus1'] = $model->drawMonth($next['month'], $next['year'], 'small');
	$next = $model->getNextMonth($next['month'], $next['year']);
	$labels['plus2'] = $model->drawMonth($next['month'], $next['year'], 'small');
	$next = $model->getNextMonth($next['month'], $next['year']);
	$labels['plus3'] = $model->drawMonth($next['month'], $next['year'], 'small');
	$next = $model->getNextMonth($next['month'], $next['year']);
	$labels['plus4'] = $model->drawMonth($next['month'], $next['year'], 'small');
	
	$block = $theme->loadBlock('modules/calendar/views/monthsubnav.block.php');
	$html .= $theme->replaceLabels($labels, $block);
	return $html;
}


//--------------------------------------------------------------------------------------------------

?>

