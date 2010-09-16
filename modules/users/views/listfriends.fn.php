<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list friends of a given user
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of user whose profile this box is on [string]

function users_listfriends($args) {
	global $user, $db; 
	$html = '';

	if (array_key_exists('userUID', $args) == false) { return false; }

	// make the list
	$model = new Users_Friendship();
	$friends = $model->getFriends($args['userUID']);	
	
	if (count($friends) > 0) {
		foreach($friends as $fUID => $rsp) { 
			$rmLink = '';
			if ($args['userUID'] == $user->UID) {
				$rmUrl = "users/editfriend/" . $fUID;
				$rmLink = "<a href='%%serverPath%%" . $rmUrl . "'>[modify]</a>";
			}
			
			$html .= "[[:users::summarynav::userUID=$fUID::"
					 . "extra= $rmLink (relationship; $rsp):]]\n"; 
		}

	} else { $html .= "(none added yet)<br/>";	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
