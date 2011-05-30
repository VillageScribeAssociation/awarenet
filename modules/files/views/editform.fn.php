<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing file metadata
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of record [string]
//opt: return - return to upload dialog [string]

function files_editform($args) {
	global $theme;
	$html = '';						//%	reutrn value [string:html]
	$return = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return 'raUID not given'; }
	if (true == array_key_exists('return', $args)) { $return = $args['return']; }
	
	$model = new Files_File($args['raUID']);
	if (false == $model->loaded) { return '(file not found)'; }
	if ($model->fileName == '') { return '(file not found)'; }

	//TODO: user permission check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	
	$labels = $model->extArray();
	$labels['return'] = $return;
	$labels['returnLink'] = '';
	$labels['thumbNail'] = '%%serverPath%%themes/%%defaultTheme%%/images/arrow_down.png';
	
	if ($return == 'uploadmultiple') { 
		$labels['returnUrl'] = '/files/uploadmultiple' 
			. '/refModule_' . $labels['refModule']
			. '/refModel_' . $labels['refModel']  
			. '/refUID_' . $labels['refUID'] . '/';
				     
		$labels['returnLink'] = "<a href='" . $labels['returnUrl'] 
				      . "'>[&lt;&lt; return to upload form ]</a>";
	}
	
	$block = $theme->loadBlock('modules/files/views/editform.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
