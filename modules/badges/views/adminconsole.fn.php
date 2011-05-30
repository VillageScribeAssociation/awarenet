<?

//--------------------------------------------------------------------------------------------------
//|	list of controls for this module as displayed on the admin console
//--------------------------------------------------------------------------------------------------

function badges_adminconsole($args) {
	global $theme, $user;
	if ('admin' != $user->role) { return ''; }
	$html = $theme->loadBlock('modules/badges/views/adminconsole.block.php');
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
