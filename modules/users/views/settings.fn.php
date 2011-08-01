<?

//--------------------------------------------------------------------------------------------------
//|	settings form for users module
//--------------------------------------------------------------------------------------------------

function users_settings($args) {
	global $user;
	global $registry;
	global $theme;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/users/views/settings.block.php');

	$labels = array(
		'users.allowpublicsignup' => $registry->get('users.allowpublicsignup'),
		'users.allowteachersignup' => $registry->get('users.allowteachersignup'),
		'users.grades' => $registry->get('users.grades')
	);		// add more settings here

	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

?>
