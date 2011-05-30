<?

	require_once($kapenta->installPath . 'modules/users/models/login.mod.php');

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	ten minute cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function users_cron_tenmins() {
	global $db;
	$report = "<h2>users_cron_tenmins</h2>\n";	//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	remove userlogin records if user has not been seen online for awhile
	//----------------------------------------------------------------------------------------------
	$expTime = time() - 600;	// TODO: make this a configurable setting
	$sql = "select * from users_login where lastseen < '" . $db->datetime($expTime) . "'";
	$result = $db->query($sql);

	if (0 == $db->numRows($result)) { $report .= "(no inactive sessions)"; }
	else {
		$count = 0;
		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			$model = new Users_Login();
			$model->loadArray($row);
			$model->delete();
			$count++;
		}
		$report .= "Removed $count inactive sessions.<br/>";
	}

	//----------------------------------------------------------------------------------------------
	//	post and retrieve a list of active logins from the central server
	//----------------------------------------------------------------------------------------------

	

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

?>
