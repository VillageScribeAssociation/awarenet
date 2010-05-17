<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	get a users name
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a user record [string]
//opt: userUID - overrides raUID [string]

function users_name($args) {
	if (array_key_exists('userUID', $args) == true) { $args['raUID'] = $args['userUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$html = '';

	$model = new User($args['raUID']);
	$html = $model->data['firstname'] . ' ' . $model->data['surname'];
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
