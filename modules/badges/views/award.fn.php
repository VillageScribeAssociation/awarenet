<?

//--------------------------------------------------------------------------------------------------
//|	form for awarding badges to users from their profile
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a Users_User object [string]

function badges_award($args) {
	global $db;
	global $user;
	global $theme;

	$title = 'Award Badge';			//%	default title [string]
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('userUID', $args)) { return '(userUID not supplied)'; }

	$model = new Users_User($args['userUID']);
	if (false == $model->loaded) { return '(user not found)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/badges/views/award.block.php');
	$labels = array('userUID' => $model->UID);
	$html = $theme->replaceLabels($labels, $block);

	$html = $theme->ntb($html, $title, 'divAward', 'hide');

	return $html;
}

?>
