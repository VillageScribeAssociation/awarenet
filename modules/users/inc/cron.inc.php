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
	global $kapenta;
	global $kapenta;

	$report = "<h2>users_cron_tenmins</h2>\n";							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	remove userlogin records if user has not been seen online for awhile
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: remove this section, replaced by users_session table

	$report .= "<h2>Removing old login records (DEPRECATED)</h2>";

	$expTime = time() - 600;	// TODO: make this a configurable setting
	$sql = "select * from users_login where lastseen < '" . $kapenta->db->datetime($expTime) . "'";
	$result = $kapenta->db->query($sql);
	$count = 0;

	if (0 == $kapenta->db->numRows($result)) { $report .= "(no inactive sessions)"; }
	else {
		$count = 0;
		while ($row = $kapenta->db->fetchAssoc($result)) {
			$row = $kapenta->db->rmArray($row);
			$model = new Users_Login();
			$model->loadArray($row);
			$model->delete();
			$count++;
		}
		$report .= "Removed $count inactive sessions.<br/>";
	}

	//----------------------------------------------------------------------------------------------
	//	mark sessions as inactive if user not seen for 10 or more minutes
	//----------------------------------------------------------------------------------------------
	//TODO: replace magic number with registry setting

	$report .= "<h2>Checking user session status.</h2>";
	$sql = "select * from users_session where status='active'";
	$result = $kapenta->db->query($sql);
	$count = 0;

	while($row = $kapenta->db->fetchAssoc($result)) {
		$item = $kapenta->db->rmArray($row);						//%	remove SQL markup [array]
		$ts = $kapenta->strtotime($item['editedOn']);	//%	session last updated [int]
		$limit = time() - 600;							//% ten nimutes ago [int]

		if ($ts < $limit) {
			$model = new Users_Session($item['UID']);
			$model->status = 'closed';
			$check = $model->save();
			if ('' == $check) {
				$report .= "Session exired: " . $model->UID . " (" . $model->createdBy . ")<br/>\n";
				$count++;
			} else {
				$report .= "<b>Could not update user session:</b><br/>$check<br/>\n";
			}
		}
	}

	if (0 < $count) { $report .= "<b>Closed $count expired sessions.</b><br/>"; }

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

?>
