<?php

//-------------------------------------------------------------------------------------------------
//|	shows the peer autoconfiguration form (admins only)
//-------------------------------------------------------------------------------------------------

function p2p_autoconfigform($args) {
	global $kapenta;
	global $theme;

	$html = '';
	
	//---------------------------------------------------------------------------------------------
	//	check user role and make the block
	//---------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	$html = $theme->loadBlock('modules/p2p/views/autoconfigform.block.php');
	$html = $theme->ntb($html, 'Autoconfigure', 'divAutoConfig', 'show');

	return $html;
}

?>
