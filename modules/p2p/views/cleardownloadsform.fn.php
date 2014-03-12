<?php

//-------------------------------------------------------------------------------------------------
//|	button to clear current download queue and delete partially downloaded files
//-------------------------------------------------------------------------------------------------

function p2p_cleardownloadsform($args) {
	global $kapenta;
	global $theme;

	if ('admin' != $kapenta->user->role) { return ''; }

	$html = $theme->loadBlock('modules/p2p/views/cleardownloadsform.block.php');

	return $html;
}

?>
