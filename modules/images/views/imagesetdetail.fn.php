<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a set of images associated with something, along with some metadata
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refModel - type of object which own images [string]
//arg: refUID - UID of item which owns images [string]
//opt: tags - display block tags instead of draggable buttons (yes|no) [string]

function images_imagesetdetail($args) {
		global $kapenta;
		global $kapenta;
		global $kapenta;
		global $theme;

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
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { return '(no owner)'; }

	if (false == $kapenta->user->authHas($refModule, $refModel, 'images-show', $refUID)) { return ''; }
	//TODO: check the permission, work out naming convention for inheritance

	//----------------------------------------------------------------------------------------------
	//	load images from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($refUID) . "'";
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($refModule) . "'";

	//$sql = "select * from Images_Image where refModule='" . $kapenta->db->addMarkup($args['refModule']) 
	//     . "' and refUID='" . $kapenta->db->addMarkup($args['refUID']) . "' order by floor(weight)";
	
	$range = $kapenta->db->loadRange('images_image', '*', $conditions, 'floor(weight)');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$block = $theme->loadBlock('modules/images/views/imagedetail.block.php');
	
	foreach($range as $row) {
		$model = new Images_Image();
		$model->loadArray($row);
		
		$labels = $model->extArray();

		$labels['tags'] = '';
		$labels['imgblocks'] = '';

		$labels['thumbUrl'] = '%%serverPath%%images/thumb/' . $row['alias'];
		$labels['editUrl'] = '%%serverPath%%images/edit/return_uploadmultiple/'. $row['alias'];

		//TODO: tidy this
		if (false == $kapenta->user->authHas($model->refModule, $model->refModel, 'images-edit', $model->refUID)) {
			$labels['editUrl'] = '%%serverPath%%images/viewset/return_uploadmultiple/'
							   . $model->alias; 
		}
		
		if ('' == trim($labels['caption'])) { $labels['caption'] = 'none.'; }

		$returnUrl = '/images/uploadmultiple/refModule_' . $model->refModule 
			   . '/refModel_' . $model->refModel
			   . '/refUID_' . $model->refUID . '/';
		
		if (true == $tags) {
			$labels['tags'] = "
			<small>
			To use this image, copy one of the following tags into your text.<br/>
	        <b>Tag:</b> [&#91;:images::thumb::imageUID=" . $model->UID . ":&#93;]<br/>
	        <b>Tag:</b> [&#91;:images::width300::imageUID=". $model->UID .":&#93;]<br/>
	        <b>Tag:</b> [&#91;:images::width570::imageUID=". $model->UID .":&#93;]<br/>	
			</small>
			";
		} else {
			$labels['imgblocks'] = "
			<small>To use this image, drag one of the buttons below into your text:</small><br/>
			[[:theme::button::label=thumbnail::alt=images|raUID=" . $model->UID . "|size=thumb|:]]
			[[:theme::button::label=width300::alt=images|raUID=" . $model->UID . "|size=width300|:]]
			[[:theme::button::label=width570::alt=images|raUID=" . $model->UID . "|size=widtheditor|:]]
			<br/>
			";
		}

		$labels['deleteForm'] = "
		<form name='deleteImage' method='POST' action='%%serverPath%%images/delete/' >
		<input type='hidden' name='action' value='deleteImage' />
		<input type='hidden' name='UID' value='" . $model->UID . "' />
		<input type='hidden' name='return' value='" . $returnUrl . "' />
		<input type='image' src='%%serverPath%%themes/%%defaultTheme%%/images/btn-del.png' alt='delete' />
		</form>
		";
		
		$html .= $theme->replaceLabels($labels, $block);		
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
