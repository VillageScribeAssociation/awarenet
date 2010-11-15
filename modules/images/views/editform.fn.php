<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing images
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of image record [string]
//opt: return - return to upload dialog (set to 'uploadmultiple') [string]

function images_editform($args) {
	global $user, $theme;
	$html = '';			//%	return value [html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	if (true == array_key_exists('return', $args)) { $return = $args['return']; }

	$model = new Images_Image($args['raUID']);
	if (false == $model->loaded) { return '(image not found)'; }
	//TODO: check permissions here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	
	$labels = $model->extArray();
	$labels['return'] = $return;
	$labels['returnLink'] = '';
	
	if ($return == 'uploadmultiple') { 
		$labels['returnUrl'] = '/images/uploadmultiple' 
			 . '/refModule_' . $model->refModule
			 . '/refModel_' . $model->refModel  
			 . '/refUID_' . $model->refUID . '/';
				     
		$labels['returnLink'] = "<a href='" . $labels['returnUrl'] 
				      . "'>[&lt;&lt; return to upload form ]</a>";
	}
	
	$block = $theme->loadBlock('modules/images/views/editform.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
