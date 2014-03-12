<?php

	require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to publish an edition to the subscriber list
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an unpublished Newsletter_Edition obejct [string]

function newsletter_publishform($args) {
	global $theme;
	global $kapenta;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }

	$model = new Newsletter_Edition($args['UID']);

	if (false == $model->loaded) { return '(edition not found)'; }
	if ('published' == $model->status) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$block = $theme->loadBlock('modules/newsletter/views/publishform.block.php');
	$labels = $model->extArray();
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
