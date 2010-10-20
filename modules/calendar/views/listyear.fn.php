<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display upload/view for a single image (eg, user profile picture)
//--------------------------------------------------------------------------------------------------
//arg: year - show a year [string]

function calendar_listyear($args) {	
	global $db;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check input
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('year', $args)) { return ''; }
	if (strlen($args['year']) != 4) { return false; }
	$model = new Calendar_Entry();
	//TODO: permissions check here
	
	//----------------------------------------------------------------------------------------------
	//	make the listing
	//----------------------------------------------------------------------------------------------
	for($i = 1; $i <= 12; $i++) {
		$monthName = $model->getMonthName($i);
		$html .= "<h2><a class='black' href='/calendar/month_" .  $args['year'] . "_" 
		      . $model->twoDigits($i) . "'>$monthName " . $args['year'] . "</a></h2>";
		      
		$events = $model->loadMonth($i, $args['year']);
		if (count($events) > 0) {
			$html .= "<table noborder>\n";
			$html .= "<tr>\n";
			$html .= "<td class='title'>&nbsp;Day&nbsp;</td>\n";
			$html .= "<td class='title'>&nbsp;Category&nbsp;</td>\n";
			$html .= "<td class='title'>&nbsp;Event&nbsp;</td>\n";
			$html .= "<tr>\n";
			
			foreach($events as $UID => $row) {
				$html .= "<tr>\n";
				$html .= "<td>" . $row['day'] . "</td>\n";
				$html .= "<td>" . $row['category'] . "</td>\n";
				$html .= "<td><A href='/calendar/" . $row['alias'] . "'>" 
					. $row['title'] . "</a></td>\n";
				$html .= "</tr>\n";
			}
			
			$html .= "</table>\n";
		} else {
			$html .= "<p>(no events recorded for this month)</p>\n";
		}
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

