<?

//--------------------------------------------------------------------------------------------------
//|	list of controls for this module as displayed on the admin console
//--------------------------------------------------------------------------------------------------

function home_adminconsole($args) {
	global $kapenta;
	if ('admin' != $kapenta->user->role) { return ''; }

	$block = $kapenta->theme->loadBlock('modules/home/views/adminconsole.block.php');
	$labels = array('home.frontpage' => $kapenta->registry->get('home.frontpage'));
	$html = $kapenta->theme->replaceLabels($labels, $block); 

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
