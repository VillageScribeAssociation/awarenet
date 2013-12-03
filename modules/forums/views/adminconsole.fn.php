<?

//--------------------------------------------------------------------------------------------------
//|	the admin module's own admin console
//--------------------------------------------------------------------------------------------------

function forums_adminconsole($args) {
	global $theme, $user;

	if ('admin' != $user->role) { return ''; }

	$html = $theme->loadBlock('modules/forums/views/adminconsole.block.php');

	return $html;
}

?>
