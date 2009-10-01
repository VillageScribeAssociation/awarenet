<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//	show list of events on a given day
//--------------------------------------------------------------------------------------------------
// * $args['year'] = year (yyyy)
// * $args['month'] = month (mm) 01 to 12
// * $args['day'] = day (dd) 01 to 31

function calendar_listday($args) {
	if (array_key_exists('day', $args) == false) { return false; }
	if (array_key_exists('month', $args) == false) { return false; }
	if (array_key_exists('year', $args) == false) { return false; }
	$year = sqlMarkup($args['year']);
	$month = sqlMarkup($args['month']);
	$day = sqlMarkup($args['day']);
	$html = '';
	
	$c = new Calendar();
	$html = $c->drawMonth($month, $year, 'large');
	$ev = $c->loadDay($day, $month, $year);
	
	$html .= "<br/>[[:theme::navtitlebox::width=570::label=Entries:]]\n";
	$html .= "<h2>All Calendar Events For " . $c->getDayName($day, $month, $year) 
	      . " $day " . $c->getMonthName($month) . " $year</h2>";
	
	if (count($ev) > 0) {
		foreach($ev as $UID => $row) {
			$c->loadArray($row);
			$html .= replaceLabels($c->extArray(), loadBlock('modules/calendar/views/show.block.php'));
		}
	} else {
		$html .= '<p>(no events are recorded for this date)</p>';
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>