<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return the UID of the school a user attends, or empty string on failure
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Users_User object [string]
//opt: UID - overrides raUID if present [string]
//opt: userUID - overrides raUID if present [string]

function users_schooluid($args) {
		global $db;
		global $user;
		global $theme;

	$uid = '';												//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('userUID', $args)) { $args['raUID'] = $args['userUID']; }
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the school
	//----------------------------------------------------------------------------------------------
	$model = new Users_User($args['raUID']);
	if (false == $model->loaded) { return ''; }
	$uid = $model->school;

	return $uid;
}

?>
