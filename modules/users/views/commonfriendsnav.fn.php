<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find common friends between the current user and some random, returns thumbnails
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a user [string]
//opt: userUID - overrides UID [string]
//opt: size - of images returned [string]

function users_commonfriendsnav($args) {
		global $kapenta;
		global $alises;

	if (array_key_exists('userUID', $args) == true) { $args['UID'] = $args['userUID']; }
	if (array_key_exists('UID', $args) == false) { return false; }
	$html = '';

	$model = new Users_Friendship();
	$common = $model->findCommonFriends($kapenta->user->UID, $args['UID']);

	if (count($common) > 0) {
		$html .= "[[:theme::navtitlebox::label=Friends In Common:]]";
		foreach ($common as $userUID) {
			$userRa = $aliases->getDefault('users_user', $userUID);	// clunky, TODO: improve
			$html .= "<a href='/users/profile/" . $userRa . "'>" 
					. "[[:users::avatar::userUID=" . $userUID . "::size=thumbsm:]]</a>\n";
		}
	} 

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
