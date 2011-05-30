<?

//--------------------------------------------------------------------------------------------------
//|	list of controls for this module as displayed on the admin console
//--------------------------------------------------------------------------------------------------

function twitter_adminconsole($args) {
	global $theme, $user;
	if ('admin' != $user->role) { return ''; }

	$html = $theme->loadBlock('modules/twitter/views/adminconsole.block.php');

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
