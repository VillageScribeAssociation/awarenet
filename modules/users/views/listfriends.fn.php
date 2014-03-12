<?

	require_once($kapenta->installPath . 'modules/users/models/friendships.set.php');
	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list friends of a given user
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of user whose profile this box is on [string]

function users_listfriends($args) {
	global $kapenta;
	global $kapenta; 

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check agument and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return false; }

	$set = new Users_Friendships($args['userUID']);

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------	
	// make the list
	//----------------------------------------------------------------------------------------------
	$friends = $set->getConfirmed();	
	
	if (0 == count($friends)) { $html .= "<div class='inlinequote'>None added yet.</div>";	}

	foreach($friends as $item) { 
		$rmLink = '';
		if ($args['userUID'] == $kapenta->user->UID) {
			$rmUrl = "users/editfriend/" . $item['friendUID'];
			$rmLink = "<a href='%%serverPath%%" . $rmUrl . "'>[modify]</a>";
		}
			
		$html .= "[[:users::summarynav::userUID=" . $item['friendUID'] . "::"
				 . "extra=<small>Relationship: " . $item['relationship'] . " $rmLink</small>:]]\n"; 
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
