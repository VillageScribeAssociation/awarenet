<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//|	get next [num] events
//--------------------------------------------------------------------------------------------------
//opt: num - number of events to show [string]

function calendar_upcomingnav($args) {
	global $kapenta;
	global $kapenta;

	$html = '';				//%	return value [string]
	$num = 10;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('num', $args)) { $num = $kapenta->db->addMarkup($args['num']); }
	$model = new Calendar_Entry();
	$ev = $model->loadAllUpcoming($num);

	foreach($ev as $UID => $row) {
		$model->loadArray($row);
		$link = '%%serverPath%%calendar/' . $model->alias;
		$timestamp = $kapenta->strtotime($row['year'] . '-' . $row['month'] . '-' . $row['day']);
		$date = date('jS F Y', $timestamp);
		$desc = $model->title;
		$html .= "<a class='black' href='$link'>$desc<br/><small>$date</small></a><br/>";
	}

	return $html;
}


?>
