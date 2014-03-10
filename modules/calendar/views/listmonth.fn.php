<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show list of events in a given month
//--------------------------------------------------------------------------------------------------
//arg: year - year (yyyy) [string]
//arg: month - month (mm) 01 to 12 [string]

function calendar_listmonth($args) {
		global $theme;
		global $user;
		global $kapenta;


	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('month', $args)) { return ''; }
	if (false == array_key_exists('year', $args)) { return ''; }
	$year = $kapenta->db->addMarkup($args['year']);
	$month = $kapenta->db->addMarkup($args['month']);
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$model = new Calendar_Entry();
	$html = $model->drawMonth($month, $year, 'large');
	$ev = $model->loadMonth($month, $year);
	
	$html .= "<br/>[[:theme::navtitlebox::width=570::label=Entries:]]\n";
	$html .= "<h2>All Calendar Events For " . $model->getMonthName($month) . " $year</h2>";
	
	$block = $theme->loadBlock('modules/calendar/views/entry.block.php');

	if (count($ev) > 0) {
		foreach($ev as $UID => $row) {
			$model->loadArray($row);
			$html .= $theme->replaceLabels($model->extArray(), $block);
		}
	} else {
		$html .= '<p>(no events are recorded for this month)</p>';
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
