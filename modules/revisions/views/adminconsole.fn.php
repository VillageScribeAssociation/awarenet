<?

//--------------------------------------------------------------------------------------------------
//|	list of controls for this module as displayed on the admin console
//--------------------------------------------------------------------------------------------------

function revisions_adminconsole($args) {
	global $theme;
	global $user;

	if ('admin' != $user->role) { return ''; }

	$html = $theme->loadBlock('modules/revisions/views/adminconsole.block.php');

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
