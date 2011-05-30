<?

//--------------------------------------------------------------------------------------------------
//|	list of controls for this module as displayed on the admin console
//--------------------------------------------------------------------------------------------------

function home_adminconsole($args) {
	global $theme, $user, $registry;
	if ('admin' != $user->role) { return ''; }

	$block = $theme->loadBlock('modules/home/views/adminconsole.block.php');
	$labels = array('home.frontpage' => $registry->get('home.frontpage'));
	$html = $theme->replaceLabels($labels, $block); 

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
