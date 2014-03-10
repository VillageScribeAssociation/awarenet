<?

//--------------------------------------------------------------------------------------------------
//|	list of controls for this module as displayed on the admin console
//--------------------------------------------------------------------------------------------------
//returns: console panel [string:html]

function aliases_adminconsole($args) {
		global $theme;
		global $user;

	if ('admin' != $user->role) { return ''; }

	$html = $theme->loadBlock('modules/aliases/views/adminconsole.block.php');

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
