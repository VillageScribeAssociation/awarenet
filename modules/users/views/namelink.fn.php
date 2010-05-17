<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a link to a users profile with the users name
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a user record [string]
//opt: userUID - overrides raUID [string]
//opt: target - link larget (for iFrames) [string]

function users_namelink($args) {
	$target = '';
	if (array_key_exists('userUID', $args) == true) { $args['raUID'] = $args['userUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$html = '';

	if (array_key_exists('target', $args) == true) { $target = "target='". $args['target'] ."'"; }

	$model = new User($args['raUID']);
	$fullName = $model->data['firstname'] . ' ' . $model->data['surname'];
	$html = "<a href='/users/profile/" . $model->data['recordAlias'] . "' $target>$fullName</a>";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
