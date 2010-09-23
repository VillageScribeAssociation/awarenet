<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show list of events on a given day
//--------------------------------------------------------------------------------------------------
//arg: year - year (yyyy) [string]
//arg: month - month (mm) 01 to 12 [string]
//arg: day - day (dd) 01 to 31 [string]

function calendar_listday($args) {
	global $theme, $db;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('day', $args)) { return ''; }
	if (false == array_key_exists('month', $args)) { return ''; }
	if (false == array_key_exists('year', $args)) { return ''; }
	$year = $db->addMarkup($args['year']);
	$month = $db->addMarkup($args['month']);
	$day = $db->addMarkup($args['day']);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------	
	//TODO: move this database work out of the model

	$model = new Calendar_Entry();
	$html = $model->drawMonth($month, $year, 'large');
	$ev = $model->loadDay($day, $month, $year);
	
	$html .= "<br/>[[:theme::navtitlebox::width=570::label=Entries:]]\n";
	$html .= "<h2>All Calendar Events For " . $model->getDayName($day, $month, $year) 
	      . " $day " . $model->getMonthName($month) . " $year</h2>";
	
	$block = $theme->loadBlock('modules/calendar/views/show.block.php');

	if (count($ev) > 0) {
		foreach($ev as $UID => $row) {
			$model->loadArray($row);
			$html .= $theme->replaceLabels($model->extArray(), $block);
		}
	} else {
		$html .= '<p>(no events are recorded for this date)</p>';
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
