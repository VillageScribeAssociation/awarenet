<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	show friend requests (to others)
//--------------------------------------------------------------------------------------------------
// * $args['userUID'] = UID of user whose profile this box is on

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
					. "::extra=(relationship: $relationship):]]<hr/>\n";
		}

	}	
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>