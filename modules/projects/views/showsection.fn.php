<?php

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a single project section
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Projects_Section object [string]
//opt: sectionUID - overrides UID if present [string]

function projects_showsection($args) {
	global $theme;
	global $kapenta;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('sectionUID', $args)) { $args['UID'] = $args['sectionUID']; }
	if (false == array_key_exists('UID', $args)) { return '(section UID not given)'; }

	$model = new Projects_Section($args['UID']);
	if (false == $model->loaded) { return '(section not found)'; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/showsection.block.php');
	$labels = $model->extArray();
	$labels['weightbuttons'] = '[[:projects::weightbuttons::UID=' . $model->UID . ':]]';
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
