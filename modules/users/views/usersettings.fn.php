<?

//--------------------------------------------------------------------------------------------------
//|	display complete table of user settings (key/value pairs)
//--------------------------------------------------------------------------------------------------
//opt: userUID - UID or alias of a Users_User object [string]

function users_usersettings($args) {
	global $kapenta;
	global $theme;

	$userUID = $kapenta->user->UID;		//%	show own data if user not specified [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (true == array_key_exists('userUID', $args)) { $userUID = $args['userUID']

	$model = new Users_User($userUID);
	if (false == $model->loaded) { return '(user not found)'; }

	$model->loadRegistry();

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table= array(array('Key', 'Value'));		
	foreach($model->registry->members as $key => $value) { $table[$key] = base64_decode($value); }
	$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
