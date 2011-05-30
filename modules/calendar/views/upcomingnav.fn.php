<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	get next [num] events
//--------------------------------------------------------------------------------------------------
//opt: num - number of events to show [string]

function calendar_upcomingnav($args) {
	global $kapenta, $db;
	$html = '';				//%	return value [string]
	$num = 10;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('num', $args)) { $num = $db->addMarkup($args['num']); }
	$model = new Calendar_Entry();
	$ev = $model->loadAllUpcoming($num);

	foreach($ev as $UID => $row) {
		$model->loadArray($row);
		$link = '%%serverPath%%calendar/' . $model->alias;
		$date = strtotime($row['year'] . '-' . $row['month'] . '-' . $row['day']);
		$date = date('jS F Y', $date);
		$desc = $model->title;
		$html .= "<a class='black' href='$link'>$desc<br/><small>$date</small></a><br/>";
	}

	return $html;
}


?>
