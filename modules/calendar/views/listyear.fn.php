<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//	display upload/view for a single image (eg, user profile picture)
//--------------------------------------------------------------------------------------------------
// * $args['year'] = show a year

function calendar_listyear($args) {	
	//----------------------------------------------------------------------------------------------
	//	check input
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('year', $args) == false) { return false; }
	if (strlen($args['year']) != 4) { return false; }
	$c = new Calendar();
	$html = '';
	
	//----------------------------------------------------------------------------------------------
	//	make the listing
	//----------------------------------------------------------------------------------------------
	for($i = 1; $i <= 12; $i++) {
		$monthName = $c->getMonthName($i);
		$html .= "<h2><a class='black' href='/calendar/month_" .  $args['year'] . "_" 
		      . $c->twoDigits($i) . "'>$monthName " . $args['year'] . "</a></h2>";
		      
		$events = $c->loadMonth($i, $args['year']);
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
				$html .= "<td><A href='/calendar/" . $row['recordAlias'] . "'>" 
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