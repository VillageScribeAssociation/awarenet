<?php

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display image summary formatted for stacking in the nav
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of an Images_Image object [string]
//opt: UID - overrides raUID if present [string]
//opt: imageUID - overrides raUID if present [string]
//opt: behavior - Behavior when links are clicked (link|editmodal) [string]

function images_summarynav($args) {
	global $kapenta;
	global $theme;
	global $kapenta;

	$behavior = 'link';						//%	behavior of links [string]
	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permisisons
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('imageUID', $args)) { $args['raUID'] = $args['imageUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(raUID not given)'; }

	$model = new Images_Image($args['raUID']);
	if (false == $model->loaded) { return '(could not load image)'; }

	//	TODO: permissions check here

	if (true == array_key_exists('behavior', $args)) { $behavior = $args['behavior']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/images/views/summarynav.block.php');
	$labels = $model->extArray();
	$labels['extra'] = '';			//	reserved
	$labels['controls'] = '';			//	reserved
	$labels['onClick'] = '';

	if ('editmodal' == $behavior) {
		$kapenta->page->requireJs('%%serverPath%%modules/images/js/editor.js');
		$labels['viewUrl'] = "javascript:Images_EditModal('" . $model->UID . "');";
		$labels['controls'] = ''
		 . "<span style='float: right;'>"
		 . "<small>"
		 . "<a href=\"javascript:Images_EditTags('" . $model->UID . "');\">[edit tags]</a>"
		 . "<a href=\"javascript:Images_Delete('" . $model->UID . "');\">[delete]</a>"
		 . "<a href=\"javascript:Images_MakeDefault('" . $model->UID . "');\">[move to top]</a>"
		 . "</small>"
		 . '';
	}

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
