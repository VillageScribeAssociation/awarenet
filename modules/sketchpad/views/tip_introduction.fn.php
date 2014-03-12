<?php

//--------------------------------------------------------------------------------------------------
//|	block to point out sketchpad if not dismissed by user
//--------------------------------------------------------------------------------------------------

function sketchpad_tip_introduction($args) {
	global $kapenta;
	global $theme;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check if current user has dismissed this policy
	//----------------------------------------------------------------------------------------------
	if ('hide' == $kapenta->user->get('info.sketchpad.intro')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('modules/sketchpad/views/tip_introduction.block.php');

	return $html;
}

?>
