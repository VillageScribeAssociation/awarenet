<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show list of events in a given month
//--------------------------------------------------------------------------------------------------
//arg: year - year (yyyy) [string]
//arg: month - month (mm) 01 to 12 [string]

function calendar_listmonth($args) {
	if (array_key_exists('month', $args) == false) { return false; }
	if (array_key_exists('year', $args) == false) { return false; }
	$year = sqlMarkup($args['year']);
	$month = sqlMarkup($args['month']);
	$html = '';
	
	$c = new Calendar();
	$html = $c->drawMonth($month, $year, 'large');
	$ev = $c->loadMonth($month, $year);
	
	$html .= "<br/>[[:theme::navtitlebox::width=570::label=Entries:]]\n";
	$html .= "<h2>All Calendar Events For " . $c->getMonthName($month) . " $year</h2>";
	
	if (count($ev) > 0) {
		foreach($ev as $UID => $row) {
			$c->loadArray($row);
			$html .= replaceLabels($c->extArray(), loadBlock('modules/calendar/views/entry.block.php'));
		}
	} else {
		$html .= '<p>(no events are recorded for this month)</p>';
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

