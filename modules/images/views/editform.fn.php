<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing images
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of an Images_Image object [string]
//opt: imageUID - overrides raUID if present [string]
//opt: igmUID - overrides raUID if present [string]
//opt: return - return to upload dialog (set to 'uploadmultiple') [string]
//opt: edittags - show edit tags link (yes|no) [string]

function images_editform($args) {
	global $kapenta;
	global $kapenta;
	global $theme;
	global $kapenta;

	$html = '';			//%	return value [html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('imageUID', $args)) { $args['raUID'] = $args['imageUID']; }
	if (true == array_key_exists('imgUID', $args)) { $args['raUID'] = $args['imgUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	if (true == array_key_exists('return', $args)) { $return = $args['return']; }

	$model = new Images_Image($args['raUID']);
	if (false == $model->loaded) { return '(image not found)'; }
	//TODO: check permissions here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/images/views/editform.block.php');
	
	$labels = $model->extArray();
	$labels['return'] = $return;
	$labels['returnLink'] = '';
	$labels['editTagsLink'] = '';

	if ($return == 'uploadmultiple') { 
		$labels['returnUrl'] = '/images/uploadmultiple' 
			 . '/refModule_' . $model->refModule
			 . '/refModel_' . $model->refModel  
			 . '/refUID_' . $model->refUID . '/';
				     
		$labels['returnLink'] = ''
		 . "<a href='" . $labels['returnUrl'] . "'>[&lt;&lt; return to upload form ]</a>";
	}
	
	if ($return == 'show') { 
		$labels['returnUrl'] = '/images/show/' . $model->alias;			     
		$labels['returnLink'] = "";
	}

	if ((true == array_key_exists('edittags', $args)) && ('yes' == $args['edittags'])) {
		$kapenta->page->requireJs($kapenta->serverPath . 'modules/images/js/editor.js');
		$labels['editTagsLink'] = ''
		 . "<a href=\"javascript:Images_EditTags('" . $model->UID . "');\">[edit tags]</a>"
		 . '';
	}

	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
