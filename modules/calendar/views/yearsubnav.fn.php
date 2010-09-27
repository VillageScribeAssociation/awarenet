<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	subnav for day display
//--------------------------------------------------------------------------------------------------
//arg: year - year (yyyy) [string]

function calendar_yearsubnav($args) {
	global $db;

	global $theme;

	global $serverPath;
	if (array_key_exists('year', $args) == false) { return false; }
	$year = $db->addMarkup($args['year']);
	$html = '';
	
	$c = new Calendar_Entry();
	$labels = array();
	$labels['prevYearUrl'] = $serverPath . 'calendar/list/year_' . ($args['year'] - 1);
	$next = $c->getNextDay($day, $month, $year);
	$labels['nextYearUrl'] = $serverPath . 'calendar/list/year_' . ($args['year'] + 1);
	
	$html .= $theme->replaceLabels($labels, $theme->loadBlock('modules/calendar/views/yearsubnav.block.php'));
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>