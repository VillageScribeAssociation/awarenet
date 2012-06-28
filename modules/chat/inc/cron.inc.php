<?

	require_once($kapenta->installPath . 'modules/chat/models/session.mod.php');

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	ten minute cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function chat_cron_tenmins() {
	global $db;
	global $kapenta;

	$report = "<h2>chat_cron_tenmins</h2>\n";			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	remove expired / unattended chat_session objects
	//----------------------------------------------------------------------------------------------

	$sql = "select * from chat_session";
	$result = $db->query($sql);

	while($row = $db->fetchAssoc($result)) {
		$item = $db->rmArray($row);
		$edited = $kapenta->strtotime($item['editedOn']);		//%	timestamp [int]
		$now = $kapenta->time();								//%	timestamp [int]
		$diff = $now - $edited;

		//TODO: use registry key to get rid of this magic number (5 minutes)
		if ($diff > 300) {
			$model = new Chat_Session($item['UID']);
			$check = $model->delete();
			if (true == $check) {
				$report .= "Removing inactive session: " . $item['userUID'] . " ($diff).<br/>\n";
			} else {
				$report .= "Could not remove session: " . $item['userUID'] . " ($diff).<br/>\n";
			}
		}
	}

	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	hourly cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function chat_cron_hourly() {
	global $db;
	global $kapenta;
	
	$report = "<h2>chat_cron_hourly</h2>\n";			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	remove chat memberships after a week of inactivity?
	//----------------------------------------------------------------------------------------------
	//TODO: managed by server?

	return $report;

}


?>
