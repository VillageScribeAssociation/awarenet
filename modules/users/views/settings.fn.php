<?

//--------------------------------------------------------------------------------------------------
//|	settings form for users module
//--------------------------------------------------------------------------------------------------

function users_settings($args) {
	global $user;
	global $kapenta;
	global $theme;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/users/views/settings.block.php');

	$labels = array(
		'users.allowpublicsignup' => $kapenta->registry->get('users.allowpublicsignup'),
		'users.allowteachersignup' => $kapenta->registry->get('users.allowteachersignup'),
		'users.grades' => $kapenta->registry->get('users.grades')
	);		// add more settings here

	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

?>
