<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//	get next [num] events
//--------------------------------------------------------------------------------------------------
// * $args['num'] = number of events to show

function calendar_upcomingnav($args) {
	global $serverPath;
	$num = 10;
	if (array_key_exists('num', $args)) { $num = sqlMarkup($args['num']); }
	$c = new Calendar();
	$ev = $c->loadAllUpcoming($num);
	$html = '';
	foreach($ev as $UID => $row) {
		$c->loadArray($row);
		$link = $serverPath . 'calendar/' . $c->data['recordAlias'];
		$date = strtotime($row['year'] . '-' . $row['month'] . '-' . $row['day']);
		$date = date('jS F Y', $date);
		$desc = $c->data['title'];
		$html .= "<a class='black' href='$link'>$desc<br/><small>$date</small></a><br/>";
	}
	return $html;
}


?>