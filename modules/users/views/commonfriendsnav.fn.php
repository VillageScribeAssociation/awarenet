<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	find common friends between the current user and some random, returns thumbnails
//--------------------------------------------------------------------------------------------------
// * $args['userUID'] = overrides raUID
// * $args['UID'] = recordAlias or UID or groups entry
// * $args['size'] = of images returned

function users_commonfriendsnav($args) {
	global $user;
	if (array_key_exists('userUID', $args) == true) { $args['UID'] = $args['userUID']; }
	if (array_key_exists('UID', $args) == false) { return false; }
	$html = '';

	$model = new Friendship();
	$common = $model->findCommonFriends($user->data['UID'], $args['UID']);

	if (count($common) > 0) {
		$html .= "[[:theme::navtitlebox::label=Friends In Common:]]";
		foreach ($common as $userUID) {
			$userRa = raGetDefault('users', $userUID);
			$html .= "<a href='/users/profile/" . $userRa . "'>" 
					. "[[:users::avatar::userUID=" . $userUID . "::size=thumbsm:]]</a>\n";
		}
	} 

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>