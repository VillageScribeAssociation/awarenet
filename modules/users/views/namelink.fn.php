<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	make a link to a users profile with the users name
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of a user record
// * $args['userUID'] = overrides raUID
// * $args['target'] = link larget

function users_namelink($args) {
	$target = '';
	if (array_key_exists('userUID', $args) == true) { $args['raUID'] = $args['userUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$html = '';

	if (array_key_exists('target', $args) == true) { $target = "target='". $args['target'] ."'"; }

	$model = new Users($args['raUID']);
	$fullName = $model->data['firstname'] . ' ' . $model->data['surname'];
	$html = "<a href='/users/profile/" . $model->data['recordAlias'] . "' $target>$fullName</a>";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>