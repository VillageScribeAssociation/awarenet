<?php

//-------------------------------------------------------------------------------------------------
//|	button to clear current download queue and delete partially downloaded files
//-------------------------------------------------------------------------------------------------

function p2p_cleardownloadsform($args) {
	global $user;
	global $theme;

	if ('admin' != $user->role) { return ''; }

	$html = $theme->loadBlock('modules/p2p/views/cleardownloadsform.block.php');

	return $html;
}

?>
