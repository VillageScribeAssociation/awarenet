<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/login.mod.php');

//--------------------------------------------------------------------------------------------------
//*	colelction object for handling Users_Login objects
//--------------------------------------------------------------------------------------------------

class Users_Logins {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Users_Logins() {
		// nothing as yet
	}

	//----------------------------------------------------------------------------------------------
	//.	make XML document listing users logged in to this node
	//----------------------------------------------------------------------------------------------

	function getLocalSessionsXml() {
		global $kapenta;

		
	}

	//----------------------------------------------------------------------------------------------
	//.	clear old entries from the Users_Login table	//TODO: move to cron
	//----------------------------------------------------------------------------------------------

	function clearOldEntries() {
		global $kapenta;
		global $kapenta;

		$range = $kapenta->db->loadRange('users_login', '*', '', '', '', '');
		foreach($range as $row) {
			if (($row['serverUID'] == $kapenta->serverPath) && ($kapenta->time() > ($row['lastseen'] + $this->maxAge))) { 
				$kapenta->db->delete('users', 'users_login', $row['UID']); 
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if there is already an entry for this user
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: true if there is a record of a current session, otherwise false [bool]

	function inList($userUID) {
		global $kapenta;

		$sql = "select * from users_login where userUID='" . $kapenta->db->addMarkup($userUID) . "'";
		$result = $kapenta->db->query($sql);
		if ($kapenta->db->numRows($result) > 0) { return true; }
		return false;
	}

}


?>
