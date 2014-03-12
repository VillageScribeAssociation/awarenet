<?

//--------------------------------------------------------------------------------------------------
//|	the admin module's own admin console
//--------------------------------------------------------------------------------------------------

function forums_adminconsole($args) {
		global $theme;
		global $kapenta;


	if ('admin' != $kapenta->user->role) { return ''; }

	$html = $theme->loadBlock('modules/forums/views/adminconsole.block.php');

	return $html;
}

?>
