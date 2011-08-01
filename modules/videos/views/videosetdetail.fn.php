<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a set of videos associated with something, along with some metadata
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refModel - type of object which own videos [string]
//arg: refUID - UID of item which owns videos [string]
//opt: tags - display block tags instead of draggable buttons (yes|no) [string]

function videos_videosetdetail($args) {
	global $kapenta, $user, $db, $theme;
	$html = '';					//%	return value [string]
	$tags = false;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ((true == array_key_exists('tags', $args)) && ('yes' == $args['tags'])) { $tags = true; }
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { return '(no such module)'; }
	if (false == $db->objectExists($refModel, $refUID)) { return '(no owner)'; }

	//if (false == $user->authHas($refModule, $refModel, 'videos-show', $refUID)) { return ''; }
	//TODO: check the permission, work out naming convention for inheritance

	//----------------------------------------------------------------------------------------------
	//	load videos from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";
	$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";

	//$sql = "select * from Videos_Video where refModule='" . $db->addMarkup($args['refModule']) 
	//     . "' and refUID='" . $db->addMarkup($args['refUID']) . "' order by floor(weight)";
	
	$range = $db->loadRange('videos_video', '*', $conditions, 'floor(weight)');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$block = $theme->loadBlock('modules/videos/views/videodetail.block.php');
	
	foreach($range as $row) {
		$model = new Videos_Video();
		$model->loadArray($row);
		
		$labels = $model->extArray();

		$labels['tags'] = '';
		$labels['imgblocks'] = '';

		$labels['thumbUrl'] = '%%serverPath%%videos/thumb/' . $row['alias'];
		$labels['editUrl'] = '%%serverPath%%videos/editvideo/return_uploadmultiple/'. $row['alias'];

		//TODO: tidy this
		if (false == $user->authHas($model->refModule, $model->refModel, 'videos-edit', $model->refUID)) {
			$labels['editUrl'] = '%%serverPath%%videos/viewset/return_uploadmultiple/'
							   . $model->alias; 
		}
		
		if ('' == trim($labels['caption'])) { $labels['caption'] = 'none.'; }

		$returnUrl = 'videos/uploadmultiple'
			   . '/refModule_' . $model->refModule 
			   . '/refModel_' . $model->refModel
			   . '/refUID_' . $model->refUID . '/';
		
		if (true == $tags) {
			$labels['tags'] = "
			<small>
			To use this video, copy one of the following tags into your text.<br/>
	        <b>Tag:</b> [&#91;:videos::thumb::videoUID=" . $model->UID . ":&#93;]<br/>
	        <b>Tag:</b> [&#91;:videos::width300::videoUID=". $model->UID .":&#93;]<br/>
	        <b>Tag:</b> [&#91;:videos::width570::videoUID=". $model->UID .":&#93;]<br/>	
			</small>
			";
		} else {
			$labels['imgblocks'] = "
			<small>To use this video, drag one of the buttons below into your text:</small><br/>
			[[:theme::button::label=thumbnail::alt=videos|raUID=" . $model->UID . "|size=thumb|:]]
			[[:theme::button::label=width300::alt=videos|raUID=" . $model->UID . "|size=width300|:]]
			[[:theme::button::label=width570::alt=videos|raUID=" . $model->UID . "|size=widtheditor|:]]
			<br/>
			";
		}

		$labels['deleteForm'] = "
		<form name='deleteVideo' method='POST' action='%%serverPath%%videos/delete/' >
		<input type='hidden' name='action' value='deleteVideo' />
		<input type='hidden' name='UID' value='" . $model->UID . "' />
		<input type='hidden' name='return' value='" . $returnUrl . "' />
		<input type='image' src='%%serverPath%%themes/clockface/images/btn-del.png' alt='delete' />
		</form>
		";
		
		$html .= $theme->replaceLabels($labels, $block);		
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
