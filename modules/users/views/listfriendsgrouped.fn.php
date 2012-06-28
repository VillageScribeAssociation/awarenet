<?

	require_once($kapenta->installPath . 'modules/users/models/friendships.set.php');
	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list friends of a given user
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of user whose profile this box is on [string]

function users_listfriendsgrouped($args) {
	global $user;
	global $db; 

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check agument and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return false; }

	$set = new Users_Friendships($args['userUID']);

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------	
	// sort into groups and make the list
	//----------------------------------------------------------------------------------------------
	$friends = $set->getConfirmed();		//%	[array]
	$groups = array();						//% [array]
	if (0 == count($friends)) { $html .= "<div class='inlinequote'>None added yet.</div>";	}

	foreach ($friends as $item) {
		if (false == array_key_exists($item['relationship'], $groups)) {
			$groups[$item['relationship']] = array();
		}
		$groups[$item['relationship']][] = $item;
	}

	foreach($groups as $group => $items) {
		if (count($groups) > 1) {
			if (count($items) > 1) {
				$html .= "<h3>" . $group . "s</h3>\n";
			} else {
				$html .= "<h3>$group</h3>\n";
			}
		}

		foreach($items as $item) {
			$rmLink = '';

			if ($args['userUID'] == $user->UID) {
				$rmUrl = "users/editfriend/" . $item['friendUID'];
				$rmLink = "<a href='%%serverPath%%" . $rmUrl . "'>[modify relationship]</a>";
			}
			
			$html .= ''
			 . "[[:users::summarynav::userUID=" . $item['friendUID'] . "::"
			 . "extra=<small>(" . $item['relationship'] . ") $rmLink</small>:]]\n"; 
		}
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
