<?

//--------------------------------------------------------------------------------------------------
//|	the admin module's own admin console
//--------------------------------------------------------------------------------------------------

function lessons_adminconsole($args) {
	global $theme;

	global $kapenta;
	if ('admin' != $kapenta->user->role) { return ''; }

	$html = $theme->loadBlock('modules/lessons/views/adminconsole.block.php');

	return $html;
}

?>
