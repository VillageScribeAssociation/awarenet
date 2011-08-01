<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	get a users name
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a user record [string]
//opt: userUID - overrides raUID [string]

function users_name($args) {
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('userUID', $args)) { $args['raUID'] = $args['userUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	get the name
	//----------------------------------------------------------------------------------------------
	$model = new Users_User($args['raUID']);
	$html = $model->firstname . ' ' . $model->surname;

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
