<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	get a users name
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of a user record
// * $args['userUID'] = overrides raUID

function users_name($args) {
	if (array_key_exists('userUID', $args) == true) { $args['raUID'] = $args['userUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$html = '';

	$model = new Users($args['raUID']);
	$html = $model->data['firstname'] . ' ' . $model->data['surname'];
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>