<?

	require_once($kapenta->installPath . 'modules/users/models/friendships.set.php');
	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show friend requests (to others)
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of user whose profile this box is on [string]

function users_showrequestedfriends($args) {
	global $user; 
	global $theme;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return '(User UID not given)'; }

	// admins can see everyones friend requests	TODO: use a permission for this
	if (($args['userUID'] != $user->UID) && ( 'admin' != $user->role) ) { return ''; }

	$set = new Users_Friendships($args['userUID']);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$requests = $set->getRequestsByMe();	
	$block = $theme->loadBlock('modules/users/views/showfriendrequest.block.php');

	if (count($requests) > 0) {
		foreach($requests as $item) { $html .= $theme->replaceLabels($item, $block); }
		$html = $theme->ntb($html, 'Friend Requests (from me)', 'divFriendsRequested', 'show');
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
