<?php

	require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');

//--------------------------------------------------------------------------------------------------
//|	format an edition and all notices to be sent by email
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Newsletter_Edition object [string]
//opt: editionUID - replaces UID if present [string]

function newsletter_mailtemplate($args) {
	global $theme;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('editionUID', $args)) { $args['UID'] == $args['editionUID']; }
	if (false == array_key_exists('UID', $args)) { return ''; }

	$model = new Newsletter_Edition($args['UID']);
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	//$block = $theme->loadBlock('modules/newsletter/views/mailtemplate.block.php');
	//$labels = $model->extArray();

	//$labels['css'] = file($kapenta->serverPath . 'home/css/default.css');

	//$html = $theme->replaceLabels($labels, $block);

	$html = implode(file($kapenta->serverPath . 'newsletter/showedition/' . $model->UID . '/allow_yes/'));

	return $html;
}

?>
