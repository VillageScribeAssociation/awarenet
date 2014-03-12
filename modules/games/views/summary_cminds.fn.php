<?php

//--------------------------------------------------------------------------------------------------
//|
//--------------------------------------------------------------------------------------------------
//;	TODO: include contexual information from the user registry

function games_summary_cminds($args) {
	global $kapenta;
	global $theme;	

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/games/views/summary_cminds.block.php');
	$html = $block;			//	<--- insert user scroes, etc here

	return $html;
}

?>
