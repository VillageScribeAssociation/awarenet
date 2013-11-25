<?

//--------------------------------------------------------------------------------------------------
//|	the admin module's own admin console
//--------------------------------------------------------------------------------------------------

function lessons_adminconsole($args) {
	global $theme;

	global $user;
	if ('admin' != $user->role) { return ''; }

	$html = $theme->loadBlock('modules/lessons/views/adminconsole.block.php');

	return $html;
}

?>
