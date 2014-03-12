<?

//--------------------------------------------------------------------------------------------------
//|	list of controls for this module as displayed on the admin console
//--------------------------------------------------------------------------------------------------

function users_adminconsole($args) {
	global $theme;

	global $kapenta;
	if ('admin' != $kapenta->user->role) { return ''; }

	$html = $theme->loadBlock('modules/users/views/adminconsole.block.php');

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>