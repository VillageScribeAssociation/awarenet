<?php

	require_once($kapenta->installPath . 'modules/lessons/models/stub.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show full cover image for a course file
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Lessons_Stub object [string]

function lessons_cover($args) {
	global $kapenta;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return ''; }
	if (false == array_key_exists('UID', $args)) { return '(resource not specified)'; }	

	$model = new Lessons_Stub($args['UID']);
	if (false === $model->loaded) { return '(resource not found)'; }
	if (false === $model->hasCover()) { return '<img src="modules/lessons/assets/missing.jpg" />'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$html .= "<img src='" . $model->cover . "' class='rounded' />";

	return $html;
}

?>
