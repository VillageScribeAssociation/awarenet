<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//	menu for calendar, no arguments
//--------------------------------------------------------------------------------------------------

function calendar_menu($args) {
	$labels = array();
	$now = time();
	$labels['thisYear'] = 'year_' . date('Y');
	$labels['thisMonth'] = 'month_' . date('Y') . '_' . date('m');
	$labels['today'] = 'day_' . date('Y') . '_' . date('m') . '_' . date('j');
	
	if ((date('m') + 1) > 12) {
		$labels['nextMonth'] = 'month_' . (date('Y') + 1) . '_01';	
	} else {
		$labels['nextMonth'] = 'month_' . date('Y') . '_' . (date('m') + 1);
	}
	
	
	if (authHas('calendar', 'edit', '')) {
		$labels['newEntry'] = '[[:theme::submenu::label=Add Calendar Entry::link=/calendar/new/:]]';
	} else { $labels['newEntry'] = ''; }
	
	$html = replaceLabels($labels, loadBlock('modules/calendar/views/menu.block.php'));
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>