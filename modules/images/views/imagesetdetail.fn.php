<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a set of images associated with something, along with some metadata
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refModel - type of object which own images [string]
//arg: refUID - UID of item which owns images [string]

function images_imagesetdetail($args) {
	global $kapenta, $user, $db, $theme;
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { return '(no such module)'; }
	if (false == $db->objectExists($refModel, $refUID)) { return '(no owner)'; }

	if (false == $user->authHas($refModule, $refModel, 'images-show', $refUID)) { return ''; }
	//TODO: check the permission, work out naming convention for inheritance

	//----------------------------------------------------------------------------------------------
	//	load images from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";
	$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";

	//$sql = "select * from Images_Image where refModule='" . $db->addMarkup($args['refModule']) 
	//     . "' and refUID='" . $db->addMarkup($args['refUID']) . "' order by floor(weight)";
	
	$range = $db->loadRange('Images_Image', '*', $conditions, 'floor(weight)');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$block = $theme->loadBlock('modules/images/views/imagedetail.block.php');
	
	foreach($range as $row) {
		$model = new Images_Image();
		$model->loadArray($row);
		
		$labels = $model->extArray();
		
		$labels['thumbUrl'] = '%%serverPath%%images/thumb/' . $row['alias'];
		$labels['editUrl'] = '%%serverPath%%images/edit/return_uploadmultiple/'. $row['alias'];

		//TODO: tidy this
		if (false == $user->authHas($model->refModule, $model->refModel, 'images', $model->refUID)) {
			$labels['editUrl'] = '%%serverPath%%images/viewset/return_uploadmultiple/'
							   . $model->alias; 
		}
		
		if ('' == trim($labels['caption'])) { $labels['caption'] = 'none.'; }

		$returnUrl = '/images/uploadmultiple/refModule_' . $model->refModule 
			   . '/refModel_' . $model->refModel
			   . '/refUID_' . $model->refUID . '/';
		
		$labels['deleteForm'] = "
		<form name='deleteImage' method='POST' action='%%serverPath%%images/delete/' >
		<input type='hidden' name='action' value='deleteImage' />
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
