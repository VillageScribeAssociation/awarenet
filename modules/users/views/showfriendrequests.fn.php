<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show friend requests (from others)
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of user whose profile this box is on [string]

function users_showfriendrequests($args) {
	global $theme;
	global $user; 

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return ''; }

	// admins can see everyones friend requests
	if (($args['userUID'] != $user->UID) && ('admin' != $user->role)) { return ''; }

	$set = new Users_Friendships($user->UID);

	//----------------------------------------------------------------------------------------------
	// make the list
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/users/views/confirmfriendnav.block.php');
	$reqs = $set->getRequestsOfMe($args['userUID']);	
	
	if (count($reqs) > 0) {
		foreach($reqs as $item) { $html .= $theme->replaceLabels($item, $block); }
		$html = $theme->ntb($html, 'Friend Requests (to me)', 'divFriendRequests', 'show');
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
