<?php

//--------------------------------------------------------------------------------------------------
//|	embed the cMinds .swf technology demonstrator
//--------------------------------------------------------------------------------------------------

function games_cminds($args) {
	global $theme;
	global $kapenta;
	global $session;

	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { return '[[:users::loginform:]]'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$labels = array(
		'userUID' => $kapenta->user->UID,
		'sessionUID' => $kapenta->session->UID,
		'apiUrl' => '%%serverPath%%games/reporting/'
	);

	$block = $theme->loadBlock('modules/games/views/cminds.block.php');
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
