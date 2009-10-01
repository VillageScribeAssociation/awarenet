<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	listfriends
//--------------------------------------------------------------------------------------------------
// * $args['userUID'] = UID of user whose profile this box is on

function users_listfriends($args) {
	global $user; $html = '';
	if (array_key_exists('userUID', $args) == false) { return false; }

	// make the list
	$model = new Friendship();
	$friends = $model->getFriends($args['userUID']);	
	
	if (count($friends) > 0) {
		foreach($friends as $fUID => $rsp) 
			{ $html .= "[[:users::summarynav::userUID=$fUID::extra=(relationship; $rsp):]]\n"; }

	} else { $html .= "(none added yet)<br/>";	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>