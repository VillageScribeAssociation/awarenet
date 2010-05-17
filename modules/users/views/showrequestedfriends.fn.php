<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show friend requests (to others)
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of user whose profile this box is on [string]

function users_showrequestedfriends($args) {
	global $user; $html = '';
	if (array_key_exists('userUID', $args) == false) { return false; }

	// admins can see everyones friend requests
	if ( ($args['userUID'] != $user->data['UID'])
	 AND ( $user->data['ofGroup'] != 'admin')  ) { return false; }

	// make the list
	$model = new Friendship();
	$reqs = $model->getFriendRequests($args['userUID']);	
	
	if (count($reqs) > 0) {
		$html .= "[[:theme::navtitlebox::label=Friend Requests (from me):]]\n";

		foreach($reqs as $friendUID => $relationship) {
			//$labels = array('userUID' => $friendUID, 'relationship' => $relationship);
			//$html .= replaceLabels($labels, loadBlock('modules/users/views/confirmfriendnav.block.php'));
			$html .= "[[:users::summarynav::userUID=" . $friendUID 
					. "::extra=(relationship: $relationship):]]\n";

			$rmUrl = '%%serverPath%%users/removefriend/';

			$html .= "<form name='withdrawFriendRequest' method='POST' action='" . $rmUrl . "'>\n"
					. "<input type='hidden' name='action' value='withdrawRequest' />\n"
					. "<input type='hidden' name='friendUID' value='" . $friendUID . "' />\n"
					. "<input type='submit' value='widthdraw request' />\n"
					. "</form>\n";

			$html .= "<hr/>\n";

		}

	}	
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
