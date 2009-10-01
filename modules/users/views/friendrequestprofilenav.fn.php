<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	show friend request box (if not already friend)
//--------------------------------------------------------------------------------------------------
// * $args['userUID'] = UID of user whose profile this box is on
// * $args['notitle'] = yes|no (optional)

function users_friendrequestprofilenav($args) {
	global $user; $html = '';
	if (array_key_exists('userUID', $args) == false) { return false; }	// dud block
	if ($args['userUID'] == $user->data['UID']) { return false; }		// own profile

	$model = new Friendship();

	//----------------------------------------------------------------------------------------------
	//	look for an existing friendship
	//----------------------------------------------------------------------------------------------
	$friends = $model->getFriends($user->data['UID']);
	foreach($friends as $friendUID => $replationship) {
		if ($friendUID == $args['userUID']) { return ''; }	// are already friends
	}

	//----------------------------------------------------------------------------------------------
	//	look for an existing friend request
	//----------------------------------------------------------------------------------------------
	$titlebar = "[[:theme::navtitlebox::label=Friend Request:]]\n";
	$return = '';
	if (array_key_exists('notitle', $args) == true) { 
		$titlebar = ''; 
		$return = 'search';
	}

	$friends = $model->getFriendRequests($user->data['UID']);
	foreach($friends as $friendUID => $relationship) {
		if ($friendUID == $args['userUID']) { 

			$html = $titlebar 
				  . "You have requested to add " 
				  . "[[:users::namelink::userUID=" . $friendUID . ":]] as a friend \n"
				  . "(relationship: $relationship), they must approve your request before "
				  . "they will appear on your friends list.<br/><br/>\n";

			return $html;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	neither, add friend request form
	//----------------------------------------------------------------------------------------------

	$labels = array('friendUID' => $args['userUID']);
	$labels['titlebar'] = $titlebar;
	$labels['return'] = $return;
	$html = replaceLabels($labels, loadBlock('modules/users/views/friendrequestform.block.php'));

	return $html;
	
}

//--------------------------------------------------------------------------------------------------

?>