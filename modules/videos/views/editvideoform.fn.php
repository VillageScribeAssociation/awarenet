<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing videos
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Video object [string]
//opt: return - return to upload dialog (set to 'uploadmultiple' or 'player') [string]

function videos_editvideoform($args) {
	global $kapenta;
	global $user;
	global $theme;
	global $page;

	$html = '';			//%	return value [html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	if (true == array_key_exists('return', $args)) { $return = $args['return']; }

	$model = new Videos_Video($args['raUID']);
	if (false == $model->loaded) { return '(video not found)'; }
	//TODO: check permissions here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	
	$block = $theme->loadBlock('modules/videos/views/editvideoform.block.php');

	$labels = $model->extArray();
	$labels['return'] = $return;
	$labels['returnUrl'] = '';
	$labels['returnLink'] = '';
	
	switch($return) {
		case 'uploadmultiple':
			$labels['returnUrl'] = '/videos/uploadmultiple' 
			 . '/refModule_' . $model->refModule
			 . '/refModel_' . $model->refModel  
			 . '/refUID_' . $model->refUID . '/';
				     
			$labels['returnLink'] = ''
			 . "<a href='" . $labels['returnUrl'] . "'>[&lt;&lt; return to upload form ]</a>";
			break;		//..........................................................................

		case 'player':
			$labels['returnUrl'] = '/videos/play/' . $model->alias;
				     
			//$labels['returnLink'] = ''
			// . "<a href='" . $labels['returnUrl'] . "' target='_parent'>[ show in player &gt;&gt; ]</a>";
			break;
		
	}

	if ((true == array_key_exists('edittags', $args)) && ('yes' == $args['edittags'])) {
		$page->requireJs($kapenta->serverPath . 'modules/videos/js/editor.js');
		$labels['editTagsLink'] = ''
		 . "<a href=\"javascript:Videos_EditTags('" . $model->UID . "');\">[edit tags]</a>"
		 . '';
	}

	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
