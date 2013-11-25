<?

//--------------------------------------------------------------------------------------------------
//|	list of controls for this module as displayed on the admin console
//--------------------------------------------------------------------------------------------------

function twitter_adminconsole($args) {
	global $kapenta;
	global $theme;
	global $user;

	if ('admin' != $user->role) { return ''; }

	$block = $theme->loadBlock('modules/twitter/views/adminconsole.block.php');

	$labels = array(
		'twitterYear' => date('Y', $kapenta->time()),
		'twitterMonth' => date('m', $kapenta->time() - (60 * 60 * 24 * 30))
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
