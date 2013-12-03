<?

	require_once($kapenta->installPath . 'modules/users/models/friendships.set.php');
	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show friend request box (if not already friend)
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of user whose profile this box is on [string]
//opt: notitle - surpress display of title (yes|no) [string]

function users_friendrequestprofilenav($args) {
	global $theme;
	global $user;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions 
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return '(userUID not given)'; }
	if ($args['userUID'] == $user->UID) { return ''; }		// cannot be friend with self

	//----------------------------------------------------------------------------------------------
	//	look for an existing friend request
	//----------------------------------------------------------------------------------------------

	if (true == $user->friendships->hasConfirmed($args['userUID'])) { return ''; } 
	//TODO:	perhaps add 'remove from friends' option in this case?
	
	$return = '';
	if (array_key_exists('notitle', $args) == true) { 
		$titlebar = ''; 
		$return = 'search';
	}

	//----------------------------------------------------------------------------------------------
	//	look for unconfirmed friend request
	//----------------------------------------------------------------------------------------------
	if ($user->friendships->hasUnconfirmed($args['userUID'])) {

		$model = new Users_Friendship();
		$model->loadFriend($user->UID, $args['userUID']);

		$withdrawBlock = '';	//TODO

		$html = ''
		 . "<div class='block'>\n"
		 . "[[:theme::navtitlebox::label=Friend Request::toggle=divFriendRequest:]]\n"
		 . "<div id='divFriendRequest'>\n"
		 . "You have requested to add " 
		 . "[[:users::namelink::userUID=" . $model->friendUID . ":]] as a friend \n"
		 . "(relationship: " . $model->relationship . "), they must approve your request "
		 . "before they will appear on your friends list.<br/><br/>\n"
		 . "<form name='withdrawFriendRequest' method='POST'"
		 . " action='%%serverPath%%users/removefriend/'>\n"
		 . "<input type='hidden' name='action' value='withdrawRequest' />\n"
		 . "<input type='hidden' name='friendUID' value='" . $model->friendUID . "' />\n"
		 . "<input type='submit' value='Widthdraw Request' />\n"
		 . "</form>\n"
		 . "</div>\n"
		 . "<div class='foot'></div>\n"
		 . "</div>\n"
		 . "<br/>\n";

		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//	neither, add friend request form
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/users/views/friendrequestform.block.php');
	$labels = array(
		'friendUID' => $args['userUID'],
		'return' => $return
	);

	$html = $theme->replaceLabels($labels, $block);
	$html = $theme->ntb($html, 'Make a Friend Request', 'divFriendRequest', 'hide');
	return $html;
	
}

//--------------------------------------------------------------------------------------------------

?>
