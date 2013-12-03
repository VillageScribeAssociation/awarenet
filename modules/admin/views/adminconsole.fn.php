<?

//--------------------------------------------------------------------------------------------------
//|	the admin module's own admin console
//--------------------------------------------------------------------------------------------------

function admin_adminconsole($args) {
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return ''; }

	$html = $kapenta->theme->loadBlock('modules/admin/views/adminconsole.block.php');

	return $html;
}

?>
