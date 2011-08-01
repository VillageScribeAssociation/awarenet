<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return the UID of the school a user attends
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Users_User object [string]
//opt: userUID - overrides raUID [string]
//opt: link - create link to school, default is yes (yes|no) [string]

function users_schooluid($args) {
	global $db, $user, $theme;
	$uid = '';												//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('userUID', $args)) { $args['raUID'] = $args['userUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Users_User($args['raUID']);
	if (false == $model->loaded) { return ''; }
	$uid = $model->school;

	return $uid;
}

?>
