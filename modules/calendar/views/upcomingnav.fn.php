<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	get next [num] events
//--------------------------------------------------------------------------------------------------
//opt: num - number of events to show [string]

function calendar_upcomingnav($args) {
	global $db;

	global $serverPath;
	$num = 10;
	if (array_key_exists('num', $args)) { $num = $db->addMarkup($args['num']); }
	$c = new Calendar_Entry();
	$ev = $c->loadAllUpcoming($num);
	$html = '';
	foreach($ev as $UID => $row) {
		$c->loadArray($row);
		$link = $serverPath . 'calendar/' . $c->alias;
		$date = strtotime($row['year'] . '-' . $row['month'] . '-' . $row['day']);
		$date = date('jS F Y', $date);
		$desc = $c->title;
		$html .= "<a class='black' href='$link'>$desc<br/><small>$date</small></a><br/>";
	}
	return $html;
}


?>