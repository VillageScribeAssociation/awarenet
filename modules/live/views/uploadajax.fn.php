<?php

//--------------------------------------------------------------------------------------------------
//|	method to create a drag and drop ajax upload form in the page
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have attached files [string]
//arg: refUID - UID of object which may have attached files [string]

function live_uploadajax($args) {
	global $kapenta;
	global $page;
	global $user;
	global $theme;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return ''; }

	if (false == array_key_exists('refModule', $args)) { return '(refModule not given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(refModel not given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(refUID not given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block and inject dependencies into page header
	//----------------------------------------------------------------------------------------------
	$page->requireJs($kapenta->serverPath . 'modules/live/js/uploader.js');

	$block = $theme->loadBlock('modules/live/views/uploadajax.block.php');

	$labels = array(
		'refModule' => $refModule,
		'refModel' => $refModel,
		'refUID' => $refUID
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
