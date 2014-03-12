<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a link to a users profile with the users name
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a user record [string]
//opt: userUID - overrides raUID [string]
//opt: target - link larget (for iFrames) [string]

function users_namelink($args) {
	global $kapenta;

	$target = '';
	if (true == array_key_exists('userUID', $args)) { $args['raUID'] = $args['userUID']; }
	if (false == array_key_exists('raUID', $args)) { return false; }
	$html = '';

	if ('public' == $kapenta->user->role) { 
		// public user canot see profiles, so don't link them
		return '[[:users::name::userUID=' . $args['raUID'] . ':]]'; 
	}

	if (array_key_exists('target', $args) == true) { $target = "target='". $args['target'] ."'"; }

	$model = new Users_User($args['raUID']);
	$fullName = $model->firstname . ' ' . $model->surname;
	$html = "<a href='/users/profile/" . $model->alias . "' $target>$fullName</a>";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
