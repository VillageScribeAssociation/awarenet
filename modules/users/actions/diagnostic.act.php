<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');

//--------------------------------------------------------------------------------------------------
//*	examine objects on this module and look for errors
//--------------------------------------------------------------------------------------------------
//TODO: check integration with admin console, and evaluate whether this is still needed.

	//----------------------------------------------------------------------------------------------
	//	user auth
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	check friendships table
	//----------------------------------------------------------------------------------------------

	$sql = "select * from User_Friendships";
	$result = $kapenta->db->query($sql);

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$userFrom = new Users_User($row['userUID']);
		$userTo = new Users_User($row['friendUID']);
		
		//------------------------------------------------------------------------------------------
		//	check that the friendship is requited
		//------------------------------------------------------------------------------------------
		$reqStr = 'unrequited';
		$model = new Users_Friendship();
		$requited = $model->loadFriend($row['friendUID'], $row['userUID']);
		if (true == $requited) { $reqStr = 'requited';}

		if (true == $requited) {
			if ('unconfirmed' == $model->status) {
				echo "***unconfirmed requited friendship*** (friend to confirm): \n";
				$model->status = 'confirmed';
				$model->save();
			}

			if ('unconfirmed' == $row['status']) {
				echo "***unconfirmed requited friendship*** (user to confirm): \n";
				$tmpModel = new Users_Friendship($row['UID']);
				$tmpModel->status = 'confirmed';
				$tmpModel->save();
			}

		} else {
			if ('confirmed' == $row['status']) {
				echo "***confirmed unrequited friendship***: \n";
			}
		}

		$msg = "checking friendship " . $row['UID'] . ": " . $userFrom->getName() 
			 . " -> " . $userTo->getName() . " (" . $row['status'] . ")($reqStr)<br/>\n";

		echo $msg;

	}

?>
