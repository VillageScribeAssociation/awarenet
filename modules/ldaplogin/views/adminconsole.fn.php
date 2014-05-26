<?

//--------------------------------------------------------------------------------------------------
//|	the admin module's own admin console
//--------------------------------------------------------------------------------------------------

function ldaplogin_adminconsole($args) {
	global $theme;

	global $kapenta;
	if ('admin' != $kapenta->user->role) { return ''; }

	$html = $theme->loadBlock('modules/ldaplogin/views/adminconsole.block.php');

	return $html;
}

?>
