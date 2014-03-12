<?php

//--------------------------------------------------------------------------------------------------
//|	show a form for exporting / backing up the current database
//--------------------------------------------------------------------------------------------------

function admin_exportdbform($args) {
	global $theme;
	global $kapenta;	

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	//TODO: check which DBMS are supported on this instance before displaying forms form them
	$html = $theme->loadBlock('modules/admin/views/exportdbform.block.php');
	return $html;
}

?>
