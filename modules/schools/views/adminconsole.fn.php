<?

//--------------------------------------------------------------------------------------------------
//|	list of controls for this module as displayed on the admin console
//--------------------------------------------------------------------------------------------------

function schools_adminconsole($args) {
	global $theme, $user;
	if ('admin' != $user->role) { return ''; }

	$html = $theme->loadBlock('modules/schools/views/adminconsole.block.php');

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
