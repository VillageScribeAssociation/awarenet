<?php

//--------------------------------------------------------------------------------------------------
//|	displays a form to send an edition to a single address
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Newsletter_Edition object [string]
//opt: editionUID - overrides UID if present [string]

function newsletter_sendsingleform($args) {
	global $theme;
	global $user;

	$html = '';	

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('editionUID', $args)) { $args['UID']  = $args['editionUID']; }
	if (false == array_key_exists('UID', $args)) { return '(edition not specified)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/newsletter/views/sendsingleform.block.php');
	$labels = $args;
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
