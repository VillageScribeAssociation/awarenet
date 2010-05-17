<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show friend requests (from others)
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of user whose profile this box is on [string]

function users_showfriendrequests($args) {
	global $user; $html = '';
	if (array_key_exists('userUID', $args) == false) { return false; }

	// admins can see everyones friend requests
	if ( ($args['userUID'] != $user->data['UID'])
	 AND ( $user->data['ofGroup'] != 'admin')  ) { return false; }

	// make the list
	$model = new Friendship();
	$reqs = $model->getFriendRequested($args['userUID']);	
	
	if (count($reqs) > 0) {
		$html .= "[[:theme::navtitlebox::label=Friend Requests (to me):]]\n";
		$block = loadBlock('modules/users/views/confirmfriendnav.block.php');

		foreach($reqs as $friendUID => $relationship) {
			$labels = array('friendUID' => $friendUID, 'relationship' => $relationship);
			$html .= replaceLabels($labels, $block);
		}

	}	
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
