<?

//--------------------------------------------------------------------------------------------------
//|	list of controls for this module as displayed on the admin console
//--------------------------------------------------------------------------------------------------

function files_adminconsole($args) {
	global $theme;
	global $user;

	if ('admin' != $user->role) { return ''; }

	$html = $theme->loadBlock('modules/files/views/adminconsole.block.php');

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
