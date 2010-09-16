<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	short summary of user record formatted or the nav bar (300px wide)
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of record to summarise [string]
//opt: userUID - overrides UID [string]
//opt: extra - add something to this summary [string]
//opt: target - a URL or _parent, for iFrames [string]

function users_summarynav($args) {
	global $db, $user, $theme;
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('userUID', $args)) { $args['UID'] = $args['userUID']; }
	if (false == array_key_exists('UID', $args)) { return '(no uid)'; }

	$model = new Users_User($args['UID']);
	if (false == $user->authHas('users', 'Users_User', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();
	$labels['extra'] = '';

	if (true == array_key_exists('extra', $args)) { $labels['extra'] = $args['extra']; }

	$block = $theme->loadBlock('modules/users/views/summarynav.block.php');
	$html = $theme->replaceLabels($labels, $block);

	if (true == array_key_exists('target', $args)) 
		{ $html = str_replace("<a href=", "<a target='" . $args['target'] . "' href=", $html); }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
