<?

//--------------------------------------------------------------------------------------------------
//|	the admin module's own admin console
//--------------------------------------------------------------------------------------------------

function admin_adminconsole($args) {
	global $theme;

	global $user;
	if ('admin' != $user->role) { return ''; }

	$html = $theme->loadBlock('modules/admin/views/adminconsole.block.php');

	return $html;
}

?>