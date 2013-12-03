<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show list of upcoming events in the same category
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or calendar entry [string]

function calendar_samecategorynav($args) {
	global $kapenta;
	if (array_key_exists('raUID', $args) == false) { return false; }
	$c = new Calendar_Entry($args['raUID']);
	$html = '';
	
	$ev = $c->loadUpcoming($c->category, 20);
	
	if (count($ev) > 0) {
		$html .= "<table noborder>\n";
		$html .= "<tr>\n";
		$html .= "<td class='title' width='90px'>&nbsp;Date&nbsp;</td>\n";
		$html .= "<td class='title'>&nbsp;Event&nbsp;</td>\n";
		$html .= "</tr>\n";
		foreach($ev as $UID => $row) {
			$link = $kapenta->serverPath . 'calendar/' . $row['alias'];
			$html .= "<tr>\n";			
			$html .= "<td valign='top'>" . $row['year'] . '-' . $row['month'] 
				. '-' . $row['day'] . "</td>\n";
			$html .= "<td><a href='" . $link . "'>" . $row['title'] . "</a></td>\n";
			$html .= "</tr>\n";
		}
		$html .= "</table>\n";

	} else {
		$html = '(no upcoming events)';
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

