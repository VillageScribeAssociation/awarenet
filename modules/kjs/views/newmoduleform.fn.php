<?php

//--------------------------------------------------------------------------------------------------
//|	shows a form for creating the basic structure of a new Kapenta.JS module
//--------------------------------------------------------------------------------------------------

function kjs_newmoduleform($args) {
	global $user;
	global $theme;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('modules/kjs/views/newmoduleform.block.php');
	$html = $theme->ntb($html, "New module", 'divNewModuleForm', 'show');

	return $html;
}

?>
